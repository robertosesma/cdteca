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
$page = 10;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username'])) {
    $conn = connect();
    $n = clear_input($_GET["n"]);

    // obtener el número total de registros
    $stmt = $conn -> prepare("SELECT * FROM listado");
    $stmt->execute();
    $d = $stmt->get_result();
    $ntot = $d->num_rows;

    // si se pasa id, posicionar en la fila del id
    if (isset($_GET["id"])) {
        $id = clear_input($_GET["id"]);
        $n = 0;
        while ($r = mysqli_fetch_array($d)) {
            if ($id == $r["id"]) {
                break;
            }
            $n = $n + 1;
        }
        if ($n==0) {
            $n = 1;
        }
    }

    // obtener lista de CDs
    if ($n==1) {
        // mostrar los 10 primeros
        $stmt = $conn -> prepare("SELECT * FROM listado LIMIT ?");
        $stmt->bind_param('i', $page);
        $ini = 1;
        $fin = 10;
        $next = 10;
        $prev = 1;
    } else {
        if ($n==0) {
            $n = $ntot - $page;     // mostrar los 10 últimos
        }
        $stmt = $conn -> prepare("SELECT * FROM listado LIMIT ?, ?");
        $stmt->bind_param('ii', $n, $page);
        $ini = $n + 1;
        $fin = $n + $page;
        $next = $fin;
        if ($fin >= $ntot) {
            $next = $ntot - $page;      // el límite superior es el nº total de registros
            $fin = $ntot;
        }
        $prev = $n - $page;
        $prev = ($prev ==0 ? 1 : $prev);
    }
    $stmt->execute();
    $d = $stmt->get_result();
    $ok = ($d->num_rows>0);
} else {
    $ok = false;
}
?>

<?php if ($ok){ ?>
<div class="container">
    <div class="container p-3 my-3 border">
        <h3>Listado de CDs</h3>
        <?php echo "<h6>Registros ".$ini." a ".$fin." de ".$ntot."</h6>";?>
        <a class="btn btn-link" href="listado.php?n=1"><<</a>
        <a class="btn btn-link" <?php echo 'href="listado.php?n='.$prev.'"';?>><</a>
        <a class="btn btn-link" <?php echo 'href="listado.php?n='.$next.'"';?>>></a>
        <a class="btn btn-link" href="listado.php?n=0">>></a><br>
        <a class="btn btn-link" href="ficha.php?id=0">Nuevo</a>
        <a class="btn btn-link" href="logout.php">Salir</a>
    </div>
</div>

<div class="container">
    <form action="search.php" method="post">
        <div class="input-group mt-2 mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Buscar:</span>
            </div>
            <input type="text" class="form-control" name="titulo" placeholder="Título">
            <input type="text" class="form-control" name="artista" placeholder="Artista(s)">
            <input type="text" class="form-control" name="tipo" placeholder="Tipo">
            <div class="input-group-append">
                <button class="btn btn-outline-primary" name="buscar" type="submit">Buscar</button>
            </div>
        </div>
    </form>
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
