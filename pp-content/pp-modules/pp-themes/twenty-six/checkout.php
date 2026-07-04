<?php
    if (!defined('PipraPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if(isset($_GET['lang'])){
        if($_GET['lang'] !== ""){
            pp_set_lang($_GET['lang']);
?>
            <script>
                location.href = '?lang=';
            </script>
<?php
            exit();
        }
    }

    if(isset($_GET['cancel'])){
        pp_set_transaction_status($data['transaction']['ref'], 'canceled');
?>
        <script>
            location.href = '<?php echo pp_checkout_address();?>';
        </script>
<?php
        exit();
    }

    $pp_gateways_mfs    = pp_gateways('mfs', $data);
    $pp_gateways_bank   = pp_gateways('bank', $data);
    $pp_gateways_global = pp_gateways('global', $data);

    $subtotal    = money_round(($data['transaction']['amount'] ?? 0) - ($data['transaction']['discount_amount'] ?? 0) - ($data['transaction']['processing_fee'] ?? 0), 2);
    $fee         = money_round($data['transaction']['processing_fee'] ?? 0, 2);
    $total       = money_round($data['transaction']['amount'] ?? 0, 2);
    $currency    = htmlspecialchars($data['transaction']['currency'] ?? '');
    $trx_ref     = htmlspecialchars($data['transaction']['ref'] ?? '');
    $brand_name  = htmlspecialchars($data['brand']['name'] ?? '');
    $brand_logo  = $data['brand']['logo'] ?? $data['brand']['favicon'];
    $primary_col = $data['options']['primary_color'] ?? '#1a7d5a';
    $text_col    = $data['options']['text_color'] ?? '#ffffff';

    // Theme asset base URL
    $theme_asset_url = $data['options']['site_url'] ?? '';
    // fallback: build from site_url if available via pp_ helper
    if(function_exists('pp_site_url')) $theme_asset_url = pp_site_url();

    $bgStyle = '';
    if (!empty($data['options']['enable_bg_image']) && $data['options']['enable_bg_image'] === 'enabled' && !empty($data['options']['background_image'])) {
        $bgImage = $data['options']['background_image'];
        $bgStyle = "background-image: url('{$bgImage}'); background-size: cover; background-position: center; background-repeat: no-repeat;";
    }

    $seoTitle      = trim($data['options']['seo_title'] ?? '');
    $seoDesc       = trim($data['options']['seo_description'] ?? '');
    $analyticsCode = trim($data['options']['analytics_code'] ?? '');
    $support       = $data['brand']['support'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $data['lang']['checkout'] ?? 'Checkout'; ?> - <?php echo $brand_name; ?></title>
    <link rel="shortcut icon" href="<?php echo $data['brand']['favicon']; ?>">
    <?php echo pp_assets('head'); ?>
    <?php if($seoTitle !== '' && $seoTitle !== '--'): ?>
        <title><?php echo htmlspecialchars($seoTitle); ?></title>
        <meta name="title" content="<?php echo htmlspecialchars($seoTitle); ?>">
    <?php endif; ?>
    <?php if($seoDesc !== '' && $seoDesc !== '--'): ?>
        <meta name="description" content="<?php echo htmlspecialchars($seoDesc); ?>">
    <?php endif; ?>
    <?php if($analyticsCode !== '' && $analyticsCode !== '--'): echo $analyticsCode; endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: <?php echo $primary_col; ?>;
            --primary-light: <?php echo pp_hexToRgba($primary_col, 0.10); ?>;
            --primary-mid:   <?php echo pp_hexToRgba($primary_col, 0.18); ?>;
            --primary-btn:   <?php echo $primary_col; ?>;
            --primary-text:  <?php echo $text_col; ?>;
            --radius-lg: 18px;
            --radius-md: 11px;
            --radius-sm: 8px;
            --shadow-card: 0 32px 80px rgba(0,0,0,0.30), 0 8px 24px rgba(0,0,0,0.14);
            --transition: 0.2s cubic-bezier(0.4,0,0.2,1);
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 12px;
            position: relative;
            overflow-x: hidden;
        }

        /* ── Background ── */
        .pp-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: <?php echo $bgStyle ?: "url('pp-content/pp-modules/pp-themes/twenty-six/assets/stadium_bg.png') center/cover no-repeat"; ?>;
        }
        .pp-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(10,25,55,0.65) 0%, rgba(5,35,22,0.58) 100%);
            backdrop-filter: blur(4px);
        }

        /* ── Card entrance animation ── */
        @keyframes ppFadeUp {
            from { opacity: 0; transform: translateY(28px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes ppShimmer {
            0%   { background-position: -200% center; }
            100% { background-position:  200% center; }
        }
        @keyframes ppPulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.7; }
        }
        @keyframes ppSpin {
            to { transform: rotate(360deg); }
        }

        /* ── Main Card ── */
        .pp-checkout-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 940px;
            display: flex;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-card);
            background: #fff;
            min-height: 560px;
            border: 1px solid rgba(255,255,255,0.18);
            animation: ppFadeUp 0.45s cubic-bezier(0.22,1,0.36,1) both;
        }

        /* ── LEFT PANE ── */
        .pp-left {
            width: 38%;
            flex-shrink: 0;
            background: linear-gradient(180deg, #f7fafd 0%, #eef3f9 100%);
            display: flex;
            flex-direction: column;
            padding: 0;
            border-right: 1px solid #dde4ef;
        }

        .pp-left-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid #e4e9f0;
            background: #fff;
        }
        .pp-back-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
        }
        .pp-back-btn:hover { background: var(--primary-mid); }
        .pp-brand-row { display: flex; align-items: center; gap: 8px; }
        .pp-brand-logo {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #e0e5ee;
        }
        .pp-brand-name { font-size: 13px; font-weight: 600; color: #1a1d23; }

        .pp-left-body { padding: 18px; flex: 1; display: flex; flex-direction: column; gap: 14px; }

        /* Trx ID row */
        .pp-trx-row {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1px solid #e4e9f0;
            border-radius: var(--radius-sm);
            padding: 7px 10px;
        }
        .pp-trx-label { font-size: 11px; color: #7a8599; font-weight: 500; flex-shrink: 0; }
        .pp-trx-value { font-size: 11px; color: #3a4052; font-weight: 600; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: 'Courier New', monospace; }
        .pp-copy-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .pp-copy-btn:hover { background: var(--primary-light); }
        .pp-copy-btn svg { width: 14px; height: 14px; }

        /* Amount badge */
        .pp-amount-badge {
            background: linear-gradient(135deg, var(--primary) 0%, <?php echo pp_hexToRgba($primary_col, 0.80); ?> 100%);
            border-radius: var(--radius-md);
            padding: 16px 16px 14px;
            color: var(--primary-text);
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 24px <?php echo pp_hexToRgba($primary_col, 0.35); ?>;
        }
        .pp-amount-badge::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 110px; height: 110px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }
        .pp-amount-badge::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.10) 50%, transparent 60%);
            background-size: 200% 100%;
            animation: ppShimmer 3s infinite linear;
        }
        .pp-amount-badge-label { font-size: 11px; opacity: 0.80; font-weight: 400; letter-spacing: 0.3px; }
        .pp-amount-badge-value { font-size: 28px; font-weight: 800; letter-spacing: -1px; margin-top: 3px; }
        .pp-countdown {
            position: absolute;
            top: 10px; right: 12px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,0.18);
            padding: 3px 9px;
            border-radius: 20px;
            transition: background 0.3s, color 0.3s;
        }
        .pp-countdown.warning {
            background: rgba(239,68,68,0.25);
            animation: ppPulse 1s infinite;
        }

        /* Receipt rows */
        .pp-receipt {
            background: #fff;
            border: 1px solid #e2e8f2;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .pp-receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            font-size: 13px;
            color: #5a6275;
            border-bottom: 1px solid #f4f6fb;
        }
        .pp-receipt-row:last-child { border-bottom: none; }
        .pp-receipt-row.total {
            font-weight: 700;
            color: #1a1d23;
            font-size: 14px;
            background: linear-gradient(90deg, #f8fafd 0%, #fff 100%);
        }
        .pp-receipt-row.total span:last-child { color: var(--primary); font-size: 15px; }

        /* Bottom nav */
        .pp-left-footer {
            padding: 12px 18px;
            border-top: 1px solid #e4e9f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }
        .pp-footer-links { display: flex; gap: 14px; }
        .pp-footer-link {
            font-size: 12px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 3px;
            transition: opacity 0.15s;
        }
        .pp-footer-link:hover { opacity: 0.7; }
        .pp-lang-btn {
            font-size: 12px;
            color: #7a8599;
            background: none;
            border: 1px solid #e0e5ee;
            border-radius: 5px;
            padding: 4px 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            font-family: inherit;
            transition: border-color 0.15s;
        }
        .pp-lang-btn:hover { border-color: var(--primary); color: var(--primary); }

        /* ── RIGHT PANE ── */
        .pp-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .pp-right-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-bottom: 1px solid #f0f3f8;
        }
        .pp-welcome { font-size: 14px; font-weight: 600; color: #3a4052; }
        .pp-login-btn {
            font-size: 12px;
            color: var(--primary);
            border: 1px solid var(--primary);
            background: none;
            padding: 4px 14px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            font-family: inherit;
            transition: background 0.15s, color 0.15s;
        }
        .pp-login-btn:hover { background: var(--primary); color: var(--primary-text); }

        /* Category Tabs */
        .pp-tabs {
            display: flex;
            border-bottom: 1px solid #f0f3f8;
            padding: 0 20px;
            gap: 0;
        }
        .pp-tab {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 12px 14px;
            font-size: 12.5px;
            font-weight: 500;
            color: #7a8599;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: color var(--transition), border-color var(--transition), background var(--transition);
            user-select: none;
            white-space: nowrap;
            border-radius: 4px 4px 0 0;
        }
        .pp-tab svg { width: 15px; height: 15px; flex-shrink: 0; transition: transform var(--transition); }
        .pp-tab.active { color: var(--primary); border-bottom-color: var(--primary); font-weight: 600; }
        .pp-tab.active svg { transform: scale(1.12); }
        .pp-tab:hover { color: var(--primary); background: <?php echo pp_hexToRgba($primary_col, 0.04); ?>; }

        /* Gateway Grid */
        .pp-right-body { flex: 1; padding: 16px 20px; overflow-y: auto; }
        .pp-tab-panel { display: none; }
        .pp-tab-panel.active { display: block; }
        .pp-gw-label { font-size: 12px; color: #7a8599; font-weight: 500; margin-bottom: 10px; }

        .pp-gw-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .pp-gw-grid::-webkit-scrollbar { width: 4px; }
        .pp-gw-grid::-webkit-scrollbar-thumb { background: #d0d7e3; border-radius: 4px; }
        .pp-gw-grid::-webkit-scrollbar-track { background: transparent; }

        .pp-gw-card {
            border: 1.5px solid #e8edf3;
            border-radius: 10px;
            padding: 14px 8px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition), background var(--transition);
            position: relative;
            background: #fff;
            user-select: none;
            aspect-ratio: 1 / 1;
        }
        .pp-gw-card:hover {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary-light), 0 4px 14px rgba(0,0,0,0.07);
            transform: translateY(-2px) scale(1.01);
            background: #fff;
        }
        .pp-gw-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light), 0 4px 16px <?php echo pp_hexToRgba($primary_col, 0.15); ?>;
            background: #fff;
            transform: translateY(-1px);
        }
        .pp-gw-card.selected .pp-check {
            opacity: 1;
            transform: scale(1);
        }
        .pp-check {
            position: absolute;
            top: 5px; right: 5px;
            width: 17px; height: 17px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.4);
            transition: opacity 0.2s cubic-bezier(0.34,1.56,0.64,1), transform 0.2s cubic-bezier(0.34,1.56,0.64,1);
            box-shadow: 0 2px 6px <?php echo pp_hexToRgba($primary_col, 0.40); ?>;
        }
        .pp-check svg { width: 10px; height: 10px; color: #fff; }
        .pp-gw-logo-wrap {
            width: 100%;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        .pp-gw-logo { max-width: 90px; max-height: 42px; width: 100%; object-fit: contain; }
        .pp-gw-name { font-size: 10px; font-weight: 500; color: #6b7490; text-align: center; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; padding: 0 4px; }

        /* Empty state */
        .pp-empty {
            text-align: center;
            padding: 40px 20px;
            color: #aab2c4;
            font-size: 13px;
        }
        .pp-empty svg { width: 40px; height: 40px; margin-bottom: 10px; opacity: 0.4; }

        /* Right Footer */
        .pp-right-footer {
            padding: 14px 20px;
            border-top: 1px solid #f0f3f8;
        }

        /* Pay Button */
        .pp-pay-btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--primary) 0%, <?php echo pp_hexToRgba($primary_col, 0.82); ?> 100%);
            color: var(--primary-text);
            border: none;
            border-radius: var(--radius-md);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: opacity var(--transition), transform var(--transition), box-shadow var(--transition);
            box-shadow: 0 4px 18px <?php echo pp_hexToRgba($primary_col, 0.38); ?>;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        .pp-pay-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 35%, rgba(255,255,255,0.12) 50%, transparent 65%);
            background-size: 200% 100%;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .pp-pay-btn:hover:not(:disabled)::after { opacity: 1; animation: ppShimmer 0.7s linear; }
        .pp-pay-btn:hover:not(:disabled) {
            opacity: 0.93;
            transform: translateY(-2px);
            box-shadow: 0 8px 28px <?php echo pp_hexToRgba($primary_col, 0.45); ?>;
        }
        .pp-pay-btn:disabled {
            opacity: 0.38;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .pp-pay-btn:active:not(:disabled) { transform: translateY(0); box-shadow: 0 3px 12px <?php echo pp_hexToRgba($primary_col, 0.30); ?>; }
        .pp-btn-spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: ppSpin 0.7s linear infinite;
            display: none;
            flex-shrink: 0;
        }
        .pp-pay-btn.loading .pp-btn-spinner { display: block; }
        .pp-pay-btn.loading .pp-btn-text { opacity: 0.75; }

        .pp-terms {
            font-size: 10.5px;
            color: #aab2c4;
            text-align: center;
            margin-top: 8px;
            line-height: 1.5;
        }
        .pp-terms a { color: var(--primary); text-decoration: none; }

        .pp-brand-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 10px;
        }
        .pp-powered {
            font-size: 11px;
            color: #aab2c4;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .pp-secure-badge {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            color: #aab2c4;
            background: #f4f7fb;
            border-radius: 4px;
            padding: 3px 8px;
        }
        .pp-secure-badge svg { width: 11px; height: 11px; color: #4caf82; }

        /* Support panel */
        .pp-support-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .pp-support-card {
            border: 1.5px solid #e8edf3;
            border-radius: var(--radius-sm);
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            transition: border-color 0.18s, background 0.18s;
            background: #fafbfc;
            cursor: pointer;
        }
        .pp-support-card:hover { border-color: var(--primary); background: var(--primary-light); }
        .pp-support-card svg { width: 24px; height: 24px; color: var(--primary); }
        .pp-support-card span { font-size: 12px; font-weight: 500; color: #3a4052; }

        /* FAQ Accordion */
        .pp-faq-list { display: flex; flex-direction: column; gap: 8px; }
        .pp-faq-item {
            border: 1px solid #e8edf3;
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .pp-faq-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 14px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #3a4052;
            background: #fafbfc;
            user-select: none;
        }
        .pp-faq-header svg { width: 16px; height: 16px; color: #aab2c4; flex-shrink: 0; transition: transform 0.2s; }
        .pp-faq-header.open svg { transform: rotate(180deg); }
        .pp-faq-body {
            display: none;
            padding: 10px 14px;
            font-size: 12.5px;
            color: #5a6275;
            line-height: 1.6;
            border-top: 1px solid #f0f3f8;
        }
        .pp-faq-body.open { display: block; }

        /* Modal Language */
        .pp-modal-overlay {
            position: fixed; inset: 0; z-index: 1000;
            background: rgba(15,30,60,0.45);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
        }
        .pp-modal-overlay.open { display: flex; }
        .pp-modal {
            background: #fff;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 380px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .pp-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .pp-modal-title { font-size: 16px; font-weight: 700; color: #1a1d23; }
        .pp-modal-close { background: none; border: none; cursor: pointer; color: #aab2c4; padding: 4px; border-radius: 4px; }
        .pp-modal-close:hover { color: #5a6275; }
        .pp-select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e4e9f0;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-family: inherit;
            color: #3a4052;
            appearance: none;
            cursor: pointer;
            background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") no-repeat right 10px center / 16px 16px;
        }
        .pp-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        .pp-modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
        .pp-btn-close {
            padding: 9px 18px;
            border: 1px solid #e4e9f0;
            border-radius: var(--radius-sm);
            background: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            color: #5a6275;
        }
        .pp-btn-close:hover { background: #f4f7fb; }

        /* Copy toast */
        .pp-toast {
            position: fixed;
            bottom: 24px; left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #1a1d23;
            color: #fff;
            padding: 9px 18px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.25s, transform 0.25s;
            z-index: 9999;
            pointer-events: none;
        }
        .pp-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

        /* Responsive */
        @media (max-width: 720px) {
            .pp-checkout-card { flex-direction: column; }
            .pp-left { width: 100%; border-right: none; border-bottom: 1px solid #e8edf3; }
            .pp-gw-grid { grid-template-columns: repeat(3, 1fr); max-height: 200px; }
        }
        @media (max-width: 480px) {
            .pp-gw-grid { grid-template-columns: repeat(2, 1fr); }
            .pp-tabs { overflow-x: auto; padding: 0 12px; }
            .pp-tab { padding: 11px 10px; font-size: 12px; }
        }
    </style>
</head>
<body>

<div class="pp-bg"></div>

<div class="pp-checkout-card">

    <!-- ═══════════════ LEFT PANE ═══════════════ -->
    <div class="pp-left">

        <div class="pp-left-header">
            <a class="pp-back-btn" href="<?php echo pp_checkout_address(); ?>?cancel" title="Cancel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div class="pp-brand-row">
                <img src="<?php echo $brand_logo; ?>" alt="<?php echo $brand_name; ?>" class="pp-brand-logo">
                <span class="pp-brand-name"><?php echo $brand_name; ?></span>
            </div>
            <div style="width:32px;"></div>
        </div>

        <div class="pp-left-body">

            <!-- Trx ID -->
            <div class="pp-trx-row">
                <span class="pp-trx-label">Trx ID:</span>
                <span class="pp-trx-value" id="pp-trx-ref"><?php echo $trx_ref; ?></span>
                <button class="pp-copy-btn" onclick="ppCopyTrxId()" title="Copy">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                </button>
            </div>

            <!-- Amount badge with countdown -->
            <div class="pp-amount-badge">
                <div class="pp-countdown" id="pp-countdown">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span id="pp-timer">10:00</span>
                </div>
                <div class="pp-amount-badge-label"><?php echo $data['lang']['you_are_paying'] ?? 'You are paying'; ?></div>
                <div class="pp-amount-badge-value"><?php echo $currency; ?><?php echo $total; ?></div>
            </div>

            <!-- Receipt breakdown -->
            <div class="pp-receipt">
                <div class="pp-receipt-row">
                    <span><?php echo $data['lang']['subtotal'] ?? 'Subtotal'; ?></span>
                    <span><?php echo $currency.$subtotal; ?></span>
                </div>
                <?php if(floatval($data['transaction']['discount_amount'] ?? 0) != 0): ?>
                <div class="pp-receipt-row">
                    <span><?php echo $data['lang']['discount'] ?? 'Discount'; ?></span>
                    <span style="color:#16a34a;">-<?php echo $currency.money_round($data['transaction']['discount_amount'], 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="pp-receipt-row">
                    <span><?php echo $data['lang']['convenience_charge'] ?? 'Convenience Charge'; ?></span>
                    <span><?php echo $currency.$fee; ?></span>
                </div>
                <div class="pp-receipt-row total">
                    <span><?php echo $data['lang']['total'] ?? 'Total amount'; ?></span>
                    <span><?php echo $currency.$total; ?></span>
                </div>
            </div>

        </div>

        <div class="pp-left-footer">
            <div class="pp-footer-links">
                <span class="pp-footer-link" onclick="ppSwitchTab('support')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M4 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3"/><path d="M15 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3"/><path d="M4 15V9a8 8 0 1 1 16 0v6"/></svg>
                    <?php echo $data['lang']['support'] ?? 'Support'; ?>
                </span>
                <?php if(!empty($data['faqs'])): ?>
                <span class="pp-footer-link" onclick="ppSwitchTab('faq')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><circle cx="12" cy="12" r="10"/><path d="M12 16v.01"/><path d="M12 13a2 2 0 0 0 .914-3.782A1.98 1.98 0 0 0 10.5 11"/></svg>
                    FAQ
                </span>
                <?php endif; ?>
            </div>
            <?php if(!empty($data['supported_languages'])): ?>
            <button class="pp-lang-btn" onclick="document.getElementById('pp-lang-modal').classList.add('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><path d="M9 6.371c0 4.418-2.239 6.629-5 6.629"/><path d="M4 6.371h7"/><path d="M5 9c0 2.144 2.252 3.908 6 4"/><path d="M12 20l4-9 4 9"/><path d="M19.1 18h-6.2"/></svg>
                <?php echo $data['lang']['language'] ?? 'Language'; ?>
            </button>
            <?php endif; ?>
        </div>

    </div><!-- /pp-left -->


    <!-- ═══════════════ RIGHT PANE ═══════════════ -->
    <div class="pp-right">

        <div class="pp-right-header">
            <span class="pp-welcome"><?php echo $data['lang']['welcome'] ?? 'Welcome!'; ?></span>
            <button class="pp-login-btn"><?php echo $data['lang']['login'] ?? 'Login'; ?></button>
        </div>

        <!-- Category Tabs -->
        <div class="pp-tabs" id="pp-tabs">
            <?php if($pp_gateways_global['status'] === true && !empty($pp_gateways_global['gateway'])): ?>
            <div class="pp-tab" data-panel="global" onclick="ppSwitchTab('global')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <span><?php echo $data['lang']['global'] ?? 'Global'; ?></span>
            </div>
            <?php endif; ?>
            <?php if($pp_gateways_mfs['status'] === true && !empty($pp_gateways_mfs['gateway'])): ?>
            <div class="pp-tab" data-panel="mfs" onclick="ppSwitchTab('mfs')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="20" rx="2"/><line x1="11" y1="5" x2="13" y2="5"/><circle cx="12" cy="18" r="1"/></svg>
                <span><?php echo $data['lang']['mobile_banking'] ?? 'Mobile Banking'; ?></span>
            </div>
            <?php endif; ?>
            <?php if($pp_gateways_bank['status'] === true && !empty($pp_gateways_bank['gateway'])): ?>
            <div class="pp-tab" data-panel="bank" onclick="ppSwitchTab('bank')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="21" x2="21" y2="21"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="5 6 12 3 19 6"/><line x1="4" y1="10" x2="4" y2="21"/><line x1="20" y1="10" x2="20" y2="21"/><line x1="8" y1="14" x2="8" y2="17"/><line x1="12" y1="14" x2="12" y2="17"/><line x1="16" y1="14" x2="16" y2="17"/></svg>
                <span><?php echo $data['lang']['net_banking'] ?? 'Net Banking'; ?></span>
            </div>
            <?php endif; ?>
            <div class="pp-tab" data-panel="support" onclick="ppSwitchTab('support')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3"/><path d="M15 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3"/><path d="M4 15V9a8 8 0 1 1 16 0v6"/></svg>
                <span><?php echo $data['lang']['support'] ?? 'Support'; ?></span>
            </div>
        </div>

        <!-- Tab Panels -->
        <div class="pp-right-body">

            <!-- Global / Card -->
            <?php if($pp_gateways_global['status'] === true && !empty($pp_gateways_global['gateway'])): ?>
            <div class="pp-tab-panel" id="pp-panel-global">
                <div class="pp-gw-label"><?php echo $data['lang']['pay_with_global'] ?? 'Pay with Global Gateway'; ?></div>
                <div class="pp-gw-grid">
                    <?php foreach($pp_gateways_global['gateway'] as $row): ?>
                    <div class="pp-gw-card" data-gateway-id="<?php echo $row['gateway_id']; ?>" onclick="ppSelectGateway(this, '<?php echo $row['gateway_id']; ?>')">
                        <div class="pp-check"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                        <div class="pp-gw-logo-wrap">
                            <img src="<?php echo $row['logo']; ?>" alt="<?php echo htmlspecialchars($row['display']); ?>" class="pp-gw-logo">
                        </div>
                        <div class="pp-gw-name"><?php echo htmlspecialchars($row['display']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- MFS / Mobile Banking -->
            <?php if($pp_gateways_mfs['status'] === true && !empty($pp_gateways_mfs['gateway'])): ?>
            <div class="pp-tab-panel" id="pp-panel-mfs">
                <div class="pp-gw-label"><?php echo $data['lang']['pay_with_mobile_banking'] ?? 'Pay with Mobile Banking'; ?></div>
                <div class="pp-gw-grid">
                    <?php foreach($pp_gateways_mfs['gateway'] as $row): ?>
                    <div class="pp-gw-card" data-gateway-id="<?php echo $row['gateway_id']; ?>" onclick="ppSelectGateway(this, '<?php echo $row['gateway_id']; ?>')">
                        <div class="pp-check"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                        <div class="pp-gw-logo-wrap">
                            <img src="<?php echo $row['logo']; ?>" alt="<?php echo htmlspecialchars($row['display']); ?>" class="pp-gw-logo">
                        </div>
                        <div class="pp-gw-name"><?php echo htmlspecialchars($row['display']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Bank / Net Banking -->
            <?php if($pp_gateways_bank['status'] === true && !empty($pp_gateways_bank['gateway'])): ?>
            <div class="pp-tab-panel" id="pp-panel-bank">
                <div class="pp-gw-label"><?php echo $data['lang']['pay_with_net_banking'] ?? 'Pay with Net Banking'; ?></div>
                <div class="pp-gw-grid">
                    <?php foreach($pp_gateways_bank['gateway'] as $row): ?>
                    <div class="pp-gw-card" data-gateway-id="<?php echo $row['gateway_id']; ?>" onclick="ppSelectGateway(this, '<?php echo $row['gateway_id']; ?>')">
                        <div class="pp-check"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                        <div class="pp-gw-logo-wrap">
                            <img src="<?php echo $row['logo']; ?>" alt="<?php echo htmlspecialchars($row['display']); ?>" class="pp-gw-logo">
                        </div>
                        <div class="pp-gw-name"><?php echo htmlspecialchars($row['display']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Support Panel -->
            <div class="pp-tab-panel" id="pp-panel-support">
                <div class="pp-gw-label"><?php echo $data['lang']['support'] ?? 'Support'; ?></div>
                <div class="pp-support-grid">
                    <?php if(!empty($support['email']) && $support['email'] != '--'): ?>
                    <a href="mailto:<?php echo $support['email']; ?>" target="_blank" class="pp-support-card">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span><?php echo $data['lang']['contact_email'] ?? 'Email'; ?></span>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($support['phone']) && $support['phone'] != '--'): ?>
                    <a href="tel:<?php echo $support['phone']; ?>" target="_blank" class="pp-support-card">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15.5 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>
                        <span><?php echo $data['lang']['contact_phone'] ?? 'Phone'; ?></span>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($support['whatsapp']) && $support['whatsapp'] != '--'): ?>
                    <a href="https://wa.me/<?php echo $support['whatsapp']; ?>" target="_blank" class="pp-support-card">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9z"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"/></svg>
                        <span>WhatsApp</span>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($support['telegram']) && $support['telegram'] != '--' && $support['telegram'] != 'https://t.me/'): ?>
                    <a href="<?php echo $support['telegram']; ?>" target="_blank" class="pp-support-card">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 10l-4 4 6 6 4-16-18 7 4 2 2 6 3-4"/></svg>
                        <span>Telegram</span>
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($support['website']) && $support['website'] != '--'): ?>
                    <a href="<?php echo $support['website']; ?>" target="_blank" class="pp-support-card">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span><?php echo $data['lang']['contact_website'] ?? 'Website'; ?></span>
                    </a>
                    <?php endif; ?>
                    <?php
                    $hasSupport = (!empty($support['email']) && $support['email'] != '--') ||
                                  (!empty($support['phone']) && $support['phone'] != '--') ||
                                  (!empty($support['whatsapp']) && $support['whatsapp'] != '--') ||
                                  (!empty($support['telegram']) && $support['telegram'] != '--' && $support['telegram'] != 'https://t.me/') ||
                                  (!empty($support['website']) && $support['website'] != '--');
                    if(!$hasSupport):
                    ?>
                    <div class="pp-empty" style="grid-column:1/-1;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        <div><?php echo $data['lang']['no_support_available'] ?? 'No support channels available.'; ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if(!empty($data['faqs'])): ?>
                <div class="pp-gw-label" style="margin-top:16px;">FAQ</div>
                <div class="pp-faq-list">
                    <?php foreach($data['faqs'] as $i => $faq): ?>
                    <div class="pp-faq-item">
                        <div class="pp-faq-header <?php echo ($i === 0) ? 'open' : ''; ?>" onclick="ppToggleFaq(this)">
                            <?php echo htmlspecialchars($faq['title']); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                        <div class="pp-faq-body <?php echo ($i === 0) ? 'open' : ''; ?>"><?php echo $faq['description']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div><!-- /pp-right-body -->

        <!-- Right Footer: Pay Button -->
        <div class="pp-right-footer">
            <button class="pp-pay-btn" id="pp-pay-btn" disabled onclick="ppProceedPayment()">
                <div class="pp-btn-spinner"></div>
                <span class="pp-btn-text"><?php echo $data['lang']['pay'] ?? 'Pay'; ?> <?php echo $currency.$total; ?></span>
            </button>
            <div class="pp-terms">
                <?php echo $data['lang']['payment_terms'] ?? 'By clicking "Pay" you agree to the <a href="#">Terms of Service</a>.'; ?>
            </div>
            <div class="pp-brand-footer">
                <div class="pp-powered">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    <?php echo $data['options']['watermark_text'] ?? 'Powered by BillPax'; ?>
                </div>
                <div class="pp-secure-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Secured
                </div>
            </div>
        </div>

    </div><!-- /pp-right -->

</div><!-- /pp-checkout-card -->

<!-- Copy Toast -->
<div class="pp-toast" id="pp-toast">Copied!</div>

<!-- Language Modal -->
<?php if(!empty($data['supported_languages'])): ?>
<div class="pp-modal-overlay" id="pp-lang-modal" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="pp-modal">
        <div class="pp-modal-header">
            <span class="pp-modal-title"><?php echo $data['lang']['select_language'] ?? 'Select Language'; ?></span>
            <button class="pp-modal-close" onclick="document.getElementById('pp-lang-modal').classList.remove('open')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <select class="pp-select" id="pp-lang-select" onchange="ppHitLanguage()">
            <option value=""><?php echo $data['lang']['select_a_language'] ?? 'Select a language'; ?></option>
            <?php foreach($data['supported_languages'] ?? [] as $code => $language): ?>
            <option value="<?php echo htmlspecialchars($code); ?>"><?php echo htmlspecialchars($language); ?></option>
            <?php endforeach; ?>
        </select>
        <div class="pp-modal-footer">
            <button class="pp-btn-close" onclick="document.getElementById('pp-lang-modal').classList.remove('open')"><?php echo $data['lang']['close'] ?? 'Close'; ?></button>
        </div>
    </div>
</div>
<?php endif; ?>

<?php echo pp_assets('footer'); ?>

<script data-cfasync="false">
(function(){
    'use strict';

    var checkoutBase = '<?php echo pp_checkout_address(); ?>';
    var selectedGatewayId = null;
    var TIMER_KEY = 'pp_timer_<?php echo md5($trx_ref); ?>';
    var TIMER_DURATION = 10 * 60; // 10 minutes

    /* ── Countdown Timer ── */
    function ppStartTimer() {
        var now = Math.floor(Date.now() / 1000);
        var stored = localStorage.getItem(TIMER_KEY);
        var endTime;

        if (stored) {
            endTime = parseInt(stored, 10);
            if (endTime <= now) {
                localStorage.removeItem(TIMER_KEY);
                endTime = now + TIMER_DURATION;
                localStorage.setItem(TIMER_KEY, endTime);
            }
        } else {
            endTime = now + TIMER_DURATION;
            localStorage.setItem(TIMER_KEY, endTime);
        }

        function tick() {
            var remaining = Math.max(0, endTime - Math.floor(Date.now() / 1000));
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;
            document.getElementById('pp-timer').textContent =
                (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;

            var cdEl = document.getElementById('pp-countdown');
            if (remaining <= 120) {
                cdEl.classList.add('warning');
            } else {
                cdEl.classList.remove('warning');
            }

            if (remaining <= 0) {
                localStorage.removeItem(TIMER_KEY);
                location.href = checkoutBase + '?cancel';
            }
        }

        tick();
        setInterval(tick, 1000);
    }

    /* ── Tab switching ── */
    function ppSwitchTab(panelId) {
        // Deactivate all tabs
        document.querySelectorAll('.pp-tab').forEach(function(t) { t.classList.remove('active'); });
        // Deactivate all panels
        document.querySelectorAll('.pp-tab-panel').forEach(function(p) { p.classList.remove('active'); });

        // Activate requested tab button (if exists in tab bar)
        var tabEl = document.querySelector('.pp-tab[data-panel="' + panelId + '"]');
        if (tabEl) tabEl.classList.add('active');

        // Activate panel
        var panelEl = document.getElementById('pp-panel-' + panelId);
        if (panelEl) panelEl.classList.add('active');
    }

    /* ── Gateway Selection ── */
    window.ppSelectGateway = function(el, gatewayId) {
        document.querySelectorAll('.pp-gw-card').forEach(function(c) { c.classList.remove('selected'); });
        el.classList.add('selected');
        selectedGatewayId = gatewayId;
        document.getElementById('pp-pay-btn').disabled = false;
    };

    /* ── Proceed Payment ── */
    window.ppProceedPayment = function() {
        if (!selectedGatewayId) return;
        var btn = document.getElementById('pp-pay-btn');
        btn.disabled = true;
        btn.classList.add('loading');
        var btnText = btn.querySelector('.pp-btn-text');
        if (btnText) btnText.textContent = '<?php echo $data['lang']['redirecting'] ?? 'Redirecting...'; ?>';
        location.href = checkoutBase + '?gateway=' + selectedGatewayId;
    };

    /* ── Copy Trx ID ── */
    window.ppCopyTrxId = function() {
        var val = document.getElementById('pp-trx-ref').textContent;
        navigator.clipboard.writeText(val).then(function() {
            var toast = document.getElementById('pp-toast');
            toast.classList.add('show');
            setTimeout(function() { toast.classList.remove('show'); }, 2200);
        }).catch(function() {
            var ta = document.createElement('textarea');
            ta.value = val; ta.style.position='fixed'; ta.style.opacity='0';
            document.body.appendChild(ta); ta.select(); document.execCommand('copy');
            document.body.removeChild(ta);
        });
    };

    /* ── Language ── */
    window.ppHitLanguage = function() {
        var lang = document.getElementById('pp-lang-select').value;
        if (lang) location.href = '?lang=' + lang;
    };

    /* ── FAQ Toggle ── */
    window.ppToggleFaq = function(header) {
        var body = header.nextElementSibling;
        header.classList.toggle('open');
        body.classList.toggle('open');
    };

    /* ── Expose tab switch globally ── */
    window.ppSwitchTab = ppSwitchTab;

    /* ── Init on DOM ready ── */
    document.addEventListener('DOMContentLoaded', function() {
        ppStartTimer();

        // Auto-activate first available tab
        var firstTab = document.querySelector('.pp-tab');
        if (firstTab) {
            ppSwitchTab(firstTab.getAttribute('data-panel'));
        } else {
            ppSwitchTab('support');
        }
    });
})();
</script>

</body>
</html>
