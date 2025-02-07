<?php
    session_start();

    //Verificamos si el usuario está autentificado.
    if (isset($_SESSION['usuario'])) {
        // Devolvemos los datos de la sesion esto me ha ayudado a ver si realmente se guardaba o no.
        echo json_encode([
            'success' => true,
            'usuario' => $_SESSION['usuario'], //Datos del usuario
            'rol' => $_SESSION['rol'] //Rol del usuario
        ]);
    } else {
        //No tenemos sesion activa.
        echo json_encode([
            'success' => false,
            'message' => 'No hay sesión activa'
        ]);
    }
?>