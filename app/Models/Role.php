<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    // roles
    const ADMIN = 'admin';
    const EDITOR = 'editor';
    const ROLES = [
        self::ADMIN,
        self::EDITOR,
    ];

    // capabilities
    const PROMOTE_EDITOR = 'promote editor';
    const CREATE_PRODUCT = 'create product';
    const UPDATE_PRODUCT = 'update product';
    const DELETE_PRODUCT = 'delete product';
    const READ_ALL_PURCHASES = 'read all purchases';
    const LIST_OUT_OF_STOCK_PRODUCTS = 'list out of stock products';

    const CAPABILITIES = [
        'admin' => [
            self::PROMOTE_EDITOR,
            self::UPDATE_PRODUCT,
            self::CREATE_PRODUCT,
            self::DELETE_PRODUCT,
            self::READ_ALL_PURCHASES,
            self::LIST_OUT_OF_STOCK_PRODUCTS
        ],
        'editor' => [
            self::UPDATE_PRODUCT,
            self::CREATE_PRODUCT,
            self::DELETE_PRODUCT,
        ],
    ];
}
