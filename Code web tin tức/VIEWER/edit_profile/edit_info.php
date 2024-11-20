<?php
session_start();
include '../../DATABASE/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần đăng nhập để chỉnh sửa thông tin!";
    header("Location: ../HTML/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$new_username = $_POST['username'] ?? '';
$new_fullname = $_POST['fullname'] ?? '';
$new_email = $_POST['email'] ?? '';
$new_password = $_POST['password'] ?? '';
$profile_picture = $_FILES['profile_picture'] ?? null;  

$conn->begin_transaction();
try {

    $sql = "SELECT Nguoidung.Username, ThongTin.HoTen, ThongTin.Email, ThongTin.AnhDaidien
            FROM Nguoidung
            LEFT JOIN ThongTin ON Nguoidung.NguoidungID = ThongTin.NguoidungID
            WHERE Nguoidung.NguoidungID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (!empty($new_username) && $new_username !== $user['Username']) {
            $update_user_sql = "UPDATE Nguoidung SET Username = ? WHERE NguoidungID = ?";
            $stmt = $conn->prepare($update_user_sql);
            $stmt->bind_param("si", $new_username, $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật tên đăng nhập.");
            }
        }

        if (!empty($new_password)) {
            $update_password_sql = "UPDATE Nguoidung SET Password = ? WHERE NguoidungID = ?";
            $stmt = $conn->prepare($update_password_sql);
            $stmt->bind_param("si", $new_password, $user_id); 
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật mật khẩu.");
            }
        }
        
        // Cập nhật thông tin cá nhân trong bảng ThongTin
        if (!empty($new_fullname) || !empty($new_email)) {
            $update_info_sql = "UPDATE ThongTin SET HoTen = ?, Email = ? WHERE NguoidungID = ?";
            $stmt_info = $conn->prepare($update_info_sql);
            $stmt_info->bind_param("ssi", $new_fullname, $new_email, $user_id);
            if (!$stmt_info->execute()) {
                throw new Exception("Lỗi khi cập nhật thông tin cá nhân.");
            }
        }

      
        if ($profile_picture && $profile_picture['error'] == 0) {
            $upload_dir = "../../img/"; 
            $file_name = time() . "_" . basename($profile_picture['name']); 
            $file_path = "img/" . $file_name; 
        
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if (in_array(strtolower($file_extension), $allowed_extensions) && $profile_picture['size'] <= 5 * 1024 * 1024) {
                if (move_uploaded_file($profile_picture['tmp_name'], $upload_dir . $file_name)) {
                   
                    $update_image_sql = "UPDATE ThongTin SET AnhDaidien = ? WHERE NguoidungID = ?";
                    $stmt_image = $conn->prepare($update_image_sql);
                    $stmt_image->bind_param("si", $file_path, $user_id);
                    if (!$stmt_image->execute()) {
                        throw new Exception("Lỗi khi cập nhật ảnh đại diện.");
                    }
                } else {
                    throw new Exception("Không thể tải ảnh lên.");
                }
            } else {
                throw new Exception("Định dạng hoặc kích thước ảnh không hợp lệ.");
            }
        }

        $conn->commit();

        $_SESSION['success_message'] = "Thông tin của bạn đã được cập nhật!";
        header("Location: ../profile.php?page=info-account");
        exit();
    } else {
        throw new Exception("Không tìm thấy thông tin người dùng.");
    }

} catch (Exception $e) {
    $conn->rollback();
    echo "Lỗi khi cập nhật thông tin: " . $e->getMessage();
}

$conn->close();
?>
