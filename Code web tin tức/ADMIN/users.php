<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../HTML/login.html");
    exit();
}

require '../DATABASE/connect.php';



$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? '%' . $conn->real_escape_string($_GET['search']) . '%' : '%';

$sql = "SELECT NguoidungID, Username, TrangthaiTK, NgaytaoTK FROM Nguoidung 
        WHERE Username LIKE ? 
        LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$count_sql = "SELECT COUNT(*) AS total FROM Nguoidung WHERE Username LIKE ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $search);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/manage.css">
    <style>
        .btn-show-all{
            margin-left: 10px;
        }
        
    </style>
</head>
<body>

<?php require 'dashboard.php'; ?>
<div class="main-content" >
   
    <h1>Quản lý người dùng</h1>
    <form method="GET" action="users.php" class="search-form">
    <input type="text" name="search" placeholder="Tìm kiếm người dùng..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
    <button type="submit">Tìm kiếm</button>
    <a href="users.php" class="btn-show-all">Hiển thị tất cả</a>
    </form>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['NguoidungID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    <td><?php echo $row['TrangthaiTK'] ? 'Viewer' : 'Admin'; ?></td>
                    <td><?php echo htmlspecialchars($row['NgaytaoTK']); ?></td>
                    <td>
                        <a href="./edit/edit_user.php?id=<?php echo htmlspecialchars($row['NguoidungID']); ?>" class="btn-edit ">Chỉnh sửa</a>
                        <a href="./delete/delete_user.php?id=<?php echo htmlspecialchars($row['NguoidungID']); ?>" class="btn-delete">Xóa</a>

                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">« Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>" 
               class="<?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($_GET['search'] ?? ''); ?>">Sau »</a>
        <?php endif; ?>
    </div>
</div>


</body>
</html>
