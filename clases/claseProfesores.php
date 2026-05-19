<?php
require_once('claseUser.php'); 

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

    // 7. REGISTRAR ASISTENCIA DE GRUPOS NORMALES
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

    // 9. ACTUALIZAR ASISTENCIA DE GRUPOS NORMALES
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

    
    // ==========================================
    // NUEVOS MÉTODOS PARA EL FUNCIONAMIENTO DE CLUBS (CORREGIDOS)
    // ==========================================

    // ==========================================
    // NUEVOS MÉTODOS PARA EL FUNCIONAMIENTO DE CLUBS (LOGICA DE ASISTENCIA)
    // ==========================================

    // 10. LISTAR ALUMNOS PERTENECIENTES A UN CLUB ESPECÍFICO
    public function Listar_alumnos_club($club_id) {
        $this->sentencia = "SELECT a.id AS alumno_id, u.nombre 
                            FROM usuario u
                            INNER JOIN alumno a ON u.id = a.usuario_id
                            INNER JOIN inscripcion_club ic ON a.id = ic.alumno_id
                            WHERE ic.club_id = '$club_id'
                            ORDER BY u.nombre ASC";
        return $this->obtener_sentencia();
    }

    // 11. BUSCAR UN CLUB ID AUTOMÁTICAMENTE PARA EL PROFESOR
    public function Obtener_club_por_profesor($usuario_id_profe) {
        $this->sentencia = "SELECT c.id 
                            FROM club c
                            INNER JOIN profesor p ON c.profesor_id = p.id
                            WHERE p.usuario_id = '$usuario_id_profe' 
                            LIMIT 1";
        $resultado = $this->obtener_sentencia();
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return $fila['id'];
        }
        return null;
    }

    // 12. GUARDAR LA ASISTENCIA INDIVIDUAL DEL CLUB
    public function Registrar_asistencia_club($alumno_id, $club_id, $fecha, $estado) {
        // Vinculación directa con la tabla inscripcion_club
        $this->sentencia = "INSERT INTO asistencia_club (alumno_id, fecha, estado)
                            VALUES ('$alumno_id', '$fecha', '$estado')
                            ON DUPLICATE KEY UPDATE estado = '$estado'";
        return $this->ejecutar_sentencia();
    }

    // 13. RESUMEN ACUMULADO DE ASISTENCIAS Y FALTAS PARA EL CLUB
    public function Resumen_asistencia_club($club_id) {
        $this->sentencia = "
            SELECT 
                u.nombre AS alumno,
                SUM(acb.estado = 'presente') AS asistencias,
                SUM(acb.estado = 'sin justificar') AS faltas_sin_justificar,
                SUM(acb.estado = 'justificado') AS faltas_justificadas,
                SUM(acb.estado = 'retardo') AS retardos
            FROM inscripcion_club ic
            INNER JOIN alumno al ON al.id = ic.alumno_id
            INNER JOIN usuario u ON u.id = al.usuario_id
            LEFT JOIN asistencia_club acb ON acb.alumno_id = ic.alumno_id
            WHERE ic.club_id = '$club_id'
            GROUP BY ic.alumno_id
            ORDER BY u.nombre ASC
        ";
        return $this->obtener_sentencia();
    }
    // 14. ACTUALIZAR ASISTENCIA EXISTENTE EN UN CLUB
    public function Actualizar_asistencia_club($alumno_id, $club_id, $fecha, $estado) {
        $this->sentencia = "
            UPDATE asistencia_club acb
            INNER JOIN alumno_club ac ON acb.alumno_club_id = ac.id
            SET acb.estado = '$estado'
            WHERE ac.alumno_id = '$alumno_id'
            AND ac.club_id = '$club_id'
            AND acb.fecha = '$fecha'
        ";
        return $this->ejecutar_sentencia();
    }
}
?>