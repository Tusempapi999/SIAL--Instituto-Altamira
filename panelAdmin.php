<?php
// ========================================================
// 1. INCLUSIONES Y REQUERIMIENTOS CRÍTICOS AL INICIO
// ========================================================
require_once('conexion.php');
require_once('clases/claseJustificante.php');
include('clases/ClaseAsignaturas.php');
include('clases/ClaseAdmin.php');

$conexion = new Conexion();
$asignatura = new Asignatura();
$admin = new admin();

// Variables globales para mensajes de retroalimentación
$msg_sistema = "";
$tipo_msg = ""; // 'exito' o 'error'

/* ========================================================
   2. PROCESOS LÓGICOS DE TRASFONDO (ANTES DE RENDERIZAR HTML)
   ======================================================== */

// --- PROCESOS DE ASIGNATURAS ---
if (isset($_POST['guardar'])) {
    if ($asignatura->altaAsignatura($_POST['nombre'], $_POST['descripcion'])) {
        $msg_sistema = "Asignatura guardada correctamente.";
        $tipo_msg = "exito";
    } else {
        $msg_sistema = "No se pudo guardar la asignatura.";
        $tipo_msg = "error";
    }
}

if (isset($_POST['eliminar'])) {
    if ($asignatura->bajaAsignatura($_POST['id'])) {
        $msg_sistema = "Asignatura eliminada correctamente.";
        $tipo_msg = "exito";
    } else {
        $msg_sistema = "No se pudo eliminar la asignatura.";
        $tipo_msg = "error";
    }
}

if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    if ($asignatura->modificarAsignatura($id, $nombre, $descripcion)) {
        $msg_sistema = "Asignatura actualizada correctamente.";
        $tipo_msg = "exito";
    } else {
        $msg_sistema = "No se pudo actualizar la asignatura.";
        $tipo_msg = "error";
    }
}

// --- PROCESOS DE USUARIOS ---
if (isset($_POST['altaUsuario'])) {
    if ($admin->agregar_usuario($_POST['nombre'], $_POST['email'], $_POST['pwd'], $_POST['rol'], $_POST['fecha_nacimiento'])) {
        $msg_sistema = "Usuario guardado correctamente.";
        $tipo_msg = "exito";
    } else {
        $msg_sistema = "No se pudo guardar el usuario.";
        $tipo_msg = "error";
    }
}

if (isset($_POST['eliminarUsuario'])) {
    if ($admin->eliminar_usuario($_POST['id'])) {
        $msg_sistema = "Usuario eliminado correctamente.";
        $tipo_msg = "exito";
    } else {
        $msg_sistema = "No se pudo eliminar el usuario.";
        $tipo_msg = "error";
    }
}

