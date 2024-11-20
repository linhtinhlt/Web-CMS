<?php
 if(!isset($_SESSION)) 
 { 
     session_start(); 
 } 
include '../DATABASE/connect.php';
include 'comment_insert.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0) {
    $userID = 0;
} else {
    $userID = $_SESSION['user_id'];
}



$baiviet_id = isset($_GET['BaivietID']) ? (int)$_GET['BaivietID'] : 0;

if ($baiviet_id > 0) {
    $query = "SELECT Baiviet.*, Danhmuc.Tendanhmuc 
              FROM Baiviet 
              LEFT JOIN Danhmuc ON Baiviet.DanhmucID = Danhmuc.DanhmucID 
              WHERE Baiviet.BaivietID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $baiviet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
} else {
    echo "Không có bài viết nào được tìm thấy.";
    exit;
}


if ($userID > 0) {
    $check_query = "SELECT * FROM BaiVietDaXem WHERE NguoidungID = ? AND BaivietID = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $userID, $baiviet_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows == 0) {
        $insert_query = "INSERT INTO BaiVietDaXem (NguoidungID, BaivietID) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $userID, $baiviet_id);
        $insert_stmt->execute();
    }
}

$current_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$comment_query = "SELECT Danhgia.DanhgiaID, Danhgia.Binhluan, Nguoidung.Username AS TenNguoidung, 
                         ThongTin.AnhDaidien, Danhgia.NgayBL, Danhgia.NguoidungID
                  FROM Danhgia 
                  JOIN Nguoidung ON Danhgia.NguoidungID = Nguoidung.NguoidungID 
                  LEFT JOIN ThongTin ON Nguoidung.NguoidungID = ThongTin.NguoidungID
                  WHERE Danhgia.BaivietID = ? AND Danhgia.Trangthai = 1
                  ORDER BY Danhgia.NgayBL DESC";
$comment_stmt = $conn->prepare($comment_query);
$comment_stmt->bind_param("i", $baiviet_id);
$comment_stmt->execute();
$comments = $comment_stmt->get_result();
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['Tieude']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/base.css">
</head>
<body>
<?php include 'header.php'; ?>
    <main class="container-lg mt-4">
        <section class="mb-4">
            <h1 class="fw-bold article-title"><?php echo htmlspecialchars($article['Tieude']); ?></h1>
            <p class="text-muted">Ngày đăng: <?php echo date("d/m/Y", strtotime($article['Ngaydang'])); ?> |Tác giả: <span class="no-translate"><?php echo htmlspecialchars($article['Tacgia']); ?> </span>| Danh mục: <?php echo htmlspecialchars($article['Tendanhmuc']); ?></p>
            <?php
      
        if (!empty($article['Hinhanh'])) {
            
            if (filter_var($article['Hinhanh'], FILTER_VALIDATE_URL)) {
                $image_src = $article['Hinhanh']; 
            } else {
              
                $image_src = "http://localhost/test2/" . htmlspecialchars($article['Hinhanh']);
            }
        }
        ?>
        <img src="<?php echo $image_src; ?>" alt="Main News" class="img-fluid w-100 my-3" style="height: 400px; object-fit: cover;">
            <!-- <img src="<?php echo htmlspecialchars($article['Hinhanh']); ?>" alt="Main News" class="img-fluid w-100 my-3" style="height: 400px; object-fit: cover;"> -->
        </section>

        <section>
            <p class="article-content"><?php echo nl2br(htmlspecialchars_decode($article['Noidung'])); ?></p>
        </section>

        <div class="social-share mt-4">
                
        <button class="btn" id="likeButton" data-baivietid="<?php echo $baiviet_id; ?>">
    <i class="bi bi-heart" id="heartIcon" style="font-size: 25px; color: <?php echo $isLiked ? 'red' : 'gray'; ?>;"></i>
