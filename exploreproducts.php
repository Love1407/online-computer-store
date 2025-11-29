<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function json_out($data){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

if (isset($_GET['fetch_categories'])) {
    $gid = intval($_GET['fetch_categories']);
    $stmt = $pdo->prepare("SELECT id, category_name FROM categories WHERE group_id = ? ORDER BY category_name");
    $stmt->execute([$gid]);
    json_out(['ok' => true, 'categories' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

if (isset($_GET['fetch_subcategories'])) {
    $cid = intval($_GET['fetch_subcategories']);
    $stmt = $pdo->prepare("SELECT id, subcategory_name FROM subcategories WHERE category_id = ? ORDER BY subcategory_name");
    $stmt->execute([$cid]);
    json_out(['ok' => true, 'subcategories' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

if (isset($_GET['quick_view'])) {
    $id = intval($_GET['quick_view']);
    $stmt = $pdo->prepare("
        SELECT p.*, g.name AS group_name, c.category_name, s.subcategory_name
        FROM products p
        LEFT JOIN groups_h g ON p.group_id = g.id
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN subcategories s ON p.subcategory_id = s.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$prod) json_out(['ok' => false, 'msg' => 'Product not found.']);
    json_out(['ok' => true, 'product' => $prod]);
}

if (isset($_GET['fetch_products'])) {
    try {
        $group = (isset($_GET['group']) && $_GET['group'] !== '') ? intval($_GET['group']) : null;
        $category = (isset($_GET['category']) && $_GET['category'] !== '') ? intval($_GET['category']) : null;
        $subcategory = (isset($_GET['subcategory']) && $_GET['subcategory'] !== '') ? intval($_GET['subcategory']) : null;
        $search = trim($_GET['search'] ?? '');
        $min_price = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? floatval($_GET['min_price']) : null;
        $max_price = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? floatval($_GET['max_price']) : null;
        $sale = (isset($_GET['sale']) && ($_GET['sale'] === '1' || $_GET['sale'] === 'true')) ? 1 : null;
        $sort = $_GET['sort'] ?? 'new';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, intval($_GET['limit'] ?? 12));
        $offset = ($page - 1) * $limit;

        $where = [];
        $params = [];

        if ($group !== null) { $where[] = "p.group_id = ?"; $params[] = $group; }
        if ($category !== null) { $where[] = "p.category_id = ?"; $params[] = $category; }
        if ($subcategory !== null) { $where[] = "p.subcategory_id = ?"; $params[] = $subcategory; }
        if ($search !== '') { $where[] = "p.product_name LIKE ?"; $params[] = "%$search%"; }
        if ($sale === 1) { $where[] = "p.is_on_sale = 1"; }
        if ($min_price !== null) { $where[] = "COALESCE(p.deal_price, p.original_price) >= ?"; $params[] = $min_price; }
        if ($max_price !== null) { $where[] = "COALESCE(p.deal_price, p.original_price) <= ?"; $params[] = $max_price; }

        $whereSql = count($where) ? "WHERE " . implode(" AND ", $where) : "";

        $countSql = "SELECT COUNT(*) FROM products p $whereSql";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $limit));

        switch ($sort) {
            case 'price_asc':
            case 'price_asc': $order = "ORDER BY COALESCE(p.deal_price, p.original_price) ASC"; break;
            case 'price_desc': $order = "ORDER BY COALESCE(p.deal_price, p.original_price) DESC"; break;
            default: $order = "ORDER BY p.created_at DESC";
        }

        $sql = "
            SELECT p.id, p.product_name, p.description, p.original_price, p.deal_price, p.image_url,
                   p.stock, p.is_on_sale, p.created_at,
                   g.name AS group_name, c.category_name, s.subcategory_name
            FROM products p
            LEFT JOIN groups_h g ON p.group_id = g.id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            $whereSql
            $order
            LIMIT $limit OFFSET $offset
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $prods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        json_out([
            'ok' => true,
            'products' => $prods,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit
        ]);

    } catch (Exception $e) {
        json_out(['ok' => false, 'error' => $e->getMessage()]);
    }
}

$groups = $pdo->query("SELECT id, name FROM groups_h ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Explore Products</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body { font-family: Arial, sans-serif; background:#f6f7fb; margin:0; padding:0; color:#222; }
.container { display:flex; gap:18px; padding:18px; max-width:1200px; margin:0 auto; }
.sidebar { width:300px; background:#fff; padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
.main { flex:1; }
.filters label { font-weight:600; display:block; margin-top:10px; }
.form-row { display:flex; gap:8px; margin-top:8px; }
.card-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:16px; }
.card { background:#fff; border-radius:8px; padding:12px; box-shadow:0 6px 18px rgba(0,0,0,0.04); display:flex; flex-direction:column; min-height:330px; }
.card img { width:100%; height:160px; object-fit:cover; border-radius:6px; background:#f2f2f2; }
.card h4 { margin:8px 0 6px; font-size:1rem; }
.price { font-weight:700; }
.old-price { text-decoration:line-through; color:#888; margin-left:8px; font-weight:500; }
.badge { background:#ff4d4f; color:#fff; padding:4px 8px; border-radius:6px; font-size:0.8rem; display:inline-block; }
.controls { display:flex; gap:8px; margin-top:auto; align-items:center; }
.btn { padding:8px 10px; border:0; border-radius:6px; cursor:pointer; }
.btn-primary { background:#007bff; color:#fff; }
.btn-outline { background:#fff; border:1px solid #ddd; color:#333; }
.search-row { display:flex; gap:8px; margin-bottom:12px; align-items:center; }
.pager { margin-top:18px; text-align:center; }
.pager button { margin:0 4px; padding:6px 10px; border-radius:6px; border:1px solid #ddd; background:#fff; cursor:pointer; }
.pager button.active { background:#007bff;color:#fff;border-color:#007bff;}
.quickview-modal { position:fixed; left:50%; top:50%; transform:translate(-50%,-50%); width:92%; max-width:900px; background:#fff; z-index:9999; padding:16px; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,0.3); display:none; }
.modal-close { float:right; cursor:pointer; font-size:18px; }
.cart-count { background:#ff4d4f; color:#fff; padding:2px 8px; border-radius:20px; font-weight:700; margin-left:8px; }
@media (max-width: 900px) { .container{flex-direction:column} .sidebar{width:100%} .card{min-height:auto} }
</style>
</head>
<body>
<section class="category-box">
<div class="container">

    <aside class="sidebar" aria-label="Filters">
        <h3 style="margin:0 0 10px 0;">Filters</h3>

        <div class="filters">
            <div class="search-row">
                <input id="searchInput" type="text" placeholder="Search product..." style="flex:1;padding:8px;border-radius:6px;border:1px solid #ddd;">
                <button id="searchBtn" class="btn btn-primary">Search</button>
            </div>

            <label>Group</label>
            <select id="filterGroup" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;">
                <option value="">All groups</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Category</label>
            <select id="filterCategory" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;">
                <option value="">All categories</option>
            </select>

            <label>Subcategory</label>
            <select id="filterSubcategory" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;">
                <option value="">All subcategories</option>
            </select>

            <div class="form-row" style="margin-top:12px;">
                <div style="flex:1">
                    <label style="font-weight:600">Min price</label>
                    <input id="minPrice" type="number" placeholder="0" style="width:100%;padding:6px;border-radius:6px;border:1px solid #ddd;">
                </div>
                <div style="flex:1">
                    <label style="font-weight:600">Max price</label>
                    <input id="maxPrice" type="number" placeholder="99999" style="width:100%;padding:6px;border-radius:6px;border:1px solid #ddd;">
                </div>
            </div>

            <label style="margin-top:10px;"><input id="saleOnly" type="checkbox"> On Sale only</label>

            <label style="margin-top:10px;">Sort</label>
            <select id="sortBy" style="width:100%;padding:8px;border-radius:6px;border:1px solid #ddd;">
                <option value="new">Newest</option>
                <option value="price_asc">Price: Low → High</option>
                <option value="price_desc">Price: High → Low</option>
            </select>

            <button id="applyFilters" class="btn btn-primary" style="margin-top:12px;width:100%;">Apply</button>
            <button id="resetFilters" class="btn btn-outline" style="margin-top:8px;width:100%;">Reset</button>

            <hr style="margin:12px 0;">
           
        </div>
    </aside>

    <main class="main" aria-live="polite">
        <div id="productsContainer" class="card-grid"></div>
        <div class="pager" id="pager" role="navigation" aria-label="Pagination"></div>
    </main>
</div>
</section>

<div id="quickView" class="quickview-modal" aria-hidden="true">
    <span class="modal-close" onclick="closeQuickView()">✕</span>
    <div id="quickViewContent"></div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
const apiUrl = '<?= basename(__FILE__) ?>';
let state = {
    page: 1,
    limit: 12,
    group: '',
    category: '',
    subcategory: '',
    search: '',
    min_price: '',
    max_price: '',
    sale: 0,
    sort: 'new'
};

const productsContainer = document.getElementById('productsContainer');
const pager = document.getElementById('pager');
const filterGroup = document.getElementById('filterGroup');
const filterCategory = document.getElementById('filterCategory');
const filterSubcategory = document.getElementById('filterSubcategory');
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const applyBtn = document.getElementById('applyFilters');
const resetBtn = document.getElementById('resetFilters');

function fetchAndRender(page = 1) {
    state.page = page;

    const params = new URLSearchParams({
        fetch_products: 1,
        page: state.page,
        limit: state.limit,
        group: state.group,
        category: state.category,
        subcategory: state.subcategory,
        search: state.search,
        min_price: state.min_price,
        max_price: state.max_price,
        sale: state.sale,
        sort: state.sort
    });

    productsContainer.innerHTML = '<p style="grid-column:1/-1;text-align:center">Loading...</p>';

    fetch(apiUrl + '?' + params.toString())
    .then(res => res.json())
    .then(data => {
        if (!data.ok) {
            productsContainer.innerHTML = '<p style="grid-column:1/-1;text-align:center">Error loading products</p>';
            console.error(data.error || 'Unknown error');
            return;
        }
        renderProducts(data.products);
        renderPager(data.page, data.totalPages);
    })
    .catch(err => {
        console.error(err);
        productsContainer.innerHTML = '<p style="grid-column:1/-1;text-align:center">Error loading products</p>';
    });
}

function renderProducts(items) {
    if (!items || items.length === 0) {
        productsContainer.innerHTML = '<p style="grid-column:1/-1;text-align:center">No products found.</p>';
        return;
    }

    productsContainer.innerHTML = items.map(p => {
        const price = p.deal_price ? parseFloat(p.deal_price).toFixed(2) : parseFloat(p.original_price).toFixed(2);
        const oldPrice = p.deal_price ? parseFloat(p.original_price).toFixed(2) : null;
        const img = p.image_url ? p.image_url : 'assets/placeholder.png';
        return `
            <div class="card" data-id="${p.id}">
                <img src="${img}" alt="${escapeHtml(p.product_name)}">A
                <h4>${escapeHtml(p.product_name)}</h4>
                <div>
                    <span class="price">₹${price}</span>
                    ${ oldPrice ? `<span class="old-price">₹${oldPrice}</span>` : '' }
                </div>
                <div style="margin-top:6px;color:#666;font-size:0.9rem;">${escapeHtml(p.group_name || '')} › ${escapeHtml(p.category_name || '')} › ${escapeHtml(p.subcategory_name || '')}</div>
                ${ p.is_on_sale ? '<div class="badge" style="margin-top:8px">SALE</div>' : '' }
               <div class="controls" style="margin-top:12px;">
    <button class="btn btn-outline" onclick="openQuickView(${p.id})">Quick View</button>
    <form method="post" action="/online-computer-store/add_to_cart.php" class="addcart-form">
        <input type="hidden" name="product_id" value="${p.id}">
        <input type="hidden" name="quantity" value="1">
        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
    </form>
</div>

            </div>
        `;
    }).join('');
}

function renderPager(page, totalPages) {
    if (!totalPages || totalPages <= 1) { pager.innerHTML = ''; return; }
    let html = '';
    if (page > 1) html += `<button onclick="fetchAndRender(${page-1})">&laquo; Prev</button>`;
    const maxButtons = 7;
    let start = Math.max(1, page - Math.floor(maxButtons/2));
    let end = start + maxButtons - 1;
    if (end > totalPages) { end = totalPages; start = Math.max(1, end - maxButtons + 1); }

    for (let i = start; i <= end; i++) {
        if (i === page) html += `<button class="active" onclick="fetchAndRender(${i})">${i}</button>`;
        else html += `<button onclick="fetchAndRender(${i})">${i}</button>`;
    }

    if (page < totalPages) html += `<button onclick="fetchAndRender(${page+1})">Next &raquo;</button>`;
    pager.innerHTML = html;
}

function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]); }

filterGroup.addEventListener('change', () => {
    state.group = filterGroup.value;
    state.category = '';
    state.subcategory = '';
    filterCategory.innerHTML = '<option>Loading...</option>';
    filterSubcategory.innerHTML = '<option value="">All subcategories</option>';
    if (!filterGroup.value) {
        filterCategory.innerHTML = '<option value="">All categories</option>';
        return;
    }
    fetch(apiUrl + '?fetch_categories=' + encodeURIComponent(filterGroup.value))
        .then(r => r.json())
        .then(d => {
            filterCategory.innerHTML = '<option value="">All categories</option>';
            if (d.ok) d.categories.forEach(c => filterCategory.innerHTML += `<option value="${c.id}">${escapeHtml(c.category_name)}</option>`);
        });
});

filterCategory.addEventListener('change', () => {
    state.category = filterCategory.value;
    state.subcategory = '';
    filterSubcategory.innerHTML = '<option>Loading...</option>';
    if (!filterCategory.value) {
        filterSubcategory.innerHTML = '<option value="">All subcategories</option>';
        return;
    }
    fetch(apiUrl + '?fetch_subcategories=' + encodeURIComponent(filterCategory.value))
        .then(r => r.json())
        .then(d => {
            filterSubcategory.innerHTML = '<option value="">All subcategories</option>';
            if (d.ok) d.subcategories.forEach(s => filterSubcategory.innerHTML += `<option value="${s.id}">${escapeHtml(s.subcategory_name)}</option>`);
        });
});

filterSubcategory.addEventListener('change', () => {
    state.subcategory = filterSubcategory.value;
});

function openQuickView(id) {
    fetch(apiUrl + '?quick_view=' + encodeURIComponent(id))
        .then(r => r.json())
        .then(d => {
            if (!d.ok) { alert(d.msg || 'Not found'); return; }
            const p = d.product;
            const img = p.image_url ? p.image_url : 'assets/placeholder.png';
            document.getElementById('quickViewContent').innerHTML = `
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:280px">
                        <img src="${img}" style="width:100%;height:320px;object-fit:cover;border-radius:6px">
                    </div>
                    <div style="flex:1;min-width:280px">
                        <h2>${escapeHtml(p.product_name)}</h2>
                        <p style="color:#666">${escapeHtml(p.category_name || '')} • ${escapeHtml(p.group_name || '')} • ${escapeHtml(p.subcategory_name || '')}</p>
                        <p style="font-weight:700">Price: ₹${(p.deal_price || p.original_price).toFixed ? (p.deal_price || p.original_price).toFixed(2) : (p.deal_price || p.original_price)}</p>
                        <p>${escapeHtml(p.description || '')}</p>
                        <p>Stock: ${p.stock}</p>
                        <div style="margin-top:12px;">
<form method="post" action="/online-computer-store/add_to_cart.php" class="addcart-form">
    <input type="hidden" name="product_id" value="${p.id}">
    <input type="hidden" name="quantity" value="1">
    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
</form>

                            <button class="btn btn-outline" onclick="closeQuickView()">Close</button>
                        </div>

                    </div>
                </div>
            `;
            openModal();
        });
}
function openModal(){ document.getElementById('quickView').style.display = 'block'; document.getElementById('quickView').setAttribute('aria-hidden','false'); }
function closeQuickView(){ document.getElementById('quickView').style.display = 'none'; document.getElementById('quickView').setAttribute('aria-hidden','true'); }

applyBtn.addEventListener('click', () => {
    state.search = searchInput.value.trim();
    state.min_price = document.getElementById('minPrice').value || '';
    state.max_price = document.getElementById('maxPrice').value || '';
    state.sale = document.getElementById('saleOnly').checked ? 1 : 0;
    const sortVal = document.getElementById('sortBy').value;
    state.sort = (sortVal === 'price_asc') ? 'price_asc' : (sortVal === 'price_desc' ? 'price_desc' : 'new');
    state.page = 1;
    fetchAndRender(1);
});

resetBtn.addEventListener('click', () => {
    searchInput.value = '';
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    document.getElementById('saleOnly').checked = false;
    document.getElementById('sortBy').value = 'new';

    filterGroup.value = '';
    filterCategory.innerHTML = '<option value="">All categories</option>';
    filterSubcategory.innerHTML = '<option value="">All subcategories</option>';

    state = { page:1, limit:12, group:'', category:'', subcategory:'', search:'', min_price:'', max_price:'', sale:0, sort:'new' };
    fetchAndRender(1);
});

searchBtn.addEventListener('click', () => {
    state.search = searchInput.value.trim();
    state.page = 1;
    fetchAndRender(1);
});

searchInput.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') {
        state.search = searchInput.value.trim();
        state.page = 1;
        fetchAndRender(1);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    fetchAndRender(1);
});
</script>
</body>
</html>