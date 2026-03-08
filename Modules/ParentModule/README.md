> # Parent Module for LMS

## 1. Introduction

The **Parent Module** is a comprehensive extension for the Laravel-based Learning Management System (LMS) that introduces a dedicated portal for parents and guardians. This module empowers parents to actively participate in their children's education by providing tools to manage their children's accounts, monitor academic progress, handle payments, and stay informed about their activities.

The module is designed to integrate seamlessly with the existing LMS structure, leveraging its authentication, course management, and payment systems while providing a secure and isolated environment for parents. It supports various family structures, allowing for multiple parents or guardians per child with granular permission controls.

## 2. Key Features

The Parent Module offers a wide range of features designed to enhance parental involvement and streamline administrative tasks:

### 2.1. Parent Authentication

- **Secure Registration**: A dedicated registration form for parents to create their own accounts.
- **Parent-Specific Login**: A separate login portal for parents, ensuring a clear distinction from student and instructor accounts.
- **Password Management**: Includes forgot password and change password functionalities integrated with the existing system.

### 2.2. Child Management

- **Add and Link Children**: Parents can either link their account to an existing student account or submit a request to create a new student account.
- **Admin Approval Workflow**: All parent-child linking requests are subject to administrative approval to ensure data privacy and security.
- **Centralized Dashboard**: A comprehensive dashboard provides an at-a-glance overview of all linked children and their key metrics.

### 2.3. Academic Monitoring

- **Course Viewing**: Parents can view all courses their children are enrolled in, including course details and progress.
- **Progress Tracking**: Detailed reports on course completion, quiz results, and certificates earned.
- **Attendance and Grades**: Granular permissions allow parents to view attendance records and grades (requires integration with relevant modules).

### 2.4. Financial Management

- **Course Enrollment**: Parents can browse the course catalog and enroll their children in new courses.
- **Integrated Payments**: The module integrates with the LMS's existing payment gateways, allowing parents to pay for courses on behalf of their children.
- **Payment History**: A detailed payment history page tracks all transactions, with options to view and download invoices.

### 2.5. Communication and Notifications

- **Dedicated Notification System**: Parents receive notifications for important events such as new enrollments, payment confirmations, and grade updates.
- **Customizable Preferences**: Parents can customize their notification preferences to choose how they receive alerts (e.g., email, SMS).

## 3. Database Schema

The module introduces several new tables to the database to support its functionality. All tables are prefixed to avoid conflicts and include an `lms_id` for multi-tenancy support.

| Table Name | Description |
| :--- | :--- |
| `parents` | Stores parent-specific profile information, linked to the main `users` table. |
| `parent_children` | A junction table that manages the many-to-many relationship between parents and students. |
| `parent_payments` | Tracks all payments made by parents for their children's courses. |
| `parent_notifications` | Manages all notifications sent to parents. |
| `parent_student_requests` | Handles requests from parents to link their accounts with student accounts. |

Additionally, a new **"Parent"** role is added to the `roles` table.

## 4. Installation and Configuration

Follow these steps to install and configure the Parent Module:

### 4.1. Installation

1.  **Copy Module Files**: Copy the `ParentModule` directory into the `Modules` directory of your LMS application.

2.  **Update `modules_statuses.json`**: Add the `ParentModule` to your `modules_statuses.json` file to enable it:

    ```json
    {
        "ParentModule": true
    }
    ```

3.  **Run Migrations**: Execute the database migrations to create the necessary tables:

    ```bash
    php artisan migrate
    ```

4.  **Publish Assets**: Although not strictly required for basic functionality, you can publish the views and configuration for customization:

    ```bash
    php artisan vendor:publish --provider="Modules\ParentModule\ParentModuleServiceProvider"
    ```

### 4.2. Configuration

The module's configuration can be found in `/Modules/ParentModule/Config/config.php`. You can publish and modify this file in `config/parentmodule.php`.

Key configuration options include:

- **`auto_approve_requests`**: Set to `true` to automatically approve all parent-child linking requests (not recommended for production).
- **`require_verification`**: Enforce parent identity verification (future feature).
- **`default_permissions`**: Define the default permissions for new parent-child relationships.

## 5. Usage

Once installed, the Parent Module is accessible through the `/parent` URL prefix.

- **Registration**: `/parent/register`
- **Login**: `/parent/login`
- **Dashboard**: `/parent/dashboard` (requires login)

Administrators will need to manage parent-child linking requests from the admin panel (requires development of an admin interface for this). Once a link is approved, the parent can manage their child's account from their dashboard.

## 6. Technical Overview

- **Framework**: Laravel
- **Architecture**: Modular (integrates with `nwidart/laravel-modules`)
- **Authentication**: Utilizes Laravel's standard authentication and a dedicated `parent` middleware.
- **Database**: Adds 5 new tables and extends the `roles` table.
- **Routing**: All parent-facing routes are grouped under the `parent.` name prefix and `/parent` URL prefix.

This module is built to be extensible. You can easily add new features, integrate with other modules, and customize the views to match your LMS's theme.

