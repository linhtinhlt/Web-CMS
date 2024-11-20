<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../HTML/login.html");
    exit();
}

require '../DATABASE/connect.php';


$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tendanhmuc = $_POST['tendanhmuc'];
    $parentid = $_POST['parentid'];
    $checkSql = "SELECT * FROM Danhmuc WHERE Tendanhmuc = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $tendanhmuc);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $message = "<p style='color: red;'>Lỗi: Tên danh mục đã tồn tại.</p>";
    } else {
        if ($parentid === "") {
            $parentid = NULL;  
        }

        $sql = "INSERT INTO Danhmuc (Tendanhmuc, ParentID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $tendanhmuc, $parentid);

        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Thêm danh mục thành công.</p>";
        } else {
            $message = "<p style='color: red;'>Lỗi khi thêm danh mục: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $checkStmt->close();
}


$sql = "SELECT * FROM Danhmuc ORDER BY ParentID, DanhmucID";
$result = $conn->query($sql);


$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/manage.css">
    <style>
        form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 8px;
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
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .child-category {
            padding-left: 20px;
        }
        
    </style>
</head>
<body>

<?php require 'dashboard.php'; ?>
<div class="main-content">
    <h1>Quản lý danh mục</h1>
    
    <h2>Danh sách danh mục</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục cha</th>
                <th>Tên danh mục con</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $parentCategories = [];
            
            foreach ($categories as $category) {
                if ($category['ParentID'] === null) {
                    
                    $parentCategories[$category['DanhmucID']] = [
                        'name' => $category['Tendanhmuc'],
                        'children' => []
                    ];
                } else {
                    $parentCategories[$category['ParentID']]['children'][] = $category;
                }
            }
            foreach ($parentCategories as $parentID => $parentCategory) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($parentID) . "</td>";
                echo "<td>" . htmlspecialchars($parentCategory['name']) . "</td>";
                echo "<td>";
                if (!empty($parentCategory['children'])) {
                    echo "<ul>";
                    foreach ($parentCategory['children'] as $childCategory) {
                        echo "<li class='child-category'>" . htmlspecialchars($childCategory['Tendanhmuc']) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "Không có";
                }
                echo "</td>";
                echo "<td>
                        <a href='./edit/edit_category.php?id=" . htmlspecialchars($parentID) . "' class='btn-edit'>Chỉnh sửa</a>
                        <a href='./delete/delete_category.php?id=" . htmlspecialchars($parentID) . "' class='btn-delete'>Xóa</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>


    <h2>Thêm danh mục mới</h2>
    <form method="POST" action="">
        <div>
            <label for="tendanhmuc">Tên danh mục:</label>
            <input type="text" id="tendanhmuc" name="tendanhmuc" required>
        </div>
        <div>
            <label for="parentid">Danh mục cha:</label>
            <select id="parentid" name="parentid">
                <option value="">Chọn danh mục cha</option>
                <?php
                $parentQuery = "SELECT * FROM Danhmuc WHERE ParentID IS NULL";
                $parentResult = $conn->query($parentQuery);
                while ($parent = $parentResult->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($parent['DanhmucID']) . "'>" . htmlspecialchars($parent['Tendanhmuc']) . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit">Thêm danh mục</button>
    </form>

    <?php echo $message; ?>
</div>
</body>
</html>
