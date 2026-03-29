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
$submitLabel = $isEditing ? 'Cập nhật' : 'Thêm mới';
$helperText = $isEditing
    ? 'Chọn ảnh mới nếu muốn thay avatar hiện tại. Bỏ trống để giữ nguyên.'
    : 'Ảnh đại diện là tùy chọn. Hỗ trợ JPG, PNG, GIF, WEBP, tối đa 2MB.';
$editName = $editingStudent['name'] ?? '';
$editEmail = $editingStudent['email'] ?? '';
$editPhone = $editingStudent['phone'] ?? '';
$editAvatar = $editingStudent['avatar'] ?? '';
$editAvatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/' . $editAvatar;
$editAvatarUrl = $avatarBaseUrl . '/' . rawurlencode($editAvatar);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .topbar,
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        form {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form input {
            display: block;
            width: 95%;
            margin-bottom: 10px;
            padding: 8px;
        }

        input[type="file"] {
            padding: 6px 0;
        }

        form button,
        .button-link,
        .action-link {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        form button {
            background-color: #28a745;
        }

        .button-link {
            background-color: #17a2b8;
        }

        .button-link.cancel {
            background-color: #6c757d;
        }

        .action-link {
            padding: 8px 12px;
            background-color: #007bff;
        }

        .action-cell {
            white-space: nowrap;
        }

        .current-avatar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .current-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
        }

        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .flash-success {
            background-color: #28a745;
        }

        .flash-error {
            background-color: #dc3545;
        }

        .avatar-image,
        .avatar-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .avatar-image {
            display: block;
            object-fit: cover;
        }

        .avatar-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #495057;
            font-size: 12px;
            font-weight: bold;
        }

        .helper-text {
            margin-top: -4px;
            margin-bottom: 12px;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php FlashMessage::display(); ?>
    </div>

    <div class="container">
        <div class="topbar" style="margin-bottom: 15px;">
            <div>
                Chào mừng, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
            </div>
            <a href="index.php?action=logout" class="button-link cancel">Đăng xuất</a>
        </div>

        <div class="toolbar" style="margin-bottom: 15px;">
            <h1 style="margin: 0;">
                <?php
                if (isset($keyword) && !empty($keyword)) {
                    echo "Kết quả tìm kiếm cho: '" . htmlspecialchars($keyword) . "'";
                } else {
                    echo 'Danh sách sinh viên';
                }
                ?>
            </h1>
            <a href="index.php?action=dashboard" class="button-link">Xem thống kê</a>
        </div>

        <form action="index.php" method="GET">
            <input type="text" name="keyword" placeholder="Tìm kiếm theo tên..." value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
            <button type="submit">Tìm kiếm</button>
        </form>

        <form action="<?php echo htmlspecialchars($formAction); ?>" method="POST" enctype="multipart/form-data">
            <h3><?php echo htmlspecialchars($formTitle); ?></h3>

            <?php if ($isEditing): ?>
                <input type="hidden" name="id" value="<?php echo (int) $editingStudent['id']; ?>">
                <div class="current-avatar">
                    <?php if (!empty($editAvatar) && is_file($editAvatarAbsolutePath)): ?>
                        <img src="<?php echo htmlspecialchars($editAvatarUrl); ?>" alt="Avatar hiện tại">
                    <?php elseif (is_file($defaultAvatarAbsolutePath)): ?>
                        <img src="<?php echo htmlspecialchars($defaultAvatarUrl); ?>" alt="Avatar mặc định">
                    <?php else: ?>
                        <span class="avatar-placeholder">N/A</span>
                    <?php endif; ?>
                    <span>Ảnh hiện tại</span>
                </div>
            <?php endif; ?>

            <input type="text" name="name" placeholder="Họ và tên" value="<?php echo htmlspecialchars($editName); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($editEmail); ?>" required>
            <input type="text" name="phone" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($editPhone); ?>" required>
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp,image/*">
            <div class="helper-text"><?php echo htmlspecialchars($helperText); ?></div>

            <button type="submit"><?php echo htmlspecialchars($submitLabel); ?></button>
            <?php if ($isEditing): ?>
                <a href="index.php" class="button-link cancel">Hủy sửa</a>
            <?php endif; ?>
        </form>

        <h2>Danh sách sinh viên</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh đại diện</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <?php
                    $avatarFile = $student['avatar'] ?? '';
                    $avatarAbsolutePath = __DIR__ . '/../public/uploads/avatars/' . $avatarFile;
                    $avatarUrl = $avatarBaseUrl . '/' . rawurlencode($avatarFile);
                    $editUrl = 'index.php?action=edit&id=' . (int) $student['id'];

                    if (!empty($keyword)) {
                        $editUrl .= '&keyword=' . urlencode($keyword);
                    }
                    ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td>
                            <?php if (!empty($avatarFile) && is_file($avatarAbsolutePath)): ?>
                                <img
                                    src="<?php echo htmlspecialchars($avatarUrl); ?>"
                                    alt="Avatar của <?php echo htmlspecialchars($student['name']); ?>"
                                    class="avatar-image">
                            <?php elseif (is_file($defaultAvatarAbsolutePath)): ?>
                                <img
                                    src="<?php echo htmlspecialchars($defaultAvatarUrl); ?>"
                                    alt="Avatar mặc định"
                                    class="avatar-image">
                            <?php else: ?>
                                <span class="avatar-placeholder">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                        <td class="action-cell">
                            <a href="<?php echo htmlspecialchars($editUrl); ?>" class="action-link">Sửa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6">Chưa có sinh viên nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        const flashMessages = document.querySelectorAll('.flash-message');

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
    </script>
</body>

</html>
