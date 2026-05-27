<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\Auth;

$user = App\Models\User::first(); // Assuming admin
\Illuminate\Support\Facades\Auth::login($user);

$laporan = App\Models\Laporan::create([
    'user_id' => $user->id,
    'jenis_bencana' => 'banjir',
    'latitude' => -6.2,
    'longitude' => 106.8,
    'wilayah' => 'Jakarta',
    'deskripsi' => 'Test test',
    'status' => 'pending',
]);

$request = Illuminate\Http\Request::create('/admin/laporan/'.$laporan->id, 'DELETE');
$request->setLaravelSession($app['session']->driver());
$app->instance('request', $request);

$controller = $app->make(App\Http\Controllers\AdminController::class);
try {
    $response = $controller->destroyLaporan($request, $laporan);
    echo "Success: " . get_class($response) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
