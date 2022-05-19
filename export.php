<?php
session_start();

$dir = dirname(dirname(__FILE__));
require $dir.'/lib/PHPRtfLite.php';
include 'func_aux.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true
    && isset($_SESSION['username'])) {
    $conn = connect();

    $titulo = $_SESSION["titulo"];
    $artista = $_SESSION["artista"];
    $tipo = $_SESSION["tipo"];

    // obtener resultado de la búsqueda
    $stmt = $conn -> prepare("SELECT * FROM listado WHERE title LIKE ? AND autores LIKE ? AND type LIKE ?");
    $stmt->bind_param('sss', $titulo, $artista, $tipo);
    $stmt->execute();
    $d = $stmt->get_result();
    $nrows = $d->num_rows;

    if ($nrows>0) {
        PHPRtfLite::registerAutoloader();       // register PHPRtfLite class loader
        $rtf = new PHPRtfLite();
        $rtf->setMargins(1, 2, 1, 2);

        $sect = $rtf->addSection();

        $sect->writeText('<b>Listado de CDs</b>',
                    new PHPRtfLite_Font(14, 'Arial'), new PHPRtfLite_ParFormat());
        $sect->writeText('Número de registros: '.$nrows,
                    new PHPRtfLite_Font(12, 'Arial'), new PHPRtfLite_ParFormat());
        $sect->writeText(' ', new PHPRtfLite_Font(12, 'Arial'), new PHPRtfLite_ParFormat());

        $font = new PHPRtfLite_Font(9, 'Arial');
        $border = new PHPRtfLite_Border($rtf);
        $border->setBorderTop(new PHPRtfLite_Border_Format(1, '#000000'));
        $border->setBorderBottom(new PHPRtfLite_Border_Format(1, '#000000'));
        $borderBottom = new PHPRtfLite_Border($rtf);
        $borderBottom->setBorderBottom(new PHPRtfLite_Border_Format(1, '#000000'));

        // crear tabla
        $table = $sect->addTable();
        // capçalera
        $row = 1;
        $table->addRows(1, 0.7);
        $table->addColumnsList(array(0.7,5,5,1,5,1,1));
        $cell = $table->getCell($row, 2);
        $cell->writeText("<b>Título</b>",$font);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        $cell = $table->getCell($row, 3);
        $cell->writeText("<b>Artista(s)</b>",$font);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        $cell = $table->getCell($row, 4);
        $cell->writeText("<b>Año</b>",$font);
        $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        $cell = $table->getCell($row, 5);
        $cell->writeText("<b>Tipo</b>",$font);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        $cell = $table->getCell($row, 6);
        $cell->writeText("<b>Orig.</b>",$font);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        $cell = $table->getCell($row, 7);
        $cell->writeText("<b>Dura.</b>",$font);
        $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
        // marges
        $table->setBorderForCellRange($border, 1, 1, 1, 7);

        $row++;
        while ($i = mysqli_fetch_array($d)) {
            $table->addRows(1, 0.8);
            $cell = $table->getCell($row, 1);
            $n = $row-1;
            $cell->writeText("<b>".$n."</b>",$font);
            $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
            $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            $cell = $table->getCell($row, 2);
            $cell->writeText($i["title"],$font);
            $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            $cell = $table->getCell($row, 3);
            $cell->writeText($i["autores"],$font);
            $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            $cell = $table->getCell($row, 4);
            $cell->writeText($i["year"],$font);
            $cell->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
            $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            $cell = $table->getCell($row, 5);
            $cell->writeText($i["type"],$font);
            $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            $cell = $table->getCell($row, 6);
            $cell->writeText($i["original"],$font);
            $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            $cell = $table->getCell($row, 7);
            $cell->writeText($i["duration"],$font);
            $cell->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            // marges
            $table->setBorderForCellRange($borderBottom, $row, 1, $row, 7);

            $row++;
        }

        // descarregar el fitxer rtf
        header('Content-Type: application/vnd.ms-word');
        header('Content-Disposition: attachment;filename="listado.rtf"');
        header('Cache-Control: max-age=0');
        $rtf->save('php://output');
    }
    $d->free();
    $conn->close();
} else {
header("Location: logout.php");
}
?>
