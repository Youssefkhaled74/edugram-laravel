# Project Overview

## 1. Project Summary
EduGram is a modular Laravel LMS with a public marketing site, authenticated dashboards, courses/classes/quizzes, organization and parent flows, payments, notifications, and a theme-driven homepage builder.

Main stack:
- Laravel + Blade
- Laravel Mix / Webpack
- Modular feature folders under `Modules/`
- Static/public assets under `public/`
- RTL-aware frontend theme support

## 2. Architecture Overview
The codebase is split between the core app and feature modules.

### Core Laravel layer
- `app/Http/Controllers/`: frontend/auth/shared controllers
- `app/Helpers/helper3.php` and `app/Helpers/helper4.php`: global helpers used in Blade and controllers
- `app/Providers/AppServiceProvider.php`: shares theme data, menus, categories, languages, and homepage content
- `app/View/Components/`: reusable frontend/backend sections

### Modular layer
Important modules for the public site:
- `Modules/FrontendManage`: homepage blocks, page records, menus, header/footer styles, custom CSS/JS
- `Modules/AoraPageBuilder`: dynamic homepage rendering and page-builder layout/assets
- `Modules/RolePermission`: roles/permissions
- `Modules/Localization`: language switching
- `Modules/ParentModule`, `Modules/Org`, `Modules/OrgSubscription`, `Modules/Quiz`, `Modules/CourseSetting`, `Modules/Payment`, `Modules/Zoom`, `Modules/Store`, `Modules/Blog`, `Modules/Chat`

### Theme layer
The active public theme is usually `infixlmstheme`, with other branches like `edume`, `kidslms`, `teachery`, and `wetech` referenced by helpers and views.

## 3. Important Folders
### `routes/`
`routes/tenant.php` is the key frontend route file. It contains:
- homepage route `/`
- auth/public routes
- asset proxy routes for `public/{path}` and `Modules/{module}/Resources/assets/{path}`
- language switching and public LMS routes

### `resources/views/`
- `resources/views/frontend/infixlmstheme/`: public theme views and partials
- `resources/views/backend/`: admin UI
- `resources/views/layouts/`: standard app layouts
- `resources/views/partials/`: shared fragments such as preloaders and emails

### `public/`
Holds compiled runtime assets and uploads:
- `public/frontend/infixlmstheme/`
- `public/backend/`
- `public/js/`
- `public/uploads/`
- fonts, icons, vendors, previews

### `database/`
Migrations do more than schema work here. They also seed homepage HTML blocks, update default homepage content, and set header/footer style defaults.

### Build files
- `package.json`
- `webpack.mix.js`

## 4. Laravel Layers in Use
### Controllers
Important frontend controllers:
- `app/Http/Controllers/Frontend/FrontendHomeController.php`
- `Modules/FrontendManage/Http/Controllers/FrontPageController.php`
- `Modules/FrontendManage/Http/Controllers/FrontendManageController.php`
- `Modules/FrontendManage/Http/Controllers/CustomStyleScriptController.php`
- `Modules/FrontendManage/Http/Controllers/HeaderFooterStyleController.php`

### Middleware
The homepage controller uses:
- `maintenanceMode`
- `onlyAppMode`

### Models and domain objects
Frequently used in the homepage/render chain:
- `Modules\FrontendManage\Entities\FrontPage`
- `Modules\FrontendManage\Entities\HomeContent`
- `Modules\FrontendManage\Entities\HeaderMenu`
- `Modules\CourseSetting\Entities\Category`
- `Modules\RolePermission\Entities\Permission`
- `Modules\Setting\Model\GeneralSetting`
- `App\User`

## 5. Module Responsibilities
### `Modules/FrontendManage`
Frontend CMS and public homepage orchestration:
- homepage blocks and ordering
- page records and content editing
- header/footer style selection
- menu management
- custom CSS/JS editor
- slider/banner/home content admin screens

### `Modules/AoraPageBuilder`
Dynamic page rendering:
- renders stored homepage HTML
- injects builder assets
- powers the `/` page-builder flow

### `Modules/RolePermission`
- role definitions such as super admin, instructor, student, staff, organization, parent
- permission trees for admin navigation

### `Modules/Localization`
- locale switching routes
- language-related behavior

### `Modules/ParentModule`
- parent role and child/student-related flows

## 6. Route Organization
`routes/tenant.php` is the main public route file. It registers auth routes, asset proxy routes, and then a `Route::group(['namespace' => 'Frontend'], ...)` block with the homepage and public LMS pages.

## 7. Request / Render Flow
The homepage path is:
- `/` -> `FrontendHomeController@index`
- if dynamic pages are enabled, it loads `FrontPage` slug `/`
- it runs `dynamicContentAppend($row->details)`
- it returns `aorapagebuilder::pages.show`
- that view extends `aorapagebuilder::layouts.master`
- the master layout includes the theme menu/footer and loads theme + builder assets

## 8. Frontend System
The frontend uses a hybrid strategy:
- Laravel Mix builds theme JS/CSS
- many files are served directly from `public/`
- builder assets are referenced from `Modules/AoraPageBuilder/Resources/assets/`
- some module assets are reachable through custom proxy routes

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
- `public/frontend/infixlmstheme/js/courses.js`

## 9. Localization / Arabic-English Behavior
- `isRtl()` decides RTL mode
- the master layout switches `dir` and CSS files
- the header partial adjusts submenu/search behavior with JS
- Arabic pages load `frontend_style_rtl.css` and `bootstrap.rtl.min.css`
- language switching is exposed in the header when frontend translation is enabled

## 10. Key Technical Risks
- hardcoded production URLs can survive inside stored homepage HTML or menus
- the project mixes Mix output, static public assets, and module assets
- RTL behavior depends on both CSS order and conditional Blade branches
- page-builder HTML is stored in the database, so old environment assumptions can leak into local rendering
- theme branches differ, so the wrong theme path can change the asset stack

## 11. Local Setup Notes
Recommended local steps:
```bash
composer install
npm install
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8000
```
If assets need rebuilding:
```bash
npm run prod
```
The repo already configures `NODE_OPTIONS=--openssl-legacy-provider` for webpack compatibility.

## 12. Developer Notes
- `AppServiceProvider` injects menus, languages, categories, and homepage content into frontend views.
- `database/migrations` contain homepage content-seeding migrations, not just schema changes.
- If layout breaks locally, inspect the rendered HTML and asset URLs before assuming a missing CSS file.
