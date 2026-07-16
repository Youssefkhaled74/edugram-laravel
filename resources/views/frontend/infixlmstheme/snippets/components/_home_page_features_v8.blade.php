<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edugram V8 – Features Section</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================================
           V8 FEATURES – Premium Design System
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
           V8 SECTION (shared with other sections)
           ============================================================ */
        .v8-features {
            padding: 90px 0;
        }

        .v8-section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .v8-section-title {
            font-size: 46px;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 16px;
            line-height: 1.2;
        }

        .v8-section-subtitle {
            font-size: 18px;
            color: var(--muted);
            max-width: 760px;
            margin: 0 auto;
            line-height: 2;
        }

        /* ============================================================
           V8 FEATURES GRID
           Matches original .features-grid styling
           ============================================================ */
        .v8-features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .v8-feature-card {
            background: #ffffff;
            border-radius: 32px;
            padding: 36px 28px 32px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .v8-feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(17, 35, 68, 0.14);
        }

        /* ---------- Feature Icon ---------- */
        .v8-feature-icon {
            width: 74px;
            height: 74px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 28px;
            flex-shrink: 0;
        }

        .v8-feature-icon-video {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        .v8-feature-icon-quiz {
            background: linear-gradient(135deg, #8a63d2, #5e47b8);
        }
        .v8-feature-icon-ai {
            background: linear-gradient(135deg, #19a974, #0e7c56);
        }
        .v8-feature-icon-store {
            background: linear-gradient(135deg, #f59f00, #d97706);
        }

        /* ---------- Feature Content ---------- */
        .v8-feature-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        .v8-feature-desc {
            font-size: 16px;
            color: var(--muted);
            line-height: 1.9;
            margin: 0;
        }

        /* ============================================================
           RESPONSIVE
           Matches original breakpoints exactly
           ============================================================ */

        /* Tablet & small laptop – 1100px */
        @media (max-width: 1100px) {
            .v8-features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .v8-section-title {
                font-size: 36px;
            }
        }

        /* Mobile – 760px */
        @media (max-width: 760px) {
            .v8-features {
                padding: 60px 0;
            }

            .v8-section-title {
                font-size: 28px;
            }

            .v8-section-subtitle {
                font-size: 16px;
                padding: 0 10px;
            }

            .v8-features-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .v8-feature-card {
                padding: 28px 20px 24px;
                border-radius: 24px;
                align-items: center;
                text-align: center;
            }

            .v8-feature-icon {
                width: 64px;
                height: 64px;
                font-size: 24px;
                border-radius: 20px;
            }

            .v8-feature-title {
                font-size: 20px;
            }

            .v8-feature-desc {
                font-size: 15px;
            }
        }

        /* Extra small – optional */
        @media (max-width: 480px) {
            .v8-feature-card {
                padding: 22px 16px 20px;
                border-radius: 20px;
            }

            .v8-feature-icon {
                width: 56px;
                height: 56px;
                font-size: 20px;
                border-radius: 16px;
            }

            .v8-feature-title {
                font-size: 18px;
            }

            .v8-feature-desc {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <!-- ============================================================
    V8 FEATURES – Component
    Fully styled to match the Edugram Premium Theme reference
    ============================================================ -->

    <div class="v8-features">
        <div class="container">
            <div class="v8-section-header">
                <h2 class="v8-section-title">كل أدوات التعلم الذكي في مكان واحد</h2>
                <p class="v8-section-subtitle">أدوات متكاملة صُممت لتجعل رحلة التعلم أسهل وأكثر فاعلية</p>
            </div>

            <div class="v8-features-grid">

                <!-- Feature 1 -->
                <div class="v8-feature-card">
                    <div class="v8-feature-icon v8-feature-icon-video">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <h4 class="v8-feature-title">فيديوهات احترافية</h4>
                    <p class="v8-feature-desc">محتوى مرئي عالي الجودة بتصاميم احترافية وشرح مبسط من أفضل المعلمين</p>
                </div>

                <!-- Feature 2 -->
                <div class="v8-feature-card">
                    <div class="v8-feature-icon v8-feature-icon-quiz">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h4 class="v8-feature-title">بنك الأسئلة</h4>
                    <p class="v8-feature-desc">آلاف الأسئلة التفاعلية مع تحليل فوري لأدائك وتحديد نقاط القوة والضعف</p>
                </div>

                <!-- Feature 3 -->
                <div class="v8-feature-card">
                    <div class="v8-feature-icon v8-feature-icon-ai">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h4 class="v8-feature-title">المساعد إيدو</h4>
                    <p class="v8-feature-desc">مساعد ذكاء اصطناعي متاح على مدار الساعة لمساعدتك في فهم الدروس والإجابة على أسئلتك</p>
                </div>

                <!-- Feature 4 -->
                <div class="v8-feature-card">
                    <div class="v8-feature-icon v8-feature-icon-store">
                        <i class="fas fa-store"></i>
                    </div>
                    <h4 class="v8-feature-title">المتجر والمحفظة</h4>
                    <p class="v8-feature-desc">اشترِ الكورسات واحصل على نقاط ومكافآت مع محفظة رقمية سهلة الاستخدام</p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>