<?php
/**
 * Sistema de Consulta de CEP - ViaCEP
 * -------------------------------------------------
 * Recebe um CEP do usuário, consulta a API pública ViaCEP
 * via requisição GET, decodifica o JSON e apresenta os dados.
 */

// -------------------------------------------------
// 1. Configuração inicial e captura da entrada
// -------------------------------------------------
$cep = isset($_GET['cep']) ? trim($_GET['cep']) : '';
$cepSomenteDigitos = preg_replace('/\D/', '', $cep); // remove traços, pontos, espaços

$resultado = null;   // dados formatados para exibição
$erro = null;        // mensagem de erro para o usuário
$carregando = false; // controla se uma consulta foi solicitada

// -------------------------------------------------
// 2. Validação e consulta à API ViaCEP
// -------------------------------------------------
if ($cep !== '') {
    $carregando = true;

    if (strlen($cepSomenteDigitos) !== 8) {
        $erro = 'Por favor, informe um CEP válido contendo 8 dígitos.';
    } else {
        $url = 'https://viacep.com.br/ws/' . rawurlencode($cepSomenteDigitos) . '/json/';

        $json = buscarViaCep($url);

        if ($json === null) {
            $erro = 'Não foi possível conectar à API ViaCEP. Verifique sua conexão e tente novamente.';
        } else {
            $dados = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $erro = 'A API retornou uma resposta inválida. Tente novamente.';
            } elseif (isset($dados['erro']) && $dados['erro'] !== '' && $dados['erro'] !== false) {
                // A API ViaCEP retorna {"erro": "true"} (string) quando o CEP não existe.
                $erro = 'CEP não encontrado. Verifique o número informado.';
            } else {
                // Monta o array de resultados apresentáveis
                $resultado = [
                    'CEP'         => isset($dados['cep'])         ? $dados['cep']         : '—',
                    'Logradouro'  => isset($dados['logradouro'])  ? $dados['logradouro']  : '—',
                    'Bairro'      => isset($dados['bairro'])      ? $dados['bairro']      : '—',
                    'Cidade'      => isset($dados['localidade'])  ? $dados['localidade']  : '—',
                    'Estado'      => isset($dados['uf'])          ? $dados['uf']          : '—',
                    'IBGE'        => isset($dados['ibge'])        ? $dados['ibge']        : '—',
                    'Complemento' => isset($dados['complemento']) && $dados['complemento'] !== ''
                                        ? $dados['complemento'] : '—',
                ];
            }
        }
    }
}

// -------------------------------------------------
// 3. Função que executa a requisição HTTP (GET)
// -------------------------------------------------
/**
 * Faz uma requisição GET para a API ViaCEP.
 * Retorna o corpo da resposta (JSON) ou null em caso de falha.
 */
function buscarViaCep(string $url): ?string
{
    // --- Método 1: cURL (recomendado, com verificação SSL ativa) ---
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,   // devolve a resposta como string
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_USERAGENT      => 'ConsultaCEP-PHP/1.0',
            // Mantém a verificação do certificado SSL (segurança)
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $resposta = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // curl_close() foi depreciada no PHP 8.5 (não tem efeito desde o 8.0),
        // por isso não é mais chamada.

        if ($resposta !== false && $status >= 200 && $status < 300) {
            return $resposta;
        }
        // Se o cURL falhar (ex.: CA bundle local), tenta o fallback abaixo.
    }

    // --- Método 2: fallback para file_get_contents ---
    // Útil quando o cURL não está disponível ou falha na verificação do
    // certificado por causa da configuração local do ambiente.
    $contexto = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'header'  => "Accept: application/json\r\n",
            'timeout' => 10,
        ],
    ]);
    $resposta = @file_get_contents($url, false, $contexto);

    return $resposta === false ? null : $resposta;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CEP — ViaCEP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

        <!-- Cabeçalho / título do sistema -->
        <header class="cabecalho">
            <h1>📍 Sistema de Consulta de CEP</h1>
            <p>Consulte endereços utilizando a API pública <strong>ViaCEP</strong></p>
        </header>

        <!-- Formulário de pesquisa -->
        <form class="formulario" action="index.php" method="get" autocomplete="off">
            <label for="cep">Informe o CEP:</label>
            <div class="grupo-campo">
                <input
                    type="text"
                    id="cep"
                    name="cep"
                    placeholder="Ex.: 01001000"
                    maxlength="9"
                    value="<?= htmlspecialchars($cep, ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autofocus
                    inputmode="numeric"
                >
                <button type="submit">CONSULTAR</button>
            </div>
            <small>Digite apenas os 8 dígitos do CEP.</small>
        </form>

        <!-- Área de resultados -->
        <section class="area-resultado">

            <?php if ($erro): ?>
                <!-- Mensagem de erro -->
                <div class="alerta alerta-erro">
                    <strong>⚠️ Ops!</strong> <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php elseif ($resultado): ?>
                <!-- Resultado encontrado -->
                <div class="alerta alerta-sucesso">
                    <strong>✅ CEP encontrado!</strong>
                </div>

                <div class="cartao">
                    <?php foreach ($resultado as $rotulo => $valor): ?>
                        <div class="linha">
                            <span class="rotulo"><?= htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="valor"><?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($cep === ''): ?>
                <!-- Estado inicial (nenhuma consulta) -->
                <div class="estado-inicial">
                    <div class="icone">🔎</div>
                    <p>Digite um CEP acima e clique em <strong>CONSULTAR</strong> para ver o endereço.</p>
                </div>

            <?php endif; ?>
        </section>

        <!-- Rodapé -->
        <footer class="rodape">
            <p>Desenvolvido com PHP • Dados por <a href="https://viacep.com.br" target="_blank" rel="noopener">ViaCEP</a></p>
        </footer>

    </div>
</body>
</html>
