# Project Overview

## 1. Project Summary

This is a Laravel-based LMS platform with a public website, dynamic homepage builder, courses, classes, quizzes, blogs, payments, notifications, and role-based dashboards.

Main stack:
- Laravel
- Blade
- Laravel Mix / Webpack
- Modular code under `Modules/`
- Static and generated assets under `public/`
- RTL-aware theme support for Arabic

## 2. Folder and Architecture Overview

- `app/`: controllers, middleware, helpers, models, traits, components
- `routes/`: frontend, tenant, API, console, broadcast routes
- `resources/views/`: Blade layouts, partials, auth pages, frontend theme views
- `public/`: compiled CSS/JS, uploads, theme images, fonts, service worker
- `config/`: Laravel and package configuration
- `database/`: migrations and seeders
- `resources/lang/`: localization files
- `Modules/`: feature modules and module-specific views, assets, migrations
- `webpack.mix.js`: frontend build pipeline
- `package.json`: frontend dependencies/scripts

## 3. Request / Render Flow

Homepage flow:
- route: `/` in `routes/tenant.php`
- controller: `App\Http\Controllers\Frontend\FrontendHomeController@index`
- dynamic homepage view: `aorapagebuilder::pages.show`
- layout: `Modules/AoraPageBuilder/Resources/views/layouts/master.blade.php`
- partials: theme menu/footer/header partials
- dynamic sections: `.dynamicData` blocks loaded during render

## 4. Frontend System

The project uses a hybrid frontend system:
- Laravel Mix compiles source assets
- many assets are served directly from `public/`
- theme assets live in `public/frontend/infixlmstheme/`
- Aora builder/editor assets live in `Modules/AoraPageBuilder/Resources/assets/`

Critical homepage CSS:
- `public/frontend/infixlmstheme/css/app.css`
- `public/frontend/infixlmstheme/css/frontend_style.css`
- `public/frontend/infixlmstheme/css/frontend_style_rtl.css`
- `public/frontend/infixlmstheme/css/mega_menu.css`
- `public/frontend/infixlmstheme/css/custom.css`

Critical homepage JS:
- `public/frontend/infixlmstheme/js/common.js`
- `public/frontend/infixlmstheme/js/app.js`
- `public/frontend/infixlmstheme/js/custom.js`

## 5. Localization / Arabic-English Behavior

- Locale is stored in session/auth user settings and exposed through `app()->getLocale()`
- `isRtl()` controls RTL mode
- RTL assets are loaded conditionally
- Arabic pages use `frontend_style_rtl.css` and `bootstrap.rtl.min.css`

Risks:
- content can contain hardcoded production URLs
- locale-dependent layout branches can break spacing if RTL CSS is missing or stale

## 6. Important Pages and Rendering Sources

- `/` -> `FrontendHomeController@index` -> `aorapagebuilder::pages.show`
- `/about-us` -> `WebsiteController@aboutData`
- `/contact-us` -> `WebsiteController@contact`
- `/courses` -> `CourseController@courses`
- `/classes` -> `ClassController@classes`
- `/quizzes` -> `QuizController@quizzes`
- `/student-dashboard` -> `StudentController@myDashboard`

## 7. Known Technical Issues or Risks

- hardcoded production URLs in saved content or menu data
- mixed asset strategies: Mix output, static public files, module assets
- RTL layout depends on correct conditional Blade branches
- stale cache or service worker can hide real fixes
- module assets may return wrong MIME if served through a proxy without explicit types

## 8. Local Development Notes

Typical local steps:
```bash
composer install
npm install
npm run prod
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
```

Common problems:
- `npm` not on PATH
- Node/OpenSSL mismatch during Mix builds
- stale config/view/cache
- browser service worker cache

## 9. Homepage Styling / Debugging Notes

Files that matter most:
- `app/Http/Controllers/Frontend/FrontendHomeController.php`
- `Modules/AoraPageBuilder/Resources/views/pages/show.blade.php`
- `Modules/AoraPageBuilder/Resources/views/layouts/master.blade.php`
- `resources/views/frontend/infixlmstheme/partials/_menu.blade.php`
- `resources/views/frontend/infixlmstheme/partials/_footer.blade.php`
- `resources/views/frontend/infixlmstheme/partials/header/2.blade.php`
- `app/Helpers/helper3.php`
- `app/Helpers/helper4.php`

First checks when the homepage breaks locally:
- confirm `/` still uses the dynamic homepage path
- confirm RTL CSS loads for Arabic
- confirm no production domain URLs remain in rendered HTML
- confirm critical CSS/JS assets return `200`
- clear caches and hard refresh the browser

