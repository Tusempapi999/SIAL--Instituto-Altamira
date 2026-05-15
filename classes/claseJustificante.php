<?php
class justificante {
    public $sentencia;
    public $conexion;
    private $alumno_id;
    private $fecha;
    private $fecha_fin;
    private $motivo;

    public function __construct($conexion, $alumno_id, $fecha, $fecha_fin, $motivo) {
        $this->conexion = $conexion;
        $this->alumno_id = $alumno_id;
        $this->fecha = $fecha;
        $this->fecha_fin = $fecha_fin;
        $this->motivo = $motivo;
    }

    // funcion para comprovar si el alumno existe dentro de la base de datos (VALIDACION)
    private function alumnoExiste() {
        $sql = "SELECT id FROM alumno WHERE id = ?";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("i", $this->alumno_id);
        $sentencia->execute();
        $resultado = $sentencia->get_result();

        return $resultado->num_rows > 0;
    }

    // funcion para crear un nuevo justificante con los datos requeridos (ALTA)
    public function crear() {
        if (!$this->alumnoExiste()) {
            return "no_existe";
        }

        $validacion = $this->fechasValidas();

        if ($validacion == "formato_invalido") {
            return "fecha_invalida";
        }

        if ($validacion == "rango_invalido") {
            return "rango_invalido";
        }

        $this->sentencia = "INSERT INTO justificante (alumno_id, fecha, fecha_fin, motivo) VALUES (?, ?, ?, ?)";        //sentencia SQL para insertar informacion a la base de datos

        $consulta = $this->conexion->prepare($this->sentencia);
        $consulta->bind_param("isss", $this->alumno_id, $this->fecha, $this->fecha_fin, $this->motivo);
        //El método bind_param() se utiliza para vincular variables a una sentencia SQL preparada.
        //Los signos ? representan espacios donde se insertarán los datos.
        //Las letras como s e i indican el tipo de dato que se enviará a la base de datos.

        if ($consulta->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    private function fechasValidas() {          // validar al formato YYYY-MM-DD (VALIDACION)
        $fecha = DateTime::createFromFormat('Y-m-d', $this->fecha);
        $fecha_fin = DateTime::createFromFormat('Y-m-d', $this->fecha_fin);

        if (!$fecha || !$fecha_fin) {           // Este if verifica que esten en el formato requerido
            return "formato_invalido";
        }

        if ($fecha_fin < $fecha) {              // validar que fecha_fin no sea menor
            return "rango_invalido";
        }

        return "ok";
    }

    public function ModificarJustificante($id, $fecha, $fecha_fin, $motivo, $estado) {
        if (strtotime($fecha_fin) < strtotime($fecha)) { // Este if verifica que la fecha_fin sea menor que fecha, asi evitamos incoherencias en el justificante
            return "rango_invalido";
        }
        $sql = "UPDATE justificante SET fecha = ?, fecha_fin = ?, motivo = ?, estado = ? WHERE id = ?"; // Sentencia SQL para actualizar los datos requeridos 
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("ssssi", $fecha, $fecha_fin, $motivo, $estado, $id);
        return $sentencia->execute();
        //El método bind_param() se utiliza para vincular variables a una sentencia SQL preparada.
        //Los signos ? representan espacios donde se insertarán los datos.
        //Las letras como s e i indican el tipo de dato que se enviará a la base de datos.
    }

    public function EstadoJustificante($estado) {
        $sql = "SELECT * FROM justificante WHERE estado = ?";
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bind_param("s", $estado);
        $sentencia->execute();
        return $sentencia->get_result();
    }

    public function getAlumnoId() {             //funcion para obtener el id del alumno
        return $this->alumno_id;
    }

    public function getFecha() {                // funcion para obtener la fecha deinicio del justificante
        return $this->fecha;
    }

    public function getFechaFin() {             // funcion para obtener la fecha final donde se aplicara el justificante
        return $this->fecha_fin;
    } 

    public function getMotivo() {               // funcion para obtener el motivo del justificante
        return $this->motivo;
    }
}
?>