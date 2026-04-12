<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Seed population data
        $this->call([
            // System & Permissions
            PermissionSeeder::class,
            PermissionsSeeder::class,
            SidebarPermissionSeeder::class,
            VillageOfficialsPermissionSeeder::class,
            VillageProfilePermissionSeeder::class,
            SuperAdminUserSeeder::class,
            AuthSeeder::class,

            // Core Profile & Infrastructure
            VillageProfileSeeder::class,
            SettlementSeeder::class,
            LocationSeeder::class,
            InfrastructureSeeder::class,
            // CommunityInstitutionSeeder::class,

            // Users & Demographics
            PopulationDataSeeder::class,
            UpdatePopulationStatusSeeder::class,
            VillageOfficialSeeder::class,
            VillageStatisticsSeeder::class,

            // Business & Budget
            UmkmSeeder::class,
            VillageBudgetSeeder::class,
            BudgetTransactionSeeder::class,

            // Public Content
            NewsAndAgendaSeeder::class,
            AgendaSeeder::class,
            AddMoreAgendaSeeder::class,
            AnnouncementSeeder::class,
            GallerySeeder::class,
            TourismDataSeeder::class,
            TourismObjectSeeder::class,
        ]);
    }
}
