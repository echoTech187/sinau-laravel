<?php

namespace App\Helpers;

use App\Models\RbacLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    // ──────────────────────────────────────────────
    // Event name constants
    // ──────────────────────────────────────────────
    const ROLE_ASSIGNED           = 'role_assigned';
    const ROLE_REVOKED            = 'role_revoked';
    const PERMISSION_TOGGLED      = 'permission_toggled';
    const MODULE_PERMISSIONS_SET  = 'module_permissions_set';
    const FIELD_SECURITY_ADDED    = 'field_security_added';
    const FIELD_SECURITY_UPDATED  = 'field_security_updated';
    const FIELD_SECURITY_REMOVED  = 'field_security_removed';
    const DIRECT_PERMISSION_GRANT = 'direct_permission_grant';
    const DIRECT_PERMISSION_DENY  = 'direct_permission_deny';
    const DIRECT_PERMISSION_RESET = 'direct_permission_reset';
    const USER_ROLE_ASSIGNED      = 'user_role_assigned';
    const USER_ROLE_REVOKED       = 'user_role_revoked';
    const ACCESS_REQUEST_CREATED  = 'access_request_created';
    const ACCESS_REQUEST_APPROVED = 'access_request_approved';
    const ACCESS_REQUEST_REJECTED = 'access_request_rejected';

    /**
     * Record an audit log entry.
     *
     * @param  string      $event        One of the class constants above
     * @param  int|null    $targetUserId The user whose access was changed (nullable for role-only changes)
     * @param  array       $properties   Arbitrary data (old/new values, role names, etc.)
     */
    public static function record(string $event, ?int $targetUserId = null, array $properties = []): void
    {
        $actorId = Auth::id();

        if (!$actorId) {
            return; // Don't log unauthenticated actions
        }

        RbacLog::create([
            'actor_id'       => $actorId,
            'target_user_id' => $targetUserId,
            'event'          => $event,
            'properties'     => $properties,
        ]);
    }

    /**
     * Human-readable label for an event type.
     */
    public static function label(string $event): string
    {
        return match ($event) {
            self::ROLE_ASSIGNED           => 'Peran Ditambahkan',
            self::ROLE_REVOKED            => 'Peran Dicabut',
            self::PERMISSION_TOGGLED      => 'Izin Diubah',
            self::MODULE_PERMISSIONS_SET  => 'Izin Modul Diubah',
            self::FIELD_SECURITY_ADDED    => 'Field Security Ditambahkan',
            self::FIELD_SECURITY_UPDATED  => 'Field Security Diperbarui',
            self::FIELD_SECURITY_REMOVED  => 'Field Security Dihapus',
            self::DIRECT_PERMISSION_GRANT => 'Izin Langsung Diberikan',
            self::DIRECT_PERMISSION_DENY  => 'Izin Langsung Diblokir',
            self::DIRECT_PERMISSION_RESET => 'Izin Langsung Direset',
            self::USER_ROLE_ASSIGNED      => 'Peran User Ditambahkan',
            self::USER_ROLE_REVOKED       => 'Peran User Dicabut',
            self::ACCESS_REQUEST_CREATED  => 'Permintaan Akses Diajukan',
            self::ACCESS_REQUEST_APPROVED => 'Permintaan Akses Disetujui',
            self::ACCESS_REQUEST_REJECTED => 'Permintaan Akses Ditolak',
            default                       => ucwords(str_replace('_', ' ', $event)),
        };
    }

    /**
     * Tailwind color class for event badge.
     */
    public static function badgeColor(string $event): string
    {
        return match (true) {
            str_contains($event, 'assigned') || str_contains($event, 'grant'),
            str_contains($event, 'added')  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',

            str_contains($event, 'revoked') || str_contains($event, 'deny'),
            str_contains($event, 'removed') => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',

            str_contains($event, 'toggled') || str_contains($event, 'updated'),
            str_contains($event, 'set')     => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',

            str_contains($event, 'reset')   => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',

            default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        };
    }
}
