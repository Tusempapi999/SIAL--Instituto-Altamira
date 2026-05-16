<?php
    session_start();
    date_default_timezone_set('America/Mexico_City'); 

    $id_profe = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 3;

    include('clases/claseProfesores.php');
    $objProfesor = new profesor();

    // ACCIÓN POST 1: GUARDAR CALIFICACIONES
    if (isset($_POST['guardar'])) {
        $alumno_id = $_POST['alumno_id']; 
        $grupo_id = $_POST['grupo_id'];
        
        if ($_POST['n1'] !== '') $objProfesor->IngresarCalificacion($alumno_id, $grupo_id, floatval($_POST['n1']), 1);
        if ($_POST['n2'] !== '') $objProfesor->IngresarCalificacion($alumno_id, $grupo_id, floatval($_POST['n2']), 2);
        if ($_POST['n3'] !== '') $objProfesor->IngresarCalificacion($alumno_id, $grupo_id, floatval($_POST['n3']), 3);

        header("Location: panelProfesor.php?accion=listarccalificacion&alumno_id=" . $alumno_id . "&msj=ok");
        exit();
    }

    // ACCIÓN POST 2: GUARDAR ASISTENCIA COLECTIVA
    if (isset($_POST['guardar_asistencia'])) {
        $grupo_id = $_POST['grupo_id_general'];
        $fecha_actual = date('Y-m-d'); 

        if (isset($_POST['asistencias']) && is_array($_POST['asistencias'])) {
            foreach ($_POST['asistencias'] as $alumno_id => $datos_asistencia) {
                $estado = isset($datos_asistencia['estado']) ? $datos_asistencia['estado'] : '';
                if (!empty($estado)) {
                    $objProfesor->Registrar_asistencia_por_profesor($alumno_id, $id_profe, $grupo_id, $fecha_actual, $estado);
                }
            }
        }

        header("Location: panelProfesor.php?accion=listara&grupo_id=" . $grupo_id . "&msj=asistencia_ok");
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
                    <div class="notificaciones">Opciones</div>
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>
        </header>

        <div class="panel-vacio">
            <?php           
                $accion = isset($_GET['accion']) ? $_GET['accion'] : '';
                $grupo_id = isset($_GET['grupo_id']) ? $_GET['grupo_id'] : null;
                $id_alumno_url = isset($_GET['alumno_id']) ? $_GET['alumno_id'] : null;

                if (isset($_GET['msj']) && $_GET['msj'] == 'asistencia_ok') {
                    echo "<p style='color:green; font-weight:bold; margin-bottom: 15px;'>¡Asistencia procesada correctamente en la Base de Datos!</p>";
                }

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
                    echo "<h2>Lista de Alumnos de la clase</h2><table class='tabla-alumno'><thead><tr><th>Nombre</th><th>Acciones</th></tr></thead><tbody>";
                    echo "<a href='?accion=asistencia&grupo_id=$grupo_id' class='btn-regresar' style='background:#3498db; margin-bottom:15px; display:inline-block;'>Tomar Lista de Hoy</a><br><br>";
                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr><td>{$fila['nombre']}</td><td>
                                <a href='?accion=listarccalificacion&alumno_id={$fila['alumno_id']}' class='btn-regresar' style='background:#2ecc71'>Ver Notas</a>
                                <a href='?accion=agregarcalificacion&alumno_id={$fila['alumno_id']}&grupo_id=$grupo_id' class='btn-regresar'>Poner Nota</a>
                                <a href='?accion=asistenciamodificar&alumno_id={$fila['alumno_id']}&nombre={$fila['nombre']}&grupo_id=$grupo_id' class='btn-regresar' style='background:#ff9008'>
                                    Modificar Asistencia
                                </a>                       
                                </td></tr>";
                    }
                    echo "</tbody></table>";
                }

                if ($accion == 'agregarcalificacion' && $id_alumno_url) {
                    $g_id = $_GET['grupo_id'];
                    $resultado_nota = $objProfesor->Listar_notas_por_profesor($id_alumno_url, $id_profe);
                    
                    $n1 = $n2 = $n3 = "0.0";

                    if ($resultado_nota && $resultado_nota->num_rows > 0) {
                        $row = $resultado_nota->fetch_assoc();
                        $n1 = $row['calificacion_1'];
                        $n2 = $row['calificacion_2'];
                        $n3 = $row['calificacion_3'];
                    }

                    function campoBloqueado($valor) {
                        return (floatval($valor) > 0) ? "readonly style='background-color: #eee;'" : "";
                    }

                    echo "<h2>Ingresar / Modificar Calificación</h2>
                        <form action='panelProfesor.php' method='POST' class='form-calificar' id='formNotas'>
                            <input type='hidden' name='alumno_id' value='$id_alumno_url'>
                            <input type='hidden' name='grupo_id' value='$g_id'>
                            
                            <label>Parcial 1:</label>
                            <input type='number' name='n1' id='n1' step='0.1' min='0' max='10' value='$n1' ".campoBloqueado($n1)."><br>
                            
                            <label>Parcial 2:</label>
                            <input type='number' name='n2' id='n2' step='0.1' min='0' max='10' value='$n2' ".campoBloqueado($n2)."><br>
                            
                            <label>Parcial 3:</label>
                            <input type='number' name='n3' id='n3' step='0.1' min='0' max='10' value='$n3' ".campoBloqueado($n3)."><br>

                            <div style='margin-top: 20px;'>
                                <button type='submit' name='guardar' class='btn-regresar'>Guardar Notas</button>
                                <button type='button' onclick='habilitarEdicion()' class='btn-regresar' style='background:#f39c12; margin-left:10px;'>Modificar Todo</button>
                                <a href='?accion=listara&grupo_id=$g_id' style='margin-left:10px;'>Cancelar</a>
                            </div>
                        </form>";
                }

                if ($accion == 'listarccalificacion') {
                    if (isset($_GET['msj'])) echo "<p style='color:green; font-weight:bold;'>¡Cambios guardados!</p>";

                    if ($id_alumno_url) {
                        $resultado = $objProfesor->Listar_notas_por_profesor($id_alumno_url, $id_profe);
                        echo "<h2>Notas del Alumno</h2>";
                    } else {
                        $resultado = $objProfesor->Listar_todas_mis_notas($id_profe); 
                        echo "<h2>Reporte General</h2>";
                    }

                    if ($resultado && $resultado->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead><tr><th>Alumno</th><th>Asignatura</th><th>P1</th><th>P2</th><th>P3</th><th>Promedio</th></tr></thead>
                                <tbody>";
                        while ($datos = $resultado->fetch_assoc()) {
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
                        echo "<p>No hay registros.</p>";
                    }
                }

                // VISTA DE ASISTENCIA COLECTIVA (CORREGIDA PARA ID REALES)
                if ($accion == 'asistencia') {
                    $g_id = isset($_GET['grupo_id']) ? $_GET['grupo_id'] : '';
                    
                    // Obtenemos de forma limpia los alumnos inscritos exclusivamente a este grupo
                    $resultado_alumnos = $objProfesor->Listar_alumnos($g_id); 

                    echo "<h2>Pase de Lista del Grupo</h2>";
                    echo "<form action='panelProfesor.php' method='POST'>
                            <input type='hidden' name='grupo_id_general' value='{$g_id}'>
                            <table class='tabla-alumno'>
                                <thead><tr><th>Alumno</th><th>Asistencia de Hoy</th></tr></thead>
                                <tbody>";

                    if ($resultado_alumnos && $resultado_alumnos->num_rows > 0) {
                        while ($datos = $resultado_alumnos->fetch_assoc()) {
                            $id_correcto_alumno = $datos['alumno_id'];
                            $fecha_hoy = date('Y-m-d');
                            $asistencia_guardada = "";
                            $desabilitar = "";

                            // Comprobamos si el alumno tiene un registro guardado el día de hoy
                            $objProfesor->sentencia = "
                                SELECT a.estado FROM asistencia a 
                                INNER JOIN matriculado m ON a.matriculado_id = m.id 
                                WHERE m.alumno_id = '$id_correcto_alumno' AND m.grupo_id = '$g_id' AND a.fecha = '$fecha_hoy'
                            ";
                            $check_asistencia = $objProfesor->obtener_sentencia();

                            if ($check_asistencia && $check_asistencia->num_rows > 0) {
                                $reg_asistencia = $check_asistencia->fetch_assoc();
                                $asistencia_guardada = $reg_asistencia['estado']; 
                                $desabilitar = "disabled style='background-color: #eee; color:#7f8c8d; cursor: not-allowed;'"; 
                            }

                            echo "<tr>
                                    <td><strong>{$datos['nombre']}</strong></td>
                                    <td>";
                                    
                            if (!empty($asistencia_guardada)) {
                                // Dejamos un campo oculto de respaldo si ya se guardó para que mantenga el valor al enviar el POST
                                echo "<input type='hidden' name='asistencias[{$id_correcto_alumno}][estado]' value='{$asistencia_guardada}'>";
                                echo "<span style='color:#27ae60; font-weight:bold; margin-right:15px;'>✓ Registrado: " . strtoupper($asistencia_guardada) . "</span>";
                            }

                            echo "      <select name='asistencias[{$id_correcto_alumno}][estado]' style='padding: 8px; width: 100%; max-width: 250px; border-radius: 4px; border: 1px solid #ccc;' required $desabilitar>
                                            <option value='' disabled " . ($asistencia_guardada == "" ? "selected" : "") . ">--- Seleccionar ---</option>
                                            <option value='presente' " . ($asistencia_guardada == "presente" ? "selected" : "") . ">Presente</option>
                                            <option value='sin justificar' " . ($asistencia_guardada == "sin justificar" ? "selected" : "") . ">Falta</option>
                                            <option value='retardo' " . ($asistencia_guardada == "retardo" ? "selected" : "") . ">Retardo</option>
                                        </select>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay alumnos matriculados en este grupo.</td></tr>";
                    }
                    
                    echo "</tbody></table>";
                    echo "
                        <div style='margin-top: 25px; display: flex; align-items: center;'>
                            <button type='submit' name='guardar_asistencia' class='btn-regresar' style='border: none; cursor: pointer; background:#2980b9;'>Guardar Asistencia Completa</button>
                            <a href='?accion=listara&grupo_id=" . $g_id . "' class='btn-regresar' style='background: #e74c3c; margin-left: 10px; text-decoration: none; text-align: center;'>Cancelar</a>
                        </div>
                    </form>";
                }
             ?> 
        </div>
    </main>
</div>
</body>
</html>