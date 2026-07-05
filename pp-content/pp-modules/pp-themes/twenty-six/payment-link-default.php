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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $data['lang']['payment_link']?> - <?php echo $data['brand']['name'];?></title>
    <link rel="shortcut icon" href="<?php echo $data['brand']['favicon'];?>">
    <?php
       echo pp_assets('head');
    ?>

    <?php
        $seoTitle = trim($data['options']['seo_title'] ?? '');
        $seoDesc  = trim($data['options']['seo_description'] ?? '');
        $seoKey   = trim($data['options']['seo_keywords'] ?? '');
        $analyticsCode = trim($data['options']['analytics_code'] ?? '');

        if ($seoTitle !== '' && $seoTitle !== '--') {
            echo '<title>' . htmlspecialchars($seoTitle) . '</title>' . PHP_EOL;
            echo '<meta name="title" content="' . htmlspecialchars($seoTitle) . '">' . PHP_EOL;
            echo '<meta property="og:title" content="' . htmlspecialchars($seoTitle) . '">' . PHP_EOL;
        }

        if ($seoDesc !== '' && $seoDesc !== '--') {
            echo '<meta name="description" content="' . htmlspecialchars($seoDesc) . '">' . PHP_EOL;
            echo '<meta property="og:description" content="' . htmlspecialchars($seoDesc) . '">' . PHP_EOL;
        }

        if ($seoKey !== '' && $seoKey !== '--') {
            echo '<meta name="keywords" content="' . htmlspecialchars($seoKey) . '">' . PHP_EOL;
        }

        if ($analyticsCode !== '' && $analyticsCode !== '--') {
            echo $analyticsCode;
        }

        $bgStyle = 'background-color:#f8f9fa;';
        if (!empty($data['options']['enable_bg_image']) && $data['options']['enable_bg_image'] === 'enabled' && !empty($data['options']['background_image'])) {
            $bgImage = $data['options']['background_image'];
            $bgStyle = "background-image: url('{$bgImage}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;";
        }
        $primaryColor = !empty($data['options']['primary_color']) ? $data['options']['primary_color'] : '#15803d';
    ?>

    <style>
        body {
            background: 
                radial-gradient(at 0% 0%, rgba(34, 197, 94, 0.05) 0px, transparent 40%), 
                radial-gradient(at 100% 0%, rgba(163, 230, 53, 0.03) 0px, transparent 40%), 
                #ECEEF8 !important;
            min-height: 100vh;
        }

        .card {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(21, 128, 61, 0.12) !important;
            border-radius: 24px !important;
            box-shadow: 0 16px 36px rgba(21, 128, 61, 0.04) !important;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            overflow: visible !important;
            position: relative;
        }
        .card:hover {
            box-shadow: 0 24px 48px rgba(21, 128, 61, 0.08) !important;
        }

        .btn-primary {
            --tblr-btn-border-color: transparent;
            --tblr-btn-hover-border-color: transparent;
            --tblr-btn-active-border-color: transparent;
            --tblr-btn-color: <?php echo $data['options']['text_color'] ?? '#ffffff';?> !important;
            --tblr-btn-bg: <?php echo $primaryColor;?> !important;
            --tblr-btn-hover-bg: <?php echo pp_hexToRgba($primaryColor, 0.85)?> !important;
            --tblr-btn-active-bg: <?php echo pp_hexToRgba($primaryColor, 0.90)?> !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            transition: all 0.25s ease !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(21, 128, 61, 0.2) !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            box-shadow: 0 6px 20px rgba(21, 128, 61, 0.3) !important;
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(1px);
        }

        .form-label {
            font-weight: 600 !important;
            color: #1e293b !important;
            font-size: 0.85rem !important;
            margin-bottom: 6px !important;
        }

        .form-control, .form-select {
            border: 1px solid rgba(21, 128, 61, 0.15) !important;
            border-radius: 10px !important;
            padding: 10px 14px !important;
            background-color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.25s ease !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #22c55e !important;
            box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.2) !important;
            background-color: #ffffff !important;
        }

        .input-group-text {
            background-color: rgba(21, 128, 61, 0.05) !important;
            border: 1px solid rgba(21, 128, 61, 0.15) !important;
            color: #15803d !important;
            font-weight: 600 !important;
            border-radius: 10px 0 0 10px !important;
        }
        .input-group .form-control {
            border-radius: 0 10px 10px 0 !important;
        }

        .pp-text-logo {
            font-size: 32px;
            font-weight: 700;
            color: <?php echo $primaryColor;?>;
            letter-spacing: -0.04em;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-transform: none;
        }

        .language-trigger {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
            color: <?php echo $primaryColor;?>;
            transition: all 0.3s ease;
            z-index: 10;
        }
        .language-trigger:hover {
            transform: rotate(20deg) scale(1.05);
            opacity: 0.8;
        }

        .secure-badge-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
            margin-top: 1rem;
        }
        .secure-badge-icon {
            padding: 14px;
            background-color: <?php echo pp_hexToRgba($primaryColor, 0.08)?>;
            border-radius: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }
        .card:hover .secure-badge-icon {
            transform: scale(1.08);
            background-color: <?php echo pp_hexToRgba($primaryColor, 0.12)?>;
        }
    </style>
