<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء التصنيفات والخدمات (أكثر من 15 خدمة متنوعة)
        $categories = [
            ['name' => 'سباكة', 'icon' => 'plumbing', 'description' => 'إصلاح تسريبات، تركيب أدوات صحية، صيانة الأنابيب'],
            ['name' => 'كهرباء', 'icon' => 'electrical', 'description' => 'تمديدات كهربائية، صيانة لوحات، تركيب إضاءة'],
            ['name' => 'تكييف', 'icon' => 'ac', 'description' => 'غسيل مكيفات، تعبئة فريون، صيانة أعطال التبريد'],
            ['name' => 'دهان', 'icon' => 'painting', 'description' => 'دهان داخلي وخارجي، معالجة رطوبة، تركيب ورق جدران'],
            ['name' => 'تنظيف', 'icon' => 'cleaning', 'description' => 'تنظيف منازل، غسيل سجاد، تنظيف واجهات'],
            ['name' => 'نجارة', 'icon' => 'carpentry', 'description' => 'صيانة أبواب، تركيب أثاث، تفصيل خزائن'],
            ['name' => 'مكافحة حشرات', 'icon' => 'pest_control', 'description' => 'رش مبيدات، مكافحة قوارض، تعقيم'],
            ['name' => 'صيانة أجهزة', 'icon' => 'appliances', 'description' => 'تصليح غسالات، ثلاجات، أفران، مايكروويف'],
            ['name' => 'نقل عفش', 'icon' => 'moving', 'description' => 'فك وتركيب، تغليف، نقل سيارات مجهزة'],
            ['name' => 'كاميرات وشبكات', 'icon' => 'cctv', 'description' => 'تركيب كاميرات مراقبة، شبكات إنترنت، سنترال'],
            ['name' => 'تصميم حدائق', 'icon' => 'landscaping', 'description' => 'تنسيق حدائق، تركيب عشب صناعي، شبكات ري'],
            ['name' => 'جبس وديكور', 'icon' => 'gypsum', 'description' => 'تركيب جبس بورد، أسقف معلقة، ديكورات إضاءة'],
            ['name' => 'زجاج وألومنيوم', 'icon' => 'glass', 'description' => 'تفصيل شبابيك ألومنيوم، كبائن شاور، واجهات زجاجية'],
            ['name' => 'تلميع سيارات', 'icon' => 'car_wash', 'description' => 'غسيل متنقل، تلميع ساطع، نانو سيراميك'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 2. إنشاء فنيين محترفين (Demo Providers)
        $providers = [
            ['phone' => '0500000001', 'name' => 'أحمد للسباكة المتقدمة', 'category' => 'سباكة'],
            ['phone' => '0500000002', 'name' => 'مؤسسة النور للكهرباء', 'category' => 'كهرباء'],
            ['phone' => '0500000003', 'name' => 'خبراء التكييف المركزي', 'category' => 'تكييف'],
            ['phone' => '0500000004', 'name' => 'لمسة إبداع للدهانات', 'category' => 'دهان'],
            ['phone' => '0500000005', 'name' => 'النظافة الماسية', 'category' => 'تنظيف'],
            ['phone' => '0500000006', 'name' => 'ورشة الأخشاب الفاخرة', 'category' => 'نجارة'],
            ['phone' => '0500000007', 'name' => 'الدرع لمكافحة الحشرات', 'category' => 'مكافحة حشرات'],
            ['phone' => '0500000008', 'name' => 'المهندس لصيانة الأجهزة', 'category' => 'صيانة أجهزة'],
            ['phone' => '0500000009', 'name' => 'السريع لنقل العفش', 'category' => 'نقل عفش'],
            ['phone' => '0500000010', 'name' => 'الرؤية الذكية للكاميرات', 'category' => 'كاميرات وشبكات'],
        ];

        foreach ($providers as $prov) {
            $category = Category::where('name', $prov['category'])->first();
            if ($category) {
                Provider::firstOrCreate(
                    ['phone' => $prov['phone']],
                    [
                        'name' => $prov['name'],
                        'category_id' => $category->id,
                        'rating' => rand(40, 50) / 10,
                        'reviews_count' => rand(15, 200),
                        'is_available' => true,
                        // جعل النطاق عالمي دائمًا للديمو
                        'max_travel_distance' => 20000,
                        'base_location_lat' => 24.7136,
                        'base_location_lng' => 46.6753,
                        'hourly_rate' => rand(50, 150),
                    ]
                );
            }
        }
    }
}