<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\FA\Coupons\Tests\Unit;
use PHPUnit\Framework\TestCase;
use Ksfraser\FrontAccounting\FA\Coupons\DTO\CouponDTO;
class CouponDtoTest extends TestCase
{
    public function testDtoRoundTripsThroughArray(): void {
        $dto = new CouponDTO(5, 'SAVE10', 'percentage', '10.00', '2026-01-01', '2026-12-31', '100', '5', '1');
        $arr = $dto->toArray();
        $this->assertSame(5, $arr['coupon_id']);
        $this->assertSame('SAVE10', $arr['coupon_code']);
        $this->assertSame('percentage', $arr['discount_type']);
        $this->assertSame('10.00', $arr['discount_value']);
        $dto2 = CouponDTO::fromArray($arr);
        $this->assertSame($dto->coupon_id, $dto2->coupon_id);
        $this->assertSame($dto->coupon_code, $dto2->coupon_code);
        $this->assertSame($dto->discount_type, $dto2->discount_type);
    }
    public function testDisplayLabelUsesCodeTypeValue(): void {
        $dto = new CouponDTO(1, 'SAVE10', 'percentage', '10.00');
        $this->assertStringContainsString('SAVE10', $dto->getDisplayLabel());
        $this->assertStringContainsString('percentage', $dto->getDisplayLabel());
        $this->assertStringContainsString('10.00', $dto->getDisplayLabel());
        $this->assertStringContainsString('%', $dto->getDisplayLabel());
    }
    public function testDisplayLabelFallsBackToId(): void {
        $dto = new CouponDTO(9, '', '', '0.00');
        $this->assertStringContainsString('Coupon #9', $dto->getDisplayLabel());
    }
}
