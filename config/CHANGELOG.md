v1.0.0 - Implementación:
13/mar/2026 
    Added
        Gestión de Empleados:  Módulo completo para el alta y administración de personal.
            Visualización de resumen detallado por empleado.
            Generación automática de nóminas en formato PDF y Excel.

        Control de Asistencias:
            Gestor de asistencia individual y por fechas.
            Carga Masiva: Implementación de importación de datos por lotes para optimizar tiempos.
            Configuración de nómina: Capacidad para incluir o excluir empleados específicos del registro de pagos.
            Historial centralizado y reportes de asistencia.

        Seguridad y Acceso:
            Sistema de autenticación con validación estricta de credenciales.
            Terminal "Secreto": Consola avanzada para la gestión directa de la base de datos (acceso restringido).

        Interfaz y UX:
            Dashboard interactivo con métricas clave.
            Diseño intuitivo con soporte para múltiples temas y paletas de colores.
            Reloj en tiempo real y monitor de estado del servidor integrados.

    Technical
        Base de Datos: Implementación de arquitectura en MySQL.
            Conectividad: Integración de capa de persistencia mediante PDO para mejorar la seguridad contra inyecciones SQL y la portabilidad.
            Despliegue: Instalación inicial y configuración del entorno de producción.

v1.0.1 - Bug fixes:
16/mar/2026 - 25/mar/2026
    Added
        Mantenimiento:
            Adicion del boton de cambiar de tema

    Changed  
        Funcion de cambiar tema actualizada

    Fixed
        Mantenimiento:
            Error al momento de simular datos (Se hacia una mala referencia de base de datos)
            Error al momento de optimizar tablas y reiniciar las id.

        Empleados:
            Al momento de consultar el total de los cargos individuales en el dashboard de los empleados, traia la id, no el nombre completo del cargo, ahora trae el nombre con la cantidad abajo.
            Al momento de traer el cargo y hacer display en la tabla, traia la id del cargo, más no su nombre, ahora lo hace correctamente
            El estado de "Nomina o Baja" ya hace display automtico y correcto de los datos como debería.
            Display del .card con tamaños correspondientes corregidos

        Asistencia:
            Icono vacio eliminado
    
    Trashed
        Empleados:
            En el dashboard de empleados el desglose por subcargo queda eliminado, ahora solamente se motrará en la tabla.

v.1.0.2 - Bug fixes:
27/mar/2026 - 27/mar/2026

    Changed
        Ahora en el dashboard se discrimina si un personal esta en nomina o no, se deja bien en claro cuantos trabajan y están en nomina, a partir de ahora, se usarán solamente esos datos.

        Empleados:
            Ahora en el dashboard de empleados, se agregó la posibilidad de ver el total de cargos por empleado activo, pero sigue filtrando mal, es una medida paliativa.
            Se cambió un poco el diseño de la pagina



    Fixed
        Asistencias
                Al momento de editar un registro desde el resumen, el dato no se guardaba y mucho menos se guardaba en base de datos, ahora lo hace correctamente
                Al momento de editar un registro, 'Si es que un valor estaba como ausente, dejaba guardar sub estados' ahora esto funciona logicamente impidiendo esto //Revisar a futuro la funcion toggleSubEstado();
                El boton de "Ojo" para ver las observaciones no funcionaba correctamente, ahora funciona correctamente.

v.1.1.0 - New freatures
