<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Illuminate\Support\Facades\Auth;
use App\Exports\UserOrdersExport;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::whereHas('role', function ($query) {
                $query->where('role_name', 'user');
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show($id)
    {
        // Ambil user beserta role dan orders
        $user = User::with(['role', 'orders'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function export()
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }

    public function exportUserOrders($id)
    {
        $user = User::findOrFail($id);
        return Excel::download(new UserOrdersExport($id), 'orders_user_'.$user->id.'.xlsx');
    }
}
