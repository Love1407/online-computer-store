<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$search = trim($_GET['search'] ?? '');

try {
    $sql = "SELECT id, name, email, is_admin, created_at FROM users";
    $params = [];

    if ($search !== '') {
        $sql .= " WHERE name LIKE ? OR email LIKE ? OR is_admin LIKE ?";
        $searchParam = "%$search%";
        $params = [$searchParam, $searchParam, $searchParam];
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

?>

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<style>
:root{
        --ud-bg: #f8fafc;
        --ud-card: #ffffff;
        --ud-primary: #2563eb;
        --ud-primary-2: #3b82f6;
        --ud-dark: #0f172a;
        --ud-gray: #6b7280;
        --ud-radius: 10px;
        --ud-shadow: 0 8px 24px rgba(15,23,42,0.06);
}

#admSidebar ~ .content {
    margin-left: var(--adm-sidebar-width, 280px);
    transition: margin-left 0.3s ease, padding 0.3s ease;
}

#admSidebar.adm-collapsed ~ .content {
    margin-left: var(--adm-sidebar-collapsed, 80px);
}

@media (max-width: 1024px) {
    #admSidebar ~ .content { margin-left: 0 !important; }
}

.content{
    padding:2rem;
    background:linear-gradient(180deg,#f8fafc,#eef2f7);
    min-height:100vh;
    transition: all 0.25s ease;
}
.wrap{
    max-width:1100px;
    margin:0 auto
}

h2{
    font-size:1.6rem;
    margin-bottom:1rem;
    color:var(--ud-dark);
    font-weight:800
}

.search-form{
    display:flex;
    gap:0.5rem;
    align-items:center;
    margin-bottom:1rem
}

.search-form input[type="text"]{
    width:320px;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #e6edf3;
    background:#fff;
    font-size:0.95rem
}

.search-form button{
    padding:10px 14px;
    border-radius:8px;
    border:none;
    background:var(--ud-primary);
    color:#fff;
    font-weight:700;
    cursor:pointer
}

.search-form a button{
    background:#f3f4f6;
    color:var(--ud-dark);
    border:1px solid #e6edf3
}

table{
    width:100%;
    border-collapse:collapse;
    border-radius:var(--ud-radius);
    overflow:hidden;
    background:var(--ud-card);
    box-shadow:var(--ud-shadow)
}

thead tr{
    background:linear-gradient(90deg,var(--ud-primary),var(--ud-primary-2));
    color:#fff;
    text-align:left
}

th,td{
    padding:12px 16px;
    border-bottom:1px solid #f3f6fa;
    font-weight:600;
    font-size:0.95rem
}

tbody tr{
    background:transparent
}

tbody tr:nth-child(even){
    background:#fbfdff
}

tbody tr:hover{
    background:#f1f5f9;
    transform:translateY(-1px);
    transition:all .15s
}

.role-badge{
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-weight:800;
    font-size:0.85rem
}

.role-admin{
    background:linear-gradient(90deg,#fff1f2,#fecaca);
    color:#7f1d1d;
    border:1px solid rgba(239,68,68,0.08)
}

.role-user{
    background:linear-gradient(90deg,#ecfdf5,#bbf7d0);
    color:#065f46;
    border:1px solid rgba(16,185,129,0.08)
}

.no-results{
    padding:3rem;
    text-align:center;
    color:var(--ud-gray)
}

@media (max-width:900px){
    .search-form{
        flex-direction:column;
        align-items:stretch
    }
    .search-form input[type="text"]{
        width:100%
    }
    th,td{
        padding:10px
    }
}
</style>

<div class="content" id="content">
    <div class="wrap">

        <h2>Users</h2>

        <form method="get" class="search-form">
            <input type="text" name="search" placeholder="Search by name, or email" 
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <?php if($search !== ''): ?>
                <a href="userdetails.php"><button type="button">Reset</button></a>
            <?php endif; ?>
        </form>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Created At</th>
    </tr>

    <?php if(count($users) > 0): ?>
        <?php foreach($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td>
                <span class="<?= $user['is_admin'] ? 'role-badge role-admin' : 'role-badge role-user' ?>">
                    <?= $user['is_admin'] ? 'Admin' : 'User' ?>
                </span>
            </td>
            <td><?= htmlspecialchars($user['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" style="text-align:center;">No users found.</td>
        </tr>
    <?php endif; ?>
</table>


    </div>
</div>

</body>
</html>