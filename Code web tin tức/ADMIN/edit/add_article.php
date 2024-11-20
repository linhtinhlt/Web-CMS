<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../../HTML/login.html");
    exit();
}

require '../../DATABASE/connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieude = $_POST['tieude'];
    $tomtat = $_POST['tomtat'];
    $noidung = $_POST['noidung'];
    $danhmucID = $_POST['danhmuc'];
    $tacgia = $_POST['tacgia'];

    // Xử lý hình ảnh
    $upload_dir = "../../img/";
    $hinhanh = ''; // Mặc định không có hình ảnh

    if (!empty($_POST['hinhanh_url'])) {
        // Nếu người dùng nhập URL hình ảnh
        $hinhanh = $_POST['hinhanh_url'];
    } elseif (!empty($_FILES['hinhanh']['name']) && $_FILES['hinhanh']['error'] == UPLOAD_ERR_OK) {
        // Nếu người dùng tải lên file
        $file_name = time() . "_" . basename($_FILES['hinhanh']['name']); // Tạo tên file duy nhất
        $target_file = $upload_dir . $file_name;
        $relative_path = "img/" . $file_name;

        if (move_uploaded_file($_FILES['hinhanh']['tmp_name'], $target_file)) {
            $hinhanh = $relative_path;
        } else {
            echo "<script>alert('Không thể tải lên file. Vui lòng thử lại.');</script>";
            exit();
        }
    }

    // Insert bài viết vào cơ sở dữ liệu
    $sql = "INSERT INTO Baiviet (Tieude, Tomtat, Noidung, Hinhanh, DanhmucID, Tacgia, Ngaydang)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $tieude, $tomtat, $noidung, $hinhanh, $danhmucID, $tacgia);

    if ($stmt->execute()) {
        echo "<script>alert('Bài viết mới đã được thêm thành công!'); window.location.href='../articles.php';</script>"; 
        exit();
    } else {
        echo "<script>alert('Đã xảy ra lỗi khi thêm bài viết.');</script>";
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Bài Viết Mới</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/manage.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <style>
        body {
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .main-content {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .main-content h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .main-content label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #555;
        }

        .main-content input[type="text"],
        .main-content textarea,
        .main-content select {
            width: 95%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
            background-color: #f9f9f9;
        }

        .main-content button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #00aaff;
            color: #ffffff;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .main-content button:hover {
            background-color: #007bb5;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h1>Thêm Bài Viết Mới</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="tieude">Tiêu đề:</label>
        <input type="text" id="tieude" name="tieude" required>

        <label for="tomtat">Tóm tắt:</label>
        <textarea id="tomtat" name="tomtat" required></textarea>

        <label for="noidung">Nội dung:</label>
        <textarea id="noidung" name="noidung"></textarea>

        <label for="tacgia">Tác giả:</label>
        <input type="text" id="tacgia" name="tacgia" required>

        <label for="hinhanh">Hình ảnh:</label>
        <input type="file" id="hinhanh" name="hinhanh" accept="image/*">
        
        <label for="hinhanh_url">Hoặc URL Hình ảnh:</label>
        <input type="url" id="hinhanh_url" name="hinhanh_url" placeholder="Nhập URL hình ảnh...">

        <label for="danhmuc">Danh mục:</label>
        <select id="danhmuc" name="danhmuc" required>
            <?php
            $danhmuc_sql = "SELECT DanhmucID, TenDanhmuc FROM Danhmuc";
            $danhmuc_result = $conn->query($danhmuc_sql);
            while ($row = $danhmuc_result->fetch_assoc()) {
                echo "<option value='" . $row['DanhmucID'] . "'>" . htmlspecialchars($row['TenDanhmuc']) . "</option>";
            }
            ?>
        </select>

        <button type="submit">Thêm bài viết</button>
    </form>
</div>

<script>
    ClassicEditor
        .create(document.querySelector('#noidung'))
        .catch(error => {
            console.error(error);
        });
</script>

</body>
</html>

<?php
$conn->close();
?>
