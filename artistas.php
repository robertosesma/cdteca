<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Artistas</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
</head>

<body>
<?php
include 'func_aux.php';
$ok = true;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true &&
    isset($_SESSION['username']) && isset($_GET['id'])) {
    $conn = connect();
    $id = clear_input($_GET["id"]);

    $stmt = $conn -> prepare('SELECT * FROM ficha WHERE id = ?');
    $stmt->bind_param('i',  $id);
    $stmt->execute();
    $d = mysqli_fetch_array($stmt->get_result());
    $title = $d["title"];

    $stmt = $conn -> prepare("SELECT * FROM lista_autores");
    $stmt->execute();
    $autores = $stmt->get_result();

    $stmt = $conn -> prepare('SELECT * FROM nom_autores WHERE id = ? ORDER BY apellido');
    $stmt->bind_param('i',  $id);
    $stmt->execute();
    $d = $stmt->get_result();
} else {
    $ok = false;
} ?>

<?php if ($ok) { ?>
<div class="container">
    <div class="page-header">
        <h2>Artistas</h2>
        <h4><?php echo $title; ?></h4>
        <h6>Identificador: <?php echo $id; ?></h6>

        <?php echo '<a class="btn btn-link" href="ficha.php?id='.$id.'">Atrás</a>'; ?>
        <a class="btn btn-link" href="logout.php">Salir</a>
    </div>
</div>

<div class="container p-3 my-3 border">
    <h4>Añadir artista</h4>
    <form method="post" action="add_artista.php">
        <div class="form-group">
            <label for="artista">Artista:</label>
            <select name="artista" class="custom-select">;
                <option selected></option>;
                <?php while ($a = mysqli_fetch_array($autores)) {
                    echo "<option value=".$a["id"].">".$a["nomcomp"]."</option>";
                }
                $autores->free();?>
            </select>
        </div>
        <input type="text" class="form-control" hidden="true" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="afegir" class="btn btn-primary">Añadir</button>
        <button type="submit" name="nuevo" class="btn btn-success">Nuevo</button>
    </form>
</div>

<div class="container">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-hover table-bordered">
        <tbody>
            <?php
            while ($r = mysqli_fetch_array($d)) { ?>
                <tr>
                    <td><?php echo $r["nomcomp"]; ?></td>
                    <?php $del = 'del_artista.php?id='.$r["id"].'&cod='.$r["cod"];
                    echo "<td><a onClick=\"javascript: return confirm('Seguro que quieres quitar el artista?');\" href='".$del."'>Quitar</a></td>"; ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
    $d->free();
    $conn->close();
} else {
    header("Location: logout.php");
}?>

</body>
</html>
