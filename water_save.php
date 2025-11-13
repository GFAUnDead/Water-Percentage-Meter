<?php
// Configuration
define('TIMEZONE', 'Australia/Sydney');
define('STORAGE_FILE', __DIR__ . '/water_state.json');
define('MAX_PERCENT', 1000);
define('MIN_PERCENT', 0);
define('DATE_FORMAT', 'Y-m-d');

// Set timezone and response headers
date_default_timezone_set(TIMEZONE);
header('Content-Type: application/json');

function load_store($path) {
    if (!file_exists($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function save_store($path, $store) {
    $json = json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }
    $result = file_put_contents($path, $json, LOCK_EX);
    return $result !== false;
}

function validate_percent($value) {
    // Check if value is set
    if (!isset($value)) {
        return [
            'valid' => false,
            'error' => 'Percentage value is required',
            'value' => null,
        ];
    }
    // Convert to integer
    $percent = (int)$value;
    // Check bounds
    if ($percent < MIN_PERCENT || $percent > MAX_PERCENT) {
        return [
            'valid' => false,
            'error' => "Percentage must be between " . MIN_PERCENT . " and " . MAX_PERCENT,
            'value' => null,
        ];
    }
    return [
        'valid' => true,
        'error' => null,
        'value' => $percent,
    ];
}

function handle_get() {
    $store = load_store(STORAGE_FILE);
    $today = date(DATE_FORMAT);
    if (isset($_GET['all']) && $_GET['all'] === 'true') {
        // Return all historical records
        echo json_encode([
            'success' => true,
            'records' => $store,
        ]);
    } else {
        // Return today's record
        $percent = array_key_exists($today, $store) ? $store[$today] : null;
        echo json_encode([
            'success' => true,
            'date' => $today,
            'percent' => $percent,
            'current_date' => $today,
        ]);
    }
}

function handle_post() {
    $input = json_decode(file_get_contents('php://input'), true);
    $today = date(DATE_FORMAT);
    // Validate input is an array
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Request body must be valid JSON',
        ]);
        return;
    }
    // Validate percent value
    $validation = validate_percent($input['percent'] ?? null);
    if (!$validation['valid']) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $validation['error'],
        ]);
        return;
    }
    $percent = $validation['value'];
    // Load existing store
    $store = load_store(STORAGE_FILE);
    $store[$today] = $percent;
    // Attempt to save
    if (!save_store(STORAGE_FILE, $store)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to persist data to storage',
        ]);
        return;
    }
    // Success response
    echo json_encode([
        'success' => true,
        'date' => $today,
        'percent' => $percent,
    ]);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method === 'GET') {
        handle_get();
    } elseif ($method === 'POST') {
        handle_post();
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Method not allowed. Use GET or POST.',
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An unexpected error occurred',
    ]);
}
?>