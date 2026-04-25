<div class="max-w-2xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold"><?= $user ? 'Sửa tài khoản' : 'Thêm tài khoản mới' ?></h2>
            <p class="text-gray-500 text-sm mt-1">Quản lý thông tin và phân quyền người dùng</p>
        </div>
        <a href="index.php?action=admin_users" class="text-gray-500 hover:text-black">Quay lại</a>
    </div>

    <form action="index.php?action=save_user" method="POST" class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm space-y-6">
        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?? '' ?>">
        
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên</label>
                <input type="text" name="name" value="<?= $user['name'] ?? '' ?>" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="Nguyễn Văn A">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tên đăng nhập</label>
                <input type="text" name="username" value="<?= $user['username'] ?? '' ?>" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="username123">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mật khẩu <?= $user ? '(để trống nếu không đổi)' : '' ?></label>
                <input type="password" name="password" <?= $user ? '' : 'required' ?>
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="••••••••">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Quyền hạn (Role)</label>
                <select name="role" class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all">
                    <option value="customer" <?= (isset($user['role']) && $user['role'] == 'customer') ? 'selected' : '' ?>>Khách hàng (Customer)</option>
                    <option value="staff" <?= (isset($user['role']) && $user['role'] == 'staff') ? 'selected' : '' ?>>Nhân viên (Staff)</option>
                    <option value="admin" <?= (isset($user['role']) && $user['role'] == 'admin') ? 'selected' : '' ?>>Quản trị viên (Admin)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                <input type="email" name="gmail" value="<?= $user['gmail'] ?? '' ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="example@gmail.com">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                <input type="text" name="number_phone" value="<?= $user['number_phone'] ?? '' ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl focus:outline-none focus:border-black transition-all"
                    placeholder="09xxx">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="active" <?= (!isset($user['status']) || $user['status'] == 'active') ? 'checked' : '' ?>>
                    <span class="text-sm">Hoạt động</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="status" value="locked" <?= (isset($user['status']) && $user['status'] == 'locked') ? 'checked' : '' ?>>
                    <span class="text-sm">Bị khóa</span>
                </label>
            </div>
        </div>

        <button type="submit" class="w-full bg-black text-white py-4 rounded-2xl font-bold hover:bg-gray-800 transition-all shadow-lg shadow-black/10">
            Lưu thông tin tài khoản
        </button>
    </form>
</div>
