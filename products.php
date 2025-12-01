<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

if (isset($_GET['fetch_categories'])) {
    $gid = intval($_GET['fetch_categories']);
    $stmt = $pdo->prepare("SELECT id, category_name FROM categories WHERE group_id = ?");
    $stmt->execute([$gid]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if (isset($_GET['fetch_subcategories'])) {
    $cid = intval($_GET['fetch_subcategories']);
    $stmt = $pdo->prepare("SELECT id, subcategory_name FROM subcategories WHERE category_id = ?");
    $stmt->execute([$cid]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$success = '';
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    if ($msg === 'deleted') $success = 'Product deleted successfully!';
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php?msg=deleted");
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch();
}

$groups = $pdo->query("SELECT id, name FROM groups_h ORDER BY name")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = intval($_POST['group_id'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $subcategory_id = intval($_POST['subcategory_id'] ?? 0);
    $product_name = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $original_price = trim($_POST['original_price'] ?? '');
    $deal_price = trim($_POST['deal_price'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;

    $image_url = $editData['image_url'] ?? '';

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image_file'];
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error during file upload. Error code: " . $file['error'];
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $errors[] = "Invalid image type. Allowed: jpg, jpeg, png, gif, webp.";
            }

            if ($file['size'] > 2*1024*1024) {
                $errors[] = "Image size must be under 2MB.";
            }

            $uploadDir = __DIR__ . '/uploads';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $errors[] = "Failed to create uploads directory.";
                }
            }

            if (empty($errors)) {
                $filename = uniqid('img_') . '.' . $ext;
                $target = $uploadDir . '/' . $filename;

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $image_url = 'uploads/' . $filename;
                } else {
                    $errors[] = "Failed to move uploaded file.";
                }
            }
        }
    }

    if ($group_id <= 0) $errors[] = "Please select a group.";
    if ($category_id <= 0) $errors[] = "Please select a category.";
    if ($product_name === '') $errors[] = "Product name cannot be empty.";
    if (!is_numeric($original_price)) $errors[] = "Original price must be numeric.";
    if ($deal_price !== '' && !is_numeric($deal_price)) $errors[] = "Deal price must be numeric.";
    if ($stock < 0) $errors[] = "Stock cannot be negative.";

    if (empty($errors)) {
        if ($editData) {
            $stmt = $pdo->prepare("
                UPDATE products SET 
                product_name=?, description=?, original_price=?, deal_price=?, image_url=?,
                group_id=?, category_id=?, subcategory_id=?, stock=?, is_on_sale=?
                WHERE id=?
            ");
            $stmt->execute([$product_name,$description,$original_price,$deal_price,$image_url,
                $group_id,$category_id,$subcategory_id,$stock,$is_on_sale,$editData['id']]);
            $success = "Product updated successfully!";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO products
                (product_name, description, original_price, deal_price, image_url, group_id, category_id, subcategory_id, stock, is_on_sale)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$product_name,$description,$original_price,$deal_price,$image_url,
                $group_id,$category_id,$subcategory_id,$stock,$is_on_sale]);
            $success = "Product added successfully!";
        }
        $editData = null;
        $_POST = [];
    }
}

