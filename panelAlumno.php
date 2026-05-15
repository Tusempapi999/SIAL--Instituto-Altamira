<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="visual_maestro.css">
</head>
<body>

<div class="contenedor">

    <aside class="sidebar">
        <div class="logo">
            Colegio<br><span>Nuevo Futuro</span>
        </div>
        <nav class="menu-lateral">
            __________________________________
            <a href="?opcion=listar">Listar asignaturas</a>
            __________________________________
            <a href="?opcion=matricular">Matricular alumno</a>
            __________________________________
        </nav>
    </aside>

    <main class="contenido">

        <header class="barra-superior">
            <h2>Bienvenido al panel del Administrador</h2>

            <div class="perfil">
                <span class="nombre">Administrador</span>
                <div class="circulo"></div>
                <div class="menu">
                    <div class="notificaciones">Notificaciones</div>
                    <a href="inicio.php" class="salir">Finalizar sesión</a>
                </div>
            </div>
        </header>

        <div class="panel-vacio">
        <?php
        include('clases/ClaseAsignaturas.php');
        $asignatura = new Asignatura();

        /* ================== ALTA ================== */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "alta") {
        ?>
            <h2>Registrar una nueva asignatura</h2>

            <form method="post">
                Nombre:
                <input type="text" name="nombre" required><br><br>

                Descripción:
                <input type="text" name="descripcion" required><br><br>

                <button type="submit" name="guardar" class="btn-regresar">
                    Guardar
                </button>
            </form>

            <form method="get">
                <input type="hidden" name="opcion" value="listar">
                <button type="submit" class="btn-regresar">
                    Regresar
                </button>
            </form>
        <?php
        }

        /* ================== MODIFICAR ================== */
        if (isset($_GET['opcion']) && $_GET['opcion'] == "modificar") {

            $id = $_GET['id'];
            $resultado = $asignatura->obtenerAsignaturaPorId($id);
            $fila = $resultado->fetch_assoc();
        ?>
            <h2>Modificar asignatura</h2>

            <form method="post">
                <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

                Nombre:
                <input type="text" name="nombre"
                       value="<?php echo $fila['nombre']; ?>" required><br><br>

                Descripción:
                <input type="text" name="descripcion"
                       value="<?php echo $fila['descripcion']; ?>" required><br><br>

                <button type="submit" name="actualizar" class="btn-regresar">
                    Actualizar asignatura
                </button>
            </form>

            <form method="post">
                <button type="submit" name="Cancelar" class="btn-regresar">
                    Cancelar
                </button>
            </form>
        <?php
        }

        /* ================== LISTAR ================== */
        if(isset($_GET['opcion']) && $_GET['opcion'] == "listar"){

            $resultado = $asignatura->listarAsignatura();

            echo "<h2>Asignaturas Registradas</h2>";

            echo "
            <form method='get'>
                <input type='hidden' name='opcion' value='alta'>
                <button type='submit' class='btn-regresar'>
                    Nueva asignatura
                </button>
            </form><br>";

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
                                <button type='submit' name='eliminar'
                                        class='btn-regresar'
                                        style='background:#e74c3c'>
                                        Eliminar
                                </button>
                            </form>

                            <form method='get' style='display:inline;'>
                                <input type='hidden' name='opcion' value='modificar'>
                                <input type='hidden' name='id' value='{$fila['id']}'>
                                <button type='submit'
                                        class='btn-regresar'
                                        style='background:#2ecc71'>
                                        Modificar
                                </button>
                            </form>
                        </td>
                      </tr>";
            }
            echo "</table>";
        }

        /* ================== ACCIONES ================== */
        if(isset($_POST['guardar'])){
            echo $asignatura->altaAsignatura($_POST['nombre'], $_POST['descripcion'])
                ? "Asignatura guardada"
                : "No se pudo guardar";
        }

        if(isset($_POST['eliminar'])){
            echo $asignatura->bajaAsignatura($_POST['id'])
                ? "Asignatura eliminada"
                : "No se pudo eliminar";
        }

        if(isset($_POST['actualizar'])){
            echo $asignatura->modificarAsignatura(
                $_POST['id'],
                $_POST['nombre'],
                $_POST['descripcion']
            ) ? "Asignatura actualizada" : "No se pudo actualizar";
        }
        ?>
        </div>

    </main>
</div>

</body>
</html>