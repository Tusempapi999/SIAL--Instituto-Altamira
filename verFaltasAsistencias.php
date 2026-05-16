<?php
    include('clases/claseAlumno.php'); // Se incluye el archivo de la clase Alumno
    $alumno = new Alumno(); // Se crea una instancia de la clase Alumno
    $alumno_id = $_GET['alumno_id']; // Se obtiene el alumno_id de la URL
    $grupo_id = $_GET['grupo_id']; // Se obtiene el grupo_id de la URL
    $resultado = $alumno->verFaltasAsistencia($alumno_id, $grupo_id); // Se llama a la función verFaltasAsistencia
    if ($resultado && $resultado->num_rows > 0) { // Si el resultado tiene más de 0 filas
        echo "<h2>Número de faltas de asistencia</h2>";
        echo "<table border='1'>";
        echo "<thead><tr><th>Fecha</th><th>Justificado</th><th>Cantidad de faltas</th></tr></thead>";
        echo "<tbody>";
        while ($datos = $resultado->fetch_assoc()) { // Recorre el resultado
            echo "<tr>";
            echo "<td>".$datos['fecha']."</td>";
            echo "<td>".$datos['justificado']."</td>";
            echo "<td>".$datos['cantidad_faltas']."</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "<br><a href='FaltasAsistencias.php?alumno_id=".$alumno_id."'>Regresar</a>";
    } else {
        echo "No se encontraron faltas de asistencia";
    }
?>