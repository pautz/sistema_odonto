<?php
session_start();

// Conectar ao banco de dados
$cx = new mysqli("127.0.0.1", "username", "password", "dbname");
if ($cx->connect_error) {
    die("Erro na conexão: " . $cx->connect_error);
}

// Obtendo parâmetros via GET
$transacao_hash = isset($_GET['transacao_hash']) ? trim($_GET['transacao_hash']) : '';
$consultorio_id = isset($_GET['consultorio_id']) ? trim($_GET['consultorio_id']) : '';
$data_reserva = isset($_GET['data_reserva']) ? trim($_GET['data_reserva']) : '';
$documento = isset($_GET['documento']) ? trim($_GET['documento']) : ''; // Novo campo de pesquisa

// Parâmetros de paginação
$por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $por_pagina;

// Criar consulta dinâmica para incluir voo_ok
$query = "
    SELECT r.consultorio_id, v.consulta, v.preco, r.numero_dentista, r.data_reserva, r.transacao_hash, r.eq_user,
           r.embarcado, r.data_embarque, i.documento, r.voo_ok
    FROM reserva_dentista r
    JOIN consultorio v ON r.consultorio_id = v.id
    LEFT JOIN identificacao i ON r.eq_user = i.username
    WHERE 1=1
";


$params = [];
$types = "";

if (!empty($transacao_hash)) {
    $query .= " AND r.transacao_hash = ?";
    $params[] = $transacao_hash;
    $types .= "s";
}
if (!empty($consultorio_id)) {
    $query .= " AND r.consultorio_id = ?";
    $params[] = $consultorio_id;
    $types .= "i";
}
if (!empty($data_reserva)) {
    $query .= " AND r.data_reserva = ?";
    $params[] = $data_reserva;
    $types .= "s";
}
if (!empty($documento)) {
    $query .= " AND i.documento = ?";
    $params[] = $documento;
    $types .= "s";
}
$numero_dentista = isset($_GET['numero_dentista']) ? trim($_GET['numero_dentista']) : '';

if (!empty($numero_dentista)) {
    $query .= " AND r.numero_dentista = ?";
    $params[] = $numero_dentista;
    $types .= "s";
}

$query .= " LIMIT ? OFFSET ?";
$params[] = $por_pagina;
$types .= "ii";
$params[] = $offset;

// Preparar e executar consulta
$stmt = $cx->prepare($query);
if (!$stmt) die("Erro na consulta: " . $cx->error);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Armazenar os resultados
$reservas = [];
while ($row = $result->fetch_assoc()) {
    $reservas[] = $row;
}

$stmt->close();
$cx->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consultar Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center">Buscar Reservas</h2>

    <form method="GET" class="text-center">
        <div class="mb-3">
            <label for="documento" class="form-label">Documento de Identidade:</label>
            <input type="text" id="documento" name="documento" class="form-control w-50 mx-auto"
                   value="<?= htmlspecialchars($_GET['documento'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="transacao_hash" class="form-label">Transação Hash:</label>
            <input type="text" id="transacao_hash" name="transacao_hash" class="form-control w-50 mx-auto"
                   value="<?= htmlspecialchars($_GET['transacao_hash'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="consultorio_id" class="form-label">Voo ID:</label>
            <input type="number" id="consultorio_id" name="consultorio_id" class="form-control w-50 mx-auto"
                   value="<?= htmlspecialchars($_GET['consultorio_id'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="data_reserva" class="form-label">Data da Reserva:</label>
            <input type="date" id="data_reserva" name="data_reserva" class="form-control w-50 mx-auto"
                   value="<?= htmlspecialchars($_GET['data_reserva'] ?? '') ?>">
        </div>
        <div class="mb-3">
    <label for="numero_dentista" class="form-label">Número do Assento:</label>
    <input type="text" id="numero_dentista" name="numero_dentista" class="form-control w-50 mx-auto"
           value="<?= htmlspecialchars($_GET['numero_dentista'] ?? '') ?>">
</div>

        <input type="hidden" name="pagina" value="1">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <?php if (!empty($reservas)): ?>
        <div class="mt-4">
            <h3 class="text-center">Detalhes das Reservas</h3>
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Usuário</th>
                        <th>Documento</th>
                        <th>Voo ID</th>
                        <th>consulta</th>
                        <th>Preço (BNB)</th>
                        <th>Assento</th>
                        <th>Data</th>
                        <th>Transação</th>
                        <th>Passagem</th>
                        <th>Data do Embarque</th>
                        <th>Embarque?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['eq_user']) ?></td>
                            <td><?= htmlspecialchars($reserva['documento'] ?? 'Não cadastrado') ?></td>
                            <td><?= htmlspecialchars($reserva['consultorio_id']) ?></td>
                            <td><?= htmlspecialchars($reserva['consulta']) ?></td>
                            <td><?= number_format($reserva['preco'], 8, ',', '.') ?></td>
                            <td><?= htmlspecialchars($reserva['numero_dentista']) ?></td>
                            <td><?= htmlspecialchars($reserva['data_reserva']) ?></td>
                            <td><?= htmlspecialchars($reserva['transacao_hash']) ?></td>
                            <td><?= $reserva['embarcado'] ? '<span class="text-success">Sim</span>' : '<span class="text-danger">Não</span>' ?></td>
                            <td><?= $reserva['data_embarque'] ? htmlspecialchars($reserva['data_embarque']) : '—' ?></td>
                            <td><?= $reserva['voo_ok'] ? '<span class="text-success">Sim</span>' : '<span class="text-danger">Não</span>' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center text-danger mt-4">Nenhuma reserva encontrada.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
