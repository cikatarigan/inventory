<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

  	app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

      $role_admin = Role::firstOrCreate(['name' => 'admin', 'description' => 'Untuk Mengelola Semua Project']);
      $role = Role::firstOrCreate(['name' => 'guest' , 'description' => 'Guest/ Pengunjung']);

      $permission = collect([
        //Warehouse
        Permission::firstOrCreate(['name' =>'location.index','guard_name'=>'web'],['display_name' => 'Daftar location']),
        Permission::firstOrCreate(['name' =>'location.store','guard_name'=>'web'],['display_name' => 'Daftar location - Menambahkan location Baru']),
        Permission::firstOrCreate(['name' =>'location.update','guard_name'=>'web'],['display_name' => 'Daftar location - Mengedit location']),
        Permission::firstOrCreate(['name' =>'location.destroy','guard_name'=>'web'],['display_name' => 'Daftar location - Menghapus location']),
        Permission::firstOrCreate(['name' =>'location.trash','guard_name'=>'web'],['display_name' => 'Daftar location Terhapus']),
        Permission::firstOrCreate(['name' =>'location.restore','guard_name'=>'web'],['display_name' => 'Daftar location Terhapus - Restore location Terhapus']),
        //Good
        Permission::firstOrCreate(['name' =>'good.index','guard_name'=>'web'],['display_name' => 'Daftar Barang']),
        Permission::firstOrCreate(['name' =>'good.store','guard_name'=>'web'],['display_name' => 'Daftar Barang - Menambahkan Barang Baru']),
        Permission::firstOrCreate(['name' =>'good.update','guard_name'=>'web'],['display_name' => 'Daftar Barang - Mengedit Barang' ]),
        Permission::firstOrCreate(['name' =>'good.destroy','guard_name'=>'web'],['display_name' => 'Daftar Barang - Menghapus Barang']),
        Permission::firstOrCreate(['name' =>'good.trash','guard_name'=>'web'],['display_name' => 'Daftar Barang Terhapus']),
        Permission::firstOrCreate(['name' =>'good.restore','guard_name'=>'web'],['display_name' => 'Daftar Barang Terhapus - Restore Barang Terhapus']),
        Permission::firstOrCreate(['name' =>'good.location','guard_name'=>'web'],['display_name' => 'Daftar Barang - Input Lokasi Barang']),
        //User
        Permission::firstOrCreate(['name' =>'user.index','guard_name'=>'web'],['display_name' => 'Daftar User']),
        Permission::firstOrCreate(['name' =>'user.store','guard_name'=>'web'],['display_name' => 'Daftar User  - Menambahkan User Baru']),
        Permission::firstOrCreate(['name' =>'user.update','guard_name'=>'web'],['display_name' => 'Daftar User  - Mengedit User']),
        Permission::firstOrCreate(['name' =>'user.destroy','guard_name'=>'web'],['display_name' => 'Daftar User  - Menghapus User']),
        Permission::firstOrCreate(['name' =>'user.trash','guard_name'=>'web'],['display_name' => 'Daftar User Terhapus']),
        Permission::firstOrCreate(['name' =>'user.restore','guard_name'=>'web'],['display_name' => 'Daftar User Terhapus - Restore User Terhapus']),
        Permission::firstOrCreate(['name' =>'user.sync','guard_name'=>'web'],['display_name' => 'Daftar User  - Mensinkron User']),
        Permission::firstOrCreate(['name' =>'user.change','guard_name'=>'web'],['display_name' => 'Daftar User  - Mengganti Password User']),
        //Role
        Permission::firstOrCreate(['name' =>'role.index','guard_name'=>'web'],['display_name' => 'Daftar Role']),
        Permission::firstOrCreate(['name' =>'role.store','guard_name'=>'web'],['display_name' => 'Daftar Role - Menambahkan Role Baru']),
        Permission::firstOrCreate(['name' =>'role.edit','guard_name'=>'web'],['display_name' => 'Daftar Role - Mengedit Role']),
        Permission::firstOrCreate(['name' =>'role.destroy','guard_name'=>'web'],['display_name' => 'Daftar Role - Menghapus Role']),
        // Permission
        Permission::firstOrCreate(['name' =>'permission.index','guard_name'=>'web'],['display_name' => 'Daftar Permission']),
        //StockEntry
        Permission::firstOrCreate(['name' =>'stockentry.index','guard_name'=>'web'],['display_name' => 'Daftar Stock Entry']),
        Permission::firstOrCreate(['name' =>'stockentry.add','guard_name'=>'web'],['display_name' => 'Daftar Stock Entry - Menambahkan Stock Entry']),

        // Allotment
        Permission::firstOrCreate(['name' =>'allotment.index','guard_name'=>'web'],['display_name' => 'Daftar Pemberian']),
        Permission::firstOrCreate(['name' =>'allotment.add','guard_name'=>'web'],['display_name' => 'Daftar Pemberian - Menambahkan Pemberian Baru']),

        // Borrow
        Permission::firstOrCreate(['name' =>'borrow.index','guard_name'=>'web'],['display_name' => 'Daftar Peminjaman']),
        Permission::firstOrCreate(['name' =>'borrow.add','guard_name'=>'web'],['display_name' => 'Daftar Peminjaman - Menambahkan Peminjaman Baru']),

        //Return
        Permission::firstOrCreate(['name' =>'return.index','guard_name'=>'web'],['display_name' => 'Daftar Pengembalian']),
        Permission::firstOrCreate(['name' =>'return.add','guard_name'=>'web'],['display_name' => 'Daftar Pengembalian - Menambahkan Pengembalian Baru']),

        // Sample
        Permission::firstOrCreate(['name' =>'sample.index','guard_name'=>'web'],['display_name' => 'Daftar Sample']),
        Permission::firstOrCreate(['name' =>'sample.store','guard_name'=>'web'],['display_name' => 'Daftar Sample - Menambahkan Sample Baru']),
        Permission::firstOrCreate(['name' =>'sample.update','guard_name'=>'web'],['display_name' => 'Daftar Sample - Mengubah Sample']),
        Permission::firstOrCreate(['name' =>'sample.view','guard_name'=>'web'],['display_name' => 'Daftar Sample - Melihat Data Sample']),
        Permission::firstOrCreate(['name' =>'sample.destroy','guard_name'=>'web'],['display_name' => 'Daftar Sample - Menghapus Data Sample']),
        ]);

        $role_admin->syncPermissions($permission->map(function ($item, $key) { return $item->name;}));
    }
}
