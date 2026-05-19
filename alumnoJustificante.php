<h1>Crear un justificante</h1> <!-- Form para generar un nuevo justificante -->
<fieldset>
        <form action="alumnoJustificante.php" method="POST">
            <label for="nombre">ID del alumno:</label>
            <input type="text" id="nombre" name="alumno_id" placeholder="2" required><br><br>

            <label for="fecha">Fecha:</label> 
            <input type="datetime" id="fecha" name="fecha" placeholder="2020-10-25" required><br><br>

            <label for="fecha_fin">fin del justificante</label>
            <input type="datetime" id="fecha_fin" name="fecha_fin" placeholder="2020-10-30" required><br><br>

            <label for="motivo">Motivo:</label>
            <input type="text" id="motivo" name="motivo" placeholder="Enfermedad: gripa severa" required><br><br>

            <input type="submit" value="Generar justificante">
        </form>
</fieldset>

<?php
require_once('conexion.php');                            // Se incluye el archivo de la conexion a la BD
require_once('clases/claseJustificante.php');            // Se incluye el archivo de la clase justificante

///////////////////
$alumno_id = 1;//// Variable TEMPORAL para evitar un pantallazo de error. Se removera cuando se conecte al login
///////////////////

$conexion = new Conexion();

// If para varificar los datos inviados por el usuario y verificar que sean validos y/o correctos
if (isset($_POST['alumno_id']) && isset($_POST['fecha']) && isset($_POST['fecha_fin']) && isset($_POST['motivo'])) { // Se verifica si se han enviado los datos para el justificante
    $alumno_id = $_POST['alumno_id'];               // en la variable alumno guardamos el contenido
    $fecha = $_POST['fecha'];                       // en la variable fecha guardamos el contenido de ingresado por el usuario en el formulario
    $fecha_fin = $_POST['fecha_fin'];               // en la variable fecha_fin guardamos el contenido de ingresado por el usuario sobre el fin del justificante
    $motivo = $_POST['motivo'];                     // en la variable motivo guardamos el motivo ingresado por el usuario, el cual señala el motivo de la falta

    $justificante = new justificante($conexion, $alumno_id, $fecha, $fecha_fin, $motivo);       //creamos un nuevo objeto justificante para almacenar los datos

    $resultado = $justificante->crear(); // Se llama a la función crear para insertar los valores a la base de datos

    if ($resultado == "no_existe") {                //// VALIDACION ////
        echo "El alumno no existe";                 // Se muestra un mensaje de error, el alumno no existe en la base de datos
    } elseif ($resultado == "fecha_invalida") { // Validacion de fecha valida (formato real)
        echo "Formato de fecha inválido";           // Formato de fecha lo valida
    } elseif ($resultado == "rango_invalido") { // Validacion de fecha valida (formato real)
        echo "La fecha final no puede ser menor que la inicial"; // La fecha ingresada es menor a la fecha inicial del justificante
    } elseif ($resultado == "ok") {
        echo "Justificante generado correctamente"; // Se muestra un mensaje de éxito
        echo "<br>En espera de una respuesta de parte de nuestros administradores...";
    } else {
        echo "Error al insertar datos";             // Se muestra un mansaje de error
    }
}

$justificanteAlumno = new justificante($conexion, "", "", "", "");


$misJustificantes = $justificanteAlumno->JustificantesAlumno($alumno_id); // Se encarga de reconocer el historial de justificantes del alumno

echo "<h2>Mis Justificantes</h2>";

while($fila = $misJustificantes->fetch_assoc()) { // Lista el historial de justificantes del alumno
    echo "<hr>";

    echo "Fecha: " . $fila['fecha'] . "<br>";
    echo "Fecha Fin: " . $fila['fecha_fin'] . "<br>";
    echo "Motivo: " . $fila['motivo'] . "<br>";
    echo "Estado: " . $fila['estado'] . "<br>";
}
?>