<?php
include('claseUser.php'); 

class profesor extends user { 

    // 1. LISTAR ALUMNOS DE UN GRUPO ESPECÍFICO
    public function Listar_alumnos($grupo_id) { 
        $this->sentencia = "SELECT a.id AS alumno_id, u.nombre, u.email
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

    // 3. REPORTE GENERAL DE NOTAS
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

    // 4. NOTAS DE UN ALUMNO ESPECÍFICO
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

    // 7. REGISTRAR ASISTENCIA (CORREGIDO: Ahora usa ejecutar_sentencia)
    public function Registrar_asistencia_por_profesor($alumno_id, $usuario_id_profe, $grupo_id, $fecha, $estado) {
        $this->sentencia = "INSERT INTO asistencia (matriculado_id, fecha, estado)
                            SELECT m.id, '$fecha', '$estado'
                            FROM matriculado m
                            INNER JOIN grupo g ON m.grupo_id = g.id
                            INNER JOIN profesor p ON g.profesor_id = p.id
                            WHERE m.alumno_id = '$alumno_id' 
                            AND g.id = '$grupo_id'
                            AND p.usuario_id = '$usuario_id_profe'
                            ON DUPLICATE KEY UPDATE estado = VALUES(estado)"; 
        return $this->ejecutar_sentencia(); 
    }

        // 8. RESUMEN DE ASISTENCIA POR ALUMNO EN UN GRUPO
    public function Resumen_asistencia_por_grupo($grupo_id) {

        $this->sentencia = "
            SELECT 
                u.nombre AS alumno,
                SUM(a.estado = 'presente') AS asistencias,
                SUM(a.estado = 'sin justificar') AS faltas_sin_justificar,
                SUM(a.estado = 'justificado') AS faltas_justificadas,
                SUM(a.estado = 'retardo') AS retardos
            FROM matriculado m
            INNER JOIN alumno al ON al.id = m.alumno_id
            INNER JOIN usuario u ON u.id = al.usuario_id
            LEFT JOIN asistencia a ON a.matriculado_id = m.id
            WHERE m.grupo_id = '$grupo_id'
            GROUP BY m.id
            ORDER BY u.nombre ASC
        ";

        return $this->obtener_sentencia();
    }
    public function Actualizar_asistencia($alumno_id, $grupo_id, $fecha, $estado) {

        $this->sentencia = "
            UPDATE asistencia a
            INNER JOIN matriculado m ON a.matriculado_id = m.id
            SET a.estado = '$estado'
            WHERE m.alumno_id = '$alumno_id'
            AND m.grupo_id = '$grupo_id'
            AND a.fecha = '$fecha'
        ";

        return $this->ejecutar_sentencia();
    }
    
}
?>