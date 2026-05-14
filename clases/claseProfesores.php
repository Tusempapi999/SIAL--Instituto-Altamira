<?php
    include('claseUser.php'); // Se incluye el archivo de la clase User
    class profesor extends user { // Se crea la clase profesor que hereda de la clase User

        public function Listar_alumnos($grupo_id) { 
    // Cambiamos u.id por a.id para que el sistema use el ID de alumno correcto
    $this->sentencia = "SELECT a.id, u.nombre, u.email
                        FROM usuario u
                        INNER JOIN alumno a ON u.id = a.usuario_id 
                        INNER JOIN matriculado m ON a.id = m.alumno_id 
                        WHERE m.grupo_id = '$grupo_id'"; 
    return $this->obtener_sentencia(); 
}

            // NUEVA FUNCIÓN: Obtiene los grupos que imparte el profesor logueado
            public function Listar_mis_grupos($usuario_id) {
                $this->sentencia = "SELECT g.id, asig.nombre AS asignatura, g.grado, g.letra_grupo 
                                    FROM grupo g
                                    INNER JOIN asignatura asig ON g.asignatura_id = asig.id
                                    INNER JOIN profesor p ON g.profesor_id = p.id
                                    WHERE p.usuario_id = '$usuario_id'";
                return $this->obtener_sentencia();
            }


        public function Listar_todos_los_profesores() {
            // Traemos el nombre del usuario y la especialidad de la tabla profesor
            $this->sentencia = "SELECT u.nombre, u.email, p.especialidad 
                                FROM usuario u
                                INNER JOIN profesor p ON u.id = p.usuario_id 
                                WHERE u.rol = 'profesor' AND u.activo = 1";
            return $this->obtener_sentencia();
        }

        public function Listar_todas_mis_notas($id_profe_usuario) {
    // Usamos INNER JOIN para conectar matriculado -> alumno -> usuario (para el nombre)
    // Y matriculado -> grupo -> asignatura (para el nombre de la materia)
    $this->sentencia = "SELECT 
                            u.nombre AS nombre_alumno, 
                            asig.nombre AS asignatura, 
                            m.calificacion_1, 
                            m.calificacion_2, 
                            m.calificacion_3, 
                            m.promedio_final 
                        FROM matriculado m
                        INNER JOIN alumno a ON m.alumno_id = a.id
                        INNER JOIN usuario u ON a.usuario_id = u.id
                        INNER JOIN grupo g ON m.grupo_id = g.id
                        INNER JOIN asignatura asig ON g.asignatura_id = asig.id
                        INNER JOIN profesor p ON g.profesor_id = p.id
                        WHERE p.usuario_id = '$id_profe_usuario'
                        ORDER BY u.nombre ASC";
    return $this->obtener_sentencia();
}
      // CUIDADO: El nombre dentro del paréntesis debe ser IGUAL al de la línea 43
        public function Listar_notas_por_profesor($alumno_id, $usuario_id_profesor) {
    // Buscamos directamente por el ID de la tabla alumno
    $this->sentencia = "SELECT u.nombre, asig.nombre AS asignatura, 
                               m.calificacion_1, m.calificacion_2, m.calificacion_3, m.promedio_final
                        FROM matriculado m
                        INNER JOIN alumno a ON m.alumno_id = a.id
                        INNER JOIN usuario u ON a.usuario_id = u.id
                        INNER JOIN grupo g ON m.grupo_id = g.id
                        INNER JOIN asignatura asig ON g.asignatura_id = asig.id
                        INNER JOIN profesor p ON g.profesor_id = p.id
                        WHERE m.alumno_id = '$alumno_id' 
                        AND p.usuario_id = '$usuario_id_profesor'"; 
    return $this->obtener_sentencia();
}
        public function IngresarCalificacion($alumno_id, $grupo_id, $nota, $parcial) {
            // Validamos qué columna actualizar según el parcial (1, 2 o 3)
            // Esto evita inyecciones de nombres de columna no deseados
            $columna = "calificacion_" . $parcial;
            if (!in_array($parcial, [1, 2, 3])) {
                $columna = "alificacion_1"; 
            }

            // Usamos directamente los IDs de la tabla matriculado para mayor eficiencia
            // No hace falta hacer JOIN si ya tienes el alumno_id y grupo_id
            $this->sentencia = "UPDATE matriculado 
                                SET $columna = $nota 
                                WHERE alumno_id = $alumno_id 
                                AND grupo_id = $grupo_id";

            return $this->ejecutar_sentencia(); 
        }
    }
?>