<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Ambil data user dengan role 'user'
     */
    public function collection()
    {
        return User::with('role')
            ->whereHas('role', function ($q) {
                $q->where('role_name', 'user');
            })
            ->get();
    }

    /**
     * Mapping data tiap row
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role->role_name ?? '-',
        ];
    }

    /**
     * Heading kolom Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'Role',
        ];
    }
}
