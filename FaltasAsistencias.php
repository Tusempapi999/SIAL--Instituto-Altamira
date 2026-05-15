<?php
    include('clases/claseAlumno.php'); // Se incluye el archivo de la clase Alumno
    $alumno = new alumno(); // Se crea una instancia de la clase Alumno
    $alumno_id = $_GET['alumno_id']; // Se obtiene el alumno_id de la URL
    $grupo_id = $_GET['grupo_id']; // Se obtiene el grupo_id de la URL
    $resultado = $alumno->FaltasAsistencias($alumno_id, $grupo_id); // Se llama a la función FaltasAsistencias con el alumno_id y el grupo_id
    if ($resultado&&$resultado->num_rows > 0) { // Si el resultado tiene más de 0 filas
        echo "<h2>Tus asignaturas</h2>";
        echo "<ul>";
        while ($datos = $resultado->fetch_assoc()) { // Recorre el resultado
            echo "<li><a href='verCalificacion.php?alumno_id=".$alumno_id."&grupo_id=".$datos['grupo_id']."'>".$datos['asignatura']."</a></li>";
            // cada asignatura es un link que manda el alumno_id y el grupo_id a verCalificacion.php
        }
        echo "</ul>";
    } else {
        echo "No se encontraron asignaturas";
    }
?>