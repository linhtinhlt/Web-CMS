<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title></title> 
    <style>
    
        footer {
            background-color: #f8f9fa;
            color: black;
            padding-top: 20px;
            padding-bottom: 10px;
        }

        footer h5 {
            font-weight: bold;
            color: black;
            margin-bottom: 15px;
        }

        footer p, footer ul, footer li, footer a {
            font-size: 0.9rem;
            color: gray;
        }

        footer p {
            line-height: 1.6;
        }

        footer ul {
            padding: 0;
            list-style-type: none;
        }

        footer ul li {
            margin-bottom: 10px;
        }

        footer ul li a {
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer .social-icons a {
            font-size: 1.2rem;
            margin-right: 15px;
            transition: color 0.3s ease;
        }

        footer .social-icons a:hover {
            color: #ffffff;
        }

        footer .text-center p {
            font-size: 0.85rem;
            margin-top: 20px;
            border-top: 1px solid #555;
            padding-top: 10px;
        }

     </style>
</head>
<body>
    
<footer >
    <div class="container-lg mt-4">
        <div class="row">
            <!-- About Section -->
            <div class="col-md-4 mb-4">
                <h5>Về Chúng Tôi</h5>
                <p class="small">
                    Đây là trang tin tức của chúng tôi, cung cấp các tin tức cập nhật và chính xác nhất đến độc giả.
                </p>
            </div>

            <!-- Quick Links Section -->
            <div class="col-md-4 mb-4">
                <h5>Liên Kết Nhanh</h5>
                <ul class="list-unstyled">
                    <li><a href="about.php" class="">Giới thiệu</a></li>
                    <li><a href="terms.php" class="">Điều khoản dịch vụ</a></li>
                    <li><a href="privacy.php" class="">Chính sách bảo mật</a></li>
                    <li><a href="contact.php" class="">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Social Media Section -->
            <div class="col-md-4 mb-4">
                <h5>Theo Dõi Chúng Tôi</h5>
                <div>
                    <a href="https://facebook.com" target="_blank" class=" me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="https://twitter.com" target="_blank" class=" me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="https://instagram.com" target="_blank" class=""><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="small mb-0">&copy; <?php echo date("Y"); ?> Vietnews online</p>
        </div>
    </div>
</footer>

</body>
</html>
