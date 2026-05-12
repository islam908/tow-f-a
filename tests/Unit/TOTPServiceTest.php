<?php

namespace Tests\Unit;

use App\Services\TOTPService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TOTPServiceTest extends TestCase
{
    public function test_it_matches_rfc6238_known_vector(): void
    {
        $service = new TOTPService();

        $result = $service->generateCurrentCode(
            'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ',
            59,
            8,
            30
        );

        $this->assertSame('94287082', $result['code']);
        $this->assertSame(1, $result['expires_in']);
    }

    public function test_it_rejects_invalid_secret_key(): void
    {
        $service = new TOTPService();

        $this->expectException(InvalidArgumentException::class);

        $service->generateCurrentCode('INVALID***SECRET');
    }
}
