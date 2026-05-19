<?php
    require_once('clases/claseUser.php'); // Se incluye el archivo de la clase admin
    class admin extends User { // Se crea la clase admin que hereda de la clase User

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

        public function buscarUsuario($id) { // Función para obtener un usuario por su ID
            $this->sentencia = "SELECT * FROM usuario WHERE id='$id'";
            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function buscarGrupo($grado, $letra_grupo) { // Función para obtener un grupo por su grado y letra
            $this->sentencia = "SELECT * FROM grupo WHERE grado='$grado' AND letra_grupo='$letra_grupo'";
            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }
    }