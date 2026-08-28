<?php
// controllers/driver_trip_controller.php

require_once __DIR__ . '/session.php'; // সিকিউরিটির জন্য[cite: 1]
require_once __DIR__ . '/trip_model.php';

// require_once '../helpers/session.php'; // সিকিউরিটির জন্য[cite: 1]
// require_once '../models/trip_model.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    // URL থেকে parameter গুলো রিসিভ করা (যেমন: ?id=5&date=2026-08-29)[cite: 1]
    $driver_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // যদি ফ্রন্টএন্ড থেকে ডেট না পাঠায়, তবে আজকের ডেট ডিফল্ট হিসেবে ধরবে
    $date = isset($_GET['date']) && !empty($_GET['date']) ? trim($_GET['date']) : date('Y-m-d'); 

    if ($driver_id === 0) {
        echo json_encode(["status" => "error", "message" => "Valid Driver ID is required."]);
        exit;
    }

    global $conn;
    $trips = get_driver_trips_by_date($conn, $driver_id, $date);

    if ($trips !== false) {
        // ডেটা পেলে ফ্রন্টএন্ডে পাঠিয়ে দিবে
        echo json_encode([
            "status" => "success",
            "date" => $date,
            "total_trips" => count($trips),
            "data" => $trips
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database query failed."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Must be GET."]);
}
?>