<?php require_once 'templates/header.php'; ?>
<?php require_once 'core/database/db.php'; ?>

<div class="container">
    <h2>Каталог товаров</h2>

    <!-- Форма фильтрации и сортировки -->
    <form method="GET" action="">
        <input type="hidden" name="page" value="catalog">

        <!-- Фильтр по категории -->
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

        <!-- Фильтр по цене -->
        <input type="number" name="min_price" placeholder="Цена от" value="<?= $_GET['min_price'] ?? '' ?>" onchange="this.form.submit()">
        <input type="number" name="max_price" placeholder="Цена до" value="<?= $_GET['max_price'] ?? '' ?>" onchange="this.form.submit()">

        <!-- Фильтр по размеру -->
        <select name="size" onchange="this.form.submit()">
            <option value="">Все размеры</option>
            <?php
            $stmt = $pdo->query("SELECT DISTINCT size FROM products WHERE size IS NOT NULL AND size != ''");
            while ($size = $stmt->fetch()):
                $selected = ($_GET['size'] ?? '') == $size['size'] ? 'selected' : '';
            ?>
                <option value="<?= htmlspecialchars($size['size']) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($size['size']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- Фильтр по материалу -->
        <select name="material" onchange="this.form.submit()">
            <option value="">Все материалы</option>
            <?php
            $stmt = $pdo->query("SELECT DISTINCT material FROM products WHERE material IS NOT NULL AND material != ''");
            while ($material = $stmt->fetch()):
                $selected = ($_GET['material'] ?? '') == $material['material'] ? 'selected' : '';
            ?>
                <option value="<?= htmlspecialchars($material['material']) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($material['material']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- Сортировка -->
        <select name="sort" onchange="this.form.submit()">
            <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Сначала новые</option>
            <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Цена: по возрастанию</option>
            <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Цена: по убыванию</option>
        </select>

        <!-- Поле поиска -->
        <input type="text" name="search" placeholder="Поиск по товарам" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">🔍</button>
    </form>

    <div class="products">
        <?php
        // Фильтрация
        $filters = [];
        $params = [];

        if (!empty($_GET['category'])) {
            $filters[] = "category = :category";
            $params['category'] = $_GET['category'];
        }

        if (!empty($_GET['min_price'])) {
            $filters[] = "price >= :min_price";
            $params['min_price'] = $_GET['min_price'];
        }

        if (!empty($_GET['max_price'])) {
            $filters[] = "price <= :max_price";
            $params['max_price'] = $_GET['max_price'];
        }

        if (!empty($_GET['size'])) {
            $filters[] = "size = :size";
            $params['size'] = $_GET['size'];
        }

        if (!empty($_GET['material'])) {
            $filters[] = "material = :material";
            $params['material'] = $_GET['material'];
        }

        if (!empty($_GET['search'])) {
            $filters[] = "(title LIKE :search OR description LIKE :search)";
            $params['search'] = "%" . $_GET['search'] . "%";
        }

        $where = !empty($filters) ? "WHERE " . implode(" AND ", $filters) : "";

        // Сортировка
        $order_by = "ORDER BY created_at DESC"; // по умолчанию - новые
        if (!empty($_GET['sort'])) {
            if ($_GET['sort'] == "price_asc") {
                $order_by = "ORDER BY price ASC";
            } elseif ($_GET['sort'] == "price_desc") {
                $order_by = "ORDER BY price DESC";
            }
        }

        $stmt = $pdo->prepare("SELECT * FROM products $where $order_by");
        $stmt->execute($params);

        while ($product = $stmt->fetch()):
            $images = json_decode($product['images'], true);
            $image_path = !empty($images[0]) ? "/znahidka/img/products/" . htmlspecialchars($images[0]) : "/znahidka/img/no-image.png";
        ?>
            <div class="product">
                <img src="<?= $image_path ?>" width="200" alt="<?= htmlspecialchars($product['title']) ?>">
                <h4><?= htmlspecialchars($product['title']) ?></h4>
                <p>Цена: <?= htmlspecialchars($product['price']) ?> грн</p>
                <a href="/znahidka/?page=product&id=<?= $product['id'] ?>">Подробнее</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
