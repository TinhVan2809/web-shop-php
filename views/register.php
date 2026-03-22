<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:
                #f4f4f4;
            display: flex;
            justify-content: center;
            align-items:
                center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h1 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;

        }

        .form-group input {
            width: 100%;
            padding: 8px;

            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color:
                #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor:
                pointer;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom:

                10px;
        }

        .switch-form {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Đăng ký tài khoản</h1>
        <?php if (isset($error)): ?>

            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="index.php?action=do_register"

            method="POST">

            <div class="form-group">
                <label for="name">Họ và Tên:</label>
                <input type="text" id="name" name="name"

                    required>

            </div>
            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username"

                    required>

            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password"

                    name="password" required>

            </div>
            <button type="submit">Đăng ký</button>
        </form>
        <div class="switch-form">
            Đã có tài khoản? <a

                href="index.php?action=login">Đăng nhập</a>

        </div>
    </div>
</body>

</html>