<section class="v8-intro-video-section">
    <div class="container">
        <div class="v8-intro-video-wrapper">

            <div class="v8-intro-video-content">
                <span class="v8-intro-video-badge">
                    <i class="fas fa-play"></i>
                    شاهد Edugram في دقيقة
                </span>

                <h2 class="v8-intro-video-title">
                    اعرف إزاي Edugram هيساعدك تتعلم بطريقة أسهل وأذكى
                </h2>

                <p class="v8-intro-video-desc">
                    فيديو سريع يوضح أهم مميزات المنصة، طريقة استخدام التطبيق، وكيف تقدر تبدأ رحلتك التعليمية مع أفضل المعلمين.
                </p>
            </div>

            <div class="v8-intro-video-card">
                <div class="v8-intro-video-frame">
                    <iframe
                        src="https://www.youtube.com/embed/ysz5S6PUM-U?rel=0&modestbranding=1"
                        title="Edugram Intro Video"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    .v8-intro-video-section {
        position: relative;
        padding: 70px 0;
        background: linear-gradient(180deg, #F7FAFF 0%, #EEF4FF 100%);
        font-family: "Cairo", sans-serif;
        overflow: hidden;
    }

    .v8-intro-video-wrapper {
        display: grid;
        grid-template-columns: 0.9fr 1.1fr;
        gap: 40px;
        align-items: center;
        padding: 38px;
        border-radius: 34px;
        background:
            radial-gradient(circle at 20% 20%, rgba(45, 103, 216, 0.12), transparent 34%),
            radial-gradient(circle at 85% 80%, rgba(193, 43, 74, 0.10), transparent 30%),
            rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 18px 45px rgba(17, 35, 68, 0.08);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .v8-intro-video-content {
        text-align: right;
    }

    .v8-intro-video-badge {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 10px 18px;
        margin-bottom: 18px;
        border-radius: 999px;
        background: rgba(45, 103, 216, 0.09);
        border: 1px solid rgba(45, 103, 216, 0.14);
        color: #2D67D8;
        font-size: 0.95rem;
        font-weight: 800;
    }

    .v8-intro-video-badge i {
        color: #C12B4A;
    }

    .v8-intro-video-title {
        margin: 0 0 16px;
        color: #10233E;
        font-size: clamp(1.8rem, 3vw, 3rem);
        line-height: 1.35;
        font-weight: 800;
    }

    .v8-intro-video-desc {
        margin: 0;
        max-width: 560px;
        color: #66758D;
        font-size: 1.05rem;
        line-height: 2;
        font-weight: 500;
    }

    .v8-intro-video-card {
        position: relative;
        padding: 14px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.88);
        box-shadow: 0 20px 55px rgba(23, 59, 120, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.75);
    }

    .v8-intro-video-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        border-radius: 22px;
        background: #10233E;
    }

    .v8-intro-video-frame iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    @media (max-width: 991px) {
        .v8-intro-video-wrapper {
            grid-template-columns: 1fr;
            padding: 28px;
        }

        .v8-intro-video-content {
            text-align: center;
        }

        .v8-intro-video-desc {
            margin-left: auto;
            margin-right: auto;
        }
    }

    @media (max-width: 575px) {
        .v8-intro-video-section {
            padding: 45px 0;
        }

        .v8-intro-video-wrapper {
            padding: 20px;
            border-radius: 24px;
            gap: 25px;
        }

        .v8-intro-video-card {
            padding: 8px;
            border-radius: 22px;
        }

        .v8-intro-video-frame {
            border-radius: 16px;
        }
    }
</style>