<?php
// php/validar_datos.php
session_start();

// Verificar que se envió el formulario por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../html/comprar.php');
    exit();
}

// 1. Recoger y sanear los datos del formulario
$form_data = [
    'nombre'   => trim($_POST['nombre'] ?? ''),
    'email'    => trim($_POST['email'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'calle'    => trim($_POST['calle'] ?? ''),
    'colonia'  => trim($_POST['colonia'] ?? ''),
    'cp'       => trim($_POST['cp'] ?? ''),
    'ciudad'   => trim($_POST['ciudad'] ?? ''),
    'estado'   => trim($_POST['estado'] ?? '')
];

// 2. Validar que ningún campo esté vacío
$campos_vacios = false;
foreach ($form_data as $campo) {
    if (empty($campo)) {
        $campos_vacios = true;
        break;
    }
}

// 3. Tomar una decisión
if ($campos_vacios) {
    // Si hay errores, guardar datos y error en sesión y redirigir de vuelta
    $_SESSION['error_datos'] = "Por favor, completa todos los campos requeridos.";
    $_SESSION['form_data'] = $form_data;
    header('Location: ../html/comprar.php');
    exit();
} else {
    // Si todo está bien, guardar los datos en sesión y redirigir a la página de pago
    $_SESSION['datos_cliente'] = $form_data;
    header('Location: ../html/seleccionar_pago.php');
    exit();
}
?>