<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CmsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Homepage
            ['name' => 'homepage.view', 'module' => 'HOMEPAGE'],
            ['name' => 'homepage.manage', 'module' => 'HOMEPAGE'],

            // Sliders
            ['name' => 'sliders.view', 'module' => 'SLIDERS'],
            ['name' => 'sliders.create', 'module' => 'SLIDERS'],
            ['name' => 'sliders.edit', 'module' => 'SLIDERS'],
            ['name' => 'sliders.delete', 'module' => 'SLIDERS'],

            // Statistics
            ['name' => 'statistics.view', 'module' => 'STATISTICS'],
            ['name' => 'statistics.create', 'module' => 'STATISTICS'],
            ['name' => 'statistics.edit', 'module' => 'STATISTICS'],
            ['name' => 'statistics.delete', 'module' => 'STATISTICS'],

            // Announcements
            ['name' => 'announcements.view', 'module' => 'ANNOUNCEMENTS'],
            ['name' => 'announcements.create', 'module' => 'ANNOUNCEMENTS'],
            ['name' => 'announcements.edit', 'module' => 'ANNOUNCEMENTS'],
            ['name' => 'announcements.publish', 'module' => 'ANNOUNCEMENTS'],
            ['name' => 'announcements.delete', 'module' => 'ANNOUNCEMENTS'],

            // News
            ['name' => 'news.view', 'module' => 'NEWS'],
            ['name' => 'news.create', 'module' => 'NEWS'],
            ['name' => 'news.edit', 'module' => 'NEWS'],
            ['name' => 'news.publish', 'module' => 'NEWS'],
            ['name' => 'news.delete', 'module' => 'NEWS'],

            // Testimonials
            ['name' => 'testimonials.view', 'module' => 'TESTIMONIALS'],
            ['name' => 'testimonials.create', 'module' => 'TESTIMONIALS'],
            ['name' => 'testimonials.edit', 'module' => 'TESTIMONIALS'],
            ['name' => 'testimonials.approve', 'module' => 'TESTIMONIALS'],
            ['name' => 'testimonials.publish', 'module' => 'TESTIMONIALS'],
            ['name' => 'testimonials.delete', 'module' => 'TESTIMONIALS'],

            // FAQs
            ['name' => 'faqs.view', 'module' => 'FAQS'],
            ['name' => 'faqs.create', 'module' => 'FAQS'],
            ['name' => 'faqs.edit', 'module' => 'FAQS'],
            ['name' => 'faqs.delete', 'module' => 'FAQS'],

            // Static Pages
            ['name' => 'pages.view', 'module' => 'PAGES'],
            ['name' => 'pages.create', 'module' => 'PAGES'],
            ['name' => 'pages.edit', 'module' => 'PAGES'],
            ['name' => 'pages.delete', 'module' => 'PAGES'],

            // Contact Messages
            ['name' => 'contact_messages.view', 'module' => 'CONTACT_MESSAGES'],
            ['name' => 'contact_messages.update', 'module' => 'CONTACT_MESSAGES'],
            ['name' => 'contact_messages.delete', 'module' => 'CONTACT_MESSAGES'],
        ];

        $permissionIds = [];
        foreach ($permissions as $perm) {
            $record = Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['module' => $perm['module']]
            );
            $permissionIds[] = $record->id;
        }

        $superAdmin = Role::firstOrCreate(['name' => 'SUPER_ADMIN'], [
            'description' => 'Super Administrator with full platform access',
        ]);

        $superAdmin->permissions()->syncWithoutDetaching($permissionIds);
    }
}
