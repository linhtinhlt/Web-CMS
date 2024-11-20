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
        $sql = "DELETE FROM Baiviet WHERE BaivietID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $article_id);
    
        if ($stmt->execute()) {
            echo "<script>alert('Xóa bài viết thành công!'); window.location.href='../articles.php';</script>"; 
            exit();
        } else {
            echo "Có lỗi xảy ra khi xóa bài viết!";
        }
        $stmt->close();
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
