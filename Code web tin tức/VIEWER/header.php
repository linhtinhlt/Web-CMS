<?php

if (!isset($_SESSION)) {
    session_start();
}

$baivietID = isset($_GET['BaivietID']) && !empty($_GET['BaivietID']) ? $_GET['BaivietID'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VietNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/css.css">
    <link rel="stylesheet" href="../CSS/base.css">
</head>
<body>

<header class="border-bottom">
        <div id="carouselExample" class="col-md-12 carousel slide mt-3" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://dienmaythienphu.vn/wp-content/uploads/2023/12/cong-trinh-pc-14122023.jpg" class="img-fluid w-100" style="height: 220px" alt="Quảng cáo 1">
                </div>
                <div class="carousel-item">
                    <img src="https://dienmaythienphu.vn/wp-content/uploads/2023/02/cong-trinh-pc-4122023.jpg" class="img-fluid w-100" style="height: 220px" alt="Quảng cáo 2">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="col-3">
            <div class="d-flex align-items-center flex-shrink-0">
                <img src="../img/logo.png" alt="Logo" class="img-fluid" style="width: 200px; height: 90px; object-fit: cover;">
            </div>
        </div>

        <div class="col-3 linkitem">
            <div class="flex-grow-1 mx-4">
                <form action="search.php" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_query" placeholder="Tìm kiếm..." aria-label="Tìm kiếm">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-5 col-12 mt-2 linkitem">
            <div class="d-flex align-items-center language-links">
                <!-- Thêm input hidden để truyền baivietID -->
                <input type="hidden" id="baiviet_id" value="<?php echo htmlspecialchars($baivietID); ?>">

                <button id="toEnglish" class="btn no-translate">
                    <img src="../img/usa-flag.png" alt="US" class="flag-icon"> EN
                </button>
                <button id="toVietnamese" class="btn no-translate">
                    <img src="../img/vn.png" alt="Vietnam" class="flag-icon"> VN
                </button>

                <div class="flex align-items-center">
                    <?php if (isset($_SESSION['username'])): ?>
                        <span class="no-translate me-2 text-decoration-none link-with-divider login-link">
                            <i class="bi bi-person me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                            <a href="../ADMIN/dashboard.php" class="text-decoration-none link-with-divider text-dark">Quản trị</a>
                        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                            <a href="../VIEWER/profile.php" class="text-decoration-none link-with-divider text-dark">Hồ sơ</a>
                        <?php endif; ?>
                        <a href="../VIEWER/logout.php" class="text-decoration-none text-dark">Đăng xuất</a>
                    <?php else: ?>
                        <a href="../HTML/login.html" class="text-decoration-none link-with-divider text-dark">Đăng nhập</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<?php
require '../DATABASE/connect.php';

$sql = "SELECT * FROM Danhmuc ORDER BY ParentID, DanhmucID";
$result = $conn->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$menu = [];
foreach ($categories as $category) {
    if ($category['ParentID'] === NULL) {
        $menu[$category['DanhmucID']] = [
            'name' => $category['Tendanhmuc'],
            'children' => []
        ];
    } else {
        $menu[$category['ParentID']]['children'][] = $category;
    }
}

?>

<nav class="navbar navbar-expand-lg" style="background-color: #891d1d; position: sticky; top: 0; z-index: 1000;">
    <div class="container-fluid">

        <a class="navbar-brand text-white" href="home.php"> <i class="bi bi-house-door"></i> </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <?php
            // Hiển thị menu
            echo'<a class="nav-link text-white" href="home.php" data-translate="Home">Trang chủ</a>';
            foreach ($menu as $parentID => $parentCategory) {
            
                echo '<li class="nav-item dropdown">';
                echo '<a class="nav-link dropdown-toggle text-white" href="news_cate.php?category=' . htmlspecialchars($parentCategory['name']) . '" id="navbarDropdown' . $parentID . '" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . htmlspecialchars($parentCategory['name']) . '</a>';
                echo '<ul class="dropdown-menu" aria-labelledby="navbarDropdown' . $parentID . '">';
                
                foreach ($parentCategory['children'] as $childCategory) {
                    echo '<li><a class="dropdown-item" href="news_cate.php?category=' . htmlspecialchars($childCategory['Tendanhmuc']) . '">' . htmlspecialchars($childCategory['Tendanhmuc']) . '</a></li>';
                }

                echo '</ul>';
                echo '</li>';
            }
            ?>
        </ul>
        </div>

    </div>

</nav>  

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function getTextNodes(node) {
    const textNodes = [];
    function recursiveSearch(node) {
        if (node.nodeType === Node.ELEMENT_NODE && (node.tagName === 'SCRIPT' || node.tagName === 'STYLE')) {
            return;
        }
        if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim()) {
            if (!node.parentElement.classList.contains('no-translate')) {
                textNodes.push(node);
            }
        } else {
            node.childNodes.forEach(child => recursiveSearch(child));
        }
    }
    recursiveSearch(node);
    return textNodes;
}


