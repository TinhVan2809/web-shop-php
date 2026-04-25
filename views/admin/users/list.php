<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-2xl font-bold">Danh sách người dùng</h2>
        <p class="text-gray-500 text-sm mt-1">Quản lý khách hàng và nhân viên hệ thống</p>
    </div>
    <a href="index.php?action=user_form" class="bg-black text-white px-5 py-2.5 rounded-xl font-medium flex items-center gap-2 hover:bg-gray-800 transition-all">
        <i class="ri-user-add-line"></i>
        Thêm tài khoản
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Người dùng</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Vai trò / Trạng thái</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Số điện thoại</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center font-bold text-gray-600">
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900"><?= $user['name'] ?></p>
                                <p class="text-xs text-gray-500"><?= $user['gmail'] ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <?php 
                            $roleClass = $user['role'] == 'admin' ? 'bg-red-50 text-red-600' : ($user['role'] == 'staff' ? 'bg-blue-50 text-blue-600' : 'bg-gray-50 text-gray-600');
                            $statusClass = $user['status'] == 'locked' ? 'bg-yellow-50 text-yellow-700' : 'bg-green-50 text-green-700';
                            ?>
                            <span class="w-fit px-2 py-0.5 rounded-full text-[10px] font-bold <?= $roleClass ?>">
                                <?= strtoupper($user['role']) ?>
                            </span>
                            <span class="w-fit px-2 py-0.5 rounded-full text-[10px] font-bold <?= $statusClass ?>">
                                <?= strtoupper($user['status']) ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?= $user['number_phone'] ?: '---' ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?= date('d/m/Y', strtotime($user['create_at'])) ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="index.php?action=user_form&id=<?= $user['user_id'] ?>" 
                               class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Sửa">
                                <i class="ri-edit-line"></i>
                            </a>
                            <a href="index.php?action=toggle_user_status&id=<?= $user['user_id'] ?>" 
                               class="w-8 h-8 flex items-center justify-center <?= $user['status'] == 'locked' ? 'text-green-600 bg-green-50' : 'text-gray-400 bg-gray-50' ?> hover:text-red-600 rounded-lg transition-all" 
                               title="<?= $user['status'] == 'locked' ? 'Mở khóa' : 'Khóa' ?>">
                                <i class="<?= $user['status'] == 'locked' ? 'ri-lock-unlock-line' : 'ri-lock-line' ?>"></i>
                            </a>
                            <a href="index.php?action=delete_user&id=<?= $user['user_id'] ?>" 
                               onclick="return confirm('Xóa vĩnh viễn tài khoản này?')"
                               class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
