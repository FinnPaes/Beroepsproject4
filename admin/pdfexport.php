<?php
// Hier volgt het maken van een PDF met FPDF.
require("includes/fpdf.php");
require("includes/database.php");


if(isset($_REQUEST["pdf"])) { // Luistert naar pdf GET request in URL
    $GETclaimID = intval($_GET["pdf"]); // De ID van de claim die ge-exporteert moet worden naar PDF.
    
    // Properties
    $datum = date("d-m-Y"); // Datum van dit moment
    
    
    try {
        $stmt = $connect->prepare("SELECT * FROM claims WHERE claimID = :claimid ORDER BY claimID DESC");
        $stmt->execute(array(
            ":claimid" => $GETclaimID
        ));
        $fetchClaims = $stmt->fetch(PDO::FETCH_OBJ);
        $claimsTeller = $stmt->rowCount();
    } catch(PDOException $e) {
        $errorMessage = $e->getMessage();
    }

    $pdf = new FPDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->MultiCell(0,10,'PDF Uitdraai', 0, 'C');
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,10, $datum, 0, "C");
    $pdf->MultiCell(0,10, "Claim ID: $fetchClaims->claimID");
    $pdf->MultiCell(0,10, "Naam: $fetchClaims->naam");
    $pdf->MultiCell(0,10, "Woonplaats: $fetchClaims->woonplaats");
    $pdf->MultiCell(0,10, "Straat: $fetchClaims->straat");
    $pdf->MultiCell(0,10, "Huisnummer: $fetchClaims->huisnummer");
    $pdf->MultiCell(0,10, "Postcode: $fetchClaims->postcode");
    $pdf->MultiCell(0,10, "Telefoonnummer: $fetchClaims->telefoonnummer");
    $pdf->MultiCell(0,10, "Titel: $fetchClaims->titel");
    $pdf->MultiCell(0,10, "");
    $pdf->MultiCell(0,10, "Beschrijving: $fetchClaims->beschrijving");
    $pdf->MultiCell(0,10, "");
    $pdf->MultiCell(0,10, "");
    $pdf->MultiCell(0,10, "");
    $pdf->MultiCell(0,10, "");
    $pdf->MultiCell(0,10, "Indien Datum: $fetchClaims->datum");
    $pdf->MultiCell(0,10, "Status: $fetchClaims->status");
    $pdf->Output();

} else {
    header("Location: panel.php");
}

?>