<?php
// Verificar se a conexão é segura (HTTPS)
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    // Gerar a URL para redirecionamento para HTTPS
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // Redirecionar para a versão HTTPS da URL atual
    header('Location: ' . $url);
    exit();
}

// Cabeçalhos de segurança
header('Strict-Transport-Security: max-age=31536000; includeSubDomains'); // HSTS
header('X-Content-Type-Options: nosniff'); // Proteção contra MIME Sniffing
header('X-Frame-Options: DENY'); // Proteger contra clickjacking
header('X-XSS-Protection: 1; mode=block'); // Proteção contra XSS
header('Referrer-Policy: no-referrer'); // Política de Referência
// Impedir que o site seja carregado em um iframe
header("X-Frame-Options: SAMEORIGIN");

// Você também pode adicionar segurança adicional
header("Content-Security-Policy: frame-ancestors 'self';");

header("Content-Security-Policy: frame-ancestors 'self' https://carlitoslocacoes.com;");
// Seu código PHP começa aqui
// ...

// Verifica se o cabeçalho enviado pelo CloudFront está presente
if (isset($_SERVER['HTTP_X_CUSTOM_HEADER'])) {
    $valorDoCabecalho = $_SERVER['HTTP_X_CUSTOM_HEADER'];

    // Exibe o valor do cabeçalho
    echo "Cabeçalho personalizado recebido: " . htmlspecialchars($valorDoCabecalho);
} else {
    // Caso o cabeçalho não exista
   
// Exibindo um botão com link específico



}

// Configura um cabeçalho de resposta (se necessário)
header("X-Powered-By: MeuServidorPHP");

?>
<?php
// Definindo os conteúdos como variáveis
$title1 = "Navegando por Novas Possibilidades";
$text1 = "Inspiramos e damos vida a ideias que refletem simplicidade e autenticidade. Nossa missão é explorar novos caminhos e transformar descobertas em experiências significativas.";

$title2 = "Conexões que Inspiram";
$text2 = "Vamos além do comum, criando vínculos autênticos que agregam valor e propósito. Acreditamos na importância de cada detalhe, com dedicação e cuidado.";

$title3 = "Detalhes que Transformam";
$text3 = "Nosso objetivo é criar algo único, que valorize a simplicidade e a conexão entre as pessoas, traduzindo ideias em experiências genuínas.";

?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Locação de Trator, Retroescavadeira e Equipamentos para Linha de Transmissão | Carlitos Locações</title>
    <meta name="description" content="Locação de tratores em Palmeira das Missões - RS. Encontre tratores de qualidade para sua necessidade! Trabalhamos com linha de transmissão, oferecendo equipamentos robustos e eficientes. Estamos na Av Independência, N 877, Sala 02, Palmeira Das Missões, Rio Grande Do Sul, Brasil. CEP: 98300-000. Acesse carlitoslocacoes.com">
    <meta name="keywords" content="trator, óleo, aeroportos, Palmeira das Missões, locação de tratores, linha de transmissão, locação de máquinas, retroescavadeiras, equipamentos pesados, aluguel de máquinas, sistema de controle de óleo, sistema de caixa, pagamentos de prestações, locação de quartos">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="index, follow">
  <meta name="author" content="Carlito Veeck Pautz Júnior">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
 

<style>
/* Definição geral do corpo */

body {
            margin: 0;
            overflow: auto;
            background-color: #f0f0f0;
            padding-top: 60px
        }
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
        }

body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.8;
            margin: 0;
            background-color: #e7e7e7;
            color: #333;
            zoom: 1;
        }


        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Mantém o fundo atrás do conteúdo */
        }

@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

h1 {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: #008080; /* Tom de verde-azulado que combina bem com seu site */
    text-align: center;
    margin-top: 20px;
}

/* Parágrafos e títulos */
p, h2, h3, h4, h5, h6 {
    color: #000; /* Texto em preto */
}

