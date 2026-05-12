<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_can_create_and_list_own_accounts(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        Sanctum::actingAs($merchant);

        $createResponse = $this->postJson('/api/merchant/accounts', [
            'label' => 'ChatGPT Team A',
            'email' => 'merchant@example.com',
            'username' => null,
            'password' => 'Pass@1234',
            'secret_key' => 'JBSWY3DPEHPK3PXP',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.label', 'ChatGPT Team A');

        $this->assertDatabaseHas('accounts', [
            'merchant_id' => $merchant->id,
            'label' => 'ChatGPT Team A',
            'email' => 'merchant@example.com',
        ]);

        $listResponse = $this->getJson('/api/merchant/accounts');

        $listResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.merchant_id', $merchant->id);
    }

    public function test_merchant_can_create_account_using_otpauth_uri(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        Sanctum::actingAs($merchant);

        $response = $this->postJson('/api/merchant/accounts', [
            'label' => 'ChatGPT QR Seed',
            'email' => 'qr@example.com',
            'password' => 'Pass@1234',
            'secret_key' => 'otpauth://totp/ChatGPT:qr@example.com?secret=JBSWY3DPEHPK3PXP&issuer=ChatGPT',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.label', 'ChatGPT QR Seed');

        $account = Account::query()->where('merchant_id', $merchant->id)->firstOrFail();

        $this->assertSame('JBSWY3DPEHPK3PXP', $account->secret_key_encrypted);
    }

    public function test_merchant_gets_validation_error_for_invalid_qr_payload(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        Sanctum::actingAs($merchant);

        $response = $this->postJson('/api/merchant/accounts', [
            'label' => 'Broken QR Seed',
            'email' => 'invalid@example.com',
            'password' => 'Pass@1234',
            'secret_key' => 'otpauth://totp/Test:invalid@example.com?issuer=TestOnly',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['secret_key']);
    }

    public function test_merchant_cannot_delete_other_merchant_account(): void
    {
        $merchantA = User::factory()->create(['role' => User::ROLE_MERCHANT]);
        $merchantB = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        $account = Account::create([
            'merchant_id' => $merchantA->id,
            'label' => 'Protected Account',
            'email' => 'a@example.com',
            'username' => null,
            'password_encrypted' => 'secret-pass',
            'secret_key_encrypted' => 'JBSWY3DPEHPK3PXP',
        ]);

        Sanctum::actingAs($merchantB);

        $response = $this->deleteJson('/api/merchant/accounts/'.$account->id);

        $response->assertNotFound();
    }
}
