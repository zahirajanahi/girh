<?php if (isLoggedIn()): ?>
        </main>
        <footer class="footer">
            <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> — <?= e(APP_SUBTITLE) ?></p>
        </footer>
    </div>
</div>
<?php else: ?>
</div>
<?php endif; ?>

<?php if (!empty($extraJs)): ?>
    <?php foreach ($extraJs as $js): ?>
        <script src="<?= e($js) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
<script src="js/app.js"></script>
</body>
</html>
