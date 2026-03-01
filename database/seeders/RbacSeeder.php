<?php

namespace Database\Seeders;

use App\Models\Menus;
use App\Models\Modules;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // TRUNCATE RBAC TABLES — KECUALI ROLES (karena users.role_id NOT NULL)
        // ============================================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        DB::table('user_has_roles')->truncate();
        DB::table('user_has_permissions')->truncate();
        DB::table('menus')->truncate();
        DB::table('permissions')->truncate();
        DB::table('modules')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Tabel RBAC (kecuali roles) berhasil di-truncate.');

        // ============================================================
        // 1. ROLES (upsert by slug supaya aman untuk user yang sudah ada)
        // ============================================================
        $rolesData = [
            ['role' => 'Super Administrator', 'slug' => 'super-admin',   'is_active' => true, 'description' => 'Akses penuh ke seluruh sistem tanpa terkecuali.'],
            ['role' => 'Administrator',       'slug' => 'administrator', 'is_active' => true, 'description' => 'Mengelola pengguna, roles, dan konfigurasi sistem.'],
            ['role' => 'Manager',             'slug' => 'manager',       'is_active' => true, 'description' => 'Menyetujui transaksi, melihat laporan dan kepegawaian.'],
            ['role' => 'Staff Keuangan',      'slug' => 'finance',       'is_active' => true, 'description' => 'Mengelola invoice, pembayaran, dan rekap keuangan.'],
            ['role' => 'Staff HRD',           'slug' => 'hrd',           'is_active' => true, 'description' => 'Mengelola data karyawan, absensi, dan payroll.'],
            ['role' => 'Staff Gudang',        'slug' => 'warehouse',     'is_active' => true, 'description' => 'Mengelola stok, produk, dan inventory gudang.'],
            ['role' => 'Viewer',              'slug' => 'viewer',        'is_active' => true, 'description' => 'Hanya dapat melihat data tanpa bisa melakukan perubahan.'],
            ['role' => 'Guest',               'slug' => 'guest',         'is_active' => false, 'description' => 'Peran sementara untuk akses terbatas.'],
        ];

        foreach ($rolesData as $data) {
            Roles::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $superAdmin = Roles::where('slug', '=', 'super-admin', 'and')->first();
        $admin = Roles::where('slug', '=', 'administrator', 'and')->first();
        $manager = Roles::where('slug', '=', 'manager', 'and')->first();
        $finance = Roles::where('slug', '=', 'finance', 'and')->first();
        $hrd = Roles::where('slug', '=', 'hrd', 'and')->first();
        $warehouse = Roles::where('slug', '=', 'warehouse', 'and')->first();
        $viewer = Roles::where('slug', '=', 'viewer', 'and')->first();
        $guestRole = Roles::where('slug', '=', 'guest', 'and')->first();

        // Set parent_id untuk guest
        $guestRole->update(['parent_id' => $viewer->id]);

        $this->command->info('✅ Roles berhasil dibuat/diperbarui: '.Roles::count('*').' roles.');

        // ============================================================
        // 2. MODULES
        // ============================================================
        $modDashboard = Modules::create(['name' => 'Dashboard',     'slug' => 'dashboard',    'icon' => 'heroicon-o-home',                   'order' => 1, 'is_active' => true,  'description' => 'Ringkasan performa dan statistik utama sistem.']);
        $modMaster = Modules::create(['name' => 'Master Data',   'slug' => 'master-data',  'icon' => 'heroicon-o-cube',                   'order' => 2, 'is_active' => true,  'description' => 'Pengelolaan produk, kategori, supplier, dan inventory.']);
        $modSales = Modules::create(['name' => 'Penjualan',     'slug' => 'sales',        'icon' => 'heroicon-o-shopping-cart',          'order' => 3, 'is_active' => true,  'description' => 'Manajemen pesanan, invoice penjualan, dan piutang.']);
        $modFinance = Modules::create(['name' => 'Keuangan',      'slug' => 'finance',      'icon' => 'heroicon-o-banknotes',              'order' => 4, 'is_active' => true,  'description' => 'Transaksi, kas & bank, laporan laba rugi.']);
        $modHrd = Modules::create(['name' => 'Kepegawaian',   'slug' => 'hrd',          'icon' => 'heroicon-o-users',                  'order' => 5, 'is_active' => true,  'description' => 'Database karyawan, absensi, dan penggajian.']);
        $modReport = Modules::create(['name' => 'Laporan',       'slug' => 'report',       'icon' => 'heroicon-o-chart-bar',              'order' => 6, 'is_active' => true,  'description' => 'Laporan penjualan, keuangan, stok, dan karyawan.']);
        $modSystem = Modules::create(['name' => 'Sistem & RBAC', 'slug' => 'system',       'icon' => 'heroicon-o-cog-6-tooth',            'order' => 7, 'is_active' => true,  'description' => 'Kelola role, izin akses, user, dan log audit.']);
        $modSettings = Modules::create(['name' => 'Pengaturan',   'slug' => 'settings',     'icon' => 'heroicon-o-adjustments-horizontal', 'order' => 8, 'is_active' => true,  'description' => 'Konfigurasi umum aplikasi, profil perusahaan.']);

        // PO Bus Modules
        $modArmada = Modules::create(['name' => 'Master Armada', 'slug' => 'armada', 'icon' => 'heroicon-o-truck', 'order' => 9, 'is_active' => true, 'description' => 'Manajemen bus, fasilitas, layout kursi, dan maintenance.']);
        $modRoute = Modules::create(['name' => 'Jadwal & Rute', 'slug' => 'routes', 'icon' => 'heroicon-o-map', 'order' => 10, 'is_active' => true, 'description' => 'Manajemen rute, titik henti, dan jadwal keberangkatan.']);
        $modCargo = Modules::create(['name' => 'Kargo & Anti-Fraud', 'slug' => 'cargo', 'icon' => 'heroicon-o-archive-box', 'order' => 11, 'is_active' => true, 'description' => 'Manajemen kargo, bagasi, dan verifikasi anti-fraud.']);
        $modManifest = Modules::create(['name' => 'SJO & Inspeksi', 'slug' => 'manifests', 'icon' => 'heroicon-o-clipboard-document-check', 'order' => 12, 'is_active' => true, 'description' => 'Manajemen Surat Jalan Operasional dan Inspeksi Kelayakan Armada (P2H).']);

        $this->command->info('✅ Modules berhasil dibuat: '.Modules::count('*').' modul.');

        // ============================================================
        // 3. PERMISSIONS
        // ============================================================
        $p = fn (int $modId, string $name, string $slug, ?string $group = null) => Permissions::create(['module_id' => $modId, 'name' => $name, 'slug' => $slug, 'group_name' => $group]);

        // Dashboard
        $pDashView = $p($modDashboard->id, 'Lihat Dashboard', 'dashboard.view');

        // Master Data
        $pProdView = $p($modMaster->id, 'Lihat Produk', 'product.view', 'Produk');
        $pProdCreate = $p($modMaster->id, 'Tambah Produk', 'product.create', 'Produk');
        $pProdEdit = $p($modMaster->id, 'Edit Produk', 'product.edit', 'Produk');
        $pProdDelete = $p($modMaster->id, 'Hapus Produk', 'product.delete', 'Produk');
        $pCatView = $p($modMaster->id, 'Lihat Kategori', 'category.view', 'Kategori');
        $pCatCreate = $p($modMaster->id, 'Tambah Kategori', 'category.create', 'Kategori');
        $pCatEdit = $p($modMaster->id, 'Edit Kategori', 'category.edit', 'Kategori');
        $pCatDelete = $p($modMaster->id, 'Hapus Kategori', 'category.delete', 'Kategori');
        $pSupView = $p($modMaster->id, 'Lihat Supplier', 'supplier.view', 'Supplier');
        $pSupCreate = $p($modMaster->id, 'Tambah Supplier', 'supplier.create', 'Supplier');
        $pSupEdit = $p($modMaster->id, 'Edit Supplier', 'supplier.edit', 'Supplier');
        $pSupDelete = $p($modMaster->id, 'Hapus Supplier', 'supplier.delete', 'Supplier');

        // Penjualan
        $pSaleView = $p($modSales->id, 'Lihat Order', 'order.view', 'Order');
        $pSaleCreate = $p($modSales->id, 'Buat Order', 'order.create', 'Order');
        $pSaleEdit = $p($modSales->id, 'Edit Order', 'order.edit', 'Order');
        $pSaleDelete = $p($modSales->id, 'Batalkan Order', 'order.delete', 'Order');
        $pSaleApprove = $p($modSales->id, 'Setujui Order', 'order.approve', 'Order');
        $pInvView = $p($modSales->id, 'Lihat Invoice', 'invoice.view', 'Invoice');
        $pInvCreate = $p($modSales->id, 'Buat Invoice', 'invoice.create', 'Invoice');
        $pInvSend = $p($modSales->id, 'Kirim Invoice', 'invoice.send', 'Invoice');

        // Keuangan
        $pFinView = $p($modFinance->id, 'Lihat Transaksi', 'transaction.view', 'Transaksi');
        $pFinCreate = $p($modFinance->id, 'Catat Transaksi', 'transaction.create', 'Transaksi');
        $pFinEdit = $p($modFinance->id, 'Edit Transaksi', 'transaction.edit', 'Transaksi');
        $pFinDelete = $p($modFinance->id, 'Hapus Transaksi', 'transaction.delete', 'Transaksi');
        $pFinApprove = $p($modFinance->id, 'Setujui Pembayaran', 'payment.approve', 'Pembayaran');
        $pFinReport = $p($modFinance->id, 'Lihat Laporan Keuangan', 'finance.report', 'Laporan');

        // Kepegawaian
        $pEmpView = $p($modHrd->id, 'Lihat Data Karyawan', 'employee.view', 'Karyawan');
        $pEmpCreate = $p($modHrd->id, 'Tambah Karyawan', 'employee.create', 'Karyawan');
        $pEmpEdit = $p($modHrd->id, 'Edit Karyawan', 'employee.edit', 'Karyawan');
        $pEmpDelete = $p($modHrd->id, 'Hapus Karyawan', 'employee.delete', 'Karyawan');
        $pAttView = $p($modHrd->id, 'Lihat Absensi', 'attendance.view', 'Absensi');
        $pAttManage = $p($modHrd->id, 'Kelola Absensi', 'attendance.manage', 'Absensi');
        $pPayView = $p($modHrd->id, 'Lihat Gaji', 'payroll.view', 'Payroll');
        $pPayProcess = $p($modHrd->id, 'Proses Penggajian', 'payroll.process', 'Payroll');

        // Laporan
        $pRepSales = $p($modReport->id, 'Lihat Laporan Penjualan', 'report.sales');
        $pRepFin = $p($modReport->id, 'Lihat Laporan Keuangan', 'report.finance');
        $pRepStock = $p($modReport->id, 'Lihat Laporan Stok', 'report.stock');
        $pRepEmp = $p($modReport->id, 'Lihat Laporan Karyawan', 'report.employee');
        $pRepExport = $p($modReport->id, 'Export Laporan', 'report.export');

        // Sistem & RBAC
        $pRoleView = $p($modSystem->id, 'Lihat Roles', 'role.view', 'RBAC');
        $pRoleManage = $p($modSystem->id, 'Kelola Roles', 'role.manage', 'RBAC');
        $pPermManage = $p($modSystem->id, 'Kelola Permissions', 'permission.manage', 'RBAC');
        $pUserView = $p($modSystem->id, 'Lihat Users', 'user.view', 'User');
        $pUserManage = $p($modSystem->id, 'Kelola Users', 'user.manage', 'User');
        $pLogView = $p($modSystem->id, 'Lihat Log Aktivitas', 'log.view', 'Log');
        $pApprView = $p($modSystem->id, 'Inbox Approval', 'rbac.approvals.index', 'Approval');

        // Pengaturan
        $pSetView = $p($modSettings->id, 'Lihat Pengaturan', 'settings.view');
        $pSetManage = $p($modSettings->id, 'Kelola Pengaturan', 'settings.manage');
        $pSetMenu = $p($modSettings->id, 'Kelola Menu Sidebar', 'settings.menu');

        // PO Bus Permissions
        $pBusView = $p($modArmada->id, 'Lihat Daftar Bus', 'buses.view', 'Armada');
        $pBusManage = $p($modArmada->id, 'Kelola Bus', 'buses.manage', 'Armada');
        $pCrewView = $p($modArmada->id, 'Lihat Daftar Kru', 'crews.view', 'Armada');
        $pAgentView = $p($modArmada->id, 'Lihat Agen', 'agents.view', 'Armada');
        $pBusClassView = $p($modArmada->id, 'Lihat Kelas Bus', 'bus_classes.view', 'Armada');
        $pSeatLayoutView = $p($modArmada->id, 'Lihat Layout Kursi', 'seat_layouts.view', 'Armada');

        $pScheduleView = $p($modRoute->id, 'Lihat Jadwal', 'schedules.view', 'Operasional');
        $pScheduleManage = $p($modRoute->id, 'Kelola Jadwal', 'schedules.manage', 'Operasional');
        $pRouteView = $p($modRoute->id, 'Lihat Rute', 'routes.view', 'Operasional');
        $pLocationView = $p($modRoute->id, 'Lihat Lokasi', 'locations.view', 'Operasional');
        $pBookingView = $p($modRoute->id, 'Lihat Reservasi', 'bookings.view', 'Operasional');
        $pBookingCreate = $p($modRoute->id, 'Buat Reservasi', 'bookings.create', 'Operasional');

        // Cargo & Anti-Fraud Permissions
        $pShipmentView = $p($modCargo->id, 'Lihat Kargo', 'shipments.view', 'Kargo');
        $pShipmentCreate = $p($modCargo->id, 'Tambah Kargo', 'shipments.create', 'Kargo');
        $pShipmentManage = $p($modCargo->id, 'Kelola Kargo', 'shipments.manage', 'Kargo');
        $pCheckerScan = $p($modCargo->id, 'Scanner Anti-Fraud', 'cargo.checker', 'Anti-Fraud');

        // SJO & Inspeksi Permissions
        $pManifestView = $p($modManifest->id, 'Lihat SJO', 'manifests.view', 'Operasional');
        $pManifestManage = $p($modManifest->id, 'Kelola SJO', 'manifests.manage', 'Operasional');
        $pManifestInspect = $p($modManifest->id, 'Input Inspeksi P2H', 'manifests.inspect', 'Operasional');

        $this->command->info('✅ Permissions berhasil dibuat: '.Permissions::count('*').' izin.');

        // ============================================================
        // 4. MENUS
        // ============================================================

        $menuDash = Menus::create(['module_id' => $modDashboard->id, 'parent_id' => null, 'permission_id' => $pDashView->id,   'name' => 'Dashboard',    'icon' => 'heroicon-o-home',                   'route' => 'dashboard',         'order' => 1, 'is_active' => true]);

        $menuMaster = Menus::create(['module_id' => $modMaster->id, 'parent_id' => null, 'permission_id' => $pProdView->id,   'name' => 'Master Data',  'icon' => 'heroicon-o-cube',                   'route' => null,                'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modMaster->id, 'parent_id' => $menuMaster->id, 'permission_id' => $pProdView->id,  'name' => 'Data Produk',   'icon' => 'heroicon-o-archive-box',   'route' => 'product.show',    'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modMaster->id, 'parent_id' => $menuMaster->id, 'permission_id' => $pCatView->id,   'name' => 'Kategori',      'icon' => 'heroicon-o-tag',           'route' => 'categories.index',  'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modMaster->id, 'parent_id' => $menuMaster->id, 'permission_id' => $pSupView->id,   'name' => 'Supplier',      'icon' => 'heroicon-o-truck',         'route' => 'suppliers.index',   'order' => 3, 'is_active' => true]);

        $menuSales = Menus::create(['module_id' => $modSales->id, 'parent_id' => null, 'permission_id' => $pSaleView->id,  'name' => 'Penjualan',    'icon' => 'heroicon-o-shopping-cart',          'route' => null,                'order' => 3, 'is_active' => true]);
        Menus::create(['module_id' => $modSales->id, 'parent_id' => $menuSales->id, 'permission_id' => $pSaleView->id, 'name' => 'Daftar Order',  'icon' => 'heroicon-o-clipboard-document-list', 'route' => 'orders.index',      'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modSales->id, 'parent_id' => $menuSales->id, 'permission_id' => $pInvView->id,  'name' => 'Invoice',       'icon' => 'heroicon-o-document-text',           'route' => 'invoices.index',    'order' => 2, 'is_active' => true]);

        $menuFin = Menus::create(['module_id' => $modFinance->id, 'parent_id' => null, 'permission_id' => $pFinView->id,   'name' => 'Keuangan',     'icon' => 'heroicon-o-banknotes',              'route' => null,                'order' => 4, 'is_active' => true]);
        Menus::create(['module_id' => $modFinance->id, 'parent_id' => $menuFin->id, 'permission_id' => $pFinView->id,   'name' => 'Transaksi',     'icon' => 'heroicon-o-arrows-right-left',       'route' => 'transactions.index', 'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modFinance->id, 'parent_id' => $menuFin->id, 'permission_id' => $pFinReport->id, 'name' => 'Lap. Keuangan', 'icon' => 'heroicon-o-chart-bar',               'route' => 'finance.report',     'order' => 2, 'is_active' => true]);

        $menuHrd = Menus::create(['module_id' => $modHrd->id, 'parent_id' => null, 'permission_id' => $pEmpView->id,   'name' => 'Kepegawaian',  'icon' => 'heroicon-o-users',                  'route' => null,                'order' => 5, 'is_active' => true]);
        Menus::create(['module_id' => $modHrd->id, 'parent_id' => $menuHrd->id, 'permission_id' => $pEmpView->id,   'name' => 'Data Karyawan', 'icon' => 'heroicon-o-identification',          'route' => 'employees.index',   'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modHrd->id, 'parent_id' => $menuHrd->id, 'permission_id' => $pAttView->id,   'name' => 'Absensi',       'icon' => 'heroicon-o-calendar-days',           'route' => 'attendance.index',  'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modHrd->id, 'parent_id' => $menuHrd->id, 'permission_id' => $pPayView->id,   'name' => 'Penggajian',    'icon' => 'heroicon-o-currency-dollar',         'route' => 'payroll.index',     'order' => 3, 'is_active' => true]);

        $menuReport = Menus::create(['module_id' => $modReport->id, 'parent_id' => null, 'permission_id' => $pRepSales->id, 'name' => 'Laporan',      'icon' => 'heroicon-o-chart-pie',              'route' => null,                'order' => 6, 'is_active' => true]);
        Menus::create(['module_id' => $modReport->id, 'parent_id' => $menuReport->id, 'permission_id' => $pRepSales->id, 'name' => 'Lap. Penjualan', 'icon' => 'heroicon-o-arrow-trending-up', 'route' => 'reports.sales',    'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modReport->id, 'parent_id' => $menuReport->id, 'permission_id' => $pRepFin->id,   'name' => 'Lap. Keuangan',  'icon' => 'heroicon-o-banknotes',        'route' => 'reports.finance',  'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modReport->id, 'parent_id' => $menuReport->id, 'permission_id' => $pRepStock->id, 'name' => 'Lap. Stok',      'icon' => 'heroicon-o-archive-box',      'route' => 'reports.stock',    'order' => 3, 'is_active' => true]);
        Menus::create(['module_id' => $modReport->id, 'parent_id' => $menuReport->id, 'permission_id' => $pRepEmp->id,   'name' => 'Lap. Karyawan',  'icon' => 'heroicon-o-users',            'route' => 'reports.employee', 'order' => 4, 'is_active' => true]);

        $menuSys = Menus::create(['module_id' => $modSystem->id, 'parent_id' => null, 'permission_id' => $pRoleView->id,  'name' => 'Sistem',       'icon' => 'heroicon-o-cog-6-tooth',            'route' => null,                'order' => 7, 'is_active' => true]);
        Menus::create(['module_id' => $modSystem->id, 'parent_id' => $menuSys->id, 'permission_id' => $pRoleManage->id, 'name' => 'RBAC Manager', 'icon' => 'heroicon-o-shield-check',    'route' => 'rbac.show',  'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modSystem->id, 'parent_id' => $menuSys->id, 'permission_id' => $pUserManage->id, 'name' => 'Kelola User',  'icon' => 'heroicon-o-user-group',      'route' => 'users.show', 'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modSystem->id, 'parent_id' => $menuSys->id, 'permission_id' => $pLogView->id,    'name' => 'Log Aktivitas', 'icon' => 'heroicon-o-clock',           'route' => 'rbac.logs',  'order' => 3, 'is_active' => true]);
        Menus::create(['module_id' => $modSystem->id, 'parent_id' => $menuSys->id, 'permission_id' => $pApprView->id,   'name' => 'Inbox Approval', 'icon' => 'heroicon-o-inbox-arrow-down', 'route' => 'rbac.approvals', 'order' => 4, 'is_active' => true]);

        // User specific menu
        Menus::create(['module_id' => $modDashboard->id, 'parent_id' => null, 'permission_id' => $pDashView->id, 'name' => 'Permintaan Akses', 'icon' => 'heroicon-o-key', 'route' => 'user.access-requests', 'order' => 2, 'is_active' => true]);

        Menus::create(['module_id' => $modSettings->id, 'parent_id' => null, 'permission_id' => $pSetView->id, 'name' => 'Pengaturan', 'icon' => 'heroicon-o-adjustments-horizontal', 'route' => 'settings.index', 'order' => 8, 'is_active' => true]);

        // PO Bus Menus
        $menuArmada = Menus::create(['module_id' => $modArmada->id, 'parent_id' => null, 'permission_id' => $pBusView->id, 'name' => 'Master Armada', 'icon' => 'heroicon-o-truck', 'route' => null, 'order' => 9, 'is_active' => true]);
        Menus::create(['module_id' => $modArmada->id, 'parent_id' => $menuArmada->id, 'permission_id' => $pBusView->id, 'name' => 'Daftar Bus', 'icon' => 'heroicon-o-sparkles', 'route' => 'buses.index', 'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modArmada->id, 'parent_id' => $menuArmada->id, 'permission_id' => $pCrewView->id, 'name' => 'Data Kru', 'icon' => 'heroicon-o-users', 'route' => 'crews.index', 'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modArmada->id, 'parent_id' => $menuArmada->id, 'permission_id' => $pAgentView->id, 'name' => 'Agen & Cabang', 'icon' => 'heroicon-o-building-storefront', 'route' => 'agents.index', 'order' => 3, 'is_active' => true]);
        Menus::create(['module_id' => $modArmada->id, 'parent_id' => $menuArmada->id, 'permission_id' => $pBusClassView->id, 'name' => 'Kelas & Fasilitas', 'icon' => 'heroicon-o-star', 'route' => 'bus-classes.index', 'order' => 4, 'is_active' => true]);
        Menus::create(['module_id' => $modArmada->id, 'parent_id' => $menuArmada->id, 'permission_id' => $pSeatLayoutView->id, 'name' => 'Konfigurasi Kursi', 'icon' => 'heroicon-o-squares-2x2', 'route' => 'seat-layouts.index', 'order' => 5, 'is_active' => true]);

        $menuOperasional = Menus::create(['module_id' => $modRoute->id, 'parent_id' => null, 'permission_id' => $pScheduleView->id, 'name' => 'Operasional', 'icon' => 'heroicon-o-map', 'route' => null, 'order' => 10, 'is_active' => true]);
        Menus::create(['module_id' => $modRoute->id, 'parent_id' => $menuOperasional->id, 'permission_id' => $pBookingCreate->id, 'name' => 'Reservasi Tiket', 'icon' => 'heroicon-o-ticket', 'route' => 'bookings.create', 'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modRoute->id, 'parent_id' => $menuOperasional->id, 'permission_id' => $pBookingView->id, 'name' => 'Riwayat Booking', 'icon' => 'heroicon-o-clipboard-document-list', 'route' => 'bookings.index', 'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modRoute->id, 'parent_id' => $menuOperasional->id, 'permission_id' => $pScheduleView->id, 'name' => 'Jadwal Keberangkatan', 'icon' => 'heroicon-o-calendar', 'route' => 'schedules.index', 'order' => 3, 'is_active' => true]);
        Menus::create(['module_id' => $modRoute->id, 'parent_id' => $menuOperasional->id, 'permission_id' => $pRouteView->id, 'name' => 'Master Rute', 'icon' => 'heroicon-o-arrows-right-left', 'route' => 'routes.index', 'order' => 4, 'is_active' => true]);
        Menus::create(['module_id' => $modRoute->id, 'parent_id' => $menuOperasional->id, 'permission_id' => $pLocationView->id, 'name' => 'Titik Lokasi / Agen', 'icon' => 'heroicon-o-map-pin', 'route' => 'locations.index', 'order' => 5, 'is_active' => true]);

        // Cargo Menus
        $menuCargo = Menus::create(['module_id' => $modCargo->id, 'parent_id' => null, 'permission_id' => $pShipmentView->id, 'name' => 'Managemen Kargo', 'icon' => 'heroicon-o-archive-box', 'route' => null, 'order' => 11, 'is_active' => true]);
        Menus::create(['module_id' => $modCargo->id, 'parent_id' => $menuCargo->id, 'permission_id' => $pShipmentView->id, 'name' => 'Daftar Resi Kargo', 'icon' => 'heroicon-o-document-duplicate', 'route' => 'shipments.index', 'order' => 1, 'is_active' => true]);
        Menus::create(['module_id' => $modCargo->id, 'parent_id' => $menuCargo->id, 'permission_id' => $pShipmentCreate->id, 'name' => 'Input Kargo Baru', 'icon' => 'heroicon-o-plus-circle', 'route' => 'shipments.create', 'order' => 2, 'is_active' => true]);
        Menus::create(['module_id' => $modCargo->id, 'parent_id' => $menuCargo->id, 'permission_id' => $pCheckerScan->id, 'name' => 'Anti-Fraud Scanner', 'icon' => 'heroicon-o-shield-check', 'route' => 'cargo.checker', 'order' => 3, 'is_active' => true]);

        // Manifest Menus
        $menuManifest = Menus::create(['module_id' => $modManifest->id, 'parent_id' => null, 'permission_id' => $pManifestView->id, 'name' => 'SJO & Inspeksi', 'icon' => 'heroicon-o-clipboard-document-check', 'route' => null, 'order' => 12, 'is_active' => true]);
        Menus::create(['module_id' => $modManifest->id, 'parent_id' => $menuManifest->id, 'permission_id' => $pManifestView->id, 'name' => 'Monitoring SJO', 'icon' => 'heroicon-o-tv', 'route' => 'manifests.index', 'order' => 1, 'is_active' => true]);

        $this->command->info('✅ Menus berhasil dibuat: '.Menus::count('*').' menu.');

        // ============================================================
        // 5. ASSIGN PERMISSIONS TO ROLES
        // ============================================================
        $allPermIds = Permissions::pluck('id', null)->toArray();

        // Super Admin: semua
        $superAdmin->permissions()->attach($allPermIds);

        // Admin: semua kecuali delete sensitif
        $adminPerms = Permissions::whereNotIn('slug', ['product.delete', 'transaction.delete', 'payroll.process'], 'and')->pluck('id', null)->toArray();
        $admin->permissions()->attach($adminPerms);

        // Manager: view semua + approve + report
        $managerSlugs = ['dashboard.view', 'product.view', 'category.view', 'supplier.view', 'order.view', 'order.approve', 'invoice.view', 'invoice.send', 'transaction.view', 'payment.approve', 'finance.report', 'employee.view', 'attendance.view', 'payroll.view', 'report.sales', 'report.finance', 'report.stock', 'report.employee', 'report.export', 'user.view', 'log.view', 'settings.view', 'rbac.approvals.index'];
        $manager->permissions()->attach(Permissions::whereIn('slug', $managerSlugs, 'and', false)->pluck('id', null)->toArray());

        // Staff Keuangan
        $financeSlugs = ['dashboard.view', 'order.view', 'invoice.view', 'invoice.create', 'invoice.send', 'transaction.view', 'transaction.create', 'transaction.edit', 'payment.approve', 'finance.report', 'report.finance', 'settings.view'];
        $finance->permissions()->attach(Permissions::whereIn('slug', $financeSlugs, 'and', false)->pluck('id', null)->toArray());

        // Staff HRD
        $hrdSlugs = ['dashboard.view', 'employee.view', 'employee.create', 'employee.edit', 'attendance.view', 'attendance.manage', 'payroll.view', 'payroll.process', 'report.employee', 'settings.view'];
        $hrd->permissions()->attach(Permissions::whereIn('slug', $hrdSlugs, 'and', false)->pluck('id', null)->toArray());

        // Staff Gudang
        $warehouseSlugs = ['dashboard.view', 'product.view', 'product.create', 'product.edit', 'category.view', 'category.create', 'supplier.view', 'order.view', 'report.stock', 'settings.view'];
        $warehouse->permissions()->attach(Permissions::whereIn('slug', $warehouseSlugs, 'and', false)->pluck('id', null)->toArray());

        // Viewer
        $viewerPerms = Permissions::where(fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('slug', 'like', '%.view', 'and')
            ->orWhere('slug', 'like', 'report.%', 'or'), null, null, 'and'
        )->pluck('id', null)->toArray();
        $viewer->permissions()->attach(array_unique($viewerPerms));

        // Guest: hanya dashboard
        $guestRole->permissions()->attach($pDashView->id);

        $this->command->info('✅ Permissions berhasil di-assign ke roles.');

        // ============================================================
        // 6. ASSIGN FIELD PERMISSIONS (FIELD SECURITY)
        // ============================================================
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('field_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Viewer dan Staff Gudang tidak boleh lihat Nominal Transaksi
        $hiddenAmountRoles = [$viewer->id, $warehouse->id];
        foreach ($hiddenAmountRoles as $rId) {
            DB::table('field_permissions')->insert([
                'role_id' => $rId,
                'model' => 'App\Models\Transaction',
                'field' => 'amount',
                'is_hidden' => true,
            ]);
        }

        // Staff Gudang dan Kasir mungkin tidak boleh lihat Salary karyawan
        DB::table('field_permissions')->insert([
            'role_id' => $finance->id,
            'model' => 'App\Models\Employee',
            'field' => 'salary',
            'is_hidden' => true,
        ]);
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Jml Izin', 'Status'],
            Roles::withCount('permissions')->get()
                ->map(fn ($r) => [$r->role, $r->permissions_count, $r->is_active ? 'Aktif' : 'Nonaktif'])
                ->toArray()
        );
        $this->command->newLine();
        $this->command->info('🎉 RBAC Seeder selesai!');
        $this->command->info('   Modules : '.Modules::count('*'));
        $this->command->info('   Perms   : '.Permissions::count('*'));
        $this->command->info('   Menus   : '.Menus::count('*'));
        $this->command->info('   Roles   : '.Roles::count('*'));
    }
}
