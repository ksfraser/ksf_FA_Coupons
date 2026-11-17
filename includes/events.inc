<?php
declare(strict_types=1);

if (!function_exists('hook_invoke_all')) {
    return;
}

function coupons_state_changed($coupon_id, $from_state, $to_state, $meta = array()) {
    hook_invoke_all('COUPONS_STATE_CHANGED', array(
        'coupon_id' => $coupon_id,
        'from_state' => $from_state,
        'to_state' => $to_state,
        'meta' => $meta,
    ));
}

function coupons_fee_added($item_id, $fee_id, $amount, $note = null) {
    hook_invoke_all('COUPONS_FEE_ADDED', array(
        'item_id' => $item_id,
        'fee_id' => $fee_id,
        'amount' => $amount,
        'note' => $note,
    ));
}
