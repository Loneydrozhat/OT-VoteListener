<?php

error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

/*
    OT-VoteListener by https://otservers.org
    
    Setup Instructions:
    1. Place this file (vote.php) somewhere in your public-facing web directory
    2. Generate a vote key from your control panel on https://otservers.org
    3. Fill out the settings below. The database table to store votes will be
       created automatically when your first vote is received. Please note the
       specified database must be for an existing server
    4. Enter the URL to this file on your otservers.org control panel
       Ex: https://yourotserver.com/vote.php
    5. Add the vote script (vote.lua) to your server's scripts folder and
       configure it as well
    
    For issues and assistance, please open an issue on our Github
    project: https://github.com/otservers/OT-VoteListener/issues
    
    
    Instruções de Setup:
    1. Coloque o arquivo (vote.php) em algum lugar em seu diretório de web público
    2. Gere uma chave de voto no seu painel de controle em https://otservers.org
    3. Preencha as configurações abaixo. A tabela do banco de dados para armazenar os votos será
       criado automaticamente quando voce receber seu primeiro voto. Fique atento, o
       banco de dados especificado deve ser para um servidor existente
    4. Digite o URL para este arquivo em seu painel de controle do otservers.org
       Ex: https://yourotserver.com/vote.php
    5. Adicione o script de voto (vote.lua) à pasta de scripts do seu servidor e
       configure-o também
     
     Enfrentando problemas e necessita de assistência? Abra um issue no nosso projeto no
     Github: https://github.com/otservers/OT-VoteListener/issues
*/

// Start of Settings - Começo das Configurações
$key = ''
$dbUser = 'root'
$dbPass = 'toor'
$dbIP = 'localhost'
$dbPort = 3306
$dbDatabase = 'tibia_db'
// End of Settings - Fim das Configurações



if (isset($_POST['key'])) {
    if ($_POST['key'] !== $key) {
        exit('fail');
    }
} else {
    exit('fail');
}

$conn = new mysqli($dbIP, $dbUser, $dbPass, $dbDatabase, $dbPort);

if (mysqli_connect_errno()) {
    exit('fail');
}

// Used by otservers.org to set up the database table
// Utilizado por otservers.org para fazer o setup da tabela de banco de dados
if (isset($_POST['setup'])) {    
    $conn->query('CREATE TABLE IF NOT EXISTS player_votes (username varchar(255) CHARACTER SET utf8 NOT NULL, votes int(11) NOT NULL, votes_total int(11) NOT NULL, PRIMARY KEY(username))');
    $conn->close();
    exit('success');
}

if (isset($_POST['username'])) { 
    $username = $_POST['username'];
    $stmt = $conn->prepare('INSERT INTO player_votes (username, votes, votes_total) VALUES (?, 1, 1) ON DUPLICATE KEY UPDATE votes=votes+1, votes_total=votes_total+1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    exit('success');
}

$conn->close();
exit('fail');

?>