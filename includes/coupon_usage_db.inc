<?php
/**
 * DB functions for fa_coupon_usage table
 */

function write_coupon_usage($usage_id, $data) {
    if ($usage_id) {
        $sql = "UPDATE `" . TB_PREF . "fa_coupon_usage` SET ";
        $sets = array();
        foreach (array('coupon_id', 'customer_person_id', 'invoice_id', 'discount_amount') as $field) {
            if (isset($data[$field])) {
                $sets[] = "`$field` = " . db_escape($data[$field]);
            }
        }
        $sql .= implode(', ', $sets) . " WHERE `usage_id` = " . db_escape($usage_id);
    } else {
        $sql = "INSERT INTO `" . TB_PREF . "fa_coupon_usage` SET ";
        $sets = array();
        foreach (array('coupon_id', 'customer_person_id', 'invoice_id', 'discount_amount') as $field) {
            if (isset($data[$field])) {
                $sets[] = "`$field` = " . db_escape($data[$field]);
            }
        }
        $sql .= implode(', ', $sets);
    }
    
    db_query($sql, "Failed to write coupon usage");
    return $usage_id ? $usage_id : db_insert_id();
}

function get_coupon_usage($usage_id) {
    $sql = "SELECT cu.*, c.coupon_code, c.discount_type, c.discount_value
        FROM `" . TB_PREF . "fa_coupon_usage` cu
        LEFT JOIN `" . TB_PREF . "fa_coupons` c ON cu.coupon_id = c.coupon_id
        WHERE cu.`usage_id` = " . db_escape($usage_id);
    $result = db_query($sql, "Failed to get coupon usage");
    return db_fetch($result);
}

function get_coupon_usage_history($coupon_id) {
    $sql = "SELECT cu.*, p.name as customer_name
        FROM `" . TB_PREF . "fa_coupon_usage` cu
        LEFT JOIN `" . TB_PREF . "crm_persons` p ON cu.customer_person_id = p.id
        WHERE cu.`coupon_id` = " . db_escape($coupon_id) . "
        ORDER BY cu.`used_at` DESC";
    return db_query($sql, "Failed to get coupon usage history");
}

function get_customer_coupon_usage($customer_person_id) {
    $sql = "SELECT cu.*, c.coupon_code, c.discount_type
        FROM `" . TB_PREF . "fa_coupon_usage` cu
        LEFT JOIN `" . TB_PREF . "fa_coupons` c ON cu.coupon_id = c.coupon_id
        WHERE cu.`customer_person_id` = " . db_escape($customer_person_id) . "
        ORDER BY cu.`used_at` DESC";
    return db_query($sql, "Failed to get customer coupon usage");
}

function delete_coupon_usage($usage_id) {
    $sql = "DELETE FROM `" . TB_PREF . "fa_coupon_usage` WHERE `usage_id` = " . db_escape($usage_id);
    db_query($sql, "Failed to delete coupon usage");
}
?>
