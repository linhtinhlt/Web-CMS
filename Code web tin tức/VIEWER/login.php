<?php
require '../DATABASE/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $sql = "SELECT NguoidungID, Password, TrangthaiTK FROM Nguoidung WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($password === $user['Password']) {
            session_start();
            $_SESSION['user_id'] = $user['NguoidungID'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['TrangthaiTK'];

            if ($user['TrangthaiTK'] == 0) {
                header("Location: ../ADMIN/dashboard.php");
            } else {
                header("Location: ../VIEWER/home.php");
            }
            exit();
        } else {
            echo "Mật khẩu không chính xác!";
        }
    } else {
        echo "Tên người dùng không tồn tại!";
    }
}

$conn->close();
?>
