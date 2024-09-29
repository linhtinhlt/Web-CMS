function openLoginModal() {
    var loginModal = document.getElementById("loginModal");
    loginModal.style.display = "block";
  
}
function openregisterModal() {
    var loginModal = document.getElementById("registerModal");
    loginModal.style.display = "block";
  
}
function goBackRegister() {
    var registerModal = document.getElementById("registerModal");
    registerModal.style.display = "none";
}

function goBackLogin() {
    var loginModal = document.getElementById("loginModal");
    loginModal.style.display = "none";
}
function transferLogin() {
    var loginModal = document.getElementById("loginModal");
    var registerModal = document.getElementById("registerModal");
    registerModal.style.display = "none";
    loginModal.style.display = "block";
}

function transferRegister() {
    var loginModal = document.getElementById("loginModal");
    var registerModal = document.getElementById("registerModal");
    registerModal.style.display = "block";
    loginModal.style.display = "none";
}

window.addEventListener('scroll', function() {
    var element = document.getElementById('scrollElement');
    if (window.scrollY > 330) { // Điều chỉnh giá trị này
        element.classList.add('fixed');
    } else {
        element.classList.remove('fixed');
    }
});
function validateRegistration() {
    // Xóa thông báo lỗi cũ
    clearErrors();

    // Lấy giá trị từ các trường form
    const fullName = document.getElementById("fullName").value.trim();
    const email = document.getElementById("registerEmail").value;
    const password = document.getElementById("registerPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    let isValid = true; // Biến để kiểm tra nếu tất cả dữ liệu đều hợp lệ

    // Kiểm tra tên đầy đủ
    if (fullName.length < 3) {
        document.getElementById("nameError").innerText = "Tên phải có ít nhất 3 ký tự!";
        isValid = false;
    }

    // Kiểm tra email phải có đuôi @gmail.com
    if (!validateEmail(email)) {
        document.getElementById("emailError").innerText = "Email phải có đuôi @gmail.com!";
        isValid = false;
    }

    // Kiểm tra mật khẩu có ít nhất 6 ký tự và ít nhất một ký tự đặc biệt
    if (!validatePassword(password)) {
        document.getElementById("passwordError").innerText = "Mật khẩu phải có ít nhất 6 ký tự và chứa ít nhất một ký tự đặc biệt!";
        isValid = false;
    }

    // Kiểm tra mật khẩu khớp nhau
    if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").innerText = "Mật khẩu không khớp!";
        isValid = false;
    }

    // Nếu tất cả hợp lệ, cho phép gửi form
    if (isValid) {
        console.log("Form is valid, submitting form.");
        document.getElementById("registerForm").submit();
    } else {
        console.log("Form is invalid, not submitting form.");
    }
}

function validateEmail(email) {
    // Kiểm tra xem email có đuôi @gmail.com hay không
    return email.endsWith("@gmail.com");
}

function validatePassword(password) {
    // Kiểm tra mật khẩu có ít nhất 6 ký tự và ít nhất một ký tự đặc biệt
    const regex = /^(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{6,}$/;
    return regex.test(password);
}

function clearErrors() {
    // Xóa thông báo lỗi
    document.getElementById("nameError").innerText = "";
    document.getElementById("emailError").innerText = "";
    document.getElementById("passwordError").innerText = "";
    document.getElementById("confirmPasswordError").innerText = "";
}


function updateDateTime() {
    const now = new Date();

    // Manually get each part of the date and time
    const daysOfWeek = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
    const dayOfWeek = daysOfWeek[now.getDay()];

    const day = now.getDate();
    const month = now.getMonth() + 1; // Months are zero-indexed
    const year = now.getFullYear();

    const hours = String(now.getHours()).padStart(2, '0'); // Pad with leading zero if necessary
    const minutes = String(now.getMinutes()).padStart(2, '0');
    // Format the date and time string
    const dateTimeString = `${dayOfWeek}, ${day}/${month}/${year}, ${hours}:${minutes}`;

    // Update the date and time in the HTML
    document.getElementById('datetime').textContent = dateTimeString;
}
// Update the date and time every second
setInterval(updateDateTime, 1000);






