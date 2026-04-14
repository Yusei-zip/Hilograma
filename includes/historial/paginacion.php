<?php if ($rango !== 'todos' && $total_paginas > 1): ?>
            <div class="pagination-container" style="display: flex; justify-content: center; gap: 5px; margin-top: 20px;">
                <a href="<?= $base_url ?>&p=1&rango=<?= $rango ?>"
                    class="page-link <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">«</a>
                <?php for ($i = max(1, $pagina_actual - 2); $i <= min($total_paginas, $pagina_actual + 2); $i++): ?>
                    <a href="<?= $base_url ?>&p=<?= $i ?>&rango=<?= $rango ?>"
                        class="page-link <?= ($i == $pagina_actual) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <a href="<?= $base_url ?>&p=<?= $total_paginas ?>&rango=<?= $rango ?>"
                    class="page-link <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">»</a>
            </div>
            <div style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 10px;">
                Mostrando <?= count($registros) ?> de <?= $total_registros ?> registros totales
            </div>
            <div style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 10px;">
                Mostrando 1 de <?= $total_paginas ?> Paginas totales
            </div>
        <?php endif; ?>

        <div class="qol-bar">
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Ver:
                <select onchange="location.href='<?= $base_url ?>&rango=' + this.value"
                    style="background: var(--input-bg); color: var(--text-main); border: 1px solid var(--border); padding: 4px; border-radius: 4px;">
                    <?php foreach ([10, 20, 50, 100, 'todos'] as $opc): ?>
                        <option value="<?= $opc ?>" <?= $rango == $opc ? 'selected' : '' ?>><?= ucfirst($opc) ?></option>
                    <?php endforeach; ?>
                </select>
                registros
            </div>

            <?php if ($rango !== 'todos' && $total_paginas > 1): ?>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Ir a página:</span>
                    <input type="number" id="jumpPage" min="1" max="<?= $total_paginas ?>" value="<?= $pagina_actual ?>"
                        style="width: 50px; text-align: center; background: var(--input-bg); color: var(--text-main); border: 1px solid var(--border); border-radius: 4px; padding: 4px;">
                    <button onclick="jumpToPage()" class="btn-icon"
                        style="background: var(--accent); padding: 4px; border-radius: 4px;">🚀</button>
                </div>
            <?php endif; ?>
        </div>