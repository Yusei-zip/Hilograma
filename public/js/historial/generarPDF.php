<script>


        function openModal(t) { document.getElementById('obsText').innerText = t; document.getElementById('obsModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('obsModal').style.display = 'none'; }

        function jumpToPage() {
            const p = document.getElementById('jumpPage').value;
            if (p >= 1 && p <= <?= $total_paginas ?? 1 ?>) {
                location.href = '<?= $base_url ?>&rango=<?= $rango ?>&p=' + p;
            }
        }

        function exportarExcel() {
            const emp = document.querySelector('select[name="id_empleado"]').value;
            const est = document.querySelector('select[name="estado"]').value;
            const mes = document.querySelector('select[name="mes"]').value; // <--- NUEVO
            window.location.href = `../logic/exportar_excel.php?id_empleado=${emp}&estado=${est}&mes=${mes}`;
        }

        loadTheme();

        async function generarPDF() {
            const card = document.querySelector('.card');
            const tabla = card.querySelector('table');
            const selectEmp = document.querySelector('select[name="id_empleado"]');
            const selectMes = document.querySelector('select[name="mes"]');

            // 1. Datos para el nombre
            const nombreEmp = selectEmp.options[selectEmp.selectedIndex].text;
            const nombreMes = selectMes.options[selectMes.selectedIndex].text;
            let nombreArchivo = `Reporte_${selectEmp.value ? nombreEmp : 'General'}_${selectMes.value ? nombreMes : 'Mes'}`;

            // 2. Capturar elementos para restaurar después
            const elementosAOcultar = [
                document.querySelector('form'),
                document.querySelector('.qol-bar'),
                document.querySelector('#themeToggler'),
                document.querySelector('div[style*="justify-content: center"]'),
                document.querySelector('div[style*="font-size: 0.8rem"]'),
                ...document.querySelectorAll('.btn-icon')
            ];
            const colAcciones = card.querySelectorAll('th:last-child, td:last-child');
            const celdas = card.querySelectorAll('td, th, strong, h2, span:not(.msg-alert)');

            // 3. Crear encabezado temporal
            const tempHeader = document.createElement('div');
            tempHeader.innerHTML = `
        <div id="pdf-temp-header" style="color: #000000; margin-bottom: 25px; border-bottom: 3px solid #333; padding-bottom: 10px; font-family: sans-serif;">
            <h1 style="margin:0; font-size: 28px;">Reporte de Asistencias</h1>
            <p style="margin:8px 0; font-size: 14px;">
                <b>Empleado:</b> ${selectEmp.value ? nombreEmp : 'Todos'} | 
                <b>Mes:</b> ${selectMes.value ? nombreMes : 'Todos'}
            </p>
            <p style="margin:0; font-size: 11px; color: #000000;">Emitido el: ${new Date().toLocaleString()}</p>
        </div>
    `;

            // Guardar estilos originales
            const originalStyles = {
                maxWidth: card.style.maxWidth,
                boxShadow: card.style.boxShadow,
                borderRadius: card.style.borderRadius,
                backgroundColor: card.style.backgroundColor
            };

            try {
                // --- TRANSFORMACIÓN TEMPORAL ---
                elementosAOcultar.forEach(el => { if (el) el.style.setProperty('display', 'none', 'important'); });
                colAcciones.forEach(el => el.style.setProperty('display', 'none', 'important'));
                card.prepend(tempHeader);

                if (tabla) tabla.style.width = "100%";
                card.style.maxWidth = "none";
                card.style.boxShadow = "none";
                card.style.borderRadius = "0";
                card.style.backgroundColor = "transparent";
                celdas.forEach(c => c.style.setProperty('color', '#000', 'important'));

                // --- GENERACIÓN ---
                const opciones = {
                    margin: [0.4, 0.4],
                    filename: `${nombreArchivo.replace(/\s+/g, '_')}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 3, useCORS: true, backgroundColor: '#000000' },
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' },
                    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
                };

                await html2pdf().set(opciones).from(card).save();

            } catch (error) {
                console.error("Error al generar PDF:", error);
                alert("Hubo un error al generar el PDF, pero restauraremos la página.");
            } finally {
                // --- RESTAURACIÓN TOTAL (El seguro de vida) ---
                tempHeader.remove();
                elementosAOcultar.forEach(el => { if (el) el.style.display = ''; });
                colAcciones.forEach(el => el.style.display = '');
                if (tabla) tabla.style.width = "";

                card.style.maxWidth = originalStyles.maxWidth;
                card.style.boxShadow = originalStyles.boxShadow;
                card.style.borderRadius = originalStyles.borderRadius;
                card.style.backgroundColor = originalStyles.backgroundColor;
                celdas.forEach(c => c.style.color = '');

                console.log("Página restaurada correctamente.");
            }
            location.reload();
        }


    </script>