<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../HTML/login.html");
    exit();
}

$isAdmin = ($_SESSION['role'] == 0);
require '../DATABASE/connect.php';

$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? '%' . $conn->real_escape_string($_GET['search']) . '%' : '%';

// Truy vấn lấy tất cả bình luận kèm tìm kiếm và phân trang
$query = "SELECT d.DanhgiaID, d.Binhluan, d.TrangThai, d.NgayBL, n.Username, b.TieuDe
          FROM Danhgia d
          JOIN Nguoidung n ON d.NguoidungID = n.NguoidungID
          JOIN Baiviet b ON d.BaivietID = b.BaivietID
          WHERE n.Username LIKE ? OR b.TieuDe LIKE ?
          ORDER BY d.NgayBL DESC
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $search, $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Truy vấn lấy tổng số bình luận (cho phân trang)
$count_query = "SELECT COUNT(*) AS total FROM Danhgia d
                JOIN Nguoidung n ON d.NguoidungID = n.NguoidungID
                JOIN Baiviet b ON d.BaivietID = b.BaivietID
                WHERE n.Username LIKE ? OR b.TieuDe LIKE ?";
$count_stmt = $conn->prepare($count_query);
$count_stmt->bind_param("ss", $search, $search);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bình luận</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
       
        <style>
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
    <h2>Quản lý bình luận</h2>

    <form method="GET" action="comment.php" class="search-form">
        <input type="text" name="search" placeholder="Tìm kiếm người dùng hoặc bài viết..." 
               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <button type="submit">Tìm kiếm</button>
     <?php if (!empty($_GET['search'])): ?>
        <a href="comment.php" style="margin-left: 10px;">Hiển thị tất cả</a>
    <?php endif; ?>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>Người dùng</th>
                <th>Bài viết</th>
                <th>Nội dung</th>
                <th>Ngày đăng</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Username']); ?></td>
                    <td><?php echo htmlspecialchars($row['TieuDe']); ?></td>
                    <td><?php echo htmlspecialchars($row['Binhluan']); ?></td>
                    <td><?php echo htmlspecialchars($row['NgayBL']); ?></td>
                    <td><?php echo $row['TrangThai'] ? 'Đã duyệt' : 'Chưa duyệt'; ?></td>
                    <td>
                        <?php if ($isAdmin): ?>
                            <?php if (!$row['TrangThai']): ?>
                                <a href="./edit/approve_comment.php?id=<?php echo $row['DanhgiaID']; ?>">Duyệt</a> 
                            <?php endif; ?>
                            <a href="./delete/delete_comment.php?id=<?php echo $row['DanhgiaID']; ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa bình luận này?');">Xóa</a>
                        <?php else: ?>
                            Không có quyền
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<div aria-label="Page navigation">
    <div class="pagination-container d-flex justify-content-center">
        <div class="page-item d-flex<?php echo ($page == 1) ? ' disabled' : ''; ?>">
            <a class="page-link" href="?page=1<?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" aria-label="First">
                <i class="bi bi-chevron-double-left"></i> 
            </a>
        </div>
        
        <div class="page-item d-flex<?php echo ($page == 1) ? ' disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" aria-label="Previous">
                <i class="bi bi-chevron-left"></i>
            </a>
        </div>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <div class="page-item<?php echo ($i == $page) ? ' active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>"><?php echo $i; ?></a>
            </div>
        <?php endfor; ?>
        <div class="page-item<?php echo ($page == $total_pages) ? ' disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo min($total_pages, $page + 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" aria-label="Next">
                <i class="bi bi-chevron-right"></i> 
            </a>
        </div>

        <div class="page-item<?php echo ($page == $total_pages) ? ' disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>" aria-label="Last">
                <i class="bi bi-chevron-double-right"></i> 
            </a>
        </div>
    </div>
</div>

</div>
</body>
</html>
