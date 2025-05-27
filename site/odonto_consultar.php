<?php
require('fpdf/fpdf.php'); // Biblioteca FPDF para gerar PDFs
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Conectar ao banco de dados
$cx = new mysqli("127.0.0.1", "username", "password", "dbname");
if ($cx->connect_error) {
    die("Erro na conexão: " . $cx->connect_error);
}

// Obtendo usuário logado e filtros
$eq_user = $_SESSION["username"];
$data_filtro = isset($_GET['data_reserva']) ? $_GET['data_reserva'] : '';
$consulta_filtro = isset($_GET['consulta']) ? $_GET['consulta'] : '';
$consultorio_id_filtro = isset($_GET['consultorio_id']) ? $_GET['consultorio_id'] : '';

// Parâmetros de paginação
$por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $por_pagina;

// Criando consulta dinâmica com paginação
$query = "
    SELECT r.consultorio_id, v.consulta, v.preco, r.numero_dentista, r.data_reserva, r.transacao_hash 
    FROM reserva_dentista r
    JOIN consultorio v ON r.consultorio_id = v.id
    WHERE r.eq_user = ?
";

$params = [$eq_user];
$types = "s";

if (!empty($data_filtro)) {
    $query .= " AND r.data_reserva = ?";
    $params[] = $data_filtro;
    $types .= "s";
}
if (!empty($consulta_filtro)) {
    $query .= " AND v.consulta = ?";
    $params[] = $consulta_filtro;
    $types .= "s";
}
if (!empty($consultorio_id_filtro)) {
    $query .= " AND r.consultorio_id = ?";
    $params[] = $consultorio_id_filtro;
    $types .= "s";
}

$query .= " LIMIT ? OFFSET ?";
$params[] = $por_pagina;
$types .= "ii";
$params[] = $offset;

// Preparar e executar consulta
$stmt = $cx->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Contagem total de registros para paginação
$query_total = "SELECT COUNT(*) AS total FROM reserva_dentista WHERE eq_user = ?";
$stmt_total = $cx->prepare($query_total);
$stmt_total->bind_param("s", $eq_user);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_registros = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

$stmt_total->close();
$stmt->close();
$cx->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minhas Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>/* Reset de estilos */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Configuração geral do corpo */
body {
    font-family: 'Roboto', Arial, sans-serif;
    background-color: #f0f0f0;
    padding-top: 80px; /* Ajuste conforme a navbar fixa */
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

/* Partículas no fundo */
#particles-js {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: -1; /* Mantém o fundo atrás do conteúdo */
}

/* Navbar fixa no topo */
.navbar {
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
    padding: 10px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    box-sizing: border-box;
}

/* Ajuste para o header ficar abaixo da navbar */
header {
    margin-top: 0;
    padding-top: 20px;
}

/* Estilo do formulário */
form {
    max-width: 600px;
    width: 100%;
    background: rgba(255, 255, 255, 0.15);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

/* Estilização de labels, inputs e botões */
label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #000;
}

input, select {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    background-color: white;
    color: black;
}

button, .btn {
    background-color: #b0b0b0;
    color: white;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 16px;
    width: 100%;
}

button:hover, .btn:hover {
    background-color: #8c8c8c;
}

/* Estilização da tabela de reservas */
.table {
    background-color: white;
    border-radius: 12px;
    overflow: hidden;
}

.table th {
    background-color: #008080;
    color: white;
    text-align: center;
}

.table td {
    text-align: center;
}

/* Paginação */
.pagination {
    text-align: center;
    margin-top: 20px;
}

.pagination a {
    padding: 8px 15px;
    margin: 5px;
    background: #ff9800;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

/* Responsividade */
@media (max-width: 768px) {
    body {
        font-size: 18px;
        padding: 10px;
    }

    form {
        max-width: 90%;
    }
}
</style>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Suas Reservas</h2>

    <form method="GET" class="mb-4 text-center">
        <label for="data_reserva" class="form-label">Filtrar por Data:</label>
        <input type="date" id="data_reserva" name="data_reserva" class="form-control w-50 mx-auto" value="<?= htmlspecialchars($data_filtro); ?>">

        <label for="consulta" class="form-label mt-3">Filtrar por Consulta:</label>
        <input type="text" id="consulta" name="consulta" class="form-control w-50 mx-auto" value="<?= htmlspecialchars($consulta_filtro); ?>">

        <label for="consultorio_id" class="form-label mt-3">Filtrar por ID:</label>
        <input type="text" id="consultorio_id" name="consultorio_id" class="form-control w-50 mx-auto" value="<?= htmlspecialchars($consultorio_id_filtro); ?>">

        <input type="hidden" name="pagina" value="1"> <!-- Sempre inicia na primeira página -->
        <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Voo ID</th>
                        <th>consulta</th>
                        <th>Preço (BNB)</th>
                        <th>Assento</th>
                        <th>Data</th>
                        <th>Transação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['consultorio_id']); ?></td>
                            <td><?= htmlspecialchars($row['consulta']); ?></td>
                            <td><?= number_format($row['preco'], 8, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['numero_dentista']); ?></td>
                            <td><?= htmlspecialchars($row['data_reserva']); ?></td>
                            <td><?= htmlspecialchars($row['transacao_hash']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <nav class="text-center mt-4">
            <ul class="pagination">
                <?php if ($pagina_atual > 1): ?>
                    <li class="page-item"><a class="page-link" href="?data_reserva=<?= urlencode($data_filtro); ?>&consulta=<?= urlencode($consulta_filtro); ?>&consultorio_id=<?= urlencode($consultorio_id_filtro); ?>&pagina=<?= $pagina_atual - 1 ?>">Anterior</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_atual) ? 'active' : '' ?>">
                        <a class="page-link" href="?data_reserva=<?= urlencode($data_filtro); ?>&consulta=<?= urlencode($consulta_filtro); ?>&consultorio_id=<?= urlencode($consultorio_id_filtro); ?>&pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <li class="page-item"><a class="page-link" href="?data_reserva=<?= urlencode($data_filtro); ?>&consulta=<?= urlencode($consulta_filtro); ?>&consultorio_id=<?= urlencode($consultorio_id_filtro); ?>&pagina=<?= $pagina_atual + 1 ?>">Próximo</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="text-center mt-4">
            <a href="gerar_pdf.php?data_reserva=<?= urlencode($data_filtro); ?>&consulta=<?= urlencode($consulta_filtro); ?>&consultorio_id=<?= urlencode($consultorio_id_filtro); ?>" class="btn btn-success">Baixar PDF</a>
        </div>

    <?php else: ?>
        <p class="text-center text-danger">Nenhuma reserva encontrada com esses filtros.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
