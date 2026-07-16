<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edugram V8 – Gamification Section</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================================
           V8 GAMIFICATION – Premium Design System
           Matches the Edugram Premium Theme reference exactly
           ============================================================ */

        :root {
            --primary: #173B78;
            --secondary: #2D67D8;
            --accent: #C12B4A;
            --gold: #F5B942;
            --bg: #F5F8FD;
            --card: rgba(255, 255, 255, 0.85);
            --text: #10233E;
            --muted: #66758D;
            --shadow: 0 10px 30px rgba(17, 35, 68, 0.08);
        }

        /* ---------- Reset & Base ---------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Cairo", sans-serif;
            background: linear-gradient(to bottom, #F7FAFF, #EEF4FF);
            color: var(--text);
            overflow-x: hidden;
            padding: 40px 20px;
        }

        .container {
            width: min(1280px, 92%);
            margin: 0 auto;
        }

        /* ============================================================
           V8 GAMIFICATION SECTION
           Matches original .gamification styling
           ============================================================ */
        .v8-gamification {
            padding: 40px 0;
        }

        .v8-gamification-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            background: linear-gradient(135deg, #173B78, #10254B);
            color: #fff;
            border-radius: 48px;
            padding: 70px 60px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background shape */
        .v8-gamification-wrap::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            top: -200px;
            left: -100px;
            pointer-events: none;
        }

        /* ---------- Content Side ---------- */
        .v8-gamification-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .v8-gamification-title {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            margin: 0;
            color: #fff;
        }

        .v8-gamification-desc {
            font-size: 18px;
            line-height: 2;
            color: rgba(255, 255, 255, 0.82);
            margin: 0 0 16px;
            max-width: 520px;
        }

        /* ---------- XP Ring ---------- */
        .v8-gamification-xp-ring {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-top: 10px;
        }

        .v8-xp-ring-circle {
            position: relative;
            width: 200px;
            height: 200px;
        }

        .v8-xp-ring-circle svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .v8-xp-ring-bg {
            fill: none;
            stroke: rgba(255, 255, 255, 0.1);
            stroke-width: 14;
        }

        .v8-xp-ring-fill {
            fill: none;
            stroke: var(--gold);
            stroke-width: 14;
            stroke-linecap: round;
            stroke-dasharray: 314;
            stroke-dashoffset: 94;
            transition: stroke-dashoffset 1s ease;
        }

        .v8-xp-ring-text {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            text-align: center;
        }

        .v8-xp-ring-percent {
            font-size: 40px;
            font-weight: 900;
            color: #fff;
            line-height: 1.1;
        }

        .v8-xp-ring-label {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
        }

        /* ---------- Leaderboard Side ---------- */
        .v8-gamification-leaderboard {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .v8-leaderboard-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .v8-leaderboard-header i {
            font-size: 28px;
            color: var(--gold);
        }

        .v8-leaderboard-header h3 {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            margin: 0;
        }

        .v8-leaderboard-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .v8-leaderboard-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 22px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .v8-leaderboard-item:hover {
            background: rgba(255, 255, 255, 0.14);
            transform: translateX(-4px);
        }

        /* Medal colors */
        .v8-leaderboard-gold {
            background: rgba(245, 185, 66, 0.18);
            border-color: rgba(245, 185, 66, 0.2);
        }
        .v8-leaderboard-gold:hover {
            background: rgba(245, 185, 66, 0.25);
        }

        .v8-leaderboard-silver {
            background: rgba(148, 163, 184, 0.15);
            border-color: rgba(148, 163, 184, 0.15);
        }
        .v8-leaderboard-silver:hover {
            background: rgba(148, 163, 184, 0.22);
        }

        .v8-leaderboard-bronze {
            background: rgba(180, 83, 9, 0.14);
            border-color: rgba(180, 83, 9, 0.15);
        }
        .v8-leaderboard-bronze:hover {
            background: rgba(180, 83, 9, 0.2);
        }

        .v8-leaderboard-rank {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .v8-leaderboard-gold .v8-leaderboard-rank {
            background: var(--gold);
            color: var(--text);
        }

        .v8-leaderboard-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
            flex-shrink: 0;
        }

        .v8-leaderboard-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 0;
        }

        .v8-leaderboard-name {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .v8-leaderboard-xp {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.3;
        }

        .v8-leaderboard-medal {
            font-size: 20px;
            color: var(--gold);
            flex-shrink: 0;
            opacity: 0.7;
        }

        .v8-leaderboard-gold .v8-leaderboard-medal {
            opacity: 1;
            color: var(--gold);
        }
        .v8-leaderboard-silver .v8-leaderboard-medal {
            opacity: 0.8;
            color: #94a3b8;
        }
        .v8-leaderboard-bronze .v8-leaderboard-medal {
            opacity: 0.8;
            color: #b45309;
        }

        /* ============================================================
           RESPONSIVE
           Matches original breakpoints exactly
           ============================================================ */

        /* Tablet & small laptop – 1100px */
        @media (max-width: 1100px) {
            .v8-gamification-wrap {
                grid-template-columns: 1fr;
                gap: 40px;
                padding: 50px 40px;
                border-radius: 36px;
            }

            .v8-gamification-title {
                font-size: 38px;
            }

            .v8-gamification-desc {
                max-width: 100%;
            }

            .v8-gamification-xp-ring {
                justify-content: center;
            }

            .v8-leaderboard-header h3 {
                font-size: 22px;
            }
        }

        /* Mobile – 760px */
        @media (max-width: 760px) {
            .v8-gamification {
                padding: 30px 0;
            }

            .v8-gamification-wrap {
                padding: 32px 20px;
                border-radius: 28px;
                gap: 32px;
            }

            .v8-gamification-title {
                font-size: 28px;
            }

            .v8-gamification-desc {
                font-size: 16px;
            }

            .v8-xp-ring-circle {
                width: 160px;
                height: 160px;
            }

            .v8-xp-ring-percent {
                font-size: 32px;
            }

            .v8-xp-ring-label {
                font-size: 12px;
            }

            .v8-leaderboard-header h3 {
                font-size: 18px;
            }
            .v8-leaderboard-header i {
                font-size: 22px;
            }

            .v8-leaderboard-item {
                padding: 12px 16px;
                border-radius: 18px;
                gap: 12px;
            }

            .v8-leaderboard-rank {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .v8-leaderboard-avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .v8-leaderboard-name {
                font-size: 15px;
            }

            .v8-leaderboard-xp {
                font-size: 12px;
            }

            .v8-leaderboard-medal {
                font-size: 16px;
            }
        }

        /* Extra small */
        @media (max-width: 480px) {
            .v8-gamification-wrap {
                padding: 24px 16px;
                border-radius: 22px;
                gap: 28px;
            }

            .v8-gamification-title {
                font-size: 24px;
            }

            .v8-gamification-desc {
                font-size: 14px;
                line-height: 1.8;
            }

            .v8-xp-ring-circle {
                width: 130px;
                height: 130px;
            }

            .v8-xp-ring-percent {
                font-size: 26px;
            }

            .v8-leaderboard-item {
                padding: 10px 12px;
                border-radius: 14px;
                gap: 10px;
            }

            .v8-leaderboard-rank {
                width: 26px;
                height: 26px;
                font-size: 12px;
            }

            .v8-leaderboard-avatar {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .v8-leaderboard-name {
                font-size: 14px;
            }

            .v8-leaderboard-xp {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>

    <!-- ============================================================
    V8 GAMIFICATION – Component
    Fully styled to match the Edugram Premium Theme reference
    ============================================================ -->

    <div class="v8-gamification">
        <div class="container">
            <div class="v8-gamification-wrap">

                <!-- Left: Content + XP Ring -->
                <div class="v8-gamification-content">
                    <h2 class="v8-gamification-title">نظام تنافسي علمي لتحفيز الطلاب</h2>
                    <p class="v8-gamification-desc">نظام نقاط ومستويات مبني على أسس علمية لتحفيز الطلاب على الاستمرار والتنافس الصحي. اجمع نقاط XP، وارتقِ بالمستويات، وتنافس مع زملائك.</p>

                    <div class="v8-gamification-xp-ring">
                        <div class="v8-xp-ring-circle">
                            <svg viewBox="0 0 120 120">
                                <circle class="v8-xp-ring-bg" cx="60" cy="60" r="50"></circle>
                                <circle class="v8-xp-ring-fill" cx="60" cy="60" r="50"
                                        stroke-dasharray="314"
                                        stroke-dashoffset="94"></circle>
                            </svg>
                            <div class="v8-xp-ring-text">
                                <span class="v8-xp-ring-percent">70%</span>
                                <span class="v8-xp-ring-label">نسبة الإنجاز</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Leaderboard -->
                <div class="v8-gamification-leaderboard">
                    <div class="v8-leaderboard-header">
                        <i class="fas fa-crown"></i>
                        <h3>أوائل المنصة هذا الأسبوع</h3>
                    </div>

                    <div class="v8-leaderboard-list">

                        <!-- Rank 1 – Gold -->
                        <div class="v8-leaderboard-item v8-leaderboard-gold">
                            <span class="v8-leaderboard-rank">1</span>
                            <div class="v8-leaderboard-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="v8-leaderboard-info">
                                <span class="v8-leaderboard-name">أحمد محمد</span>
                                <span class="v8-leaderboard-xp">2,450 XP</span>
                            </div>
                            <i class="fas fa-medal v8-leaderboard-medal"></i>
                        </div>

                        <!-- Rank 2 – Silver -->
                        <div class="v8-leaderboard-item v8-leaderboard-silver">
                            <span class="v8-leaderboard-rank">2</span>
                            <div class="v8-leaderboard-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="v8-leaderboard-info">
                                <span class="v8-leaderboard-name">فاطمة العلي</span>
                                <span class="v8-leaderboard-xp">2,180 XP</span>
                            </div>
                            <i class="fas fa-medal v8-leaderboard-medal"></i>
                        </div>

                        <!-- Rank 3 – Bronze -->
                        <div class="v8-leaderboard-item v8-leaderboard-bronze">
                            <span class="v8-leaderboard-rank">3</span>
                            <div class="v8-leaderboard-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="v8-leaderboard-info">
                                <span class="v8-leaderboard-name">خالد الشمري</span>
                                <span class="v8-leaderboard-xp">1,920 XP</span>
                            </div>
                            <i class="fas fa-medal v8-leaderboard-medal"></i>
                        </div>

                        <!-- Rank 4 – Default -->
                        <div class="v8-leaderboard-item">
                            <span class="v8-leaderboard-rank">4</span>
                            <div class="v8-leaderboard-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="v8-leaderboard-info">
                                <span class="v8-leaderboard-name">نورة الحربي</span>
                                <span class="v8-leaderboard-xp">1,750 XP</span>
                            </div>
                            <i class="fas fa-medal v8-leaderboard-medal"></i>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>