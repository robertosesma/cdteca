<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Nuevo artista</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>

<body>
<?php
include 'func_aux.php';
$ok = true;
if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && isset($_SESSION['username']))
    && (isset($_GET['id'])  || $_SERVER["REQUEST_METHOD"] == "POST")) {
    $conn = connect();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cod = getnextautor($conn);
        $id = clear_input($_POST["id"]);
        $nom = clear_input($_POST["nom"]);
        $ape = clear_input($_POST["ape"]);

        // añadir el artista al diccionario de autores
        $stmt = $conn -> prepare("INSERT INTO dautores (autor,nom,ape) VALUES (?,?,?)");
        $stmt->bind_param('iss',$cod,$nom,$ape);
        $stmt->execute();
        // añadir el artista al CD
        $stmt = $conn -> prepare("INSERT INTO autores (id,autor) VALUES (?,?)");
        $stmt->bind_param('ii',$id,$cod);
        $stmt->execute();

        header("Refresh:0; url=artistas.php?id=".$id);
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = clear_input($_GET["id"]);
    }
} else {
    $ok = false;
} ?>

<?php if ($ok) { ?>
<div class="container">
    <div class="page-header">
        <h2>Nuevo artista</h2>
        <?php echo '<a class="btn btn-link" href="artistas.php?id='.$id.'">Atrás</a>'; ?>
        <a class="btn btn-link" href="logout.php">Salir</a>
    </div>
</div>

<div class="container p-3 my-3 border">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="nom">Nombre:</label>
            <input type="text" class="form-control" name="nom">
        </div>
        <div class="form-group">
            <label for="ape">Apellidos / Grupo:</label>
            <input type="text" class="form-control" name="ape" required>
        </div>
        <input type="text" class="form-control" hidden="true" name="id" value="<?php echo $id; ?>">
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>

<?php
    $conn->close();
} else {
    header("Location: logout.php");
}?>

</body>
</html>
