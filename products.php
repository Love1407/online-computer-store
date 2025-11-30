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
$success = '';

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
    $allowed = ['jpg','jpeg','png','gif'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error during file upload. Error code: " . $file['error'];
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid image type. Allowed: jpg, jpeg, png, gif.";
        }

        if ($file['size'] > 2*1024*1024) {
            $errors[] = "Image size must be under 2MB.";
        }

        $uploadDir = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                $errors[] = "Failed to create uploads directory. Check folder permissions.";
            }
        }

        if (empty($errors)) {
            $filename = uniqid('img_') . '.' . $ext;
            $target = $uploadDir . '/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $image_url = 'uploads/' . $filename;
            } else {
                $errors[] = "Failed to move uploaded file. Check folder permissions.";
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
?>

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<div class="content" id="content">
<div class="wrap">

<h2><?= $editData ? "Edit" : "Add" ?> Product</h2>

<div class="messages">
<?php foreach ($errors as $err): ?>
    <div class="error"><?= htmlspecialchars($err) ?></div>
<?php endforeach; ?>
<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
</div>

<form method="POST" enctype="multipart/form-data">
    <?php if ($editData): ?>
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
    <?php endif; ?>

    <label>Group</label>
    <select name="group_id" id="group_id" onchange="loadCategories(this.value)" required>
        <option value="">-- Select Group --</option>
        <?php foreach ($groups as $g): ?>
            <option value="<?= $g['id'] ?>" <?= ($editData && $editData['group_id']==$g['id'])?'selected':'' ?>><?= htmlspecialchars($g['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Category</label>
    <select name="category_id" id="category_id" onchange="loadSubcategories(this.value)" required>
        <option value="">Select Group First</option>
    </select>

    <label>Subcategory</label>
    <select name="subcategory_id" id="subcategory_id">
        <option value="">Optional</option>
    </select>

    <label>Product Name</label>
    <input type="text" name="product_name" value="<?= $editData['product_name'] ?? '' ?>" required>

    <label>Description</label>
    <textarea name="description"><?= $editData['description'] ?? '' ?></textarea>

    <label>Original Price</label>
    <input type="text" name="original_price" value="<?= $editData['original_price'] ?? '' ?>" required>

    <label>Deal Price</label>
    <input type="text" name="deal_price" value="<?= $editData['deal_price'] ?? '' ?>">

    <label>Product Image</label>
    <input type="file" name="image_file" accept="image/*" onchange="previewImage(event)">
    <br>
    <img id="image_preview" src="<?= $editData['image_url'] ?? '' ?>" style="max-width:150px; margin-top:10px; <?= empty($editData['image_url'])?'display:none;':'' ?>">

    <label>Stock</label>
    <input type="number" name="stock" value="<?= $editData['stock'] ?? 0 ?>" min="0">

    <label>
        <input type="checkbox" name="is_on_sale" <?= (isset($editData['is_on_sale']) && $editData['is_on_sale'])?'checked':'' ?>> On Sale
    </label>

    <button type="submit"><?= $editData ? "Update" : "Add" ?></button>
    <?php if ($editData): ?>
        <a href="products.php"><button type="button">Cancel</button></a>
    <?php endif; ?>
</form>

<h2 style="margin-top:40px;">Products List</h2>
<table>
<tr>
<th>ID</th>
<th>Group</th>
<th>Category</th>
<th>Subcategory</th>
<th>Name</th>
<th>Price</th>
<th>Deal Price</th>
<th>Stock</th>
<th>On Sale</th>
<th>Image</th>
<th>Actions</th>
</tr>
<?php foreach ($list as $row): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['group_name']) ?></td>
<td><?= htmlspecialchars($row['category_name']) ?></td>
<td><?= htmlspecialchars($row['subcategory_name']) ?></td>
<td><?= htmlspecialchars($row['product_name']) ?></td>
<td><?= $row['original_price'] ?></td>
<td><?= $row['deal_price'] ?></td>
<td><?= $row['stock'] ?></td>
<td><?= $row['is_on_sale'] ? 'Yes' : 'No' ?></td>
<td><?php if($row['image_url']): ?><img src="<?= htmlspecialchars($row['image_url']) ?>" width="50"><?php endif; ?></td>
<td class="actions">
<a href="products.php?edit=<?= $row['id'] ?>">Edit</a>
<a href="products.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</div>

<script>
function previewImage(event){
    const input=event.target;
    const preview=document.getElementById('image_preview');
    if(input.files && input.files[0]){
        const reader=new FileReader();
        reader.onload=function(e){
            preview.src=e.target.result;
            preview.style.display='block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src='';
        preview.style.display='none';
    }
}

function loadCategories(groupId, selectedCat = null){
    const categorySelect=document.getElementById('category_id');
    categorySelect.innerHTML="<option>Loading...</option>";
    const subSelect=document.getElementById('subcategory_id');
    subSelect.innerHTML="<option>Optional</option>";

    if(!groupId){
        categorySelect.innerHTML="<option value=''>Select Group First</option>";
        return;
    }

    fetch("products.php?fetch_categories="+groupId)
    .then(res=>res.json())
    .then(data=>{
        let html="<option value=''>Select Category</option>";
        data.forEach(row=>{
            let sel=(selectedCat==row.id)?"selected":"";
            html+=`<option value="${row.id}" ${sel}>${row.category_name}</option>`;
        });
        categorySelect.innerHTML=html;
    });
}

function loadSubcategories(categoryId, selectedSub=null){
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