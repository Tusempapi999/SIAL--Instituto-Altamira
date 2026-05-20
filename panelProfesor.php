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

        header("Location: panelProfesor.php?accion=faltasasistencias&grupo_id=" . $grupo_id . "&msj=asistencia_ok");
        exit();
    }

    // ACCIÓN POST 3: MODIFICAR ASISTENCIA EXISTENTE
    if (isset($_POST['modificar_asistencia'])) {
        $grupo_id = $_POST['grupo_id_general'];
        $fecha_buscada = $_POST['fecha']; 

        if (isset($_POST['asistencias']) && is_array($_POST['asistencias'])) {
            foreach ($_POST['asistencias'] as $alumno_id => $datos) {
                $estado = $datos['estado'];
                $objProfesor->Actualizar_asistencia($alumno_id, $grupo_id, $fecha_buscada, $estado);
            }
        }

        header("Location: panelProfesor.php?accion=faltasasistencias&grupo_id=$grupo_id&msj=mod_ok");
        exit();
    }

    // ACCIÓN POST 4: GUARDAR ASISTENCIA DEL CLUB
    if (isset($_POST['guardar_asistencia_club'])) {
        $club_id = $_POST['club_id_general'];
        $fecha_actual = $_POST['fecha_asistencia']; 

        if (isset($_POST['asistencias_club']) && is_array($_POST['asistencias_club'])) {
            foreach ($_POST['asistencias_club'] as $alumno_id => $datos_asistencia) {
                $estado = isset($datos_asistencia['estado']) ? $datos_asistencia['estado'] : '';
                if (!empty($estado)) {
                    $objProfesor->Registrar_asistencia_club($alumno_id, $club_id, $fecha_actual, $estado);
                }
            }
        }

        header("Location: panelProfesor.php?accion=asistencia_club&club_id=" . $club_id . "&msj=asistencia_ok");
        exit();
    }

    // ACCIÓN POST 5: MODIFICAR ASISTENCIA EXISTENTE DEL CLUB
    if (isset($_POST['modificar_asistencia_club'])) {
        $club_id = $_POST['club_id_general'];
        $fecha_buscada = $_POST['fecha']; 

        if (isset($_POST['asistencias_club']) && is_array($_POST['asistencias_club'])) {
            foreach ($_POST['asistencias_club'] as $alumno_id => $datos) {
                $estado = $datos['estado'];
                // Guardamos usando directamente el método que interactúa correctamente con la BD
                $objProfesor->Registrar_asistencia_club($alumno_id, $club_id, $fecha_buscada, $estado);
            }
        }

        header("Location: panelProfesor.php?accion=asistencia_club&club_id=$club_id&msj=mod_club_ok");
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
                    input.style.backgroundColor = '#ffffff';
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
            <a href="?accion=asistencia_club">Asistencia Club</a>
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
                $club_id = isset($_GET['club_id']) ? $_GET['club_id'] : null;

                // Mensajes de feedback
                if (isset($_GET['msj']) && $_GET['msj'] == 'asistencia_ok') {
                    echo "<p style='color:green; font-weight:bold; margin-bottom: 15px;'>¡Asistencia procesada correctamente en la Base de Datos!</p>";
                }
                if (isset($_GET['msj']) && $_GET['msj'] == 'mod_ok') {
                    echo "<p style='color:green; font-weight:bold; margin-bottom: 15px;'>¡Asistencia modificada correctamente!</p>";
                }
                if (isset($_GET['msj']) && $_GET['msj'] == 'mod_club_ok') {
                    echo "<p style='color:green; font-weight:bold; margin-bottom: 15px;'>¡Asistencia del Club modificada correctamente!</p>";
                }
                if (isset($_GET['msj']) && $_GET['msj'] == 'ok') {
                    echo "<p style='color:green; font-weight:bold; margin-bottom: 15px;'>¡Calificaciones actualizadas correctamente!</p>";
                }

                // VISTA: LISTADO DE PROFESORES
                if ($accion == 'listarp') {
                    $resultado = $objProfesor->Listar_todos_los_profesores();
                    echo "<h2>Tus profesores</h2><table class='tabla-alumno'><thead><tr><th>Nombre</th><th>Especialidad</th><th>Email</th></tr></thead><tbody>";
                    while ($datos = $resultado->fetch_assoc()) {
                        echo "<tr><td>{$datos['nombre']}</td><td>{$datos['especialidad']}</td><td>{$datos['email']}</td></tr>";
                    }
                    echo "</tbody></table>";
                } 

                // VISTA: LISTADO DE CALIFICACIONES
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
                            $notes_res = $objProfesor->Listar_notas_por_profesor($alumno_id, $id_profe);

                            $p1 = 0; $p2 = 0; $p3 = 0;

                            if ($notes_res && $notes_res->num_rows > 0) {
                                $notafila = $notes_res->fetch_assoc();
                                $p1 = floatval($notafila['calificacion_1']);
                                $p2 = floatval($notafila['calificacion_2']);
                                $p3 = floatval($notafila['calificacion_3']);
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

                // VISTA: AGREGAR/MODIFICAR CALIFICACIÓN
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

                // VISTA: RESUMEN GENERAL DE ASISTENCIAS
                if ($accion == 'faltasasistencias' && $grupo_id) {
                    echo "<h2>Resumen de Asistencia del Grupo</h2>";
                    echo "<a href='?accion=asistencia&grupo_id=$grupo_id' class='btn-regresar' style='background:#3498db; margin-bottom:15px; margin-right:15px; display:inline-block;'>Tomar Lista de Hoy</a>";
                    echo "<a href='?accion=modificar&grupo_id=$grupo_id' class='btn-regresar' style='background:#ff9008; margin-bottom:15px; margin-right:15px; display:inline-block;'>Modificar Asistencias Pasadas</a>";
                    echo "<a href='?accion=listara&grupo_id=$grupo_id' class='btn-regresar' style='background:#2ecc71; margin-bottom:15px; display:inline-block;'>Calificaciones</a><br><br>";
                    
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

                // VISTA: TOMAR ASISTENCIA DE HOY
                if ($accion == 'asistencia') {
                    $g_id = isset($_GET['grupo_id']) ? $_GET['grupo_id'] : '';
                    $resultado_alumnos = $objProfesor->Listar_alumnos($g_id); 

                    echo "<h2>Pase de Lista del Grupo (Hoy)</h2>";
                    echo "<form action='panelProfesor.php' method='POST'>
                            <input type='hidden' name='grupo_id_general' value='{$g_id}'>
                            <input type='hidden' name='guardar_asistencia' value='1'>
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
                            <button type='submit' class='btn-regresar' style='background:#2980b9; border:none; cursor:pointer;'>
                                Guardar Asistencia Completa
                            </button>
                            <a href='?accion=faltasasistencias&grupo_id=$g_id' class='btn-regresar' style='background:#e74c3c; margin-left:10px; text-decoration:none;'>
                                Cancelar
                            </a>
                          </div>
                        </form>";
                }

                // VISTA: MODIFICAR ASISTENCIA (POR FECHA SELECCIONADA)
                if ($accion == 'modificar' && $grupo_id) {
                    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

                    echo "<h2>Modificar Asistencia del Grupo</h2>";

                    echo "<form method='GET' action='panelProfesor.php'>
                            <input type='hidden' name='accion' value='modificar'>
                            <input type='hidden' name='grupo_id' value='{$grupo_id}'>

                            <label>Selecciona fecha a modificar:</label>
                            <input type='date' name='fecha' value='{$fecha}' required style='padding:5px; border-radius:4px; border:1px solid #ccc;'>

                            <button type='submit' class='btn-regresar' style='background:#3498db; margin-left:10px; border:none; cursor:pointer;'>
                                Buscar Registro
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

                            $objProfesor->sentencia = "
                                SELECT a.estado 
                                FROM asistencia a
                                INNER JOIN matriculado m ON a.matriculado_id = m.id
                                WHERE m.alumno_id = '$alumno_id'
                                AND m.grupo_id = '$grupo_id'
                                AND a.fecha = '$fecha'
                            ";

                            $res = $objProfesor->obtener_sentencia();
                            $estado_actual = ($res && $res->num_rows > 0) ? $res->fetch_assoc()['estado'] : '';

                            echo "<tr>
                                    <td><strong>{$al['nombre']}</strong></td>
                                    <td>
                                        <select name='asistencias[$alumno_id][estado]'
                                                style='padding:8px; width:100%; max-width:250px; border-radius:4px; border:1px solid #ccc;' required>
                                            <option value='presente' " . ($estado_actual == 'presente' ? 'selected' : '') . ">Presente</option>
                                            <option value='sin justificar' " . ($estado_actual == 'sin justificar' ? 'selected' : '') . ">Falta</option>
                                            <option value='justificada' " . ($estado_actual == 'justificada' ? 'selected' : '') . ">Falta Justificada</option>
                                            <option value='retardo' " . ($estado_actual == 'retardo' ? 'selected' : '') . ">Retardo</option>
                                        </select>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay alumnos registrados.</td></tr>";
                    }

                    echo "</tbody></table>";
                    echo "<div style='margin-top:25px; display:flex; align-items:center;'>
                            <button type='submit' class='btn-regresar' style='background:#2980b9; border:none; cursor:pointer;'>
                                Guardar Cambios
                            </button>
                            <a href='?accion=faltasasistencias&grupo_id=$grupo_id' class='btn-regresar' style='background:#e74c3c; margin-left:10px; text-decoration:none;'>
                                Cancelar
                            </a>
                        </div>
                    </form>";
                }

                // VISTA PRINCIPAL DEL CLUB
                if ($accion == 'asistencia_club') {
                    if (empty($club_id)) {
                        $club_id = $objProfesor->Obtener_club_por_profesor($id_profe);
                    }

                    if ($club_id) {
                        $sub_accion = isset($_GET['sub']) ? $_GET['sub'] : '';

                        // FASE A: TOMAR LISTA DE HOY
                        if ($sub_accion == 'tomar_lista') {
                            $resultado_alumnos_c = $objProfesor->Listar_alumnos_club($club_id);
                            $fecha_hoy = date('Y-m-d');
                            
                            echo "<h2>Pase de Lista del Club (Hoy: {$fecha_hoy})</h2><br>";
                            echo "<form action='panelProfesor.php' method='POST'>
                                    <input type='hidden' name='club_id_general' value='{$club_id}'>
                                    <input type='hidden' name='fecha_asistencia' value='{$fecha_hoy}'>
                                    <table class='tabla-alumno'>
                                        <thead>
                                            <tr>
                                                <th>Alumno</th>
                                                <th>Asistencia de Hoy</th>
                                            </tr>
                                        </thead>
                                        <tbody>";
                            
                            if ($resultado_alumnos_c && $resultado_alumnos_c->num_rows > 0) {
                                $objClubAux = new profesor();
                                while ($dc = $resultado_alumnos_c->fetch_assoc()) {
                                    $id_correcto_alumno = $dc['alumno_id'];
                                    $asistencia_club_guardada = "";
                                    $propiedades_select = ""; 

                                    $objClubAux->sentencia = "SELECT estado FROM asistencia_club WHERE alumno_id = '$id_correcto_alumno' AND fecha = '$fecha_hoy'";
                                    $check_c = $objClubAux->obtener_sentencia();
                                    
                                    if ($check_c && $check_c->num_rows > 0) {
                                        $asistencia_club_guardada = $check_c->fetch_assoc()['estado'];
                                        $propiedades_select = "style='background-color: #eee; color:#7f8c8d; pointer-events: none; padding: 8px; width: 100%; max-width: 250px; border-radius: 4px; border: 1px solid #ccc;'";
                                    } else {
                                        $propiedades_select = "style='padding: 8px; width: 100%; max-width: 250px; border-radius: 4px; border: 1px solid #ccc;'";
                                    }

                                    echo "<tr>
                                            <td><strong>{$dc['nombre']}</strong></td>
                                            <td>";
                                    if (!empty($asistencia_club_guardada)) {
                                        echo "<span style='color:#8e44ad; font-weight:bold; margin-right:15px;'>✓ Registrado: " . strtoupper($asistencia_club_guardada) . "</span>";
                                    }
                                    echo "      <select name='asistencias_club[{$id_correcto_alumno}][estado]' {$propiedades_select} required>
                                                    <option value='' disabled " . ($asistencia_club_guardada == "" ? "selected" : "") . ">--- Seleccionar ---</option>
                                                    <option value='presente' " . ($asistencia_club_guardada == "presente" ? "selected" : "") . ">Presente</option>
                                                    <option value='sin justificar' " . ($asistencia_club_guardada == "sin justificar" ? "selected" : "") . ">Falta</option>
                                                    <option value='retardo' " . ($asistencia_club_guardada == "retardo" ? "selected" : "") . ">Retardo</option>
                                                </select>
                                            </td>
                                         </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>No hay alumnos inscritos en este club.</td></tr>";
                            }
                            echo "</tbody></table>
                                  <div style='margin-top:25px;'>
                                    <button type='submit' name='guardar_asistencia_club' class='btn-regresar' style='background:#8e44ad; border:none; cursor:pointer;'>Guardar Asistencia de Club</button>
                                    <a href='panelProfesor.php?accion=asistencia_club' class='btn-regresar' style='background:#e74c3c; margin-left:10px; text-decoration:none;'>Cancelar</a>
                                  </div>
                                  </form>";
                        } else {
                            // FASE B: MOSTRAR TABLA DE RESUMEN ACUMULADO DEL CLUB
                            echo "<h2>Resumen de Asistencia del Club</h2>";
                            echo "<a href='?accion=asistencia_club&sub=tomar_lista' class='btn-regresar' 
                                    style='background:#8e44ad; margin-bottom:15px; margin-right:15px; display:inline-block; text-decoration:none;'>
                                    Tomar Lista de Hoy</a>";

                            echo "<a href='?accion=modificar_club&club_id=$club_id' class='btn-regresar' 
                                    style='background:#8e44ad; margin-bottom:15px; display:inline-block; text-decoration:none;'>
                                    Modificar asistencia</a><br><br>";
                            $resumen_club = $objProfesor->Resumen_asistencia_club($club_id);

                            if ($resumen_club && $resumen_club->num_rows > 0) {
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

                                while ($r = $resumen_club->fetch_assoc()) {
                                    $asistencias = $r['asistencias'] ?? 0;
                                    $faltas_sj = $r['faltas_sin_justificar'] ?? 0;
                                    $faltas_j = $r['faltas_justificadas'] ?? 0;
                                    $retardos = $r['retardos'] ?? 0;

                                    echo "<tr>
                                            <td><strong>{$r['alumno']}</strong></td>
                                            <td>{$asistencias}</td>
                                            <td style='color:#c0392b;'>{$faltas_sj}</td>
                                            <td style='color:#2980b9;'>{$faltas_j}</td>
                                            <td style='color:#f39c12;'>{$retardos}</td>
                                        </tr>";
                                }
                                echo "</tbody></table>";
                            } else {
                                echo "<p>No hay registros de alumnos o asistencias en este club.</p>";
                            }
                        }
                    } else {
                        echo "<h2>Pase de Lista del Club</h2>";
                        echo "<p style='color:red; font-weight:bold;'>Error: No tienes ningún club asignado en el sistema.</p>";
                    }
                }

                // VISTA: MODIFICAR ASISTENCIA DEL CLUB (POR FECHA SELECCIONADA)
                if ($accion == 'modificar_club' && $club_id) {
                    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

                    echo "<h2>Modificar Asistencia del Club</h2>";

                    // Formulario para buscar la fecha a modificar
                    echo "<form method='GET' action='panelProfesor.php'>
                            <input type='hidden' name='accion' value='modificar_club'>
                            <input type='hidden' name='club_id' value='{$club_id}'>

                            <label>Selecciona fecha a modificar:</label>
                            <input type='date' name='fecha' value='{$fecha}' required style='padding:5px; border-radius:4px; border:1px solid #ccc;'>

                            <button type='submit' class='btn-regresar' style='background:#3498db; margin-left:10px; border:none; cursor:pointer;'>
                                Buscar Registro
                            </button>
                        </form><br>";

                    // Formulario para guardar los cambios de los alumnos
                    echo "<form action='panelProfesor.php' method='POST'>
                            <input type='hidden' name='club_id_general' value='{$club_id}'>
                            <input type='hidden' name='modificar_asistencia_club' value='1'>
                            <input type='hidden' name='fecha' value='{$fecha}'>

                            <table class='tabla-alumno'>
                                <thead>
                                    <tr>
                                        <th>Alumno</th>
                                        <th>Asistencia ({$fecha})</th>
                                    </tr>
                                </thead>
                                <tbody>";

                    $resultado_alumnos_c = $objProfesor->Listar_alumnos_club($club_id);

                    if ($resultado_alumnos_c && $resultado_alumnos_c->num_rows > 0) {
                        $objClubAux = new profesor();
                        while ($dc = $resultado_alumnos_c->fetch_assoc()) {
                            $alumno_id = $dc['alumno_id'];

                            // CORRECCIÓN CLAVE: Consulta limpia de acuerdo a la estructura real de tu DB en claseProfesores.php
                            $objClubAux->sentencia = "
                                SELECT estado 
                                FROM asistencia_club 
                                WHERE alumno_id = '$alumno_id' 
                                AND fecha = '$fecha'
                            ";

                            $res = $objClubAux->obtener_sentencia();
                            $estado_actual = ($res && $res->num_rows > 0) ? $res->fetch_assoc()['estado'] : '';

                            echo "<tr>
                                    <td><strong>{$dc['nombre']}</strong></td>
                                    <td>
                                        <select name='asistencias_club[$alumno_id][estado]'
                                                style='padding:8px; width:100%; max-width:250px; border-radius:4px; border:1px solid #ccc;' required>
                                            <option value='presente' " . ($estado_actual == 'presente' ? 'selected' : '') . ">Presente</option>
                                            <option value='sin justificar' " . ($estado_actual == 'sin justificar' ? 'selected' : '') . ">Falta</option>
                                            <option value='retardo' " . ($estado_actual == 'retardo' ? 'selected' : '') . ">Retardo</option>
                                        </select>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay alumnos inscritos en este club.</td></tr>";
                    }

                    echo "</tbody></table>";
                    echo "<div style='margin-top:25px; display:flex; align-items:center;'>
                            <button type='submit' class='btn-regresar' style='background:#8e44ad; border:none; cursor:pointer;'>
                                Guardar Cambios
                            </button>
                            <a href='?accion=asistencia_club&club_id=$club_id' class='btn-regresar' style='background:#e74c3c; margin-left:10px; text-decoration:none;'>
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