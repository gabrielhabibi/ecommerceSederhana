<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminsExport;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role' => function($q) {
                        $q->select('id', 'role_name');
                    }])
                    ->whereHas('role', function($q) {
                        $q->where('role_name', 'admin');
                    });

        // Jika ada query search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->get(['id', 'name', 'email', 'role_id']);

        return view('admin.admins.index', ['admins' => $admins]);
    }

    public function create()
    {
        // ambil role admin + user biar fleksibel
        $roles = Role::whereIn('role_name', ['admin', 'user'])->get();

        return view('admin.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|min:3|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|confirmed|min:6|max:8',
            'password_confirmation' => 'required',
            'address'               => 'required|string|min:5|max:255',
            'phone_number'          => 'required|regex:/^[0-9]{11,13}$/',
        ]);

        $userRole = Role::where('role_name', 'admin')->first();

        if (!$userRole) {
            return back()->withErrors(['role' => __('admin.error_role_required')]);
        }

        User::create([
            'name'         => $request->name,
            'email'        => strtolower($request->email),
            'password'     => Hash::make($request->password),
            'role_id'      => $userRole->id,
            'address'      => $request->address,
            'phone_number' => $request->phone_number,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admins.index')->with('success', __('admin.success_create'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::whereIn('role_name', ['admin', 'user'])->get();

        return view('admin.admins.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|min:3|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'role_id'      => 'required|exists:roles,id',
            'address'      => 'required|string|min:5|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]{11,13}$/',
        ]);

        $user->update($validated);

        return redirect()->route('admins.index')->with('success', __('admin.success_update'));
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();
        
        return redirect()->route('admins.index')->with('success', __('admin.success_delete'));
    }

    public function export()
    {
        return Excel::download(new AdminsExport, 'admins.xlsx');
    }
}