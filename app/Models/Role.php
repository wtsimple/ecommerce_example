<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    const ADMIN = 'admin';
    const EDITOR = 'editor';
    const ROLES = [
        self::ADMIN,
        self::EDITOR,
    ];

    const CAPABILITIES = [
        'admin' => [
            'promote editor',
            'edit product',
            'create product',
            'delete product',
        ],
        'editor' => [
            'edit product',
            'create product',
            'delete product',
        ],
    ];
}
