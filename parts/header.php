<div class="header">
    <nav>
        <div>
            <a href="./index.php" class="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])) == 'index.php' ? 'active' : ''; ?>">Home</a>
        </div>
        <div>
            <?php if (isset($_SESSION['userid'])): ?>
                <a href="./bookadd.php" class="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])) == 'bookadd.php' ? 'active' : ''; ?>">New book</a>
                <a href="./profile.php" class="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])) == 'profile.php' ? 'active' : ''; ?>">Profile</a>
                <a href="./logout.php">Logout</a>
            <?php else: ?>
                <a href="./login.php" class="<?php echo htmlspecialchars (basename($_SERVER['PHP_SELF'])) == 'login.php' ? 'active' : ''; ?>">Login</a>
                <a href="./register.php" class="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])) == 'register.php' ? 'active' : ''; ?>">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</div>

