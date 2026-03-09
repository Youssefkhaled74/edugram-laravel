# Edugram Laravel - Easy English Project Guide

## 1) What is this project?
This project is an online learning platform (LMS) built with Laravel.
Users can:
- register and login
- buy courses
- learn from lessons/quizzes/classes
- get certificates

Admins and instructors can:
- create/manage courses
- manage students and payments
- manage many features through modules

## 2) Main technology stack
- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Blade templates + JS/CSS assets
- Database: MySQL (there is a SQL dump in `database/infixlms.sql`)
- Auth/API: Laravel auth + API routes in `routes/api.php`
- Modular system: `nwidart/laravel-modules` (`Modules/` folder)

## 3) Quick folder map (important folders)
- `app/` -> core app logic (controllers, models, providers, helpers)
- `Modules/` -> feature modules (Payment, Quiz, Blog, Zoom, etc.)
- `routes/` -> route entry files
- `resources/views/` -> Blade UI pages
- `database/` -> migrations, seeders, SQL dump
- `config/` -> app configuration
- `public/` -> public assets and entry point
- `storage/` -> logs, cache, sessions, uploaded/generated files

## 4) How routing works in this project
- `routes/web.php` is very small and loads `routes/tenant.php`
- Most website routes are in `routes/tenant.php`
- API endpoints are in `routes/api.php`
- Many modules also have their own routes in `Modules/*/Routes/`

Simple request flow:
1. User opens URL
2. Route matches in `routes/tenant.php` (or module route)
3. Controller handles logic
4. Model reads/writes DB
5. Blade view returns HTML (or API returns JSON)

## 5) Modules (very important in this codebase)
This project has many modules, for example:
- `Payment`, `PaymentMethodSetting`, `Wallet`
- `Quiz`, `VirtualClass`, `Zoom`, `Jitsi`
- `Blog`, `Certificate`, `Coupon`, `AmazonS3`

Enabled/disabled modules are tracked in:
- `modules_statuses.json`

If a feature looks "missing", first check if its module is disabled.

## 6) Local setup (basic)
Run inside project root:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Then update `.env` (DB, mail, queue, cache, app URL).

Database options:
- Option A: import `database/infixlms.sql`
- Option B: run migrations/seeders if your flow requires it

Then run app:

```bash
php artisan serve
```

If frontend assets need build:

```bash
npm install
npm run dev
```

## 7) First files to read to understand the project fast
Read in this order:
1. `composer.json` (packages and architecture clues)
2. `modules_statuses.json` (active feature map)
3. `routes/web.php` and `routes/tenant.php` (main web flow)
4. `routes/api.php` (mobile/API flow)
5. `config/app.php` (providers and app behavior)
6. A target module folder you care about (example: `Modules/Quiz`)

## 8) Where to edit common things
- Add/modify web endpoint: `routes/tenant.php` + target controller
- Add/modify API endpoint: `routes/api.php`
- Change UI page: `resources/views/...`
- Change module feature: `Modules/<ModuleName>/...`
- Change global settings behavior: `app/Providers/...` or `config/...`

## 9) High-risk areas (edit carefully)
- Payment modules and checkout logic
- Auth/2FA endpoints in API
- Module enable/disable states
- DB schema + old SQL dump compatibility
- Any file with credentials or API key placeholders

## 10) Safe learning workflow
1. Pick one feature (example: Quiz)
2. Trace route -> controller -> service/model -> view
3. Make a very small change
4. Test in browser/API
5. Commit small and clear

## 11) Useful commands
```bash
php artisan route:list
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
php artisan migrate:status
```

## 12) Notes for your repository
- Keep real secrets only in `.env` (never in committed files)
- If GitHub blocks push because of secret scanning, remove secret from commit history and push again
- Prefer small commits; this project is large and modular

## 13) Business view (how this LMS makes value)
This system is not only code. It is a business platform.

Main business goals:
- Sell learning content (courses, quizzes, classes)
- Help students finish learning and get certificates
- Help instructors publish content and earn revenue
- Help admin control quality, payments, and growth

Main value for each side:
- Student: easy learning, progress, certificate
- Instructor: upload once, sell many times
- Admin/Owner: platform income + user growth + brand

## 14) User roles in business language
Typical roles in this project:
- `Admin`: controls settings, modules, users, payments, reports
- `Instructor`: creates courses/quizzes, manages students, tracks earnings
- `Student`: browses, buys, learns, takes quizzes, gets certificates
- `Affiliate` (if active): promotes courses and gets commission
- `Parent` module users (if active): monitor learner activity

