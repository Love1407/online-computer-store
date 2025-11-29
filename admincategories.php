<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = '';
$editData = null;

// =========================
// DELETE CATEGORY
// =========================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admincategories.php?msg=deleted");
    exit;
}

// =========================
// EDIT MODE - FETCH CATEGORY
// =========================
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// =========================
// ADD / UPDATE CATEGORY
// =========================
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
            // Check if group exists
            $stmt = $pdo->prepare("SELECT id FROM groups_h WHERE id = ?");
            $stmt->execute([$group_id]);
            if (!$stmt->fetch()) {
                $errors[] = "Group does not exist.";
            } else {
                if (!empty($_POST['id'])) {
                    // Update
                    $stmt = $pdo->prepare("UPDATE categories SET group_id=?, category_name=? WHERE id=?");
                    $stmt->execute([$group_id, $category_name, $_POST['id']]);
                    $success = "Category updated successfully!";
                } else {
                    // Insert
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

// =========================
// FETCH GROUPS FOR DROPDOWN
// =========================
$groups = [];
try {
    $groups = $pdo->query("SELECT id, name FROM groups_h ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Failed to fetch groups: " . $e->getMessage();
}

// =========================
// FETCH ALL CATEGORIES FOR TABLE
// =========================
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

<div class="wrap">
    <h2><?= $editData ? "Edit Category" : "Add Category" ?></h2>

    <div class="messages">
        <?php foreach ($errors as $err): ?>
            <div class="error"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
    </div>

    <form method="post" action="">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= (int)$editData['id'] ?>">
        <?php endif; ?>

        <label for="group_id">Select Group</label>
        <select name="group_id" id="group_id" required>
            <option value="">-- Choose Group --</option>
            <?php foreach ($groups as $g): ?>
                <option value="<?= (int)$g['id'] ?>"
                    <?= ($editData && (int)$editData['group_id'] === (int)$g['id']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($g['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="category_name">Category Name</label>
        <input type="text" name="category_name" id="category_name" required
               value="<?= htmlspecialchars($editData['category_name'] ?? ($_POST['category_name'] ?? '')) ?>"
               placeholder="Enter category name">

        <button type="submit"><?= $editData ? "Update" : "Save" ?></button>

        <?php if ($editData): ?>
            <a href="admincategories.php">
                <button type="button" style="background:#aaa; margin-left:10px;">Cancel</button>
            </a>
        <?php endif; ?>
    </form>

    <!-- CATEGORY TABLE -->
    <h2 style="margin-top:40px;">Category List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Group</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($categories as $row): ?>
            <tr>
                <td><?= (int)$row['id'] ?></td>
                <td><?= htmlspecialchars($row['group_name']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td class="actions">
                    <a href="admincategories.php?edit=<?= (int)$row['id'] ?>">Edit</a>
                    <a href="admincategories.php?delete=<?= (int)$row['id'] ?>"
                       onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>