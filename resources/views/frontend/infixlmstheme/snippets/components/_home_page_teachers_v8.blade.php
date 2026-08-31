<style>
    .v8-teachers {
        --v8-primary: #173B78;
        --v8-secondary: #2D67D8;
        --v8-muted: #66758D;
        --v8-shadow: 0 10px 30px rgba(17, 35, 68, .08);
        padding: 90px 0;
        background: linear-gradient(to bottom, #F7FAFF, #EEF4FF);
        direction: rtl;
        overflow: hidden;
    }

    .v8-teachers .container { width: min(1280px, 92%); margin: 0 auto; }
    .v8-section-header { text-align: center; margin-bottom: 40px; }
    .v8-section-title { color: var(--v8-primary); font-size: 46px; font-weight: 800; line-height: 1.2; margin: 0 0 16px; }
    .v8-section-subtitle { color: var(--v8-muted); font-size: 18px; line-height: 2; margin: 0 auto; }

    .v8-teachers-slider { position: relative; padding: 0 54px; }
    .v8-teachers-viewport { overflow: hidden; padding: 10px 2px 26px; }
    .v8-teachers-track {
        display: flex;
        gap: 24px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        scrollbar-width: none;
        -ms-overflow-style: none;
        padding: 0 2px;
    }
    .v8-teachers-track::-webkit-scrollbar { display: none; }

    .v8-teacher-card {
        flex: 0 0 calc((100% - 72px) / 4);
        min-width: 0;
        scroll-snap-align: start;
        background: rgba(255, 255, 255, .9);
        border: 1px solid rgba(255, 255, 255, .7);
        border-radius: 32px;
        box-shadow: var(--v8-shadow);
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 28px 20px 24px;
        text-align: center;
        text-decoration: none;
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .v8-teacher-card:hover, .v8-teacher-card:focus-visible { transform: translateY(-8px); box-shadow: 0 16px 40px rgba(17, 35, 68, .15); color: inherit; }
    .v8-teacher-card:focus-visible, .v8-slider-arrow:focus-visible { outline: 3px solid rgba(45, 103, 216, .38); outline-offset: 3px; }
    .v8-teacher-img { width: 92px; height: 92px; border: 3px solid var(--v8-secondary); border-radius: 50%; overflow: hidden; background: #edf3ff; flex: 0 0 auto; }
    .v8-teacher-img img { width: 100%; height: 100%; object-fit: cover; }
    .v8-teacher-name { color: var(--v8-primary); font-size: 20px; font-weight: 800; line-height: 1.55; margin: 0; }
    .v8-teacher-bio { color: var(--v8-muted); font-size: 14px; line-height: 1.7; margin: 0; min-height: 24px; }
    .v8-teacher-subject-pill { background: rgba(45, 103, 216, .1); border-radius: 999px; color: var(--v8-secondary); font-size: 14px; font-weight: 700; margin-top: 4px; padding: 8px 16px; }

    .v8-slider-arrow {
        align-items: center;
        background: #fff;
        border: 1px solid rgba(45, 103, 216, .18);
        border-radius: 50%;
        box-shadow: 0 6px 18px rgba(17, 35, 68, .13);
        color: var(--v8-primary);
        cursor: pointer;
        display: flex;
        font-size: 28px;
        height: 42px;
        justify-content: center;
        line-height: 1;
        padding: 0 0 4px;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        transition: background .2s ease, color .2s ease, transform .2s ease;
        width: 42px;
        z-index: 1;
    }
    .v8-slider-arrow:hover { background: var(--v8-secondary); color: #fff; transform: translateY(-50%) scale(1.06); }
    .v8-slider-arrow--next { right: 0; }
    .v8-slider-arrow--prev { left: 0; }
    .v8-teachers-empty { color: var(--v8-muted); text-align: center; }

    @media (max-width: 1100px) { .v8-teacher-card { flex-basis: calc((100% - 24px) / 2); } }
    @media (max-width: 760px) {
        .v8-teachers { padding: 60px 0; }
        .v8-section-title { font-size: 32px; }
        .v8-section-subtitle { font-size: 16px; }
        .v8-teachers-slider { padding: 0 42px; }
        .v8-teachers-track { gap: 16px; }
        .v8-teacher-card { flex-basis: 100%; border-radius: 24px; }
        .v8-slider-arrow { height: 36px; width: 36px; font-size: 23px; }
    }
    @media (prefers-reduced-motion: reduce) { .v8-teachers-track { scroll-behavior: auto; } .v8-teacher-card, .v8-slider-arrow { transition: none; } }
</style>

<section class="v8-teachers" aria-labelledby="v8-teachers-title">
    <div class="container">
        <div class="v8-section-header">
            <h2 class="v8-section-title" id="v8-teachers-title">الكادر التعليمي لمنصة EduGram</h2>
            <p class="v8-section-subtitle">نخبة من أفضل المعلمين المتخصصين في جميع المراحل الدراسية</p>
        </div>

        @if($teachers->isNotEmpty())
            <div class="v8-teachers-slider" data-teachers-slider>
                <button class="v8-slider-arrow v8-slider-arrow--next" type="button" data-slider-next aria-label="المدرس التالي">&#8249;</button>
                <div class="v8-teachers-viewport">
                    <div class="v8-teachers-track" data-slider-track tabindex="0" aria-label="قائمة المدرسين">
                        @foreach($teachers as $teacher)
                            <a class="v8-teacher-card" href="{{ route('instructorDetails', [$teacher->id, \Illuminate\Support\Str::slug($teacher->name, '-')]) }}">
                                <div class="v8-teacher-img">
                                    <img src="{{ getProfileImage($teacher->image, $teacher->name) }}" alt="صورة {{ $teacher->name }}" loading="lazy">
                                </div>
                                <h3 class="v8-teacher-name">{{ $teacher->name }}</h3>
                                <p class="v8-teacher-bio">{{ $teacher->headline ?: 'معلم متخصص' }}</p>
                                <span class="v8-teacher-subject-pill">عرض الملف الشخصي</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <button class="v8-slider-arrow v8-slider-arrow--prev" type="button" data-slider-prev aria-label="المدرس السابق">&#8250;</button>
            </div>
        @else
            <p class="v8-teachers-empty">سيتم إضافة المدرسين قريبًا.</p>
        @endif
    </div>
</section>

<script>
    document.querySelectorAll('[data-teachers-slider]').forEach(function (slider) {
        var track = slider.querySelector('[data-slider-track]');
        var cards = Array.prototype.slice.call(track.children);
        var index = 0;
        var paused = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function showCard(nextIndex) {
            index = (nextIndex + cards.length) % cards.length;
            cards[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        slider.querySelector('[data-slider-next]').addEventListener('click', function () { showCard(index + 1); });
        slider.querySelector('[data-slider-prev]').addEventListener('click', function () { showCard(index - 1); });
        slider.addEventListener('mouseenter', function () { paused = true; });
        slider.addEventListener('mouseleave', function () { paused = false; });
        slider.addEventListener('focusin', function () { paused = true; });
        slider.addEventListener('focusout', function () { paused = false; });

        if (cards.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            window.setInterval(function () { if (!paused) showCard(index + 1); }, 4500);
        }
    });
</script>
