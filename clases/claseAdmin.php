<?php
    class admin extends user { // Se crea la clase admin que hereda de la clase User

        public function agregar_usuario($nombre, $email, $pwd, $rol, $fecha_nacimiento) { // Función para agregar un nuevo usuario con matricula y contraseña
            $this->sentencia = "INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('$nombre', '$email', '$pwd', '$rol', '$fecha_nacimiento')";
            return $this->ejecutar_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function eliminar_usuario($matricula) { // Función para eliminar un usuario con una matricula específica
            $this->sentencia = "DELETE FROM usuario WHERE id='$matricula'";
            return $this->ejecutar_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function modificar_usuario($matricula, $nombre, $email, $pwd, $rol, $fecha_nacimiento) { // Función para modificar la contraseña de un usuario con una matricula específica
            $this->sentencia = "UPDATE usuario SET nombre='$nombre', email='$email', pwd='$pwd', rol='$rol', fecha_nacimiento='$fecha_nacimiento' WHERE id='$matricula'";
            return $this->ejecutar_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function listarUsuarios() { // Función para listar todos los usuarios registrados
            $this->sentencia = "SELECT * FROM usuario";
            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function matricularAlumno($alumno_id, $grupo_id) { // Función para matricular a un alumno en un grupo específico
            $this->sentencia = "INSERT INTO matriculado (alumno_id, grupo_id) VALUES ('$alumno_id', '$grupo_id')";
            return $this->ejecutar_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        
    }