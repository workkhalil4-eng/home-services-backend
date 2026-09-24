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
        // 1. Plumbing (سباكة)
        $plumbingCategory = Category::firstOrCreate(
            ['name' => 'سباكة'],
            [
                'icon' => 'plumbing',
                'description' => 'خدمات السباكة المنزلية وتسريب المياه',
                'is_active' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'إصلاح تسريب مياه', 'category_id' => $plumbingCategory->id],
            [
                'base_price' => 50.00,
                'is_fixed_price' => true,
                'description' => 'إصلاح تسريبات الأنابيب والصنابير والخلاطات',
                'is_active' => true,
            ]
        );

        $plumbingProvider = User::firstOrCreate(
            ['phone' => '0500000001'],
            [
                'name' => 'فني سباكة ديمو',
                'email' => 'plumber@demo.com',
                'password' => Hash::make('password'),
                'role' => 'provider',
                'is_active' => true,
            ]
        );

        $plumbingProfile = ProviderProfile::firstOrCreate(
            ['user_id' => $plumbingProvider->id],
            [
                'is_available' => true,
                'kyc_status' => 'approved',
                'max_travel_distance' => 20000,
                'latitude' => 24.7136,
                'longitude' => 46.6753,
            ]
        );

        DB::table('category_provider_profile')->updateOrInsert(
            ['category_id' => $plumbingCategory->id, 'provider_profile_id' => $plumbingProfile->id],
            []
        );

        // 2. Electrical (كهرباء)
        $electricCategory = Category::firstOrCreate(
            ['name' => 'كهرباء'],
            [
                'icon' => 'electrical',
                'description' => 'إصلاح الأعطال الكهربائية وتمديدات الإنارة',
                'is_active' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'إصلاح أعطال كهربائية', 'category_id' => $electricCategory->id],
            [
                'base_price' => 60.00,
                'is_fixed_price' => true,
                'description' => 'فحص القواطع وتمديد وصيانة الأفياش والإنارة',
                'is_active' => true,
            ]
        );

        $electricProvider = User::firstOrCreate(
            ['phone' => '0500000003'],
            [
                'name' => 'فني كهرباء ديمو',
                'email' => 'electrician@demo.com',
                'password' => Hash::make('password'),
                'role' => 'provider',
                'is_active' => true,
            ]
        );

        $electricProfile = ProviderProfile::firstOrCreate(
            ['user_id' => $electricProvider->id],
            [
                'is_available' => true,
                'kyc_status' => 'approved',
                'max_travel_distance' => 20000,
                'latitude' => 24.7136,
                'longitude' => 46.6753,
            ]
        );

        DB::table('category_provider_profile')->updateOrInsert(
            ['category_id' => $electricCategory->id, 'provider_profile_id' => $electricProfile->id],
            []
        );

        // 3. Air Conditioning (تكييف)
        $acCategory = Category::firstOrCreate(
            ['name' => 'تكييف'],
            [
                'icon' => 'ac',
                'description' => 'صيانة وتنظيف مكيفات سبليت وشباك',
                'is_active' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'صيانة وتنظيف مكيف', 'category_id' => $acCategory->id],
            [
                'base_price' => 80.00,
                'is_fixed_price' => true,
                'description' => 'تنظيف فلاتر، شحن فريون، وصيانة وحدات التبريد',
                'is_active' => true,
            ]
        );

        $acProvider = User::firstOrCreate(
            ['phone' => '0500000004'],
            [
                'name' => 'فني تكييف ديمو',
                'email' => 'ac@demo.com',
                'password' => Hash::make('password'),
                'role' => 'provider',
                'is_active' => true,
            ]
        );

        $acProfile = ProviderProfile::firstOrCreate(
            ['user_id' => $acProvider->id],
            [
                'is_available' => true,
                'kyc_status' => 'approved',
                'max_travel_distance' => 20000,
                'latitude' => 24.7136,
                'longitude' => 46.6753,
            ]
        );

        DB::table('category_provider_profile')->updateOrInsert(
            ['category_id' => $acCategory->id, 'provider_profile_id' => $acProfile->id],
            []
        );

        // 4. Painting (دهان)
        $paintCategory = Category::firstOrCreate(
            ['name' => 'دهان'],
            [
                'icon' => 'painting',
                'description' => 'دهان وترميم الجدران الداخلية والخارجية',
                'is_active' => true,
            ]
        );

        Service::firstOrCreate(
            ['name' => 'دهان الجدران والترميم', 'category_id' => $paintCategory->id],
            [
                'base_price' => 120.00,
                'is_fixed_price' => false,
                'description' => 'طلاء غرف، معالجة الرطوبة والتشققات وتجديد الألوان',
                'is_active' => true,
            ]
        );

        $paintProvider = User::firstOrCreate(
            ['phone' => '0500000005'],
            [
                'name' => 'فني دهان ديمو',
                'email' => 'painter@demo.com',
                'password' => Hash::make('password'),
                'role' => 'provider',
                'is_active' => true,
            ]
        );

        $paintProfile = ProviderProfile::firstOrCreate(
            ['user_id' => $paintProvider->id],
            [
                'is_available' => true,
                'kyc_status' => 'approved',
                'max_travel_distance' => 20000,
                'latitude' => 24.7136,
                'longitude' => 46.6753,
            ]
        );

        DB::table('category_provider_profile')->updateOrInsert(
            ['category_id' => $paintCategory->id, 'provider_profile_id' => $paintProfile->id],
            []
        );

        // 5. Demo Customer User
        User::firstOrCreate(
            ['phone' => '0500000002'],
            [
                'name' => 'زبون ديمو',
                'email' => 'customer@demo.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );
    }
}