$list = $pdo->query("
    SELECT p.*, g.name AS group_name, c.category_name, s.subcategory_name
    FROM products p
    JOIN groups_h g ON p.group_id = g.id
    JOIN categories c ON p.category_id = c.id
    LEFT JOIN subcategories s ON p.subcategory_id = s.id
    ORDER BY p.id DESC
")->fetchAll();

require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="adm-content" id="content">
    <div class="adm-page-header">
        <h1 class="adm-page-title">Product Management</h1>
        <div class="adm-breadcrumb">
            <a href="admin.php">Dashboard</a>
            <span>/</span>
            <span>Products</span>
        </div>
    </div>

    <?php if (!empty($errors) || $success): ?>
        <div class="adm-messages">
            <?php foreach ($errors as $err): ?>
                <div class="adm-alert adm-alert-danger">
                    <span class="adm-alert-icon">⚠️</span>
                    <span><?= htmlspecialchars($err) ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ($success): ?>
                <div class="adm-alert adm-alert-success">
                    <span class="adm-alert-icon">✓</span>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="adm-card adm-form-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">
                <?= $editData ? "✏️ Edit Product" : "➕ Add New Product" ?>
            </h3>
            <?php if ($editData): ?>
                <a href="products.php" class="adm-btn adm-btn-sm adm-btn-secondary">
                    ← Back to Add
                </a>
            <?php endif; ?>
        </div>

        <form method="POST" enctype="multipart/form-data" class="adm-form">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <?php endif; ?>

            <div class="adm-form-section-title">
                <span class="adm-section-icon">📂</span>
                <span>Product Classification</span>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label for="group_id" class="adm-label">
                        <span class="adm-label-text">Group</span>
                        <span class="adm-label-required">*</span>
                    </label>
                    <select name="group_id" id="group_id" class="adm-select" onchange="loadCategories(this.value)" required>
                        <option value="">-- Select Group --</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= ($editData && $editData['group_id']==$g['id'])?'selected':'' ?>><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="adm-form-group">
                    <label for="category_id" class="adm-label">
                        <span class="adm-label-text">Category</span>
                        <span class="adm-label-required">*</span>
                    </label>
                    <select name="category_id" id="category_id" class="adm-select" onchange="loadSubcategories(this.value)" required>
                        <option value="">Select Group First</option>
                    </select>
                </div>

                <div class="adm-form-group">
                    <label for="subcategory_id" class="adm-label">
                        <span class="adm-label-text">Subcategory</span>
                        <span class="adm-label-optional">(Optional)</span>
                    </label>
                    <select name="subcategory_id" id="subcategory_id" class="adm-select">
                        <option value="">-- None --</option>
                    </select>
                </div>
            </div>

            <div class="adm-form-section-title">
                <span class="adm-section-icon">📝</span>
                <span>Product Details</span>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group adm-form-group-full">
                    <label for="product_name" class="adm-label">
                        <span class="adm-label-text">Product Name</span>
                        <span class="adm-label-required">*</span>
                    </label>
                    <input type="text" name="product_name" id="product_name" class="adm-input" value="<?= htmlspecialchars($editData['product_name'] ?? '') ?>" placeholder="Enter product name" required>
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group adm-form-group-full">
                    <label for="description" class="adm-label">
                        <span class="adm-label-text">Description</span>
                    </label>
                    <textarea name="description" id="description" class="adm-textarea" rows="4" placeholder="Enter product description"><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="adm-form-section-title">
                <span class="adm-section-icon">💰</span>
                <span>Pricing & Stock</span>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label for="original_price" class="adm-label">
                        <span class="adm-label-text">Original Price (₹)</span>
                        <span class="adm-label-required">*</span>
                    </label>
                    <input type="number" step="0.01" name="original_price" id="original_price" class="adm-input" value="<?= htmlspecialchars($editData['original_price'] ?? '') ?>" placeholder="0.00" required>
                </div>

                <div class="adm-form-group">
                    <label for="deal_price" class="adm-label">
                        <span class="adm-label-text">Deal Price (₹)</span>
                        <span class="adm-label-optional">(Optional)</span>
                    </label>
                    <input type="number" step="0.01" name="deal_price" id="deal_price" class="adm-input" value="<?= htmlspecialchars($editData['deal_price'] ?? '') ?>" placeholder="0.00">
                </div>

                <div class="adm-form-group">
                    <label for="stock" class="adm-label">
                        <span class="adm-label-text">Stock Quantity</span>
                        <span class="adm-label-required">*</span>
                    </label>
                    <input type="number" name="stock" id="stock" class="adm-input" value="<?= htmlspecialchars($editData['stock'] ?? 0) ?>" min="0" placeholder="0" required>
                </div>
            </div>

            <div class="adm-form-section-title">
                <span class="adm-section-icon">🖼️</span>
                <span>Product Image</span>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group adm-form-group-full">
                    <label for="image_file" class="adm-label">
                        <span class="adm-label-text">Upload Image</span>
                        <span class="adm-label-optional">(JPG, PNG, GIF, WEBP - Max 2MB)</span>
                    </label>
                    <div class="adm-file-upload">
                        <input type="file" name="image_file" id="image_file" class="adm-file-input" accept="image/*" onchange="previewImage(event)">
                        <label for="image_file" class="adm-file-label">
                            <span class="adm-file-icon">📁</span>
                            <span class="adm-file-text">Choose Image</span>
                        </label>
                        <span class="adm-file-name" id="fileName">No file chosen</span>
                    </div>
                    <div class="adm-image-preview-wrapper">
                        <img id="image_preview" src="<?= htmlspecialchars($editData['image_url'] ?? '') ?>" class="adm-image-preview" style="<?= empty($editData['image_url'])?'display:none;':'' ?>">
                    </div>
                </div>
            </div>

            <div class="adm-form-section-title">
                <span class="adm-section-icon">⚙️</span>
                <span>Additional Options</span>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group adm-form-group-full">
                    <label class="adm-checkbox-wrapper">
                        <input type="checkbox" name="is_on_sale" class="adm-checkbox-input" <?= (isset($editData['is_on_sale']) && $editData['is_on_sale'])?'checked':'' ?>>
                        <span class="adm-checkbox-label">
                            <span class="adm-checkbox-icon">🏷️</span>
                            Mark this product as "On Sale"
                        </span>
                    </label>
                </div>
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg">
                    <?= $editData ? "💾 Update Product" : "➕ Add Product" ?>
                </button>

                <?php if ($editData): ?>
                    <a href="products.php" class="adm-btn adm-btn-secondary adm-btn-lg">
                        ✕ Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">
                All Products 
                <span class="adm-count-badge"><?= count($list) ?></span>
            </h3>
        </div>

        <?php if(count($list) > 0): ?>
            <div class="adm-table-wrapper">
                <table class="adm-table adm-products-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row): ?>
                            <tr>
                                <td>
                                    <?php if($row['image_url']): ?>
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" class="adm-product-thumb" alt="<?= htmlspecialchars($row['product_name']) ?>">
                                    <?php else: ?>
                                        <div class="adm-product-thumb-empty">📦</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="adm-product-info">
                                        <strong class="adm-product-name"><?= htmlspecialchars($row['product_name']) ?></strong>
                                        <span class="adm-product-id">#<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="adm-category-stack">
                                        <span class="adm-group-tag-sm">📁 <?= htmlspecialchars($row['group_name']) ?></span>
                                        <span class="adm-category-tag-sm">📂 <?= htmlspecialchars($row['category_name']) ?></span>
                                        <?php if($row['subcategory_name']): ?>
                                            <span class="adm-subcategory-tag-sm">📄 <?= htmlspecialchars($row['subcategory_name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="adm-price-display">
                                        <span class="adm-price-original">₹<?= number_format($row['original_price'], 2) ?></span>
                                        <?php if($row['deal_price']): ?>
                                            <span class="adm-price-deal">₹<?= number_format($row['deal_price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="adm-stock-badge <?= $row['stock'] > 0 ? 'adm-stock-in' : 'adm-stock-out' ?>">
                                        <?= $row['stock'] ?> units
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['is_on_sale']): ?>
                                        <span class="adm-badge adm-badge-danger">🏷️ On Sale</span>
                                    <?php else: ?>
                                        <span class="adm-badge adm-badge-success">Regular</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="adm-action-buttons">
                                        <a href="products.php?edit=<?= $row['id'] ?>" class="adm-btn adm-btn-sm adm-btn-primary" title="Edit Product">
                                            ✏️ Edit
                                        </a>
                                        <a href="products.php?delete=<?= $row['id'] ?>" class="adm-btn adm-btn-sm adm-btn-danger" onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')" title="Delete Product">
                                            🗑️ Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="adm-empty-state">
                <div class="adm-empty-icon">📦</div>
                <h3 class="adm-empty-title">No products yet</h3>
                <p class="adm-empty-text">Start by adding your first product using the form above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
   #admSidebar ~ .adm-content {
    margin-left: var(--adm-sidebar-width, 280px);
    transition: margin-left .28s ease;
}

#admSidebar.adm-collapsed ~ .adm-content {
    margin-left: var(--adm-sidebar-collapsed, 80px);
}

@media (max-width:1024px) {
    #admSidebar ~ .adm-content {
        margin-left: 0 !important;
    }
}

.adm-form-card {
    padding: 1.25rem;
}

.adm-form-section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    margin-top: 1.25rem;
    margin-bottom: 0.75rem;
}

