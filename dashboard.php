
<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

// DASHBOARD STATISTICS

$totalMembers = (int)$pdo
    ->query("SELECT COUNT(*) FROM members")
    ->fetchColumn();

$totalTrainers = (int)$pdo
    ->query("SELECT COUNT(*) FROM trainers")
    ->fetchColumn();

$activeSubs = (int)$pdo
    ->query("SELECT COUNT(*) FROM subscriptions WHERE status = 'active'")
    ->fetchColumn();

$revenue = (float)$pdo
    ->query("SELECT COALESCE(SUM(amount), 0) FROM subscriptions")
    ->fetchColumn();

// RECENT SUBSCRIPTIONS

$recentSubs = $pdo->query("
    SELECT s.*, m.full_name
    FROM subscriptions s
    JOIN members m ON m.member_id = s.member_id
    ORDER BY s.subscription_id DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

?>

<!-- PAGE HEADING -->

<div class="page-heading">

    <div>
        <h3>Dashboard Overview</h3>

        <p>
            Welcome back! Here's what's happening at your gym.
        </p>
    </div>

    <div class="text-muted small">
        <i class="fa-regular fa-calendar me-1"></i>
        <?= date('d M Y') ?>
    </div>

</div>


<!-- WELCOME BANNER -->

<div class="welcome-banner mb-4">

    <div class="position-relative" style="z-index:2;">

        <h2>
            Welcome to FITNESS MANAGEMENT SYSTEM!
        </h2>

        <p style="color:black;font-family:arial;">
            Manage your gym smarter, monitor your members
            and grow your fitness business.
        </p>

        <a href="/fitness-system/members/index.php"
           class="btn btn-light btn-sm mt-3 fw-bold">

            <i class="fa-solid fa-users me-2"></i>
            Manage Members

        </a>

    </div>

    <i class="fa-solid fa-dumbbell banner-icon"></i>

</div>


<!-- STATISTICS -->

<div class="row g-3 mb-4">

    <!-- MEMBERS -->

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon icon-blue">
                <i class="fa-solid fa-users"></i>
            </div>

            <div>
                <h3><?= number_format($totalMembers) ?></h3>
                <small>Total Members</small>
            </div>

        </div>

    </div>


    <!-- TRAINERS -->

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon icon-green">
                <i class="fa-solid fa-user-tie"></i>
            </div>

            <div>
                <h3><?= number_format($totalTrainers) ?></h3>
                <small>Total Trainers</small>
            </div>

        </div>

    </div>


    <!-- SUBSCRIPTIONS -->

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon icon-orange">
                <i class="fa-solid fa-id-card"></i>
            </div>

            <div>
                <h3><?= number_format($activeSubs) ?></h3>
                <small>Active Subscriptions</small>
            </div>

        </div>

    </div>


    <!-- REVENUE -->

    <div class="col-12 col-sm-6 col-xl-3">

        <div class="stat-card">

            <div class="stat-icon icon-purple">
                <i class="fa-solid fa-wallet"></i>
            </div>

            <div>
                <h3 style="font-size:21px;">
                    <?= number_format($revenue, 2) ?>
                </h3>

                <small>Subscription Value</small>
            </div>

        </div>

    </div>

</div>


<!-- MAIN DASHBOARD CONTENT -->

<div class="row g-4">

    <!-- RECENT SUBSCRIPTIONS TABLE -->

    <div class="col-12 col-xl-8">

        <div class="content-card">

            <div class="d-flex justify-content-between
                        align-items-center mb-4">

                <div>

                    <h5 class="mb-1">
                        <i class="fa-solid fa-clock-rotate-left
                                  text-primary me-2"></i>

                        Recent Subscriptions
                    </h5>

                    <small class="text-muted">
                        Latest registered gym subscriptions
                    </small>

                </div>

                <a href="/fitness-system/subscriptions/index.php"
                   class="btn-gim">

                    View All
                    <i class="fa-solid fa-arrow-right ms-1"></i>

                </a>

            </div>


            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Member</th>
                            <th>Plan</th>
                            <th>Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if ($recentSubs): ?>

                        <?php foreach ($recentSubs as $s): ?>

                            <?php

                            $status = strtolower($s['status'] ?? '');

                            $statusClass = match ($status) {
                                'active' => 'status-active',
                                'expired' => 'status-expired',
                                'pending' => 'status-pending',
                                default => 'bg-light text-dark'
                            };

                            ?>

                            <tr>

                                <td>
                                    #<?= (int)$s['subscription_id'] ?>
                                </td>

                                <td class="fw-bold">
                                    <?= e($s['full_name']) ?>
                                </td>

                                <td>
                                    <?= e($s['plan_name']) ?>
                                </td>

                                <td>
                                    <?= e($s['start_date']) ?>
                                    <span class="text-muted">→</span>
                                    <?= e($s['end_date']) ?>
                                </td>

                                <td class="fw-bold">
                                    <?= number_format((float)$s['amount'], 2) ?>
                                </td>

                                <td>

                                    <span class="status-badge <?= $statusClass ?>">

                                        <?= e(ucfirst($status)) ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6"
                                class="text-center text-muted py-5">

                                <i class="fa-solid fa-folder-open
                                          fs-2 d-block mb-3"></i>

                                No subscriptions found yet.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- QUICK ACTIONS -->

    <div class="col-12 col-xl-4">

        <div class="promo-card">

            <i class="fa-solid fa-dumbbell"></i>

            <hr class="border-light opacity-25">

            <h6 class="fw-bold mb-3">
                Quick Actions
            </h6>

            <div class="d-grid gap-2">

                <a href="/fitness-system/members/index.php"
                   class="btn btn-light text-primary fw-bold">

                    <i class="fa-solid fa-user-plus me-2"></i>
                    Manage Members

                </a>

                <a href="/fitness-system/trainers/index.php"
                   class="btn btn-outline-light">

                    <i class="fa-solid fa-user-tie me-2"></i>
                    Manage Trainers

                </a>

                <a href="/fitness-system/subscriptions/create.php"
                   class="btn btn-outline-light">

                    <i class="fa-solid fa-plus me-2"></i>
                    New Subscription

                </a>

            </div>

        </div>

    </div>

</div>
