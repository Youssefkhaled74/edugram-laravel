# Parent Module - Installation & Setup Guide

## Overview

This guide will walk you through the complete installation and configuration of the **Parent Module** for your Laravel LMS system.

## Prerequisites

Before installing the Parent Module, ensure you have:

- Laravel LMS application (version 7.x or higher)
- PHP 7.4 or higher
- MySQL/MariaDB database
- Composer installed
- Basic knowledge of Laravel

## Installation Steps

### Step 1: Extract Module Files

1. Extract the `ParentModule.zip` file
2. Copy the entire `ParentModule` folder to your LMS's `Modules` directory

```bash
# Navigate to your LMS root directory
cd /path/to/your/lms

# Copy the module
cp -r /path/to/ParentModule ./Modules/
```

### Step 2: Enable the Module

Add the ParentModule to your `modules_statuses.json` file (usually located in the root directory):

```json
{
    "ParentModule": true
}
```

If the file doesn't exist, create it with the above content.

### Step 3: Update Composer Autoload

Run composer dump-autoload to register the new module:

```bash
composer dump-autoload
```

### Step 4: Run Database Migrations

Execute the migrations to create all necessary database tables:

```bash
php artisan migrate
```

This will create the following tables:
- `parents` - Parent profile information
- `parent_children` - Parent-student relationships
- `parent_payments` - Payment records
- `parent_notifications` - Notification system
- `parent_student_requests` - Link requests

It will also add a "Parent" role to your `roles` table.

### Step 5: Publish Assets (Optional)

If you want to customize the views or configuration:

```bash
# Publish views
php artisan vendor:publish --provider="Modules\ParentModule\ParentModuleServiceProvider" --tag="views"

# Publish config
php artisan vendor:publish --provider="Modules\ParentModule\ParentModuleServiceProvider" --tag="config"
```

### Step 6: Clear Cache

Clear all Laravel caches to ensure the module is properly loaded:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Configuration

### Module Configuration

The module configuration file is located at:
- `Modules/ParentModule/Config/config.php` (default)
- `config/parentmodule.php` (after publishing)

Key configuration options:

```php
'settings' => [
    'auto_approve_requests' => false,  // Auto-approve parent-child links
    'require_verification' => true,     // Require parent verification
    'allow_multiple_parents' => true,   // Allow multiple parents per child
],

'default_permissions' => [
    'can_make_payments' => true,
    'can_view_grades' => true,
    'can_enroll_courses' => true,
],
```

### Environment Variables

No additional environment variables are required. The module uses your existing database and authentication configuration.

## Accessing the Parent Portal

Once installed, the parent portal is accessible at:

- **Registration**: `https://yourdomain.com/parent/register`
- **Login**: `https://yourdomain.com/parent/login`
- **Dashboard**: `https://yourdomain.com/parent/dashboard` (after login)

## Admin Configuration

### Approving Parent-Child Link Requests

Currently, parent-child link requests need to be approved manually through the database. You can:

1. **View pending requests** in the `parent_student_requests` table
2. **Approve a request** by:
   - Setting `status` to `'approved'`
   - Creating a record in `parent_children` table
   - Optionally sending a notification

**Recommended**: Create an admin interface to manage these requests. A sample admin controller can be added to handle this workflow.

### Setting Up Payment Gateways

The module includes placeholders for payment gateway integration. To enable payments:

1. Install your preferred payment gateway package (PayPal, Stripe, etc.)
2. Update the `processPaymentGateway()` method in `ParentPaymentController.php`
3. Configure gateway credentials in your `.env` file

## Features Overview

### For Parents

- **Registration & Authentication**: Dedicated parent portal
- **Child Management**: Link to existing students or request new accounts
- **Course Viewing**: View all courses their children are enrolled in
- **Progress Tracking**: Detailed reports on course completion and quiz results
- **Payment Management**: Pay for courses and view payment history
- **Notifications**: Receive updates about children's activities

### For Administrators

- **Request Management**: Approve/reject parent-child link requests
- **Permission Control**: Set granular permissions for each parent-child relationship
- **Payment Tracking**: Monitor all parent payments

## Database Schema

### Main Tables

| Table | Purpose |
|-------|---------|
| `parents` | Stores parent profile data (linked to `users` table) |
| `parent_children` | Many-to-many relationship between parents and students |
| `parent_payments` | All payment transactions made by parents |
| `parent_notifications` | Notification system for parents |
| `parent_student_requests` | Pending requests for parent-child links |

### Relationships

- A parent can have multiple children (many-to-many)
- A student can have multiple parents (many-to-many)
- Each parent-child relationship has its own permissions
- Payments are linked to both parent and student

## Troubleshooting

### Module Not Loading

1. Check `modules_statuses.json` has `"ParentModule": true`
2. Run `composer dump-autoload`
3. Clear all caches: `php artisan cache:clear`

### Routes Not Working

1. Clear route cache: `php artisan route:clear`
2. Check if web middleware is properly configured
3. Verify Apache/Nginx configuration

### Database Errors

1. Ensure migrations ran successfully: `php artisan migrate:status`
2. Check database connection in `.env`
3. Verify user has proper database permissions

### Views Not Found

1. Check views exist in `Modules/ParentModule/Resources/views/`
2. Clear view cache: `php artisan view:clear`
3. Verify service provider is registered

## Customization

### Customizing Views

All Blade templates are located in `Modules/ParentModule/Resources/views/`. You can:

1. Publish views: `php artisan vendor:publish --tag="views"`
2. Edit published views in `resources/views/vendor/parentmodule/`
3. Customize layouts, colors, and styles

### Adding Custom Permissions

Edit the `parent_children` migration to add new permission columns, then update:
- `ParentModel.php` - Add permission check methods
- `ParentChild.php` - Add to fillable array
- Views - Add permission checkboxes

### Extending Functionality

The module is designed to be extensible. You can:

- Add new controllers for additional features
- Create custom routes in `Routes/web.php`
- Add new models for additional data
- Integrate with other LMS modules

## Security Considerations

1. **Always validate parent-child relationships** before displaying data
2. **Use middleware** to ensure parents can only access their own children's data
3. **Implement proper authorization** for all sensitive operations
4. **Validate file uploads** to prevent security vulnerabilities
5. **Use HTTPS** in production for secure data transmission

## Support & Maintenance

### Regular Maintenance

- Monitor the `parent_student_requests` table for pending requests
- Review payment records regularly
- Keep the module updated with your LMS version

### Backup

Always backup your database before:
- Running new migrations
- Updating the module
- Making structural changes

## Next Steps

After installation:

1. **Test the registration flow** - Create a test parent account
2. **Create test requests** - Link a test student to the parent
3. **Approve the request** - Manually approve through database
4. **Test all features** - Courses, reports, payments
5. **Customize views** - Match your LMS theme
6. **Set up payment gateways** - Configure your payment providers
7. **Create admin interface** - Build admin tools for request management

## Conclusion

The Parent Module is now installed and ready to use. Parents can register, link their children, monitor progress, and manage payments all from a dedicated portal.

For questions or issues, please refer to the README.md file or contact your development team.

