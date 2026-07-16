<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edugram V8 – Teachers Section</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================================
           V8 TEACHERS – Premium Design System
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
           V8 SECTION HEADER (shared with other sections)
           ============================================================ */
        .v8-section-header {
            text-align: center;
            margin-bottom: 48px;
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
           V8 TEACHERS GRID
           Matches original .teachers-grid styling
           ============================================================ */
        .v8-teachers {
            padding: 90px 0;
        }

        .v8-teachers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .v8-teacher-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 32px;
            padding: 28px 20px 24px;
            box-shadow: var(--shadow);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .v8-teacher-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(17, 35, 68, 0.14);
        }

        /* ---------- Avatar ---------- */
        .v8-teacher-img {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--secondary);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(45, 103, 216, 0.08);
        }

        .v8-teacher-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .v8-teacher-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            font-size: 2.5rem;
            background: rgba(45, 103, 216, 0.06);
        }

        /* ---------- Info ---------- */
        .v8-teacher-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            width: 100%;
        }

        .v8-teacher-name {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }

        .v8-teacher-bio {
            font-size: 14px;
            color: var(--muted);
            margin: 0 0 6px;
        }

        /* ---------- Subject pill ---------- */
        .v8-teacher-subject-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(45, 103, 216, 0.1);
            color: var(--secondary);
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            margin-top: 4px;
        }

        .v8-teacher-subject-pill i {
            font-size: 0.9rem;
        }

        /* ============================================================
           RESPONSIVE
           Matches original breakpoints exactly
           ============================================================ */

        /* Tablet & small laptop – 1100px */
        @media (max-width: 1100px) {
            .v8-teachers-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        /* Mobile – 760px */
        @media (max-width: 760px) {
            .v8-teachers {
                padding: 60px 0;
            }

            .v8-section-title {
                font-size: 32px;
            }

            .v8-section-subtitle {
                font-size: 16px;
                padding: 0 10px;
            }

            .v8-teachers-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .v8-teacher-card {
                padding: 24px 16px 20px;
                border-radius: 24px;
            }

            .v8-teacher-img {
                width: 72px;
                height: 72px;
            }

            .v8-teacher-name {
                font-size: 18px;
            }

            .v8-teacher-bio {
                font-size: 13px;
            }

            .v8-teacher-subject-pill {
                font-size: 13px;
                padding: 6px 14px;
            }
        }

        /* Extra small – optional */
        @media (max-width: 480px) {
            .v8-teacher-card {
                padding: 20px 12px 16px;
                border-radius: 20px;
            }

            .v8-teacher-img {
                width: 64px;
                height: 64px;
            }

            .v8-teacher-name {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- ============================================================
    V8 TEACHERS – Component
    Fully styled to match the Edugram Premium Theme reference
    ============================================================ -->

    <div class="v8-teachers">
        <div class="container">
            <div class="v8-section-header">
                <h2 class="v8-section-title">الكادر التعليمي لمنصة EduGram</h2>
                <p class="v8-section-subtitle">نخبة من أفضل المعلمين المتخصصين في جميع المراحل الدراسية</p>
            </div>

            <div class="v8-teachers-grid">

                <!-- Teacher 1 -->
                <div class="v8-teacher-card">
                    <div class="v8-teacher-img">
                        <img src="https://ui-avatars.com/api/?name=محمد+العربي&background=2D67D8&color=fff&size=128" alt="أ. محمد العربي">
                    </div>
                    <div class="v8-teacher-info">
                        <h4 class="v8-teacher-name">أ. محمد العربي</h4>
                        <p class="v8-teacher-bio">خبير مادة الأحياء</p>
                        <span class="v8-teacher-subject-pill">
                            <i class="fas fa-book-reader"></i>
                            Biology
                        </span>
                    </div>
                </div>

                <!-- Teacher 2 -->
                <div class="v8-teacher-card">
                    <div class="v8-teacher-img">
                        <img src="https://ui-avatars.com/api/?name=ابراهيم+رزق&background=173B78&color=fff&size=128" alt="أ. ابراهيم رزق">
                    </div>
                    <div class="v8-teacher-info">
                        <h4 class="v8-teacher-name">أ. إبراهيم رزق</h4>
                        <p class="v8-teacher-bio">خبير مادة التاريخ</p>
                        <span class="v8-teacher-subject-pill">
                            <i class="fas fa-book-reader"></i>
                            History
                        </span>
                    </div>
                </div>

                <!-- Teacher 3 -->
                <div class="v8-teacher-card">
                    <div class="v8-teacher-img">
                        <img src="https://ui-avatars.com/api/?name=كريم+صابر&background=C12B4A&color=fff&size=128" alt="أ. كريم صابر">
                    </div>
                    <div class="v8-teacher-info">
                        <h4 class="v8-teacher-name">أ. كريم صابر</h4>
                        <p class="v8-teacher-bio">خبير التخصصات العلمية</p>
                        <span class="v8-teacher-subject-pill">
                            <i class="fas fa-book-reader"></i>
                            Science
                        </span>
                    </div>
                </div>

                <!-- Teacher 4 -->
                <div class="v8-teacher-card">
                    <div class="v8-teacher-img">
                        <img src="https://ui-avatars.com/api/?name=خالد+اللقاني&background=F5B942&color=fff&size=128" alt="أ. خالد اللقاني">
                    </div>
                    <div class="v8-teacher-info">
                        <h4 class="v8-teacher-name">أ. خالد اللقاني</h4>
                        <p class="v8-teacher-bio">خبير المناهج الدراسية</p>
                        <span class="v8-teacher-subject-pill">
                            <i class="fas fa-book-reader"></i>
                            Education
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>