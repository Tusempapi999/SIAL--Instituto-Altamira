<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="visual_maestro.css">

</head>
<body>

<div class="contenedor">

    <!-- Barra lateral -->
    <aside class="sidebar">
        <div class="logo">
            Colegio<br><span>Nuevo Futuro</span>
            <br>
        </div>
        <nav class="menu-lateral">
                __________________________________
                <!-- Enlace para dar de alta una asignatura (envía opcion=alta por GET) -->
                <a href="?opcion=alta">Alta asignatura</a>

                
                __________________________________
                <!-- Enlace para eliminar una asignatura -->
                <a href="?opcion=baja">Baja asignatura</a>
        
                __________________________________
                <!-- Enlace para modificar una asignatura -->

                <a href="?opcion=modificar">Modificar asignatura</a>

                __________________________________
                <!-- Enlace para listar todas las asignaturas -->
                <a href="?opcion=listar">Listar asignaturas</a>
                __________________________________

                <a href="?opcion=matricular">Matricular alumno</a>
                __________________________________
            
        </nav>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenido">

        <!-- BARRA SUPERIOR -->
        <header class="barra-superior">

            <span><h2>Bienvenido al panel del Administrador</h2></span>

            <!-- PERFIL -->
            <div class="perfil">
                <span class="nombre">Administrador</span>
                <div class="circulo"></div>

                <!-- MENÚ DESPLEGABLE -->
                <div class="menu">
                    <div class="notificaciones">
                        Notificaciones
                    </div>        
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
    
            </div>

        </header>

        <!-- PANEL VACÍO -->
        <div class="panel-vacio">
            <?php
            // Incluye el archivo donde está la clase con operaciones CRUD
            include('classes/ClaseAsignaturas.php');

            // Crea un objeto para poder usar los métodos de la clase Asignatura
            $asignatura = new Asignatura();

            // Si se selecciona la opción "alta"
        if (isset($_GET['opcion']) && $_GET['opcion'] == "alta") {
        ?>

            <!-- Título del formulario de alta -->
            <h2>Registrar una nueva asignatura</h2>

            <!-- Formulario para registrar asignatura -->
            <form method="post">

                Nombre:
                <!-- Campo para ingresar el nombre -->
                <input type="text" name="nombre" required><br><br>

                Descripción:
                <!-- Campo para ingresar la descripción -->
                <input type="text" name="descripcion" required><br><br>

                <!-- Botón para enviar datos al servidor -->
                <input type="submit" name="guardar" value="Guardar">
            </form>

        <?php
        }

        // Si se selecciona la opción "baja"
        if (isset($_GET['opcion']) && $_GET['opcion'] == "baja") {
        ?>

            <!-- Título del formulario de eliminación -->
            <h2>Eliminar asignatura</h2>

            <!-- Formulario para eliminar por ID -->
            <form method="post">

                ID de la asignatura:
                <!-- Campo para capturar ID -->
                <input type="text" name="id" required><br><br>

                <!-- Botón de eliminación -->
                <input type="submit" name="eliminar" value="Eliminar">
            </form>

        <?php
        }

        // Si se selecciona la opción "modificar"
        if (isset($_GET['opcion']) && $_GET['opcion'] == "modificar") {
        ?>

            <!-- Título del formulario de modificación -->
            <h2>Modificar asignatura</h2>

            <!-- Formulario para actualizar datos -->
            <form method="post">

                ID:
                <!-- ID del registro a modificar -->
                <input type="text" name="id" required><br><br>

                Nuevo nombre:
                <!-- Nuevo nombre -->
                <input type="text" name="nombre"><br><br>

                Nueva descripción:
                <!-- Nueva descripción -->
                <input type="text" name="descripcion"><br><br>

                <!-- Botón para actualizar -->
                <input type="submit" name="actualizar" value="Actualizar asignatura">
            </form>

        <?php
        }


        // Si se selecciona la opción "listar"
        if(isset($_GET['opcion']) && $_GET['opcion'] == "listar"){

            // Ejecuta consulta para obtener todas las asignaturas
            $resultado = $asignatura->listarAsignatura();

            // Título de la sección
            echo "<h2>Asignaturas Registradas</h2>";
            echo "<br>";
            // Inicio de la tabla HTML
            echo "<table border='1'>";

            // Encabezados de la tabla
            echo "<tr>
                    <th>ID</th> <!-- Encabezado para ID -->
                    <th>Nombre</th> <!-- Encabezado para nombre -->
                    <th>Descripción</th> <!-- Encabezado para descripción -->
                </tr>";

            // Recorre cada fila obtenida de la base de datos
            while($fila = $resultado->fetch_assoc()){

                echo "<tr>"; // Inicio de una nueva fila en la tabla

                // Muestra ID
                echo "<td>".$fila['id']."</td>"; 

                // Muestra nombre
                echo "<td>".$fila['nombre']."</td>"; 

                // Muestra descripción
                echo "<td>".$fila['descripcion']."</td>";

                echo "</tr>";
            }

            // Cierre de tabla
            echo "</table>";
        }

        // Si se presiona el botón "guardar"
        if(isset($_POST['guardar'])){

            // Captura nombre del formulario
            $nombre = $_POST['nombre'];

            // Captura descripción del formulario
            $descripcion = $_POST['descripcion'];

            // Llama método para insertar en BD
            $resultado = $asignatura->altaAsignatura($nombre,$descripcion);

            // Verifica resultado de la operación
            if($resultado){
                echo "Asignatura guardada";
            }else{ // Si hubo un error al guardar mostrar mensaje
                echo "No se pudo guardar";
            }
        }

        // Si se presiona el botón "eliminar"
        if(isset($_POST['eliminar'])){

            // Captura ID a eliminar
            $id = $_POST['id'];

            // Ejecuta método de eliminación
            $resultado = $asignatura->bajaAsignatura($id);

            // Mensaje de resultado
            if($resultado){
                echo "Asignatura eliminada";
            }else{ // Si hubo un error al eliminar mostrar mensaje
                echo "No se pudo eliminar";
            }
        }

        // Si se presiona el botón "actualizar"
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
            echo "<h2>Matricular alumno en un grupo</h2>";

            // Formulario para matricular alumno
            echo "<form method='post'>";

            // Campo para ingresar ID del alumno
            echo "Matricula del alumno: <input type='text' name='alumno_id' required><br><br>";

            // Campo para ingresar ID del grupo
            echo "ID del grupo: <input type='text' name='grupo_id' required><br><br>";

            // Botón para enviar datos al servidor
            echo "<input type='submit' name='matricular' value='Matricular'>";
            echo "</form>";
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
        }
        ?>

        </div>

    </main>

</div>

</body>
</html>
        