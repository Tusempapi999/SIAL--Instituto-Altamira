<?php
    include('clases/claseAlumno.php'); // Se incluye el archivo de la clase Alumno
    $alumno = new Alumno(); // Se crea una instancia de la clase Alumno
    $alumno_id = $_GET['alumno_id']; // Se obtiene el alumno_id de la URL
    $grupo_id = $_GET['grupo_id']; // Se obtiene el grupo_id de la URL
    $resultado = $alumno->verCalificacion($alumno_id, $grupo_id); // Se llama a la función verCalificacion
    if ($resultado && $resultado->num_rows > 0) { // Si el resultado tiene más de 0 filas
        echo "<h2>Calificaciones</h2>";
        echo "<table border='1'>";
        echo "<thead><tr><th>Asignatura</th><th>Calificación 1</th><th>Calificación 2</th><th>Calificación 3</th><th>Promedio Final</th></tr></thead>";
        echo "<tbody>";
        while ($datos = $resultado->fetch_assoc()) { // Recorre el resultado
            echo "<tr>";
            echo "<td>".$datos['asignatura']."</td>";
            echo "<td>".$datos['calificacion_1']."</td>";
            echo "<td>".$datos['calificacion_2']."</td>";
            echo "<td>".$datos['calificacion_3']."</td>";
            echo "<td>".$datos['promedio_final']."</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "<br><a href='setCalificacion.php?alumno_id=".$alumno_id."'>Regresar</a>";
    } else {
        echo "No se encontraron calificaciones";
    }
?>