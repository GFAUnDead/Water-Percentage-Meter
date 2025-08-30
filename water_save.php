<?php
date_default_timezone_set('Australia/Sydney');
header('Content-Type: application/json');
$path = __DIR__ . '/water_state.json';

// Helper: load existing store (map of date => percent)
function load_store($path){
    if (!file_exists($path)) return [];
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// Helper: persist store
function save_store($path, $store){
    $json = json_encode($store, JSON_PRETTY_PRINT);
    if ($json === false) return false;
    return file_put_contents($path, $json, LOCK_EX) !== false;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$today = date('Y-m-d');

if ($method === 'GET'){
    $store = load_store($path);
    $percent = array_key_exists($today, $store) ? $store[$today] : null;
    echo json_encode(['date' => $today, 'percent' => $percent]);
    exit;
}

if ($method === 'POST'){
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || !isset($input['percent'])){
        http_response_code(400);
        echo json_encode(['error' => 'missing percent']);
        exit;
    }
    $percent = (int)$input['percent'];
    if ($percent < 0 || $percent > 100){
        http_response_code(400);
        echo json_encode(['error' => 'percent out of range']);
        exit;
    }
    $store = load_store($path);
    $store[$today] = $percent;
    if (!save_store($path, $store)){
        http_response_code(500);
        echo json_encode(['error' => 'failed to write']);
        exit;
    }
    echo json_encode(['ok' => true, 'date' => $today, 'percent' => $percent]);
    exit;
}

// Fallback
http_response_code(405);
echo json_encode(['error' => 'method not allowed']);
