<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../HTML/login.html");
    exit();
}

require '../../DATABASE/connect.php';


$message = "";

if (isset($_GET['id'])) {
    $categoryID = $_GET['id'];

    $sql = "SELECT * FROM Danhmuc WHERE DanhmucID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        $message = "<p style='color: red;'>Danh mục không tồn tại.</p>";
    }
    $stmt->close();
} else {
    $message = "<p style='color: red;'>Không có ID danh mục.</p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_category'])) {
        $tendanhmuc = $_POST['tendanhmuc'];
        $parentid = $_POST['parentid'];

        // Kiểm tra nếu ParentID 
        if ($parentid != "") {
          
            $checkParentSql = "SELECT * FROM Danhmuc WHERE DanhmucID = ?";
            $checkStmt = $conn->prepare($checkParentSql);
            $checkStmt->bind_param("i", $parentid);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows == 0) {
                $message = "<p style='color: red;'>Danh mục cha không hợp lệ.</p>";
                $checkStmt->close();
            } else {
                // Nếu ParentID hợp lệ, tiến hành cập nhật
                $updateSql = "UPDATE Danhmuc SET Tendanhmuc = ?, ParentID = ? WHERE DanhmucID = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("sii", $tendanhmuc, $parentid, $categoryID);

                if ($updateStmt->execute()) {
                    $message = "<p style='color: green;'>Cập nhật danh mục thành công.</p>";
                } else {
                    $message = "<p style='color: red;'>Lỗi khi cập nhật danh mục: " . $updateStmt->error . "</p>";
                }

                $updateStmt->close();
            }
        } else {
            $updateSql = "UPDATE Danhmuc SET Tendanhmuc = ?, ParentID = NULL WHERE DanhmucID = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $tendanhmuc, $categoryID);

            if ($updateStmt->execute()) {
                $message = "<p style='color: green;'>Cập nhật danh mục thành công.</p>";
            } else {
                $message = "<p style='color: red;'>Lỗi khi cập nhật danh mục: " . $updateStmt->error . "</p>";
            }

            $updateStmt->close();
        }
    } elseif (isset($_POST['update_subcategory'])) {
       
        $subcategoryID = $_POST['subcategory_id'];
        $subcategoryName = $_POST['subcategory_name'];

        $updateSubcategorySql = "UPDATE Danhmuc SET Tendanhmuc = ? WHERE DanhmucID = ?";
        $updateSubcategoryStmt = $conn->prepare($updateSubcategorySql);
        $updateSubcategoryStmt->bind_param("si", $subcategoryName, $subcategoryID);

        if ($updateSubcategoryStmt->execute()) {
            $message = "<p style='color: green;'>Cập nhật danh mục con thành công.</p>";
        } else {
            $message = "<p style='color: red;'>Lỗi khi cập nhật danh mục con: " . $updateSubcategoryStmt->error . "</p>";
        }

        $updateSubcategoryStmt->close();
    }
}

// Truy vấn 
$subcategoriesSql = "SELECT * FROM Danhmuc WHERE ParentID = ?";
$subcategoriesStmt = $conn->prepare($subcategoriesSql);
$subcategoriesStmt->bind_param("i", $categoryID);
$subcategoriesStmt->execute();
$subcategoriesResult = $subcategoriesStmt->get_result();
$subcategoriesStmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa danh mục</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/manage.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .main-content {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-weight: 600;
            margin-bottom: 20px;
        }
        h2 {
            font-weight: 500;
            margin-top: 40px;
        }
        .message {
            margin-bottom: 20px;
        }
        form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input[type="text"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        button {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        button:focus {
            outline: none;
        }
        .exit-button {
            background-color: #f44336;
            margin-top: 20px;
            width: 100%;
        }
        .exit-button:hover {
            background-color: #e53935;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h1>Chỉnh sửa danh mục</h1>

    <div class="message">
        <?php echo $message; ?>
    </div>

    <form method="POST" action="">
        <div>
            <label for="tendanhmuc">Tên danh mục:</label>
            <input type="text" id="tendanhmuc" name="tendanhmuc" value="<?php echo htmlspecialchars($category['Tendanhmuc']); ?>" required>
        </div>
        <div>
            <label for="parentid">Danh mục cha:</label>
            <select id="parentid" name="parentid">
                <option value="">Chọn danh mục cha</option>
                <?php
                $parentQuery = "SELECT * FROM Danhmuc WHERE ParentID IS NULL";
                $parentResult = $conn->query($parentQuery);
                while ($parent = $parentResult->fetch_assoc()) {
                    echo "<option value='" . $parent['DanhmucID'] . "'" . ($category['ParentID'] == $parent['DanhmucID'] ? " selected" : "") . ">" . $parent['Tendanhmuc'] . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="update_category">Cập nhật danh mục</button>
    </form>

    <h2>Danh mục con:</h2>
    <ul>
        <?php while ($subcategory = $subcategoriesResult->fetch_assoc()): ?>
            <li>
                <form method="POST" action="">
                    <input type="text" name="subcategory_name" value="<?php echo htmlspecialchars($subcategory['Tendanhmuc']); ?>" required>
                    <button type="submit" name="update_subcategory">Cập nhật</button>
                    <input type="hidden" name="subcategory_id" value="<?php echo $subcategory['DanhmucID']; ?>">
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <form action="../categories.php" method="get">
        <button type="submit" class="exit-button">Thoát</button>
    </form>
</div>

</body>
</html>
