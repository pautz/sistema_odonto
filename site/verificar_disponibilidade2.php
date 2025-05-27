<?php
session_start();
$cx = new mysqli("127.0.0.1", "username", "password", "dbname");

if ($cx->connect_error) {
    die("Erro na conexão com o banco: " . $cx->connect_error);
}

$consultorio_id = isset($_POST['consultorio_id']) ? intval($_POST['consultorio_id']) : null;
$assento = isset($_POST['assento']) ? intval($_POST['assento']) : null;
$data_reserva = isset($_POST['data_reserva']) ? $_POST['data_reserva'] : null;

if (!$consultorio_id || !$assento || !$data_reserva) {
    echo "Erro: Informações insuficientes.";
    exit;
}

// **Verificar se aquele assento está ocupado na data específica**
$stmt = $cx->prepare("SELECT COUNT(*) FROM reserva_dentista WHERE consultorio_id = ? AND numero_dentista = ? AND data_reserva = ? AND pago = 1 AND transacao_hash IS NOT NULL");
$stmt->bind_param("iis", $consultorio_id, $assento, $data_reserva);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    echo "ocupado";
} else {
    echo "disponivel";
}

$cx->close();
?>
