<?php
// models/trip_model.php
require_once __DIR__ . '/db_connect.php';
//require_once '../config/db.php';

function get_driver_trips_by_date($conn, $driver_id, $date) {
    // trips, buses এবং routes টেবিল জয়েন করে পূর্ণাঙ্গ ট্রিপ ডেটা ফেচ করা হচ্ছে
    $sql = "SELECT t.trip_id, t.trip_date, t.departure_time, t.status, 
                   b.bus_number, b.type AS bus_type, 
                   r.origin, r.destination
            FROM trips t
            JOIN buses b ON t.bus_id = b.bus_id
            JOIN routes r ON t.route_id = r.route_id
            WHERE t.driver_id = ? AND t.trip_date = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return false; // কুয়েরি ফেইল করলে false রিটার্ন করবে
    }

    // i = integer (driver_id), s = string (date)
    mysqli_stmt_bind_param($stmt, "is", $driver_id, $date);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    // একাধিক ট্রিপ থাকতে পারে, তাই লুপ চালিয়ে অ্যারে তৈরি করতে হবে
    $trips = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $trips[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    
    return $trips; // ফাঁকা হলেও অ্যারে রিটার্ন করবে
}
?>