<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\FA\Coupons\DTO;
final class CouponDTO {
    public $coupon_id;
    public $coupon_code;
    public $discount_type;
    public $discount_value;
    public $valid_from;
    public $valid_to;
    public $max_uses;
    public $used_count;
    public $is_active;
    public $created_at;
    public function __construct(
        int $coupon_id,
        string $coupon_code = '',
        string $discount_type = 'percentage',
        string $discount_value = '0.00',
        string $valid_from = '',
        string $valid_to = '',
        string $max_uses = '0',
        string $used_count = '0',
        string $is_active = '1',
        string $created_at = ''
    ) {
        $this->coupon_id = $coupon_id;
        $this->coupon_code = $coupon_code;
        $this->discount_type = $discount_type;
        $this->discount_value = $discount_value;
        $this->valid_from = $valid_from;
        $this->valid_to = $valid_to;
        $this->max_uses = $max_uses;
        $this->used_count = $used_count;
        $this->is_active = $is_active;
        $this->created_at = $created_at;
    }
    public function getDisplayLabel(): string {
        if ($this->coupon_code !== '') {
            return sprintf('%s %s %s %s', $this->coupon_code, $this->discount_type, $this->discount_value, $this->discount_type === 'percentage' ? '%' : '');
        }
        return _('Coupon #') . (string) $this->coupon_id;
    }
    public function toArray(): array {
        return array(
            'coupon_id' => $this->coupon_id,
            'coupon_code' => $this->coupon_code,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'max_uses' => $this->max_uses,
            'used_count' => $this->used_count,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        );
    }
    public static function fromArray(array $data): self {
        return new self(
            (int) ($data['coupon_id'] ?? 0),
            (string) ($data['coupon_code'] ?? ''),
            (string) ($data['discount_type'] ?? 'percentage'),
            (string) ($data['discount_value'] ?? '0.00'),
            (string) ($data['valid_from'] ?? ''),
            (string) ($data['valid_to'] ?? ''),
            (string) ($data['max_uses'] ?? '0'),
            (string) ($data['used_count'] ?? '0'),
            (string) ($data['is_active'] ?? '1'),
            (string) ($data['created_at'] ?? '')
        );
    }
}