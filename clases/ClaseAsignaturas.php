<?php
// Se incluye el archivo de conexión a la base de datos
require_once('conexion.php');

// Definición de la clase Asignatura
class Asignatura extends Conexion {

    // Atributos privados de la clase
    private $id;
    private $nombre;
    private $descripcion;
    

    // Método para dar de alta una asignatura
    public function altaAsignatura($nombre, $descripcion){


        $this->sentencia = "INSERT INTO asignatura (nombre, descripcion) VALUES ('$nombre','$descripcion')";

        return $this->ejecutar_sentencia();
    }

    // Método para eliminar una asignatura por ID
    public function bajaAsignatura($id){

        if (!is_numeric($id)) {
            return false;
        }


        $this->sentencia = "DELETE FROM asignatura WHERE id = '$id'";

        return $this->ejecutar_sentencia();
    }

    // Método para modificar una asignatura
    public function modificarAsignatura($id, $nombre, $descripcion){


        $this->sentencia = "UPDATE asignatura 
                                SET nombre = '$nombre', descripcion = '$descripcion'
                                WHERE id = '$id'";

        return $this->ejecutar_sentencia();
    }

    // Método para listar todas las asignaturas
    public function listarAsignatura(){


        $this->sentencia = "SELECT * FROM asignatura";

        return $this->obtener_sentencia();
    }

    // 🔹 MÉTODO AGREGADO (NO MODIFICA NADA MÁS)
    public function obtenerAsignaturaPorId($id){

        if (!is_numeric($id)) {
            return false;
        }

        $this->sentencia = "SELECT * FROM asignatura WHERE id = '$id'";

        return $this->obtener_sentencia();
    }
}
?>