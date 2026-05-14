<?php
    session_start();
    $id_profe = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 3;

    include('clases/claseProfesores.php');
    $objProfesor = new profesor();

    if (isset($_POST['guardar'])) {
    // Estos IDs deben ser los de las tablas 'alumno' y 'grupo', no de 'usuario'
    $alumno_id = $_POST['alumno_id']; 
    $grupo_id = $_POST['grupo_id'];
    
    // Convertimos a float para asegurar que la base de datos lo acepte (ej: 8.5)
    if ($_POST['n1'] !== '') $objProfesor->IngresarCalificacion($alumno_id, $grupo_id, floatval($_POST['n1']), 1);
    if ($_POST['n2'] !== '') $objProfesor->IngresarCalificacion($alumno_id, $grupo_id, floatval($_POST['n2']), 2);
    if ($_POST['n3'] !== '') $objProfesor->IngresarCalificacion($alumno_id, $grupo_id, floatval($_POST['n3']), 3);

    header("Location: panelProfesor.php?accion=listarccalificacion&alumno_id=" . $alumno_id);
    exit();
}

    $grupos = $objProfesor->Listar_mis_grupos($id_profe);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Maestro - Colegio Nuevo Futuro</title>
    <link rel="stylesheet" href="visual_maestro.css">
</head>
<body>
<div class="contenedor">
    <aside class="sidebar">
        <div class="logo">Colegio<br><span>Nuevo Futuro</span></div>
        <nav class="menu-lateral">
            <h3 style="color: white; padding: 10px;">Mis Clases</h3>
            <?php
                if ($grupos && $grupos->num_rows > 0) {
                    while ($g = $grupos->fetch_assoc()) {
                        echo "<a href='?accion=listara&grupo_id={$g['id']}'>
                                {$g['asignatura']} ({$g['grado']}°{$g['letra_grupo']})
                            </a>";
                    }
                }
            ?>
            <hr>
            <a href="?accion=listarp">Directorio Profesores</a>
            <a href="?accion=listarccalificacion">Reporte General de Notas</a>
        </nav>
    </aside>

    <main class="contenido">
        <header class="barra-superior">
            <h2>Bienvenido al panel del Profesor</h2>
            <div class="perfil">
                <span class="nombre">Profesor</span>
                <div class="circulo"></div>
                <div class="menu">
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>
        </header>

        <div class="panel-vacio">
            <?php           
                $accion = isset($_GET['accion']) ? $_GET['accion'] : '';
                $grupo_id = isset($_GET['grupo_id']) ? $_GET['grupo_id'] : null;
                $id_alumno_url = isset($_GET['alumno_id']) ? $_GET['alumno_id'] : null;

                if ($accion == 'listarp') {
                    $resultado = $objProfesor->Listar_todos_los_profesores();
                    echo "<h2>Tus profesores</h2><table class='tabla-alumno'><thead><tr><th>Nombre</th><th>Especialidad</th><th>Email</th></tr></thead><tbody>";
                    while ($datos = $resultado->fetch_assoc()) {
                        echo "<tr><td>{$datos['nombre']}</td><td>{$datos['especialidad']}</td><td>{$datos['email']}</td></tr>";
                    }
                    echo "</tbody></table>";
                } 

                if ($accion == 'listara' && $grupo_id) {
                    $res = $objProfesor->Listar_alumnos($grupo_id);
                    echo "<h2>Alumnos del Grupo</h2><table class='tabla-alumno'><thead><tr><th>Nombre</th><th>Acciones</th></tr></thead><tbody>";
                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr><td>{$fila['nombre']}</td><td>
                                <a href='?accion=listarccalificacion&alumno_id={$fila['id']}' class='btn-regresar' style='background:#2ecc71'>Ver Notas</a>
                                <a href='?accion=agregarcalificacion&alumno_id={$fila['id']}&grupo_id=$grupo_id' class='btn-regresar'>Poner Nota</a>
                            </td></tr>";
                    }
                    echo "</tbody></table>";
                }

                if ($accion == 'agregarcalificacion' && $id_alumno_url) {
                    $g_id = $_GET['grupo_id'];
                    $resultado_nota = $objProfesor->Listar_notas_por_profesor($id_alumno_url, $id_profe);
                    $n1 = $n2 = $n3 = "";

                    if ($resultado_nota && $resultado_nota->num_rows > 0) {
                        $row = $resultado_nota->fetch_assoc();
                        // Asignamos las notas actuales
                        $n1 = $row['calificacion_1'];
                        $n2 = $row['calificacion_2'];
                        $n3 = $row['calificacion_3'];
                    }

                    // Creamos variables para bloquear (si la nota es mayor a 0, se bloquea)
                    $bloqueo1 = ($n1 > 0) ? "readonly style='background-color: #e9e9e9;'" : "";
                    $bloqueo2 = ($n2 > 0) ? "readonly style='background-color: #e9e9e9;'" : "";
                    $bloqueo3 = ($n3 > 0) ? "readonly style='background-color: #e9e9e9;'" : "";

                    echo "<h2>Ingresar Calificación</h2>
                        <form action='panelProfesor.php' method='POST' class='form-calificar'>
                            <input type='hidden' name='alumno_id' value='$id_alumno_url'>
                            <input type='hidden' name='grupo_id' value='$g_id'>
                            
                            <label>Parcial 1:</label>
                            <input type='number' name='n1' step='0.1' value='$n1' $bloqueo1><br>
                            
                            <label>Parcial 2:</label>
                            <input type='number' name='n2' step='0.1' value='$n2' $bloqueo2><br>
                            
                            <label>Parcial 3:</label>
                            <input type='number' name='n3' step='0.1' value='$n3' $bloqueo3><br>
                            
                            <button type='submit' name='guardar' class='btn-regresar'>Guardar Notas</button>
                            <a href='?accion=listara&grupo_id=$g_id'>Cancelar</a>
                        </form>";
                }

                                // CASO 3: VER CALIFICACIONES
                if ($accion == 'listarccalificacion') {
                    if ($id_alumno_url) {
                        $resultado = $objProfesor->Listar_notas_por_profesor($id_alumno_url, $id_profe);
                        echo "<h2>Notas de mi Asignatura - Alumno Seleccionado</h2>";
                    } else {
                        $resultado = $objProfesor->Listar_todas_mis_notas($id_profe); 
                        echo "<h2>Reporte General de Calificaciones</h2>";
                    }

                    if ($resultado && $resultado->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead>
                                    <tr>
                                        <th>Alumno</th>
                                        <th>Asignatura</th>
                                        <th>P1</th>
                                        <th>P2</th>
                                        <th>P3</th>
                                        <th>Promedio</th>
                                    </tr>
                                </thead>
                                <tbody>";
                        while ($datos = $resultado->fetch_assoc()) {
                            // CORRECCIÓN: Usamos 'nombre_alumno' si viene de la lista general
                            // o 'nombre' si viene de la lista individual.
                            $nombre_mostrar = isset($datos['nombre_alumno']) ? $datos['nombre_alumno'] : $datos['nombre'];
                            
                            echo "<tr>
                                    <td>{$nombre_mostrar}</td>
                                    <td>{$datos['asignatura']}</td>
                                    <td>{$datos['calificacion_1']}</td>
                                    <td>{$datos['calificacion_2']}</td>
                                    <td>{$datos['calificacion_3']}</td>
                                    <td><strong>{$datos['promedio_final']}</strong></td>
                                </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No hay notas registradas para tus grupos todavía.</p>";
                    }
                }
             ?> 
        </div>
    </main>
</div>
</body>
</html>