.adm-form-section-title .adm-section-icon {
    font-size: 1.25rem;
}

.adm-form-section-title span {
    font-weight: 800;
    color: var(--adm-dark);
    font-size: 1.05rem;
}

.adm-form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    align-items: start;
}

.adm-form-group {
    display: flex;
    flex-direction: column;
}

.adm-form-group-full {
    grid-column: 1 / -1;
}

.adm-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-weight: 700;
    color: var(--adm-dark);
}

.adm-label .adm-label-text {
    font-weight: 700;
}

.adm-label .adm-label-required {
    color: var(--adm-danger);
    margin-left: 8px;
    font-weight: 800;
}

.adm-label .adm-label-optional {
    color: var(--adm-gray);
    font-size: 0.85rem;
}

.adm-input,
.adm-select,
.adm-textarea {
    padding: 0.85rem 1rem;
    border-radius: 10px;
    border: 1.5px solid var(--adm-gray-light);
    background: var(--adm-white);
    font-size: 1rem;
    transition: box-shadow .18s, border-color .18s;
}

.adm-input:focus,
.adm-select:focus,
.adm-textarea:focus {
    outline: none;
    border-color: var(--adm-primary);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
    transform: translateY(-1px);
}

.adm-form-row input[type="number"] {
    font-weight: 700;
}