if (isset($_POST['modificarU'])) {
    $matricula = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $pwd = $_POST['pwd'];
    $rol = $_POST['rol'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    
    if ($admin->modificar_usuario($matricula, $nombre, $email, $pwd, $rol, $fecha_nacimiento)) {
        $msg_sistema = "Usuario actualizado correctamente.";
        $tipo_msg = "exito";
    } else {
        $msg_sistema = "No se pudo actualizar el usuario.";
        $tipo_msg = "error";
    }
}

// --- PROCESO CRÍTICO: MODIFICAR JUSTIFICANTE ---
if (isset($_POST['modificarJustificante'])) {
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $fecha_fin = $_POST['fecha_fin'];
    $motivo = $_POST['motivo'];
    $estado_act = $_POST['estado'];

    // Instancia limpia para ejecutar la edición utilizando tu clase con la conexión
    $justificanteEditar = new justificante($conexion, "", "", "", "");
    $resultadoJust = $justificanteEditar->ModificarJustificante($id, $fecha, $fecha_fin, $motivo, $estado_act);

    if ($resultadoJust === "rango_invalido") {
        $msg_sistema = "Error: La fecha final no puede ser menor que la inicial.";
        $tipo_msg = "error";
    } elseif ($resultadoJust) {
        // Redirección limpia para evitar reenvío de formulario y mantener el estado visual actual
        $estado_retorno = isset($_GET['estado']) ? $_GET['estado'] : 'pendiente';
        header("Location: ?opcion=justificantes&estado=" . $estado_retorno);
        exit();
    } else {
        $msg_sistema = "Error al intentar actualizar los datos del justificante.";
        $tipo_msg = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="visual_Admin.css">
</head>
<body>

<div class="contenedor">

    <aside class="sidebar">
        <div class="logo">
            Colegio<br><span>Nuevo Futuro</span>
        </div>
        <nav class="menu-lateral">
            <hr>
            <a href="?opcion=listar">Asignaturas</a>
            <hr>
            <a href="?opcion=usuarios">Usuarios</a>
            <hr>
            <a href="?opcion=matricular">Matricular alumno</a>
            <hr>
            <hr>
            <a href="?opcion=justificantes&estado=pendiente">Justificantes</a>
            <hr>
        </nav>
    </aside>

    <main class="contenido">

        <header class="barra-superior">
            <h2>Bienvenido al panel del Administrador</h2>

            <div class="perfil">
                <span class="nombre">Administrador</span>
                <div class="circulo"></div>
                <div class="menu">
                    <div class="notificaciones">Opciones</div>
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>
        </header>

        <div class="panel-vacio">

        <?php if (!empty($msg_sistema)): ?>
            <div class="notificaciones" style="padding: 10px; margin-bottom: 20px; border-radius: 5px; background-color: <?php echo $tipo_msg == 'exito' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $tipo_msg == 'exito' ? '#155724' : '#721c24'; ?>; border: 1px solid <?php echo $tipo_msg == 'exito' ? '#c3e6cb' : '#f5c6cb'; ?>;">
                <?php echo $msg_sistema; ?>
            </div>
        <?php endif; ?>
        
        <?php
        /* ========= ALTA DE ASIGNATURA ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "alta") {
        ?>
            <div class="formulario-estilo-imagen">
                <h2>Registrar una nueva asignatura</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Nombre de la asignatura</label>
                        <input type="text" name="nombre" placeholder="Ej. Matemáticas" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" placeholder="Ej. Álgebra y geometría básica" required>
                    </div>
                    <div class="acciones-form">
                        <input type="submit" name="guardar" class="btn-accion btn-regresar" value="Guardar">
                        <a href="?opcion=listar" class="btn-accion btn-regresar">Regresar</a>
                    </div>
                </form>
            </div>
        <?php } ?>

        <?php
        /* ========= MODIFICAR ASIGNATURA ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "modificar") {
            $id = $_GET['id'];
            $resultado = $asignatura->obtenerAsignaturaPorId($id);
            $fila = $resultado->fetch_assoc();
        ?>
            <div class="formulario-estilo-imagen">
                <h2>Ingresar / Modificar Calificación</h2>

                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
                    <div class="form-group">
                        <label>Nombre de la asignatura</label>
                        <input type="text" name="nombre" value="<?php echo $fila['nombre']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" value="<?php echo $fila['descripcion']; ?>" required>
                    </div>
                    <div class="acciones-form">
                        <input type="submit" name="actualizar" value="Actualizar asignatura" class="btn-accion btn-regresar ">
                        <a href="?opcion=listar" class="btn-accion btn-regresar">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php } ?>

        <?php
        /* ========= LISTAR ASIGNATURAS ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "listar") {
            $resultado = $asignatura->listarAsignatura();
            echo "<h2>Asignaturas</h2>";
            echo "<a href='?opcion=alta' class='btn-regresar'>Nueva asignatura</a><br><br>";
            echo "<table class='tabla-alumno'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>";

            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>
                        <td>{$fila['id']}</td>
                        <td>{$fila['nombre']}</td>
                        <td>{$fila['descripcion']}</td>
                        <td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$fila['id']}'>
                                <button type='submit' name='eliminar' class='btn-regresar' style='background:#e74c3c'>Eliminar</button>
                            </form>
                            <a href='?opcion=modificar&id={$fila['id']}' class='btn-regresar' style='background:#2ecc71'>Modificar</a>
                        </td>
                    </tr>";
            }
            echo "</table>";
        }
        ?>

        <?php
        /* ========= MATRICULAR ALUMNO ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "matricular") {
                echo '
                <div class="formulario-estilo-imagen">
                <h2>Matricular alumno en un grupo</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Matricula del alumno</label>
                        <input type="text" name="alumno_id" placeholder="Ej. 12345" required>
                    </div>

                    <div class="form-group">
                        <label>Grado</label>
                        <input type="text" name="grado" placeholder="Ej. 6" required>
                    </div>

                    <div class="form-group">
                        <label>Grupo</label>
                        <input type="text" name="letra_grupo" placeholder="Ej. A" required>
                    </div>

                    <div class="acciones-form">
                        <button type="submit" name="matricular" class="btn-accion btn-regresar"> Matricular</button>
                        <a href="?opcion=listar" class="btn-accion btn-regresar">Regresar</a>
                    </div>
                </form>
                </div>';
        }   

        if (isset($_POST['matricular'])) {
            $usuario_id = $_POST['alumno_id']; 
            $alumno_id = $admin->alumno_id_de_usuario($usuario_id)->fetch_assoc()['id'];
            $letra_grupo = $_POST['letra_grupo'];
            $grado = $_POST['grado'];

            $grupo = $admin->buscarGrupo($grado, $letra_grupo);
            $materias_encontradas = false;
            $todo_bien = true; 

            while ($fila = $grupo->fetch_assoc()) {
                $materias_encontradas = true; 
                $grupo_id = $fila['id'];
                
                if (!$admin->matricularAlumno($alumno_id, $grupo_id)) {
                    $todo_bien = false;
                }
            }

            if (!$materias_encontradas) {
                echo "<p class='notificaciones'>No se encontraron asignaturas creadas para el grupo " . $grado . "°" . $letra_grupo . "</p>";
            } else if ($todo_bien) {
                echo "<p class='notificaciones'>Alumno matriculado correctamente en todas las asignaturas del grupo.</p>";
            } else {
                echo "<p class='notificaciones'>Hubo un error y no se pudo matricular al alumno en algunas asignaturas.</p>";
            }
        }
        ?>

        <?php
        /* ========= LISTAR USUARIOS ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "usuarios") {
            $resultado = $admin->listarUsuarios();
            echo "<h2>Usuarios</h2>";
            echo "<a href='?opcion=altaUsuario' class='btn-regresar'>Nuevo usuario</a><br><br>";
            echo "<table class='tabla-alumno'>
                    <thead>
                        <tr>
                            <th>Matricula</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Fecha de nacimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>";

            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>
                        <td>{$fila['id']}</td>
                        <td>{$fila['nombre']}</td>
                        <td>{$fila['email']}</td>
                        <td>{$fila['rol']}</td>
                        <td>{$fila['fecha_nacimiento']}</td>
                        <td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$fila['id']}'>
                                <button type='submit' name='eliminarUsuario' class='btn-regresar' style='background:#e74c3c'>Eliminar</button>
                            </form>
                            <a href='?opcion=modificarUsuario&id={$fila['id']}' class='btn-regresar' style='background:#2ecc71'>Modificar</a>
                        </td>
                    </tr>";
            }
            echo "</table>";
        }

        /* ========= ALTA DE USUARIO ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "altaUsuario") {
        ?>
            <div class="formulario-estilo-imagen">
                <h2>Registrar un nuevo Usuario</h2>
                <form method="post">
                    <div class="form-group">
                        <label>Nombre de Usuario</label>
                        <input type="text" name="nombre" placeholder="Ej. Juan Pérez" required>
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" placeholder="Ej. juan@ejemplo.com" required>
                    </div>

                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="pwd" placeholder="Ej. ********" required>
                    </div>

                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" required>
                            <option value="alumno">Alumno</option>
                            <option value="profesor">Profesor</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" required>
                    </div>
                    <div class="acciones-form">
                        <input type="submit" name="altaUsuario" class="btn-accion btn-regresar" value="Guardar">
                        <a href="?opcion=listar" class="btn-accion btn-regresar">Regresar</a>
                    </div>
                </form>
            </div>
        <?php } ?>

        <?php
        /* ========= MODIFICAR USUARIO ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "modificarUsuario") {
            $id = $_GET['id'];
            $resultado = $admin->buscarUsuario($id);
            $fila = $resultado->fetch_assoc();
        ?>
            <div class="formulario-estilo-imagen">
                <h2>Ingresar / Modificar Usuario</h2>

                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

                    <div class="form-group">
                        <label>Nombre de usuario</label>
                        <input type="text" name="nombre" value="<?php echo $fila['nombre']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" value="<?php echo $fila['email']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="pwd" value="<?php echo $fila['pwd']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" required>
                            <option value="alumno" <?php echo $fila['rol'] == 'alumno' ? 'selected' : ''; ?>>Alumno</option>
                            <option value="profesor" <?php echo $fila['rol'] == 'profesor' ? 'selected' : ''; ?>>Profesor</option>
                            <option value="admin" <?php echo $fila['rol'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="<?php echo $fila['fecha_nacimiento']; ?>" required>
                    </div>

                    <div class="acciones-form">
                        <input type="submit" name="modificarU" value="Actualizar usuario" class="btn-accion btn-regresar ">
                        <a href="?opcion=listar" class="btn-accion btn-regresar">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php } ?>


        <?php
        /* ========= NUEVO APARTADO SECCIÓN: JUSTIFICANTES ========= */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "justificantes") {
            // Capturar el filtro del estado (por defecto: pendiente)
            $estado_filtro = isset($_GET['estado']) ? $_GET['estado'] : 'pendiente';
            
            echo "<h2>Gestión de Justificantes Institucionales</h2>";
            
            // ─── SUB-NAV INTERNO PARA CAMBIAR ENTRE ESTADOS ───
            echo "<div style='margin-bottom: 25px;'>
                    <a href='?opcion=justificantes&estado=pendiente' class='btn-regresar' style='background: #f39c12; margin-right: 10px; text-decoration: none;'>Pendientes</a>
                    <a href='?opcion=justificantes&estado=aceptado' class='btn-regresar' style='background: #2ecc71; margin-right: 10px; text-decoration: none;'>Aceptados</a>
                    <a href='?opcion=justificantes&estado=rechazado' class='btn-regresar' style='background: #e74c3c; text-decoration: none;'>Rechazados</a>
                  </div>";

            // ─── FORMULARIO DINÁMICO DE EDICIÓN (SOLO SI SE SELECCIONA UN ID) ───
            if (isset($_GET['id'])) {
                $id_just = $_GET['id'];
                
                // Ejecución directa de la query de búsqueda tal como la tenías estructurada
                $conexion->sentencia = "SELECT * FROM justificante WHERE id = '$id_just' ORDER BY id DESC";   
                $resultadoEditar = $conexion->obtener_sentencia();
                $datosEditar = $resultadoEditar->fetch_assoc();

                if ($datosEditar) {
                ?>
                    <div class="formulario-estilo-imagen" style="margin-bottom: 35px; border-left: 5px solid #3498db;">
                        <h2>Modificar Justificante #<?php echo $datosEditar['id']; ?> (Alumno ID: <?php echo $datosEditar['alumno_id']; ?>)</h2>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $datosEditar['id']; ?>">
                            
                            <div class="form-group">
                                <label>Fecha de Inicio</label>
                                <input type="text" name="fecha" value="<?php echo $datosEditar['fecha']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Fecha de Finalización</label>
                                <input type="text" name="fecha_fin" value="<?php echo $datosEditar['fecha_fin']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Motivo o Descripción</label>
                                <input type="text" name="motivo" value="<?php echo $datosEditar['motivo']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Cambiar Estado del Trámite</label>
                                <select name="estado">
                                    <option value="pendiente" <?php echo $datosEditar['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="aceptado" <?php echo $datosEditar['estado'] == 'aceptado' ? 'selected' : ''; ?>>Aceptado</option>
                                    <option value="rechazado" <?php echo $datosEditar['estado'] == 'rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                                </select>
                            </div>
                            
                            <div class="acciones-form">
                                <button type="submit" name="modificarJustificante" class="btn-accion btn-regresar">Guardar cambios</button>
                                <a href="?opcion=justificantes&estado=<?php echo $estado_filtro; ?>" class="btn-accion btn-regresar" style="background:#7f8c8d;">Cancelar</a>
                            </div>
                        </form>
                    </div>
                <?php
                }
            }

            // ─── TABLA DE LISTADO DINÁMICO ───
            echo "<h3>Listado de Solicitudes: " . ucfirst($estado_filtro) . "s</h3>";
            
            // Instanciar tu objeto para llamar el método EstadoJustificante($estado)
            $justificanteInstancia = new justificante($conexion, "", "", "", "");
            $pendientes = $justificanteInstancia->EstadoJustificante($estado_filtro);

            echo "<table class='tabla-alumno'>
                    <thead>
                        <tr>
                            <th>Matrícula Alumno</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Motivo</th>
                            <th>Estado Actual</th>
                            <th>Acción</th>
                        </tr>
                    </thead>";

            if ($pendientes && $pendientes->num_rows > 0) {
                while ($fila = $pendientes->fetch_assoc()) {
                    // Color dinámico según el estado para mejorar la legibilidad
                    $color_estado = '#f39c12'; // Pendiente
                    if ($fila['estado'] == 'aceptado') $color_estado = '#2ecc71';
                    if ($fila['estado'] == 'rechazado') $color_estado = '#e74c3c';

                    echo "<tr>
                            <td>{$fila['alumno_id']}</td>
                            <td>{$fila['fecha']}</td>
                            <td>{$fila['fecha_fin']}</td>
                            <td>{$fila['motivo']}</td>
                            <td><span style='font-weight:bold; color: {$color_estado};'>{$fila['estado']}</span></td>
                            <td>
                                <a href='?opcion=justificantes&estado={$estado_filtro}&id={$fila['id']}' class='btn-regresar' style='background:#3498db; text-decoration:none; padding: 5px 10px; font-size:12px;'>Modificar / Revisar</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center; color: #7f8c8d;'>No existen registros de justificantes bajo el estado '" . $estado_filtro . "'.</td></tr>";
            }
            echo "</table>";
        }
        ?>
        
        </div>
    </main>
</div>
</body>
</html>