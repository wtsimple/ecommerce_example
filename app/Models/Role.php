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

    const CREATE_PRODUCT = 'create product';
    const UPDATE_PRODUCT = 'update product';
    const DELETE_PRODUCT = 'delete product';
    const READ_ALL_PURCHASES = 'read all purchases';

    const CAPABILITIES = [
        'admin' => [
            'promote editor',
            self::UPDATE_PRODUCT,
            self::CREATE_PRODUCT,
            self::DELETE_PRODUCT,
            self::READ_ALL_PURCHASES,
        ],
        'editor' => [
            self::UPDATE_PRODUCT,
            self::CREATE_PRODUCT,
            self::DELETE_PRODUCT,
        ],
    ];
}
