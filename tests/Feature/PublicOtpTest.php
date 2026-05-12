<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_otp_generation_consumes_usage_and_creates_log(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        $account = Account::create([
            'merchant_id' => $merchant->id,
            'label' => 'ChatGPT Main',
            'email' => 'chatgpt@example.com',
            'username' => null,
            'password_encrypted' => 'Password@123',
            'secret_key_encrypted' => 'JBSWY3DPEHPK3PXP',
        ]);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'account_id' => $account->id,
            'name' => 'Client 01',
            'token' => 'public-token-001',
            'usage_limit' => 3,
            'usage_count' => 1,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/public/otp/'.$customer->token);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'تم توليد رمز التحقق بنجاح.')
            ->assertJsonPath('remaining_usage', 1)
            ->assertJsonStructure([
                'otp_code',
                'expires_in',
                'expires_at',
            ]);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $response->json('otp_code'));
        $this->assertGreaterThanOrEqual(1, $response->json('expires_in'));
        $this->assertLessThanOrEqual(30, $response->json('expires_in'));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'usage_count' => 2,
        ]);

        $this->assertDatabaseHas('otp_logs', [
            'customer_id' => $customer->id,
        ]);
    }

    public function test_public_otp_generation_fails_when_balance_is_exhausted(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        $account = Account::create([
            'merchant_id' => $merchant->id,
            'label' => 'Netflix Shared',
            'email' => 'netflix@example.com',
            'username' => null,
            'password_encrypted' => 'Password@123',
            'secret_key_encrypted' => 'JBSWY3DPEHPK3PXP',
        ]);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'account_id' => $account->id,
            'name' => 'Client 02',
            'token' => 'public-token-002',
            'usage_limit' => 2,
            'usage_count' => 2,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/public/otp/'.$customer->token);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'لا يوجد رصيد متبقٍ لهذا الرابط.');

        $this->assertDatabaseMissing('otp_logs', [
            'customer_id' => $customer->id,
        ]);
    }

    public function test_public_otp_generation_rejects_inactive_link(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        $account = Account::create([
            'merchant_id' => $merchant->id,
            'label' => 'Claude Shared',
            'email' => 'claude@example.com',
            'username' => null,
            'password_encrypted' => 'Password@123',
            'secret_key_encrypted' => 'JBSWY3DPEHPK3PXP',
        ]);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'account_id' => $account->id,
            'name' => 'Client 03',
            'token' => 'public-token-003',
            'usage_limit' => 5,
            'usage_count' => 0,
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/public/otp/'.$customer->token);

        $response
            ->assertNotFound()
            ->assertJsonPath('message', 'الرابط غير صالح أو غير نشط.');
    }
}
