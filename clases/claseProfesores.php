<?php
include('claseUser.php'); 

class profesor extends user { 

    // 1. LISTAR ALUMNOS DE UN GRUPO ESPECÍFICO
    // Importante: Seleccionamos a.id (ID de tabla alumno) para que el guardado funcione
    public function Listar_alumnos($grupo_id) { 
        $this->sentencia = "SELECT a.id, u.nombre, u.email
                            FROM usuario u
                            INNER JOIN alumno a ON u.id = a.usuario_id 
                            INNER JOIN matriculado m ON a.id = m.alumno_id 
                            WHERE m.grupo_id = '$grupo_id'
                            ORDER BY u.nombre ASC"; 
        return $this->obtener_sentencia(); 
    }

    // 2. LISTAR LOS GRUPOS ASIGNADOS AL PROFESOR
    public function Listar_mis_grupos($usuario_id_profe) {
        $this->sentencia = "SELECT g.id, asig.nombre AS asignatura, g.grado, g.letra_grupo 
                            FROM grupo g
                            INNER JOIN asignatura asig ON g.asignatura_id = asig.id
                            INNER JOIN profesor p ON g.profesor_id = p.id
                            WHERE p.usuario_id = '$usuario_id_profe'";
        return $this->obtener_sentencia();
    }

    // 3. REPORTE GENERAL DE NOTAS (TODOS LOS ALUMNOS DEL PROFE)
    public function Listar_todas_mis_notas($usuario_id_profe) {
        $this->sentencia = "SELECT 
                                u_alum.nombre AS nombre_alumno, 
                                asig.nombre AS asignatura, 
                                m.calificacion_1, 
                                m.calificacion_2, 
                                m.calificacion_3, 
                                m.promedio_final 
                            FROM matriculado m
                            INNER JOIN alumno a ON m.alumno_id = a.id
                            INNER JOIN usuario u_alum ON a.usuario_id = u_alum.id
                            INNER JOIN grupo g ON m.grupo_id = g.id
                            INNER JOIN asignatura asig ON g.asignatura_id = asig.id
                            INNER JOIN profesor p ON g.profesor_id = p.id
                            WHERE p.usuario_id = '$usuario_id_profe'
                            ORDER BY u_alum.nombre ASC";
        return $this->obtener_sentencia();
    }

    // 4. NOTAS DE UN ALUMNO ESPECÍFICO (PARA EL FORMULARIO DE EDICIÓN)
    public function Listar_notas_por_profesor($alumno_id, $usuario_id_profe) {
        $this->sentencia = "SELECT u_alum.nombre, asig.nombre AS asignatura, 
                                   m.calificacion_1, m.calificacion_2, m.calificacion_3, m.promedio_final
                            FROM matriculado m
                            INNER JOIN alumno a ON m.alumno_id = a.id
                            INNER JOIN usuario u_alum ON a.usuario_id = u_alum.id
                            INNER JOIN grupo g ON m.grupo_id = g.id
                            INNER JOIN asignatura asig ON g.asignatura_id = asig.id
                            INNER JOIN profesor p ON g.profesor_id = p.id
                            WHERE m.alumno_id = '$alumno_id' 
                            AND p.usuario_id = '$usuario_id_profe'"; 
        return $this->obtener_sentencia();
    }

    // 5. GUARDAR O ACTUALIZAR CALIFICACIÓN
    public function IngresarCalificacion($alumno_id, $grupo_id, $nota, $parcial) {
        $columna = "calificacion_" . $parcial;
        
        // Aseguramos que la nota sea un número válido para SQL (ej: 8.5)
        $nota_valida = floatval($nota);

        $this->sentencia = "UPDATE matriculado 
                            SET $columna = $nota_valida 
                            WHERE alumno_id = '$alumno_id' 
                            AND grupo_id = '$grupo_id'";

        return $this->ejecutar_sentencia(); 
    }

    // 6. LISTAR DIRECTORIO DE PROFESORES
    public function Listar_todos_los_profesores() {
        $this->sentencia = "SELECT u.nombre, u.email, p.especialidad 
                            FROM usuario u 
                            INNER JOIN profesor p ON u.id = p.usuario_id 
                            WHERE u.rol = 'profesor'";
        return $this->obtener_sentencia();
    }
}
?>