Why roles matter:
- Each role has different menu, permissions, and data access
- Most business mistakes come from wrong role permissions

## 15) Core business processes (end-to-end)
### A) Student purchase journey
1. Student signs up / logs in
2. Student browses course catalog
3. Student adds course to cart
4. Student chooses payment method
5. Payment success creates enrollment/order
6. Student accesses lessons/quizzes
7. Completion can create certificate

### B) Instructor content journey
1. Instructor creates course structure
2. Adds lessons, files, quizzes, classes
3. Submits or publishes course
4. Students enroll and start learning
5. Instructor tracks reviews, progress, and revenue

### C) Admin operations journey
1. Enable needed modules (`modules_statuses.json`)
2. Configure payments, emails, certificates, storage
3. Manage users/roles/permissions
4. Review orders, refunds, payouts
5. Track growth with reports

## 16) Revenue model (money flow)
Money usually flows like this:
1. Student pays course price
2. Platform records order/transaction
3. System splits revenue (platform share + instructor share)
4. Instructor payout can happen later (manual or gateway flow)

Related modules you may see:
- `Payment`, `PaymentMethodSetting`, `Wallet`
- Gateway modules (`Razorpay`, `Paytm`, `PayStack`, `Midtrans`, etc.)
- `OfflinePayment`, `BankPayment` for manual payment approval

Business risk points:
- wrong currency/rate setup
- wrong commission settings
- failed callbacks from payment gateways
- refunds not synced with enrollments

## 17) Learning model (education flow)
Important learning objects:
- Category -> Course -> Chapter/Section -> Lesson
- Quiz/Class can be part of the learning path
- Completion rules decide certificate and progress

Good business check:
- If students buy but do not finish, issue is usually in content structure,
  UX, or progress rules (not only marketing).

## 18) Important business entities in database
You will commonly work with:
- Users (students/instructors/admins)
- Courses, lessons, quizzes, classes
- Orders, carts, transactions
- Enrollments (who has access to what)
- Reviews/comments
- Certificates
- Coupons and discounts
- Refund requests

Simple relation idea:
- One instructor -> many courses
- One student -> many enrollments/orders
- One order -> one or more course items

## 19) Business KPIs you should track
If you want to run the platform well, track these:
- New registrations per day/week
- Conversion rate (visitor -> buyer)
- Average order value
- Course completion rate
- Refund rate
- Instructor active rate
- Revenue by category
- Top payment method success rate

Where code usually exists:
- Dashboard/report controllers
- API endpoints for mobile dashboards
- module-specific report pages

## 20) Multi-module strategy (how to think before coding)
Before adding any feature, ask:
1. Is there already a module for it?
2. Is module enabled?
3. Is this global feature or module feature?
4. Does this affect payment, permission, or certificate logic?

Practical rule:
- If feature is business-critical (payment, enrollment, access), test full flow end-to-end after any change.

## 21) How to read the project like a business engineer
Use this method for each feature:
1. Business question: what problem does this solve?
2. Entry point: where user starts (web/API route)
3. Core logic: controller/service/module
4. Data impact: which tables update?
5. Side effects: email, notification, invoice, wallet, certificate
6. Failure case: what happens if payment/email fails?

Example (buy course):
- Route/API receives checkout request
- Payment module processes gateway
- On success: order + enrollment + transaction saved
- Optional: notify user/instructor

## 22) Day-to-day admin checklist (real business use)
Daily:
- Check failed payments
- Check new enrollments and support issues
- Check error logs (`storage/logs`)

Weekly:
- Review top-selling courses and low-conversion courses
- Review refund reasons
- Review inactive instructors/students

Monthly:
- Payout review
- Revenue and growth report
- Security review (secrets, access, backups)

## 23) Fast troubleshooting map (business impact first)
If issue is "student paid but no access":
1. Verify payment status
2. Verify order record
3. Verify enrollment record
4. Verify course publish/access settings

If issue is "course not visible":
1. Verify module enabled
2. Verify course status/publish
3. Verify category/filter visibility
4. Verify frontend cache/config

If issue is "email not sent":
1. Verify mail settings (`SystemSetting`)
2. Verify queue/worker (if queued)
3. Check logs for SMTP/API errors

## 24) Suggested next docs to create (optional)
After this file, best extra docs are:
1. `docs/PAYMENT_FLOW_EASY.md`
2. `docs/COURSE_LIFECYCLE_EASY.md`
3. `docs/ROLE_PERMISSION_EASY.md`
4. `docs/TROUBLESHOOTING_EASY.md`

## 25) Database schema (easy map)
Source used:
- Main SQL dump: `database/infixlms.sql`

