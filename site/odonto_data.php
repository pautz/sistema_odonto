<?php
session_start();

// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$_SESSION["eq_user"] = $_SESSION["username"];
$eq_user = $_SESSION["eq_user"];

// Conectar ao banco de dados
$cx = new mysqli("127.0.0.1", "username", "password", "dbname");
if ($cx->connect_error) {
    die("Erro na conexão com o banco: " . $cx->connect_error);
}

$username = htmlspecialchars($_SESSION["username"]);
$consultorio_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$consultorioelecionado = null;
$enderecoconsulta = "";
$dentistasDisponiveis = [];
$datasReservadasPorAssento = [];

if ($consultorio_id) {
    // Buscar detalhes do voo
    $stmt = $cx->prepare("SELECT id, consulta, preco, metamask FROM consultorio WHERE id = ?");
    $stmt->bind_param("i", $consultorio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $consultorioelecionado = $result->fetch_assoc();
        $enderecoconsulta = $consultorioelecionado['metamask'];
    } else {
        die("Erro: Voo não encontrado.");
    }
    $stmt->close();

    // Buscar dentistas disponíveis
    $stmt = $cx->prepare("SELECT numero_dentista FROM dentistas WHERE consultorio_id = ? AND pago = 0");
    $stmt->bind_param("i", $consultorio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $dentistasDisponiveis[] = $row['numero_dentista'];
    }
    $stmt->close();

    // Buscar datas reservadas por assento com transação confirmada
    $stmt = $cx->prepare("SELECT numero_dentista, data_reserva FROM reserva_dentista WHERE consultorio_id = ? AND pago = 1 AND transacao_hash IS NOT NULL");
    $stmt->bind_param("i", $consultorio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $datasReservadasPorAssento[$row['numero_dentista']][] = $row['data_reserva'];
    }
    $stmt->close();
}

