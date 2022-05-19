<?php
session_start();
include 'func_aux.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username']) && isset($_POST['subir'])
    && isset($_POST['idportada'])) {
    $id = clear_input($_POST['idportada']);

    $ext = strtolower(pathinfo("tmp/".basename($_FILES["portada"]["name"]),PATHINFO_EXTENSION));
    // Check if image file is an actual image or fake image
    if(getimagesize($_FILES["portada"]["tmp_name"]) !== false) {
        // la imagen se copia en la carpeta portadas y su nombre es el id del CD
        $upload_img = '../portadas/'.$id.".".$ext;
        // copiar la imagen temporal a la carpeta portadas definitiva
        if (move_uploaded_file($_FILES["portada"]["tmp_name"], $upload_img)) {
            // crear el thumbnail
            $thumb_img = '../portadas/thumb/'.$id.".".$ext;
            list($width,$height) = getimagesize($upload_img);
            $thumb_create = imagecreatetruecolor(50,50);
            switch($ext){
            case 'jpg' || 'jpeg':
                $source = imagecreatefromjpeg($upload_img);
                break;
            case 'png':
                $source = imagecreatefrompng($upload_img);
                break;
            default:
                $source = imagecreatefromjpeg($upload_img);
            }
            imagecopyresized($thumb_create,$source,0,0,0,0,50,50,$width,$height);
            switch($ext){
            case 'jpg' || 'jpeg':
                imagejpeg($thumb_create,$thumb_img,100);
                break;
            case 'png':
                imagepng($thumb_create,$thumb_img,100);
                break;
            default:
                imagejpeg($thumb_create,$thumb_img,100);
            }
            // una vez copiada la imagen y creado el thumbnail, volver a la ficha de CD
            // Refresh:0 fuerza un refresco de la imagen de la portada
            header("Refresh:0; url=ficha.php?id=".$id);
        } else {
            echo "<h1>Se produjo un error subiendo la imagen<\h1>";
        }
    } else {
        echo "<h1>ERROR: el archivo no es una imagen</h1>";
    }
} else {
    header("Location: logout.php");
}
?>
