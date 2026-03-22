<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sinh viên</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
    }

    .container {
        max-width: 800px;
        margin: auto;
    }

    form {
        margin-bottom: 20px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    form input {
        display: block;
        margin-bottom: 10px;
        width:

            95%;
        padding: 8px;
    }

    form button {
        padding: 10px 15px;
        background-color:
            #28a745;
        color: white;
        border: none;
        cursor: pointer;
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
    }

    th {
        background-color: #f2f2f2;
    }
</style>

<body>
    <div class="container">
        <div class="container">
            <h1>
                <?php
                // Nếu có biến $keyword (tức là đang tìm kiếm), thì


                if (isset($keyword) && !empty($keyword)) {
                    echo "Kết quả tìm kiếm cho: '" .

                        htmlspecialchars($keyword) . "'";
                } else {
                    // Nếu không thì hiển thị tiêu đề mặc định
                    echo "Danh sách sinh viên";
                }
                ?>
            </h1>
            <form action="index.php" method="GET"

                style="margin-bottom: 20px;">

                <input type="text" name="keyword" placeholder="Tìm

kiếm theo tên..."

                    value="<?php echo htmlspecialchars($keyword ??

                                ''); ?>">

                <button type="submit">Tìm kiếm</button>
            </form>
            <table>
            </table>
        </div>
        <form action="index.php?action=add" method="POST">
            <h3>Thêm sinh viên mới</h3>
            <input type="text" name="name" placeholder="Họ và

Tên" required>

            <input type="email" name="email" placeholder="Email"

                required>

            <input type="text" name="phone" placeholder="Số điện

thoại" required>

            <button type="submit">Thêm mới</button>
        </form>
        <h2>Danh sách sinh viên</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>

                    Bài mẫu

                    <th>Họ và Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo
                            htmlspecialchars($student['name']); ?></td>
                        <td><?php echo
                            htmlspecialchars($student['email']); ?></td>
                        <td><?php echo
                            htmlspecialchars($student['phone']); ?></td>

                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="4">Chưa có sinh viên

                            nào.</td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>