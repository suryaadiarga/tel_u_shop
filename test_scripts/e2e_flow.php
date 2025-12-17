<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Str;

$now = time();
$email = "e2e_{$now}@example.com";
$password = 'Secret123!';

$state = [
    'cookies' => [],
    'token' => null,
];

function buildCookieHeader(array $cookies)
{
    $pairs = [];
    foreach ($cookies as $c) {
        $pairs[] = $c->getName() . '=' . $c->getValue();
    }
    return implode('; ', $pairs);
}

// Register
$registerPayload = [
    'name' => 'E2E User',
    'email' => $email,
    'password' => $password,
    'password_confirmation' => $password,
];
$req = Request::create('/api/register', 'POST', $registerPayload);
$req->headers->set('Accept', 'application/json');
$res = $kernel->handle($req);
echo "REGISTER: " . $res->getContent() . "\n";
$kernel->terminate($req, $res);
$body = json_decode($res->getContent(), true);
if (isset($body['token'])) {
    $state['token'] = $body['token'];
}

// Login (ensure token)
$req = Request::create('/api/login', 'POST', ['email' => $email, 'password' => $password]);
$req->headers->set('Accept', 'application/json');
$res = $kernel->handle($req);
echo "LOGIN: " . $res->getContent() . "\n";
$kernel->terminate($req, $res);
$body = json_decode($res->getContent(), true);
if (isset($body['token'])) $state['token'] = $body['token'];

$authHeader = $state['token'] ? ('Bearer ' . $state['token']) : null;

// 1) List products via web home (no auth required)
$req = Request::create('/', 'GET');
$req->headers->set('Accept', 'application/json');
$res = $kernel->handle($req);
$home = json_decode($res->getContent(), true);
$products = $home['products'] ?? [];

// If no products, create one directly in DB
$db = $app->make('db');
if (empty($products)) {
    echo "No products found, inserting test product into DB...\n";
    $id = $db->table('products')->insertGetId([
        'merchant_id' => 1,
        'name' => 'E2E Product',
        'price' => 15000,
        'stock' => 10,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $productId = $id;
    echo "Inserted product id={$productId}\n";
} else {
    $productId = $products[0]['id'];
    echo "Found product id={$productId}\n";
}

// 2) Add to cart (this will create a session cookie)
$req = Request::create('/api/cart/add/' . $productId, 'POST', ['qty' => 1, 'price' => 15000]);
$req->headers->set('Accept', 'application/json');
if ($authHeader) $req->headers->set('Authorization', $authHeader);
$res = $kernel->handle($req);
echo "ADD TO CART: " . $res->getContent() . "\n";
// capture cookies
$cookies = $res->headers->getCookies();
$state['cookies'] = $cookies;
$cookieHeader = buildCookieHeader($cookies);
$kernel->terminate($req, $res);

// 3) View cart with cookie + auth
$req = Request::create('/api/cart', 'GET');
$req->headers->set('Accept', 'application/json');
if ($authHeader) $req->headers->set('Authorization', $authHeader);
if ($cookieHeader) $req->headers->set('Cookie', $cookieHeader);
$res = $kernel->handle($req);
echo "VIEW CART: " . $res->getContent() . "\n";
$kernel->terminate($req, $res);

// 4) Top up wallet so checkout will succeed
$req = Request::create('/api/wallet/topup', 'POST', ['amount' => 50000]);
$req->headers->set('Accept', 'application/json');
if ($authHeader) $req->headers->set('Authorization', $authHeader);
$res = $kernel->handle($req);
echo "TOPUP: " . $res->getContent() . "\n";
$kernel->terminate($req, $res);

// 5) Checkout (uses session cookie)
$req = Request::create('/api/checkout', 'POST');
$req->headers->set('Accept', 'application/json');
if ($authHeader) $req->headers->set('Authorization', $authHeader);
if ($cookieHeader) $req->headers->set('Cookie', $cookieHeader);
$res = $kernel->handle($req);
echo "CHECKOUT: " . $res->getContent() . "\n";
$kernel->terminate($req, $res);

// 6) Get wallet transactions
$req = Request::create('/api/wallet/transactions', 'GET');
$req->headers->set('Accept', 'application/json');
if ($authHeader) $req->headers->set('Authorization', $authHeader);
$res = $kernel->handle($req);
echo "WALLET TRANSACTIONS: " . $res->getContent() . "\n";
$kernel->terminate($req, $res);

// 7) Query orders for user
$user = $db->table('users')->where('email', $email)->first();
if ($user) {
    $orders = $db->table('orders')->where('user_id', $user->id)->get();
    echo "ORDERS IN DB for user {$user->id}: " . json_encode($orders) . "\n";
}

// 8) Activities (if any)
$activities = $db->table('activities')->where('user_id', $user->id)->get();
echo "ACTIVITIES: " . json_encode($activities) . "\n";

echo "E2E flow complete.\n";
