<?php

use Illuminate\Support\Facades\Route;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;


// routes/web.php
use App\Libraries\php_zklib\CustomZKLib;

Route::get('/test-zkteco', function () {
    $ip = '103.121.25.4';
    $port = 4369; // Try 4369 if 4370 fails
    $comm_key = 1122;
    $zk = new CustomZKLib($ip, $port, $comm_key);
    try {
        if ($zk->connect()) {
            $zk->disconnect();
            return response()->json(['status' => 'success', 'message' => 'Connected to ZKTeco device']);
        }
        return response()->json(['status' => 'error', 'message' => 'Failed to connect']);
    } catch (\Exception $e) {
        \Log::error("ZKTeco Test Error: " . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}); // Secure the route

Route::get('/zkteco-test', function () {
    $ip = '103.121.25.4';
    $port = 4369; // Try 4369 if 4370 fails
    $comm_key = 1122; // Set to 0 if cleared on device

    try {
        $scriptPath = str_replace('/', '\\', storage_path('app\scripts\zkteco.py'));
        $command = "python \"$scriptPath\" $ip $port $comm_key";
        Log::debug('Executing Python script', ['command' => $command]);

        $output = shell_exec($command);
        if ($output === null) {
            Log::error('Python script execution failed', ['command' => $command]);
            return response()->json(['status' => 'error', 'message' => 'Failed to execute Python script'], 500);
        }

        $result = json_decode($output, true);
        if (!$result || !is_array($result) || $result['status'] === 'error') {
            Log::error('PyZK error', ['error' => $result['message'] ?? 'Invalid response']);
            return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'PyZK failed'], 500);
        }

        return response()->json($result);
    } catch (Exception $e) {
        Log::error('ZKTeco Python execution error', ['error' => $e->getMessage()]);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});