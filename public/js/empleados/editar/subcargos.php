<?php
const subCargosData = {
            "Costurero": ["Overlock", "Collareta", "Recta", "Botonera", "Ojaladora", "Atracadora", "Engomadora", "Cinturera", "Cadeneta", "Pasacintera", "Doble", "Cortabies"],
            "Serigrafista": ["Revelado", "Estampado Manual", "Pulpo", "Secado"]
        };

        // Al cargar la página, memorizamos el cargo actual
        const select = document.getElementById('cargoSelect');
        let cargoActualNombre = select.options[select.selectedIndex].text;
        let seleccionados = <?= json_encode($subCargosGuardados) ?>;

        function checkSubCargos() {
            const nuevoCargoNombre = select.options[select.selectedIndex].text;
            const listaDiv = document.getElementById('listaSubCargos');
            const modal = document.getElementById('modalSubCargos');
            const inputFinal = document.getElementById('sub_cargos_final');

            // SI EL CARGO CAMBIÓ: Limpiamos los subcargos anteriores inmediatamente
            if (nuevoCargoNombre !== cargoActualNombre) {
                inputFinal.value = "";
                seleccionados = [];
                cargoActualNombre = nuevoCargoNombre; // Actualizamos el rastreador
            }

            // Si el cargo tiene máquinas, abrimos el modal
            if (subCargosData[nuevoCargoNombre]) {
                document.getElementById('modalTitle').innerText = `🛠️ Especialidades: ${nuevoCargoNombre}`;
                listaDiv.innerHTML = "";
                subCargosData[nuevoCargoNombre].forEach(sub => {
                    const isChecked = seleccionados.includes(sub) ? 'checked' : '';
                    listaDiv.innerHTML += `
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; cursor: pointer; padding: 5px; border-radius: 4px;">
                        <input type="checkbox" class="check-sub" value="${sub}" ${isChecked}> ${sub}
                    </label>
                `;
                });
                modal.style.display = 'flex';
            } else {
                // Si el cargo no tiene subcargos, el input se queda vacío
                inputFinal.value = "";
            }
        }

        function closeSubModal() {
            const checks = document.querySelectorAll('.check-sub:checked');
            const values = Array.from(checks).map(c => c.value);

            document.getElementById('sub_cargos_final').value = values.join(', ');
            seleccionados = values;

            document.getElementById('modalSubCargos').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera (opcional, por usabilidad)
        window.onclick = function (event) {
            const modal = document.getElementById('modalSubCargos');
            if (event.target == modal) { closeSubModal(); }
        }

        // Aplicar tema guardado
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'dark');