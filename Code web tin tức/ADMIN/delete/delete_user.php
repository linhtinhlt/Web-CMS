<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../../HTML/login.html");
    exit();
}

require '../../DATABASE/connect.php';

if (isset($_GET['id'])) {
    $article_id = intval($_GET['id']);
    
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        $conn->begin_transaction();

        try {
            // Xóa các bản ghi liên quan trong bảng BaivietDaxem
            $sql_baivietdaxem = "DELETE FROM BaivietDaxem WHERE BaivietID = ?";
            $stmt_baivietdaxem = $conn->prepare($sql_baivietdaxem);
            $stmt_baivietdaxem->bind_param("i", $article_id);
            if (!$stmt_baivietdaxem->execute()) {
                throw new Exception("Lỗi khi xóa bản ghi trong bảng BaivietDaxem!");
            }

            // Xóa các bản ghi liên quan trong bảng YeuThich
            $sql_yeuthich = "DELETE FROM YeuThich WHERE BaivietID = ?";
            $stmt_yeuthich = $conn->prepare($sql_yeuthich);
            $stmt_yeuthich->bind_param("i", $article_id);
            if (!$stmt_yeuthich->execute()) {
                throw new Exception("Lỗi khi xóa bản ghi trong bảng YeuThich!");
            }

            // Xóa các bản dịch trong bảng BaivietDich
            $sql_baivietdich = "DELETE FROM BaivietDich WHERE BaivietID = ?";
            $stmt_baivietdich = $conn->prepare($sql_baivietdich);
            $stmt_baivietdich->bind_param("i", $article_id);
            if (!$stmt_baivietdich->execute()) {
                throw new Exception("Lỗi khi xóa bản dịch trong bảng BaivietDich!");
            }

            // Xóa bài viết chính trong bảng Baiviet
            $sql_baiviet = "DELETE FROM Baiviet WHERE BaivietID = ?";
            $stmt_baiviet = $conn->prepare($sql_baiviet);
            $stmt_baiviet->bind_param("i", $article_id);
            if (!$stmt_baiviet->execute()) {
                throw new Exception("Lỗi khi xóa bài viết trong bảng Baiviet!");
            }

            // Commit transaction nếu mọi thứ đều thành công
            $conn->commit();
            
            echo "<script>alert('Xóa bài viết và các bản ghi liên quan thành công!'); window.location.href='../articles.php';</script>";
            exit();
        } catch (Exception $e) {
            // Rollback nếu có lỗi xảy ra
            $conn->rollback();
            echo "Có lỗi xảy ra khi xóa bài viết: " . $e->getMessage();
        }
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Xác nhận xóa bài viết</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
            <style>
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: #f3f4f6;
                }

                .confirm-box {
                    background-color: #fff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }

                .confirm-box h2 {
                    margin-bottom: 20px;
                }

                .confirm-box button {
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    background-color: #00aaff;
                    color: #fff;
                    font-size: 16px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                }

                .confirm-box button:hover {
                    background-color: #007bb5;
                }

                .confirm-box a {
                    display: inline-block;
                    margin: 10px;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 4px;
                    background-color: #ccc;
                    color: #333;
                    text-decoration: none;
                    font-size: 16px;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
        <div class="confirm-box">
            <h2>Bạn có chắc chắn muốn xóa bài viết này không?</h2>
            <form method="GET" action="delete_article.php">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($article_id); ?>">
                <input type="hidden" name="confirm" value="yes">
                <button type="submit">Xóa</button>
            </form>
            <a href="../articles.php">Hủy bỏ</a>
        </div>
        </body>
        </html>
        <?php
    }
} else {
    echo "ID bài viết không được cung cấp!";
}

$conn->close();
?>
