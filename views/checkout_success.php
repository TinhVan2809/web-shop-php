<main class="container mx-auto px-7 py-20 mt-20 text-center min-h-[60vh] flex flex-col items-center justify-center">
    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-6 mx-auto">
        <i class="ri-check-line text-5xl text-green-500"></i>
    </div>
    <h1 class="text-4xl font-bold mb-4 text-gray-800">Đặt hàng thành công!</h1>
    <p class="text-gray-600 mb-2">Cảm ơn bạn đã mua sắm tại Haseki Store.</p>
    <?php if (isset($_GET['order_code'])): ?>
        <p class="text-gray-600 mb-8">Mã đơn hàng của bạn là: <strong class="text-black text-xl"><?php echo htmlspecialchars($_GET['order_code']); ?></strong></p>
        <p class="text-gray-500 text-sm mb-8">Một email xác nhận đã được gửi đến hộp thư của bạn.</p>
    <?php endif; ?>
    
    <div class="flex justify-center gap-4">
        <a href="index.php" class="px-8 py-3 bg-black text-white rounded-full font-medium hover:bg-gray-800 transition-colors">Tiếp tục mua sắm</a>
        <?php if (isset($_GET['order_id'])): ?>
            <a href="index.php?action=orderDetail&id=<?php echo (int)$_GET['order_id']; ?>" class="px-8 py-3 bg-white text-black border border-gray-300 rounded-full font-medium hover:bg-gray-50 transition-colors">Xem đơn hàng</a>
        <?php else: ?>
            <a href="index.php?action=profile&id=<?php echo $_SESSION['user_id'] ?? ''; ?>" class="px-8 py-3 bg-white text-black border border-gray-300 rounded-full font-medium hover:bg-gray-50 transition-colors">Xem đơn hàng</a>
        <?php endif; ?>
    </div>
</main>
