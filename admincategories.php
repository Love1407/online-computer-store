<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = '';
$editData = null;

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admincategories.php?msg=deleted");
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
    $category_name = trim($_POST['category_name'] ?? '');

    if ($group_id <= 0) {
        $errors[] = "Please select a valid group.";
    }
    if ($category_name === '') {
        $errors[] = "Category name cannot be empty.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM groups_h WHERE id = ?");
            $stmt->execute([$group_id]);
            if (!$stmt->fetch()) {
                $errors[] = "Group does not exist.";
            } else {
                if (!empty($_POST['id'])) {
                    $stmt = $pdo->prepare("UPDATE categories SET group_id=?, category_name=? WHERE id=?");
                    $stmt->execute([$group_id, $category_name, $_POST['id']]);
                    $success = "Category updated successfully!";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO categories (group_id, category_name) VALUES (?, ?)");
                    $stmt->execute([$group_id, $category_name]);
                    $success = "Category added successfully!";
                }
                $_POST = [];
                $editData = null;
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

$groups = [];
try {
    $groups = $pdo->query("SELECT id, name FROM groups_h ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Failed to fetch groups: " . $e->getMessage();
}

$categories = [];
try {
    $categories = $pdo->query("
        SELECT c.id, c.category_name, g.name AS group_name
        FROM categories c
        JOIN groups_h g ON c.group_id = g.id
        ORDER BY c.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Failed to fetch categories: " . $e->getMessage();
}

require_once __DIR__ . '/includes/sidebar.php';
?>
<link rel="stylesheet" href="assets/css/admincategories.css">

<div class="adm-content" id="content">
    <div class="adm-page-header">
        <h1 class="adm-page-title">Category Management</h1>
        <div class="adm-breadcrumb">
            <a href="admin.php">Dashboard</a>
            <span>/</span>
            <span>Categories</span>
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
                <?= $editData ? " Edit Category" : "➕ Add New Category" ?>
            </h3>
            <?php if ($editData): ?>
                <a href="admincategories.php" class="adm-btn adm-btn-sm adm-btn-secondary">
                    ← Back to Add
                </a>
            <?php endif; ?>
        </div>

        <form method="post" action="" class="adm-form">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= (int)$editData['id'] ?>">
            <?php endif; ?>

            <div class="adm-form-grid">
                <div class="adm-form-group">
                    <label for="group_id" class="adm-label">
                        <span class="adm-label-text">Select Group</span>
                        <span class="adm-label-required">*</span>
                    </label>
                    <select name="group_id" id="group_id" class="adm-select" required>
                        <option value="">-- Choose Group --</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?= (int)$g['id'] ?>"
                                <?= ($editData && (int)$editData['group_id'] === (int)$g['id']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($g['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="adm-form-group">
                    <label for="category_name" class="adm-label">
                        <span class="adm-label-text">Category Name</span>
                        <span class="adm-label-required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="category_name" 
                        id="category_name" 
                        class="adm-input"
                        required
                        value="<?= htmlspecialchars($editData['category_name'] ?? ($_POST['category_name'] ?? '')) ?>"
                        placeholder="Enter category name">
                </div>
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg">
                    <?= $editData ? "Update Category" : "Add Category" ?>
                </button>

                <?php if ($editData): ?>
                    <a href="admincategories.php" class="adm-btn adm-btn-secondary adm-btn-lg">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">
                All Categories 
                <span class="adm-count-badge"><?= count($categories) ?></span>
            </h3>
        </div>

        <?php if(count($categories) > 0): ?>
            <div class="adm-table-wrapper">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Group</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $row): ?>
                            <tr>
                                <td>
                                    <span class="adm-id-badge">#<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td>
                                    <span class="adm-group-tag">📁 <?= htmlspecialchars($row['group_name']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['category_name']) ?></strong>
                                </td>
                                <td>
                                    <div class="adm-action-buttons">
                                        <a href="admincategories.php?edit=<?= (int)$row['id'] ?>" 
                                           class="adm-btn adm-btn-sm adm-btn-primary"
                                           title="Edit Category">
                                             Edit
                                        </a>
                                        <a href="admincategories.php?delete=<?= (int)$row['id'] ?>"
                                           class="adm-btn adm-btn-sm adm-btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.')"
                                           title="Delete Category">
                                             Delete
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
                <div class="adm-empty-icon">📁</div>
                <h3 class="adm-empty-title">No categories yet</h3>
                <p class="adm-empty-text">Start by adding your first category using the form above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const successAlerts = document.querySelectorAll('.adm-alert-success');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

let formChanged = false;
const form = document.querySelector('.adm-form');
if (form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', (e) => {
        if (formChanged && !form.dataset.submitted) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    form.addEventListener('submit', () => {
        form.dataset.submitted = 'true';
        formChanged = false;
    });
}
</script>

</body>
</html>