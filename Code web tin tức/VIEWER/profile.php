<?php
include '../DATABASE/connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            border-right: 1px solid #ddd;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            padding-bottom: 20px;
        }

        .sidebar .nav-profile {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
           
        }

        .sidebar .nav-link-profile {
            font-size: 17px;
            padding: 20px 20px;
            color: #333;
            display: block;
            text-decoration: none ;
        }

        .sidebar .nav-link-profile.active {
            background-color: #f8f9fa;
            font-weight: bold;
            text-decoration: none; 
        }


        .sidebar .nav-link-profile i {
            margin-right: 10px;
        }

        .content {
            padding: 30px;
        }

        .container-fluid-profile {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding-top: 30px;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container-fluid-profile">
        <div class="row">
            <nav class="col-md-4 col-lg-3 d-md-block bg-white sidebar">
                <ul class="nav-profile flex-column">
                    <li class="nav-item-profile">
                        <a class="nav-link-profile" href="javascript:void(0);" onclick="loadPage('info-account')">
                            <i class="bi bi-person"></i> Thông tin tài khoản
                        </a>
                    </li>
                    <li class="nav-item-profile">
                        <a class="nav-link-profile" href="javascript:void(0);" onclick="loadPage('comment-activity')">
                            <i class="bi bi-chat-dots"></i> Hoạt động bình luận
                        </a>
                    </li>
                    <li class="nav-item-profile">
                        <a class="nav-link-profile" href="javascript:void(0);" onclick="loadPage('view-article')">
                            <i class="bi bi-eye"></i> Tin đã xem
                        </a>
                    </li>
                    <li class="nav-item-profile">
                        <a class="nav-link-profile" href="javascript:void(0);" onclick="loadPage('like_article')">
                            <i class="bi bi-bookmark"></i> Tin yêu thích
                        </a>
                    </li>
                    <li class="nav-item-profile">
                        <a class="nav-link-profile text-danger" href="../VIEWER/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-8 ms-sm-auto col-lg-9 px-md-4 content">
               
            </main>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <script>
        function loadPage(page) {
    
    window.history.pushState({path: page}, '', '?page=' + page);

    $.ajax({
        url: './edit_profile/' + page + '.php',
        type: 'GET',
        success: function(response) {
            $('.content').html(response);

            $('.nav-link-profile').removeClass('active'); 
            $('a[href="javascript:void(0);"][onclick="loadPage(\'' + page + '\')"]').addClass('active');
        },
        error: function() {
            alert('Không thể tải trang.');
        }
    });
}


window.onload = function() {
    var page = new URLSearchParams(window.location.search).get('page');
    if (page) {
        loadPage(page);
    } else {
        $('.content').html('<h1>Chọn mục để xem</h1>'); 
    }
}

    </script>
</body>

</html>
