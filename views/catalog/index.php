<?php require_once 'templates/header.php'; ?>
<?php require_once 'core/database/db.php'; ?>

<div class="container">
    <h2>Каталог товаров</h2>

    <!-- Фильтр по категориям -->
    <form method="GET" action="">
        <input type="hidden" name="page" value="catalog">
        <select name="category" onchange="this.form.submit()">
            <option value="">Все категории</option>
            <?php
            $stmt = $pdo->query("SELECT DISTINCT category FROM products");
            while ($category = $stmt->fetch()):
                $selected = ($_GET['category'] ?? '') == $category['category'] ? 'selected' : '';
            ?>
                <option value="<?= htmlspecialchars($category['category']) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($category['category']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <div class="products">
        <?php
        // Фильтр по категории
        $category_filter = "";
        $params = [];
        if (!empty($_GET['category'])) {
            $category_filter = "WHERE category = :category";
            $params['category'] = $_GET['category'];
        }

        // Получаем товары
        $stmt = $pdo->prepare("SELECT * FROM products $category_filter ORDER BY created_at DESC");
        $stmt->execute($params);

        // Выводим товары
        while ($product = $stmt->fetch()):
        ?>
            <div class="product">
            <img src="/znahidka/img/products/<?= htmlspecialchars($product['image']) ?>" width="200" alt="<?= htmlspecialchars($product['title']) ?>">
                <h4><?= htmlspecialchars($product['title']) ?></h4>
                <p>Цена: <?= htmlspecialchars($product['price']) ?> грн</p>
                <a href="/znahidka/?page=product&id=<?= $product['id'] ?>">Подробнее</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
