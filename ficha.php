<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Ficha CD</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>
<?php
include 'func_aux.php';
$ok = true;
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && isset($_SESSION['username'])
    && (isset($_GET['id']) || $_SERVER["REQUEST_METHOD"] == "POST")) {
    // Create connection
    $conn = connect();

    if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
        $id = clear_input($_POST["id"]);
        if (isset($_POST["autores"])) {
            // editar los artistas
            header("Location: artistas.php?id=".$id);
        }
        if (isset($_POST["guardar"])) {
            // grabar los cambios
            $title = clear_input($_POST["title"]);
            $tipo = clear_input($_POST["tipo"]);
            $min = clear_input($_POST["min"]);
            $seg = clear_input($_POST["seg"]);
            $year = clear_input($_POST["year"]);
            $orig = clear_input($_POST["orig"]=="activado");
            $orig = ($orig == 1 ? 1 : 0);

            $stmt = $conn -> prepare("UPDATE ficha SET title=?, type=?, min =?, seg=?, year=?, orig=? WHERE id=?");
            $stmt->bind_param('sissiii', $title, $tipo, $min, $seg, $year, $orig, $id);
            $stmt->execute();
            header("Location: listado.php?id=".$id);
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET" ) {
        $new = 0;
        $id = clear_input($_GET["id"]);

        $stmt = $conn -> prepare('SELECT * FROM dtipo');
        $stmt->execute();
        $dtipo = $stmt->get_result();

        if ($id==0) {
            $new = 1;
            // añadir el nuevo registro
            $id = getnextcd($conn);
            $stmt = $conn -> prepare('INSERT INTO ficha (id) VALUES (?)');
            $stmt->bind_param('i',$id);
            $stmt->execute();
            $title = $autores = $type = $min = $seg = $year = '';
            $orig = 0;
        } else {
            $stmt = $conn -> prepare('SELECT * FROM ficha WHERE id = ?');
            $stmt->bind_param('i',  $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows>0) {
                $d = mysqli_fetch_array($result);

                $stmt = $conn -> prepare("SELECT ficha.id AS id,
                        GROUP_CONCAT(nom_autores.nomcomp order by nom_autores.apellido ASC separator ', ') AS autores
                        FROM ficha LEFT JOIN nom_autores ON ficha.id = nom_autores.id
                        WHERE ficha.id = ?
                        GROUP BY ficha.id");
                $stmt->bind_param('i',$id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows>0) {
                    $r = mysqli_fetch_array($result);
                    $autores = $r["autores"];
                }
                $title = $d["title"];
                $type = $d["type"];
                $min = $d["min"];
                $seg = $d["seg"];
                $year = $d["year"];
                $orig = $d["orig"];
            } else {
                $ok = false;
            }
        }
    }
} else {
    $ok = false;
}
?>

<?php if ($ok) { ?>
<div class="container">
    <div class="page-header">
        <?php if ($new==0) {
            echo "<h2>Ficha de CD</h2>";
        } else {
            echo "<h2>Nuevo CD</h2>";
        } ?>
        <h4>Identificador: <?php echo $id; ?></h4>
        <?php echo '<a class="btn btn-link" href="del_cd.php?id='.$id.'">Borrar</a>';
        if ($new!=1) {
            echo '<a class="btn btn-link" href="listado.php?id='.$id.'">Atrás</a>';
        } ?>
        <a class="btn btn-link" href="logout.php">Salir</a>
    </div>

    <form action="upload.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <?php echo "<img src='../portadas/".$id.".jpg' class='img-responsive' width=300 height=300 alt='portada'>"; ?>
            <div class="custom-file mt-2">
                <input type="file" class="custom-file-input" accept=".jpg,.jpeg,.png" name="portada" id="portada">
                <label class="custom-file-label" for="portada">Escoger portada</label>
                <input type="text" class="form-control" hidden="true" name="idportada" value="<?php echo $id; ?>">
                <button class="btn btn-secondary mt-2 mb-5" name="subir" type="submit">Subir</button>
            </div>
        </div>
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="title">Título:</label>
            <textarea class="form-control" rows="1" max-rows="2" id="title" name="title"><?php echo $title ?></textarea>
        </div>
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <span class="input-group-text">Artista(s):</span>
            </div>
            <input type="text" class="form-control" readonly=true value="<?php echo $autores ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-primary" name="autores" type="submit">Editar</button>
            </div>
        </div>
        <div class="form-row mb-2">
            <div class="col">
                <label for="tipo">Tipo:</label>
                <select name="tipo" class="custom-select">
                    <?php while ($t = mysqli_fetch_array($dtipo)) {
                        $selected = ($t["cod"]==$type ? "selected" : "");
                        echo '<option '.$selected.' value="'.$t["cod"].'">'.$t["tipo"].'</option>';
                    } ?>
                </select>
            </div>
            <div class="col">
                <label for="min">Minutos:</label>
                <input type="text" class="form-control" type="number" name="min" value="<?php echo $min ?>">
            </div>
            <div class="col">
                <label for="min">Segundos:</label>
                <input type="text" class="form-control" type="number" name="seg" value="<?php echo $seg ?>">
            </div>
            <div class="col">
                <label for="year">Año:</label>
                <input type="text" class="form-control" type="number" name="year" value="<?php echo $year ?>">
            </div>
        </div>
        <div class="custom-control custom-checkbox">
            <?php $check_orig = ($orig==1 ? "checked" : ""); ?>
            <input type="checkbox" class="custom-control-input" name="orig" id="orig"
                value="activado" <?php echo $check_orig; ?>>
            <label class="custom-control-label" for="orig">Original</label>
        </div>
        <input type="text" class="form-control" hidden="true" name="id" value="<?php echo $id; ?>">
        <button class="btn btn-primary mt-2" name="guardar" type="submit">Guardar</button>
    </form>
</div>

<script>
// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>

<?php
    location.reload();
    $conn->close();
} else {
    header("Location: logout.php");
}?>

</body>
</html>
