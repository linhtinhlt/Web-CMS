<?php
session_start();
include '../../DATABASE/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    echo "Vui lòng đăng nhập để xem các bài viết đã đọc.";
    exit;
}

$userID = $_SESSION['user_id'];


$deleteQuery = "
    DELETE FROM BaiVietDaXem 
    WHERE NguoidungID = ? 
    AND ThoiGianDaXem < NOW() - INTERVAL 30 DAY
";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("i", $userID);
$deleteStmt->execute();
$deleteStmt->close();


$query = "
    SELECT Baiviet.BaivietID, Baiviet.Tieude, Baiviet.Hinhanh, Baiviet.Ngaydang, Danhmuc.Tendanhmuc, BaiVietDaXem.ThoiGianDaXem
    FROM BaiVietDaXem
    JOIN Baiviet ON BaiVietDaXem.BaivietID = Baiviet.BaivietID
    JOIN Danhmuc ON Baiviet.DanhmucID = Danhmuc.DanhmucID
    WHERE BaiVietDaXem.NguoidungID = ?
    ORDER BY BaiVietDaXem.ThoiGianDaXem DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
    <div class="container-lg mt-4">
        <h1 class="fw-bold mb-5" style="font-size:30px">Các Bài Viết Đã Xem</h1>
        <div class="row">
            <?php while ($article = $result->fetch_assoc()): ?>
                <div class="col-md-12 mb-4">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <img src="<?php echo htmlspecialchars($article['Hinhanh']); ?>" class="img-fluid" alt="Article Image">
                        </div>
                        
                        <div class="col-12 col-md-9">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($article['Tieude']); ?></h5>
                                <p><b>Thời gian truy cập:</b> <?php echo htmlspecialchars($article['ThoiGianDaXem']); ?></p>
                                <p class="card-text"><b>Danh mục:</b><?php echo htmlspecialchars($article['Tendanhmuc']); ?></p>
                                <a href="article_detail.php?BaivietID=<?php echo $article['BaivietID']; ?>" >Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php else: ?>
    <p>Không có bài viết nào được tìm thấy.</p>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>
