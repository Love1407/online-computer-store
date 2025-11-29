<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Enable error display for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =========================
// SEARCH PARAMETER
// =========================
$search = trim($_GET['search'] ?? '');

// =========================
// FETCH USERS
// =========================
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

<div class="content" id="content">
    <div class="wrap">

        <h2>Users</h2>

        <!-- Search Form -->
        <form method="get" style="margin-bottom:15px;">
            <input type="text" name="search" placeholder="Search by name, or email" 
                   value="<?= htmlspecialchars($search) ?>" style="width:300px; padding:6px;">
            <button type="submit">Search</button>
            <?php if($search !== ''): ?>
                <a href="userdetails.php"><button type="button">Reset</button></a>
            <?php endif; ?>
        </form>

        <!-- Users Table -->
        <!-- Users Table -->
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
            <td><?= $user['is_admin'] ? 'Admin' : 'User' ?></td>
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
