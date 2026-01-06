<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class AuthUnitTest extends TestCase
{
    public function testIsBannedPositive(): void
    {
        $user = new User(['is_banned' => false]);

        $this->assertFalse($user->isBanned());
    }

    public function testIsBannedNegative(): void
    {
        $user = new User(['is_banned' => true]);

        $this->assertTrue($user->isBanned());
    }
}
