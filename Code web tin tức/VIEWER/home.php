<?php

include '../DATABASE/connect.php';


$categories = [
    'Thời sự' => 1,
    'Thế giới' => 2,
    'Kinh tế' => 3,
    'Giải trí' => 4,
    'Thể thao' => 5
];


$articles_by_category = [];


foreach ($categories as $category_name => $category_id) {
    $query = "
        SELECT * 
        FROM Baiviet 
        WHERE DanhmucID = ? OR DanhmucID IN (SELECT DanhmucID FROM Danhmuc WHERE parentID = ?)
        ORDER BY Ngaydang DESC 
        LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $category_id, $category_id);
    $stmt->execute();
    $articles_by_category[$category_name] = $stmt->get_result();
}

// bài viết mới nhất
$new_articles_query = "SELECT * FROM Baiviet ORDER BY Ngaydang DESC LIMIT 5";
$new_articles_result = $conn->query($new_articles_query);

// 5 bài viết ngẫu nhiên từ cơ sở dữ liệu
$sql_rand = "SELECT * FROM Baiviet ORDER BY RAND() LIMIT 5";
$result_rand = mysqli_query($conn, $sql_rand);

// bài viết nổi bật chính ngẫu nhiên
$sql_main = "SELECT * FROM Baiviet ORDER BY RAND() LIMIT 1";
$result_main = mysqli_query($conn, $sql_main);

//  2 bài viết phụ ngẫu nhiên
$sql_secondary = "SELECT * FROM Baiviet ORDER BY RAND() LIMIT 2";
$result_secondary = mysqli_query($conn, $sql_secondary);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VietNews Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="../CSS/base.css">
    <style>
        a{
            text-decoration: none;
        }
    </style>
</head>
<body>
    
<?php include 'header.php'; ?>