.adm-form-row input[name="original_price"] {
    border-left: 4px solid rgba(37, 99, 235, 0.08);
}

.adm-form-row input[name="deal_price"] {
    border-left: 4px solid rgba(16, 185, 129, 0.08);
}

.adm-form-row input[name="stock"] {
    color: var(--adm-dark);
}

.adm-form-row input[name="stock"][value="0"] {
    color: var(--adm-danger);
}

.adm-file-label {
    cursor: pointer;
    border-radius: 10px;
    padding: 0.65rem 1rem;
    display: inline-flex;
    gap: 0.6rem;
    align-items: center;
}

.adm-file-name {
    margin-left: 0.75rem;
    color: var(--adm-gray);
}

.adm-image-preview {
    max-width: 220px;
    max-height: 220px;
    border-radius: 8px;
    border: 2px solid var(--adm-gray-light);
}

@media (max-width:600px) {
    .adm-form-row {
        grid-template-columns: 1fr;
    }

    .adm-image-preview {
        max-width: 100%;
        height: auto;
    }
}

.adm-form-section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--adm-dark);
    margin: 2rem 0 1.5rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--adm-gray-lighter);
}

.adm-section-icon {
    font-size: 1.5rem;
}

.adm-label-optional {
    color: var(--adm-gray);
    font-size: 0.85rem;
    font-weight: 500;
    font-style: italic;
}

.adm-textarea {
    width: 100%;
    padding: 0.875rem 1.25rem;
    border: 2px solid var(--adm-gray-light);
    border-radius: var(--adm-radius);
    font-size: 1rem;
    transition: var(--adm-transition);
    background: var(--adm-gray-lighter);
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
}

.adm-textarea:focus {
    outline: none;
    border-color: var(--adm-primary);
    background: var(--adm-white);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.adm-file-upload {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.adm-file-input {
    display: none;
}

.adm-file-label {
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, var(--adm-primary), var(--adm-primary-light));
    color: var(--adm-white);
    border-radius: var(--adm-radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    transition: var(--adm-transition);
    box-shadow: var(--adm-shadow);
}

.adm-file-label:hover {
    background: linear-gradient(135deg, var(--adm-primary-dark), var(--adm-primary));
    transform: translateY(-2px);
    box-shadow: var(--adm-shadow-lg);
}

.adm-file-icon {
    font-size: 1.25rem;
}

.adm-file-name {
    color: var(--adm-gray);
    font-size: 0.9rem;
}

.adm-image-preview-wrapper {
    margin-top: 1rem;
}

.adm-image-preview {
    max-width: 250px;
    max-height: 250px;
    border-radius: var(--adm-radius-lg);
    box-shadow: var(--adm-shadow-lg);
    border: 3px solid var(--adm-gray-light);
}

.adm-checkbox-wrapper {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    cursor: pointer;
    transition: var(--adm-transition);
    margin-bottom: 5px;
}

.adm-checkbox-input {
    width: 24px;
    height: 24px;
    cursor: pointer;
    margin-right: 1rem;
}

.adm-checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--adm-dark);
    font-size: 1rem;
}

