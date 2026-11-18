<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\FA\Coupons;

require_once __DIR__ . '/../../../../../ksf_Infrastructure/FA/2.4.3/includes/session.inc';

if (!defined('TB_PREF')) {
    $path_to_root = dirname(__DIR__, 2);
    require_once $path_to_root . '/includes/session.inc';
}