<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php if(isset($_GET['error'])): ?>
    <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if(isset($_GET['success'])): ?>
    <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<form id="signupForm" action="signup_process.php" method="post" novalidate>
    <div class="form-group">
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
        <small>Minimum 8 characters, at least one letter and one number.</small>
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input id="confirm_password" name="confirm_password" type="password" required>
    </div>

    <div style="text-align:center;margin-top:10px">
        <button class="btn" type="submit">Sign Up</button>
        <a class="btn btn--muted" href="login.php">Already have an account?</a>
    </div>
</form>

<script>
document.getElementById('signupForm').addEventListener('submit', function(e){
    var name = document.getElementById('name').value.trim();
    var email = document.getElementById('email').value.trim();
    var pass = document.getElementById('password').value;
    var confirm = document.getElementById('confirm_password').value;

    var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var passReg = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

    if(!name){ alert('Please enter your name'); e.preventDefault(); return; }
    if(!emailReg.test(email)){ alert('Enter a valid email'); e.preventDefault(); return; }
    if(!passReg.test(pass)){ alert('Password should be minimum 8 chars with at least one letter and one number'); e.preventDefault(); return; }
    if(pass !== confirm){ alert('Passwords do not match'); e.preventDefault(); return; }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>