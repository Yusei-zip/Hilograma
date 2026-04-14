<script>

const subCargosBD = <?php echo json_encode($subCargosBD); ?>;

        function checkSubCargos() {
            const cargoId = document.getElementById('cargoSelect').value;
            const filtrados = subCargosBD.filter(s => s.id_cargo_padre == cargoId);
            const container = document.getElementById('listaSubCargos');

            if (filtrados.length > 0) {
                container.innerHTML = "";
                filtrados.forEach(sub => {
                    container.innerHTML += `
                        <label style="font-size: 0.85rem; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" class="sub-check" value="${sub.nombre_subcargo}"> ${sub.nombre_subcargo}
                        </label>`;
                });
                document.getElementById('modalSub').style.display = 'flex'; // Aquí se muestra
            } else {
                document.getElementById('sub_cargos_final').value = "";
            }
        }

        function cerrarModal() {
            const selected = Array.from(document.querySelectorAll('.sub-check:checked')).map(c => c.value);
            document.getElementById('sub_cargos_final').value = selected.join(', ');
            document.getElementById('modalSub').style.display = 'none'; // Aquí se oculta
        }

        function prepararEnvio() { return true; }

        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });

        document.addEventListener('DOMContentLoaded', () => {
            const alertMsg = document.getElementById('alert-msg');
            if (alertMsg) {
                // Espera 3 segundos (3000ms) y luego cambia la opacidad
                setTimeout(() => {
                    alertMsg.style.opacity = '0';
                    // Después de la transición de desvanecimiento, lo elimina del espacio
                    setTimeout(() => {
                        alertMsg.style.display = 'none';
                    }, 500);
                }, 3000);
            }
        });

    </scrip>