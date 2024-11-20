<?php
session_start();
include '../../DATABASE/connect.php';


if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần đăng nhập để chỉnh sửa thông tin!";
    header("Location: ../HTML/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

    
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
    } else {
        echo "Không tìm thấy thông tin người dùng!";
        exit();
    }

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Chỉnh sửa thông tin cá nhân</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form action="./edit_profile/edit_info.php" method="POST" class="shadow p-4 rounded bg-light" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" name="username" id="username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới (nếu có)</label>
            <input type="password" class="form-control" name="password" id="password">
        </div>
        <div class="mb-3">
            <label for="fullname" class="form-label">Họ và tên</label>
            <input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['HoTen']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
        </div>

        <!-- Hiển thị ảnh đại diện nếu có -->
        <div class="mb-3">
            <label for="profile_picture" class="form-label">Ảnh đại diện</label>
            <?php if (!empty($user['AnhDaidien'])): ?>
                <div class="mb-2">
                    <img src="http://localhost/test/<?php echo htmlspecialchars($user['AnhDaidien']); ?>" alt="Ảnh đại diện" class="img-thumbnail" style="width: 150px; height: 150px;">
                </div>
            <?php else: ?>
                <p>Chưa có ảnh đại diện</p> 
            <?php endif; ?>
            <input type="file" class="form-control" name="profile_picture" id="profile_picture">
        </div>




        <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


