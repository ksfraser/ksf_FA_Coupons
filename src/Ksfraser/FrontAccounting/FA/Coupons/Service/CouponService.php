<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\FA\Coupons\Service;
use Ksfraser\FrontAccounting\FA\Coupons\DTO\CouponDTO;
use Ksfraser\FrontAccounting\FA\Coupons\Repository\CouponRepository;
final class CouponService {
    private $repo;
    public function __construct(?CouponRepository $repo = null) { $this->repo = $repo ?? new CouponRepository(); }
    public function findByCode(string $code): ?array {
        return $this->repo->findByCode($code);
    }
    public function findActive(): array {
        return $this->repo->findActive();
    }
    public function listAll(): array {
        $rows = $this->repo->findAll();
        $results = array();
        foreach ($rows as $row) {
            $r = CouponDTO::fromArray($row);
            $results[] = array(
                'id' => $r->coupon_id,
                'fields' => array(
                    'coupon_id' => (string) $r->coupon_id,
                    'coupon_code' => $r->coupon_code,
                    'discount_type' => $r->discount_type,
                    'discount_value' => $r->discount_value,
                    'valid_from' => $r->valid_from,
                    'valid_to' => $r->valid_to,
                    'max_uses' => $r->max_uses,
                    'used_count' => $r->used_count,
                    'is_active' => $r->is_active,
                    'url' => '/modules/ksf_FA_Coupons/pages/coupon_form.php?action=edit&coupon_id=' . $r->coupon_id,
                ),
                'label' => $r->getDisplayLabel(),
            );
        }
        return $results;
    }
    public function insertCoupon(array $data): int {
        return $this->repo->insertCoupon($data);
    }
    public function updateCoupon(int $couponId, array $data): void {
        $this->repo->updateCoupon($couponId, $data);
    }
    public function deleteCoupon(int $couponId): void {
        $this->repo->deleteCoupon($couponId);
    }
    public function validateCoupon(string $code): array {
        $row = $this->repo->findByCode($code);
        if ($row === null) {
            return array('valid' => false, 'message' => _('Invalid coupon code'));
        }
        $coupon = CouponDTO::fromArray($row);
        if ($coupon->is_active !== '1') {
            return array('valid' => false, 'message' => _('Coupon is inactive'));
        }
        if ($coupon->valid_from !== '' && $coupon->valid_from > date('Y-m-d')) {
            return array('valid' => false, 'message' => _('Coupon not yet valid'));
        }
        if ($coupon->valid_to !== '' && $coupon->valid_to < date('Y-m-d')) {
            return array('valid' => false, 'message' => _('Coupon has expired'));
        }
        if ((int) $coupon->max_uses > 0 && (int) $coupon->used_count >= (int) $coupon->max_uses) {
            return array('valid' => false, 'message' => _('Coupon usage limit reached'));
        }
        return array(
            'valid' => true,
            'coupon' => $coupon,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
        );
    }
    public function getStateTransitions(string $currentState): array {
        $transitions = array(
            'active' => array('inactive' => _('Deactivate')),
            'inactive' => array('active' => _('Activate')),
        );
        return isset($transitions[$currentState]) ? $transitions[$currentState] : array();
    }
    public function isAllowedTransition(string $fromState, string $toState): bool {
        $transitions = $this->getStateTransitions($fromState);
        return isset($transitions[$toState]);
    }
}