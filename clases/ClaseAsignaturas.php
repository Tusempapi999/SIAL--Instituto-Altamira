<?php
// Se incluye el archivo de conexión a la base de datos
include('conexion.php');

// Definición de la clase Asignatura
class Asignatura {

    // Atributos privados de la clase
    private $id; //Atributo para almacenar el ID de la asignatura
    private $nombre; //Atributo para almacenar el nombre de la asignatura
    private $descripcion; //Atributo para almacenar la descripción de la asignatura

    // Método para dar de alta una asignatura
    public function altaAsignatura($nombre, $descripcion){

        // Se crea una nueva instancia de conexión a la base de datos
        $conexion = new Conexion();

        // Se define la sentencia SQL para insertar datos
        $conexion->sentencia = "INSERT INTO asignatura (nombre, descripcion)
        VALUES ('$nombre','$descripcion')"; //

        // Se ejecuta la sentencia SQL y se retorna el resultado
        return $conexion->ejecutar_sentencia();
    }

    // Método para eliminar una asignatura por ID
    public function bajaAsignatura($id){

        if (!is_numeric($id)) {
        return false;
        }

        // Se crea conexión a la base de datos
        $conexion = new Conexion();

        // Sentencia SQL para eliminar un registro específico
        $conexion->sentencia = "DELETE FROM asignatura WHERE id = '$id'"; // Se define la sentencia SQL para eliminar el registro con el ID especificado

        // Ejecuta la sentencia y retorna resultado
        return $conexion->ejecutar_sentencia();
    }

    // Método para modificar (UPDATE) una asignatura
    public function modificarAsignatura($id, $nombre, $descripcion){

        // Se crea conexión a la base de datos
        $conexion = new Conexion();

        // Sentencia SQL para actualizar datos del registro
       $conexion->sentencia = "UPDATE asignatura SET nombre = '$nombre', descripcion = '$descripcion'
        WHERE id = '$id'"; // Se define la sentencia SQL para actualizar el registro con el ID especificado

        // Ejecuta la actualización y retorna resultado
        return $conexion->ejecutar_sentencia();
    }

    // Método para listar (SELECT) todas las asignaturas
    public function listarAsignatura(){

        // Se crea conexión a la base de datos
        $conexion = new Conexion();

        // Sentencia SQL para obtener todos los registros
        $conexion->sentencia = "SELECT * FROM asignatura";

        // Ejecuta la consulta y retorna los resultados
        return $conexion->obtener_sentencia();
    }

}
?>