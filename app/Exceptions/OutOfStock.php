<?php

namespace App\Exceptions;

use App\Models\Product;
use Exception;
use Throwable;

class OutOfStock extends Exception
{
    public static function create(Product $product): static
    {
        return new static("Product {$product->name} is out of stock");
    }

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
