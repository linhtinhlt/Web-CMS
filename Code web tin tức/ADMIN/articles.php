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

$sql = "SELECT Baiviet.BaivietID, Baiviet.Tieude, Baiviet.Tomtat, Baiviet.Hinhanh, Baiviet.NgayDang, Danhmuc.TenDanhmuc 
        FROM Baiviet 
        LEFT JOIN Danhmuc ON Baiviet.DanhmucID = Danhmuc.DanhmucID 
        WHERE Baiviet.Tieude LIKE ? 
        ORDER BY Baiviet.NgayDang DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();


$count_sql = "SELECT COUNT(*) AS total FROM Baiviet WHERE Tieude LIKE ?";
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
    <title>Quản Lý Bài Viết</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="../CSS/manage.css">
    <style>
        .btn-show-all{
            margin-left: 10px;
        }
        .action-buttons{
            width: 100px;
            margin-top: 10px;
        }
        .btn-add-article{
            border-radius: 5px;
            margin: 20px;
            padding: 10px;
            border: 1px solid black;
        }
        .pagination{
            margin: 20px;
        }
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .pagination-container .page-item {
            display: inline-flex;
            margin-right: 5px;
        }

        .pagination-container .page-item.disabled {
            pointer-events: none;
        }

        .page-link {
            display: inline-block;
            padding: 10px 15px;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
            text-decoration: none;
        }

        .pagination-container .page-item:hover {
            background-color: #007bff;
        }

        .pagination-container .page-item:hover .page-link {
            color: white;
        }

        .pagination-container .page-item.active .page-link {
            background-color: #0056b3;
            color: white;
        }

        .page-item.disabled .page-link {
            background-color: #f1f1f1;
            color: #ccc;
        }
    </style>
</head>
<body>
<?php require 'dashboard.php'; ?>
<div class="main-content">
    <h1>Danh Sách Bài Viết</h1>
    <form method="GET" action="articles.php" class="search-form">
        <input type="text" name="search" placeholder="Tìm kiếm bài viết..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <button type="submit">Tìm kiếm</button>
        <a href="articles.php" class="btn-show-all">Hiển thị tất cả</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Tóm tắt</th>
                <th>Hình ảnh</th>
                <th>Danh mục</th>
                <th>Ngày đăng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $image_src = "";
        if (!empty($row['Hinhanh'])) {
            // Nếu Hinhanh là URL hợp lệ
            if (filter_var($row['Hinhanh'], FILTER_VALIDATE_URL)) {
                $image_src = $row['Hinhanh']; // URL hợp lệ
            } else {
                // Đường dẫn cục bộ (thư mục img)
                $image_src = "http://localhost/test/" . htmlspecialchars($row['Hinhanh']);
            }
        }

        // Hiển thị thông tin bài viết
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['BaivietID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Tieude']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Tomtat']) . "</td>";

        echo "<td>";
        if (!empty($image_src)) {
            echo "<img src='" . htmlspecialchars($image_src) . "' class='thumbnail' alt='Hình ảnh bài viết' style='height: 50px; width: 50px;'>";
        } else {
            echo "Không có hình ảnh";
        }
        echo "</td>";

        echo "<td>" . htmlspecialchars($row['TenDanhmuc']) . "</td>";
        echo "<td>" . htmlspecialchars($row['NgayDang']) . "</td>";

        echo "<td class='action-buttons'>
                <a href='./edit/edit_article.php?id=" . $row['BaivietID'] . "' class='btn-edit'>Chỉnh sửa</a>
                <a href='./delete/delete_article.php?id=" . $row['BaivietID'] . "' class='btn-delete'>Xóa</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Không có bài viết nào.</td></tr>";
}
?>

        </tbody>
    </table>

    <!-- Phân trang -->
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

    <a href="./edit/add_article.php" class="btn-add-article">Thêm bài viết mới</a>

</div>
</body>
</html>

<?php
$conn->close();
?>
