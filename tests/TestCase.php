<?php

namespace Tests;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createRoles();
    }

    protected function createRoles()
    {
        Role::factory()->create(['id' => 1, 'role_name' => 'admin']);
        Role::factory()->create(['id' => 2, 'role_name' => 'moderator']);
        Role::factory()->create(['id' => 3, 'role_name' => 'regular_user']);
    }
}