// Buscar taxa de câmbio para BNB
$bnbToBrlRate = 0;
$url = "https://api.coingecko.com/api/v3/simple/price?ids=binancecoin&vs_currencies=brl";
$json = @file_get_contents($url);
if ($json !== false) {
    $data = json_decode($json, true);
    $bnbToBrlRate = $data["binancecoin"]["brl"];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <style>
    
    .assento-indisponivel {
        background-color: #ffcccc !important;
        color: gray !important;
    }
</style>
    <meta charset="UTF-8">
    <title>Escolha seu Assento e Data</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/web3/1.7.5/web3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>
    <style>
/* Reset de estilos */
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
    color: #333;
    text-align: center;
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

/* Área principal */
.wrapper {
    max-width: 500px;
    width: 100%;
    background: rgba(255, 255, 255, 0.15);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

/* Títulos */
h1, h2, h3 {
    font-family: 'Playfair Display', serif;
    color: #008080;
}

/* Estilização de inputs e selects */
select, input {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    background-color: white;
    color: black;
}

/* Estilização dos botões */

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

    .wrapper {
        max-width: 90%;
    }
}
/* Centraliza o conteúdo do corpo */
body {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

/* Centraliza navbar */
.navbar {
    text-align: center;
}

/* Centraliza o wrapper */
.wrapper {
    margin: auto;
    text-align: center;
}

/* Centraliza botões e inputs */
select, input, button {
    display: block;
    margin: auto;
    text-align: center;
}
/* Ajuste do contêiner do calendário */
.pika-single {
    background: white;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

/* Estilização dos dias normais */
.pika-single td {
    text-align: center;
    font-size: 14px;
    padding: 10px;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.3s ease-in-out;
}

/* Dias disponíveis */
.pika-single td:not(.is-disabled) {
    background-color: #f9f9f9;
    color: #333;
}

/* Dias desativados - Agora ficarão vermelhos */
.pika-single td.is-disabled {
    background-color: #ff4d4d !important; /* Vermelho intenso */
    color: white !important; /* Mantém o texto visível */
    font-weight: bold;
    border-radius: 6px;
    opacity: 1 !important;
}

/* Destaque ao passar o mouse (exceto em dias bloqueados) */
.pika-single td:not(.is-disabled):hover {
    background-color: #d1ecf1;
    color: black;
}

/* Ajuste visual para dias selecionados */
.pika-single td.is-selected {
    background-color: #008080 !important;
    color: white !important;
    font-weight: bold;
}


    </style>
</head>
<body>
    <div id="particles-js"></div>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
particlesJS("particles-js", {
    particles: {
        number: { value: 10, density: { enable: true, value_area: 800 } },
        shape: { type: "image", image: { src: "https://carlitoslocacoes.com/odonto/escova_dente.png", width: 1080, height: 1080 } },
        opacity: { value: 0.8 },
        size: { value: 50, random: true },
        move: { enable: true, speed: 3, direction: "none", random: true },
        line_linked: { enable: false }
    },
    interactivity: {
        events: { onhover: { enable: true, mode: "bubble" }, onclick: { enable: true, mode: "repulse" } },
        modes: { bubble: { distance: 200, size: 30, duration: 2, opacity: 1 }, repulse: { distance: 150, duration: 0.4 } }
    }
});
</script>
<center><a href="https://carlitoslocacoes.com/site2/nossasmaquinas/horarios_odonto.php" class="btn">Início</a></center><br>
   <div class="wrapper">
    <h2>Olá, <b><?php echo $username; ?></b>. Escolha seu dentista e a data para consulta.</h2>

    <?php if ($consultorioelecionado): ?>
        <h3>Consulta: <?php echo htmlspecialchars($consultorioelecionado['consulta']); ?> - BNB <?php echo number_format($consultorioelecionado['preco'], 8, ',', '.'); ?>  
        (≈ R$ <?php echo number_format($consultorioelecionado['preco'] * $bnbToBrlRate, 2, ',', '.'); ?>)</h3>

        <label for="assento">Selecione um dentista:</label>
       <select id="assento">
    <?php foreach ($dentistasDisponiveis as $assento): ?>
        <option value="<?php echo $assento; ?>"><?php echo $assento; ?></option>
    <?php endforeach; ?>
</select>



       <label for="datepicker">Selecione uma Data:</label>
<input type="text" id="datepicker" readonly>


        <button id="confirmarReserva" disabled onclick="realizarPagamento()">Confirmar Reserva via MetaMask</button>
    <?php else: ?>
        <p>Voo não encontrado.</p>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var datasReservadasPorAssento = <?php echo json_encode($datasReservadasPorAssento); ?>;
    var dentistaselect = document.getElementById("assento");
    var picker;

    function atualizarCalendario(dentistaselecionado) {
        var datasParaDesabilitar = datasReservadasPorAssento[dentistaselecionado] || [];

        document.getElementById("datepicker").value = ""; 

        if (picker) {
            picker.destroy();
        }

        picker = new Pikaday({
            field: document.getElementById('datepicker'),
            format: 'YYYY-MM-DD',
            disableDayFn: function (date) {
                let dataStr = date.toISOString().split('T')[0];
                return datasParaDesabilitar.includes(dataStr);
            },
            onDraw: function () {
                setTimeout(() => {
                    document.querySelectorAll('.pika-single td').forEach(td => {
                        let dataStr = td.dataset.pikaDay;
                        if (datasParaDesabilitar.includes(dataStr)) {
                            td.classList.add("data-indisponivel");
                        }
                    });
                }, 100);
            },
            onSelect: function () {
                document.getElementById("confirmarReserva").disabled = false;
            }
        });
    }

    dentistaselect.addEventListener("change", function () {
        atualizarCalendario(this.value);
    });

    atualizarCalendario(dentistaselect.value);
});



document.getElementById("confirmarReserva").addEventListener("click", async function () {
    const dentistaselecionado = document.getElementById("assento").value;
    const dataSelecionada = document.getElementById("datepicker").value;

    // Verificar disponibilidade antes de abrir MetaMask
    const verificarDisponibilidade = await fetch("verificar_disponibilidade2.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `consultorio_id=<?php echo $consultorio_id; ?>&assento=${dentistaselecionado}&data_reserva=${dataSelecionada}`
    });

    const resposta = await verificarDisponibilidade.text();

    if (resposta.includes("ocupado")) {
        alert("Erro: O assento " + dentistaselecionado + " já está reservado na data escolhida!");
        return; // **MetaMask não será aberta**
    }

    // Prosseguir com o pagamento via MetaMask
    const web3 = new Web3(window.ethereum);
    await window.ethereum.request({ method: "eth_requestAccounts" });

    const contas = await web3.eth.getAccounts();
    const contaOrigem = contas[0];

    if (!contaOrigem) {
        alert("Erro: Nenhuma conta MetaMask conectada.");
        return;
    }

    const enderecoconsulta = "<?php echo $enderecoconsulta; ?>";
    const valorReserva = web3.utils.toWei("<?php echo $consultorioelecionado['preco']; ?>", "ether");

    try {
        const transacao = await web3.eth.sendTransaction({
            from: contaOrigem,
            to: enderecoconsulta,
            value: valorReserva
        });

        const hashTransacao = transacao.transactionHash;
        
        if (!hashTransacao) {
            alert("Erro: Hash da transação não encontrada.");
            return;
        }

        alert("Transação enviada! Hash: " + hashTransacao);

        // Registrar reserva no banco de dados após confirmação do pagamento
        const response = await fetch("salvar_reserva2.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `consultorio_id=<?php echo $consultorio_id; ?>&assento=${dentistaselecionado}&data_reserva=${dataSelecionada}&transacao_hash=${hashTransacao}&eq_user=<?php echo $eq_user; ?>`
        });

        const result = await response.text();
        alert(result);

    } catch (erro) {
        alert("Erro ao realizar pagamento: " + erro.message);
    }
});




</script>

</body>
</html>
