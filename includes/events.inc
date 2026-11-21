<?php
declare(strict_types=1);

if (!function_exists('hook_invoke_all')) {
    return;
}

function coupons_state_changed($coupon_code, $from_state, $to_state, $meta = array()) {
    $data = array(
        'coupon_code' => $coupon_code,
        'from_state' => $from_state,
        'to_state' => $to_state,
        'meta' => $meta,
    );
    hook_invoke_all('COUPONS_STATE_CHANGED', $data);
}