<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use Tests\TestCase;

class MerchantProductsUnitTest extends TestCase
{
    public function testMerchantApprovalPositive(): void
    {
        $merchant = new User(['role_id' => 2, 'merchant_status' => 'approved']);
        $merchant->setRelation('role', new Role(['name' => 'merchant']));

        $this->assertTrue($merchant->isMerchantApproved());
    }

    public function testMerchantApprovalNegative(): void
    {
        $merchant = new User(['role_id' => 2, 'merchant_status' => 'pending']);
        $merchant->setRelation('role', new Role(['name' => 'merchant']));

        $this->assertFalse($merchant->isMerchantApproved());
    }
}
