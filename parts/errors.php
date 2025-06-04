<div class="error-messages">
    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li> <?php echo isset($error) ? htmlspecialchars($error) : ''; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
