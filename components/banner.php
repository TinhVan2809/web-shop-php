<!-- Swiper Container -->
<div class="swiper bannerSwiper mt-35 w-full">
    <div class="swiper-wrapper">
        <?php if (isset($banners) && !empty($banners)): ?>
            <?php foreach ($banners as $banner): ?>
                <div class="swiper-slide relative">
                    <img src="../asset/<?php echo htmlspecialchars($banner['img']); ?>" class="w-full h-[600px] object-cover">
                    <div class="absolute inset-0 bg-black/20 flex px-10 md:px-55 py-10 justify-center items-center gap-6 flex-col text-center">
                        <p class="text-white text-lg md:text-xl max-w-2xl drop-shadow-md">
                            <?php echo htmlspecialchars($banner['content']); ?>
                        </p>
                        <a href="<?php echo htmlspecialchars($banner['url'] ?: '#'); ?>" 
                           class="border border-white px-6 py-2 text-white bg-black/30 backdrop-blur-sm duration-300 hover:bg-white hover:text-black font-semibold">
                            Shop Now
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper(".bannerSwiper", {
            loop: true,
            autoplay: {
                delay: 5000,
            },
        });
    });
</script>