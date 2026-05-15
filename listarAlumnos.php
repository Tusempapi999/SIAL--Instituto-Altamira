<?php
    include('clases/claseAlumno.php'); // Se incluye el archivo de la clase Alumno
    $alumno = new Alumno(); // Se crea una instancia de la clase Alumno
    $alumno_id = $_GET['alumno_id']; // Se obtiene el alumno_id enviado por el formulario
    $resultado = $alumno->Listar_alumnos($alumno_id); // Se llama a la función Listar_alumnos con el alumno_id
    if ($resultado&&$resultado->num_rows > 0) { // Si el resultado tiene más de 0 filas
        echo "<h2>Alumnos de tu clase</h2>";
        echo "<table>";
        echo "<thead><tr><th>Nombre</th></tr></thead>";
        echo "<tbody>";
        while ($datos=$resultado->fetch_assoc()) { // Recorre el resultado
            echo "<tr>";
            echo "<td>".$datos['nombre']."</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "No se encontraron alumnos";
    }
?>