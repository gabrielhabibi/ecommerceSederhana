<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdminsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::with('role')
            ->whereHas('role', fn($q) => $q->where('role_name', 'admin'))
            ->get(['id', 'name', 'email', 'role_id'])
            ->map(function($admin) {
                return [
                    'ID'    => $admin->id,
                    'Name'  => $admin->name,
                    'Email' => $admin->email,
                    'Role'  => $admin->role->role_name ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Role'];
    }
}
