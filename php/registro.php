<?php

    require("../vendor/autoload.php");

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $usuario = trim($_POST["usuario"]);
        $password = trim($_POST["password"]);
        $password_repetida = trim($_POST["passwordrepetir"]);

        $nombreBBDD = "Trello";
        $nombreColeccion = "Usuarios";

        try{

            $uri = 'mongodb+srv://davidpp:abc123.@cluster0.4mh62.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
            $cliente = new MongoDB\Client($uri);
    
            $db = $cliente->selectDatabase($nombreBBDD);
            $colection = $db->selectCollection($nombreColeccion);
       
            $usuarioExistente = $colection ->findOne(["nombre" => $usuario]);

            if($usuarioExistente){
                echo "<script>alert('⚠️ El usuario ya existe. Elige otro nombre'); window.location.href='../registro.html';</script>";
            }else{

                $nuevoUsuario = [
                    "nombre"=>$usuario,
                    "password"=>$password,
                    "rol"=>"usuario"
                ];

                $resultado = $colection->insertOne($nuevoUsuario);

                if($resultado->getInsertedCount() > 0){
                    echo "<script>alert('✅ Registro exitoso'); window.location.href='../login.html';</script>";
                }else{
                    echo "<script>alert('❌ Error al registrar'); window.location.href='../registro.html';</script>";
                }
            }
    
        } catch (Exception $error){
    
            http_response_code(500);
            echo json_encode(['error' => 'Error al conectar con MongoDB: ' . $error->getMessage()]);
            exit;
        }
    }

?>