.adm-checkbox-icon {
    font-size: 1.25rem;
}

.adm-products-table {
    font-size: 0.95rem;
}

.adm-product-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: var(--adm-radius);
    border: 2px solid var(--adm-gray-light);
}

.adm-product-thumb-empty {
    width: 60px;
    height: 60px;
    background: var(--adm-gray-lighter);
    border-radius: var(--adm-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    border: 2px solid var(--adm-gray-light);
}

.adm-product-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.adm-product-name {
    color: var(--adm-dark);
    font-weight: 700;
    font-size: 1rem;
}

.adm-product-id {
    color: var(--adm-gray);
    font-size: 0.8rem;
}

.adm-category-stack {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.adm-group-tag-sm,
.adm-category-tag-sm,
.adm-subcategory-tag-sm {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: var(--adm-radius);
    font-size: 0.8rem;
    font-weight: 600;
}

.adm-group-tag-sm {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(251, 191, 36, 0.05));
    color: #92400e;
}

.adm-category-tag-sm {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
    color: #1e40af;
}

.adm-subcategory-tag-sm {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
    color: #065f46;
}

.adm-price-display {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.adm-price-original {
    font-weight: 800;
    color: var(--adm-dark);
    font-size: 1.1rem;
}

.adm-price-deal {
    font-weight: 700;
    color: var(--adm-danger);
    font-size: 0.95rem;
}

.adm-stock-badge {
    padding: 0.5rem 0.75rem;
    border-radius: var(--adm-radius);
    font-weight: 700;
    font-size: 0.85rem;
}

.adm-stock-in {
    background: rgba(16, 185, 129, 0.1);
    color: var(--adm-secondary);
}

.adm-stock-out {
    background: rgba(239, 68, 68, 0.1);
    color: var(--adm-danger);
}

@media (max-width: 768px) {
    .adm-products-table {
        font-size: 0.85rem;
    }

    .adm-product-thumb,
    .adm-product-thumb-empty {
        width: 50px;
        height: 50px;
    }

    .adm-action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
function previewImage(event){
    const input = event.target;
    const preview = document.getElementById('image_preview');
    const fileName = document.getElementById('fileName');
    
    if(input.files && input.files[0]){
        fileName.textContent = input.files[0].name;
        
        const reader = new FileReader();
        reader.onload = function(e){
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        fileName.textContent = 'No file chosen';
        preview.src = '';
        preview.style.display = 'none';
    }
}

function loadCategories(categoryId, selectedSub=null){
    const subSelect=document.getElementById('subcategory_id');
    subSelect.innerHTML="<option>Loading...</option>";

    if(!categoryId){
        subSelect.innerHTML="<option value=''>Optional</option>";
        return;
    }

    fetch("products.php?fetch_subcategories="+categoryId)
    .then(res=>res.json())
    .then(data=>{
        let html="<option value=''>Optional</option>";
        data.forEach(row=>{
            let sel=(selectedSub==row.id)?"selected":"";
            html+=`<option value="${row.id}" ${sel}>${row.subcategory_name}</option>`;
        });
        subSelect.innerHTML=html;
    });
}

<?php if($editData): ?>
loadCategories(<?= $editData['group_id'] ?>, <?= $editData['category_id'] ?>);
loadSubcategories(<?= $editData['category_id'] ?>, <?= $editData['subcategory_id'] ?? 'null' ?>);
<?php endif; ?>
</script>

</body>
</html>