</head>
<body style="<?= $bgStyle ?> font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <?php $brand_name = htmlspecialchars($data['brand']['name'] ?? ''); $brand_logo = $data['brand']['logo'] ?? $data['brand']['favicon']; ?>
            <?php if(!empty($brand_logo) && $brand_logo !== '--'): ?>
                <img src="<?php echo $brand_logo; ?>" alt="<?php echo $brand_name; ?>" class="pp-brand-logo-img" style="height:42px; object-fit:contain;" />
            <?php else: ?>
                <div class="pp-text-logo"><?php echo $brand_name; ?></div>
            <?php endif; ?>
        </div>
        <div class="card card-md">
            <!-- Language Selector Trigger -->
            <div class="language-trigger" data-bs-target="#modal-language" data-bs-toggle="modal" title="Change Language">
                <svg xmlns="http://www.w3.org/2000/svg" style=" padding: 8px; background-color: <?php echo pp_hexToRgba($primaryColor, 0.08)?>; border-radius: 100%; width: 36px; height: 36px; " viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-language">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 6.371c0 4.418 -2.239 6.629 -5 6.629" />
                    <path d="M4 6.371h7" />
                    <path d="M5 9c0 2.144 2.252 3.908 6 4" />
                    <path d="M12 20l4 -9l4 9" />
                    <path d="M19.1 18h-6.2" />
                    <path d="M6.694 3l.793 .582" />
                </svg>
            </div>

            <div class="card-body text-center pb-0">
                <div class="secure-badge-container">
                    <div class="secure-badge-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="<?php echo $primaryColor;?>" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-shield-lock">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
                            <path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                            <path d="M12 12l0 2.5" />
                        </svg>
                    </div>
                    <h3 class="mb-1 text-dark fw-bold" style="font-size: 1.35rem;"><?php echo $data['lang']['payment_link']?></h3>
                    <div class="d-flex align-items-center gap-1 text-success justify-content-center fw-semibold" style="font-size: 0.8rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v.5" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /><path d="M15 19l2 2l4 -4" /></svg>
                        Secure 256-bit SSL Checkout
                    </div>
                </div>
            </div>

            <div class="card-body pt-2">
                <form action="" method="POST" id="form" enctype="multipart/form-data">
                    <?php pp_renderFormFields('payment-link-default', $data); ?>
                    <button type="submit" id="payButton" class="btn btn-primary w-100 mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M3 8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -8" />
                            <path d="M3 10l18 0" />
                            <path d="M7 15l.01 0" />
                            <path d="M11 15l2 0" />
                        </svg> 
                        <?php echo $data['lang']['pay_now']?>
                    </button>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-language" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scrollableLabel"><?php echo $data['lang']['select_language']?></h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"> 
                    <div class="form-group mt-1">
                        <label for="" class="form-label"><?php echo $data['lang']['language']?> <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <select class="form-select" id="model-languages" onchange="hitLanguage()">
                                <option value="" selected><?php echo $data['lang']['select_a_language']?></option>
                                <?php foreach ($data['supported_languages'] ?? [] as $code => $language): ?>
                                    <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($language) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal"><?php echo $data['lang']['close']?></button>
                </div>
            </div>
        </div>
    </div>


    <?php
       echo pp_assets('footer');
    ?>


    <script data-cfasync="false">
        function hitLanguage(){
            var language = document.querySelector("#model-languages").value;

            if(language !== ""){
                location.href = '?lang='+language;
            }
        }
        
        $(document).ready(function() {
            $('#form').on('submit', function(e) {
                e.preventDefault(); 

                var formData = $(this).serialize(); 

                document.querySelector("#payButton").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

                $.ajax({
                    url: '<?php echo pp_site_address(); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: formData, 
                    success: function(data) {
                        document.querySelector("#payButton").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -8" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg> <?php echo $data['lang']['pay_now']?>';

                        if (data.status == "true") {
                            location.href = data.redirect;
                        } else {
                            createToast({
                                title: data.title,
                                description: data.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                timeout: 6000
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        createToast({
                            title: '<?php echo addslashes($data['lang']['something_wrong'])?>',
                            description: '<?php echo addslashes($data['lang']['support_contact_text'])?>',
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
