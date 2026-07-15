<div data-type="component-text"
     data-preview="{{!function_exists('themeAsset')?'':themeAsset('img/snippets/preview/home/homepage_banner.jpg')}}"
     data-aoraeditor-title="Homepage V8 Banner" data-aoraeditor-categories="Home Page;Banner">

    <div class="v8-banner">
        <div class="v8-banner-bg-shape"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="v8-banner-content">
                        <div class="v8-banner-badge">
                            <i class="fas fa-star"></i>
                            <span>منصة تعليمية ذكية لكافة المراحل الدراسية</span>
                        </div>

                        <h1 class="v8-banner-title">تعلّم بذكاء. تفوّق بثقة. وحقق أفضل نتائجك.</h1>

                        <p class="v8-banner-desc">منصة تعليمية متكاملة تضم أفضل المعلمين، فيديوهات احترافية، اختبارات ذكية، حصص مباشرة، ومساعد ذكاء اصطناعي يساعدك على تحقيق أعلى الدرجات.</p>

                        <div class="v8-banner-btns">
                            <a href="{{route('register')}}" class="v8-btn v8-btn-primary">
                                <i class="fas fa-rocket"></i>
                                ابدأ التعلم الآن
                            </a>
                            <a href="{{route('frontendHomePage')}}#courses" class="v8-btn v8-btn-light">
                                <i class="fas fa-compass"></i>
                                استكشف المنصة
                            </a>
                        </div>

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

                <div class="col-lg-6">
                    <div class="v8-banner-visual">
                        <div class="v8-banner-main-card">
                            <div class="v8-banner-main-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="v8-banner-main-glow"></div>
                        </div>

                        <div class="v8-banner-float v8-banner-float-ai">
                            <div class="v8-banner-float-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="v8-banner-float-text">
                                <span class="v8-banner-float-label">المساعد الذكي</span>
                                <span class="v8-banner-float-value">إيدو AI</span>
                            </div>
                        </div>

                        <div class="v8-banner-float v8-banner-float-wallet">
                            <div class="v8-banner-float-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="v8-banner-float-text">
                                <span class="v8-banner-float-label">المحفظة</span>
                                <span class="v8-banner-float-value">{{auth()->check()? number_format(auth()->user()->balance, 2):'0.00'}} {{Settings('currency_text')}}</span>
                            </div>
                        </div>

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
</div>
