<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\FA\Coupons\DTO;
final class CouponDTO {
    public $coupon_code;
    public $discount_type;
    public $discount_value;
    public $valid_from;
    public $valid_to;
    public $applies_to_type;
    public $applies_to_id;
    public $apply_order;
    public $stackable;
    public $apply_to_original;
    public $created_at;
    public $updated_at;
    public function __construct(
        string $coupon_code,
        string $discount_type = 'percentage',
        string $discount_value = '0.00',
        string $valid_from = '',
        string $valid_to = '',
        string $applies_to_type = 'cart_wide',
        int $applies_to_id = 0,
        int $apply_order = 0,
        bool $stackable = true,
        bool $apply_to_original = true,
        string $created_at = '',
        string $updated_at = ''
    ) {
        $this->coupon_code = $coupon_code;
        $this->discount_type = $discount_type;
        $this->discount_value = $discount_value;
        $this->valid_from = $valid_from;
        $this->valid_to = $valid_to;
        $this->applies_to_type = $applies_to_type;
        $this->applies_to_id = $applies_to_id;
        $this->apply_order = $apply_order;
        $this->stackable = $stackable;
        $this->apply_to_original = $apply_to_original;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
    public function getDisplayLabel(): string {
        $amount = $this->discount_value . ($this->discount_type === 'percentage' ? '%' : '');
        return sprintf('%s - %s %s', $this->coupon_code, $this->discount_type, $amount);
    }
    public function toArray(): array {
        return array(
            'coupon_code' => $this->coupon_code,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'applies_to_type' => $this->applies_to_type,
            'applies_to_id' => $this->applies_to_id,
            'apply_order' => $this->apply_order,
            'stackable' => $this->stackable,
            'apply_to_original' => $this->apply_to_original,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
    public static function fromArray(array $data): self {
        return new self(
            (string) ($data['coupon_code'] ?? ''),
            (string) ($data['discount_type'] ?? 'percentage'),
            (string) ($data['discount_value'] ?? '0.00'),
            (string) ($data['valid_from'] ?? ''),
            (string) ($data['valid_to'] ?? ''),
            (string) ($data['applies_to_type'] ?? 'cart_wide'),
            (int) ($data['applies_to_id'] ?? 0),
            (int) ($data['apply_order'] ?? 0),
            (bool) ($data['stackable'] ?? true),
            (bool) ($data['apply_to_original'] ?? true),
            (string) ($data['created_at'] ?? ''),
            (string) ($data['updated_at'] ?? '')
        );
    }
}