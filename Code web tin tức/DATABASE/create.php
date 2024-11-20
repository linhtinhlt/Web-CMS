<?php

$servername = "localhost";
$username = "root"; 
$password = "";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Tạo cơ sở dữ liệu
$sql = "CREATE DATABASE IF NOT EXISTS trangtintuc";
if ($conn->query($sql) === TRUE) {
    echo "Tạo cơ sở dữ liệu thành công<br>";
} else {
    echo "Lỗi khi tạo cơ sở dữ liệu: " . $conn->error;
}

// Sử dụng cơ sở dữ liệu vừa tạo
$conn->select_db("trangtintuc");

// Tạo bảng Nguoidung
$sql = "CREATE TABLE IF NOT EXISTS Nguoidung (
    NguoidungID INT(11) AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(255) NOT NULL,
    NgaytaoTK TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    TrangthaiTK TINYINT(1) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng Nguoidung thành công<br>";
} else {
    echo "Lỗi khi tạo bảng Nguoidung: " . $conn->error . "<br>";
}
// Tạo bảng thông tin
$sql = "CREATE TABLE IF NOT EXISTS ThongTin (
    ThongtinID INT(11) AUTO_INCREMENT PRIMARY KEY,
    NguoidungID INT(11),
    HoTen VARCHAR(100),
    AnhDaidien VARCHAR(255),
    Email VARCHAR(100),
    Ngaycapnhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (NguoidungID) REFERENCES Nguoidung(NguoidungID)
)";

if ($conn->query($sql) === TRUE) {
    echo "Tạo lại bảng ThongTin thành công.<br>";
} else {
    echo "Lỗi khi tạo bảng ThongTin: " . $conn->error . "<br>";
}


// Tạo bảng Baiviet
$sql = "CREATE TABLE IF NOT EXISTS Baiviet (
    BaivietID INT(11) AUTO_INCREMENT PRIMARY KEY,
    Tieude VARCHAR(255),
    Tomtat TEXT,
    Noidung TEXT,
    Hinhanh VARCHAR(255),
    DanhmucID INT,
    Tacgia VARCHAR(100) NOT NULL,
    Ngaydang TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Ngaycapnhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng Baiviet thành công<br>";
} else {
    echo "Lỗi khi tạo bảng Baiviet: " . $conn->error . "<br>";
}

// Tạo bảng Danhmuc
$sql = "CREATE TABLE IF NOT EXISTS Danhmuc (
    DanhmucID INT(11) AUTO_INCREMENT PRIMARY KEY,
    Tendanhmuc VARCHAR(100) NOT NULL,
    ParentID INT(11),  
    Ngaytao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ParentID) REFERENCES Danhmuc(DanhmucID) ON DELETE SET NULL
)"; 
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng Danhmuc thành công<br>";
} else {
    echo "Lỗi khi tạo bảng Danhmuc: " . $conn->error . "<br>";
}


$sql = "CREATE TABLE IF NOT EXISTS Danhgia (
    DanhgiaID INT(11) AUTO_INCREMENT PRIMARY KEY,
    BaivietID INT(11),
    NguoidungID INT(11),
    Binhluan TEXT,
    TrangThai TINYINT(1) DEFAULT 0, 
    NgayBL TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (BaivietID) REFERENCES Baiviet(BaivietID) ON DELETE CASCADE,
    FOREIGN KEY (NguoidungID) REFERENCES Nguoidung(NguoidungID) ON DELETE CASCADE
)";
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng Danhgia thành công<br>";
} else {
    echo "Lỗi khi tạo bảng Danhgia: " . $conn->error . "<br>";
}



// Tạo bảng YeuThich
$sql = "CREATE TABLE IF NOT EXISTS YeuThich (
    YeuThichID INT(11) AUTO_INCREMENT PRIMARY KEY,
    NguoidungID INT(11) NOT NULL,
    BaivietID INT(11) NOT NULL,
    ThoiGianYeuThich TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (NguoidungID) REFERENCES Nguoidung(NguoidungID),
    FOREIGN KEY (BaivietID) REFERENCES Baiviet(BaivietID),
    UNIQUE (NguoidungID, BaivietID)
)";
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng YeuThich thành công<br>";
} else {
    echo "Lỗi khi tạo bảng YeuThich: " . $conn->error . "<br>";
}

// Tạo bảng BaiVietDaXem
$sql = "CREATE TABLE IF NOT EXISTS BaiVietDaXem (
    DaXemID INT(11) AUTO_INCREMENT PRIMARY KEY,
    NguoidungID INT(11) NOT NULL,
    BaivietID INT(11) NOT NULL,
    ThoiGianDaXem TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (NguoidungID) REFERENCES Nguoidung(NguoidungID),
    FOREIGN KEY (BaivietID) REFERENCES Baiviet(BaivietID) ON DELETE CASCADE ,
    UNIQUE (NguoidungID, BaivietID)
)";
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng BaiVietDaXem thành công<br>";
} else {
    echo "Lỗi khi tạo bảng BaiVietDaXem: " . $conn->error . "<br>";
}


$sql = "CREATE TABLE IF NOT EXISTS BaivietDich (
    BaivietDichID INT(11) AUTO_INCREMENT PRIMARY KEY,
    BaivietID INT(11) NOT NULL,
    NgonNgu VARCHAR(10) NOT NULL, 
    TieudeDich VARCHAR(255) NOT NULL,  
    TomtatDich TEXT NOT NULL,  
    NoidungDich TEXT NOT NULL,  
    FOREIGN KEY (BaivietID) REFERENCES Baiviet(BaivietID) ON DELETE CASCADE,
    UNIQUE (BaivietID, NgonNgu)  
)";
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng BaivietDich thành công<br>";
} else {
    echo "Lỗi khi tạo bảng BaivietDich: " . $conn->error . "<br>";
}


$conn->close();
?>
