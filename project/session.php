<?php
// helpers/session.php
session_start();
require_once __DIR__ . '/db_connect.php';
//require_once '../config/db_connect.php'; // ডাটাবেস কানেকশন

// যদি সেশনে লগইন না থাকে, কিন্তু ব্রাউজারে 'user_email' নামের কুকি থাকে
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_email'])) {
    $cookie_email = $_COOKIE['user_email'];

    // ডাটাবেস থেকে কুকির ইমেইল দিয়ে ইউজার খুঁজি
    $sql = "SELECT user_id, name, role FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $cookie_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // ইউজার পেলে সেশন সেট করে দিই
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
    }
    mysqli_stmt_close($stmt);
}
?>