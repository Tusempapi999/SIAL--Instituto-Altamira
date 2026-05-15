<?php
    include('claseUser.php'); // Se incluye el archivo de la clase User
    class alumno extends user { // Se crea la clase alumno que hereda de la clase User

        public function Listar_alumnos($grupo_id) { // Función para listar un alumno con una matricula específica 
            $this->sentencia = "SELECT nombre FROM usuario 
                                INNER JOIN alumno ON usuario.id = alumno.user_id 
                                INNER JOIN matriculado ON alumno.id = matriculado.alumno_id 
                                WHERE matriculado.grupo_id = '$grupo_id'"; 
                                //Muestrame el nombre de la tabla usuario uniendome
                                // alumno donde el id de usuario sea igual al user_id de alumno y uniendome la tabla matricula 
                                // donde el id de alumno sea igual al alumno_id de matriculado y donde el grupo_id de matriculado sea igual a grupo_id

            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function Listar_profesores($alumno_id) { // Función para listar profesores
            $this->sentencia = "SELECT usuario.nombre, profesor.especialidad, asignatura.nombre AS asignatura 
                                FROM usuario 
                                INNER JOIN profesor ON usuario.id = profesor.user_id 
                                INNER JOIN grupo ON profesor.id = grupo.profesor_id 
                                INNER JOIN asignatura ON grupo.asignatura_id = asignatura.id 
                                INNER JOIN matriculado ON grupo.id = matriculado.grupo_id 
                                WHERE matriculado.alumno_id = '$alumno_id'";
                                // Muestrame el nombre del usuario, la especialidad del profesor y el nombre de la asignatura
                                // uniendo la tabla usuario con profesor donde el id de usuario sea igual al user_id de profesor
                                // uniendo la tabla profesor con grupo donde el id de profesor sea igual al profesor_id de grupo
                                // uniendo la tabla grupo con asignatura donde el asignatura_id de grupo sea igual al id de asignatura
                                // uniendo la tabla grupo con matriculado donde el id de grupo sea igual al grupo_id de matriculado
                                // donde el alumno_id de matriculado sea igual al alumno_id recibido

            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

    }
?>