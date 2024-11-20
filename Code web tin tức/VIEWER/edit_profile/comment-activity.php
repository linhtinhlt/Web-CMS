<?php
session_start();
include '../../DATABASE/connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../HTML/login.html");
    exit();
}

$user_id = $_SESSION['user_id']; 
$query = "SELECT d.DanhgiaID, d.Binhluan, d.NgayBL, b.TieuDe, b.Hinhanh, b.BaivietID
          FROM Danhgia d
          JOIN Baiviet b ON d.BaivietID = b.BaivietID
          WHERE d.NguoidungID = ?
          ORDER BY d.NgayBL DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="col-12 px-md-4 content">
    <h1 class="mb-5"  style="font-size: 30px;">Hoạt động bình luận</h1>
    <?php if ($result->num_rows > 0): ?>
        <ul class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <div class="media d-flex">
                        <div class="col-3 d-flex">
                            <?php if (!empty($row['Hinhanh'])): ?>
                                <img src="<?php echo htmlspecialchars($row['Hinhanh']); ?>" alt="Image" class="mr-3" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                        <div class="media-body col-9">
                            <strong>
                                <a href="article_detail.php?BaivietID=<?php echo $row['BaivietID']; ?>" class="text-dark"><?php echo htmlspecialchars($row['TieuDe']); ?></a>
                            </strong><br>
                            <span><?php echo htmlspecialchars($row['Binhluan']); ?></span><br>
                            <small>Ngày: <?php echo htmlspecialchars($row['NgayBL']); ?></small><br>
                            <a href="./article_detail.php?BaivietID=<?php echo $row['BaivietID']; ?>" class=" mt-2">Xem chi tiết</a>
                        </div>
                    </div>
                </li>
                <hr>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted text-center">Bạn không có hoạt động bình luận nào</p>
    <?php endif; ?>
</main>

<?php
$stmt->close();
$conn->close();
?>
