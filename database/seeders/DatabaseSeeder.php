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
        $plumbingCategory = Category::create([
            'name' => 'سباكة',
            'icon' => 'plumbing',
            'description' => 'خدمات السباكة المنزلية وتسريب المياه',
            'is_active' => true,
        ]);

        Service::create([
            'category_id' => $plumbingCategory->id,
            'name' => 'إصلاح تسريب مياه',
            'base_price' => 50.00,
            'is_fixed_price' => true,
            'description' => 'إصلاح تسريبات الأنابيب والصنابير والخلاطات',
            'is_active' => true,
        ]);

        $plumbingProvider = User::create([
            'name' => 'فني سباكة ديمو',
            'phone' => '0500000001',
            'email' => 'plumber@demo.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'is_active' => true,
        ]);

        $plumbingProfile = ProviderProfile::create([
            'user_id' => $plumbingProvider->id,
            'is_available' => true,
            'kyc_status' => 'approved',
            'max_travel_distance' => 25,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
        ]);

        DB::table('category_provider_profile')->insert([
            'category_id' => $plumbingCategory->id,
            'provider_profile_id' => $plumbingProfile->id,
        ]);

        // 2. Electrical (كهرباء)
        $electricCategory = Category::create([
            'name' => 'كهرباء',
            'icon' => 'electrical',
            'description' => 'إصلاح الأعطال الكهربائية وتمديدات الإنارة',
            'is_active' => true,
        ]);

        Service::create([
            'category_id' => $electricCategory->id,
            'name' => 'إصلاح أعطال كهربائية',
            'base_price' => 60.00,
            'is_fixed_price' => true,
            'description' => 'فحص القواطع وتمديد وصيانة الأفياش والإنارة',
            'is_active' => true,
        ]);

        $electricProvider = User::create([
            'name' => 'فني كهرباء ديمو',
            'phone' => '0500000003',
            'email' => 'electrician@demo.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'is_active' => true,
        ]);

        $electricProfile = ProviderProfile::create([
            'user_id' => $electricProvider->id,
            'is_available' => true,
            'kyc_status' => 'approved',
            'max_travel_distance' => 25,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
        ]);

        DB::table('category_provider_profile')->insert([
            'category_id' => $electricCategory->id,
            'provider_profile_id' => $electricProfile->id,
        ]);

        // 3. Air Conditioning (تكييف)
        $acCategory = Category::create([
            'name' => 'تكييف',
            'icon' => 'ac',
            'description' => 'صيانة وتنظيف مكيفات سبليت وشباك',
            'is_active' => true,
        ]);

        Service::create([
            'category_id' => $acCategory->id,
            'name' => 'صيانة وتنظيف مكيف',
            'base_price' => 80.00,
            'is_fixed_price' => true,
            'description' => 'تنظيف فلاتر، شحن فريون، وصيانة وحدات التبريد',
            'is_active' => true,
        ]);

        $acProvider = User::create([
            'name' => 'فني تكييف ديمو',
            'phone' => '0500000004',
            'email' => 'ac@demo.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'is_active' => true,
        ]);

        $acProfile = ProviderProfile::create([
            'user_id' => $acProvider->id,
            'is_available' => true,
            'kyc_status' => 'approved',
            'max_travel_distance' => 30,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
        ]);

        DB::table('category_provider_profile')->insert([
            'category_id' => $acCategory->id,
            'provider_profile_id' => $acProfile->id,
        ]);

        // 4. Painting (دهان)
        $paintCategory = Category::create([
            'name' => 'دهان',
            'icon' => 'painting',
            'description' => 'دهان وترميم الجدران الداخلية والخارجية',
            'is_active' => true,
        ]);

        Service::create([
            'category_id' => $paintCategory->id,
            'name' => 'دهان الجدران والترميم',
            'base_price' => 120.00,
            'is_fixed_price' => false,
            'description' => 'طلاء غرف، معالجة الرطوبة والتشققات وتجديد الألوان',
            'is_active' => true,
        ]);

        $paintProvider = User::create([
            'name' => 'فني دهان ديمو',
            'phone' => '0500000005',
            'email' => 'painter@demo.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'is_active' => true,
        ]);

        $paintProfile = ProviderProfile::create([
            'user_id' => $paintProvider->id,
            'is_available' => true,
            'kyc_status' => 'approved',
            'max_travel_distance' => 25,
            'latitude' => 24.7136,
            'longitude' => 46.6753,
        ]);

        DB::table('category_provider_profile')->insert([
            'category_id' => $paintCategory->id,
            'provider_profile_id' => $paintProfile->id,
        ]);

        // 5. Demo Customer User
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
