<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$search = trim($_GET['search'] ?? '');
$success = '';
$error = '';
if (isset($_GET['delete'])) {
    $delId = intval($_GET['delete']);
    if (empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header('Location: userdetails.php?msg=forbidden');
        exit;
    }

    try {
        $check = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $check->execute([$delId]);
        $row = $check->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            header('Location: userdetails.php?msg=not_found');
            exit;
        }

        if ((int)$row['is_admin'] === 1) {
            header('Location: userdetails.php?msg=cannot_delete_admin');
            exit;
        }

        $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $del->execute([$delId]);
        header('Location: userdetails.php?msg=deleted');
        exit;
    } catch (PDOException $e) {
        header('Location: userdetails.php?msg=error');
        exit;
    }
}

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
<link rel="stylesheet" href="assets/css/userdetails.css">
<div class="content" id="content">
    <div class="wrap">

        <h2>Users</h2>

        <?php if (isset($_GET['msg'])): 
            $m = $_GET['msg'];
            if ($m === 'deleted') $success = 'User deleted successfully.';
            if ($m === 'cannot_delete_admin') $error = 'Admin accounts cannot be deleted.';
            if ($m === 'not_found') $error = 'User not found.';
            if ($m === 'forbidden') $error = 'You do not have permission to perform that action.';
            if ($m === 'error') $error = 'An error occurred while trying to delete the user.';
        ?>
            <div class="adm-messages" style="margin-bottom:1rem;">
                <?php if ($success): ?>
                    <div class="adm-alert adm-alert-success"><span class="adm-alert-icon">✓</span> <?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="adm-alert adm-alert-danger"><span class="adm-alert-icon">⚠️</span> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

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
        <th>Actions</th>
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
            <td>
                <?php if ($user['is_admin']): ?>
                    <span style="opacity:0.8;color:#7f1d1d;font-weight:700;">Protected</span>
                <?php else: ?>
                    <form method="get" action="userdetails.php" style="display:inline; margin:0;">
                        <input type="hidden" name="delete" value="<?= (int)$user['id'] ?>">
                        <button type="submit" class="adm-btn adm-btn-danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" style="text-align:center;">No users found.</td>
        </tr>
    <?php endif; ?>
</table>
    </div>
</div>

</body>
</html>