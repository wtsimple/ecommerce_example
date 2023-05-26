<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
