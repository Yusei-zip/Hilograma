<?php if (!isset($pdo) || $pdo === null): ?>
        <div style="background: var(--error); color: white; text-align: center; padding: 15px; font-weight: bold;">
            ⚠️ El servidor de base de datos (XAMPP) no responde. Los datos no están disponibles.
        </div>
    <?php endif; ?>