Quick fact:
- Total tables in this dump: `94`

### A) Main table groups
1. Identity and access
- `users`
- `roles`
- `permissions`
- `role_permission`
- `password_resets`
- `oauth_*` tables

2. Course and learning
- `categories`
- `sub_categories`
- `courses`
- `chapters`
- `lessons`
- `lesson_completes`
- `online_quizzes`
- `quiz_tests`
- `quiz_test_details`
- `student_take_online_quizzes`

3. Commerce and payment
- `carts`
- `checkouts`
- `orders`
- `course_enrolleds`
- `payment_methods`
- `coupons`
- `withdraws`
- `instructor_payouts`
- `offline_payments`
- `bank_payment_requests`

4. Content and engagement
- `blogs`
- `course_comments`
- `course_comment_replies`
- `course_reveiws`
- `notifications`
- `messages`

5. Settings and platform
- `general_settings`
- `business_settings`
- `email_settings`
- `frontend_settings`
- `languages`
- `currencies`
- `time_zones`
- `themes`
- `modules`

### B) Key schema relationships (business core)
Think of this as the core data graph:

1. User and role
- `users.role_id -> roles.id`
- Permission mapping: `role_permission.role_id -> roles.id`

2. Course catalog
- `sub_categories.category_id -> categories.id`
- `courses.category_id -> categories.id`
- `courses.subcategory_id -> sub_categories.id`
- `courses.user_id -> users.id` (course instructor/creator)

3. Course content
- `chapters.course_id -> courses.id`
- `lessons.course_id -> courses.id`
- `lessons.chapter_id -> chapters.id`
- `lesson_completes.lesson_id -> lessons.id`
- `lesson_completes.user_id -> users.id`

4. Enrollment and purchase
- `carts.user_id -> users.id`
- `carts.course_id -> courses.id`
- `orders.user_id -> users.id` (stored as varchar in this schema)
- `course_enrolleds.user_id -> users.id`
- `course_enrolleds.course_id -> courses.id`
- `course_enrolleds.tracking` usually matches order tracking flow

5. Quiz flow
- `online_quizzes.course_id -> courses.id` (nullable)
- `quiz_tests.user_id -> users.id` (by usage)
- `student_take_online_quizzes.student_id -> users.id`
- `student_take_online_quizzes.online_exam_id -> online_quizzes.id`

6. Revenue and payout
- `withdraws.instructor_id -> users.id`
- `instructor_payouts.user_id` / instructor reference by module logic
- `orders.transaction_id` links to gateway reference strings

### C) Important table snapshots (easy reading)
1. `users`
- Main columns: `id`, `role_id`, `name`, `username`, `email`, `password`, `status`, `balance`
- Stores both students and instructors/admins

2. `courses`
- Main columns: `id`, `category_id`, `subcategory_id`, `user_id`, `title`, `price`, `discount_price`, `publish`, `status`, `type`
- `type`: `1=Course`, `2=Quiz` (from schema comment)

3. `lessons`
- Main columns: `id`, `course_id`, `chapter_id`, `name`, `video_url`, `host`, `is_lock`, `is_quiz`

4. `orders`
- Main columns: `id`, `user_id`, `tracking`, `amount`, `status`, `transaction_id`, `currency`
- Business note: this stores checkout/payment summary

5. `course_enrolleds`
- Main columns: `id`, `tracking`, `user_id`, `course_id`, `purchase_price`, `coupon`, `discount_amount`, `status`, `reveune`
- Business note: this table gives course access after purchase

6. `payment_methods`
- Stores configured payment gateways and activation

### D) Simple ER flow (mental model)
Use this chain when debugging:
1. `users` -> who paid
2. `orders` -> payment record
3. `course_enrolleds` -> access granted or not
4. `courses` -> course exists/published
5. `lessons` + `lesson_completes` -> learning progress

If issue is "paid but cannot open course", check these 5 tables in order.

### E) SQL commands to inspect schema quickly
If DB is imported locally, run:

```sql
SHOW TABLES;
DESCRIBE users;
DESCRIBE courses;
DESCRIBE orders;
DESCRIBE course_enrolleds;
```

Check real foreign key constraints:

```sql
SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, COLUMN_NAME;
```

### F) Important schema notes
- This project is modular, so some module tables appear only when that module is installed/migrated.
- Some relationships are logical (application-level) and may not be strict DB foreign keys.
- Table names include legacy spellings like `course_reveiws` and `reveune`; keep them as-is unless you plan a careful migration.

---
If you want, I can also make a second file later with a "feature-by-feature map" (Quiz, Payment, Blog, Certificates) in the same easy English style.
