<?php 
session_start(); 
require '../../DATABASE/connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 0 || !isset($_GET['id'])) {
    header("Location: ../HTML/login.html");
    exit();
}

$comment_id = (int)$_GET['id'];


$query = "DELETE FROM Danhgia WHERE DanhgiaID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $comment_id);

if ($stmt->execute()) {
    header("Location: ../comment.php?message=Xóa thành công"); // Chuyển hướng với thông báo
} else {
    echo "Lỗi: " . $conn->error;
}
?>
