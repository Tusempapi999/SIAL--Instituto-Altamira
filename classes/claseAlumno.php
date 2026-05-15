<?php
include('classes/claseUser.php');

class alumno extends user {

    // ===== LISTAR ALUMNOS DEL GRUPO =====
    public function Listar_alumnos($grupo_id) {
        $this->sentencia = "
            SELECT alumno.id AS alumno_id,
                   usuario.nombre,
                   usuario.email
            FROM matriculado
            INNER JOIN alumno ON matriculado.alumno_id = alumno.id
            INNER JOIN usuario ON alumno.usuario_id = usuario.id
            WHERE matriculado.grupo_id = '$grupo_id'
        ";
        return $this->obtener_sentencia();
    }

    // ===== LISTAR PROFESORES DEL ALUMNO =====
    public function Listar_profesores($alumno_id) {
        $this->sentencia = "
            SELECT usuario.nombre,
                   profesor.especialidad,
                   asignatura.nombre AS asignatura
            FROM matriculado
            INNER JOIN grupo ON matriculado.grupo_id = grupo.id
            INNER JOIN profesor ON grupo.profesor_id = profesor.id
            INNER JOIN usuario ON profesor.usuario_id = usuario.id
            INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id
            WHERE matriculado.alumno_id = '$alumno_id'
        ";
        return $this->obtener_sentencia();
    }

    // ===== LISTAR ASIGNATURAS DEL ALUMNO =====
    public function setCalificacion($alumno_id) {
        $this->sentencia = "
            SELECT asignatura.nombre AS asignatura,
                   matriculado.grupo_id,
                   matriculado.calificacion_1,
                   matriculado.calificacion_2,
                   matriculado.calificacion_3,
                   matriculado.promedio_final
            FROM matriculado
            INNER JOIN grupo ON matriculado.grupo_id = grupo.id
            INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id
            WHERE matriculado.alumno_id = '$alumno_id'
        ";
        return $this->obtener_sentencia();
    }

    // ===== VER CALIFICACIÓN DE UNA ASIGNATURA =====
    public function verCalificacion($alumno_id, $grupo_id) {
        $this->sentencia = "
            SELECT asignatura.nombre AS asignatura,
                   matriculado.calificacion_1,
                   matriculado.calificacion_2,
                   matriculado.calificacion_3,
                   matriculado.promedio_final
            FROM matriculado
            INNER JOIN grupo ON matriculado.grupo_id = grupo.id
            INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id
            WHERE matriculado.alumno_id = '$alumno_id'
              AND matriculado.grupo_id = '$grupo_id'
        ";
        return $this->obtener_sentencia();
    }
}
?>