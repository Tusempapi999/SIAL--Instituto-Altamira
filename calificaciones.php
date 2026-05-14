<?php
    include('clases/claseProfesores.php'); 
    $profesor = new profesor(); 
    
    // Cambiado: Generalmente se recibe por nombre de variable, ej: ?alumno_id=1
    $id_alumno = isset($_GET['alumno_id']) ? $_GET['alumno_id'] : 1; 

    // Llamamos a la nueva función que trae notas
    $resultado = $profesor->Listar_profesores_con_notas($id_alumno); 

    if ($resultado && $resultado->num_rows > 0) {
        echo "<h2>Tus Profesores y Calificaciones</h2>";
        echo "<table border='1'>";
        echo "<thead>
                <tr>
                    <th>Nombre</th>
                    <th>Especialidad</th>
                    <th>Asignatura</th>
                    <th>Parcial 1</th>
                    <th>Parcial 2</th>
                    <th>Parcial 3</th>
                    <th>Promedio</th>
                </tr>
              </thead>";
        echo "<tbody>";
        
        while ($datos = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$datos['nombre']."</td>";
            echo "<td>".$datos['especialidad']."</td>";
            echo "<td>".$datos['asignatura']."</td>";
            // Mostramos las calificaciones traídas de la tabla matriculado
            echo "<td>".$datos['calificacion_1']."</td>";
            echo "<td>".$datos['calificacion_2']."</td>";
            echo "<td>".$datos['calificacion_3']."</td>";
            echo "<td><strong>".$datos['promedio_final']."</strong></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "No se encontraron registros para este alumno.";
    }
?>