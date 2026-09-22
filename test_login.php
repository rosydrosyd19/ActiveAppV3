<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = microtime(true);
$user = App\Models\User::where('email', 'admin@erp.com')->first();
echo "Find User: " . round((microtime(true) - $start) * 1000) . " ms\n";

$start = microtime(true);
$isValid = Hash::check('password123', $user->password);
echo "Hash Check: " . round((microtime(true) - $start) * 1000) . " ms\n";

$start = microtime(true);
Auth::login($user);
echo "Auth Login: " . round((microtime(true) - $start) * 1000) . " ms\n";
