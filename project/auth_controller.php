<?php
// controllers/auth_controller.php

require_once __DIR__ .'/session.php'; // সেশন শুরু করার জন্য
require_once __DIR__ .'/user_model.php';

// require_once '../helpers/session.php'; // সেশন শুরু করার জন্য
// require_once '../models/user_model.php';

// শুধু JSON রেসপন্স দেওয়ার জন্য
header('Content-Type: application/json');

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = isset($_POST["email"]) ? cleanInput($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $remember = isset($_POST["remember"]) ? 1 : 0;

    // ১. সাধারণ ভ্যালিডেশন
    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Email and password are required"]);
        exit;
    }

    // ২. মডেল কল করে ডাটাবেস থেকে ইউজার আনা
    global $conn;
    $user = get_user_by_email($conn, $email);

    if ($user) {
        // ৩. পাসওয়ার্ড মেলানো (Hashed Password Verification)
        if (password_verify($password, $user['password'])) {

            // ৪. সেশন সেট করা[cite: 1]
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // ৫. Remember me কুকি সেট করা[cite: 1] (৩০ দিনের জন্য)
            if ($remember === 1) {
                setcookie("user_email", $user['email'], time() + (86400 * 30), "/"); // 86400 = 1 day
            }

            // ৬. ড্যাশবোর্ডের লিংক ঠিক করা
            $redirect_url = "";
            if ($user['role'] === 'admin') {
                $redirect_url = "/views/admin/dashboard.html";
            } elseif ($user['role'] === 'driver') {
                $redirect_url = "/views/driver/dashboard.html";
            } elseif ($user['role'] === 'manager') {
                $redirect_url = "/views/manager/dashboard.html";
            } else {
                $redirect_url = "/views/passenger/home.html";
            }

            // ৭. ফ্রন্টএন্ডে সাকসেস মেসেজ পাঠানো
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "role" => $user['role'],
                "redirect" => $redirect_url
            ]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect password"]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No account found with this email"]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
