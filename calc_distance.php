<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$faces = \App\Models\SystemAuthFaceData::all();
$a = $faces[0]->descriptor;
$b = $faces[1]->descriptor;

if (is_string($a)) $a = json_decode($a, true);
if (is_string($b)) $b = json_decode($b, true);

$sum = 0;
for ($i = 0; $i < 128; $i++) {
    $sum += pow($a[$i] - $b[$i], 2);
}
echo "Distance: " . sqrt($sum) . "\n";
