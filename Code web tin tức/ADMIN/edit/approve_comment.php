<?php
session_start();
require '../../DATABASE/connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 0 || !isset($_GET['id'])) {
    header("Location: ../HTML/login.html");
    exit();
}

$comment_id = (int)$_GET['id'];
$query = "UPDATE Danhgia SET TrangThai = 1 WHERE DanhgiaID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $comment_id);

if ($stmt->execute()) {
    header("Location: ../comment.php?message=Duyệt thành công"); 
} else {
    echo "Lỗi: " . $conn->error;
}
?>
