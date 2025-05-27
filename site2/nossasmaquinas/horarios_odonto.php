<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "dbname";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Definir a quantidade de consultorio por página
$limite = 9; 
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

// Pesquisa personalizada
// Pesquisa personalizada
$searchQuery = "WHERE 1=1"; 
if (!empty($_GET['search_consulta'])) {
    $search_consulta = $conn->real_escape_string($_GET['search_consulta']);
    $searchQuery .= " AND v.consulta LIKE '%$search_consulta%'";
}
if (!empty($_GET['search_horario'])) {
    $search_horario = $conn->real_escape_string($_GET['search_horario']);
    $searchQuery .= " AND v.horario = '$search_horario'";
}
if (!empty($_GET['search_id'])) {  // 🔥 Novo filtro para pesquisar por ID
    $search_id = intval($_GET['search_id']); // Certifica-se que o ID é um número
    $searchQuery .= " AND v.id = $search_id";
}


// Obtenção dos consultorio com paginação
$sql = "SELECT v.id, v.consulta, v.preco, v.horario, v.metamask FROM consultorio v 
        $searchQuery ORDER BY v.id DESC LIMIT $limite OFFSET $offset";

$result = $conn->query($sql);

// Contar o total de consultorio para calcular páginas
$sqlTotal = "SELECT COUNT(*) AS total FROM consultorio v $searchQuery";
$resultTotal = $conn->query($sqlTotal);
$totalconsultorio = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalconsultorio / $limite);

$conn->close();
// Verifica se a cotação está armazenada e se não expirou (atualiza a cada 5 minutos)
if (!isset($_SESSION['bnb_rate']) || (time() - $_SESSION['bnb_rate_time'] > 300)) { 
    $apiUrl = "https://api.coingecko.com/api/v3/simple/price?ids=binancecoin&vs_currencies=brl";
    $response = @file_get_contents($apiUrl); // Usa '@' para evitar erros caso a API falhe

    if ($response !== false) { // Verifica se conseguiu obter dados da API
        $data = json_decode($response, true);
        $_SESSION['bnb_rate'] = $data["binancecoin"]["brl"];
        $_SESSION['bnb_rate_time'] = time(); // Guarda o tempo da última atualização
    } else {
        $_SESSION['bnb_rate'] = null;
    }
}

// Define a variável para uso no sistema
$taxaCambioBNB = $_SESSION['bnb_rate'];


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>consultorio Disponíveis</title>
    <style>
      /* Configuração geral do corpo */
body {
  margin: 0;
  overflow: auto;
  background-color: #f0f0f0;
  padding-top: 80px; /* Ajustado conforme a navbar fixa */
  font-family: 'Roboto', Arial, sans-serif;
  line-height: 1.8;
  color: #333;
  zoom: 1;
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
  text-align: center;
}

/* Ajustes visuais para títulos */
h1, h2, h3, h4 {
  font-family: 'Playfair Display', serif;
  color: #008080;
  text-align: center;
}

/* Formulário de pesquisa */
.form-container {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 20px;
}

/* Botões estilizados */
button, .btn {
  background-color: #b0b0b0;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background-color 0.3s;
  cursor: pointer;
}

button:hover, .btn:hover {
  background-color: #8c8c8c;
}

/* Contêiner de produtos */
.product-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
}

/* Cartões individuais */
.product-card {
  width: calc(33.33% - 20px);
  max-width: 300px;
  background: #fff;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Ajuste para informações no card */
.product-card p {
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Paginação estilizada */
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

/* Ajustes responsivos */
@media (max-width: 768px) {
  body {
    font-size: 18px;
    padding: 10px;
  }

  .navbar {
    padding: 15px;
  }

  .product-card {
    width: 100%;
  }
}
#particles-js {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1; /* Mantém atrás do conteúdo */
}
  #particles-js {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1; /* Mantém atrás do conteúdo */
}
    </style>
