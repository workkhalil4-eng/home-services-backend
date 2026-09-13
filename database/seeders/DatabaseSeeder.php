<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\ProviderProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Category
        $category = Category::create([
            'name' => 'سباكة',
            'icon' => 'plumbing',
            'description' => 'خدمات السباكة المنزلية',
            'is_active' => true,
        ]);

        // 2. Create a Service
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'إصلاح تسريب مياه',
            'base_price' => 50.00,
            'is_fixed_price' => true,
            'description' => 'إصلاح تسريبات الأنابيب والصنابير',
            'is_active' => true,
        ]);

        // 3. Create a Demo Provider User
        $provider = User::create([
            'name' => 'فني سباكة ديمو',
            'phone' => '0500000001',
            'email' => 'provider@demo.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'is_active' => true,
        ]);

        // 4. Create an approved Provider Profile
        $providerProfile = ProviderProfile::create([
            'user_id' => $provider->id,
            'is_available' => true,
            'kyc_status' => 'approved',
        ]);

        // Optional: Attach category to provider if CategoryProviderProfile table is used
        DB::table('category_provider_profile')->insert([
            'category_id' => $category->id,
            'provider_profile_id' => $providerProfile->id,
        ]);
        
        // 5. Create a Demo Customer User
        User::create([
            'name' => 'زبون ديمو',
            'phone' => '0500000002',
            'email' => 'customer@demo.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);
    }
}
