<?php
function connect(){
    require '../dbconfig.php';

    $con = mysqli_connect($dbconfig['server'],$dbconfig['username'],$dbconfig['password'],$dbconfig['db']);
    if(!$con){
        die("Fallo al conectar con la base de datos");
    }
    $con->query("SET NAMES 'utf8'");
    $con->query("SET CHARACTER SET utf8");
    $con->query("SET SESSION collation_connection = 'utf8_unicode_ci'");

    return $con;
}

function clear_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getnextcd($conn){
    $stmt = $conn -> prepare("SELECT MAX(id) AS max FROM ficha");
    $stmt->execute();
    $max = $stmt->get_result();
    $r = $max->fetch_assoc();
    $next = $r["max"]+1;
    return $next;
}

function getnextautor($conn){
    $stmt = $conn -> prepare("SELECT MAX(autor) AS max FROM dautores");
    $stmt->execute();
    $max = $stmt->get_result();
    $r = $max->fetch_assoc();
    $next = $r["max"]+1;
    return $next;
}

?>