</head>
<body>
<div id="particles-js"></div>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <center><a href="../../odonto" class="btn">Início</a></center>
    <h2>Horários</h2>
    
    <br>

    <!-- Formulários de Pesquisa -->
   <form method="get" action="" class="form-container" aria-label="Formulário de pesquisa de consultorio">
    <div>
        <label for="search_consulta" id="label_consulta">Tipo de Consulta:</label>
        <input type="text" id="search_consulta" name="search_consulta" aria-labelledby="label_consulta">
    </div>
    <div>
        <label for="search_horario" id="label_horario">Pesquisar por Horário:</label>
        <input type="time" id="search_horario" name="search_horario" aria-labelledby="label_horario">
    </div>
    <div>
        <label for="search_id" id="label_id">Pesquisar por ID:</label>
        <input type="number" id="search_id" name="search_id" aria-labelledby="label_id">
    </div>
    <div>
        <input type="submit" value="Pesquisar" class="btn" aria-label="Botão de pesquisa de consultorio">
    </div>
</form>


    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='product-card'>";
                echo "<p title='ID do voo: " . htmlspecialchars($row["id"]) . "' aria-label='ID do voo: " . htmlspecialchars($row["id"]) . "'><strong>ID:</strong> " . htmlspecialchars($row["id"]) . "</p>";
                echo "<h3 title='Consulta: " . htmlspecialchars($row["consulta"]) . "' 
        aria-label='Consulta: " . htmlspecialchars($row["consulta"]) . "'>
        Consulta: " . htmlspecialchars($row["consulta"]) . "</h3>";
$precoBRL = $row["preco"] * $taxaCambioBNB; // ✅ Agora está correto!
echo "<p title='Preço do voo: BNB " . number_format($row["preco"], 8, ',', '.') . " (~R$ " . number_format($precoBRL, 2, ',', '.') . ")' 
        aria-label='Preço do voo: BNB " . number_format($row["preco"], 8, ',', '.') . " (~R$ " . number_format($precoBRL, 2, ',', '.') . ")'>
        <strong>Preço:</strong> BNB " . number_format($row["preco"], 8, ',', '.') . " (~R$ " . number_format($precoBRL, 2, ',', '.') . ")</p>";


                echo "<p title='Horário do voo: " . htmlspecialchars($row["horario"]) . "' 
        aria-label='Horário do voo: " . htmlspecialchars($row["horario"]) . "'>
        <strong>Horário:</strong> " . htmlspecialchars($row["horario"]) . "</p>";

                echo "<p class='metamask-info'><strong>Pagamento MetaMask:</strong> " . htmlspecialchars($row["metamask"]) . "</p>";
                echo "<p><a href='https://carlitoslocacoes.com/site/odonto_data.php?id=" . $row["id"] . "' class='btn' title='Escolher Data e Dentista #" . htmlspecialchars($row["id"]) . "' aria-label='Escolher Data e Dentista #" . htmlspecialchars($row["id"]) . "'>Escolher Data e Dentista #" . htmlspecialchars($row["id"]) . "</a></p>";

                echo "</div>";
            }
        } else {
            echo "<p>Nenhum voo encontrado.</p>";
        }
        ?>
    </div>

<script>
    particlesJS("particles-js", {
        particles: {
            number: { value: 10, density: { enable: true, value_area: 800 } },
            shape: {
                type: "image",
                image: {
                    src: "https://carlitoslocacoes.com/odonto/escova_dente.png",  /* Substitua pela sua imagem */
                    width: 1080,
                    height: 1080
                }
            },
            opacity: { value: 0.8 },
            size: { value: 70, random: true },
            move: { enable: true, speed: 4, direction: "none", random: true },
            line_linked: { enable: false }
        },
        interactivity: {
            events: {
                onhover: { enable: true, mode: "bubble" },
                onclick: { enable: true, mode: "repulse" }
            },
            modes: {
                bubble: { distance: 200, size: 40, duration: 2, opacity: 1 },
                repulse: { distance: 150, duration: 0.4 }
            }
        }
    });
</script>
    <!-- Paginação -->
    <div class='pagination'>
        <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>
            <a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php } ?>
    </div>

</body>