function translatePage(toLanguage, fromLanguage, translatedData = null) {
    const textNodes = getTextNodes(document.body); 
    if (translatedData) {
        const { TieudeDich, TomtatDich, NoidungDich } = translatedData;

        const titleNode = document.querySelector('.article-title');
        const summaryNode = document.querySelector('.article-summary');
        const contentNode = document.querySelector('.article-content');

       
        if (titleNode) titleNode.textContent = TieudeDich || titleNode.textContent;
        if (summaryNode) summaryNode.textContent = TomtatDich || summaryNode.textContent;
        if (contentNode) contentNode.textContent = NoidungDich || contentNode.textContent;

       
        const otherNodes = textNodes.filter(node => 
            node !== titleNode && node !== summaryNode && node !== contentNode && node.nodeValue.trim() !== ''
        );
        const otherTexts = otherNodes.map(node => node.nodeValue.trim());

        if (otherTexts.length > 0) {
            const formData = new FormData();
            formData.append('text', JSON.stringify(otherTexts));
            formData.append('to', toLanguage);
            formData.append('from', fromLanguage);

            fetch('../API/translate.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data && data[0] && data[0].translations) {
                    otherNodes.forEach((node, index) => {
                        if (data[index] && data[index].translations[0]) {
                            node.nodeValue = data[index].translations[0].text;
                        }
                    });
                } else {
                    console.error('Dịch thất bại:', data);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
            });
        }
    } else {
        const texts = textNodes.map(node => node.nodeValue.trim());
        if (texts.length === 0) {
            console.warn("Không có văn bản để dịch.");
            return;
        }

        const formData = new FormData();
        formData.append('text', JSON.stringify(texts));
        formData.append('to', toLanguage);
        formData.append('from', fromLanguage);

        fetch('../API/translate.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data && data[0] && data[0].translations) {
                textNodes.forEach((node, index) => {
                    if (data[index] && data[index].translations[0]) {
                        node.nodeValue = data[index].translations[0].text;
                    }
                });
            } else {
                console.error('Dịch thất bại:', data);
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
        });
    }
}

document.getElementById('toEnglish').addEventListener('click', () => {
    const baivietID = document.getElementById('baiviet_id') ? document.getElementById('baiviet_id').value : null; // Lấy baivietID nếu có

    if (baivietID) {
        fetch('../API/save_trans.php', {
            method: 'POST',
            body: new URLSearchParams({
                'baivietID': baivietID, 
                'language': 'en' 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Bài viết đã được dịch thành công:', data.message);
                if (data.data) {
                    translatePage('en', 'vi', data.data); 
                } else {
                    translatePage('en', 'vi');  
                }
            } else {
                console.error('Lỗi:', data.message);
            }
        })
        .catch(error => {
            console.error('Lỗi khi gửi yêu cầu dịch:', error);
        });
    } else {
      
        translatePage('en', 'vi');
    }
});

document.getElementById('toVietnamese').addEventListener('click', () => {
    const baivietID = document.getElementById('baiviet_id') ? document.getElementById('baiviet_id').value : null; 

    if (baivietID) {
        fetch('../API/save_trans.php', {
            method: 'POST',
            body: new URLSearchParams({
                'baivietID': baivietID,  
                'language': 'vi'  
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Bài viết đã được dịch thành công:', data.message);
                if (data.data) {
                    translatePage('vi', 'en', data.data); 
                } else {
                    translatePage('vi', 'en');  
                }
            } else {
                console.error('Lỗi:', data.message);
            }
        })
        .catch(error => {
            console.error('Lỗi khi gửi yêu cầu dịch:', error);
        });
    } else {
        translatePage('vi', 'en');
    }
});


</script>


<script>
    document.querySelectorAll('.navbar-nav .dropdown > a.dropdown-toggle').forEach(function(element) {
        element.addEventListener('click', function(event) {

            if (this.getAttribute('href') !== '#') {
                
                const dropdownMenu = this.nextElementSibling; 
                if (dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                }
              
                window.location.href = this.getAttribute('href');
            }
            
            event.preventDefault(); 
        });
    });
</script>

</body>
</html>
