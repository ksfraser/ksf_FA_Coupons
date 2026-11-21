<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\FA\Coupons;

if (!defined('TB_PREF')) {
    // Deployed, __DIR__ is <fa-root>/modules/ksf_FA_Coupons/includes.
    // In a standalone checkout the file is absent, so this guards instead of
    // aborting before FrontAccounting has had a chance to bootstrap.
    $path_to_root = dirname(__DIR__, 3);
    if (is_file($path_to_root . '/includes/session.inc')) {
        require_once $path_to_root . '/includes/session.inc';
    }
}
