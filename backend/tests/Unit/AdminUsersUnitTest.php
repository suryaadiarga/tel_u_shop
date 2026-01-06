<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AdminUsersUnitTest extends TestCase
{
    public function testHasRolePositive(): void
    {
        $admin = new User(['role_id' => 1]);
        $admin->setRelation('role', new Role(['name' => 'admin']));

        $this->assertTrue($admin->hasRole('admin'));
    }

    public function testHasRoleNegative(): void
    {
        $customer = new User(['role_id' => 3]);
        $customer->setRelation('role', new Role(['name' => 'customer']));

        $this->assertFalse($customer->hasRole('admin'));
    }
}
