<?php
include '../DATABASE/connect.php';

$search_query = '';
$search_results = [];

if (isset($_GET['search_query'])) {
    $search_query = $_GET['search_query'];

    if ($search_query) {
        $query = "
            SELECT * FROM Baiviet 
            WHERE Tieude LIKE ? OR Noidung LIKE ?
            ORDER BY Ngaydang DESC
        ";
        $stmt = $conn->prepare($query);
        $search_param = "%" . $search_query . "%";
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        $search_results = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<?php include 'header.php'; ?>

<main class="container-lg mt-4">
    <h3 class="mb-3">Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($search_query); ?>"</h3>
    
    <?php if (mysqli_num_rows($search_results) > 0): ?>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($search_results)): ?>
                <article class="col-md-6 mb-4">
                    <a href="article_detail.php?BaivietID=<?php echo $row['BaivietID']; ?>" class="text-decoration-none text-dark">
                        <img src="<?php echo htmlspecialchars($row['Hinhanh']); ?>" alt="Article Image" class="img-fluid w-100 mb-3" style="height: 200px; object-fit: cover;">
                        <h5><?php echo htmlspecialchars($row['Tieude']); ?></h5>
                        <p class="text-muted"><?php echo mb_strimwidth($row['Noidung'], 0, 150, "..."); ?></p>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Không tìm thấy bài viết nào với từ khóa "<?php echo htmlspecialchars($search_query); ?>"</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
