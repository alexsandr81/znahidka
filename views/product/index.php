<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$product_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='container'><h2>❌ Товар не найден</h2></div>";
    require_once 'templates/footer.php';
    exit;
}

// Загружаем изображения
$images = !empty($product['images']) ? json_decode($product['images'], true) : [];
$image_dir = "/znahidka/img/products/";
$default_image = "/znahidka/img/no-image.png";

// Проверяем, есть ли товар в избранном
$favorites = $_SESSION['favorites'] ?? [];
$is_favorite = in_array($product_id, $favorites);
?>

<!-- ✅ Подключение Swiper.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- ✅ Основной контейнер -->
<div class="container product-container">
    <!-- ✅ Блок изображений -->
    <div class="product-images">
        <!-- ✅ Главное изображение -->
        <img id="mainImage" class="main-image" 
             src="<?= !empty($images) ? $image_dir . htmlspecialchars($images[0]) : $default_image ?>" 
             alt="Фото <?= htmlspecialchars($product['title']) ?>"
             onclick="openModal(this.src)">

        <!-- ✅ Слайдер миниатюр -->
        <div class="swiper thumbnails-swiper">
            <div class="swiper-wrapper">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $image): ?>
                        <?php 
                        $image_path = $image_dir . htmlspecialchars($image);
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                            $image_path = $default_image;
                        }
                        ?>
                        <div class="swiper-slide">
                            <img class="thumbnail" 
                                 src="<?= $image_path ?>" 
                                 alt="Миниатюра <?= htmlspecialchars($product['title']) ?>"
                                 onclick="changeMainImage('<?= $image_path ?>')">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- Кнопки навигации -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>

    <!-- ✅ Описание товара -->
    <div class="product-info">
        <h2><?= htmlspecialchars($product['title']) ?></h2>
        <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <p><strong>Размер:</strong> <?= htmlspecialchars($product['size']) ?></p>
        <p><strong>Материал:</strong> <?= htmlspecialchars($product['material']) ?></p>
        <p><strong>Артикул:</strong> <?= htmlspecialchars($product['sku']) ?></p>
        <p><strong>Цена:</strong> <?= htmlspecialchars($product['price']) ?> грн</p>

        <!-- Кнопка "Добавить в избранное" -->
        <form method="post" action="/znahidka/core/favorites/toggle_favorite.php">
            <input type="hidden" name="product_id" value="<?= $product_id ?>">
            <button type="submit" class="favorite-btn">
                <?= $is_favorite ? "💔 Убрать из избранного" : "❤️ Добавить в избранное" ?>
            </button>
        </form>

        <!-- Кнопка "Добавить в корзину" -->
        <form method="post" action="/znahidka/core/cart/add_to_cart.php">
            <input type="hidden" name="product_id" value="<?= $product_id ?>">
            <button type="submit">🛒 Добавить в корзину</button>
        </form>
    </div>
</div>

<!-- ✅ Модальное окно для изображения -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<!-- ✅ Скрипты -->
<script>
    function changeMainImage(newSrc) {
        document.getElementById('mainImage').src = newSrc;
    }

    function openModal(imageSrc) {
        let modal = document.getElementById("imageModal");
        let modalImg = document.getElementById("modalImage");

        modal.style.display = "block";
        modalImg.src = imageSrc;
    }

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    // Закрытие по клику вне изображения
    window.onclick = function(event) {
        let modal = document.getElementById("imageModal");
        if (event.target === modal) {
            closeModal();
        }
    }

    // ✅ Инициализация Swiper (миниатюры)
    document.addEventListener("DOMContentLoaded", function () {
        new Swiper('.thumbnails-swiper', {
            slidesPerView: 3,  // Отображаем 3 миниатюры
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    });
</script>

<?php require_once 'templates/footer.php'; ?>
