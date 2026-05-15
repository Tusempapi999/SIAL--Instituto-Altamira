<?php
    include('clases/claseUser.php'); // Se incluye el archivo de la clase User
    $user = new User(); // Se crea una instancia de la clase User
    if (isset($_POST['matricula']) && isset($_POST['pwd'])) { // Se verifica si se han enviado los datos de matricula y contraseña
        $matricula = $_POST['matricula']; // Se obtiene el valor de la matricula enviada por el formulario
        $pwd = $_POST['pwd']; // Se obtiene el valor de la contraseña enviada por el formulario
        $resultado = $user->iniciar_sesion($matricula, $pwd); // Se llama a la función iniciar_sesion con los datos obtenidos
        if ($resultado->num_rows > 0) { // Si el resultado tiene más de 0 filas, significa que se encontró un usuario con esa matricula y contraseña
            echo "Inicio de sesión exitoso" . "<br>"; // Se muestra un mensaje de éxito
            while ($datos = $resultado->fetch_assoc()) { // Se recorre el resultado para obtener los datos del usuario
            
                if ('admin' == $datos['rol']) { // Si la rol es 1 es admin
                    header("Location: panelAdmin.php");
                    exit;
                    //echo "<a href='panelAdmin.html'>Ir al panel de admin</a>";
                }
                if ('alumno' == $datos['rol']) { // Si la rol es 2 es alumno
                    header("Location: panelAlumno.php");
                    exit;
                    //echo "<a fhref='panelAlumno.html'>Ir al panel de alumno</a>";
                }
                if ('profesor' == $datos['rol']) { // Si la rol es 3 es profesor
                    header("Location: panelProfesor.php");
                    exit;
                    //echo "<a href='panelProfesor.html'>Ir al panel de profesor</a>";
                }
               

            }

            while ($datos = $resultado->fetch_assoc()) { // Se recorre el resultado para obtener los datos del usuario
                ?>
                <table>
                <thead>
                    <tr>
                        <th>Matricula</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha de nacimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $datos['id']; ?></td>
                        <td><?php echo $datos['nombre']; ?></td>
                        <td><?php echo $datos['email']; ?></td>
                        <td><?php echo $datos['rol']; ?></td>
                        <td><?php echo $datos['fecha_nacimiento']; ?></td>
                    </tr>
                </tbody> 
                </table>
                <?php
            }
        } else {
           header("Location: inicio.php?error=1");
            exit;
        }
    }