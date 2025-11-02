<?php
session_start();
require 'db_conexion.php';
require 'fpdf/fpdf.php';

//Verificar que el usuario esté logueado y que se haya pasado un ID de pedido
if (!isset($_GET['id'])) {
    die("Error: No se ha especificado un ID de pedido.");
}

$ord_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$ord_id) {
    die("Error: ID de pedido no válido.");
}

//Obtener los datos del pedido y del cliente

$sql_order = "SELECT * FROM orders WHERE ord_id = ?";
$stmt_order = $conexion->prepare($sql_order);
$stmt_order->bind_param("i", $ord_id);
$stmt_order->execute();
$pedido = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

if (!$pedido) {
    die("No se encontró el pedido.");
}
// Verificación de seguridad para usuarios registrados
if (isset($_SESSION['usr_id']) && $pedido['usr_id'] !== null && $pedido['usr_id'] != $_SESSION['usr_id']) {
    die("No tienes permiso para ver este recibo.");
}

$sql_details = "SELECT p.prod_name, od.od_amount, p.prod_price 
                FROM order_details od
                JOIN products p ON od.prod_id = p.prod_id
                WHERE od.ord_id = ?";
$stmt_details = $conexion->prepare($sql_details);
$stmt_details->bind_param("i", $ord_id);
$stmt_details->execute();
$detalles_pedido = $stmt_details->get_result();

class PDF extends FPDF {
    // Cabecera de página
    function Header() {
        $this->Image('../img/logo.png', 10, 8, 33);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Recibo de Compra - Bit&Byte', 0, 0, 'C');
        $this->Ln(20);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, @utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Creación del objeto PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

//Información del Pedido
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, @utf8_decode('Número de Pedido:'), 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, '#' . $pedido['ord_id'], 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Fecha de Compra:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, date("d/m/Y H:i", strtotime($pedido['ord_date'])), 0, 1);
$pdf->Ln(10);

//Información del Cliente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Datos del Cliente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 7, @utf8_decode('Nombre: ' . $pedido['ord_customer_name']), 0, 1);
$pdf->Cell(0, 7, @utf8_decode('Correo: ' . $pedido['ord_customer_email']), 0, 1);
$pdf->Cell(0, 7, @utf8_decode('Teléfono: ' . $pedido['ord_customer_phone']), 0, 1);
$pdf->Cell(0, 7, @utf8_decode('Dirección de Envío: ' . $pedido['ord_shipping_address']), 0, 1);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(95, 10, 'Producto', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Precio Unit.', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Subtotal', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$gran_total = 0;
while ($item = $detalles_pedido->fetch_assoc()) {
    $subtotal = $item['od_amount'] * $item['prod_price'];
    $gran_total += $subtotal;
    
    $pdf->Cell(95, 10, @utf8_decode($item['prod_name']), 1, 0);
    $pdf->Cell(30, 10, $item['od_amount'], 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($item['prod_price'], 2), 1, 0, 'R');
    $pdf->Cell(35, 10, '$' . number_format($subtotal, 2), 1, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(155, 10, 'Total:', 0, 0, 'R');
$pdf->Cell(35, 10, '$' . number_format($gran_total, 2), 1, 1, 'R');

$pdf->Output('D', 'recibo_orden_' . $ord_id . '.pdf');

$conexion->close();
?>