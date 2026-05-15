<?php
    include('conexion.php'); // Se incluye el archivo de conexión a la base de datos

    class User extends Conexion { // Se crea la clase User que hereda de la clase Conexion
    
        public function iniciar_sesion($matricula, $pwd) { // Función para iniciar sesión con un matricula y contraseña
            $this->sentencia = "SELECT * FROM usuario WHERE id='$matricula' AND pwd='$pwd'";
            return $this->obtener_sentencia(); // Se ejecuta la sentencia SQL y se devuelve el resultado
        }

        public function cerrar_sesion() { // Función para cerrar sesión, se destruye la sesión actual
            session_destroy();
        }
    }
?>