<main class="container mx-auto px-7 py-10 mt-20">
    <div class="flex flex-col lg:flex-row gap-10">
        <!-- Checkout Form -->
        <div class="lg:w-2/3">
            <h1 class="text-3xl font-bold mb-8">Thanh toán</h1>
            
            <form action="index.php?action=process_checkout" method="POST" id="checkout-form">
                
                <!-- Shipping Address -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8 shadow-sm">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><i class="ri-map-pin-line text-blue-600"></i> Địa chỉ giao hàng</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên người nhận</label>
                            <input type="text" name="recipient_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                            <input type="text" name="recipient_phone" value="<?php echo htmlspecialchars($user['number_phone'] ?? ''); ?>" required class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ Email (Để nhận biên lai)</label>
                            <input type="email" name="recipient_email" value="<?php echo htmlspecialchars($user['gmail'] ?? ''); ?>" required class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành phố</label>
                            <input type="text" name="province_name" id="province" list="province_list" required class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <datalist id="province_list"></datalist>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện</label>
                            <input type="text" name="district_name" id="district" list="district_list" required class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <datalist id="district_list"></datalist>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phường/Xã</label>
                            <input type="text" name="ward_name" id="ward" list="ward_list" required class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <datalist id="ward_list"></datalist>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ cụ thể (Số nhà, đường...)</label>
                            <input type="text" name="specific_address" required class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8 shadow-sm">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><i class="ri-truck-line text-green-600"></i> Phương thức vận chuyển</h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="shipping_method" value="standard" class="w-5 h-5 text-blue-600" checked onchange="updateShipping(30000)">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Giao hàng tiêu chuẩn</span>
                                <span class="block text-sm text-gray-500">Dự kiến giao trong 3-5 ngày</span>
                            </div>
                            <span class="font-bold">30.000₫</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="shipping_method" value="fast" class="w-5 h-5 text-blue-600" onchange="updateShipping(50000)">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Giao hàng nhanh</span>
                                <span class="block text-sm text-gray-500">Dự kiến giao trong 1-2 ngày</span>
                            </div>
                            <span class="font-bold">50.000₫</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="shipping_method" value="pickup" class="w-5 h-5 text-blue-600" onchange="updateShipping(0)">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Nhận tại cửa hàng</span>
                                <span class="block text-sm text-gray-500">Đến trực tiếp cửa hàng để nhận</span>
                            </div>
                            <span class="font-bold text-green-600">Miễn phí</span>
                        </label>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8 shadow-sm">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2"><i class="ri-secure-payment-line text-purple-600"></i> Phương thức thanh toán</h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="payment_method" value="cod" class="w-5 h-5 text-blue-600" checked>
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Thanh toán khi nhận hàng (COD)</span>
                            </div>
                            <i class="ri-cash-line text-2xl text-gray-400"></i>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="payment_method" value="vnpay" class="w-5 h-5 text-blue-600">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Thanh toán trực tuyến (VNPay)</span>
                            </div>
                            <img src="https://vnpay.vn/wp-content/uploads/2020/07/Logo-VNPAYQR-update.png" class="h-6 object-contain">
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="payment_method" value="paypal" class="w-5 h-5 text-blue-600">
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Thanh toán qua PayPal</span>
                            </div>
                            <i class="ri-paypal-fill text-2xl text-blue-700"></i>
                        </label>
                    </div>
                </div>

            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-1/3">
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 sticky top-24 shadow-sm">
                <h2 class="text-xl font-bold mb-6 pb-4 border-b border-gray-200">Thông tin đơn hàng</h2>
                
                <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2">
                    <?php foreach ($items as $item): ?>
                        <?php $price = $item['variant_price'] ?: ($item['discount_price'] ?? $item['price']); ?>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-gray-200 text-xs flex items-center justify-center font-bold shrink-0"><?php echo $item['quantity']; ?></span>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-600 line-clamp-1 font-medium"><?php echo htmlspecialchars($item['name'] ?? 'Sản phẩm'); ?></span>
                                    <?php if (!empty($item['variant_details'])): ?>
                                        <span class="text-[10px] text-gray-400 italic"><?php echo htmlspecialchars($item['variant_details']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="font-medium text-sm shrink-0"><?php echo number_format($price * $item['quantity'], 0, ',', '.'); ?>₫</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="space-y-4 text-gray-600 mb-6 pb-6 border-y border-gray-200 pt-4">
                    <div class="flex justify-between">
                        <span>Tạm tính</span>
                        <span class="font-medium"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Thuế (10%)</span>
                        <span class="font-medium"><?php echo number_format($tax, 0, ',', '.'); ?>₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí vận chuyển</span>
                        <span class="font-medium" id="display-shipping">30.000₫</span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-8">
                    <span class="font-bold text-lg">Tổng cộng</span>
                    <span class="font-bold text-3xl text-red-600" id="display-total"><?php echo number_format($subtotal + $tax + 30000, 0, ',', '.'); ?>₫</span>
                </div>
                
                <button type="button" onclick="document.getElementById('checkout-form').submit();" class="w-full bg-black text-white py-4 rounded-full font-bold hover:bg-gray-800 transition-colors text-lg shadow-lg flex justify-center items-center gap-2">
                    <i class="ri-check-line text-xl"></i> Đặt Hàng Ngay
                </button>
            </div>
        </div>
    </div>
</main>

<script>
    const subtotal = <?php echo $subtotal; ?>;
    const tax = <?php echo $tax; ?>;

    function formatCurrency(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + '₫';
    }

    function updateShipping(fee) {
        document.getElementById('display-shipping').textContent = fee === 0 ? 'Miễn phí' : formatCurrency(fee);
        const total = subtotal + tax + fee;
        document.getElementById('display-total').textContent = formatCurrency(total);
    }

    // Dữ liệu gợi ý 5 Thành phố trực thuộc trung ương
    const locationData = {
        "Hà Nội": {
            "Quận Ba Đình": ["Phường Phúc Xá", "Phường Trúc Bạch", "Phường Vĩnh Phúc", "Phường Cống Vị", "Phường Liễu Giai"],
            "Quận Hoàn Kiếm": ["Phường Phúc Tân", "Phường Đồng Xuân", "Phường Hàng Mã", "Phường Hàng Buồm", "Phường Hàng Đào"],
            "Quận Đống Đa": ["Phường Cát Linh", "Phường Văn Miếu", "Phường Quốc Tử Giám", "Phường Láng Thượng", "Phường Ô Chợ Dừa"],
            "Quận Cầu Giấy": ["Phường Nghĩa Đô", "Phường Nghĩa Tân", "Phường Mai Dịch", "Phường Dịch Vọng", "Phường Quan Hoa"],
            "Quận Thanh Xuân": ["Phường Thanh Xuân Bắc", "Phường Thanh Xuân Trung", "Phường Thượng Đình", "Phường Khương Trung"]
        },
        "TP Hồ Chí Minh": {
            "Quận 1": ["Phường Tân Định", "Phường Đa Kao", "Phường Bến Nghé", "Phường Bến Thành", "Phường Nguyễn Thái Bình"],
            "Quận 3": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường 5", "Phường Võ Thị Sáu"],
            "Quận 10": ["Phường 1", "Phường 2", "Phường 4", "Phường 9", "Phường 12", "Phường 14"],
            "Quận Bình Thạnh": ["Phường 1", "Phường 2", "Phường 3", "Phường 5", "Phường 7", "Phường 11", "Phường 15"],
            "Thành phố Thủ Đức": ["Phường Linh Trung", "Phường Linh Chiểu", "Phường Bình Thọ", "Phường Hiệp Phú", "Phường Tăng Nhơn Phú A"]
        },
        "Đà Nẵng": {
            "Quận Hải Châu": ["Phường Hải Châu I", "Phường Hải Châu II", "Phường Thạch Thang", "Phường Thuận Phước", "Phường Hòa Thuận Tây"],
            "Quận Thanh Khê": ["Phường Tam Thuận", "Phường Thanh Khê Tây", "Phường Thanh Khê Đông", "Phường Xuân Hà"],
            "Quận Sơn Trà": ["Phường Thọ Quang", "Phường Nại Hiên Đông", "Phường Mân Thái", "Phường An Hải Bắc", "Phường Phước Mỹ"]
        },
        "Hải Phòng": {
            "Quận Hồng Bàng": ["Phường Quán Toan", "Phường Hùng Vương", "Phường Sở Dầu", "Phường Thượng Lý"],
            "Quận Ngô Quyền": ["Phường Máy Chai", "Phường Máy Tơ", "Phường Vạn Mỹ", "Phường Cầu Tre"],
            "Quận Lê Chân": ["Phường Cát Dài", "Phường An Biên", "Phường Trại Cau", "Phường Hàng Kênh"]
        },
        "Cần Thơ": {
            "Quận Ninh Kiều": ["Phường Cái Khế", "Phường An Hòa", "Phường Thới Bình", "Phường An Nghiệp"],
            "Quận Bình Thủy": ["Phường Bình Thủy", "Phường Trà An", "Phường Trà Nóc", "Phường Thới An Đông"],
            "Quận Cái Răng": ["Phường Lê Bình", "Phường Hưng Phú", "Phường Hưng Thạnh", "Phường Ba Láng"]
        }
    };

    const provinceInput = document.getElementById('province');
    const districtInput = document.getElementById('district');
    const wardInput = document.getElementById('ward');
    
    const provinceList = document.getElementById('province_list');
    const districtList = document.getElementById('district_list');
    const wardList = document.getElementById('ward_list');

    // Populate Provinces
    Object.keys(locationData).forEach(city => {
        let option = document.createElement('option');
        option.value = city;
        provinceList.appendChild(option);
    });

    // Handle Province change
    provinceInput.addEventListener('input', function() {
        districtList.innerHTML = '';
        wardList.innerHTML = '';
        if(locationData[this.value]) {
            Object.keys(locationData[this.value]).forEach(district => {
                let option = document.createElement('option');
                option.value = district;
                districtList.appendChild(option);
            });
        }
    });

    // Handle District change
    districtInput.addEventListener('input', function() {
        wardList.innerHTML = '';
        const city = provinceInput.value;
        if(locationData[city] && locationData[city][this.value]) {
            locationData[city][this.value].forEach(ward => {
                let option = document.createElement('option');
                option.value = ward;
                wardList.appendChild(option);
            });
        }
    });
</script>
