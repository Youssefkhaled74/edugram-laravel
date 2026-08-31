<style>
    .v8-announcements { padding: 34px 0 16px; background: #f7faff; direction: rtl; }
    .v8-announcements .container { width: min(1280px, calc(100% - 32px)); margin-inline: auto; }
    .v8-announcements-head { align-items: end; display: flex; justify-content: space-between; gap: 20px; margin-bottom: 22px; }
    .v8-announcements-kicker { color: #2d67d8; font-size: .9rem; font-weight: 800; margin: 0 0 6px; }
    .v8-announcements-title { color: #173b78; font-size: clamp(1.7rem, 3vw, 2.35rem); font-weight: 800; margin: 0; }
    .v8-announcements-link { color: #2d67d8; font-size: .95rem; font-weight: 800; text-decoration: none; white-space: nowrap; }
    .v8-announcements-list { display: grid; gap: 18px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .v8-announcement-card { align-items: center; background: #fff; border: 1px solid rgba(45,103,216,.1); border-radius: 24px; box-shadow: 0 10px 28px rgba(17,35,68,.07); color: inherit; display: flex; gap: 16px; min-height: 132px; padding: 22px; text-decoration: none; transition: transform .25s ease, box-shadow .25s ease; }
    .v8-announcement-card:hover { box-shadow: 0 16px 34px rgba(17,35,68,.13); color: inherit; transform: translateY(-4px); }
    .v8-announcement-icon { align-items: center; background: linear-gradient(135deg, #2d67d8, #173b78); border-radius: 18px; color: #fff; display: flex; flex: 0 0 auto; font-size: 1.35rem; height: 56px; justify-content: center; width: 56px; }
    .v8-announcement-card:nth-child(2) .v8-announcement-icon { background: linear-gradient(135deg, #19a974, #0e7c56); }
    .v8-announcement-card:nth-child(3) .v8-announcement-icon { background: linear-gradient(135deg, #8a63d2, #5e47b8); }
    .v8-announcement-content { min-width: 0; }
    .v8-announcement-badge { color: #2d67d8; display: block; font-size: .75rem; font-weight: 800; margin-bottom: 5px; }
    .v8-announcement-card h3 { color: #173b78; font-size: 1.02rem; font-weight: 800; line-height: 1.55; margin: 0 0 4px; }
    .v8-announcement-card p { color: #66758d; font-size: .86rem; line-height: 1.65; margin: 0; }
    @media (max-width: 900px) { .v8-announcements-list { grid-template-columns: 1fr; } }
    @media (max-width: 560px) { .v8-announcements-head { align-items: start; flex-direction: column; } .v8-announcement-card { padding: 18px; } }
</style>

<section class="v8-announcements" aria-labelledby="v8-announcements-title">
    <div class="container">
        <div class="v8-announcements-head">
            <div>
                <p class="v8-announcements-kicker">اكتشف المزيد</p>
                <h2 class="v8-announcements-title" id="v8-announcements-title">الجديد على منصتنا EduGram</h2>
            </div>
            <a class="v8-announcements-link" href="{{ url('/courses') }}">استعرض جميع الدورات ←</a>
        </div>

        <div class="v8-announcements-list">
            <a class="v8-announcement-card" href="{{ url('/courses') }}">
                <span class="v8-announcement-icon"><i class="fas fa-graduation-cap"></i></span>
                <span class="v8-announcement-content"><span class="v8-announcement-badge">دورات جديدة</span><h3>اكتشف أحدث الدورات التعليمية</h3><p>ابدأ رحلتك التعليمية مع محتوى جديد ومدرسين متخصصين.</p></span>
            </a>
            <a class="v8-announcement-card" href="{{ url('/classes') }}">
                <span class="v8-announcement-icon"><i class="fas fa-video"></i></span>
                <span class="v8-announcement-content"><span class="v8-announcement-badge">بث مباشر</span><h3>تابع الحصص والبثوث القادمة</h3><p>شارك في جلسات مباشرة وتفاعل مع مدرسيك.</p></span>
            </a>
            <a class="v8-announcement-card" href="{{ url('/register') }}">
                <span class="v8-announcement-icon"><i class="fas fa-gift"></i></span>
                <span class="v8-announcement-content"><span class="v8-announcement-badge">عروض المنصة</span><h3>انضم إلى مجتمع EduGram</h3><p>أنشئ حسابك الآن واستمتع بتجربة تعلم مميزة.</p></span>
            </a>
        </div>
    </div>
</section>
