<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        // ambil semua role
        $roles = Role::all();

        // daftar semua fitur (hardcode atau bisa juga diambil dari config)
        $allPermissions = [
            'Dashboard',
            'Categories',
            'Product',
            'Users',
            'Admins',
            'Orders',
            'Setting',
            'Role',
        ];

        return view('admin.role.index', compact('roles', 'allPermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // kalau super admin → otomatis semua permissions
        if ($role->role_name === 'super admin') {
            $role->update([
                'permissions' => $this->getAllPermissions(),
            ]);
        } else {
            $role->update([
                'permissions' => $request->permissions ?? [],
            ]);
        }

        return redirect()->route('roles.index')->with('success', __('role.success_update'));
    }

    private function getAllPermissions()
    {
        return [
            'Dashboard',
            'Categories',
            'Product',
            'Users',
            'Admins',
            'Orders',
            'Setting',
            'Role',
        ];
    }
}