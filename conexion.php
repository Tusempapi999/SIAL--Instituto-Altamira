<?php
// se crea la clase Conexion
    class Conexion {
        private $host = "localhost"; // Nomre del servidor
        private $user = "root"; // Nombre del usuario del servidor
        private $password = ""; // Contraseña del usuario del servidor
        private $database = "Altamira"; // Nombre de la base de datos a la que se va a conectar
        public $sentencia; // Variable para almacenar la sentencia SQL a ejecutar
        private $conexion; // Variable para almacenar la conexión a la base de datos
        
        private function abrir_conexion() { // Función para abrir la conexión a la base de datos
            $this->conexion = new mysqli($this->host, $this->user, $this->password, $this->database); 
            // Se crea la conexión mediante mysqli, se le pasan los parámetros de conexión
        }

        private function cerrar_conexion() { // Función para cerrar la conexión a la base de datos
            $this->conexion->close(); // close() es un método de mysqli para cerrar la conexión a la base de datos
        }

        //Ejecuta INSERT, UPDATE, DELETE
        public function ejecutar_sentencia() { // Función para ejecutar la sentencia SQL almacenada en la variable $sentencia
            $this->abrir_conexion(); // Se abre la conexión a la base de datos
            $bandera = $this->conexion->query($this->sentencia); // Se ejecuta la sentencia SQL y se almacena el bandera en la variable $bandera
            $this->cerrar_conexion(); // Se cierra la conexión a la base de datos
            return $bandera; // Se devuelve el bandera de la consulta
        }

        //query() es un metodo de mysqli para ejecutar una sentencia en la base de datos
        //ya sea para INSERT, UPDATE, DELETE en estos casos devuelve true o false dependiendo del exito de la sentencia
        //Cuando es para una sentencia SELECT devuelve un objeto con la informacion.

        //Ejecuta SELECT
        public function obtener_sentencia() { 
            $this->abrir_conexion();
            $resultado = $this->conexion->query($this->sentencia); //se manda la sentencia SELEC y se recibe el resultado en $resultado
            $this->cerrar_conexion();
            return $resultado; // Se devuelve el resultado de la consulta SELECT
        }

        //Los dos metodos anteriores son iguales, solo que ejecutar_sentencia() solo lo usamos para INSET, UPDATE Y DELETE
        //mientras que obtener_sentencia() solo la usamos para SELECT.
    }