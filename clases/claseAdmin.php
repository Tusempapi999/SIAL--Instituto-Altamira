<?php
    class admin extends user { // Se crea la clase admin que hereda de la clase User

        public function agregar_usuario($matricula, $pwd) { // Función para agregar un nuevo usuario con matricula y contraseña
            $this->sentencia = "INSERT INTO usuario (id, pwd) VALUES ('$matricula', '$pwd')";
            return $this->ejecutar_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado

        }

        public function eliminar_usuario($matricula) { // Función para eliminar un usuario con una matricula específica
            $this->sentencia = "DELETE FROM usuario WHERE id='$matricula'";
            return $this->ejecutar_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function modificar_usuario($matricula, $pwd) { // Función para modificar la contraseña de un usuario con una matricula específica
            $this->sentencia = "UPDATE usuario SET pwd='$pwd' WHERE id='$matricula'";
            return $this->ejecutar_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function matricula_alumno($alumno_id, $grupo_id) { 
            $this->sentencia = "INSERT INTO matriculado (alumno_id, grupo_id) VALUES ('$alumno_id', '$grupo_id')";
            return $this->ejecutar_sentencia();
        }
    }
?>