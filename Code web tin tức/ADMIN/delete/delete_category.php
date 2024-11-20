<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 0) {
    header("Location: ../../HTML/login.html");
    exit();
}

require '../../DATABASE/connect.php';

if (isset($_GET['id'])) {
    $category_id = intval($_GET['id']);

    $childrenQuery = "SELECT DanhmucID, TenDanhmuc FROM Danhmuc WHERE ParentID = ?";
    $stmt = $conn->prepare($childrenQuery);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $childrenResult = $stmt->get_result();
    $hasChildren = $childrenResult->num_rows > 0;
    $stmt->close();

    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'delete_parent') {
            $deleteChildrenSql = "DELETE FROM Danhmuc WHERE ParentID = ?";
            $stmt = $conn->prepare($deleteChildrenSql);
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $stmt->close();

            $sql = "DELETE FROM Danhmuc WHERE DanhmucID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $category_id);

            if ($stmt->execute()) {
                header("Location: ../categories.php");
                exit();
            } else {
                echo "Có lỗi xảy ra khi xóa danh mục!";
            }
            $stmt->close();
        } elseif ($_GET['confirm'] == 'delete_child' && isset($_GET['child_id'])) {
            $child_id = intval($_GET['child_id']);
            $deleteChildSql = "DELETE FROM Danhmuc WHERE DanhmucID = ?";
            $stmt = $conn->prepare($deleteChildSql);
            $stmt->bind_param("i", $child_id);

            if ($stmt->execute()) {
                header("Location: ../categories.php");
                exit();
            } else {
                echo "Có lỗi xảy ra khi xóa danh mục con!";
            }
            $stmt->close();
        }
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Xác nhận xóa danh mục</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    background-color: #f3f4f6;
                }

                .confirm-box {
                    max-width: 500px;
                    background-color: #ffffff;
                    border-radius: 12px;
                    padding: 30px;
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                    text-align: center;
                }

                .confirm-box h2 {
                    font-size: 1.5em;
                    color: #333;
                    margin-bottom: 20px;
                }

                .confirm-box button, .confirm-box a {
                    padding: 12px 20px;
                    border: none;
                    border-radius: 6px;
                    font-size: 1em;
                    cursor: pointer;
                    margin: 10px;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }

                .confirm-box .delete-parent {
                    background-color: #ff4d4f;
                    color: #fff;
                }

                .confirm-box .delete-parent:hover {
                    background-color: #ff1a1d;
                }

                .dropdown-container {
                    position: relative;
                    display: inline-block;
                }

                .dropdown-button {
                    background-color: #ffa940;
                    color: #fff;
                    padding: 12px 20px;
                    border-radius: 8px;
                    font-size: 0.9em;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .dropdown-button:hover {
                    background-color: #ff7a1a;
                }

                .dropdown-options {
                    display: none;
                    position: absolute;
                    top: 100%;
                    left: 0;
                    width: 100%;
                    background-color: #fff;
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                    border-radius: 8px;
                    overflow: hidden;
                    z-index: 1;
                }

                .dropdown-container:hover .dropdown-options {
                    display: block;
                }

                .dropdown-option {
                    padding: 10px 20px;
                    text-align: left;
                    background-color: #ffa940;
                    color: #fff;
                    font-size: 0.9em;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                .dropdown-option:hover {
                    background-color: #ff7a1a;
                }

                .cancel-container {
                    margin-top: 20px; 
                    text-align: center; 
                }

                .cancel {
                    background-color: #ccc;
                    color: #333;
                    padding: 12px 20px;
                    border-radius: 6px;
                    text-decoration: none;
                }

                .cancel:hover {
                    background-color: #b3b3b3;
                }

            </style>
        </head>
        <body>
        <div class="confirm-box">
            <h2>Bạn có chắc chắn muốn xóa danh mục này không?</h2>
            
            <?php if ($hasChildren): ?>
                <form method="GET" action="../delete/delete_category.php" style="margin-bottom: 20px;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($category_id); ?>">
                    <input type="hidden" name="confirm" value="delete_parent">
                    <button type="submit" class="delete-parent">Xóa danh mục cha và tất cả danh mục con</button>
                </form>
                
                <h3>Chọn một danh mục con để xóa:</h3>
                <div class="dropdown-container">
                    <div class="dropdown-button">Chọn danh mục con</div>
                    <div class="dropdown-options">
                        <?php while ($child = $childrenResult->fetch_assoc()): ?>
                            <form method="GET" action="../delete/delete_category.php">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($category_id); ?>">
                                <input type="hidden" name="confirm" value="delete_child">
                                <input type="hidden" name="child_id" value="<?php echo htmlspecialchars($child['DanhmucID']); ?>">
                                <button type="submit" class="dropdown-option"><?php echo htmlspecialchars($child['TenDanhmuc']); ?></button>
                            </form>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php else: ?>
                <form method="GET" action="../delete/delete_category.php">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($category_id); ?>">
                    <input type="hidden" name="confirm" value="delete_parent">
                    <button type="submit" class="delete-parent">Xóa danh mục</button>
                </form>
            <?php endif; ?>

            <div class="cancel-container">
                <a href="../categories.php" class="cancel">Hủy bỏ</a>
            </div>

        </div>
        </body>
        </html>
        <?php
    }
} else {
    echo "ID danh mục không được cung cấp!";
}

$conn->close();
?>
