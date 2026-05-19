<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <!-- la linea anterior es para que se adapte a cualquier pantalla -->
    <!-- y no se vea mal en dispositivos moviles -->
    <title>Prueba de Consultas</title>
</head>
<body>
    <h1>Justificante</h1>

    <a href="adminJustificante.php?estado=pendiente">Pendientes</a>
    <br>
    <a href="adminJustificante.php?estado=aceptado">Aceptados</a>
    <br>
    <a href="adminJustificante.php?estado=rechazado">Rechazados</a>

</body>
</html>
<?php
require_once('conexion.php');                            // Se incluye el archivo de la conexion a la BD
require_once('clases/claseJustificante.php');            // Se incluye el archivo de la clase justificante

$conexion = new Conexion();

// If que solo funcionara cuando el usuario haga clic sobre el boton "Guardar cambios"
if(isset($_POST['modificarJustificante'])) {
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $fecha_fin = $_POST['fecha_fin'];
    $motivo = $_POST['motivo'];
    $estado = $_POST['estado'];
    // Lo anterios muestra los datos actuales del justificante //

    $justificanteEditar = new justificante($conexion, "", "", "", "");
    $resultado = $justificanteEditar->ModificarJustificante($id, $fecha, $fecha_fin, $motivo, $estado);

    if ($resultado == "rango_invalido") {
        ?><p>La fecha final no puede ser menor que la inicial</p> <?php
    } elseif ($resultado) {
        header("Location: adminJustificante.php"); // Reenvia al usuario a una pantalla atras para que elimine el form de edicion del justificante
        exit();                                     // Detiene la ejecucion PHP
    } else {
        echo "Error al actualizar";         // Mendaje de error al actualizar los datos
    }
}
////////// ////////// //////////

// If para verificar que la id del justificante a modificar sea valia y existente
if (isset($_GET['id'])) {           // Identificar la id del justificante que el usuario seleccione
 
    $id = $_GET['id'];              // Guarda la id sleccionada en una variable llamada id

    // Sentencia para recuperar el justificante
    $conexion->sentencia = 
        "SELECT * FROM justificante 
        WHERE id = '$id'
        ORDER BY id DESC";   
    
    $resultadoEditar = $conexion->obtener_sentencia();
    $datosEditar = $resultadoEditar->fetch_assoc();
}
////////// ////////// //////////

$estado = "Pendientes";

if(isset($_GET['estado'])) {
    $estado = $_GET['estado'];
}


////////// TEXTO DINAMICO PARA EL ESTADO DEL JUSTIFICANTE
if($estado == "pendiente") {
    echo "<h2>Justificantes Pendientes</h2>";
}
if($estado == "aceptado") {
    echo "<h2>Justificantes Aceptados</h2>";
}
if($estado == "rechazado") {
    echo "<h2>Justificantes Rechazados</h2>";
}
////////// //////////


// Genera un nuevo objeto "justificante"
$justificantePendiente = new justificante($conexion, "", "", "", "");
$pendientes = $justificantePendiente->EstadoJustificante($estado);

// 
while ($fila = $pendientes->fetch_assoc()) {
    echo "<hr>";

    echo "Alumno ID: " . $fila['alumno_id'] . "<br>";
    echo "Fecha: " . $fila['fecha'] . "<br>";
    echo "Fecha Fin: " . $fila['fecha_fin'] . "<br>";
    echo "Motivo: " . $fila['motivo'] . "<br>";
    echo "Estado: " . $fila['estado'] . "<br>";

    echo "<a href='adminJustificante.php?id=" . $fila['id'] . "'> Modificar </a><br>";
}

if (isset($datosEditar)) { ?>
    <form method="POST">

        <input type="hidden" name="id" value="<?php echo $datosEditar['id']; ?>">
        <!-- Modificar fecha del justificante -->
        <input type="text" name="fecha" value="<?php echo $datosEditar['fecha']; ?>">
        <!-- Modificar la fecha final del justificante -->
        <input type="text" name="fecha_fin" value="<?php echo $datosEditar['fecha_fin']; ?>">
        <!-- Modificar la el motivo del justificante -->
        <input type="text" name="motivo" value="<?php echo $datosEditar['motivo']; ?>">

        <!-- ComboBox con los estados del justificante -->
        <select name="estado">
            <option value="pendiente">Pendiente</option>
            <option value="aceptado">Aceptado</option>
            <option value="rechazado">Rechazado</option>
        </select>
        <br>

        <!-- Buttom para guardar los cambios realizados -->
        <button type="submit" name="modificarJustificante">Guardar cambios</button>

    </form>

<?php } ?>
