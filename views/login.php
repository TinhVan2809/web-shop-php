<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Haseki Store</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8f9fa] flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-[1100px] bg-white rounded-[32px] shadow-2xl shadow-blue-100/50 flex overflow-hidden min-h-[600px]">
        <!-- Left Side: Decorative -->
        <div class="hidden lg:flex w-1/2 bg-black p-12 flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500 rounded-full blur-[120px] opacity-20 -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500 rounded-full blur-[120px] opacity-20 -ml-32 -mb-32"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-black">
                        <i class="ri-flashlight-fill text-xl"></i>
                    </div>
                    <span class="text-white text-2xl font-bold tracking-tight">Haseki Store</span>
                </div>
                <h1 class="text-5xl font-bold text-white leading-tight">Nâng tầm phong cách <br><span class="text-gray-400">của bạn.</span></h1>
            </div>

            <div class="relative z-10">
                <p class="text-gray-400 max-w-sm mb-8 italic">"Thời trang không chỉ là những gì bạn mặc, mà còn là cách bạn khẳng định bản thân với thế giới."</p>
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-black bg-gray-300"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-black bg-gray-400"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-black bg-gray-500"></div>
                    </div>
                    <span class="text-gray-300 text-sm font-medium">+2k khách hàng tin dùng</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 p-12 md:p-20 flex flex-col justify-center">
            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Chào mừng trở lại!</h2>
                <p class="text-gray-500">Vui lòng nhập thông tin để truy cập tài khoản.</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl flex items-center gap-3 text-sm animate-pulse">
                    <i class="ri-error-warning-line text-lg"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="index.php?action=handleLogin" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 ml-1">Tên đăng nhập</label>
                    <div class="relative">
                        <i class="ri-user-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="text" name="username" required
                            class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black transition-all"
                            placeholder="username">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 ml-1">Mật khẩu</label>
                    <div class="relative">
                        <i class="ri-lock-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="password" name="password" required
                            class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:outline-none focus:ring-2 focus:ring-black/5 focus:border-black transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm py-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black">
                        <span class="text-gray-600 group-hover:text-black transition-colors">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="font-bold text-black hover:underline decoration-2 underline-offset-4">Quên mật khẩu?</a>
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-black text-white rounded-2xl font-bold text-lg shadow-xl shadow-black/10 hover:shadow-black/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    Đăng nhập ngay
                </button>
            </form>

            <div class="mt-12 text-center">
                <p class="text-gray-500">Chưa có tài khoản? <a href="#" class="font-bold text-black hover:underline decoration-2 underline-offset-4">Đăng ký miễn phí</a></p>
            </div>
        </div>
    </div>
</body>
</html>
