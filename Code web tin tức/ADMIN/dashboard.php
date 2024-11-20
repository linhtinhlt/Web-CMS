<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VietNews Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/manage.css">
    <style>
        .sidebar {
            width: 250px;
            background-color: #f4f4f4;
            height: 100vh; 
            position: fixed;
            top: 0;
            left: 0;
            transition: transform 0.3s ease;
            transform: translateX(0); 
            z-index: 1000; 
        }

        .sidebar.hidden {
            transform: translateX(0); 
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease; 
        }

        .toggle-btn {
            display: none; 
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px; 
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); 
            }

            .main-content {
                margin-left: 0; 
            }

            .toggle-btn {
                display: block; 
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <h2>Quản lý</h2>
    <a href="../VIEWER/home.php">Trang chủ</a>
    <a href="users.php">Quản lý người dùng</a>
    <a href="articles.php">Quản lý bài viết</a>
    <a href="categories.php">Quản lý danh mục</a>
    <a href="comment.php">Quản lý bình luận</a>
    <a href="../VIEWER/logout.php">Đăng xuất</a>
</div>

<div class="main-content" id="main-content">
    <div class="header">
        <button class="toggle-btn" id="toggle-btn">☰</button>
        <h1>Chào mừng đến với Trang Quản trị</h1>
    </div>
   
</div>

<script src="../JAVASCRIPT/main.js"></script>

</body>
</html>
