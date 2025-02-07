<?php
    require '../vendor/autoload.php'; // Asegúrate de tener instalado el paquete de MongoDB con Composer

    $nombreBBDD = "Trello";
    $nombreColeccion = "Tarjetas";

    $uri = 'mongodb+srv://davidpp:abc123.@cluster0.4mh62.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $cliente = new MongoDB\Client($uri);
    $db = $cliente->selectDatabase($nombreBBDD);
    $colection = $db->selectCollection($nombreColeccion);

    session_start(); // Inicia la sesión
    if (!isset($_SESSION["usuario"])) {
        echo json_encode(["success" => false, "message" => "No hay sesión activa"]);
        exit;
    }

    $usuarioActivo = $_SESSION["usuario"]; // Nombre del usuario activo

    // Buscar tarjetas donde el usuario es propietario o colaborador
    $filtro = [
        '$or' => [
            ["propietario" => $usuarioActivo],
            ["colaboradores" => $usuarioActivo]
        ]
    ];

    $tarjetas = $colection->find($filtro);

    $resultados = [];
    foreach ($tarjetas as $tarjeta) {
        $resultados[] = [
            "id" => (string)$tarjeta["_id"], // Convertir ObjectId a string
            "titulo" => $tarjeta["titulo"],
            "propietario" => $tarjeta["propietario"],
            "colaboradores" => $tarjeta["colaboradores"],
            "estado" => $tarjeta["estado"],
            "notas" => $tarjeta["notas"]
        ];
    }

    // Devolver los resultados en formato JSON
    echo json_encode(["success" => true, "tarjetas" => $resultados]);
?>