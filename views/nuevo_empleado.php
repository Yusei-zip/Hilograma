<?php
session_start();

// Si no existe la sesión, lo mandamos al login de una
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

include_once '../config/db.php';
?>
<div class="form-group">
    <label>Cargo Principal</label>
    <select name="cargo" id="cargoSelect" onchange="checkSubCargos()" required>
        <option value="Aseo">Aseo</option>
        <option value="Planchador">Planchador</option>
        <option value="Chofer">Chofer</option>
        <option value="Serigrafista">Serigrafista</option>
        <option value="Costurero">Costurero</option>
    </select>
</div>

<div class="form-group">
    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
        <input type="checkbox" name="is_active" checked style="width: auto;"> 
        ¿Está Contratado? (Activo en Nómina)
    </label>
</div>

<div id="modalSubCargos" class="modal-overlay">
    <div class="modal-content card" style="max-width: 400px;">
        <h3>🛠️ Especialidades de Costura</h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">Selecciona las máquinas que opera el trabajador:</p>
        
        <div id="listaSubCargos" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
            </div>

        <button type="button" onclick="closeSubModal()" class="btn btn-primary" style="width: 100%;">Confirmar Selección</button>
    </div>
</div>

<script>
const subCargosData = {
    "Costurero": [
        "Overlock", "Collareta", "Recta", "Botonera", "Ojaladora", 
        "Atracadora", "Engomadora", "Cinturera", "Cadeneta", 
        "Pasacintera", "Doble", "Cortabies"
    ]
};

function checkSubCargos() {
    const cargo = document.getElementById('cargoSelect').value;
    const listaDiv = document.getElementById('listaSubCargos');
    const modal = document.getElementById('modalSubCargos');

    if (subCargosData[cargo]) {
        listaDiv.innerHTML = ""; // Limpiar
        subCargosData[cargo].forEach(sub => {
            listaDiv.innerHTML += `
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; cursor: pointer;">
                    <input type="checkbox" name="sub_cargos[]" value="${sub}" style="width: auto;"> ${sub}
                </label>
            `;
        });
        modal.style.display = 'flex';
    }
}

function closeSubModal() {
    document.getElementById('modalSubCargos').style.display = 'none';
}
</script>