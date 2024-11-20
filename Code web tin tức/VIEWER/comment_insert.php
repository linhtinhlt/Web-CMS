<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../DATABASE/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && !empty($_POST['comment'])) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../HTML/login.html");  
        exit;
    }

    $comment = htmlspecialchars($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? 1; 
    $baiviet_id = $_POST['baiviet_id'];

    $insert_comment_query = "INSERT INTO Danhgia (BaivietID, NguoidungID, Binhluan, NgayBL) 
                             VALUES (?, ?, ?, NOW())";
    $insert_comment_stmt = $conn->prepare($insert_comment_query);
    $insert_comment_stmt->bind_param("iis", $baiviet_id, $user_id, $comment);
    $insert_comment_stmt->execute();

    $_SESSION['comment_success'] = true;

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>

