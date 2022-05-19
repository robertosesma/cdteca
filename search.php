<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Listado</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>

<body>

<?php
include 'func_aux.php';
$ok = true;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username'])) {
    $conn = connect();
    $titulo = "%".clear_input($_POST["titulo"])."%";
    $artista = "%".clear_input($_POST["artista"])."%";
    $tipo = "%".clear_input($_POST["tipo"])."%";

    $_SESSION["titulo"] = $titulo;
    $_SESSION["artista"] = $artista;
    $_SESSION["tipo"] = $tipo;

    // obtener resultado de la búsqueda
    $stmt = $conn -> prepare("SELECT * FROM listado WHERE title LIKE ? AND autores LIKE ? AND type LIKE ?");
    $stmt->bind_param('sss', $titulo, $artista, $tipo);
    $stmt->execute();
    $d = $stmt->get_result();
    $nrows = $d->num_rows;
} else {
    $ok = false;
}
?>

<?php if ($ok){ ?>
<div class="container">
    <div class="container p-3 my-3 border">
        <h3>Resultado búsqueda</h3>
        <?php echo "<h6>Número de registros: ".$nrows."</h6>";?>
        <a class="btn btn-link" href="export.php?n=1">Exportar</a>
        <a class="btn btn-link" href="listado.php?n=1">Atrás</a>
        <a class="btn btn-link" href="logout.php">Salir</a>
    </div>
</div>

<div class="container">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-hover table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Portada</th>
                <th>Título</th>
                <th>Artista(s)</th>
                <th><div class='text-center'>Año</div></th>
                <th>Tipo</th>
                <th>Orig.</th>
                <th>Dura.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $j = 1;
            while ($r = mysqli_fetch_array($d)) { ?>
                <tr>
                    <td><?php echo "<img src='../portadas/thumb/".$r["id"].".jpg' class='img-rounded' alt=''>"; ?></td>
                    <td><?php echo "<a href='ficha.php?id=".$r["id"]."'>".$r["title"]."</a>"; ?></td>
                    <td><?php echo $r["autores"]; ?></td>
                    <td><?php echo $r["year"]; ?></td>
                    <td><?php echo $r["type"]; ?></td>
                    <td><?php echo $r["original"]; ?></td>
                    <td><?php echo $r["duration"]; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
    location.reload();
    $conn->close(); ?>
<?php } else {
    header("Location: logout.php");
}?>

</body>
</html>
