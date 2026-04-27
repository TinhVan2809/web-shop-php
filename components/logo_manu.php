<div class="logo-slider overflow-hidden py-10 bg-white border-y border-gray-100 mb-10">
    <div class="logo-track flex w-max">
        <!-- Nhóm logo 1 -->
        <div class="flex gap-16 items-center px-8">
            <img loading="lazy" src="../asset/logo/download (1).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (2).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (3).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (4).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (6).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (7).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (8).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (9).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (10).webp" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
        </div>
        <!-- Nhóm logo 2 (Nhân bản để tạo hiệu ứng lặp vô tận) -->
        <div class="flex gap-16 items-center px-8">
            <img loading="lazy" src="../asset/logo/download (1).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (2).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (3).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (4).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (6).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (7).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (8).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (9).png" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
            <img loading="lazy" src="../asset/logo/download (10).webp" class="w-32 object-contain grayscale hover:grayscale-0 transition-all cursor-pointer">
        </div>
    </div>
</div>

<style>
    .logo-track {
        animation: scroll 40s linear infinite;
    }

    @keyframes scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* Dừng lại khi di chuột vào */
    .logo-slider:hover .logo-track {
        animation-play-state: paused;
    }
</style>
