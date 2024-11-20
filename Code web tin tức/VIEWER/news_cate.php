<?php
include '../DATABASE/connect.php';

// Kiểm tra xem có tham số category trong URL không
if (isset($_GET['category'])) {
    $category_name = $_GET['category'];

    // Truy vấn để lấy danh mục cha
    $category_query = "SELECT DanhmucID FROM Danhmuc WHERE Tendanhmuc = ?";
    $stmt = $conn->prepare($category_query);
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $category_result = $stmt->get_result();

    if ($category_row = $category_result->fetch_assoc()) {
        $category_id = $category_row['DanhmucID'];

        $subcategories_query = "SELECT DanhmucID, Tendanhmuc FROM Danhmuc WHERE ParentID = ?";
        $stmt = $conn->prepare($subcategories_query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $subcategories_result = $stmt->get_result();

        // Lưu danh mục cha vào mảng
        $subcategory_ids = [$category_id];
        
        // Lấy tất cả các danh mục con
        $subcategories = [];
        while ($subcategory = $subcategories_result->fetch_assoc()) {
            $subcategories[] = $subcategory;
            $subcategory_ids[] = $subcategory['DanhmucID'];
        }

        // Truy vấn để lấy tất cả bài viết thuộc danh mục cha và các danh mục con
        $placeholders = implode(',', array_fill(0, count($subcategory_ids), '?'));
        $articles_query = "
            SELECT * 
            FROM Baiviet 
            WHERE DanhmucID IN ($placeholders) 
            ORDER BY Ngaydang DESC";
        
        // Chuẩn bị câu truy vấn
        $stmt = $conn->prepare($articles_query);
        $stmt->bind_param(str_repeat("i", count($subcategory_ids)), ...$subcategory_ids);
        $stmt->execute();
        $articles_result = $stmt->get_result();
    } else {
        echo "Không tồn tại danh mục";
        exit();
    }
} else {
    echo "Không tồn tại tên danh mục"; 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/base.css">
    <style>
        .subcategory-link {
            margin-right: 15px;
            font-size: 14px;
            text-decoration: none;
            color: black;
        }
        .subcategory-link:hover{
            color: grey;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<main class="container-lg mt-4">
    <h2 class="header-title-body"><?php echo htmlspecialchars($category_name); ?></h2>

    <div class="subcategory-links mb-4">
        <?php foreach ($subcategories as $subcategory): ?>
            <a href="news_cate.php?category=<?php echo urlencode($subcategory['Tendanhmuc']); ?>" class="subcategory-link">
                <?php echo htmlspecialchars($subcategory['Tendanhmuc']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <?php while ($article = $articles_result->fetch_assoc()): ?>
            <div class="col-md-4 mb-3">
                <article>
                    <a href="article_detail.php?BaivietID=<?php echo $article['BaivietID']; ?>" class="text-decoration-none text-dark">
                        <img src="<?php echo htmlspecialchars($article['Hinhanh']); ?>" alt="Article Image" class="img-fluid w-100 mb-3" style="height: 200px; object-fit: cover;">
                        <h5><?php echo htmlspecialchars($article['Tieude']); ?></h5>
                        <p class="text-muted"><?php echo mb_strimwidth($article['Noidung'], 0, 150, "..."); ?></p>
                    </a>
                </article>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