/* Botões com estilo moderno */
button {
    background-color: #b0b0b0; /* Tom mais escuro para contraste */
    color: #fff; /* Texto branco */
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
}

button:hover {
    background-color: #8c8c8c; /* Fundo mais escuro no hover */
    color: #fff;
}

/* Carrossel */
.carousel-caption {
    background-color: rgba(0, 0, 0, 0.5); /* Fundo semi-transparente */
    padding: 10px;
}

.carousel-inner > .item > img,
.carousel-inner > .item > a > img {
    display: block;
    height: auto;
    width: 100%;
    max-width: 100%;
    line-height: 1;
}

/* Imagens redondas */
.img-circle-custom {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    object-fit: cover;
}

/* Fundo de seções */
.section {
    background: #e7e7e7; /* Fundo com a cor escolhida */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    color: #333; /* Texto em cinza escuro */
}

.section:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Ajustes para dispositivos móveis */
@media (max-width: 767px) {
    body {
        font-size: 18px; /* Tamanho da fonte menor */
        padding: 10px;
    }

    .img-circle-custom {
        width: 80px;
        height: 80px;
    }

    .carousel-inner > .item > img,
    .carousel-inner > .item > a > img {
        height: 300px; /* Ajuste para carrosséis menores */
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    body {
        font-size: 20px;
    }

    .img-circle-custom {
        width: 90px;
        height: 90px;
    }

    .carousel-inner > .item > img,
    .carousel-inner > .item > a > img {
        height: 420px;
    }
}

/* Iframes responsivos */
.video-container {
    position: relative;
    padding-bottom: 56.25%; /* Proporção 16:9 */
    height: 0;
}

.video-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Botões fixados na parte inferior */
.stylized-button {
    position: fixed;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    font-size: 16px;
    color: #fff;
    background-color: #b0b0b0; /* Tom escuro para destacar */
    border: none;
    padding: 10px 20px;
    text-align: center;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.stylized-button:hover {
    background-color: #8c8c8c;
}

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

body {
  padding-top: 60px; /* Ajuste conforme a altura real da navbar */
}

header {
  margin-top: 60px; /* Deve ser igual ou maior que a altura da navbar */
}

/* Ajuste dos textos */
.navbar-brand,
.navbar-nav li a {
  color: #555 !important; /* Melhor contraste */
  font-size: 16px;
  font-weight: bold;
  text-decoration: none;
}

.navbar-brand:hover,
.navbar-nav li a:hover {
  color: #222 !important;
}

/* Estilização do telefone */
.telefone {
  font-size: 14px;
  color: #555;
  text-decoration: none;
}

/* Garante que tudo fique na mesma linha */
.navbar-header {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between; /* Distribui os itens corretamente */
  width: 100%;
}

/* Mantém "Contato" e "Entrar" na mesma linha e à direita */
.navbar-links {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-end; /* Alinha os itens à direita */
  flex-grow: 1; /* Faz com que ocupem o espaço disponível à direita */
}

.navbar-nav {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 15px;
  list-style: none;
  padding: 0;
  margin: 0;
}

.navbar-nav li {
  display: inline-block;
}

/* Ajuste para impedir que "Entrar" desça para outra linha */
.entrar-button {
  text-align: right;
  white-space: nowrap; /* Garante que não quebre a linha */
}

/* Melhor responsividade no mobile */
@media (max-width: 768px) {
  .navbar-header {
    flex-direction: column; /* Ajusta para telas menores */
    align-items: center;
    text-align: center;
  }

  .navbar-links {
    width: 100%;
    justify-content: center; /* Alinha ao centro no mobile */
  }

  .navbar-nav {
    justify-content: center;
    flex-direction: row;
  }
}

/* Contêiner principal para PDF estilo slide */
.pdf-slide-container {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

/* PDF individual como parte de um slide */
.pdf-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: transform 0.5s ease-in-out;
}

*::before {
    content: none !important; /* Remove qualquer conteúdo */
    display: none !important; /* Garante que não ocupe espaço */
}


.container {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 3 colunas */
    gap: 15px; /* Espaçamento entre os botões */
    justify-items: center; /* Centraliza os botões */
    align-items: center; /* Alinha verticalmente */
    padding: 20px;
   
}
}

/* Ajuste para telas menores */
@media (max-width: 768px) {
    .container {
        grid-template-columns: repeat(2, 1fr); /* 2 colunas */
    }
}

@media (max-width: 480px) {
    .container {
        grid-template-columns: repeat(1, 1fr); /* 1 coluna */
    }
}

.button-wrapper {
    width: 100%;
    text-align: center;
}

.button {
    padding: 12px 20px;
    font-size: 16px;
    cursor: pointer;
    width: 100%; /* Faz os botões ocuparem toda a largura disponível */
    max-width: 250px; /* Limita o tamanho máximo */
}
@media (max-width: 768px) {
  .navbar-nav {
    display: flex;
    flex-direction: row;
  }



</style>


</head>
<body>
  

<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <div class="navbar-content">
        <a class="navbar-brand" href="../">Carlito's Locações</a>
        <a class="navbar-brand telefone" href="tel:+5555996479747">(55) 9.9647-9747</a>
      </div>
      <div class="navbar-links">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="https://carlitoslocacoes.com/contato/">Contato</a></li>
          <li><a href="/site/login.php" class="entrar-button">Entrar</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>





<div id="particles-js"></div>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
   
<header class="container-fluid bg-1 text-center">
        <img src="dentejoinha.png" class="title-img" width="200" height="200" alt="Carlitos Chapéu">
        <img src="/site2/fontcarlitos.png" class="title-img" width="400" height="400" alt="Carlitos Locações">
    </header>


<center><h1>Consultório Odontológico</h3><br>
  
     <div class="container">
    <div class="button-wrapper"><button class="button" onclick="location.href='https://carlitoslocacoes.com/site3/cadastro_produto/cadastro_consultorio.php'">Cadastrar Consultórios</button></div>
    <div class="button-wrapper">
    <button class="button" onclick="location.href='https://carlitoslocacoes.com/site2/nossasmaquinas/horarios_odonto.php'"
            aria-label="Botão para acessar a página Pegar Carona">
        Hórarios
    </button>
</div>
<div class="button-wrapper"><button class="button" onclick="location.href='https://carlitoslocacoes.com/site/odonto_consultar.php'">Minhas Consultas</button></div>
    <div class="button-wrapper"><button class="button" onclick="location.href='https://carlitoslocacoes.com/site/odonto_comprovante.php'">Meus Comprovantes</button></div>
</div>

      <br>
      


<br>
</center>

<div class="container-fluid bg-3 text-center">
 <div class="section">
        <h2><?php echo $title1; ?></h2>
        <p><?php echo $text1; ?></p>
    </div>

    <div class="section">
        <h2><?php echo $title2; ?></h2>
        <p><?php echo $text2; ?></p>
    </div>

    <div class="section">
        <h2><?php echo $title3; ?></h2>
        <p><?php echo $text3; ?></p>
    </div>

</div>
    
 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.13.216/pdf.min.js"></script>
    
<!-- Footer -->
<footer class="container-fluid bg-4 text-center">

<p>Av Independência, N 877, Sala 02, Palmeira Das Missões, Rio Grande Do Sul, Brazil 98300-000</p>
 <p>Desenvolvido por Carlito Veeck Pautz Júnior.</p> 
 <a href="https://carlitoslocacoes.com/site/login.php">Entrar</a>
 </footer>

</body>
</html>
 <script>
     particlesJS("particles-js", {
            particles: {
                number: { value: 10, density: { enable: true, value_area: 800 } },
                shape: {
                    type: "image",
                    image: {
                        src: "escova_dente.png",  // Substitua pelo link da sua imagem
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