</button>

            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($current_url); ?>" target="_blank" class="btn btn-outline-primary me-1">
                <i class="bi bi-facebook"></i> Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($current_url); ?>&text=<?php echo urlencode($article['Tieude']); ?>" target="_blank" class="btn btn-outline-info me-1">
                <i class="bi bi-twitter"></i> Twitter
            </a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($current_url); ?>" target="_blank" class="btn btn-outline-secondary me-1">
                <i class="bi bi-linkedin"></i> LinkedIn
            </a>
            <a href="mailto:?subject=<?php echo urlencode($article['Tieude']); ?>&body=<?php echo urlencode($current_url); ?>" class="btn btn-outline-danger">
                <i class="bi bi-envelope"></i> Email
            </a>
        </div>

        <!-- Comments Section -->
        <section class="mt-5">
            <h4 class="fw-bold">Bình Luận</h4>
            <div class="border-top pt-3">
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="mb-3">
                        <div class="d-flex">
                            <div class="me-3">
                                <?php if (!empty($comment['AnhDaidien'])): ?>
                                    <img src="http://localhost/test/<?php echo htmlspecialchars($comment['AnhDaidien']); ?>" 
                                        alt="Ảnh đại diện" 
                                        class="rounded-circle" 
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                                <?php endif; ?>
                            </div>

                            <div>
                            <p class="mb-1"><strong class="no-translate"><?php echo htmlspecialchars($comment['TenNguoidung']); ?></strong> <span class="text-muted">- <?php echo date("d/m/Y", strtotime($comment['NgayBL'])); ?></span></p>
                                <p><?php echo nl2br(htmlspecialchars($comment['Binhluan'])); ?></p>
                                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['NguoidungID'] || $_SESSION['role'] == 0)): ?>
                                    <a href="?BaivietID=<?php echo $baiviet_id; ?>&delete=<?php echo $comment['DanhgiaID']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                        Xóa
                                    </a>                                
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>




               
                <form action="comment_insert.php" method="post" class="mt-4">
                    <input type="hidden" name="baiviet_id" value="<?php echo $baiviet_id; ?>">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Thêm bình luận của bạn</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Viết bình luận..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                </form>

                <!-- Hiển thị thông báo lỗi nếu người dùng chưa đăng nhập -->
                <?php if (isset($_SESSION['comment_error'])): ?>
                    <div class="alert alert-danger mt-4">
                        <?php echo $_SESSION['comment_error']; ?>
                        <?php unset($_SESSION['comment_error']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
  
    <div class="modal" id="successCommentModal" style="display:none;">
    <div class="modal__overlay">
        <div class="modal__body">
                    <div class="text-center">
                <p class="fw-bold text-uppercase text-success">Bình luận gửi thành công và đang được xét duyệt.</p>
                <p class="fw-light" id="commentTime">Thời gian còn lại: <span id="countdown">3</span> giây</p> <!-- Countdown text -->
                </div>
        </div>
    </div>
</div>

<?php
if (isset($_GET['delete']) && isset($_GET['BaivietID'])) {
    $baiviet_id = (int)$_GET['BaivietID'];
    $danhgia_id = (int)$_GET['delete'];

    // Kiểm tra bình luận có tồn tại không
    $delete_query = "SELECT * FROM Danhgia WHERE DanhgiaID = ? AND BaivietID = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $danhgia_id, $baiviet_id);
    $delete_stmt->execute();
    $result = $delete_stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_comment_query = "DELETE FROM Danhgia WHERE DanhgiaID = ?";
        $delete_comment_stmt = $conn->prepare($delete_comment_query);
        $delete_comment_stmt->bind_param("i", $danhgia_id);
        if ($delete_comment_stmt->execute()) {
            echo "<script>alert('Xóa bình luận thành công!'); window.location.href = '?BaivietID=$baiviet_id';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi xóa bình luận.'); window.location.href = '?BaivietID=$baiviet_id';</script>";
        }
    } else {
        echo "<script>alert('Không tìm thấy bình luận hoặc bình luận không thuộc bài viết này.'); window.location.href = '?BaivietID=$baiviet_id';</script>";
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
       
       <?php if (isset($_SESSION['comment_success'])): ?>
    document.getElementById('successCommentModal').style.display = 'block';
    var countdown = 3;
    var countdownElement = document.getElementById('countdown');
    
    var interval = setInterval(function() {
        countdown--;
        countdownElement.innerText = countdown;
        if (countdown <= 0) {
            clearInterval(interval); 
            document.getElementById('successCommentModal').style.display = 'none'; 
        }
    }, 1000); 

    <?php unset($_SESSION['comment_success']); ?>
<?php endif; ?>

   
    $(document).ready(function () {
        $('#likeButton').click(function () {
            if (<?php echo $userID; ?> === 0) {
                alert("Bạn cần đăng nhập để thích bài viết.");
                window.location.href = '../HTML/login.html';  // Redirect to login page if not logged in
                return;  // Prevent further execution
            }
            var baivietID = $(this).data('baivietid');
            var $icon = $('#heartIcon');

            $.post('../VIEWER/edit_profile/like_article.php', {
                action: 'toggle_like',  
                baiviet_id: baivietID
            }, function(response) {
                if ($icon.css('color') === 'rgb(255, 0, 0)') { 
                    $icon.css('color', 'gray'); // Unliked
                } else {
                    $icon.css('color', 'red'); // Liked
                }
            });
        });
    });


    </script>

</body>
</html>
