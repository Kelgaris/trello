<?php

require '../vendor/autoload.php';

$nombreBBDD = "Trello";
$nombreColeccion = "Tarjetas";

header("Content-Type: application/json");

try {
    // Verifica si el ID está presente en la solicitud
    if (!isset($_POST["id"])) {
        echo json_encode(['success' => false, 'message' => 'El ID no fue proporcionado.']);
        http_response_code(400);
        exit;
    }

    // Asigna el valor del ID recibido en la solicitud a la variable $id
    $id = trim($_POST["id"]);
    $objectId = new MongoDB\BSON\ObjectId($id);
  

    // Verifica si el ID no está vacío
    if (empty($objectId)) {
        echo json_encode(['success' => false, 'message' => 'El ID proporcionado es vacío.']);
        http_response_code(400);
        exit;
    }

    // Conexión a MongoDB
    $uri = 'mongodb+srv://davidpp:abc123.@cluster0.4mh62.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
    $cliente = new MongoDB\Client($uri);

    $db = $cliente->selectDatabase($nombreBBDD);
    $colection = $db->selectCollection($nombreColeccion);

    // Buscar la tarjeta por ID
    $tarjeta = $colection->findOne(["_id" => $objectId]);

    if (!$tarjeta) {
        echo json_encode(['success' => false, 'message' => 'Tarjeta no encontrada.']);
        http_response_code(404); // Not Found
        exit;
    }

    // Eliminar la tarjeta por ID
    $resultado = $colection->deleteOne(["_id" => $objectId]);

    if ($resultado->getDeletedCount() === 1) {
        echo json_encode(['success' => true, 'message' => 'Tarjeta eliminada con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la tarjeta.']);
        http_response_code(500); // Internal Server Error
    }
} catch (Exception $err) {
    // Log error for debugging purposes
    error_log('MongoDB Connection Error: ' . $err->getMessage());

    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Error al conectar con MongoDB: ' . $err->getMessage()]);
    exit;
}

?>
