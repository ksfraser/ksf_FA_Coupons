<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\FA\Coupons\Service;
use Ksfraser\FrontAccounting\FA\Coupons\DTO\CouponDTO;
use Ksfraser\FrontAccounting\FA\Coupons\Repository\CouponRepository;
final class CouponService {
    private $repo;
    public function __construct(?CouponRepository $repo = null) { $this->repo = $repo ?? new CouponRepository(); }
    public function findByCode(string $code): ?CouponDTO {
        $row = $this->repo->findByCode($code);
        if ($row === null) { return null; }
        return CouponDTO::fromArray($row);
    }
    public function findActive(): array {
        $rows = $this->repo->findActive();
        $results = array();
        foreach ($rows as $row) {
            $r = CouponDTO::fromArray($row);
            $results[] = array(
                'coupon_id' => $r->coupon_code,
                'coupon_code' => $r->coupon_code,
                'discount_type' => $r->discount_type,
                'discount_value' => $r->discount_value,
                'valid_from' => $r->valid_from,
                'valid_to' => $r->valid_to,
                'applies_to_type' => $r->applies_to_type,
                'applies_to_id' => $r->applies_to_id,
                'apply_order' => $r->apply_order,
                'stackable' => $r->stackable,
                'apply_to_original' => $r->apply_to_original,
                'label' => $r->getDisplayLabel(),
            );
        }
        return $results;
    }
    public function listAll(): array {
        $rows = $this->repo->findAll();
        $results = array();
        foreach ($rows as $row) {
            $r = CouponDTO::fromArray($row);
            $results[] = array(
                'coupon_id' => $r->coupon_code,
                'coupon_code' => $r->coupon_code,
                'discount_type' => $r->discount_type,
                'discount_value' => $r->discount_value,
                'valid_from' => $r->valid_from,
                'valid_to' => $r->valid_to,
                'applies_to_type' => $r->applies_to_type,
                'applies_to_id' => $r->applies_to_id,
                'apply_order' => $r->apply_order,
                'stackable' => $r->stackable,
                'apply_to_original' => $r->apply_to_original,
                'label' => $r->getDisplayLabel(),
            );
        }
        return $results;
    }
    public function insertCoupon(array $data): string {
        return $this->repo->insertCoupon($data);
    }
    public function updateCoupon(string $couponCode, array $data): void {
        $this->repo->updateCoupon($couponCode, $data);
    }
    public function deleteCoupon(string $couponCode): void {
        $this->repo->deleteCoupon($couponCode);
    }
    public function validateCoupon(string $code): array {
        $coupon = $this->repo->findByCode($code);
        if ($coupon === null) {
            return array('valid' => false, 'message' => _('Invalid coupon code'));
        }
        $dto = CouponDTO::fromArray($coupon);
        if ($dto->is_active !== '1') {
            return array('valid' => false, 'message' => _('Coupon is inactive'));
        }
        $now = date('Y-m-d');
        if ($dto->valid_from !== '' && $dto->valid_from > $now) {
            return array('valid' => false, 'message' => _('Coupon not yet valid'));
        }
        if ($dto->valid_to !== '' && $dto->valid_to < $now) {
            return array('valid' => false, 'message' => _('Coupon has expired'));
        }
        if ((int) $dto->max_uses > 0 && (int) $dto->used_count >= (int) $dto->max_uses) {
            return array('valid' => false, 'message' => _('Coupon usage limit reached'));
        }
        return array(
            'valid' => true,
            'coupon' => $coupon,
            'discount_type' => $dto->discount_type,
            'discount_value' => $dto->discount_value,
        );
    }
    public function getStateTransitions(string $currentState): array {
        return array();
    }
    public function isAllowedTransition(string $fromState, string $toState): bool {
        return false;
    }
}