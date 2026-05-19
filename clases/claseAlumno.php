<?php
require_once('clases/claseUser.php');

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

    //VER FALTAS Y ASISTENCIAS DE UNA ASIGNATURA
    public function FaltasAsistencias($alumno_id, $grupo_id) {
        $this->sentencia = "
            SELECT matriculado.id AS matriculado_id, 
                    asignatura.nombre AS asignatura_nombre,
                    grupo.grado,
                    grupo.letra_grupo
                FROM matriculado
                INNER JOIN grupo ON matriculado.grupo_id = grupo.id
                INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id
                WHERE matriculado.alumno_id = '$alumno_id'
        ";
        return $this->obtener_sentencia();
    }

    // ===== LISTAR ASIGNATURAS PARA EL PANEL DE ASISTENCIA =====
    public function verFaltasAsistencia($grupo_id, $alumno_id) {
    $this->sentencia = "
            SELECT asistencia.fecha, asistencia.estado 
            FROM asistencia 
            INNER JOIN matriculado ON asistencia.matriculado_id = matriculado.id
            WHERE matriculado.grupo_id = '$grupo_id' 
            AND matriculado.alumno_id = '$alumno_id'
        ";
        return $this->obtener_sentencia();
    }

    //CONTAR FALTAS DE ASISTENCIA
    public function contarFaltasAsistencia($matriculado_id) {
        $this->sentencia = "
            SELECT COUNT(*) as cantidad 
            FROM asistencia 
            INNER JOIN matriculado ON asistencia.matriculado_id = matriculado.id
            INNER JOIN grupo ON matriculado.grupo_id = grupo.id
            WHERE grupo.asignatura_id = '$asignatura_id' AND matriculado.alumno_id = '$alumno_id' AND
            asistencia.estado = 'sin justificar' GROUP BY matriculado.alumno_id
        ";
        return $this->obtener_sentencia();
    }

    //Consultar horario del alumno
    public function verHorarioAsignatura($grupo_id) {
    // Consulta simple: trae día y horas de la tabla horario para ese grupo
    $this->sentencia = "
            SELECT dia_semana, hora_inicio, hora_fin 
            FROM horario 
            WHERE grupo_id = '$grupo_id' 
            ORDER BY dia_semana ASC
        ";
        return $this->obtener_sentencia();
    }

    //Listar clubes sabatinos
    public function listarMisClubs($alumno_id) {
    $this->sentencia = "
            SELECT asignatura.nombre AS club, 
                    grupo.id AS grupo_id
            FROM matriculado
            INNER JOIN grupo ON matriculado.grupo_id = grupo.id
            INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id
            WHERE matriculado.alumno_id = '$alumno_id' 
            AND UPPER(asignatura.nombre) LIKE '%CLUB%'
        "; 
    
        return $this->obtener_sentencia();
    }

    //Ver clubes sabatinos
    public function verAsistenciaClub($grupo_id, $alumno_id) {        $this->sentencia = "
            SELECT asistencia.fecha, asistencia.estado
            FROM asistencia
            INNER JOIN matriculado m ON asistencia.matriculado_id = matriculado.id
            WHERE matriculado.grupo_id = '$grupo_id' 
            AND matriculado.alumno_id = '$alumno_id'
            ORDER BY asistencia.fecha DESC
        ";
        return $this->obtener_sentencia();
    }

    // Inscribirse al club sabatino
    public function inscribirClub($alumno_id, $grupo_id) {
    $this->sentencia = "
        INSERT INTO matriculado (alumno_id, grupo_id) VALUES ('$alumno_id', '$grupo_id')
        ";
    return $this->ejecutar_sentencia();
}

    // Listar clubes disponibles para inscripción
    public function listarClubesDisponibles($alumno_id) {
        $this->sentencia = "
            SELECT asignatura.nombre AS club, 
                    grupo.id AS grupo_id
            FROM grupo
            INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id
            LEFT JOIN matriculado ON matriculado.grupo_id = grupo.id AND matriculado.alumno_id = '$alumno_id'
            WHERE asignatura.nombre LIKE '%Club%'
            AND matriculado.id IS NULL
        ";
        return $this->obtener_sentencia();
    }
}
?>