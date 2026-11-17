<?php
declare(strict_types=1);
namespace Ksfraser\FrontAccounting\FA\Coupons\Repository;
final class CouponRepository {
    private string $table = 'ksf_coupons_coupons';
    public function findAll(): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT * FROM " . TB_PREF . $this->table . " ORDER BY coupon_code ASC";
        $result = db_query($sql, 'Could not load coupons');
        if ($result === false) { return array(); }
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    public function findById(int $id): ?array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return null; }
        $sql = "SELECT * FROM " . TB_PREF . $this->table . " WHERE coupon_id = " . (int) $id;
        $result = db_query($sql, 'Could not load coupon');
        if ($result === false) { return null; }
        $row = db_fetch_assoc($result);
        return $row === false ? null : $row;
    }
    public function findByCode(string $code): ?array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return null; }
        $sql = "SELECT * FROM " . TB_PREF . $this->table . " WHERE coupon_code = " . db_escape($code);
        $result = db_query($sql, 'Could not load coupon by code');
        if ($result === false) { return null; }
        $row = db_fetch_assoc($result);
        return $row === false ? null : $row;
    }
    public function findActive(): array {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return array(); }
        $sql = "SELECT * FROM " . TB_PREF . $this->table .
            " WHERE is_active = 1 AND (valid_from IS NULL OR valid_from <= CURDATE()) AND (valid_to IS NULL OR valid_to >= CURDATE())" .
            " ORDER BY coupon_code ASC";
        $result = db_query($sql, 'Could not load active coupons');
        if ($result === false) { return array(); }
        $rows = array();
        while (($row = db_fetch_assoc($result)) !== false && $row !== null) { $rows[] = $row; }
        return $rows;
    }
    public function insertCoupon(array $data): int {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return 0; }
        $sql = "INSERT INTO " . TB_PREF . $this->table . " (
                coupon_code, discount_type, discount_value, valid_from, valid_to, max_uses, used_count, is_active, created_at)
            VALUES ("
            . db_escape($data['coupon_code'] ?? '') . ", "
            . db_escape($data['discount_type'] ?? 'percentage') . ", "
            . db_escape($data['discount_value'] ?? '0.00') . ", "
            . db_escape($data['valid_from'] ?? null) . ", "
            . db_escape($data['valid_to'] ?? null) . ", "
            . db_escape($data['max_uses'] ?? 0) . ", "
            . db_escape($data['used_count'] ?? 0) . ", "
            . db_escape($data['is_active'] ?? 1) . ", "
            . db_escape(date('Y-m-d H:i:s')) . ")";
        db_query($sql, 'Could not insert coupon');
        return db_insert_id();
    }
    public function updateCoupon(int $couponId, array $data): void {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return; }
        $sets = array();
        foreach (array('coupon_code', 'discount_type', 'discount_value', 'valid_from', 'valid_to', 'max_uses', 'used_count', 'is_active') as $field) {
            if (isset($data[$field])) {
                $sets[] = "`$field` = " . db_escape($data[$field]);
            }
        }
        if (empty($sets)) return;
        $sets[] = "updated_at = " . db_escape(date('Y-m-d H:i:s'));
        $sql = "UPDATE " . TB_PREF . $this->table . " SET " . implode(', ', $sets)
            . " WHERE coupon_id = " . db_escape($couponId);
        db_query($sql, 'Could not update coupon');
    }
    public function deleteCoupon(int $couponId): void {
        if (!function_exists('db_query') || !defined('TB_PREF')) { return; }
        db_query("UPDATE " . TB_PREF . $this->table . " SET is_active = 0 WHERE coupon_id = " . db_escape($couponId), 'Could not deactivate coupon');
    }
}