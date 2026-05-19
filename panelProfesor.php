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

        // CORREGIDO: Redirige de vuelta a la lista de alumnos/calificaciones del grupo actual
        header("Location: panelProfesor.php?accion=listara&grupo_id=" . $grupo_id . "&msj=ok");
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
    
    <script>
        function habilitarEdicion() {
            var inputs = ['n1', 'n2', 'n3'];
            inputs.forEach(function(id) {
                var input = document.getElementById(id);
                if(input) {
                    input.removeAttribute('readonly');
                    input.style.backgroundColor = '#ffffff'; // Devuelve el fondo a blanco
                }
            });
        }
    </script>
</head>
<body>
<div class="contenedor">
    <aside class="sidebar">
        <div class="logo">Colegio<br><span>Nuevo Futuro</span></div>
        <nav class="menu-lateral">
            
            <?php
            $grupos_result = $objProfesor->Listar_mis_grupos($id_profe);
            $grupos = [];

            if ($grupos_result && $grupos_result->num_rows > 0) {
                while ($g = $grupos_result->fetch_assoc()) {
                    $grupos[] = $g;
                }
            }
            ?>

            <h3 style="color: white; padding: 10px;">Salón</h3>
            <?php
                foreach ($grupos as $g) {
                    echo "<a href='?accion=faltasasistencias&grupo_id={$g['id']}'>
                            {$g['asignatura']} ({$g['grado']}°{$g['letra_grupo']})
                    </a>";
                }
            ?>
            
            <hr>
            <a href="?accion=listarp">Directorio Profesores</a>
            <hr>
            
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
                
                // Mensaje visual opcional cuando las notas se guardan con éxito
                if (isset($_GET['msj']) && $_GET['msj'] == 'ok') {
                    echo "<p style='color:green; font-weight:bold; margin-bottom: 15px;'>¡Calificaciones actualizadas correctamente!</p>";
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

                    echo "<h2>Calificaciones</h2><br>";
                    echo "<table class='tabla-alumno'>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Parcial 1</th>
                                    <th>Parcial 2</th>
                                    <th>Parcial 3</th>
                                    <th>Promedio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>";

                    if ($res && $res->num_rows > 0) {
                        while ($fila = $res->fetch_assoc()) {
                            $alumno_id = $fila['alumno_id'];
                            $notas = $objProfesor->Listar_notas_por_profesor($alumno_id, $id_profe);

                            $p1 = 0; $p2 = 0; $p3 = 0;

                            if ($notas && $notas->num_rows > 0) {
                                $n = $notas->fetch_assoc();
                                $p1 = floatval($n['calificacion_1']);
                                $p2 = floatval($n['calificacion_2']);
                                $p3 = floatval($n['calificacion_3']);
                            }

                            $promedio = number_format(($p1 + $p2 + $p3) / 3, 2);

                            echo "<tr>
                                    <td><strong>{$fila['nombre']}</strong></td>
                                    <td>{$p1}</td>
                                    <td>{$p2}</td>
                                    <td>{$p3}</td>
                                    <td><strong>{$promedio}</strong></td>
                                    <td>
                                        <a href='?accion=agregarcalificacion&alumno_id={$alumno_id}&grupo_id=$grupo_id'
                                        class='btn-regresar'
                                        style='background:#2ecc71'>
                                        Poner Nota/Modificar
                                        </a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No hay alumnos en este grupo</td></tr>";
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
                                <a href='?accion=listara&grupo_id=$g_id' style='margin-left:10px;' class='btn-regresar'>Cancelar</a>
                            </div>
                        </form>";
                }

                if ($accion == 'faltasasistencias' && $grupo_id) {
                    echo "<h2>Resumen de Asistencia del Grupo</h2>";
                    echo "<a href='?accion=asistencia&grupo_id=$grupo_id' class='btn-regresar' style='background:#3498db; margin-bottom:15px; margin-right:15px; display:inline-block;'>Tomar Lista de Hoy</a>";
                    echo "<a href='?accion=listara&grupo_id=$grupo_id' class='btn-regresar' style='background:#3498db; margin-bottom:15px; display:inline-block;'>Calificaciones</a><br><br>";
                    
                    $resumen = $objProfesor->Resumen_asistencia_por_grupo($grupo_id);

                    if ($resumen && $resumen->num_rows > 0) {
                        echo "<table class='tabla-alumno'>
                                <thead>
                                    <tr>
                                        <th>Alumno</th>
                                        <th>Asistencias</th>
                                        <th>Faltas sin justificar</th>
                                        <th>Faltas justificadas</th>
                                        <th>Retardos</th>
                                    </tr>
                                </thead>
                                <tbody>";

                        while ($r = $resumen->fetch_assoc()) {
                            echo "<tr>
                                    <td><strong>{$r['alumno']}</strong></td>
                                    <td>{$r['asistencias']}</td>
                                    <td style='color:#c0392b;'>{$r['faltas_sin_justificar']}</td>
                                    <td style='color:#2980b9;'>{$r['faltas_justificadas']}</td>
                                    <td style='color:#f39c12;'>{$r['retardos']}</td>
                                </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No hay registros de asistencia.</p>";
                    }
                }

                if ($accion == 'asistencia') {
                    $g_id = isset($_GET['grupo_id']) ? $_GET['grupo_id'] : '';
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
                    echo "<div style='margin-top: 25px; display: flex; align-items: center;'>
                            <a href='#' class='btn-regresar' style='background:#2980b9; border:none; cursor:pointer; text-decoration:none;' onclick=\"this.closest('form').submit(); return false;\">
                                Guardar Asistencia Completa
                            </a>
                            <a href='?accion=faltasasistencias&grupo_id=$g_id' class='btn-regresar' style='background:#e74c3c; margin-left:10px; text-decoration:none;'>
                                Cancelar
                            </a>
                            <a href='?accion=modificar&grupo_id=$g_id' class='btn-regresar' style='background:#ff9008; margin-left:10px; text-decoration:none;'>
                                Modificar Asistencia 
                            </a>
                          </div>
                    </form>";
                }

                if (isset($_POST['modificar_asistencia'])) {
                    $grupo_id = $_POST['grupo_id_general'];
                    $fecha_actual = date('Y-m-d');

                    foreach ($_POST['asistencias'] as $alumno_id => $datos) {
                        $estado = $datos['estado'];
                        $objProfesor->Actualizar_asistencia($alumno_id, $grupo_id, $fecha_actual, $estado);
                    }

                    header("Location: panelProfesor.php?accion=faltasasistencias&grupo_id=$grupo_id&msj=mod_ok");
                    exit();
                }

                if ($accion == 'modificar' && $grupo_id) {

            $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

            echo "<h2>Modificar asistencia del grupo</h2>";

            // FORMULARIO PARA ELEGIR FECHA
            echo "<form method='GET' action='panelProfesor.php'>
                    <input type='hidden' name='accion' value='modificar'>
                    <input type='hidden' name='grupo_id' value='{$grupo_id}'>

                    <label>Selecciona fecha:</label>
                    <input type='date' name='fecha' value='{$fecha}' required>

                    <button type='submit' class='btn-regresar' style='background:#3498db; margin-left:10px;'>
                        Buscar
                    </button>
                </form><br>";

            echo "<form action='panelProfesor.php' method='POST'>
                    <input type='hidden' name='grupo_id_general' value='{$grupo_id}'>
                    <input type='hidden' name='modificar_asistencia' value='1'>
                    <input type='hidden' name='fecha' value='{$fecha}'>

                    <table class='tabla-alumno'>
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Asistencia ({$fecha})</th>
                            </tr>
                        </thead>
                        <tbody>";

            $resultado_alumnos = $objProfesor->Listar_alumnos($grupo_id);

            if ($resultado_alumnos && $resultado_alumnos->num_rows > 0) {

                while ($al = $resultado_alumnos->fetch_assoc()) {

                    $alumno_id = $al['alumno_id'];

                    // CONSULTA DE ASISTENCIA POR FECHA SELECCIONADA
                    $objProfesor->sentencia = "
                        SELECT a.estado 
                        FROM asistencia a
                        INNER JOIN matriculado m ON a.matriculado_id = m.id
                        WHERE m.alumno_id = '$alumno_id'
                        AND m.grupo_id = '$grupo_id'
                        AND a.fecha = '$fecha'
                    ";

                    $res = $objProfesor->obtener_sentencia();

                    $estado_actual = ($res && $res->num_rows > 0)
                        ? $res->fetch_assoc()['estado']
                        : '';

                    echo "<tr>
                            <td><strong>{$al['nombre']}</strong></td>
                            <td>
                                <select name='asistencias[$alumno_id][estado]'
                                        style='padding:8px; width:100%; max-width:250px;
                                        border-radius:4px; border:1px solid #ccc;' required>

                                    <option value='presente' " . ($estado_actual=='presente'?'selected':'') . ">Presente</option>
                                    <option value='sin justificar' " . ($estado_actual=='sin justificar'?'selected':'') . ">Falta</option>
                                    <option value='retardo' " . ($estado_actual=='retardo'?'selected':'') . ">Retardo</option>

                                </select>
                            </td>
                        </tr>";
                }

            } else {
                echo "<tr><td colspan='2'>No hay alumnos registrados.</td></tr>";
            }

            echo "</tbody></table>

                <div style='margin-top:25px; display:flex; align-items:center; justify-content:center;'>

                    <button type='submit' class='btn-regresar' style='background:#2980b9'>
                        Guardar Cambios
                    </button>

                    <a href='?accion=faltasasistencias&grupo_id=$grupo_id'
                    class='btn-regresar'
                    style='background:#e74c3c; margin-left:10px; text-decoration:none;'>
                    Cancelar
                    </a>

                </div>
            </form>";
        }
             ?> 
        </div>
    </main>
</div>
</body>
</html>