<div class="container-lg mt-4">
    <div class="row border-bottom border-2 p-2">
        <h2 class="header-title-body mb-3">TIN NỔI BẬT</h2>
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="d-flex flex-column">
                <?php
                if (mysqli_num_rows($result_rand) > 0) {
                    while ($row = mysqli_fetch_assoc($result_rand)) {
                        $image_src = filter_var($row['Hinhanh'], FILTER_VALIDATE_URL) ? $row['Hinhanh'] : "http://localhost/test2/" . htmlspecialchars($row['Hinhanh']);
                        echo '
                        <article class="d-flex align-items-center border-bottom border-2 p-2">
                            <div class="col-4 d-flex justify-content-center">
                                <img src="' . htmlspecialchars($image_src) . '" alt="Tin nổi bật" class="img-fluid" style="width: 100px; height: 80px; object-fit: cover;">
                            </div>
                            <div class="col-8 ms-2">
                                <a href="article_detail.php?BaivietID=' . $row['BaivietID'] . '" class="text-decoration-none text-dark">
                                <p>' . htmlspecialchars($row['Tieude']) . '</p>
                            </div>
                            </a>
                        </article>';
                    }
                } else {
                    echo "<p>Không có tin nổi bật để hiển thị.</p>";
                }
                ?>
            </div>
        </aside>

        <section class="col-lg-9">
            <div class="row">
                <div class="col-md-8 main-article mb-4">
                    <?php
                    if (mysqli_num_rows($result_main) > 0) {
                        $main_article = mysqli_fetch_assoc($result_main);
                        $main_image_src = filter_var($main_article['Hinhanh'], FILTER_VALIDATE_URL) ? $main_article['Hinhanh'] : "http://localhost/test2/" . htmlspecialchars($main_article['Hinhanh']);
                        echo '
                        <article>
                            <a href="article_detail.php?BaivietID=' . $main_article['BaivietID'] . '" class="text-decoration-none text-dark">
                            <img src="' . htmlspecialchars($main_image_src) . '" alt="Main News" class="img-fluid w-100 mb-3" style="height: 400px; object-fit: cover;">
                            <div class="mt-3">
                                <div class="text-dark fw-bold text-decoration-none">
                                    ' . htmlspecialchars($main_article['Tieude']) . '
                                </div>
                                <p class="text-muted">
                                    ' . htmlspecialchars($main_article['Tomtat']) . '
                                </p>
                            </div>
                            </a>
                        </article>';
                    }
                    ?>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <?php
                        if (mysqli_num_rows($result_secondary) > 0) {
                            while ($secondary_article = mysqli_fetch_assoc($result_secondary)) {
                                $secondary_image_src = filter_var($secondary_article['Hinhanh'], FILTER_VALIDATE_URL) ? $secondary_article['Hinhanh'] : "http://localhost/test2/" . htmlspecialchars($secondary_article['Hinhanh']);
                                echo '
                                <article class="col-md-12 mb-4">
                                    <a href="article_detail.php?BaivietID=' . $secondary_article['BaivietID'] . '" class="text-decoration-none text-dark">
                                    <img src="' . htmlspecialchars($secondary_image_src) . '" alt="Secondary News" class="img-fluid w-100 mb-3" style="height: 200px; object-fit: cover;">
                                    <p class="fw-bold">' . htmlspecialchars($secondary_article['Tieude']) . '</p>
                                    </a>
                                </article>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>  
        </section>

    </div>

    <div class="row border-bottom border-2 p-2">
        
        <section class="col-lg-3 col-12">
            <h3 class="header-title-body mb-3">TIN MỚI NHẤT</h3>
            <div class="d-flex flex-column">
                <?php
                while ($new_article = $new_articles_result->fetch_assoc()) {
                    $new_image_src = filter_var($new_article['Hinhanh'], FILTER_VALIDATE_URL) ? $new_article['Hinhanh'] : "http://localhost/test2/" . htmlspecialchars($new_article['Hinhanh']);
                    echo '<article class="d-flex align-items-center border-bottom border-2 p-2">';
                    echo '<div class="col-4 d-flex justify-content-center">';
                    echo '<a href="article_detail.php?BaivietID=' . $new_article['BaivietID'] . '" class="text-decoration-none text-dark">';
                    echo '<img src="' . htmlspecialchars($new_image_src) . '" alt="Tin Moi Nhat" class="img-fluid" style="width: 100px; height: 80px; object-fit: cover;">';
                    echo '</div>';
                    echo '<div class="col-8 ms-2">';
                    echo '<p>' . htmlspecialchars($new_article['Tieude']) . '</p>';
                    echo '</a>';
                    echo '</div>';
                    echo '</article>';
                }
                ?>
            </div>
        </section> 

        <!-- Phần hiển thị các danh mục khác nhau -->
        <section class="col-lg-9 col-12">
        <?php foreach ($articles_by_category as $category_name => $articles): ?>
            <section class="mb-5">
                <h2 class="header-title-body ms-5 mt-3"><?php echo $category_name; ?></h2>
                <div class="border-start ps-5">
                    
                    <div class="content-wrapper border-bottom mt-3">

                        <div class="d-flex flex-column flex-md-row">
                            <?php if ($main_article = $articles->fetch_assoc()): ?>
                                <article class="col-md-6 mb-3">
                                    <div class="me-3">
                                    <a href="article_detail.php?BaivietID=<?php echo $main_article['BaivietID']; ?>" class="text-decoration-none text-dark">
                                        <img src="<?php echo filter_var($main_article['Hinhanh'], FILTER_VALIDATE_URL) ? $main_article['Hinhanh'] : 'http://localhost/test2/' . htmlspecialchars($main_article['Hinhanh']); ?>" alt="Main News Image" class="img-fluid w-100" style="height: 200px; object-fit: cover;">
                                        <div class="mt-3">
                                            <h5><?php echo $main_article['Tieude']; ?></h5>
                                            <p class="text-muted"><?php echo mb_strimwidth($main_article['Noidung'], 0, 100, "..."); ?></p>
                                        </div>
                                    </a>
                                    </div>
                                </article>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="d-flex flex-column">
                                    <?php while ($article = $articles->fetch_assoc()): ?>
                                    <article class="d-flex align-items-center border-bottom py-2">
                                        <div class="col-4">
                                            <a href="article_detail.php?BaivietID=<?php echo $article['BaivietID']; ?>">
                                            <img src="<?php echo filter_var($article['Hinhanh'], FILTER_VALIDATE_URL) ? $article['Hinhanh'] : 'http://localhost/test2/' . htmlspecialchars($article['Hinhanh']); ?>" alt="Article Image" class="img-fluid w-100" style="height: 80px; object-fit: cover;">
                                            </a>
                                        </div>
                                        <div class="col-8 ms-2">
                                            <a href="article_detail.php?BaivietID=<?php echo $article['BaivietID']; ?>" class="text-decoration-none text-dark">
                                                <p><?php echo htmlspecialchars($article['Tieude']); ?></p>
                                            </a>
                                        </div>
                                    </article>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
        <?php endforeach; ?>
    </div>


    </div>
                <div class="row  border-bottom border-2 p-2" >
                    <h2 class="header-title-body ">PODCASTS</h2>
                    <section class="col-md-12 d-flex p-3">
                    
                        <div class="row w-100">
                            <div class="col-md-4 d-flex align-items-center">
                                <div>
                                    <a href="#" class="text-dark fw-bold text-decoration-none">Bí ẩn vụ chôn sống con cháu nhà Lý của công thần nhà Trần</a>
                                    <p class="text-muted">Đại Việt sử ký toàn thư chép rằng sau khi nhà Trần thành lập, Trần Thủ Độ đã dựng bẫy giết hết tôn thất nhà Lý, giới khảo cứu đã chỉ ra những điểm vô lý trong sự việc này.</p>
                                </div>
                            </div>
                            <div class="col-md-5 d-flex  ">
                                <img src="https://i1-vnexpress.vnecdn.net/2024/08/19/Frame175-1724033548-4940-1724034006.jpg?w=320&h=320&q=100&dpr=1&fit=crop&s=hTIBXWjEhLf0w2ajJxlbbg" alt="R.V. Rental" class="img-fluid"  style="width: 400px; height: 400px; object-fit: cover;">
                            </div>
                            <div class="col-md-3 d-flex">
                                <div class="row ">
                                    <div class="col-12  border-bottom border-2">
                                        <div class="d-flex align-items-center">
                                            <div class="col-md-8 p-1">
                                                <p><strong>Vì sao thi thể Từ Hi Thái Hậu nguyên vẹn sau 20 năm chôn cất?</strong></p>
                                            </div>
                                            <div class="col-md-4 d-flex justify-content-center  ">
                                                <img src="https://i1-vnexpress.vnecdn.net/2024/05/30/Group20707-1717064321-5334-1717066790.jpg?w=100&h=100&q=100&dpr=1&fit=crop&s=dwpQZZrGUvKVQGDKAgNGOw" alt="Fact Check" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
                                            </div>                       
                                        </div>
                                    </div>
                                    <div class="col-12 border-bottom border-2 mt-4">
                                        <div class="d-flex align-items-center justify-content-center  ">
                                            <div class="col-md-8   p-1 ">
                                                <p><strong>Chồng ca hát nhậu nhẹt, mặc kệ con ốm bệnh</strong></p>
                                            </div>
                                            <div class="col-md-4 d-flex ">
                                                <img src="https://i1-vnexpress.vnecdn.net/2024/08/16/ChonghamchoiV-1723798085-4598-1723798111.jpg?w=320&h=320&q=100&dpr=1&fit=crop&s=1w9xuTuSmWhjvY75vefjQQ" alt="Fact Check" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
                                            </div>                       
                                        </div>           
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="d-flex align-items-center">
                                        <div class="col-md-8   p-1 ">
                                            <p><strong>Giải mã hiện tượng bóng đè
                                            </strong></p>
                                        </div>
                                        <div class="col-md-4 d-flex justify-content-center  ">
                                            <img src="https://i1-vnexpress.vnecdn.net/2024/08/22/Frame17-1724298385-7518-1724298564.jpg?w=320&h=320&q=100&dpr=1&fit=crop&s=S7yxJfIUJqW2YjmbuwVOcA" alt="Fact Check" class="img-fluid" style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>                       
                                    </div>           
                                </div>
                                
                            </div>
                        </div>

                    </div>
                    </section>
                </div>

                <div class="row border-bottom border-2 p-2" >
                        <h2 class=" header-title-body">GAMES</h2>
                            <div class="row mt-8  border-bottom  p-3">
                                <!-- Hàng thứ hai -->
                                <div class="col-md-4 border-end ">
                                    <div class="d-flex align-items-center">
                                        <div class="col-md-10   p-2 ">
                                            <p><strong>Wordle</strong></p>
                                            <p class="text-muted">Get 6 chances to guess a 5-letter word.</p>
                                        </div>
                                        <div class="col-md-2 d-flex justify-content-center  ">
                                            <img src="../img/Wordle .png" alt="Fact Check" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>                       
                                    </div>
                                </div>
                                    
                                <div class="col-md-4 border-end ">
                                    <div class="d-flex align-items-center">
                                        <div class="col-md-10   p-2 ">
                                            <p><strong>Spelling Bee</strong></p>
                                            <p class="text-muted">How many words can you make with 7 letters?</p>
                                        </div>
                                        <div class="col-md-2 d-flex justify-content-center  ">
                                            <img src="../img/bee.png" alt="Fact Check" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>                       
                                    </div>
                                </div>

                                <div class="col-md-4 ">
                                    <div class="d-flex align-items-center">
                                        <div class="col-md-10   p-2 ">
                                            <p><strong>Live Election Updates: Democrats Turn Focus to Harris, After Fiery Biden Speech</strong></p>
                                        </div>
                                        <div class="col-md-2 d-flex justify-content-center  ">
                                            <img src="https://static01.nyt.com/images/2024/08/20/multimedia/20election-live-header-phwl/20election-live-header-phwl-superJumbo.jpg?quality=75&auto=webp" alt="Fact Check" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>                       
                                    </div>
                                </div>
                            </div>
                </div>


</main>

<?php include 'footer.php'; ?>
</body>
</html>
