<?php require_once __DIR__ . '/includes/header.php'; ?>


<div class="form-card">
<h2>Login</h2>
<?php if(isset($_GET['error'])): ?>
<div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>


<form id="loginForm" action="login_process.php" method="post" novalidate>
<div class="form-group">
<label for="email">Email</label>
<input id="email" name="email" type="email" required>
</div>


<div class="form-group">
<label for="password">Password</label>
<input id="password" name="password" type="password" required>
</div>


<div style="text-align:center;margin-top:10px">
<button class="btn" type="submit">Login</button>
<a class="btn btn--muted" href="signup.php">Create an account</a>
</div>
</form>
</div>


<script>
document.getElementById('loginForm').addEventListener('submit', function(e){
var email = document.getElementById('email').value.trim();
var pass = document.getElementById('password').value;
if(!email || !pass){ alert('Please fill both fields'); e.preventDefault(); }
});
</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>