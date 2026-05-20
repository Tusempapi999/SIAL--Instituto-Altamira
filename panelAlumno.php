<?php
// ========================================================
// 1. INCLUSIONES Y REQUERIMIENTOS CRÍTICOS AL INICIO
// ========================================================
require_once('conexion.php');
require_once('clases/claseJustificante.php');
include('clases/claseAlumno.php');

$conexion = new Conexion();
$objAlumno = new alumno();

// Variables globales para mensajes de retroalimentación en justificantes
$msg_justificante = "";
$tipo_msg = ""; 


/* ========================================================
   2. PROCESO LÓGICO: ALTA DE JUSTIFICANTE (POST)
   ======================================================== */
if (isset($_POST['alumno_id']) && isset($_POST['fecha']) && isset($_POST['fecha_fin']) && isset($_POST['motivo'])) {
    $alumno_post_id = $_POST['alumno_id']; 
    $fecha = $_POST['fecha']; 
    $fecha_fin = $_POST['fecha_fin']; 
    $motivo = $_POST['motivo']; 

    // Creamos el objeto con los datos del formulario
    $justificante = new justificante($conexion, $alumno_post_id, $fecha, $fecha_fin, $motivo);
    $resultado = $justificante->crear(); 

    if ($resultado == "no_existe") {
        $msg_justificante = "El alumno con ID $alumno_post_id no existe en la base de datos.";
        $tipo_msg = "error";
    } elseif ($resultado == "fecha_invalida") {
        $msg_justificante = "Formato de fecha inválido. Recuerda usar el calendario.";
        $tipo_msg = "error";
    } elseif ($resultado == "rango_invalido") {
        $msg_justificante = "La fecha final no puede ser menor que la fecha de inicio.";
        $tipo_msg = "error";
    } elseif ($resultado == "ok") {
        $msg_justificante = "Justificante generado correctamente. En espera de respuesta por los administradores.";
        $tipo_msg = "exito";
    } else {
        $msg_justificante = "Error crítico al intentar insertar los datos.";
        $tipo_msg = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Alumno</title>
    <link rel="stylesheet" href="visual_maestro.css">
    <style>
        /* ESTILOS PARA EL MODAL DE CONFIRMACIÓN */
        .modal-club {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.5); 
            align-items: center;
            justify-content: center;
        }
        .modal-club-contenido {
            background-color: #fefefe;
            margin: auto;
            padding: 25px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-align: left;
        }
        .modal-club-header {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .modal-club-detalles {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
        }
        .modal-club-detalles strong {
            color: #111;
        }
        .modal-club-acciones {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-modal-cancelar {
            background-color: #e74c3c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-modal-confirmar {
            background-color: #2ecc71;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="contenedor">

    <aside class="sidebar">
        <div class="logo">
            SIAL<br><span>Altamira</span>
        </div>

        <nav class="menu-lateral">
            <a href="?accion=verhorario">Ver horario</a>
            <hr>
            <a href="?accion=listara">Listar compañeros</a>
            <hr>
            <a href="?accion=listarp">Listar profesores</a>
            <hr>
            <a href="?accion=faltasasistencias">Faltas y asistencias</a>
            <hr>
            <a href="?accion=vercalificaciones">Ver calificaciones</a>
            <hr>
            <a href="?accion=clubes">Clubes sabatinos</a>
            <hr>
            <a href="?accion=justificantes" >Solicitar Justificante</a>
        </nav>
    </aside>

    <main class="contenido">
        <header class="barra-superior">
            <h2>Panel del Alumno</h2>
            <div class="perfil">
                <span class="nombre">Alumno</span>
                <div class="circulo"></div>

                <div class="menu">
                    <div class="notificaciones">Opciones</div>        
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>
        </header>
        
        <div class="panel-vacio">

        <?php if (!empty($msg_justificante)): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 4px; background-color: <?php echo $tipo_msg == 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipo_msg == 'exito' ? '#155724' : '#721c24'; ?>; border: 1px solid <?php echo $tipo_msg == 'exito' ? '#c3e6cb' : '#f5c6cb'; ?>;">
                <?php echo $msg_justificante; ?>
            </div>
        <?php endif; ?>

        <?php
        $accion = $_GET['accion'] ?? '';
        $grupo_id = $_GET['grupo_id'] ?? null;
        $club_id = $_GET['club_id'] ?? null;

        /* =====================================================
         NUEVA SECCIÓN: CREAR Y VER JUSTIFICANTES
        ===================================================== */
        if ($accion == 'justificantes') {
            echo "<h2>Mis Justificantes Solicitados</h2>";
            echo "<a href='?accion=crearjustif' class='btn-regresar'>Crear Justificante</a><br><br>";

            $justificanteAlumno = new justificante($conexion, "", "", "", "");
            $misJustificantes = $justificanteAlumno->JustificantesAlumno($alumno_id);

            echo "<table class='tabla-alumno'>
                    <thead>
                        <tr>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Motivo / Causa</th>
                            <th>Estado Actual</th>
                        </tr>
                    </thead>
                    <tbody>";

            if ($misJustificantes && $misJustificantes->num_rows > 0) {
                while ($fila = $misJustificantes->fetch_assoc()) {
                    $badge_color = '#f39c12';
                    if ($fila['estado'] == 'accepted' || $fila['estado'] == 'aceptado') $badge_color = '#2ecc71';
                    if ($fila['estado'] == 'rechazado') $badge_color = '#e74c3c';

                    echo "<tr>
                            <td>{$fila['fecha']}</td>
                            <td>{$fila['fecha_fin']}</td>
                            <td>{$fila['motivo']}</td>
                            <td>
                                <span style='color:white;background:$badge_color;padding:3px 8px;border-radius:3px;font-size:12px;font-weight:bold;'>
                                    {$fila['estado']}
                                </span>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr>
                        <td colspan='4' style='text-align:center;color:#7f8c8d;'>
                            No has registrado ninguna solicitud de justificante.
                        </td>
                      </tr>";
            }
            echo "</tbody></table>";
        }

        /* =====================================================
        CREAR JUSTIFICANTE (ESTILOS Y FORMULARIO)
        ===================================================== */
        echo '
        <style>
        .panel-formulario {
            max-width: 500px;
            margin: 20px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        .panel-formulario h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 22px;
            text-align: center;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .form-group input[type="text"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 9px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input[readonly] {
            background-color: #eee !important;
            cursor: not-allowed;
        }
        .acciones-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
        }
        .btn-principal {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-principal:hover { background-color: #27ae60; }
        .btn-regresar {
            background-color: #0022ff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            transition: background 0.2s;
            text-align: center;
        }
        </style>
        ';

        if ($accion == 'crearjustif') {
            echo '
            <div class="panel-formulario">
                <h2>Crear un nuevo Justificante Médico / Particular</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>ID del Alumno (Confirmación):</label>
                        <input type="text" name="alumno_id" value="' . $alumno_id . '" readonly>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Inicio de Inasistencia:</label>
                        <input type="date" name="fecha" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha Finalización de Inasistencia:</label>
                        <input type="date" name="fecha_fin" required>
                    </div>
                    <div class="form-group">
                        <label>Motivo o Justificación Detallada:</label>
                        <input type="text" name="motivo" required>
                    </div>
                    <div class="acciones-form">
                        <input type="submit" class="btn-principal" value="Generar Trámite">
                        <a href="?accion=justificantes" class="btn-regresar">Volver</a>
                    </div>
                </form>
            </div>';
        }

        /* =====================================================
        LISTAR COMPAÑEROS
        ===================================================== */
        elseif ($accion == 'listara') {
            $grupo_id = 1; 
            $res = $objAlumno->Listar_alumnos($grupo_id);

            echo "<h2>Compañeros de clase</h2>";

            if ($res && $res->num_rows > 0) {
                echo "<table class='tabla-alumno'>
                        <thead>
                            <tr>
                                <th>ID Alumno</th>
                                <th>Nombre</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($fila = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['alumno_id']}</td>
                            <td>{$fila['nombre']}</td>
                            <td>{$fila['email']}</td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No hay compañeros.</p>";
            }
        }

        /* =====================================================
        LISTAR PROFESORES
        ===================================================== */
        elseif ($accion == 'listarp') {
            $res = $objAlumno->Listar_profesores($alumno_id);

            echo "<h2>Tus profesores</h2>";

            if ($res && $res->num_rows > 0) {
                echo "<table class='tabla-alumno'>
                        <thead>
                            <tr>
                                <th>Profesor</th>
                                <th>Especialidad</th>
                                <th>Asignatura</th>
                            </tr>
                        </thead>
                        <tbody>";
                while ($fila = $res->fetch_assoc()) {
                    echo "<tr>
                            <td>{$fila['nombre']}</td>
                            <td>{$fila['especialidad']}</td>
                            <td>{$fila['asignatura']}</td>
                        </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No se encontraron profesores.</p>";
            }
        }

        /* =====================================================
        VER CALIFICACIONES
        ===================================================== */
        elseif ($accion == 'vercalificaciones') {
            $res = $objAlumno->setCalificacion($alumno_id); 

            echo "<h2>Calificaciones</h2><br>";
            echo "<table class='tabla-alumno'>
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Parcial 1</th>
                            <th>Parcial 2</th>
                            <th>Parcial 3</th>
                            <th>Promedio</th>
                        </tr>
                    </thead>
                    <tbody>";

            if ($res && $res->num_rows > 0) {
                while ($fila = $res->fetch_assoc()) {
                    $p1 = isset($fila['calificacion_1']) ? floatval($fila['calificacion_1']) : 0;
                    $p2 = isset($fila['calificacion_2']) ? floatval($fila['calificacion_2']) : 0;
                    $p3 = isset($fila['calificacion_3']) ? floatval($fila['calificacion_3']) : 0;

                    $promedio = number_format(($p1 + $p2 + $p3) / 3, 2);

                    echo "<tr>
                            <td><strong>{$fila['asignatura']}</strong></td>
                            <td>{$p1}</td>
                            <td>{$p2}</td>
                            <td>{$p3}</td>
                            <td><strong>{$promedio}</strong></td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No tienes materias o calificaciones registradas actualmente.</td></tr>";
            }
            echo "</tbody></table>";
        }

        /* =====================================================
        FALTAS DE ASISTENCIA
        ===================================================== */
        elseif ($accion == 'faltasasistencias') {
            if (!$grupo_id) {
                $res = $objAlumno->setCalificacion($alumno_id); 

                echo "<h2>Selecciona una asignatura para ver tus faltas</h2><br>";

                if ($res && $res->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Asignatura</th><th>Acción</th></tr></thead>
                            <tbody>";
                    while ($fila = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['asignatura']}</td>
                                <td>
                                    <a href='?accion=faltasasistencias&grupo_id={$fila['grupo_id']}' class='btn-regresar' style='background:#2ecc71'>
                                        Ver faltas
                                    </a>
                                </td>
                              </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No estás matriculado en ninguna asignatura.</p>";
                }
            } else {
                $resFaltas = $objAlumno->verFaltasAsistencia($grupo_id, $alumno_id);
                
                echo "<h2>Detalle de Faltas</h2>";

                if ($resFaltas && $resFaltas->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Fecha</th><th>Estado</th></tr></thead>
                            <tbody>";
                    while ($falta = $resFaltas->fetch_assoc()) {
                        echo "<tr>
                                <td>{$falta['fecha']}</td>
                                <td>{$falta['estado']}</td>
                              </tr>";
                    }
                    echo "</tbody></table> <br>";
                } else {
                    echo "<p>No tienes faltas en esta asignatura.</p><br>";
                }
                echo "<a href='?accion=faltasasistencias' class='btn-regresar'>Volver al listado</a><br><br>";
            }
        }

        /* =====================================================
        VER HORARIO
        ===================================================== */
        elseif ($accion == 'verhorario') {
            if (!$grupo_id) {
                $resMaterias = $objAlumno->setCalificacion($alumno_id); 
                    
                echo "<h2>Mi horario</h2>";
                if ($resMaterias && $resMaterias->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Asignatura</th><th>Acción</th></tr></thead>";
                    while ($materia = $resMaterias->fetch_assoc()) {
                        echo "<tr>
                                <td>{$materia['asignatura']}</td>
                                <td><a class='btn' href='?accion=verhorario&grupo_id={$materia['grupo_id']}'>Consultar Horario</a></td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No estás matriculado en ninguna asignatura.</p>";
                }
            } else {
                $resHorario = $objAlumno->verHorarioAsignatura($grupo_id);
                
                echo "<h2>Horario de asignatura</h2>";

                if ($resHorario && $resHorario->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead>
                                <tr><th>Día</th><th>Hora Inicio</th><th>Hora Fin</th></tr>
                            </thead>";
                    while ($h = $resHorario->fetch_assoc()) {
                        echo "<tr>
                                <td>{$h['dia_semana']}</td>
                                <td>{$h['hora_inicio']}</td>
                                <td>{$h['hora_fin']}</td>
                            </tr>";
                    }
                    echo "</table><br>";
                } else {
                    echo "<p>No hay un horario registrado</p><br>";
                }
                echo "<a href='?accion=verhorario' class='btn-regresar'>Volver al listado</a><br><br>";
            }
        }

        /* =====================================================
        CLUBES SABATINOS
        ===================================================== */
        elseif ($accion == 'clubes') {
            echo "<div class='seccion-clubes'>";

            if (isset($_GET['inscribir_id'])) {
                $id_del_club = intval($_GET['inscribir_id']);
                $misClubs = $objAlumno->listarMisClubs($alumno_id);

                if ($misClubs && $misClubs->num_rows > 0) {
                    echo "<h2>No puedes inscribirte</h2>";
                    echo "<p>Ya estás inscrito en un club sabatino actualmente.</p><br>";
                } else {
                    $objAlumno->inscribirClub($alumno_id, $id_del_club);
                    echo "<h2>Te has inscrito con éxito</h2>";
                }
                echo "<a href='?accion=clubes' class='btn'>Volver a mi panel de clubes</a>";
            }

            elseif (isset($_GET['club_id'])) {
                $id_del_club = intval($_GET['club_id']);
                $resAsis = $objAlumno->verAsistenciaClub($id_del_club, $alumno_id);

                echo "<h2>Control de Asistencia del Club</h2>";
                
                if ($resAsis && $resAsis->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Fecha</th><th>Estado</th></tr></thead><tbody>";
                    while ($asist = $resAsis->fetch_assoc()) {
                        echo "<tr><td>{$asist['fecha']}</td><td>{$asist['estado']}</td></tr>";
                    }
                    echo "</tbody></table><br>";
                } else {
                    echo "<p>Aún no se han registrado asistencias para ti en este club.</p><br>";
                }
                echo "<a href='?accion=clubes' class='btn'>Volver</a>";
            }

            else {
                echo "<h2>Clubes Sabatinos</h2>";

                $misClubes = $objAlumno->listarMisClubs($alumno_id);
                echo "<h3>Mis Clubes</h3>";
                if ($misClubes && $misClubes->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Club</th><th>Acción</th></tr></thead><tbody>";
                    while ($fila = $misClubes->fetch_assoc()) {
                        echo "<tr>
                                <td>{$fila['club']}</td>
                                <td><a class='btn' href='?accion=clubes&club_id={$fila['club_id']}'>Ver Asistencias</a></td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No tienes inscripciones activas.</p>";
                }

                $disponibles = $objAlumno->listarClubesDisponibles($alumno_id);
                echo "<br><br> <h3>Clubes Disponibles para Inscripción</h3>";
                if ($disponibles && $disponibles->num_rows > 0) {
                    echo "<table class='tabla-alumno'>
                            <thead><tr><th>Nombre del Club</th><th>Cupo Máx.</th><th>Acción</th></tr></thead><tbody>";
                    while ($filaD = $disponibles->fetch_assoc()) {
                        $nombre_club = htmlspecialchars($filaD['club'], ENT_QUOTES, 'UTF-8');
                        $descripcion_club = isset($filaD['descripcion']) ? htmlspecialchars($filaD['descripcion'], ENT_QUOTES, 'UTF-8') : 'Sin descripción disponible.';
                        $cupo = intval($filaD['cupo_maximo']);
                        $id_c = intval($filaD['club_id']);

                        echo "<tr>
                                <td>{$nombre_club}</td>
                                <td>{$cupo} vacantes</td>
                                <td>
                                    <button type='button' class='btn' style='cursor:pointer; border:none;' 
                                            onclick=\"abrirConfirmacion('{$id_c}', '{$nombre_club}', '{$descripcion_club}', '{$cupo}')\">
                                        Inscribirme
                                    </button>
                                </td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No hay clubes disponibles para inscripción en este momento o ya estás dentro de uno.</p>";
                }
            }
            echo "</div>";
        }
        ?>
        
        </div>
    </main>
</div>

<div id="modalConfirmarClub" class="modal-club">
    <div class="modal-club-contenido">
        <div class="modal-club-header" id="modalTitulo">Información del Club</div>
        <div class="modal-club-detalles">
            <p><strong>Descripción:</strong> <span id="modalDescripcion"></span></p>
            <p><strong>Cupo Disponible:</strong> <span id="modalCupo"></span> vacantes</p>
            <p style="margin-top: 15px; color: #c0392b; font-weight: bold;">¿Estás seguro de que deseas inscribirte a este club?</p>
        </div>
        <div class="modal-club-acciones">
            <button class="btn-modal-cancelar" onclick="cerrarConfirmacion()">Cancelar</button>
            <a id="btnConfirmarInscripcion" href="#" class="btn-modal-confirmar">Confirmar e Inscribirme</a>
        </div>
    </div>
</div>

<script>
    function abrirConfirmacion(id, nombre, descripcion, cupo) {
        document.getElementById('modalTitulo').innerText = "Información del Club: " + nombre;
        document.getElementById('modalDescripcion').innerText = descripcion;
        document.getElementById('modalCupo').innerText = cupo;
        document.getElementById('btnConfirmarInscripcion').href = "?accion=clubes&inscribir_id=" + id;
        document.getElementById('modalConfirmarClub').style.display = 'flex';
    }

    function cerrarConfirmacion() {
        document.getElementById('modalConfirmarClub').style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('modalConfirmarClub');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>