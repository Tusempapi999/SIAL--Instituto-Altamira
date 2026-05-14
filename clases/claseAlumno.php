<?php
    include('claseUser.php'); // Se incluye el archivo de la clase User
    class alumno extends user { // Se crea la clase alumno que hereda de la clase User

        public function Listar_alumnos($grupo_id) { // Función para listar un alumno con una matricula específica 
           $this->sentencia = "SELECT usuario.id, usuario.nombre, usuario.email
                                    FROM usuario 
                                    INNER JOIN alumno  ON usuario.id = alumno.usuario_id 
                                    INNER JOIN matriculado ON alumno.id = matriculado.alumno_id 
                                    WHERE matriculado.grupo_id = '$grupo_id'"; 
                return $this->obtener_sentencia(); 
        }

        public function Listar_profesores($alumno_id) { 
            $this->sentencia = "SELECT u.nombre, p.especialidad, a.nombre AS asignatura 
                                FROM usuario u
                                INNER JOIN profesor p ON u.id = p.usuario_id 
                                INNER JOIN grupo g ON p.id = g.profesor_id 
                                INNER JOIN asignatura a ON g.asignatura_id = a.id 
                                INNER JOIN matriculado m ON g.id = m.grupo_id 
                                WHERE m.alumno_id = '$alumno_id'";

            return $this->obtener_sentencia(); 
        }

        public function setCalificacion($alumno_id) { // Función para obtener las asignaturas del alumno conectado
            $this->sentencia = "SELECT nombre AS asignatura, matriculado.grupo_id, matriculado.calificacion_1, matriculado.calificacion_2, matriculado.calificacion_3,
                                matriculado.promedio_final
                                FROM matriculado 
                                INNER JOIN grupo ON matriculado.grupo_id = grupo.id 
                                INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id 
                                WHERE matriculado.alumno_id = '$alumno_id'";
                                // Muestrame el nombre de la asignatura, el grupo_id y la calificacion de la tabla matriculado
                                // uniendo la tabla matriculado con grupo donde el grupo_id de matriculado sea igual al id de grupo
                                // uniendo la tabla grupo con asignatura donde el asignatura_id de grupo sea igual al id de asignatura
                                // donde el alumno_id de matriculado sea igual al alumno_id recibido
            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
}

        public function obtenerAlumnoPorMatricula($matricula) {
            $this->sentencia = "SELECT id, nombre, email, rol, fecha_nacimiento 
                                FROM usuario 
                                WHERE id = '$matricula'";
            return $this->ejecutar_sentencia();
        }

        

        public function verCalificacion($alumno_id, $grupo_id) { // Función para obtener parciales y calificacion de una asignatura
            $this->sentencia = "SELECT asignatura.nombre AS asignatura, matriculado.calificacion_1, matriculado.calificacion_2, matriculado.calificacion_3,
                                matriculado.promedio_final
                                FROM matriculado
                                INNER JOIN grupo ON matriculado.grupo_id = grupo.id
                                INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id
                                WHERE matriculado.alumno_id = '$alumno_id' AND matriculado.grupo_id = '$grupo_id'";
                                // Raund es una función de MySQL que redondea el resultado a un decimal, se le pasa 
                                // como primer parámetro la operación para calcular el promedio de los 3 parciales 
                                // y como segundo parámetro el número de decimales a mostrar
                                // Muestrame el nombre de la asignatura, los 3 parciales y el promedio calculado
                                // uniendo la tabla matriculado con grupo donde el grupo_id de matriculado sea igual al id de grupo
                                // uniendo la tabla grupo con asignatura donde el asignatura_id de grupo sea igual al id de asignatura
                                // donde el alumno_id sea igual al alumno_id recibido y el grupo_id sea igual al grupo_id recibido
            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }
    }
?>