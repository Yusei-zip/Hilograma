function generarPDF() {
    // 1. Elementos que NO queremos en el PDF (filtros, botones, navegación)
    const elementosAOcultar = [
        document.querySelector('form'), 
        document.querySelector('.qol-bar'),
        document.querySelector('#themeToggler'),
        ...document.querySelectorAll('.btn-icon'), // Iconos de editar/borrar
        document.querySelector('div[style*="justify-content: center"]') // Botones de navegación inferior
    ];

    // 2. Ocultamos temporalmente
    elementosAOcultar.forEach(el => el?.classList.add('no-export'));

    // 3. Configuración del PDF
    const contenido = document.querySelector('.card');
    const opciones = {
        margin:       [0.5, 0.5],
        filename:     'Reporte_Asistencia.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, logging: false, useCORS: true },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    // 4. Generar y descargar
    html2pdf().set(opciones).from(contenido).save().then(() => {
        // 5. Al terminar, volvemos a mostrar todo
        elementosAOcultar.forEach(el => el?.classList.remove('no-export'));
    });
}