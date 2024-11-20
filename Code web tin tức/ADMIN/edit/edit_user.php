<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../../HTML/login.html");
    exit();
}

require '../../DATABASE/connect.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    $sql = "SELECT Nguoidung.Username, Nguoidung.Password, Nguoidung.TrangthaiTK, ThongTin.HoTen, ThongTin.Email 
            FROM Nguoidung
            LEFT JOIN ThongTin ON Nguoidung.NguoidungID = ThongTin.NguoidungID
            WHERE Nguoidung.NguoidungID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Người dùng không tồn tại!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $new_username = $_POST['username'];
        $new_password = $_POST['password'];  
        $new_fullname = $_POST['fullname'];
        $new_email = $_POST['email'];
        $new_role = $_POST['role'];  
        

        if (!empty($new_password)) {
            $update_sql = "UPDATE Nguoidung SET Username = ?, Password = ?, TrangthaiTK = ? WHERE NguoidungID = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssii", $new_username, $new_password, $new_role, $user_id);
        } else {
            
            $update_sql = "UPDATE Nguoidung SET Username = ?, TrangthaiTK = ? WHERE NguoidungID = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sii", $new_username, $new_role, $user_id);
        }

        if ($stmt->execute()) {
            
            $check_sql = "SELECT * FROM ThongTin WHERE NguoidungID = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
               
                $update_sql_info = "UPDATE ThongTin SET HoTen = ?, Email = ? WHERE NguoidungID = ?";
                $stmt_info = $conn->prepare($update_sql_info);
                $stmt_info->bind_param("ssi", $new_fullname, $new_email, $user_id);
            } else {
               
                $insert_sql_info = "INSERT INTO ThongTin (HoTen, Email, NguoidungID) VALUES (?, ?, ?)";
                $stmt_info = $conn->prepare($insert_sql_info);
                $stmt_info->bind_param("ssi", $new_fullname, $new_email, $user_id);
            }
            
            if ($stmt_info->execute()) {
                echo "<script>alert('Đã cập nhật thông tin người dùng thành công!'); window.location.href='../users.php';</script>"; 
                exit();
            } else {
                echo "Có lỗi xảy ra khi cập nhật thông tin cá nhân!";
            }
        } else {
            echo "Có lỗi xảy ra khi cập nhật thông tin người dùng!";
        }
    }
} else {
    echo "ID người dùng không được cung cấp!";
    exit();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin người dùng</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f4f6;
        }

        .edit-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .edit-box h2 {
            margin-bottom: 20px;
        }

        .edit-box label {
            display: block;
            margin-bottom: 5px;
        }

        .edit-box input[type="text"],
        .edit-box input[type="email"],
        .edit-box input[type="password"],
        .edit-box select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .edit-box button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #00aaff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .edit-box button:hover {
            background-color: #007bb5;
        }

        .edit-box a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #ccc;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="edit-box">
        <h2>Chỉnh sửa thông tin người dùng</h2>
        <form method="POST">
            <label for="username">Tên đăng nhập</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>

            <label for="password">Mật khẩu mới (nếu có)</label>
            <input type="password" name="password" id="password">

            <label for="fullname">Họ và tên</label>
            <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['HoTen']); ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

            <label for="role">Trạng thái</label>
            <select name="role" id="role" required>
                <option value="0" <?php echo $user['TrangthaiTK'] == 0 ? 'selected' : ''; ?>>Quản trị viên</option>
                <option value="1" <?php echo $user['TrangthaiTK'] == 1 ? 'selected' : ''; ?>>Người dùng</option>
            </select>

            <button type="submit">Cập nhật</button>
        </form>
        <a href="../users.php">Quay lại</a>
    </div>
</body>
</html>
