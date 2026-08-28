<?php
// models/user_model.php

function get_user_by_email($conn, $email) {
    // ইউজারের সব তথ্য নিয়ে আসছি
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    
    return $user; // ইউজার পেলে ডেটা দিবে, না পেলে null
}
?>