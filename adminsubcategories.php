<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/db.php';

$success = '';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM subcategories WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: adminsubcategories.php?msg=deleted");
    exit;
}

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
    if ($msg === 'deleted') $success = 'Subcategory deleted successfully!';
    if ($msg === 'updated') $success = 'Subcategory updated successfully!';
    if ($msg === 'added') $success = 'Subcategory added successfully!';
}

if (isset($_GET['fetch_categories'])) {
    $gid = intval($_GET['fetch_categories']);
    header('Content-Type: application/json');
    $stmt = $pdo->prepare("SELECT id, category_name FROM categories WHERE group_id = ? ORDER BY category_name");
    $stmt->execute([$gid]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$groups = $pdo->query("SELECT id, name FROM groups_h ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stm = $pdo->prepare("SELECT * FROM subcategories WHERE id = ?");
    $stm->execute([$id]);
    $editData = $stm->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gid  = intval($_POST['group_id']);
    $cid  = intval($_POST['category_id']);
    $name = trim($_POST['subcategory_name']);

    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE subcategories SET group_id=?, category_id=?, subcategory_name=? WHERE id=?");
        $stmt->execute([$gid, $cid, $name, $_POST['id']]);
        header("Location: adminsubcategories.php?msg=updated");
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO subcategories (group_id, category_id, subcategory_name) VALUES (?, ?, ?)");
        $stmt->execute([$gid, $cid, $name]);
        header("Location: adminsubcategories.php?msg=added");
        exit;
    }
}

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
<link rel="stylesheet" href="assets/css/adminsubcategories.css">

<div class="adm-content" id="content">
    <div class="adm-page-header">
        <h1 class="adm-page-title">Subcategory Management</h1>
        <div class="adm-breadcrumb">
            <a href="admin.php">Dashboard</a>
            <span>/</span>
            <span>Subcategories</span>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="adm-messages">
            <div class="adm-alert adm-alert-success">
                <span class="adm-alert-icon">✓</span>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="adm-card adm-form-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">
                <?= $editData ? " Edit Subcategory" : "➕ Add New Subcategory" ?>
            </h3>
            <?php if ($editData): ?>
                <a href="adminsubcategories.php" class="adm-btn adm-btn-sm adm-btn-secondary">
                    ← Back to Add
                </a>
            <?php endif; ?>
        </div>

        <form method="POST" class="adm-form">
            <?php if ($editData): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
            <?php endif; ?>

            <div class="adm-form-section">
                <div class="adm-form-row">
                    <div class="adm-form-group adm-form-group-full">
                        <label for="group_id" class="adm-label">
                            <span class="adm-label-text">Step 1: Select Group</span>
                            <span class="adm-label-required">*</span>
                        </label>
                        <select id="group_id" name="group_id" class="adm-select adm-select-large" onchange="loadCategories(this.value)" required>
                            <option value="">-- Choose a Group --</option>
                            <?php foreach ($groups as $g): ?>
                                <option value="<?= (int)$g['id'] ?>"
                                    <?= ($editData && (int)$editData['group_id'] === (int)$g['id']) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($g['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="adm-form-row">
                    <div class="adm-form-group adm-form-group-full">
                        <label for="category_id" class="adm-label">
                            <span class="adm-label-text">Step 2: Select Category</span>
                            <span class="adm-label-required">*</span>
                        </label>
                        <select name="category_id" id="category_id" class="adm-select adm-select-large" required disabled>
                            <option value="">First, select a group above</option>
                        </select>
                    </div>
                </div>

                <div class="adm-form-row">
                    <div class="adm-form-group adm-form-group-full">
                        <label for="subcategory_name" class="adm-label">
                            <span class="adm-label-text">Step 3: Enter Subcategory Name</span>
                            <span class="adm-label-required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="subcategory_name" 
                            id="subcategory_name"
                            class="adm-input adm-input-large"
                            value="<?= htmlspecialchars($editData['subcategory_name'] ?? '') ?>" 
                            placeholder="e.g., Gaming Laptops, Wireless Earbuds, Smart TVs..."
                            required>
                    </div>
                </div>
            </div>

            <div class="adm-form-actions">
                <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg">
                    <?= $editData ? "Update Subcategory" : "Add Subcategory" ?>
                </button>

                <?php if ($editData): ?>
                    <a href="adminsubcategories.php" class="adm-btn adm-btn-secondary adm-btn-lg">
                       Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">
                All Subcategories 
                <span class="adm-count-badge"><?= count($list) ?></span>
            </h3>
        </div>

        <?php if(count($list) > 0): ?>
            <div class="adm-table-wrapper">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Group</th>
                            <th>Category</th>
                            <th>Subcategory Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row): ?>
                            <tr>
                                <td>
                                    <span class="adm-id-badge">#<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td>
                                    <span class="adm-group-tag">📁 <?= htmlspecialchars($row['group_name']) ?></span>
                                </td>
                                <td>
                                    <span class="adm-category-tag">📂 <?= htmlspecialchars($row['category_name']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['subcategory_name']) ?></strong>
                                </td>
                                <td>
                                    <div class="adm-action-buttons">
                                        <a href="adminsubcategories.php?edit=<?= (int)$row['id'] ?>" 
                                           class="adm-btn adm-btn-sm adm-btn-primary"
                                           title="Edit Subcategory">
                                             Edit
                                        </a>
                                        <a href="adminsubcategories.php?delete=<?= (int)$row['id'] ?>"
                                           class="adm-btn adm-btn-sm adm-btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this subcategory? This action cannot be undone.')"
                                           title="Delete Subcategory">
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
                <div class="adm-empty-icon">📂</div>
                <h3 class="adm-empty-title">No subcategories yet</h3>
                <p class="adm-empty-text">Start by adding your first subcategory using the form above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function loadCategories(gid, selectedCat = null) {
    const catEl = document.getElementById('category_id');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');

    if (!gid) {
        if (catEl) {
            catEl.innerHTML = "<option value=''>First, select a group above ↑</option>";
            catEl.disabled = true;
        }
        if (step2 && step2.classList) step2.classList.remove('adm-step-active', 'adm-step-completed');
        if (step1 && step1.classList) step1.classList.add('adm-step-active');
        return;
    }

    if (step1 && step1.classList) {
        step1.classList.add('adm-step-completed');
        step1.classList.remove('adm-step-active');
    }
    if (step2 && step2.classList) step2.classList.add('adm-step-active');
    catEl.classList.add('loading');
    catEl.innerHTML = "<option value=''>⏳ Loading categories...</option>";
    catEl.disabled = true;

    fetch('adminsubcategories.php?fetch_categories=' + encodeURIComponent(gid))
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            let html = "<option value=''>-- Choose a Category --</option>";
            
            if (data.length === 0) {
                html = "<option value=''>⚠️ No categories available for this group</option>";
            } else {
                data.forEach(row => {
                    const sel = (selectedCat != null && String(selectedCat) === String(row.id)) ? ' selected' : '';
                    html += `<option value="${row.id}"${sel}>${row.category_name}</option>`;
                });
            }
            
            if (catEl) {
                catEl.innerHTML = html;
                catEl.classList.remove('loading');
                catEl.disabled = false;
            }
            if (data.length > 0) {
                if (step2 && step2.classList) {
                    step2.classList.add('adm-step-completed');
                    step2.classList.remove('adm-step-active');
                }
                if (step3 && step3.classList) step3.classList.add('adm-step-active');
            }
        })
        .catch(err => {
            console.error('Error loading categories:', err);
            if (catEl) {
                catEl.innerHTML = "<option value=''> Error loading categories</option>";
                catEl.classList.remove('loading');
                catEl.disabled = false;
            }
            showNotification('Failed to load categories. Please try again.', 'error');
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('step2').classList.add('adm-step-completed');
                document.getElementById('step2').classList.remove('adm-step-active');
                document.getElementById('step3').classList.add('adm-step-active');
            }
        });
    }

    const subcategoryInput = document.getElementById('subcategory_name');
    if (subcategoryInput) {
        subcategoryInput.addEventListener('input', function() {
            if (this.value.trim()) {
                document.getElementById('step3').classList.add('adm-step-completed');
            } else {
                document.getElementById('step3').classList.remove('adm-step-completed');
            }
        });
    }
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `adm-alert adm-alert-${type === 'error' ? 'danger' : type}`;
    notification.innerHTML = `
        <span class="adm-alert-icon">${type === 'error' ? '⚠️' : 'ℹ️'}</span>
        <span>${message}</span>
    `;
    
    const messagesContainer = document.querySelector('.adm-messages') || (() => {
        const container = document.createElement('div');
        container.className = 'adm-messages';
        document.querySelector('.adm-content').insertBefore(container, document.querySelector('.adm-card'));
        return container;
    })();
    
    messagesContainer.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transition = 'all 0.5s ease';
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function () {
    <?php if ($editData): 
        $egid = (int)$editData['group_id'];
        $ecid = (int)$editData['category_id'];
    ?>
        loadCategories(<?= $egid ?>, <?= $ecid ?>);
    <?php endif; ?>

    const successAlerts = document.querySelectorAll('.adm-alert-success');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
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
});
</script>

</body>
</html>