<?php
require_once '../DATABASE/connect.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

   
    if ($password !== $confirm_password) {
        echo "Mật khẩu không trùng khớp!";
    } else {
        $sql = "SELECT * FROM Nguoidung WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "Tên người dùng đã tồn tại!";
        } else {
         
            $sql = "INSERT INTO Nguoidung (Username, Password, NgaytaoTK, TrangthaiTK) VALUES (?, ?, NOW(), 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);

            if ($stmt->execute()) {
                $nguoidungID = $stmt->insert_id; 

               
                $sql = "INSERT INTO Thongtin (NguoidungID, HoTen, Email, Ngaycapnhat) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $nguoidungID, $fullname, $email);

                if ($stmt->execute()) {
                
                    echo "<script>alert('Đăng kí thành công thành công!'); window.location.href='../HTML/login.html';</script>"; 
                    exit();
                } else {
                    echo "Đăng ký thất bại: " . $conn->error;
                }
            } else {
                echo "Đăng ký thất bại: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>
