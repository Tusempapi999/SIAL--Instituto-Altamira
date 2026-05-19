<?php
session_start();

include('clases/claseUser.php');
$user = new User();

if (isset($_POST['matricula']) && isset($_POST['pwd'])) {

    $matricula = $_POST['matricula'];
    $pwd = $_POST['pwd'];

    $resultado = $user->iniciar_sesion($matricula, $pwd);

    if ($resultado->num_rows > 0) {

        $datos = $resultado->fetch_assoc();

        $_SESSION['id'] = $datos['id'];
        $_SESSION['rol'] = $datos['rol'];

        // REDIRECCIÓN SEGÚN ROL
        if ($datos['rol'] == 'admin') {
            header("Location: panelAdmin.php");
            exit;
        }

        if ($datos['rol'] == 'alumno') {
            header("Location: panelAlumno.php");
            exit;
        }

        if ($datos['rol'] == 'profesor') {
            header("Location: panelProfesor.php");
            exit;
        }

    } else {
        // 🔴 AQUÍ SE ACTIVA EL ERROR PARA TU LOGIN
        header("Location: inicio.php?error=1");
        exit;
    }

} else {
    header("Location: inicio.php?error=1");
    exit;
}
?>