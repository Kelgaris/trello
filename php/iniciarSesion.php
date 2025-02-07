<?php
    
    session_start();

    require("../vendor/autoload.php");

    $usuario = $_POST["usuario"];
    $password = $_POST["password"];


    $nombreBBDD = "Trello";
    $nombreColeccion = "Usuarios";

    try{
    
        $uri = 'mongodb+srv://davidpp:abc123.@cluster0.4mh62.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
        $cliente = new MongoDB\Client($uri);

        $db = $cliente->selectDatabase($nombreBBDD);
        $colection = $db->selectCollection($nombreColeccion);

        $usuarioEncontrado = $colection->findOne(["nombre" => $usuario]);

        if($usuarioEncontrado){


            if($password === $usuarioEncontrado["password"]){

                $_SESSION["usuario"] = $usuario;
                $usuarioRol =   $usuarioEncontrado["rol"] ;
                $_SESSION["rol"] = $usuarioRol;

                header("Location: ../principal.html");
                exit;
            }

        }else{
            header("Location: ../login.html");
            exit;
        }

    } catch (Exception $error){

        http_response_code(500);
        echo json_encode(['error' => 'Error al conectar con MongoDB: ' . $error->getMessage()]);
        exit;
    }
?>