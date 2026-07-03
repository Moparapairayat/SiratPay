<?php
    if (!defined('PipraPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (!canAccessPage(json_decode($global_response_permission['response'][0]['permission'], true), 'dashboard', $global_user_response['response'][0]['role'])) {
        http_response_code(403);
        exit('Access denied. You need permission to perform this action. Please contact the admin.');
    }
?>

<style>
  #chart-gateway-statistics .apexcharts-legend.apexcharts-align-center.apx-legend-position-bottom {
      top: 263px !important;
  }

  /* Override page background to premium radial gradient with brand glow blobs */
  .page-wrapper {
      background: 
          radial-gradient(at 0% 0%, rgba(34, 197, 94, 0.04) 0px, transparent 40%), 
          radial-gradient(at 100% 0%, rgba(163, 230, 53, 0.02) 0px, transparent 40%), 
          #ECEEF8 !important;
  }

  /* ============================================================
     DASHBOARD PREMIUM CARDS & CHARTS
     ============================================================ */
  /* ---- Stat Card Base ---- */
  .pp-metric-card {
      position: relative;
      overflow: hidden;
      transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease;
      border: none !important;
      border-radius: 20px !important;
      min-height: 110px;
  }

  /* No top border — using gradient bg instead */
  .pp-metric-card::before { display: none; }

  .pp-metric-card:hover {
      transform: translateY(-7px) scale(1.01);
      box-shadow: 0 24px 48px rgba(0, 0, 0, 0.22) !important;
  }

  /* Full gradient backgrounds — Unique Next-Gen Colors */
  .pp-metric-card--indigo {
      background: linear-gradient(135deg, #0b3924 0%, #15803d 50%, #22c55e 100%) !important;
      border: 1px solid rgba(34, 197, 94, 0.25) !important;
      box-shadow: 0 8px 24px rgba(34, 197, 94, 0.25) !important;
  }
  .pp-metric-card--indigo .pp-metric-label { color: #a3e635; }
  .pp-metric-card--indigo .pp-metric-icon { 
      background: rgba(163, 230, 53, 0.12) !important; 
      color: #a3e635 !important; 
      border-color: rgba(163, 230, 53, 0.25);
  }
  .pp-metric-card--indigo .pp-metric-sub { color: rgba(180, 215, 195, 0.6); }

  .pp-metric-card--amber {
      background: linear-gradient(135deg, #451a03 0%, #b45309 50%, #f59e0b 100%) !important;
      border: 1px solid rgba(245, 158, 11, 0.25) !important;
      box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25) !important;
  }
  .pp-metric-card--amber .pp-metric-label { color: #fde047; }
  .pp-metric-card--amber .pp-metric-icon { 
      background: rgba(253, 224, 71, 0.12) !important; 
      color: #fde047 !important; 
      border-color: rgba(253, 224, 71, 0.25);
  }
  .pp-metric-card--amber .pp-metric-sub { color: rgba(253, 230, 138, 0.6); }

  .pp-metric-card--rose {
      background: linear-gradient(135deg, #500724 0%, #9d174d 50%, #db2777 100%) !important;
      border: 1px solid rgba(219, 39, 119, 0.25) !important;
      box-shadow: 0 8px 24px rgba(219, 39, 119, 0.25) !important;
  }
  .pp-metric-card--rose .pp-metric-label { color: #fca5a5; }
  .pp-metric-card--rose .pp-metric-icon { 
      background: rgba(252, 165, 165, 0.12) !important; 
      color: #fca5a5 !important; 
      border-color: rgba(252, 165, 165, 0.25);
  }
  .pp-metric-card--rose .pp-metric-sub { color: rgba(252, 228, 228, 0.6); }

  .pp-metric-card--teal {
      background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%) !important;
      border: 1px solid rgba(37, 99, 235, 0.25) !important;
      box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25) !important;
  }
  .pp-metric-card--teal .pp-metric-label { color: #60a5fa; }
  .pp-metric-card--teal .pp-metric-icon { 
      background: rgba(96, 165, 250, 0.12) !important; 
      color: #60a5fa !important; 
      border-color: rgba(96, 165, 250, 0.25);
  }
  .pp-metric-card--teal .pp-metric-sub { color: rgba(191, 219, 254, 0.6); }

  /* White text on colored cards with custom labels */
  .pp-metric-label {
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.09em;
      margin-bottom: 6px;
  }

  .pp-metric-value {
      font-size: 2.1rem;
      font-weight: 800;
      color: #ffffff;
      line-height: 1;
      letter-spacing: -0.02em;
  }

  .pp-metric-sub {
      font-size: 0.7rem;
      font-weight: 500;
      margin-top: 5px;
  }

  /* Icon circle */
  .pp-metric-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      backdrop-filter: blur(8px);
  }

  .pp-metric-icon svg {
      width: 22px;
      height: 22px;
  }

  /* Decorative background blobs */
  .pp-blob1 {
      position: absolute;
      width: 180px; height: 180px;
      border-radius: 50%;
      background: rgba(34, 197, 94, 0.04);
      right: -50px; bottom: -60px;
      pointer-events: none;
  }

  .pp-blob2 {
      position: absolute;
      width: 90px; height: 90px;
      border-radius: 50%;
      background: rgba(163, 230, 53, 0.03);
      right: 40px; bottom: 10px;
      pointer-events: none;
  }

  .pp-blob3 {
      position: absolute;
      width: 60px; height: 60px;
      border-radius: 50%;
      background: rgba(34, 197, 94, 0.03);
      left: -10px; top: -10px;
      pointer-events: none;
  }

  /* Make ApexCharts sparkline white on colored cards */
  .pp-metric-card .apexcharts-series path[fill] {
      fill: rgba(34,197,94,0.1) !important;
  }
  .pp-metric-card .apexcharts-series path[stroke] {
      stroke: #a3e635 !important;
  }
  .pp-metric-card .apexcharts-tooltip {
      background: rgba(5,18,13,0.9);
      border: 1px solid rgba(163, 230, 53, 0.3);
      color: #fff;
  }

  /* Premium Glassmorphism Chart Cards */
  .pp-chart-card {
      background: rgba(255, 255, 255, 0.76) !important;
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(34, 197, 94, 0.1) !important;
      border-radius: 24px !important; /* Slightly more rounded for high-end look */
      box-shadow: 0 10px 30px rgba(34, 197, 94, 0.03) !important;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      overflow: hidden;
  }

  .pp-chart-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 18px 44px rgba(34, 197, 94, 0.07) !important;
  }

  .pp-chart-card .card-header {
      background: rgba(255, 255, 255, 0.4) !important;
      border-bottom: 1px solid rgba(34, 197, 94, 0.06) !important;
      padding: 18px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
  }

  .pp-chart-card .card-title {
      font-size: 0.98rem;
      font-weight: 750;
      color: #0f172a;
      display: flex;
      align-items: center;
  }

  /* Customize global apexchart styling inside dashboard */
  .apexcharts-grid-lines line {
      stroke: rgba(34, 197, 94, 0.04) !important;
  }
  .apexcharts-xaxis-tick {
      stroke: rgba(34, 197, 94, 0.04) !important;
  }

  /* Page header — Nexora OS forest-green brand v3 */
  .pp-page-header {
      background: linear-gradient(135deg, #020a06 0%, #051a10 50%, #0d3824 100%);
      border-radius: 20px;
      padding: 32px 36px;
      margin-bottom: 28px;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(163, 230, 53, 0.18);
      box-shadow: 0 12px 36px rgba(5, 18, 13, 0.4);
  }

  /* Grid overlay for futuristic vibe */
  .pp-page-header::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: 
          linear-gradient(rgba(163, 230, 53, 0.04) 1px, transparent 1px),
          linear-gradient(90deg, rgba(163, 230, 53, 0.04) 1px, transparent 1px);
      background-size: 20px 20px;
      background-position: center;
      opacity: 0.8;
  }

  /* Glow shapes */
  .pp-header-glow-1 {
      position: absolute;
      right: -80px; top: -80px;
      width: 320px; height: 320px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(163, 230, 53, 0.16) 0%, transparent 70%);
      pointer-events: none;
  }

  .pp-header-glow-2 {
      position: absolute;
      left: 10%; bottom: -100px;
      width: 250px; height: 250px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(34, 197, 94, 0.12) 0%, transparent 70%);
      pointer-events: none;
  }

  .pp-page-header-title {
      font-size: 1.85rem;
      font-weight: 900;
      color: #ffffff;
      margin: 0;
      letter-spacing: -0.03em;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  }

  .pp-page-header-sub {
      font-size: 0.72rem;
      color: #a3e635; /* Neon Lime Accent */
      margin: 0 0 4px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.12em;
  }

  /* Modern Metallic Cron Badge */
  .pp-cron-badge {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(163, 230, 53, 0.22);
      border-radius: 14px;
      padding: 10px 20px;
      text-align: left;
      backdrop-filter: blur(12px);
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: inset 0 0 12px rgba(255, 255, 255, 0.02);
  }

  .pp-cron-badge-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #a3e635;
      box-shadow: 0 0 10px #a3e635, 0 0 20px #a3e635;
      animation: ppPulse 1.8s infinite;
  }

  .pp-cron-badge-info .label {
      font-size: 0.65rem;
      color: rgba(180, 215, 195, 0.7);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      display: block;
      line-height: 1.2;
  }

  .pp-cron-badge-info .value {
      font-size: 0.88rem;
      color: #ffffff;
      font-weight: 700;
      line-height: 1.3;
  }

  @keyframes ppPulse {
      0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 8px #a3e635; }
      50% { transform: scale(1.15); opacity: 0.5; box-shadow: 0 0 2px #a3e635; }
  }
</style>

<!-- ==================== PAGE HEADER ==================== -->
<div class="pp-page-header d-print-none">
    <div class="pp-header-glow-1"></div>
    <div class="pp-header-glow-2"></div>
    
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3" style="position:relative; z-index:2;">
        <div>
            <p class="pp-page-header-sub">System Statistics</p>
            <h1 class="pp-page-header-title">Dashboard Overview</h1>
        </div>
        <div class="pp-cron-badge">
            <div class="pp-cron-badge-dot"></div>
            <div class="pp-cron-badge-info">
                <span class="label">System Cron Status</span>
                <span class="value">
                    <?php
                        $lastCron = get_env('last-cron-invocation');
                        $userTimezone = ($global_response_brand['response'][0]['timezone'] ?? '') === '--' || empty($global_response_brand['response'][0]['timezone']) ? 'Asia/Dhaka' : $global_response_brand['response'][0]['timezone'];
                        echo empty($lastCron) ? 'Active' : 'Sync ' . timeAgo($lastCron);
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- ==================== PAGE BODY ==================== -->
<div class="page-body" style="padding-top: 0;">
    <div class="container-xl">
        <div class="row g-3 mb-3">

            <!-- Total Payments -->
            <div class="col-lg-3 col-md-6">
                <div class="card pp-metric-card pp-metric-card--indigo">
                    <div class="pp-blob1"></div>
                    <div class="pp-blob2"></div>
                    <div class="pp-blob3"></div>
                    <div class="card-body" style="padding: 16px 20px 8px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <p class="pp-metric-label mb-0">Total Payments</p>
                            <div class="pp-metric-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            </div>
                        </div>
                        <div class="pp-metric-value"><?php
                            $count = 0;
                            $response_dashboard_info = json_decode( getData( $db_prefix.'transaction', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status NOT IN ("initiated")'), true );
                            if($response_dashboard_info['status'] == true){ foreach($response_dashboard_info['response'] as $row){ $count++; } }
                            echo number_format($count, 0);
                        ?></div>
                        <div class="pp-metric-sub">All transactions</div>
                    </div>
                    <div id="chart-total-payment"></div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="col-lg-3 col-md-6">
                <div class="card pp-metric-card pp-metric-card--amber">
                    <div class="pp-blob1"></div>
                    <div class="pp-blob2"></div>
                    <div class="pp-blob3"></div>
                    <div class="card-body" style="padding: 16px 20px 8px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <p class="pp-metric-label mb-0">Pending Payments</p>
                            <div class="pp-metric-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                        </div>
                        <div class="pp-metric-value"><?php
                            $count = 0;
                            $response_dashboard_info = json_decode(getData($db_prefix.'transaction',' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "pending"'),true);
                            if($response_dashboard_info['status'] == true){ foreach($response_dashboard_info['response'] as $row){ $count++; } }
                            echo number_format($count, 0);
                        ?></div>
                        <div class="pp-metric-sub">Awaiting confirmation</div>
                    </div>
                    <div id="chart-pending-payment"></div>
                </div>
            </div>

            <!-- Unpaid Invoices -->
            <div class="col-lg-3 col-md-6">
                <div class="card pp-metric-card pp-metric-card--rose">
                    <div class="pp-blob1"></div>
                    <div class="pp-blob2"></div>
                    <div class="pp-blob3"></div>
                    <div class="card-body" style="padding: 16px 20px 8px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <p class="pp-metric-label mb-0">Unpaid Invoices</p>
                            <div class="pp-metric-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            </div>
                        </div>
                        <div class="pp-metric-value"><?php
                            $count = 0;
                            $response_dashboard_info = json_decode(getData($db_prefix.'invoice',' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "unpaid"'),true);
                            if($response_dashboard_info['status'] == true){ foreach($response_dashboard_info['response'] as $row){ $count++; } }
                            echo number_format($count, 0);
                        ?></div>
                        <div class="pp-metric-sub">Need collection</div>
                    </div>
                    <div id="chart-unpaid-invoice"></div>
                </div>
            </div>

            <!-- Customers -->
            <div class="col-lg-3 col-md-6">
                <div class="card pp-metric-card pp-metric-card--teal">
                    <div class="pp-blob1"></div>
                    <div class="pp-blob2"></div>
                    <div class="pp-blob3"></div>
                    <div class="card-body" style="padding: 16px 20px 8px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <p class="pp-metric-label mb-0">Customers</p>
                            <div class="pp-metric-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                        </div>
                        <div class="pp-metric-value"><?php
                            $count = 0;
                            $response_dashboard_info = json_decode(getData($db_prefix.'customer',' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'"'),true);
                            if($response_dashboard_info['status'] == true){ foreach($response_dashboard_info['response'] as $row){ $count++; } }
                            echo number_format($count, 0);
                        ?></div>
                        <div class="pp-metric-sub">Registered users</div>
                    </div>
                    <div id="chart-customer"></div>
                </div>
            </div>

            <!-- Transaction Statistics Chart -->
            <div class="col-lg-6 col-md-6">
                <div class="card pp-chart-card" style="animation: ppFadeUp 0.4s ease 0.25s both;">
                    <div class="card-header pp-chart-card">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align:-3px; filter: drop-shadow(0 0 5px rgba(99, 102, 241, 0.6));"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            Transaction Statistics
                        </h3>
                        <div class="card-actions btn-actions">
                            <div class="position-relative">
                                <span class="dashboard-transaction-statistics-loading"></span>
                                <svg onclick="toggleFilter('filterDropdown-transaction-statistics')" style="cursor:pointer"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icon-tabler-filter">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"></path>
                                </svg>
                                <!-- Dropdown -->
                                <div id="filterDropdown-transaction-statistics" class="card shadow position-absolute end-0 mt-2 p-3" style="width: 300px; display:none; z-index:1050;">
                                    <label class="form-label fw-bold mb-2">Filter By</label>
                                    <select class="form-select mb-2" id="dateFilter-transaction-statistics" onchange="handleFilterChangeTransactionStatistics(this.value)">
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This week</option>
                                        <option value="last_week">Last week</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="this_year" selected>This year</option>
                                        <option value="previous_year">Previous year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                    <div id="customRange-transaction-statistics" class="d-none">
                                        <label class="form-label mt-2">Start Date</label>
                                        <input type="date" id="startDate-transaction-statistics" class="form-control">
                                        <label class="form-label mt-2">End Date</label>
                                        <input type="date" id="endDate-transaction-statistics" class="form-control">
                                        <button class="btn btn-primary mt-3 w-100" onclick="applyCustomRangeTransactionStatistics()">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart-transaction-statistics" style="height: 303px !important; min-height: 303px !important;"></div>
                    </div>
                </div>
            </div>

            <!-- Gateway Statistics Chart -->
            <div class="col-lg-6 col-md-6">
                <div class="card pp-chart-card" style="animation: ppFadeUp 0.4s ease 0.3s both;">
                    <div class="card-header pp-chart-card">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2" style="vertical-align:-3px; filter: drop-shadow(0 0 5px rgba(20, 184, 166, 0.6));"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                            Gateway Statistics
                        </h3>
                        <div class="card-actions btn-actions">
                            <div class="position-relative">
                                <span class="dashboard-gateway-statistics-loading"></span>
                                <svg onclick="toggleFilter('filterDropdown-gateway-statistics')" style="cursor:pointer"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icon-tabler-filter">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z"></path>
                                </svg>
                                <!-- Dropdown -->
                                <div id="filterDropdown-gateway-statistics" class="card shadow position-absolute end-0 mt-2 p-3" style="width: 300px; display:none; z-index:1050;">
                                    <label class="form-label fw-bold mb-2">Filter By</label>
                                    <select class="form-select mb-2" id="dateFilter-gateway-statistics" onchange="handleFilterChangeGatewayStatistics(this.value)">
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This week</option>
                                        <option value="last_week">Last week</option>
                                        <option value="this_month">This month</option>
                                        <option value="last_month">Last month</option>
                                        <option value="this_year" selected>This year</option>
                                        <option value="previous_year">Previous year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                    <div id="customRange-gateway-statistics" class="d-none">
                                        <label class="form-label mt-2">Start Date</label>
                                        <input type="date" id="startDate-gateway-statistics" class="form-control">
                                        <label class="form-label mt-2">End Date</label>
                                        <input type="date" id="endDate-gateway-statistics" class="form-control">
                                        <button class="btn btn-primary mt-3 w-100" onclick="applyCustomRangeGatewayStatistics()">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart-gateway-statistics" style="height: 303px !important;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    <?php
      $labels = [];
      $data   = [];

      // initialize last 30 days with 0
      for ($i = 29; $i >= 0; $i--) {
          $date = date('Y-m-d', strtotime("-$i days"));
          $labels[$date] = 0;
      }

      $response_dashboard_info = json_decode(getData($db_prefix.'customer',' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND created_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(created_date)', 'DATE(created_date) as day, COUNT(*) as total FROM'),true);
      foreach($response_dashboard_info['response'] as $row){                       
          if (isset($labels[$row['day']])) {
              $labels[$row['day']] = (int)$row['total'];
          }
      }

      // prepare JS arrays
      $chartLabels = json_encode(array_keys($labels));
      $chartData   = json_encode(array_values($labels));
    ?>
  
    function loadChartCustomer() {
        if (!window.ApexCharts) return;

        new ApexCharts(document.getElementById("chart-customer"), {
            chart: {
                type: "area",
                fontFamily: "inherit",
                height: 40,
                sparkline: { enabled: true },
                animations: { enabled: false }
            },
            dataLabels: { enabled: false },
            fill: {
                type: "solid",
                colors: [
                    "color-mix(in srgb, transparent, var(--tblr-success) 16%)"
                ]
            },
            stroke: {
                width: 2,
                curve: "smooth",
                lineCap: "round"
            },
            series: [{
                name: "Customers",
                data: <?= $chartData ?>
            }],
            tooltip: { theme: "dark" },
            grid: { strokeDashArray: 4 },
            xaxis: {
                type: "datetime",
                labels: { show: false },
                axisBorder: { show: false },
                tooltip: { enabled: false }
            },
            yaxis: {
                labels: { show: false }
            },
            labels: <?= $chartLabels ?>,
            colors: [
                "color-mix(in srgb, transparent, var(--tblr-success) 100%)"
            ],
            legend: { show: false }
        }).render();
    }

    loadChartCustomer();


    <?php
      $labels = [];
      $data   = [];

      // initialize last 30 days with 0
      for ($i = 29; $i >= 0; $i--) {
          $date = date('Y-m-d', strtotime("-$i days"));
          $labels[$date] = 0;
      }

      $response_dashboard_info = json_decode(getData($db_prefix.'invoice',' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "unpaid" AND created_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(created_date)', 'DATE(created_date) as day, COUNT(*) as total FROM'),true);
      foreach($response_dashboard_info['response'] as $row){                       
          if (isset($labels[$row['day']])) {
              $labels[$row['day']] = (int)$row['total'];
          }
      }

      // prepare JS arrays
      $chartLabels = json_encode(array_keys($labels));
      $chartData   = json_encode(array_values($labels));
    ?>
  
    function loadChartUnpaidInvoice() {
        if (!window.ApexCharts) return;

        new ApexCharts(document.getElementById("chart-unpaid-invoice"), {
            chart: {
                type: "area",
                fontFamily: "inherit",
                height: 40,
                sparkline: { enabled: true },
                animations: { enabled: false }
            },
            dataLabels: { enabled: false },
            fill: {
                type: "solid",
                colors: [
                    "color-mix(in srgb, transparent, var(--tblr-danger) 16%)"
                ]
            },
            stroke: {
                width: 2,
                curve: "smooth",
                lineCap: "round"
            },
            series: [{
                name: "Unpaid Invoices",
                data: <?= $chartData ?>
            }],
            tooltip: { theme: "dark" },
            grid: { strokeDashArray: 4 },
            xaxis: {
                type: "datetime",
                labels: { show: false },
                axisBorder: { show: false },
                tooltip: { enabled: false }
            },
            yaxis: {
                labels: { show: false }
            },
            labels: <?= $chartLabels ?>,
            colors: [
                "color-mix(in srgb, transparent, var(--tblr-danger) 100%)"
            ],
            legend: { show: false }
        }).render();
    }

    loadChartUnpaidInvoice();

    <?php
      $labels = [];
      $data   = [];

      // initialize last 30 days with 0
      for ($i = 29; $i >= 0; $i--) {
          $date = date('Y-m-d', strtotime("-$i days"));
          $labels[$date] = 0;
      }

      $response_dashboard_info = json_decode(getData($db_prefix.'transaction',' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status = "pending" AND created_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(created_date)', 'DATE(created_date) as day, COUNT(*) as total FROM'),true);
      foreach($response_dashboard_info['response'] as $row){                       
          if (isset($labels[$row['day']])) {
              $labels[$row['day']] = (int)$row['total'];
          }
      }

      // prepare JS arrays
      $chartLabels = json_encode(array_keys($labels));
      $chartData   = json_encode(array_values($labels));
    ?>
  
    function loadChartPendingPayment() {
        if (!window.ApexCharts) return;

        new ApexCharts(document.getElementById("chart-pending-payment"), {
            chart: {
                type: "area",
                fontFamily: "inherit",
                height: 40,
                sparkline: { enabled: true },
                animations: { enabled: false }
            },
            dataLabels: { enabled: false },
            fill: {
                type: "solid",
                colors: [
                    "color-mix(in srgb, transparent, var(--tblr-warning) 16%)"
                ]
            },
            stroke: {
                width: 2,
                curve: "smooth",
                lineCap: "round"
            },
            series: [{
                name: "Pending Payments",
                data: <?= $chartData ?>
            }],
            tooltip: { theme: "dark" },
            grid: { strokeDashArray: 4 },
            xaxis: {
                type: "datetime",
                labels: { show: false },
                axisBorder: { show: false },
                tooltip: { enabled: false }
            },
            yaxis: {
                labels: { show: false }
            },
            labels: <?= $chartLabels ?>,
            colors: [
                "color-mix(in srgb, transparent, var(--tblr-warning) 100%)"
            ],
            legend: { show: false }
        }).render();
    }

    loadChartPendingPayment();

    <?php
      $labels = [];
      $data   = [];

      // initialize last 30 days with 0
      for ($i = 29; $i >= 0; $i--) {
          $date = date('Y-m-d', strtotime("-$i days"));
          $labels[$date] = 0;
      }

      $response_dashboard_info = json_decode(getData($db_prefix.'transaction', ' WHERE brand_id = "'.$global_response_brand['response'][0]['brand_id'].'" AND status NOT IN ("initiated", "expired") AND created_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(created_date)', 'DATE(created_date) as day, COUNT(*) as total FROM'),true);
      foreach($response_dashboard_info['response'] as $row){                       
          if (isset($labels[$row['day']])) {
              $labels[$row['day']] = (int)$row['total'];
          }
      }

      // prepare JS arrays
      $chartLabels = json_encode(array_keys($labels));
      $chartData   = json_encode(array_values($labels));
    ?>
  
    function loadChartTotalPayment() {
        if (!window.ApexCharts) return;

        new ApexCharts(document.getElementById("chart-total-payment"), {
            chart: {
                type: "area",
                fontFamily: "inherit",
                height: 40,
                sparkline: { enabled: true },
                animations: { enabled: false }
            },
            dataLabels: { enabled: false },
            fill: {
                type: "solid",
                colors: [
                    "color-mix(in srgb, transparent, var(--tblr-primary) 16%)"
                ]
            },
            stroke: {
                width: 2,
                curve: "smooth",
                lineCap: "round"
            },
            series: [{
                name: "Total Payments",
                data: <?= $chartData ?>
            }],
            tooltip: { theme: "dark" },
            grid: { strokeDashArray: 4 },
            xaxis: {
                type: "datetime",
                labels: { show: false },
                axisBorder: { show: false },
                tooltip: { enabled: false }
            },
            yaxis: {
                labels: { show: false }
            },
            labels: <?= $chartLabels ?>,
            colors: [
                "color-mix(in srgb, transparent, var(--tblr-primary) 100%)"
            ],
            legend: { show: false }
        }).render();
    }

    loadChartTotalPayment();

    function load_dashboard_transaction_statistics(){
        const el = document.getElementById('filterDropdown-transaction-statistics');
        el.style.display = el.style.display = 'none';

        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var date = $('#dateFilter-transaction-statistics').val();
        var start = $('#startDate-transaction-statistics').val();
        var end = $('#endDate-transaction-statistics').val();
        
        const loaderTx = document.querySelector(".dashboard-transaction-statistics-loading");
        if (loaderTx) {
            loaderTx.innerHTML = '<div class="spinner-border spinner-border-sm text-primary me-2">  <span class="visually-hidden">Loading...</span></div>';
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "dashboard-transaction-statistics", csrf_token: csrf_token_default, date: date, start: start, end: end},
            dataType: 'json',
            success: function (res) {
                const loaderTxDone = document.querySelector(".dashboard-transaction-statistics-loading");
                if (loaderTxDone) {
                    loaderTxDone.innerHTML = '';
                }

                document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                    input.value = res.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                    input.value = res.csrf_token;
                });

                if (res.status === 'true') {
                    if (chartTransactionStatistics) {
                      chartTransactionStatistics.destroy();
                    }

                    chartTransactionStatistics = new ApexCharts(
                      document.getElementById("chart-transaction-statistics"),
                      {
                        chart: {
                          type: "area",
                          height: 288,
                          fontFamily: "inherit",
                          toolbar: { show: false },
                          sparkline: { enabled: false },
                          animations: { enabled: true, easing: 'easeinout', speed: 800 }
                        },

                        stroke: {
                          width: 3,
                          curve: "smooth",
                          lineCap: "round"
                        },

                        fill: {
                          type: "gradient",
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.22,
                            opacityTo: 0.01,
                            stops: [0, 90, 100]
                          }
                        },

                        markers: {
                          size: 0,
                          hover: {
                            size: 6,
                            sizeOffset: 3
                          }
                        },

                        series: [
                          {
                            name: "Total",
                            data: res.total
                          },
                          {
                            name: "Complete",
                            data: res.complete
                          },
                          {
                            name: "Pending",
                            data: res.pending
                          }
                        ],

                        xaxis: {
                          type: "category",
                          categories: res.labels,
                          labels: { padding: 0 }
                        },

                        yaxis: {
                          labels: { padding: 4 }
                        },

                        grid: {
                          strokeDashArray: 4,
                          padding: {
                            top: -20,
                            right: 0,
                            left: -4,
                            bottom: -4
                          }
                        },

                        tooltip: {
                          theme: "dark"
                        },

                        legend: {
                          show: true,
                          position: "bottom",
                          offsetY: 8,
                          markers: { radius: 12 }
                        },

                        colors: [
                          "var(--tblr-primary)",
                          "var(--tblr-success)",
                          "var(--tblr-warning)"
                        ]
                      }
                    );

                    chartTransactionStatistics.render();

                    load_dashboard_gateway_statistics();
                } else {
                    createToast({
                        title: res.title,
                        description: res.message,
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                }
            },
            error: function (xhr, status, error) {
                createToast({
                    title: 'Something Wrong!',
                    description: 'For further assistance, please contact our support team.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
            }
        });
    }

    load_dashboard_transaction_statistics();

    function handleFilterChangeTransactionStatistics(value) {
        const custom = document.getElementById('customRange-transaction-statistics');

        if (value === 'custom') {
            custom.classList.remove('d-none');
        } else {
            custom.classList.add('d-none');

            load_dashboard_transaction_statistics();
        }
    }

    function applyCustomRangeTransactionStatistics() {
        const start = document.getElementById('startDate-transaction-statistics').value;
        const end   = document.getElementById('endDate-transaction-statistics').value;

        if (!start && !end) {
            createToast({
                title: "Action required",
                description: 'Please select at least one date',
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 6000,
                top: 70
            });
        }else{
          load_dashboard_transaction_statistics();
        }
    }

    function load_dashboard_gateway_statistics(){
        const el = document.getElementById('filterDropdown-gateway-statistics');
        el.style.display = el.style.display = 'none';

        var csrf_token_default = $('input[name="csrf_token_default"]').val();
        var date = $('#dateFilter-gateway-statistics').val();
        var start = $('#startDate-gateway-statistics').val();
        var end = $('#endDate-gateway-statistics').val();
        
        const loaderGw = document.querySelector(".dashboard-gateway-statistics-loading");
        if (loaderGw) {
            loaderGw.innerHTML = '<div class="spinner-border spinner-border-sm text-primary me-2">  <span class="visually-hidden">Loading...</span></div>';
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo $site_url.$path_admin ?>/dashboard',
            data: {action: "dashboard-gateway-statistics", csrf_token: csrf_token_default, date: date, start: start, end: end},
            dataType: 'json',
            success: function (res) {
                const loaderGwDone = document.querySelector(".dashboard-gateway-statistics-loading");
                if (loaderGwDone) {
                    loaderGwDone.innerHTML = '';
                }

                document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
                    input.value = res.csrf_token;
                });
                document.querySelectorAll('input[name="csrf_token_default"]').forEach(input => {
                    input.value = res.csrf_token;
                });

                if (res.status === 'true') {

                    if (chartGatewayStatistics) {
                      chartGatewayStatistics.destroy();
                    }


                    // Transform data to totals like Chart.js
                    const data = res.gateway_labels.map(label => {
                        return res.data[label] ? res.data[label].reduce((a,b)=>a+b,0) : 0;
                    });

                        chartGatewayStatistics = new ApexCharts(
                            document.getElementById("chart-gateway-statistics"),
                            {
                                chart: {
                                    type: "donut",
                                    height: 290,
                                    fontFamily: "inherit",
                                    animations: { enabled: true, easing: 'easeinout', speed: 800 }
                                },
                                plotOptions: {
                                    pie: {
                                        donut: {
                                            size: '72%',
                                            labels: {
                                                show: true,
                                                name: {
                                                    show: true,
                                                    fontSize: '12px',
                                                    color: '#64748b',
                                                    offsetY: -10
                                                },
                                                value: {
                                                    show: true,
                                                    fontSize: '20px',
                                                    fontWeight: '800',
                                                    color: '#0f172a',
                                                    offsetY: 4,
                                                    formatter: function (val) {
                                                        return val;
                                                    }
                                                },
                                                total: {
                                                    show: true,
                                                    label: 'Total Txs',
                                                    color: '#64748b',
                                                    formatter: function (w) {
                                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                series: data,
                                labels: res.gateway_labels,
                                colors: res.colors,
                                stroke: {
                                    show: true,
                                    width: 2.5,
                                    colors: ['#ffffff']
                                },
                                tooltip: { theme: "dark", fillSeriesColor: false },
                                legend: { 
                                    show: true, 
                                    position: "bottom", 
                                    offsetY: 0,
                                    markers: { radius: 12 }
                                }
                            }
                        );
                        chartGatewayStatistics.render();

                } else {
                    createToast({
                        title: res.title,
                        description: res.message,
                        svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                        timeout: 6000,
                        top: 70
                    });
                }
            },
            error: function (xhr, status, error) {
                createToast({
                    title: 'Something Wrong!',
                    description: 'For further assistance, please contact our support team.',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 70
                });
            }
        });
    }

    function handleFilterChangeGatewayStatistics(value) {
        const custom = document.getElementById('customRange-gateway-statistics');

        if (value === 'custom') {
            custom.classList.remove('d-none');
        } else {
            custom.classList.add('d-none');

            load_dashboard_gateway_statistics();
        }
    }

    function applyCustomRangeGatewayStatistics() {
        const start = document.getElementById('startDate-gateway-statistics').value;
        const end   = document.getElementById('endDate-gateway-statistics').value;

        if (!start && !end) {
            createToast({
                title: "Action required",
                description: 'Please select at least one date',
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 6000,
                top: 70
            });
        }else{
          load_dashboard_gateway_statistics();
        }
    }

    function toggleFilter(ClassfilterDropdown) {
        const el = document.getElementById(ClassfilterDropdown);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }
</script>