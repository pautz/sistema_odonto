<?php
// Inicializar a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar se o usuário está logado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: https://carlitoslocacoes.com/site/login.php");
    exit;
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "dbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$eq_user = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Cadastro de consultorio</title>
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

/* Formulário de cadastro */
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

input[type="submit"], button {
    background-color: #b0b0b0;
    color: white;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
    font-size: 16px;
    width: 100%;
}

input[type="submit"]:hover, button:hover {
    background-color: #8c8c8c;
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
/* Botão estilizado */
.styled-button {
    display: inline-block;
    background-color: #b0b0b0;
    color: white;
    padding: 12px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    transition: background-color 0.3s;
}

.styled-button:hover {
    background-color: #8c8c8c;
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
<center>
    <a href="../../odonto" class="btn styled-button">Início</a>
</center><br>

    <h2>Cadastro:</h2>
    <form action="" method="post">
        <label for="consulta">Tipo de Consulta:</label>
        <input type="text" id="consulta" name="consulta" required>

        <label for="preco">Preço em BNB:</label>
<input type="number" id="preco" name="preco" step="0.000000001" required>


        <label for="quantidade_dentistas">Quantidade de Consultórios e Dentistas:</label>
        <input type="number" id="quantidade_dentistas" name="quantidade_dentistas" required>

        <label for="metamask">Endereço MetaMask para Pagamento:</label>
        <input type="text" id="metamask" name="metamask" required>
        
        <label for="horario">Horário da Consulta:</label>
<input type="time" id="horario" name="horario" required>


        <input type="submit" value="Cadastrar consultorio">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $consulta = htmlspecialchars($_POST['consulta']);
        $preco = floatval($_POST['preco']);
        $quantidade_dentistas = intval($_POST['quantidade_dentistas']);
        $metamask = htmlspecialchars($_POST['metamask']);
        $eq_user = $_SESSION['username'];
        $horario = htmlspecialchars($_POST['horario']);

        // Inserir consultorio no banco
        $stmt = $conn->prepare("INSERT INTO consultorio (consulta, preco, metamask, horario) VALUES (?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("sdss", $consulta, $preco, $metamask, $horario);


            if ($stmt->execute()) {
                $consultorio_id = $stmt->insert_id;

                // Criar os dentistas automaticamente
                for ($i = 1; $i <= $quantidade_dentistas; $i++) {
                    $numero_dentista = "A" . $i;
                    $stmt_assento = $conn->prepare("INSERT INTO dentistas (consultorio_id, numero_dentista, pago) VALUES (?, ?, 0)");
                    if ($stmt_assento) {
                        $stmt_assento->bind_param("is", $consultorio_id, $numero_dentista);
                        $stmt_assento->execute();
                    }
                }

                echo "<h3 style='color: green;'>consultorio cadastrado com sucesso, incluindo $quantidade_dentistas dentistas!</h3>";
            } else {
                echo "<h3 style='color: red;'>Erro ao cadastrar consultorio.</h3>";
            }
            $stmt->close();
        }
    }

    $conn->close();
    ?>
</body>
</html>
