<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\FA\Coupons\Tests\Unit;
use PHPUnit\Framework\TestCase;
use Ksfraser\FrontAccounting\FA\Coupons\DTO\CouponDTO;
class CouponDtoTest extends TestCase
{
    public function testDtoRoundTripsThroughArray(): void {
        $dto = new CouponDTO('SAVE10', 'percentage', '10.00', '2026-01-01', '2026-12-31', 'cart_wide', 0, 0, true, true);
        $arr = $dto->toArray();
        $this->assertSame('SAVE10', $arr['coupon_code']);
        $this->assertSame('percentage', $arr['discount_type']);
        $this->assertSame('10.00', $arr['discount_value']);
        $this->assertSame('2026-01-01', $arr['valid_from']);
        $this->assertSame('2026-12-31', $arr['valid_to']);
        $this->assertSame('cart_wide', $arr['applies_to_type']);
        $this->assertSame(0, $arr['applies_to_id']);
        $this->assertSame(0, $arr['apply_order']);
        $this->assertTrue($arr['stackable']);
        $this->assertTrue($arr['apply_to_original']);
        $dto2 = CouponDTO::fromArray($arr);
        $this->assertSame($dto->coupon_code, $dto2->coupon_code);
        $this->assertSame($dto->discount_type, $dto2->discount_type);
        $this->assertSame($dto->discount_value, $dto2->discount_value);
    }
    public function testDisplayLabelUsesCodeTypeValue(): void {
        $dto = new CouponDTO('SAVE10', 'percentage', '10.00');
        $this->assertStringContainsString('SAVE10', $dto->getDisplayLabel());
        $this->assertStringContainsString('percentage', $dto->getDisplayLabel());
        $this->assertStringContainsString('10.00', $dto->getDisplayLabel());
        $this->assertStringContainsString('%', $dto->getDisplayLabel());
    }
    public function testDisplayLabelUsesCodeTypeValueFixed(): void {
        $dto = new CouponDTO('SAVE5', 'fixed', '5.00');
        $this->assertStringContainsString('SAVE5', $dto->getDisplayLabel());
        $this->assertStringContainsString('fixed', $dto->getDisplayLabel());
        $this->assertStringContainsString('5.00', $dto->getDisplayLabel());
    }
}