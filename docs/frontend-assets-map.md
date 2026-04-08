# Frontend Assets Map

## 1. Asset System Summary
The project uses a hybrid asset strategy:
- Laravel Mix compiles the main theme bundles
- many files are served directly from `public/`
- module assets are referenced from `Modules/*/Resources/assets/*`
- custom routes in `routes/tenant.php` can proxy those assets locally

Build tooling:
- `package.json`
- `webpack.mix.js`

This frontend stack is Mix/Webpack based, not Vite.

## 2. Homepage CSS Assets
| Asset | Path | Included From | Conditional | RTL? | Build-Generated | Critical | Exists Locally |
|---|---|---|---|---|---|---|---|
| Theme core CSS | `public/frontend/infixlmstheme/css/app.css` | master layout | No | No | Yes | Yes | Yes |
| Main theme stylesheet | `public/frontend/infixlmstheme/css/frontend_style.css` | master layout | LTR branch | No | Yes | Yes | Yes |
| RTL theme stylesheet | `public/frontend/infixlmstheme/css/frontend_style_rtl.css` | master layout | `isRtl()` branch | Yes | Yes | Yes | Yes |
| Bootstrap LTR | `Modules/AoraPageBuilder/Resources/assets/css/bootstrap.min.css` | master layout | No-RTL branch | No | No | Yes | Yes |
| Bootstrap RTL | `Modules/AoraPageBuilder/Resources/assets/css/bootstrap.rtl.min.css` | master layout | RTL branch | Yes | No | Yes | Yes |
| Font Awesome theme CSS | `public/frontend/infixlmstheme/css/fontawesome.css` | master layout | Theme branch | No | Yes | Medium | Yes |
| Mega menu CSS | `public/frontend/infixlmstheme/css/mega_menu.css` | master layout | No | Indirect | No | Yes | Yes |
| Custom CSS | `public/frontend/infixlmstheme/css/custom.css` | master layout | No | Can affect | No | Medium | Yes |
| Preloader CSS | `public/css/preloader.css` | master layout | No | No | No | Medium | Yes |
| Header v7 CSS | `public/frontend/infixlmstheme/css/homepageV7/header-v7.css` | header partial | Header style specific | Partial | No | High | Yes |
| Homepage section CSS | `public/frontend/infixlmstheme/css/sections/homepage_v4.css`, `sponsor.css` | header/sections | Page-specific | No | No | Medium | Yes |

## 3. Homepage JS Assets
| Asset | Path | Included From | Conditional | RTL? | Build-Generated | Critical | Exists Locally |
|---|---|---|---|---|---|---|---|
| Theme common JS | `public/frontend/infixlmstheme/js/common.js` | master layout | No | No | Yes | Yes | Yes |
| Theme app JS | `public/frontend/infixlmstheme/js/app.js` | master layout and footer | No | No | Yes | Yes | Yes |
| Theme custom JS | `public/frontend/infixlmstheme/js/custom.js` | master layout and footer | No | No | No | Medium | Yes |
| Courses JS | `public/frontend/infixlmstheme/js/courses.js` | `show.blade.php` | Route-dependent | No | No | Medium | Yes |
| Lazy JS | `public/frontend/infixlmstheme/js/jquery.lazy.min.js` | master layout | No | No | No | Medium | Yes |
| Map JS | `public/frontend/infixlmstheme/js/map.js` | footer partial | No | No | No | Low/Med | Yes |
| Contact JS | `public/frontend/infixlmstheme/js/contact.js` | footer partial | No | No | No | Low/Med | Yes |
| Builder JS | `Modules/AoraPageBuilder/Resources/assets/js/aoraeditor.js` | master layout | No | No | No | High | Yes |
| Builder components JS | `Modules/AoraPageBuilder/Resources/assets/js/aoraeditor-components.js` | master layout | No | No | No | High | Yes |
| Builder bootstrap JS | `Modules/AoraPageBuilder/Resources/assets/js/bootstrap.min.js` | master layout | No | No | No | Medium | Yes |
| Builder jQuery UI JS | `Modules/AoraPageBuilder/Resources/assets/js/jquery-ui.min.js` | master layout | No | No | No | Medium | Yes |
| Summernote JS | `public/backend/js/summernote-bs5.min.js` | footer partial | No | No | No | Low/Med | Yes |

## 4. Fonts and Icons
| Dependency | Path / Source | Included From | Critical | Exists Locally | Notes |
|---|---|---|---|---|---|
| Themify icons | `public/backend/css/themify-icons.css` | master layout | Yes | Yes | Used by navigation and layout icons |
| Font Awesome | `public/frontend/infixlmstheme/css/fontawesome.css` | master layout | Yes | Yes | Used by many theme icons |
| Google Fonts | external Google Fonts URL | header partial / CSS | Medium | External | Can shift text sizing |
| Webfont loader | external AJAX WebFont script | rendered page | Medium | External | Affects typography rendering |

## 5. Images and Visual Dependencies
| Asset | Path | Included From | Critical | Exists Locally | Notes |
|---|---|---|---|---|---|
| Logo | `Settings('logo')` via `getLogoImage()` | header partial | Yes | Data-driven | Affects header height and brand placement |
| Favicon | `Settings('favicon')` | master layout | Low/Med | Data-driven | Browser tab icon |
| Preloader image | `Settings('preloader_image')` | preloader | Low | Data-driven | Loading UX |
| Homepage banner images | `front_pages` / `home_contents` | stored content | Yes | Data-driven | Can be local or absolute URLs |
| Header/footer style previews | `public/header-footer/header*.jpg` and `footer*.jpg` | admin screens | Admin only | Yes | Used in CMS style picker |

## 6. Builder / Module Assets
Builder assets are referenced directly with `asset('Modules/...')`.
Relevant route support in `routes/tenant.php`:
- `Route::get('Modules/{module}/Resources/assets/{path}', App\Http\Controllers\ModuleAssetProxyController::class)->where('path', '.*')->name('modules.asset.proxy');`

There is also a proxy for public paths:
- `Route::get('public/{path}', App\Http\Controllers\AssetProxyController::class)->where('path', '.*')->name('public.asset.proxy');`

## 7. Exact Homepage Load Order
1. HTML/meta tags
2. theme color components
3. Themify icons
4. builder/module CSS
5. Bootstrap CSS branch
6. Font Awesome/app CSS
7. `frontend_style.css` or `frontend_style_rtl.css`
8. `mega_menu.css`
9. `preloader.css` and `custom.css`
10. `common.js` and `jquery.lazy.min.js`
11. page content and header/footer partials
12. theme `app.js` and `custom.js`
13. Aora builder JS files
14. route-specific scripts like `courses.js`
15. footer scripts like `map.js`, `contact.js`, `summernote-bs5.min.js`

## 8. Conditional Rules
- `isRtl()` selects RTL vs LTR assets
- `currentTheme()` selects the frontend branch
- `Settings('header_style')` and `Settings('footer_style')` select partials
- `Settings('mobile_app_only_mode')` hides menu/footer
- `Settings('hide_menu_search_box')` controls header search
- route names affect some footer JS includes

## 9. Conflict Risks
- duplicate `app.js` / `custom.js` inclusion can happen in some flows
- production URLs can survive inside stored HTML or menus
- module assets and public assets are mixed together
- header partials contain inline CSS/JS that can override compiled styles
- RTL behavior depends on the correct CSS order and `dir="rtl"`

## 10. Local Verification Summary
Current verification showed:
- the critical theme CSS/JS files exist on disk
- the homepage response uses RTL CSS when RTL is active
- the builder asset files exist locally
- the rendered homepage HTML did not show production host URLs in the tested response
