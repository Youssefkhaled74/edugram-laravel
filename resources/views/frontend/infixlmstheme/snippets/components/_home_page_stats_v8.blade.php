<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edugram V8 – Stats Section</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================================
           V8 STATS – Premium Design System
           Matches the Edugram Premium Theme reference
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
           V8 STATS
           Matches original .stats styling exactly
           ============================================================ */

        .v8-stats {
            padding: 40px 0;
        }

        .v8-stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 22px;
        }

        .v8-stat-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .v8-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(17, 35, 68, 0.14);
        }

        /* ---------- Icon ---------- */
        .v8-stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            flex-shrink: 0;
            margin: 0 auto;
        }

        /* Icon color variants – using the same palette as original */
        .v8-stat-icon-blue {
            background: linear-gradient(135deg, #2d67d8, #173b78);
        }
        .v8-stat-icon-green {
            background: linear-gradient(135deg, #19a974, #0e7c56);
        }
        .v8-stat-icon-purple {
            background: linear-gradient(135deg, #8a63d2, #5e47b8);
        }
        .v8-stat-icon-orange {
            background: linear-gradient(135deg, #f59f00, #d97706);
        }
        .v8-stat-icon-red {
            background: linear-gradient(135deg, #e74c3c, #c12b4a);
        }

        /* ---------- Info ---------- */
        .v8-stat-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        /* Number – matches original h3 style */
        .v8-stat-number {
            font-size: 38px;
            font-weight: 800;
            color: var(--secondary);
            line-height: 1.15;
            display: block;
        }

        /* Label – matches original p style */
        .v8-stat-label {
            font-size: 16px;
            color: var(--muted);
            font-weight: 600;
            display: block;
        }

        /* ============================================================
           RESPONSIVE
           Matches original breakpoints exactly
           ============================================================ */

        /* Tablet & small laptop – match original 1100px breakpoint */
        @media (max-width: 1100px) {
            .v8-stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 18px;
            }
        }

        /* Mobile – match original 760px breakpoint */
        @media (max-width: 760px) {
            .v8-stats {
                padding: 30px 0;
            }

            .v8-stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 14px;
            }

            .v8-stat-card {
                padding: 22px 14px;
                border-radius: 22px;
            }

            .v8-stat-number {
                font-size: 28px;
            }

            .v8-stat-label {
                font-size: 14px;
            }

            .v8-stat-icon {
                width: 48px;
                height: 48px;
                font-size: 1.1rem;
                border-radius: 14px;
            }
        }

        /* Extra small – single column */
        @media (max-width: 480px) {
            .v8-stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .v8-stat-card {
                padding: 18px 12px;
                border-radius: 18px;
            }

            .v8-stat-number {
                font-size: 22px;
            }

            .v8-stat-label {
                font-size: 12px;
            }

            .v8-stat-icon {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
                border-radius: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- ============================================================
    V8 STATS – Component
    Fully styled to match the Edugram Premium Theme reference
    ============================================================ -->

    <div class="v8-stats">
        <div class="container">
            <div class="v8-stats-grid">

                <!-- Stat 1 -->
                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">120K+</span>
                        <span class="v8-stat-label">طالب نشط</span>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-green">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">11</span>
                        <span class="v8-stat-label">معلم محترف</span>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-purple">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">950+</span>
                        <span class="v8-stat-label">كورس تعليمي</span>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-orange">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">18K+</span>
                        <span class="v8-stat-label">اختبار تفاعلي</span>
                    </div>
                </div>

                <!-- Stat 5 -->
                <div class="v8-stat-card">
                    <div class="v8-stat-icon v8-stat-icon-red">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="v8-stat-info">
                        <span class="v8-stat-number">5K+</span>
                        <span class="v8-stat-label">طالب مسجل</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>