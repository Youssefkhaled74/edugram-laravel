<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edugram V8 – Homepage Banner</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================================
           V8 BANNER – Premium Design System
           Based on the Edugram Premium Theme reference
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
            padding: 20px;
        }

        .container {
            width: min(1280px, 92%);
            margin: 0 auto;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }

        /* ---------- Buttons ---------- */
        .v8-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 32px;
            font-family: "Cairo", sans-serif;
            font-size: 1rem;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            text-decoration: none;
        }

        .v8-btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }
        .v8-btn:active {
            transform: translateY(0);
        }

        .v8-btn-primary {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: #fff;
            box-shadow: 0 8px 24px rgba(23, 59, 120, 0.3);
        }
        .v8-btn-primary:hover {
            box-shadow: 0 12px 32px rgba(23, 59, 120, 0.45);
            color: #fff;
        }

        .v8-btn-light {
            background: var(--card);
            color: var(--text);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 16px rgba(17, 35, 68, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .v8-btn-light:hover {
            box-shadow: 0 8px 24px rgba(17, 35, 68, 0.12);
            color: var(--text);
        }

        /* ============================================================
           V8 BANNER
           ============================================================ */
        .v8-banner {
            position: relative;
            padding: 60px 0 40px;
            isolation: isolate;
            overflow: hidden;
            border-radius: 0;
        }

        /* Background shape (decorative) */
        .v8-banner-bg-shape {
            position: absolute;
            inset: 50px 40px 40px 40px;
            border-radius: 44px;
            background:
                radial-gradient(circle at 25% 20%, rgba(45, 103, 216, 0.18), transparent 34%),
                radial-gradient(circle at 78% 70%, rgba(193, 43, 74, 0.12), transparent 30%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.62), rgba(255, 255, 255, 0.16));
            filter: blur(1px);
            z-index: 0;
            pointer-events: none;
        }

        .v8-banner-content {
            position: relative;
            z-index: 2;
        }

        /* ---------- Badge ---------- */
        .v8-banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            margin-bottom: 20px;
            border-radius: 999px;
            background: rgba(45, 103, 216, 0.08);
            border: 1px solid rgba(45, 103, 216, 0.14);
            color: var(--secondary);
            font-size: 0.95rem;
            font-weight: 700;
        }
        .v8-banner-badge i {
            color: var(--gold);
        }

        /* ---------- Title ---------- */
        .v8-banner-title {
            margin: 0 0 18px;
            font-size: clamp(2.4rem, 5.5vw, 4.2rem);
            line-height: 1.2;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        /* ---------- Description ---------- */
        .v8-banner-desc {
            max-width: 600px;
            margin: 0 0 28px;
            color: var(--muted);
            font-size: clamp(1rem, 1.3vw, 1.2rem);
            line-height: 2;
        }

        /* ---------- Buttons row ---------- */
        .v8-banner-btns {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 28px;
        }

        /* ---------- Pills ---------- */
        .v8-banner-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .v8-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: var(--shadow);
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .v8-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(17, 35, 68, 0.1);
        }
        .v8-pill i {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        /* ============================================================
           HERO VISUAL (right side)
           ============================================================ */
        .v8-banner-visual {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 480px;
            z-index: 2;
        }

        /* ---------- Main card ---------- */
        .v8-banner-main-card {
            position: relative;
            width: 340px;
            aspect-ratio: 0.72;
            border-radius: 36px;
            background: linear-gradient(180deg, #b4a1ff 0%, #7b6bf1 42%, #456fd8 100%);
            box-shadow: 0 24px 56px rgba(23, 59, 120, 0.22);
            overflow: hidden;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .v8-banner-main-card::before {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            top: 20px;
            left: 20px;
            pointer-events: none;
        }

        .v8-banner-main-card::after {
            content: '';
            position: absolute;
            right: -30px;
            bottom: -20px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            pointer-events: none;
        }

        .v8-banner-main-icon {
            position: relative;
            z-index: 2;
            color: rgba(255, 255, 255, 0.25);
            font-size: 5.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .v8-banner-main-glow {
            position: absolute;
            inset: auto 18% 10% 18%;
            height: 18%;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.65), transparent 70%);
            filter: blur(14px);
            z-index: 1;
        }

        /* ---------- Floating cards ---------- */
        .v8-banner-float {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.75);
            box-shadow: 0 14px 34px rgba(17, 35, 68, 0.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            z-index: 3;
            animation: v8-float 5s ease-in-out infinite;
            min-width: 150px;
        }

        @keyframes v8-float {
            0%,
            100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-14px);
            }
        }

        .v8-banner-float-ai {
            top: 10%;
            right: -10px;
            animation-delay: 0s;
        }

        .v8-banner-float-wallet {
            bottom: 18%;
            left: -10px;
            animation-delay: 1.5s;
        }

        .v8-banner-float-xp {
            top: 56%;
            right: -30px;
            animation-delay: 3s;
        }

        .v8-banner-float-icon {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(45, 103, 216, 0.12);
            color: var(--secondary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .v8-banner-float-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .v8-banner-float-label {
            font-size: 0.72rem;
            line-height: 1.2;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .v8-banner-float-value {
            color: var(--text);
            font-size: 1rem;
            line-height: 1.3;
            font-weight: 800;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */

        /* Tablet & small laptop */
        @media (max-width: 1100px) {
            .col-lg-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .v8-banner-content {
                text-align: center;
            }

            .v8-banner-desc {
                margin-left: auto;
                margin-right: auto;
            }

            .v8-banner-btns {
                justify-content: center;
            }

            .v8-banner-pills {
                justify-content: center;
            }

            .v8-banner-visual {
                min-height: 420px;
                margin-top: 30px;
            }

            .v8-banner-float-ai {
                top: 6%;
                right: 0;
            }
            .v8-banner-float-wallet {
                bottom: 12%;
                left: 0;
            }
            .v8-banner-float-xp {
                top: 50%;
                right: -10px;
            }

            .v8-banner-main-card {
                width: 300px;
            }
        }

        /* Mobile */
        @media (max-width: 760px) {
            .v8-banner {
                padding: 30px 0 20px;
            }

            .v8-banner-bg-shape {
                inset: 30px 20px 20px 20px;
                border-radius: 28px;
            }

            .v8-banner-title {
                font-size: clamp(1.8rem, 8vw, 2.6rem);
            }

            .v8-banner-desc {
                font-size: 0.95rem;
                line-height: 1.8;
            }

            .v8-banner-btns {
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }

            .v8-banner-btns .v8-btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }

            .v8-banner-pills {
                gap: 8px;
            }
            .v8-pill {
                font-size: 0.8rem;
                padding: 8px 14px;
            }

            .v8-banner-visual {
                min-height: 340px;
            }

            .v8-banner-main-card {
                width: 240px;
            }

            .v8-banner-main-icon {
                font-size: 3.8rem;
            }

            .v8-banner-float {
                padding: 10px 14px;
                min-width: 110px;
                gap: 10px;
                border-radius: 14px;
            }

            .v8-banner-float-icon {
                width: 34px;
                height: 34px;
                font-size: 0.85rem;
                border-radius: 10px;
            }

            .v8-banner-float-label {
                font-size: 0.6rem;
            }
            .v8-banner-float-value {
                font-size: 0.8rem;
            }

            .v8-banner-float-ai {
                top: 2%;
                right: 4px;
            }
            .v8-banner-float-wallet {
                bottom: 8%;
                left: 4px;
            }
            .v8-banner-float-xp {
                top: 44%;
                right: -6px;
            }
        }

        /* Extra small */
        @media (max-width: 480px) {
            .v8-banner-main-card {
                width: 190px;
            }
            .v8-banner-main-icon {
                font-size: 3rem;
            }
            .v8-banner-float {
                min-width: 90px;
                padding: 8px 12px;
                gap: 8px;
            }
            .v8-banner-float-icon {
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
            }
            .v8-banner-float-value {
                font-size: 0.7rem;
            }
            .v8-banner-float-label {
                font-size: 0.55rem;
            }
            .v8-banner-float-ai {
                top: 0;
                right: 0;
            }
            .v8-banner-float-wallet {
                bottom: 4%;
                left: 0;
            }
            .v8-banner-float-xp {
                top: 40%;
                right: -4px;
            }
        }

        /* ---------- Utility ---------- */
        .text-gold {
            color: var(--gold);
        }
        .text-primary {
            color: var(--secondary);
        }
        .text-muted {
            color: var(--muted);
        }
    </style>
</head>
<body>

    <!-- ============================================================
    V8 BANNER – Component
    Fully styled to match the Edugram Premium Theme reference
    ============================================================ -->

    <div class="v8-banner">
        <!-- Decorative background shape -->
        <div class="v8-banner-bg-shape"></div>

        <div class="container">
            <div class="row align-items-center">

                <!-- Left: Content -->
                <div class="col-lg-6">
                    <div class="v8-banner-content">

                        <!-- Badge -->
                        <div class="v8-banner-badge">
                            <i class="fas fa-star"></i>
                            <span>منصة تعليمية ذكية لكافة المراحل الدراسية</span>
                        </div>

                        <!-- Title -->
                        <h1 class="v8-banner-title">
                            تعلّم بذكاء. تفوّق بثقة. وحقق أفضل نتائجك.
                        </h1>

                        <!-- Description -->
                        <p class="v8-banner-desc">
                            منصة تعليمية متكاملة تضم أفضل المعلمين، فيديوهات احترافية، اختبارات ذكية، حصص مباشرة، ومساعد ذكاء اصطناعي يساعدك على تحقيق أعلى الدرجات.
                        </p>

                        <!-- Buttons -->
                        <div class="v8-banner-btns">
                            <a href="#" class="v8-btn v8-btn-primary">
                                <i class="fas fa-rocket"></i>
                                ابدأ التعلم الآن
                            </a>
                            <a href="#" class="v8-btn v8-btn-light">
                                <i class="fas fa-compass"></i>
                                استكشف المنصة
                            </a>
                        </div>

                        <!-- Pills -->
                        <div class="v8-banner-pills">
                            <span class="v8-pill">
                                <i class="fas fa-robot"></i>
                                المساعد إيدو
                            </span>
                            <span class="v8-pill">
                                <i class="fas fa-video"></i>
                                حصص Live
                            </span>
                            <span class="v8-pill">
                                <i class="fas fa-database"></i>
                                بنك الأسئلة
                            </span>
                        </div>

                    </div>
                </div>

                <!-- Right: Visual -->
                <div class="col-lg-6">
                    <div class="v8-banner-visual">

                        <!-- Main card -->
                        <div class="v8-banner-main-card">
                            <div class="v8-banner-main-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="v8-banner-main-glow"></div>
                        </div>

                        <!-- Floating card: AI -->
                        <div class="v8-banner-float v8-banner-float-ai">
                            <div class="v8-banner-float-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="v8-banner-float-text">
                                <span class="v8-banner-float-label">المساعد الذكي</span>
                                <span class="v8-banner-float-value">إيدو AI</span>
                            </div>
                        </div>

                        <!-- Floating card: Wallet -->
                        <div class="v8-banner-float v8-banner-float-wallet">
                            <div class="v8-banner-float-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="v8-banner-float-text">
                                <span class="v8-banner-float-label">المحفظة</span>
                                <span class="v8-banner-float-value">1,250.00 ج.م</span>
                            </div>
                        </div>

                        <!-- Floating card: XP -->
                        <div class="v8-banner-float v8-banner-float-xp">
                            <div class="v8-banner-float-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="v8-banner-float-text">
                                <span class="v8-banner-float-label">مستوى XP</span>
                                <span class="v8-banner-float-value">المستوى 12</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>