<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/db.php';

// =========================
// DELETE SUBCATEGORY
// =========================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM subcategories WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: adminsubcategories.php?msg=deleted");
    exit;
}

// =========================
// AJAX: FETCH CATEGORIES (IN SAME FILE)
// =========================
if (isset($_GET['fetch_categories'])) {
    $gid = intval($_GET['fetch_categories']);
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT id, category_name FROM categories WHERE group_id = ? ORDER BY category_name");
    $stmt->execute([$gid]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// =========================
// FETCH GROUPS
// =========================
$groups = $pdo->query("SELECT id, name FROM groups_h ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// =========================
// EDIT MODE
// =========================
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stm = $pdo->prepare("SELECT * FROM subcategories WHERE id = ?");
    $stm->execute([$id]);
    $editData = $stm->fetch(PDO::FETCH_ASSOC);
}

// =========================
// ADD / UPDATE LOGIC
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $gid  = intval($_POST['group_id']);
    $cid  = intval($_POST['category_id']);
    $name = trim($_POST['subcategory_name']);

    if (!empty($_POST['id'])) {
        // UPDATE
        $stmt = $pdo->prepare("UPDATE subcategories SET group_id=?, category_id=?, subcategory_name=? WHERE id=?");
        $stmt->execute([$gid, $cid, $name, $_POST['id']]);

        header("Location: adminsubcategories.php?msg=updated");
        exit;

    } else {
        // INSERT
        $stmt = $pdo->prepare("INSERT INTO subcategories (group_id, category_id, subcategory_name) VALUES (?, ?, ?)");
        $stmt->execute([$gid, $cid, $name]);

        header("Location: adminsubcategories.php?msg=added");
        exit;
    }
}

// =========================
// LIST SUBCATEGORIES
// =========================
$list = $pdo->query("
    SELECT s.*, 
           g.name AS group_name, 
           c.category_name 
    FROM subcategories s
    JOIN groups_h g ON s.group_id = g.id
    JOIN categories c ON s.category_id = c.id
    ORDER BY s.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- MAIN CONTENT -->
<div class="content" id="content">

    <div class="wrap">

        <h2><?= $editData ? "Edit Subcategory" : "Add Subcategory" ?></h2>

      <div class="form-box">
    <form method="POST">

        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
        <?php endif; ?>

        <!-- GROUP DROPDOWN -->
        <label>Group</label>
        <select id="group_id" name="group_id" onchange="loadCategories(this.value)" required>
            <option value="">Select Group</option>

            <?php foreach ($groups as $g): ?>
                <option value="<?= (int)$g['id'] ?>"
                    <?= ($editData && (int)$editData['group_id'] === (int)$g['id']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($g['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- CATEGORY DROPDOWN -->
        <label>Category</label>
        <select name="category_id" id="category_id" required>
            <option value="">Select Group First</option>
        </select>

        <!-- SUBCATEGORY NAME -->
        <label>Subcategory Name</label>
        <input type="text" name="subcategory_name"
               value="<?= htmlspecialchars($editData['subcategory_name'] ?? '') ?>" required>

        <!-- BUTTONS -->
        <button type="submit"><?= $editData ? "Update" : "Save" ?></button>
        <?php if ($editData): ?>
            <a href="adminsubcategories.php">
                <button type="button" style="background:#aaa; margin-left:10px;">Cancel</button>
            </a>
        <?php endif; ?>
    </form>
</div>


        <h2 style="margin-top:40px;">Subcategory List</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Group</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($list as $row): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= htmlspecialchars($row['group_name']) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td><?= htmlspecialchars($row['subcategory_name']) ?></td>
                    <td class="actions">
                        <a href="adminsubcategories.php?edit=<?= (int)$row['id'] ?>">Edit</a>
                        <a href="adminsubcategories.php?delete=<?= (int)$row['id'] ?>"
                           onclick="return confirm('Delete this item?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>
</div>

<script>
/**
 * Load categories for a given group id.
 * If selectedCat is provided, it will be set after options are loaded.
 */
function loadCategories(gid, selectedCat = null) {
    const catEl = document.getElementById('category_id');

    if (!gid) {
        catEl.innerHTML = "<option value=''>Select Group First</option>";
        return;
    }

    fetch('adminsubcategories.php?fetch_categories=' + encodeURIComponent(gid))
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            let html = "<option value=''>Select Category</option>";
            data.forEach(row => {
                const sel = (selectedCat != null && String(selectedCat) === String(row.id)) ? ' selected' : '';
                html += `<option value="${row.id}"${sel}>${row.category_name}</option>`;
            });
            catEl.innerHTML = html;
        })
        .catch(err => {
            console.error('Error loading categories:', err);
            catEl.innerHTML = "<option value=''>Error loading categories</option>";
        });
}

// Wait for DOM ready, then if we're in edit mode, call loadCategories with selected values.
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($editData): 
        // ensure integers and safe JS output
        $egid = (int)$editData['group_id'];
        $ecid = (int)$editData['category_id'];
    ?>
        // call after DOM ready so function and elements exist
        loadCategories(<?= $egid ?>, <?= $ecid ?>);
    <?php endif; ?>
});
</script>

</body>
</html>
