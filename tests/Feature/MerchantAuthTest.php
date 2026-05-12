<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MerchantAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_can_login_and_receive_sanctum_token(): void
    {
        $merchant = User::factory()->create([
            'password' => Hash::make('secret-pass'),
            'role' => User::ROLE_MERCHANT,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/merchant/login', [
            'email' => $merchant->email,
            'password' => 'secret-pass',
            'device_name' => 'phpunit',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'token_type',
                'merchant' => ['id', 'email', 'role'],
            ])
            ->assertJsonPath('merchant.id', $merchant->id)
            ->assertJsonPath('merchant.role', User::ROLE_MERCHANT);
    }

    public function test_inactive_merchant_cannot_login(): void
    {
        $merchant = User::factory()->inactiveMerchant()->create([
            'password' => Hash::make('secret-pass'),
        ]);

        $response = $this->postJson('/api/merchant/login', [
            'email' => $merchant->email,
            'password' => 'secret-pass',
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('message', 'حساب التاجر غير نشط حاليًا.');
    }
}
