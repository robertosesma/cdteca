<?php
session_start();
include 'func_aux.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username']) && ($_SERVER["REQUEST_METHOD"] == "POST")) {

    if (isset($_POST["afegir"])) {
        // añadir un artista que ya existe
        $conn = connect();
        $id = clear_input($_POST['id']);
        $artista = clear_input($_POST['artista']);

        $stmt = $conn -> prepare('SELECT * FROM autores WHERE id = ? AND autor = ?');
        $stmt->bind_param('ii', $id, $artista);
        $stmt->execute();
        $d = $stmt->get_result();
        if ($d->num_rows==0) {
            // si el autor no se ha añadido, añadirlo
            $stmt = $conn -> prepare("INSERT INTO autores (id,autor) VALUES (?,?)");
            $stmt->bind_param('ii',$id,$artista);
            $stmt->execute();
        } else {
            // si el autor ya se ha añadido, no hacer nada
        }
        $d->free();
        $conn->close();
        header("Refresh:0; url=artistas.php?id=".$id);
    }

    if (isset($_POST["nuevo"])) {
        // añadir un artista nuevo: pedir los datos
        $id = clear_input($_POST['id']);
        header("Location: new_artista.php?id=".$id);
    }
} else {
    header("Location: logout.php");
}
?>
