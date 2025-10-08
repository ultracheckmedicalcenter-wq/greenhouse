<?php
require_once __DIR__ . '/../config.php';

// receive JSON from ESP32
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "No input"]);
    exit;
}

$temp = $data['temp'];
$hum = $data['humidity'];
$soil = $data['soil_moisture'];
$light = $data['light_intensity'];

// 1. Save sensor reading
$stmt = $pdo->prepare("INSERT INTO sensor_readings (temp, humidity, soil_moisture, light_intensity) VALUES (?,?,?,?)");
$stmt->execute([$temp, $hum, $soil, $light]);

// 2. Run KNN decision
require_once __DIR__ . '/../knn.php';
$cmd = knn_decision($temp, $hum, $soil, $light);

// 3. Save command
$stmt = $pdo->prepare("INSERT INTO commands (heater, fan, pump, light_act, source) VALUES (?,?,?,?,?)");
$stmt->execute([$cmd['heater'], $cmd['fan'], $cmd['pump'], $cmd['light_act'], "knn"]);

// 4. Respond to ESP32
echo json_encode(["status" => "ok", "cmd" => $cmd]);
