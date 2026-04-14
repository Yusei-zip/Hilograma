 <footer class="dashboard-footer">
        <div class="footer-section">
            <span class="status-dot <?= $db_status ? 'status-online' : 'status-offline' ?>"></span>
            <p>Estado del Servidor: <strong><?= $db_status ? ' MySQL Activo' : 'Error de Conexión' ?></strong>
            </p>
            <?php if ($db_status): ?>
                <span class="db-v">v<?= explode('-', $db_version)[0] ?></span>
            <?php endif; ?>
        </div>

        <div class="footer-section">
            <p>Versión del Sistema <span class="version-tag">v1.0.2-unstable</span></p>
        </div>

        <div class="footer-section">
            <p id="footer-clock">00:00:00</p>
        </div>
    </footer>