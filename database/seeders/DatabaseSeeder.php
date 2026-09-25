<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Models\ProviderProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. التصنيفات والخدمات
        $catalog = [
            [
                'name' => 'سباكة',
                'icon' => 'plumbing',
                'description' => 'صيانة شبكات المياه، معالجة التسريبات، وتركيب الأدوات الصحية',
                'services' => [
                    ['name' => 'كشف وإصلاح تسريب مياه', 'price' => 120.00, 'desc' => 'فحص المواسير الداخلية ومعالجة التسريبات والكسور'],
                    ['name' => 'تركيب وصيانة أدوات صحية', 'price' => 80.00, 'desc' => 'تركيب خلاطات، مغاسل، ومراحيض جديدة'],
                    ['name' => 'تسليك مجاري وصرف صحي', 'price' => 150.00, 'desc' => 'تنظيف وتسليك خطوط الصرف بأحدث الأجهزة'],
                ],
                'provider' => ['phone' => '0500000001', 'name' => 'فني سباكة معتمد', 'email' => 'plumber@demo.com']
            ],
            [
                'name' => 'كهرباء',
                'icon' => 'electrical',
                'description' => 'صيانة وتمديد الشبكات الكهربائية وتركيب الإضاءة والأجهزة',
                'services' => [
                    ['name' => 'إصلاح قواطع وأعطال كهربائية', 'price' => 90.00, 'desc' => 'فحص لوحة التوزيع والفيوزات وحل مشكلة الشورت'],
                    ['name' => 'تركيب إنارة وسبوت لايت', 'price' => 70.00, 'desc' => 'تركيب نجف، إضاءة ليد، ومفاتيح ذكية'],
                    ['name' => 'تمديدات كهربائية جديدة', 'price' => 180.00, 'desc' => 'تمديد أسلاك ونقاط كهرباء جديدة للغرف والمطابخ'],
                ],
                'provider' => ['phone' => '0500000003', 'name' => 'مهندس كهرباء ديمو', 'email' => 'electrician@demo.com']
            ],
            [
                'name' => 'تكييف',
                'icon' => 'ac',
                'description' => 'صيانة وتنظيف وشحن فريون لمكيفات الاسبليت والمركزي',
                'services' => [
                    ['name' => 'غسيل وتنظيف مكيف سبليت', 'price' => 85.00, 'desc' => 'تنظيف الوحدة الداخلية والخارجية وإزالة الروائح'],
                    ['name' => 'تعبئة وشحن فريون أمريكي', 'price' => 130.00, 'desc' => 'فحص ضغط الغاز وتعبئة الفريون بكفاءة عالية'],
                    ['name' => 'صيانة وتصليح أعطال التبريد', 'price' => 110.00, 'desc' => 'إصلاح الكمبروسر، تسريب الماء، وتغيير القطع التالفة'],
                ],
                'provider' => ['phone' => '0500000004', 'name' => 'خبير تكييف وتبريد', 'email' => 'ac@demo.com']
            ],
            [
                'name' => 'دهان',
                'icon' => 'painting',
                'description' => 'دهان داخلي وخارجي، ترميم الحوائط، وورق الجدران',
                'services' => [
                    ['name' => 'دهان غرف وجدران داخلية', 'price' => 150.00, 'desc' => 'دهان احترافي مع الصنفرة والمعجون وتأسيس ممتاز'],
                    ['name' => 'معالجة رطوبة وتشققات الجدران', 'price' => 130.00, 'desc' => 'عزل الرطوبة وسد الشقوق قبل إعادة الطلاء'],
                    ['name' => 'تركيب ورق جدران وبديل خشب', 'price' => 120.00, 'desc' => 'تركيب ديكورات جدارية عصرية بدقة متناهية'],
                ],
                'provider' => ['phone' => '0500000005', 'name' => 'فني دهانات وديكور', 'email' => 'painter@demo.com']
            ],
            [
                'name' => 'تنظيف',
                'icon' => 'cleaning',
                'description' => 'نظافة شاملة للمنازل والفلل وغسيل السجاد والكنب',
                'services' => [
                    ['name' => 'تنظيف منازل وشقق شامل', 'price' => 200.00, 'desc' => 'تنظيف عميق للأرضيات، المطابخ، والحمامات بالبخار'],
                    ['name' => 'غسيل سجاد وكنب بالبخار', 'price' => 100.00, 'desc' => 'إزالة أصعب البقع والتعقيم الفوري بأجهزة البخار'],
                ],
                'provider' => ['phone' => '0500000006', 'name' => 'مؤسسة النظافة الاحترافية', 'email' => 'cleaning@demo.com']
            ],
            [
                'name' => 'نجارة',
                'icon' => 'carpentry',
                'description' => 'تركيب وصيانة الأبواب والأثاث والمطابخ الخشبية',
                'services' => [
                    ['name' => 'صيانة وتبديل كوالين الأبواب', 'price' => 60.00, 'desc' => 'تركيب أقفال ذكية وكوالين جديدة للأبواب'],
                    ['name' => 'تركيب غرف نوم وأثاث إيكيا', 'price' => 140.00, 'desc' => 'تجميع وتركيب الدواليب، الأسرة، والمكاتب'],
                ],
                'provider' => ['phone' => '0500000007', 'name' => 'نجار خبير صيانة أثاث', 'email' => 'carpenter@demo.com']
            ],
            [
                'name' => 'مكافحة حشرات',
                'icon' => 'pest_control',
                'description' => 'رش مبيدات آمنة ومكافحة القوارض والآفات بضمان رسمي',
                'services' => [
                    ['name' => 'رش مبيدات حشرية للمنازل', 'price' => 160.00, 'desc' => 'مبيدات ألمانية بدون رائحة مع ضمان 6 أشهر'],
                    ['name' => 'مكافحة قوارض وصراصير', 'price' => 140.00, 'desc' => 'وضع طعوم وجل آمن وفعال للقضاء على الآفات'],
                ],
                'provider' => ['phone' => '0500000008', 'name' => 'الدرع لمكافحة الآفات', 'email' => 'pest@demo.com']
            ],
            [
                'name' => 'صيانة أجهزة',
                'icon' => 'appliances',
                'description' => 'تصليح غسالات، ثلاجات، أفران، وميكروويف فورياً',
                'services' => [
                    ['name' => 'تصليح غسالات أوتوماتيك', 'price' => 95.00, 'desc' => 'فحص الموتور وطلمبة الطرد وتبديل القطع الأصلية'],
                    ['name' => 'صيانة ثلاجات وفريزر', 'price' => 110.00, 'desc' => 'حل مشاكل ضعف التبريد وتسريب الفريون والتايمر'],
                ],
                'provider' => ['phone' => '0500000009', 'name' => 'المهندس لصيانة الأجهزة', 'email' => 'appliances@demo.com']
            ],
            [
                'name' => 'نقل عفش',
                'icon' => 'moving',
                'description' => 'نقل وتغليف الأثاث بسيارات دينا مقفلة مع فك وتركيب',
                'services' => [
                    ['name' => 'نقل عفش مع الفك والتركيب', 'price' => 300.00, 'desc' => 'عمالة مدربة وسيارات مجهزة مع ضمان سلامة العفش'],
                    ['name' => 'تغليف أثاث ومقتنيات ثمينة', 'price' => 120.00, 'desc' => 'تغليف احترافي بنايلون وفقاعات وكراتين مقواة'],
                ],
                'provider' => ['phone' => '0500000010', 'name' => 'السريع لنقل الأثاث', 'email' => 'moving@demo.com']
            ],
            [
                'name' => 'كاميرات وشبكات',
                'icon' => 'cctv',
                'description' => 'تركيب كاميرات مراقبة، شبكات واي فاي، وسنترال ذكي',
                'services' => [
                    ['name' => 'تركيب كاميرات مراقبة منزلية', 'price' => 180.00, 'desc' => 'تمديد وبرمجة كاميرات IP وربطها بالجوال عن بعد'],
                    ['name' => 'تقوية شبكات الواي فاي والإنترنت', 'price' => 100.00, 'desc' => 'تركيب مقويات Access Point وتغطية كامل المنزل'],
                ],
                'provider' => ['phone' => '0500000011', 'name' => 'الرؤية الذكية للأنظمة', 'email' => 'cctv@demo.com']
            ],
        ];

        foreach ($catalog as $item) {
            // إنشاء أو تحديث التصنيف
            $cat = Category::firstOrCreate(
                ['name' => $item['name']],
                [
                    'icon' => $item['icon'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );
            $cat->update(['is_active' => true]);

            // إنشاء الخدمات التابعة للتصنيف
            foreach ($item['services'] as $srv) {
                Service::firstOrCreate(
                    ['name' => $srv['name'], 'category_id' => $cat->id],
                    [
                        'base_price' => $srv['price'],
                        'is_fixed_price' => true,
                        'description' => $srv['desc'],
                        'is_active' => true,
                    ]
                );
            }

            // إنشاء الفني المزود للخدمة
            $provData = $item['provider'];
            $user = User::firstOrCreate(
                ['phone' => $provData['phone']],
                [
                    'name' => $provData['name'],
                    'email' => $provData['email'],
                    'password' => Hash::make('password'),
                    'role' => 'provider',
                    'is_active' => true,
                ]
            );

            $profile = ProviderProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'is_available' => true,
                    'kyc_status' => 'approved',
                    'max_travel_distance' => 20000,
                    'latitude' => 24.7136,
                    'longitude' => 46.6753,
                ]
            );
            $profile->update([
                'is_available' => true,
                'max_travel_distance' => 20000,
                'kyc_status' => 'approved',
            ]);

            // ربط الفني بالتصنيف في جدول pivot
            DB::table('category_provider_profile')->updateOrInsert(
                ['category_id' => $cat->id, 'provider_profile_id' => $profile->id],
                []
            );
        }

        // مستخدم الزبون التجريبي
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