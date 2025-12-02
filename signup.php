<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="form-card">
    <h2>Create Account</h2>
    
    <?php if(isset($_GET['error'])): ?>
        <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
        <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <form id="signupForm" action="signup_process.php" method="post" novalidate>
        <div class="form-group">
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" placeholder="John Doe" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" placeholder="john@example.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="••••••••" required>
            <small>Minimum 8 characters, at least one letter and one number.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" placeholder="••••••••" required>
        </div>

        <div class="form-actions">
            <button class="btn" type="submit">Create Account</button>
            <a class="btn btn--muted" href="login.php">Already have an account?</a>
        </div>
    </form>

    <div class="form-link">
        By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
    </div>
</div>

<script src="assests/js/signup.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>