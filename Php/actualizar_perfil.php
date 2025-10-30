<?php
session_start();
require_once __DIR__ . '/../db_conexion.php';

// Seguridad: verificar que el usuario esté logueado y que los datos lleguen por POST
if (!isset($_SESSION['usr_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$usr_id = $_SESSION['usr_id'];

// Recoger y sanear los datos del formulario
$form_data = [
    'nombre'   => trim($_POST['nombre'] ?? ''),
    'email'    => trim($_POST['email'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'calle'    => trim($_POST['calle'] ?? ''),
    'colonia'  => trim($_POST['colonia'] ?? ''),
    'ciudad'   => trim($_POST['ciudad'] ?? ''),
    'estado'   => trim($_POST['estado'] ?? ''),
    'cp'       => trim($_POST['cp'] ?? '')
];

// Validar que ningún campo esté vacío (puedes añadir más validaciones)
$error = false;
foreach ($form_data as $campo) {
    if (empty($campo)) {
        $error = true;
        break;
    }
}

if ($error) {
    $_SESSION['resultado_perfil'] = "Error: Todos los campos son obligatorios.";
} else {
    // Preparar la consulta UPDATE
    $sql = "UPDATE users SET 
                usr_nombre_completo = ?, usr_email = ?, usr_telefono = ?, 
                usr_calle = ?, usr_colonia = ?, usr_ciudad = ?, 
                usr_estado = ?, usr_cp = ?
            WHERE usr_id = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssi", 
        $form_data['nombre'], $form_data['email'], $form_data['telefono'],
        $form_data['calle'], $form_data['colonia'], $form_data['ciudad'],
        $form_data['estado'], $form_data['cp'], $usr_id
    );

    if ($stmt->execute()) {
        $_SESSION['resultado_perfil'] = "¡Tus datos se han actualizado correctamente!";
    } else {
        $_SESSION['resultado_perfil'] = "Error al actualizar los datos. Inténtalo de nuevo.";
    }
    $stmt->close();
}

$conexion->close();
header('Location: ../perfil.php');
exit();
?>