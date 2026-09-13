<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Service;
use App\Models\ProviderProfile;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Clean Database
DB::statement('DELETE FROM reviews');
DB::statement('DELETE FROM service_requests');
DB::statement('DELETE FROM category_provider_profile');
DB::statement('DELETE FROM provider_profiles');
DB::statement('DELETE FROM users');
DB::statement('DELETE FROM personal_access_tokens');

// 1. Setup Customer
$customer = User::create(['name' => 'Test Customer', 'email' => 'c1@test.com', 'password' => bcrypt('123'), 'phone' => '0501111111', 'role' => 'customer']);
$customerToken = $customer->createToken('test')->plainTextToken;

// 2. Setup Provider
$provider = User::create(['name' => 'Test Provider', 'email' => 'p1@test.com', 'password' => bcrypt('123'), 'phone' => '0502222222', 'role' => 'provider']);
$providerToken = $provider->createToken('test')->plainTextToken;

$service = Service::first();

// Provider Profile setup (KYC approved)
$profile = ProviderProfile::create([
    'user_id' => $provider->id,
    'kyc_status' => 'approved',
    'is_available' => true,
    'latitude' => 24.7136,
    'longitude' => 46.6753, // Riyadh center
    'max_travel_distance' => 50,
]);
$profile->categories()->attach($service->category_id);

echo "--- STEP 1: Registration Done ---\n";
echo "Customer Token: $customerToken\n";
echo "Provider Token: $providerToken\n\n";

use Illuminate\Support\Facades\Http;

function makeRequest($method, $uri, $token, $data = []) {
    $url = "http://127.0.0.1:8000" . $uri;
    $response = Http::withToken($token)
        ->acceptJson();
        
    if ($method === 'POST') {
        $response = $response->post($url, $data);
    } elseif ($method === 'GET') {
        $response = $response->get($url, $data);
    }
    
    $json = $response->json();
    $msg = isset($json['message']) ? $json['message'] : json_encode($json, JSON_UNESCAPED_UNICODE);
    if (isset($json['tracking_visible'])) {
        $msg .= " | Tracking Visible: " . ($json['tracking_visible'] ? 'Yes' : 'No');
    }
    echo "Response: " . $msg . "\n\n";
    return $json;
}

// 3. Customer Requests Service (Near provider)
echo "--- STEP 2: Customer Creates Request ---\n";
$reqData = [
    'service_id' => $service->id,
    'description' => 'Need AC repair',
    'latitude' => 24.7140, // Very close
    'longitude' => 46.6760,
    'address' => 'Olaya, Riyadh',
];
$resp1 = makeRequest('POST', '/api/service-requests', $customerToken, $reqData);
$requestId = $resp1['data']['id'];

// 4. Provider Accepts Service
echo "--- STEP 3: Provider Accepts Request ---\n";
makeRequest('POST', "/api/provider/requests/{$requestId}/accept", $providerToken);

// 5. Customer Tries to Track (Should fail - Not paid yet)
echo "--- STEP 4: Customer Tries Tracking BEFORE Payment ---\n";
makeRequest('GET', "/api/service-requests/{$requestId}/track", $customerToken);

// 6. Customer Pays
echo "--- STEP 5: Customer Authorizes Payment ---\n";
$payData = ['service_request_id' => $requestId, 'source_id' => 'token_success_123', 'payment_id' => 'pay_abc123'];
makeRequest('POST', "/api/payments/authorize", $customerToken, $payData);

// 7. Customer Tries to Track again (Should succeed)
echo "--- STEP 6: Customer Tries Tracking AFTER Payment ---\n";
makeRequest('GET', "/api/service-requests/{$requestId}/track", $customerToken);

// 8. Provider Completes Service
echo "--- STEP 7: Provider Completes Service ---\n";
makeRequest('POST', "/api/provider/requests/{$requestId}/complete", $providerToken);

// 9. Customer Reviews
echo "--- STEP 8: Customer Reviews Service ---\n";
$revData = ['service_request_id' => $requestId, 'rating' => 5, 'comment' => 'Excellent work!'];
makeRequest('POST', "/api/reviews", $customerToken, $revData);

echo "--- FULL LIFECYCLE COMPLETED SUCCESSFULLY ---\n";

