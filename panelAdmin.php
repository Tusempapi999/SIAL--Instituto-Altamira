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
        <?php
        include('clases/ClaseAsignaturas.php');
        $asignatura = new Asignatura();

        
        include('clases/ClaseAdmin.php');
        $admin = new admin();
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
        /* ========= MODIFICAR ASIGNATURA (ESTILO IMAGEN) ========= */
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
        if(isset($_GET['opcion']) && $_GET['opcion'] == "listar"){
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

            while($fila = $resultado->fetch_assoc()){
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

        /* Procesos Logicos */
        if(isset($_POST['guardar'])){
            echo $asignatura->altaAsignatura($_POST['nombre'], $_POST['descripcion']) ? "Asignatura guardada" : "No se pudo guardar";
        }
        if(isset($_POST['eliminar'])){
            echo $asignatura->bajaAsignatura($_POST['id']) ? "Asignatura eliminada" : "No se pudo eliminar";
        }
        if(isset($_POST['actualizar'])){

            // Captura ID
            $id = $_POST['id'];

            // Captura nuevo nombre
            $nombre = $_POST['nombre'];

            // Captura nueva descripción
            $descripcion = $_POST['descripcion'];

            // Ejecuta actualización en la base de datos
            $resultado = $asignatura->modificarAsignatura($id,$nombre,$descripcion);

            // Mensaje según resultado
            if($resultado){
                echo "Asignatura actualizada";
            }else{ // Si hubo un error al actualizar mostrar mensaje
                echo "No se pudo actualizar";
            }
        }

        if(isset($_GET['opcion']) && $_GET['opcion'] == "matricular") {
            // Título del formulario de matriculación
            
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

        if(isset($_POST['matricular'])) {
            
            
            // Captura ID del alumno
            $alumno_id = $_POST['alumno_id'];

            // Captura letra del grupo
            $letra_grupo = $_POST['letra_grupo'];

            // Captura grado del grupo
            $grado = $_POST['grado'];

            // Busca el ID del grupo
            $grupo = $admin->buscarGrupo($grado, $letra_grupo);
            $materias_encontradas = true;
            $todo_bien = true; // Variable para rastrear si todas las matriculaciones fueron exitosas

            // fetch_assoc() devuelve un array asociativo que corresponde a la fila obtenida 
            while($fila = $grupo->fetch_assoc()) {
                $materias_encontradas = true; // Confirmamos que al menos encontramos una materia
                $grupo_id = $fila['id'];
                
                // Intentamos matricular. Si una sola falla, $todo_bien se vuelve false
                if (!$admin->matricularAlumno($alumno_id, $grupo_id)) {
                    $todo_bien = false;
                }
            }

            // Mensajes de retroalimentación claros
            if (!$materias_encontradas) {
                echo "No se encontraron asignaturas creadas para el grupo " . $grado . "°" . $letra_grupo;
            } else if($todo_bien){
                echo "Alumno matriculado correctamente en todas las asignaturas del grupo.";
            } else {
                echo "Hubo un error y no se pudo matricular al alumno en algunas asignaturas.";
            }

        }

        if(isset($_GET['opcion']) && $_GET['opcion'] == "usuarios") {
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

            while($fila = $resultado->fetch_assoc()){
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
        <?php }

        if(isset($_POST['altaUsuario'])){
            echo $admin->agregar_usuario($_POST['nombre'], $_POST['email'], $_POST['pwd'], $_POST['rol'], $_POST['fecha_nacimiento']) ? "Usuario guardado" : "No se pudo guardar";
        }
        if(isset($_POST['eliminarUsuario'])){
            echo $admin->eliminar_usuario($_POST['id']) ? "Usuario eliminado" : "No se pudo eliminar";
        }
        
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
        <?php }

        if(isset($_POST['modificarU'])){

            // Captura ID
            $matricula = $_POST['id'];

            // Captura nuevo nombre
            $nombre = $_POST['nombre'];

            // Captura nueva descripción
            $email = $_POST['email'];

            // Captura nueva contraseña
            $pwd = $_POST['pwd'];

            // Captura nuevo rol
            $rol = $_POST['rol'];

            // Captura nueva fecha de nacimiento
            $fecha_nacimiento = $_POST['fecha_nacimiento'];

            // Ejecuta actualización en la base de datos
            $resultado = $admin->modificar_usuario($matricula, $nombre, $email, $pwd, $rol, $fecha_nacimiento);

            // Mensaje según resultado
            if($resultado){
                echo "Usuario actualizado";
            }else{ // Si hubo un error al actualizar mostrar mensaje
                echo "No se pudo actualizar";
            }
        }
        ?>
        </div>
    </main>
</div>
</body>
</html>