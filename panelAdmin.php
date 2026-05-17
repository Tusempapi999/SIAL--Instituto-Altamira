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
            <a href="?opcion=listar">Listar asignaturas</a>
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
            echo "<h2>Asignaturas Registradas</h2>";
            echo "<a href='?opcion=alta' class='btn-regresar'>Nueva asignatura</a><br><br>";
            echo "<table class='tabla-alumno'>
                    <thead>
                        <tr>
                            <th>ID</th>
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
                        <label>Matricula</label>
                        <input type="text" name="alumno_id" placeholder="Matricula de alumno" required>
                    </div>

                    <div class="form-group">
                        <label>Id del grupo</label>
                        <input type="text" name="grupo_id" placeholder="ID del grupo" required>
                    </div>

                    <div class="acciones-form">
                        <a  href="?opcion=matricular" class="btn-accion btn-regresar" > Matricular</a>
                        <a href="?opcion=listar" class="btn-accion btn-regresar">Regresar</a>
                    </div>
                </form>
                </div>';
        }   

        if(isset($_POST['matricular'])) {
            
            include('classes/ClaseAdmin.php');

            $admin = new admin();
            
            // Captura ID del alumno
            $alumno_id = $_POST['alumno_id'];

            // Captura ID del grupo
            $grupo_id = $_POST['grupo_id'];

            // Ejecuta la matriculación del alumno en el grupo
            $resultado = $admin->matricula_alumno($alumno_id, $grupo_id);

            // Mensaje según resultado
            if($resultado){
                echo "Alumno matriculado correctamente";
            }else{
                echo "No se pudo matricular al alumno";
            }
            
            echo $asignatura->modificarAsignatura($_POST['id'], $_POST['nombre'], $_POST['descripcion']) ? "Asignatura actualizada" : "No se pudo actualizar";
        }
        ?>
        </div>
    </main>
</div>
</body>
</html>