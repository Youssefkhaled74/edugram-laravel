<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FixMenuTranslations extends Command
{
    protected $signature = 'fix:menu-translations';
    protected $description = 'Fix menu translations by converting plain text to JSON format';

    public function handle()
    {
        $this->info('Starting menu translation fix...');

        // Fix permissions table
        $permissionsFixed = $this->fixPermissions();
        $this->info("Fixed {$permissionsFixed} permission records");

        // Fix permission_sections table
        $sectionsFixed = $this->fixPermissionSections();
        $this->info("Fixed {$sectionsFixed} permission section records");

        // Clear all sidebar caches
        $this->clearCaches();
        $this->info('Cleared all sidebar caches');

        $this->info('Menu translation fix completed successfully!');
        return 0;
    }

    private function fixPermissions()
    {
        $permissions = DB::table('permissions')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->where('name', 'NOT LIKE', '{%')
            ->get();

        $count = 0;
        foreach ($permissions as $permission) {
            $name = $permission->name;
            
            // Skip if already JSON
            if (strpos($name, '{') === 0) {
                continue;
            }

            // Create JSON translation
            $jsonName = json_encode([
                'en' => $name,
                'ar' => $name  // Set to same value, admin can update later
            ], JSON_UNESCAPED_UNICODE);

            DB::table('permissions')
                ->where('id', $permission->id)
                ->update(['name' => $jsonName]);

            $count++;
        }

        return $count;
    }

    private function fixPermissionSections()
    {
        if (!DB::getSchemaBuilder()->hasTable('permission_sections')) {
            return 0;
        }

        $sections = DB::table('permission_sections')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->where('name', 'NOT LIKE', '{%')
            ->get();

        $count = 0;
        foreach ($sections as $section) {
            $name = $section->name;
            
            // Skip if already JSON
            if (strpos($name, '{') === 0) {
                continue;
            }

            // Create JSON translation
            $jsonName = json_encode([
                'en' => $name,
                'ar' => $name  // Set to same value, admin can update later
            ], JSON_UNESCAPED_UNICODE);

            DB::table('permission_sections')
                ->where('id', $section->id)
                ->update(['name' => $jsonName]);

            $count++;
        }

        return $count;
    }

    private function clearCaches()
    {
        // Clear all sidebar permission caches
        $domain = function_exists('SaasDomain') ? SaasDomain() : 'main';
        
        Cache::forget('PermissionList_' . $domain);
        Cache::forget('MenuPermissionList_' . $domain);
        Cache::forget('RoleList_' . $domain);
        Cache::forget('oldPermissionSync' . $domain);
        
        // Clear for all role IDs
        for ($i = 1; $i <= 5; $i++) {
            Cache::forget('SidebarPermissionList_' . $i . $domain);
        }

        // Also try to clear with user IDs (in case Org module is not active)
        Cache::flush();
    }
}
