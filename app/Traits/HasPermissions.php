<?php

namespace App\Traits;

use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Support\Facades\Cache;

trait HasPermissions
{
    /**
     * Get the permissions associated with the user (Direct Overrides).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'user_has_permissions', 'user_id', 'permission_id')
            ->withPivot('is_forbidden');
    }

    /**
     * Check if user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermissionTo(string $permission): bool
    {
        // 1. Check Caching
        $cacheKey = 'user_permissions_' . $this->id;
        $permissions = Cache::remember($cacheKey, 3600, function () {
            // Get Direct Overrides (Active) / Check if forbidden
            $directPermissionsPivot = $this->permissions()->get();
            $directPermissions = [];
            $forbiddenPermissions = [];

            foreach ($directPermissionsPivot as $p) {
                if ($p->pivot->is_forbidden) {
                    $forbiddenPermissions[] = $p->slug;
                } else {
                    $directPermissions[] = $p->slug;
                }
            }

            // Get Roles (Primary Role + Active Additional Roles)
            $roles = collect([]);
            if ($this->role) {
                $roles->push($this->role);
            }

            if (method_exists($this, 'roles')) {
                $additionalRoles = $this->roles()
                    ->where(function($q) {
                        $q->whereNull('user_has_roles.expires_at')
                          ->orWhere('user_has_roles.expires_at', '>', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('user_has_roles.starts_at')
                          ->orWhere('user_has_roles.starts_at', '<=', now());
                    })
                    ->get();
                $roles = $roles->concat($additionalRoles)->unique('id');
            }

            // Get Role Permissions recursively (Inheritance)
            $rolePermissions = [];
            foreach ($roles as $r) {
                $rolePermissions = array_merge($rolePermissions, $this->getPermissionsRecursive($r));
            }

            // Gabungkan role dan direct, lalu hapus yang masuk ke daftar forbidden
            $merged = array_unique(array_merge($directPermissions, $rolePermissions));
            return array_diff($merged, $forbiddenPermissions);
        });

        // 2. Check if the specific permission exists
        if (in_array($permission, $permissions)) {
            return true;
        }

        // 3. Recursive Wildcard Check (e.g. if checking 'user.create', verify 'user.*' and '*')
        $parts = explode('.', $permission);
        while (count($parts) > 0) {
            array_pop($parts);
            $wildcard = empty($parts) ? '*' : implode('.', $parts) . '.*';
            if (in_array($wildcard, $permissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get role permissions recursively (Inheritance).
     *
     * @param \App\Models\Roles $role
     * @return array
     */
    protected function getPermissionsRecursive($role): array
    {
        if (!$role) {
            return [];
        }

        $permissions = $role->permissions()->pluck('slug')->toArray();

        if ($role->parent_id && $role->parent) {
            $permissions = array_merge($permissions, $this->getPermissionsRecursive($role->parent));
        }

        return $permissions;
    }

    /**
     * Check if user has a specific role.
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role->slug ?? null, $roles);
        }

        return ($this->role->slug ?? null) === $roles;
    }

    /**
     * Clear permission cache.
     *
     * @return void
     */
    public function clearPermissionCache(): void
    {
        Cache::forget('user_permissions_' . $this->id);
        Cache::forget('user_data_scopes_' . $this->id);
        Cache::forget('user_field_permissions_' . $this->id);
    }

    /**
     * Get aggregated data scopes from all active roles.
     *
     * @return array
     */
    public function getDataScopes(): array
    {
        $cacheKey = 'user_data_scopes_' . $this->id;
        
        return Cache::remember($cacheKey, 3600, function () {
            $scopes = [];

            if (method_exists($this, 'roles')) {
                // Get all active temporary/secondary roles
                $activeRoles = $this->roles()
                    ->where(function($q) {
                        $q->whereNull('user_has_roles.expires_at')
                          ->orWhere('user_has_roles.expires_at', '>', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('user_has_roles.starts_at')
                          ->orWhere('user_has_roles.starts_at', '<=', now());
                    })
                    ->whereNotNull('user_has_roles.data_scope')
                    ->get();
                
                foreach ($activeRoles as $role) {
                    $roleScope = json_decode($role->pivot->data_scope, true);
                    if (is_array($roleScope)) {
                        foreach ($roleScope as $key => $values) {
                            if (!isset($scopes[$key])) {
                                $scopes[$key] = [];
                            }
                            
                            $valuesArray = is_array($values) ? $values : [$values];
                            $scopes[$key] = array_unique(array_merge($scopes[$key], $valuesArray));
                        }
                    }
                }
            }

            return $scopes;
        });
    }

    /**
     * Get specific data scope by key.
     *
     * @param string $key The data scope key (e.g. 'branch_id')
     * @return array|null Returns array of allowed values, or null if unrestricted
     */
    public function getDataScope(string $key): ?array
    {
        // If user is super admin, they have no restriction (return null)
        if ($this->hasPermissionTo('super-admin')) {
            return null;
        }

        $scopes = $this->getDataScopes();
        
        return $scopes[$key] ?? null;
    }

    /**
     * Check if a specific field is hidden for the user.
     *
     * @param string $model
     * @param string $field
     * @return bool
     */
    public function isFieldHidden(string $model, string $field): bool
    {
        // Super admin ignores field hiding rules
        if ($this->hasPermissionTo('super-admin')) {
            return false;
        }

        $cacheKey = 'user_field_permissions_' . $this->id;

        $hiddenFields = Cache::remember($cacheKey, 3600, function () {
            $hidden = [];

            // Get Roles (Primary Role + Active Additional Roles)
            $roles = collect([]);
            if ($this->role) {
                $roles->push($this->role);
            }

            if (method_exists($this, 'roles')) {
                $additionalRoles = $this->roles()
                    ->where(function($q) {
                        $q->whereNull('user_has_roles.expires_at')
                          ->orWhere('user_has_roles.expires_at', '>', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('user_has_roles.starts_at')
                          ->orWhere('user_has_roles.starts_at', '<=', now());
                    })
                    ->with('fieldPermissions')
                    ->get();
                $roles = $roles->concat($additionalRoles)->unique('id');
            } else {
                 if ($this->role) {
                     $this->role->load('fieldPermissions');
                 }
            }

            foreach ($roles as $role) {
                if ($role->fieldPermissions) {
                    foreach ($role->fieldPermissions as $fp) {
                        if ($fp->is_hidden) {
                            $key = $fp->model . '::' . $fp->field;
                            $hidden[$key] = true;
                        }
                    }
                }
            }

            return $hidden;
        });

        $searchKey = $model . '::' . $field;
        return isset($hiddenFields[$searchKey]);
    }
}
