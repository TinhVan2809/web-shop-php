<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet" />
    <title>Đăng ký - Haseki Store</title>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Tạo tài khoản</h1>
            <p class="text-gray-500 mt-2">Tham gia cùng Haseki Store ngay hôm nay</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm border border-red-100">
                <i class="ri-error-warning-line mr-1"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?action=handleRegister" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Họ và tên *</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black outline-none transition-all" placeholder="Nguyễn Văn A">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tên đăng nhập *</label>
                    <input type="text" name="username" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black outline-none transition-all" placeholder="username123">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Giới tính</label>
                    <select name="gender" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black outline-none transition-all">
                        <option value="1">Nam</option>
                        <option value="2">Nữ</option>
                        <option value="0">Khác</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu *</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black outline-none transition-all" placeholder="••••••••">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" name="gmail" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black outline-none transition-all" placeholder="vi-du@gmail.com">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Số điện thoại</label>
                <input type="number" name="number_phone" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-black outline-none transition-all" placeholder="0987654321">
            </div>

            <button type="submit" class="w-full bg-black text-white py-3 rounded-lg font-bold hover:bg-gray-800 transform transition-active active:scale-[0.98] mt-6">
                Đăng ký ngay
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm">
            <span class="text-gray-500">Đã có tài khoản?</span>
            <a href="index.php?action=login" class="text-black font-bold hover:underline ml-1">Đăng nhập</a>
        </div>
        
        <div class="mt-4 text-center">
            <a href="index.php" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                <i class="ri-arrow-left-line"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
</body>
</html>