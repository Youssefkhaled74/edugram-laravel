<section class="v8-stats">
    <div class="container">
        <div class="v8-stats-grid">

            <div class="v8-stat-card">
                <div class="v8-stat-icon v8-stat-icon-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="v8-stat-info">
                    <span class="v8-stat-number">120K+</span>
                    <span class="v8-stat-label">طالب نشط</span>
                </div>
            </div>

            <div class="v8-stat-card">
                <div class="v8-stat-icon v8-stat-icon-green">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="v8-stat-info">
                    <span class="v8-stat-number">11</span>
                    <span class="v8-stat-label">معلم محترف</span>
                </div>
            </div>

            <div class="v8-stat-card">
                <div class="v8-stat-icon v8-stat-icon-purple">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="v8-stat-info">
                    <span class="v8-stat-number">950+</span>
                    <span class="v8-stat-label">كورس تعليمي</span>
                </div>
            </div>

            <div class="v8-stat-card">
                <div class="v8-stat-icon v8-stat-icon-orange">
                    <i class="fas fa-laptop"></i>
                </div>
                <div class="v8-stat-info">
                    <span class="v8-stat-number">18K+</span>
                    <span class="v8-stat-label">اختبار تفاعلي</span>
                </div>
            </div>

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
</section>

<style>
    .v8-stats {
        position: relative;
        z-index: 5;
        padding: 40px 0 70px;
        background: linear-gradient(180deg, #EEF4FF 0%, #F7FAFF 100%);
        font-family: "Cairo", sans-serif;
    }

    .v8-stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 22px;
        align-items: stretch;
    }

    .v8-stat-card {
        background: #ffffff;
        border-radius: 28px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(17, 35, 68, 0.08);
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

    .v8-stat-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .v8-stat-number {
        font-size: 32px;
        font-weight: 800;
        color: #2D67D8;
        line-height: 1.15;
        display: block;
    }

    .v8-stat-label {
        font-size: 15px;
        color: #66758D;
        font-weight: 600;
        display: block;
        line-height: 1.6;
    }

    @media (max-width: 1100px) {
        .v8-stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 760px) {
        .v8-stats {
            padding: 30px 0 50px;
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
            font-size: 26px;
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

    @media (max-width: 480px) {
        .v8-stats-grid {
            grid-template-columns: repeat(2, 1fr);
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