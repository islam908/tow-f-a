<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_can_create_customer_and_get_info_link(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        $account = Account::create([
            'merchant_id' => $merchant->id,
            'label' => 'Netflix Slot',
            'email' => 'netflix@example.com',
            'username' => null,
            'password_encrypted' => 'pass-123',
            'secret_key_encrypted' => 'JBSWY3DPEHPK3PXP',
        ]);

        Sanctum::actingAs($merchant);

        $response = $this->postJson('/api/merchant/customers', [
            'account_id' => $account->id,
            'name' => 'Customer One',
            'usage_limit' => 5,
            'is_active' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Customer One')
            ->assertJsonPath('data.usage_limit', 5)
            ->assertJsonPath('data.usage_count', 0);

        $this->assertDatabaseHas('customers', [
            'merchant_id' => $merchant->id,
            'account_id' => $account->id,
            'name' => 'Customer One',
            'usage_limit' => 5,
            'usage_count' => 0,
        ]);

        $this->assertStringContainsString('/info/', $response->json('data.info_url'));
    }

    public function test_merchant_cannot_assign_customer_to_other_merchant_account(): void
    {
        $merchantA = User::factory()->create(['role' => User::ROLE_MERCHANT]);
        $merchantB = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        $accountOwnedByA = Account::create([
            'merchant_id' => $merchantA->id,
            'label' => 'A Account',
            'email' => 'a@example.com',
            'username' => null,
            'password_encrypted' => 'pass',
            'secret_key_encrypted' => 'JBSWY3DPEHPK3PXP',
        ]);

        Sanctum::actingAs($merchantB);

        $response = $this->postJson('/api/merchant/customers', [
            'account_id' => $accountOwnedByA->id,
            'name' => 'Injected Customer',
            'usage_limit' => 5,
        ]);

        $response->assertNotFound();
    }

    public function test_customer_update_rejects_usage_count_over_limit(): void
    {
        $merchant = User::factory()->create(['role' => User::ROLE_MERCHANT]);

        $account = Account::create([
            'merchant_id' => $merchant->id,
            'label' => 'Primary Account',
            'email' => 'merchant@example.com',
            'username' => null,
            'password_encrypted' => 'pass',
            'secret_key_encrypted' => 'JBSWY3DPEHPK3PXP',
        ]);

        $customer = Customer::create([
            'merchant_id' => $merchant->id,
            'account_id' => $account->id,
            'name' => 'Existing Customer',
            'token' => 'fixed-token-123',
            'usage_limit' => 5,
            'usage_count' => 2,
            'is_active' => true,
        ]);

        Sanctum::actingAs($merchant);

        $response = $this->putJson('/api/merchant/customers/'.$customer->id, [
            'usage_limit' => 3,
            'usage_count' => 4,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'عدد الاستخدامات لا يمكن أن يتجاوز حد الاستخدام.');
    }
}
