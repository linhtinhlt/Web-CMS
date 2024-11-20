<?php
session_start();
include '../../DATABASE/connect.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    echo "Bạn cần đăng nhập để yêu thích bài viết.";
    exit;
}


$userID = $_SESSION['user_id'];
if (isset($_POST['action']) && $_POST['action'] == 'toggle_like') {
    $baivietID = isset($_POST['baiviet_id']) ? (int)$_POST['baiviet_id'] : 0;

    if ($baivietID > 0) {
       
        $checkQuery = "SELECT * FROM YeuThich WHERE NguoidungID = ? AND BaivietID = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $userID, $baivietID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
        
            $deleteQuery = "DELETE FROM YeuThich WHERE NguoidungID = ? AND BaivietID = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("ii", $userID, $baivietID);
            $deleteStmt->execute();
            echo "Đã xóa khỏi yêu thích.";
        } else {
          
            $insertQuery = "INSERT INTO YeuThich (NguoidungID, BaivietID) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ii", $userID, $baivietID);
            $insertStmt->execute();
            echo "Đã thêm vào yêu thích.";
        }
    }
}


$query = "
    SELECT Baiviet.BaivietID, Baiviet.Tieude, Baiviet.Hinhanh, Baiviet.Ngaydang, Danhmuc.Tendanhmuc, YeuThich.ThoiGianYeuThich
    FROM YeuThich
    JOIN Baiviet ON YeuThich.BaivietID = Baiviet.BaivietID
    JOIN Danhmuc ON Baiviet.DanhmucID = Danhmuc.DanhmucID
    WHERE YeuThich.NguoidungID = ?
    ORDER BY YeuThich.ThoiGianYeuThich DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$like_article = $stmt->get_result();
?>
<div class="container-lg mt-4">
    <h1 class="fw-bold mb-5" style="font-size:30px;">Danh sách bài viết yêu thích</h1>
    <div class="row">
        <?php if ($like_article->num_rows > 0): ?>
            <?php while ($article = $like_article->fetch_assoc()): ?>
                <div class="col-md-12 mb-4">
                    <div class="row">
                        <div class="col-3">
                            <img src="<?php echo htmlspecialchars($article['Hinhanh']); ?>" alt="" class="img-fluid">
                        </div>
                        <div class="col-9">
                            <h5><?php echo htmlspecialchars($article['Tieude']); ?></h5>
                            <p><b>Thời gian yêu thích:</b> <?php echo htmlspecialchars($article['ThoiGianYeuThich']); ?></p>
                            <button class="btn btn-danger toggle-like float-end" 
                                data-baivietid="<?php echo $article['BaivietID']; ?>">
                                Bỏ thích
                            </button>
                            <a href="article_detail.php?BaivietID=<?php echo $article['BaivietID']; ?>" >Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Chưa có bài viết nào được yêu thích.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    $('.toggle-like').click(function () {
        var baivietID = $(this).data('baivietid');
        $.post('../VIEWER/edit_profile/like_article.php', { action: 'toggle_like', baiviet_id: baivietID }, function (response) {
            location.reload(); 
        });
    });
</script>

<script>
    function confirmDelete() {
        return confirm("Bạn chắc chắn muốn xóa bài viết này?");
    }
</script>

<?php
$stmt->close();
$conn->close();
?>
