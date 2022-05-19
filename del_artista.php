<?php
session_start();
include 'func_aux.php';
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && isset($_SESSION['username'])) {
    if (isset($_GET['id']) && isset($_GET['cod'])) {
        $conn = connect();
        $id = clear_input($_GET["id"]);
        $cod = clear_input($_GET["cod"]);

        // quitar el artista
        $stmt = $conn -> prepare("DELETE FROM autores WHERE id=? AND autor=?");
        $stmt->bind_param('ii',$id,$cod);
        $stmt->execute();

        $conn->close();
        header("Refresh:0; url=artistas.php?id=".$id);
    }
}
exit();
?>
