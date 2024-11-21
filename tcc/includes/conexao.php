<?php
// Definir as credenciais de conexão com o banco de dados
$hostname = 'localhost';     // Nome do host do banco de dados
$username = 'root';          // Nome de usuário do banco de dados
$password = '';              // Senha do banco de dados
$database = 'ECO';           // Nome do banco de dados
$port = 3307;                // Porta do banco de dados (caso necessário)

// Estabelecer a conexão com o banco de dados
$con = mysqli_connect($hostname, $username, $password, $database, $port);

// Verificar se a conexão falhou
if (mysqli_connect_errno()) {
    printf("Erro Conexão: %s", mysqli_connect_error());
    exit();
}

// Obtenha sua chave de API gratuitamente em http://hgbrasil.com/weather
$chave = '8ef376b8'; 

// Resgata o IP do usuário ou define um padrão
$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';

// Realiza a requisição à API HG Weather
$dados = hg_request(array(
    'user_ip' => $ip,
    'lat' => '-23.5489', // Latitude para São Paulo (ou outra localização desejada)
    'lon' => '-46.6388'  // Longitude para São Paulo (ou outra localização desejada)
), $chave);

// Verifica se os dados foram obtidos
if (!isset($dados)) {
    echo 'Erro ao obter dados da API.';
    die();
}

// Função para realizar a requisição à API
function hg_request($parametros, $chave = null, $endpoint = 'weather') {
    $url = 'http://api.hgbrasil.com/' . $endpoint . '/?format=json&';
    
    if (is_array($parametros)) {
        // Insere a chave nos parâmetros
        if (!empty($chave)) $parametros = array_merge($parametros, array('key' => $chave));
        
        // Transforma os parâmetros em URL
        foreach ($parametros as $key => $value) {
            if (empty($value)) continue;
            $url .= $key . '=' . urlencode($value) . '&';
        }
        
        // Obtém os dados da API
        $resposta = file_get_contents(substr($url, 0, -1));
        
        return json_decode($resposta, true);
    } else {
        return false;
    }
}

// Verificar se os dados de clima foram recebidos corretamente
if (isset($dados['results'])) {
    $current = $dados['results']; // Armazenar os dados meteorológicos atuais

    // Definir a data atual
    $data_dados = date('Y-m-d');
    // Definir o ID do sensor (substitua pelo ID correto do seu banco de dados)
    $sensor_id = 0;

    // Inserir ou atualizar a temperatura (ID_Dados = 1)
    $temperatura = isset($current['temp']) ? $current['temp'] : 'Indisponível'; // Verifica se o valor está disponível
    $sql_temp = "INSERT INTO Dados (ID_Dados, ID_Sensor, Valor_Dados, Data_Dados)
                 VALUES (1, '$sensor_id', '$temperatura', '$data_dados')
                 ON DUPLICATE KEY UPDATE Valor_Dados = '$temperatura', Data_Dados = '$data_dados'";
    $con->query($sql_temp); // Executar a consulta

    // Inserir ou atualizar a umidade (ID_Dados = 2)
    $umidade = isset($current['humidity']) ? $current['humidity'] : 'Indisponível'; // Verifica se o valor está disponível
    $sql_umidade = "INSERT INTO Dados (ID_Dados, ID_Sensor, Valor_Dados, Data_Dados)
                    VALUES (2, '$sensor_id', '$umidade', '$data_dados')
                    ON DUPLICATE KEY UPDATE Valor_Dados = '$umidade', Data_Dados = '$data_dados'";
    $con->query($sql_umidade); // Executar a consulta

    // Inserir ou atualizar a direção do vento (ID_Dados = 3)
    $direcao_vento = isset($current['wind_direction']) ? $current['wind_direction'] : 'Indisponível'; // Verifica se o valor está disponível
    $sql_direcao_vento = "INSERT INTO Dados (ID _Dados, ID_Sensor, Valor_Dados, Data_Dados)
                          VALUES (3, '$sensor_id', '$direcao_vento', '$data_dados')
                          ON DUPLICATE KEY UPDATE Valor_Dados = '$direcao_vento', Data_Dados = '$data_dados'";
    $con->query($sql_direcao_vento); // Executar a consulta

    // Inserir ou atualizar a chance de chuva (ID_Dados = 4)
    $chance_chuva = isset($current['precipitation']) ? $current['precipitation'] : 'Indisponível'; // Verifica se o valor está disponível
    $sql_chance_chuva = "INSERT INTO Dados (ID_Dados, ID_Sensor, Valor_Dados, Data_Dados)
                         VALUES (4, '$sensor_id', '$chance_chuva', '$data_dados')
                         ON DUPLICATE KEY UPDATE Valor_Dados = '$chance_chuva', Data_Dados = '$data_dados'";
    $con->query($sql_chance_chuva); // Executar a consulta

    // Inserir ou atualizar a pressão atmosférica (ID_Dados = 5)
    $pressao = isset($current['pressure']) ? $current['pressure'] : 'Indisponível'; // Verifica se o valor está disponível
    $sql_pressao = "INSERT INTO Dados (ID_Dados, ID_Sensor, Valor_Dados, Data_Dados)
                    VALUES (5, '$sensor_id', '$pressao', '$data_dados')
                    ON DUPLICATE KEY UPDATE Valor_Dados = '$pressao', Data_Dados = '$data_dados'";
    $con->query($sql_pressao); // Executar a consulta

    // Inserir ou atualizar a velocidade do vento (ID_Dados = 6)
    $vento = isset($current['wind_speed']) ? $current['wind_speed'] : 'Indisponível'; // Verifica se o valor está disponível
    $sql_vento = "INSERT INTO Dados (ID_Dados, ID_Sensor, Valor_Dados, Data_Dados)
                  VALUES (6, '$sensor_id', '$vento', '$data_dados')
                  ON DUPLICATE KEY UPDATE Valor_Dados = '$vento', Data_Dados = '$data_dados'";
    $con->query($sql_vento); // Executar a consulta

    echo "Dados inseridos com sucesso!"; // Mensagem de sucesso
} else {
    echo "Erro: Dados não encontrados na API."; // Mensagem de erro caso os dados não sejam encontrados
}

// Fechar a conexão com o banco de dados
$con->close();
?>