<?php

use Tinhl\Bai01QuanlySv\Core\FlashMessage;

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '\\') {
    $scriptDir = '';
}

$avatarBaseUrl = $scriptDir . '/uploads/avatars';
$defaultAvatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/default-avatar.png';
$defaultAvatarUrl = $avatarBaseUrl . '/default-avatar.png';
$isEditing = !empty($editingStudent);
$formAction = $isEditing ? 'index.php?action=update' : 'index.php?action=add';
$formTitle = $isEditing ? 'Sửa thông tin sinh viên' : 'Thêm sinh viên mới';
$submitLabel = $isEditing ? 'Cập nhật' : 'Lưu sinh viên';
$helperText = $isEditing
    ? 'Chọn ảnh mới nếu muốn thay avatar hiện tại. Bỏ trống để giữ nguyên.'
    : 'Ảnh đại diện là tùy chọn. Hỗ trợ JPG, PNG, GIF, WEBP, tối đa 2MB.';
$editName = $editingStudent['name'] ?? '';
$editEmail = $editingStudent['email'] ?? '';
$editPhone = $editingStudent['phone'] ?? '';
$editAvatar = $editingStudent['avatar'] ?? '';
$editAvatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/' . $editAvatar;
$editAvatarUrl = $avatarBaseUrl . '/' . rawurlencode($editAvatar);
$editCourse = $editingStudent['course'] ?? '';
$editClassName = $editingStudent['class_name'] ?? '';
$editMajor = $editingStudent['major'] ?? '';
$currentPage = (int) ($currentPage ?? 1);
$totalPages = (int) ($totalPages ?? 1);
$totalStudents = (int) ($totalStudents ?? 0);
$listStart = (int) ($listStart ?? 0);
$listEnd = (int) ($listEnd ?? 0);
$keyword = (string) ($keyword ?? '');
$sortby = (string) ($sortby ?? 'id');
$order = strtolower((string) ($order ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$nextOrder = $order === 'asc' ? 'desc' : 'asc';
$showStudentForm = $isEditing;
$formContainerVisibilityClass = $showStudentForm ? 'flex' : 'hidden';
$openButtonVisibilityClass = $showStudentForm ? 'hidden' : 'inline-flex';

$buildListUrl = static function (int $page, ?string $sortColumn = null, ?string $sortDirection = null) use ($keyword, $sortby, $order): string {
    $params = [];
    $sortColumn = $sortColumn ?? $sortby;
    $sortDirection = $sortDirection ?? $order;

    if ($page > 1) {
        $params['page'] = $page;
    }

    if ($keyword !== '') {
        $params['keyword'] = $keyword;
    }

    if ($sortColumn !== 'id' || $sortDirection !== 'desc') {
        $params['sortby'] = $sortColumn;
        $params['order'] = $sortDirection;
    }

    return 'index.php' . (!empty($params) ? '?' . http_build_query($params) : '');
};

$buildActionUrl = static function (string $action, int $id) use ($currentPage, $keyword, $sortby, $order): string {
    $params = [
        'action' => $action,
        'id' => $id,
    ];

    if ($currentPage > 1) {
        $params['page'] = $currentPage;
    }

    if ($keyword !== '') {
        $params['keyword'] = $keyword;
    }

    if ($sortby !== 'id' || $order !== 'desc') {
        $params['sortby'] = $sortby;
        $params['order'] = $order;
    }

    return 'index.php?' . http_build_query($params);
};

$sortIndicator = static function (string $column) use ($sortby, $order): string {
    if ($sortby !== $column) {
        return '↕';
    }

    return $order === 'asc' ? '↑' : '↓';
};

$cancelUrl = $buildListUrl($currentPage);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Quản lý sinh viên</title>
</head>

<body class="min-h-screen bg-stone-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div id="flash-messages" class="mb-6 space-y-3">
            <?php FlashMessage::display(); ?>
        </div>

        <header class="mb-8 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <div class="bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.14),_transparent_38%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.12),_transparent_34%)] p-6 sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <span class="inline-flex rounded-full border border-slate-200 bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Student Hub
                        </span>
                        <h1 class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                            Students Management System
                        </h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                            Theo dõi, tìm kiếm và cập nhật hồ sơ sinh viên trong một giao diện gọn gàng,
                            rõ ràng và tập trung vào dữ liệu.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <a
                            href="index.php?action=dashboard"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            Xem thống kê
                        </a>
                        <button
                            type="button"
                            id="open-student-form-button"
                            class="<?php echo $openButtonVisibilityClass; ?> items-center rounded-full bg-slate-950 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                            Thêm sinh viên mới
                        </button>
                        <a
                            href="index.php?action=logout"
                            class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                            Đăng xuất
                        </a>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Tổng sinh viên</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950"><?php echo $totalStudents; ?></p>
                        <p class="mt-1 text-sm text-slate-500">Hồ sơ đang có trong hệ thống</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Hiển thị</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">
                            <?php echo $totalStudents > 0 ? $listStart . '-' . $listEnd : '0'; ?>
                        </p>
                        <p class="mt-1 text-sm text-slate-500">Phạm vi bản ghi của trang hiện tại</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-white/90 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Chế độ xem</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950"><?php echo $currentPage; ?>/<?php echo $totalPages; ?></p>
                        <p class="mt-1 text-sm text-slate-500">
                            <?php echo $keyword !== '' ? "Đang lọc theo từ khóa: " . htmlspecialchars($keyword) : 'Đang xem toàn bộ dữ liệu'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <section class="mb-6 rounded-[2rem] border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <form action="index.php" method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="keyword"
                        placeholder="Tìm kiếm theo tên sinh viên..."
                        value="<?php echo htmlspecialchars($keyword); ?>"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:bg-white">
                    <input type="hidden" name="sortby" value="<?php echo htmlspecialchars($sortby); ?>">
                    <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                </div>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800">
                    Tìm kiếm
                </button>
            </form>
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Bảng sinh viên</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        <?php if ($totalStudents > 0): ?>
                            Hiển thị <?php echo $listStart; ?>-<?php echo $listEnd; ?> trên tổng <?php echo $totalStudents; ?> sinh viên
                        <?php else: ?>
                            Chưa có sinh viên nào trong hệ thống
                        <?php endif; ?>
                    </p>
                </div>
                <div class="text-sm text-slate-500">
                    Sắp xếp hiện tại: <span class="font-medium text-slate-700"><?php echo strtoupper(htmlspecialchars($sortby)); ?></span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                            <?php foreach (['id' => 'ID', 'name' => 'Họ và tên', 'email' => 'Email', 'phone' => 'Số điện thoại'] as $column => $label): ?>
                                <?php $currentColOrder = $sortby === $column ? $nextOrder : 'asc'; ?>
                                <th class="px-4 py-4 sm:px-6">
                                    <a
                                        href="<?php echo htmlspecialchars($buildListUrl($currentPage, $column, $currentColOrder)); ?>"
                                        class="inline-flex items-center gap-2 transition hover:text-slate-900">
                                        <span><?php echo $label; ?></span>
                                        <span class="<?php echo $sortby === $column ? 'text-slate-900' : 'text-slate-300'; ?>">
                                            <?php echo $sortIndicator($column); ?>
                                        </span>
                                    </a>
                                </th>
                            <?php endforeach; ?>
                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 sm:px-6">Avatar</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 sm:px-6">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($students as $student): ?>
                            <?php
                            $avatarFile = $student['avatar'] ?? '';
                            $avatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/' . $avatarFile;
                            $avatarUrl = $avatarBaseUrl . '/' . rawurlencode($avatarFile);
                            $editUrl = $buildActionUrl('edit', (int) $student['id']);
                            $deleteUrl = $buildActionUrl('delete', (int) $student['id']);
                            $detailUrl = $buildActionUrl('detail', (int) $student['id']);
                            $avatarLabel = strtoupper(substr((string) ($student['name'] ?? 'N'), 0, 1));
                            ?>
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-4 py-4 text-sm font-medium text-slate-600 sm:px-6">#<?php echo $student['id']; ?></td>
                                <td class="px-4 py-4 sm:px-6">
                                    <div class="min-w-[180px]">
                                        <p class="font-medium text-slate-900"><?php echo htmlspecialchars($student['name']); ?></p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            <?php echo htmlspecialchars($student['major'] ?? 'Chưa cập nhật ngành học'); ?>
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600 sm:px-6"><?php echo htmlspecialchars($student['email']); ?></td>
                                <td class="px-4 py-4 text-sm text-slate-600 sm:px-6"><?php echo htmlspecialchars($student['phone']); ?></td>
                                <td class="px-4 py-4 sm:px-6">
                                    <?php if (!empty($avatarFile) && is_file($avatarAbsolutePath)): ?>
                                        <img
                                            src="<?php echo htmlspecialchars($avatarUrl); ?>"
                                            alt="Avatar của <?php echo htmlspecialchars($student['name']); ?>"
                                            class="h-11 w-11 rounded-2xl object-cover ring-1 ring-slate-200">
                                    <?php elseif (is_file($defaultAvatarAbsolutePath)): ?>
                                        <img
                                            src="<?php echo htmlspecialchars($defaultAvatarUrl); ?>"
                                            alt="Avatar mặc định"
                                            class="h-11 w-11 rounded-2xl object-cover ring-1 ring-slate-200">
                                    <?php else: ?>
                                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-sm font-semibold text-slate-500">
                                            <?php echo htmlspecialchars($avatarLabel); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    <div class="flex flex-wrap items-center gap-3 text-sm font-medium">
                                        <a href="<?php echo htmlspecialchars($editUrl); ?>" class="text-sky-600 transition hover:text-sky-800">
                                            Sửa
                                        </a>
                                        <a
                                            href="<?php echo htmlspecialchars($deleteUrl); ?>"
                                            onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?');"
                                            class="text-rose-600 transition hover:text-rose-800">
                                            Xóa
                                        </a>
                                        <a href="<?php echo htmlspecialchars($detailUrl); ?>" class="text-emerald-600 transition hover:text-emerald-800">
                                            Chi tiết
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center sm:px-6">
                                    <div class="mx-auto max-w-md">
                                        <p class="text-lg font-medium text-slate-900">Không có dữ liệu phù hợp</p>
                                        <p class="mt-2 text-sm text-slate-500">
                                            Hãy thử tìm kiếm với từ khóa khác hoặc thêm sinh viên mới để bắt đầu.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="flex flex-col gap-4 border-t border-slate-200 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="text-sm text-slate-500">
                        Trang <?php echo $currentPage; ?> / <?php echo $totalPages; ?>
                    </p>
                    <div class="flex flex-wrap items-center gap-2">
                        <?php if ($currentPage > 1): ?>
                            <a
                                href="<?php echo htmlspecialchars($buildListUrl($currentPage - 1)); ?>"
                                class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                                Trước
                            </a>
                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                            <a
                                href="<?php echo htmlspecialchars($buildListUrl($page)); ?>"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-medium transition <?php echo $page === $currentPage ? 'bg-slate-950 text-white' : 'border border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50'; ?>">
                                <?php echo $page; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a
                                href="<?php echo htmlspecialchars($buildListUrl($currentPage + 1)); ?>"
                                class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                                Sau
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div
        id="student-form-container"
        class="<?php echo $formContainerVisibilityClass; ?> fixed inset-0 z-50 items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
        <form
            id="student-form"
            action="<?php echo htmlspecialchars($formAction); ?>"
            method="POST"
            enctype="multipart/form-data"
            class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[2rem] border border-slate-200 bg-white p-6 shadow-2xl sm:p-8">
            <input type="hidden" name="page" value="<?php echo $currentPage; ?>">
            <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            <input type="hidden" name="sortby" value="<?php echo htmlspecialchars($sortby); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">

            <?php if ($isEditing): ?>
                <input type="hidden" name="id" value="<?php echo (int) $editingStudent['id']; ?>">
            <?php endif; ?>

            <div class="flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">
                        <?php echo $isEditing ? 'Chế độ chỉnh sửa' : 'Thêm hồ sơ mới'; ?>
                    </p>
                    <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">
                        <?php echo htmlspecialchars($formTitle); ?>
                    </h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        <?php echo htmlspecialchars($helperText); ?>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <?php if (!$isEditing): ?>
                        <button
                            type="button"
                            id="close-student-form-button"
                            class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                            Đóng form
                        </button>
                    <?php endif; ?>
                    <?php if ($isEditing): ?>
                        <a
                            href="<?php echo htmlspecialchars($cancelUrl); ?>"
                            class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                            Hủy sửa
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
                <div class="space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Họ và tên</span>
                            <input
                                type="text"
                                name="name"
                                value="<?php echo htmlspecialchars($editName); ?>"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Email</span>
                            <input
                                type="email"
                                name="email"
                                value="<?php echo htmlspecialchars($editEmail); ?>"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                        </label>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-3">
                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Số điện thoại</span>
                            <input
                                type="text"
                                name="phone"
                                value="<?php echo htmlspecialchars($editPhone); ?>"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Khóa học</span>
                            <input
                                type="text"
                                name="course"
                                value="<?php echo htmlspecialchars($editCourse); ?>"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Tên lớp</span>
                            <input
                                type="text"
                                name="class_name"
                                value="<?php echo htmlspecialchars($editClassName); ?>"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                        </label>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-700">Ngành học</span>
                        <input
                            type="text"
                            name="major"
                            value="<?php echo htmlspecialchars($editMajor); ?>"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                    </label>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Ảnh đại diện</p>
                            <p class="mt-1 text-sm leading-6 text-slate-500">Tải lên ảnh hồ sơ rõ nét để bảng dữ liệu trực quan hơn.</p>
                        </div>
                        <?php if (!empty($editAvatar) && is_file($editAvatarAbsolutePath)): ?>
                            <img src="<?php echo htmlspecialchars($editAvatarUrl); ?>" alt="Avatar hiện tại" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-slate-200">
                        <?php elseif (is_file($defaultAvatarAbsolutePath)): ?>
                            <img src="<?php echo htmlspecialchars($defaultAvatarUrl); ?>" alt="Avatar mặc định" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-slate-200">
                        <?php else: ?>
                            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-sm font-semibold text-slate-400 ring-1 ring-slate-200">N/A</span>
                        <?php endif; ?>
                    </div>

                    <label class="mt-5 block">
                        <span class="mb-2 block text-sm font-medium text-slate-700">Chọn tệp</span>
                        <input
                            type="file"
                            name="avatar"
                            accept=".jpg,.jpeg,.png,.gif,.webp,image/*"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800">
                    </label>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-full bg-slate-950 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-800">
                    <?php echo htmlspecialchars($submitLabel); ?>
                </button>
                <span class="text-sm text-slate-500">Dữ liệu sẽ được lưu ngay sau khi xác nhận.</span>
            </div>
        </form>
    </div>

    <script>
        const flashMessages = document.querySelectorAll('.flash-message');
        const studentFormContainer = document.getElementById('student-form-container');
        const studentForm = document.getElementById('student-form');
        const openStudentFormButton = document.getElementById('open-student-form-button');
        const closeStudentFormButton = document.getElementById('close-student-form-button');

        flashMessages.forEach((message) => {
            message.classList.add(
                'rounded-3xl',
                'border',
                'px-4',
                'py-3',
                'text-sm',
                'font-medium',
                'shadow-sm',
                'transition',
                'duration-500'
            );

            if (message.classList.contains('flash-success')) {
                message.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
            } else {
                message.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-700');
            }
        });

        if (flashMessages.length > 0) {
            setTimeout(() => {
                flashMessages.forEach((message) => {
                    message.style.opacity = '0';

                    setTimeout(() => {
                        message.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        }

        const openStudentForm = () => {
            if (!studentFormContainer || !openStudentFormButton) {
                return;
            }

            studentFormContainer.classList.remove('hidden');
            studentFormContainer.classList.add('flex');
            openStudentFormButton.classList.add('hidden');
            openStudentFormButton.classList.remove('inline-flex');
        };

        const closeStudentForm = () => {
            if (!studentFormContainer || !studentForm || !openStudentFormButton) {
                return;
            }

            studentForm.reset();
            studentFormContainer.classList.add('hidden');
            studentFormContainer.classList.remove('flex');
            openStudentFormButton.classList.remove('hidden');
            openStudentFormButton.classList.add('inline-flex');
        };

        if (studentFormContainer && openStudentFormButton) {
            openStudentFormButton.addEventListener('click', openStudentForm);
        }

        if (studentFormContainer && studentForm && openStudentFormButton && closeStudentFormButton) {
            closeStudentFormButton.addEventListener('click', closeStudentForm);

            studentFormContainer.addEventListener('click', (event) => {
                if (event.target === studentFormContainer) {
                    closeStudentForm();
                }
            });
        }
    </script>
</body>

</html>
