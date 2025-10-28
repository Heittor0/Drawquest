<?php
// config.php - Conexão compatível com Neon + fallback sem chamar pg_last_error indevidamente

$databaseUrl = getenv('DATABASE_URL');

// fallback local (apenas se você ainda não definiu env) - opcional, remova se preferir
if (!$databaseUrl) {
    // Atenção: não deixe credenciais hardcoded no repositório em produção
    $databaseUrl = 'postgresql://neondb_owner:npg_xZdsYvp34nNJQ@ep-green-salad-aderih8r-pooler.c-2.us-east-1.aws.neon.tech/loja_database?sslmode=require';
}

function addEndpointOptionToUrl($url, $endpoint_id) {
    $sep = (strpos($url, '?') === false) ? '?' : '&';
    return $url . $sep . 'options=endpoint%3D' . rawurlencode($endpoint_id);
}

$parts = parse_url($databaseUrl);
if ($parts === false || !isset($parts['host'])) {
    die("DATABASE_URL inválida. Verifique a variável de ambiente no Render (cole a string completa do Neon).");
}

$host = $parts['host'];
$port = isset($parts['port']) ? $parts['port'] : 5432;
$user = isset($parts['user']) ? $parts['user'] : null;
$password = isset($parts['pass']) ? $parts['pass'] : null;
$dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : null;

// endpoint_id: parte antes de "-pooler" se existir, caso contrário primeira label do host
if (strpos($host, '-pooler') !== false) {
    $endpoint_id = substr($host, 0, strpos($host, '-pooler'));
} else {
    $endpoint_id = explode('.', $host)[0];
}

// Tenta conexão PDO (ideal para Render/dockers atualizados)
$dsn_pdo = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

try {
    $pdo = new PDO($dsn_pdo, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    // $pdo disponível para uso no resto do app
    // echo "Conexão PDO OK";
    return;
} catch (PDOException $e) {
    $msg = $e->getMessage();

    // Se for problema de host truncado (ex.: '...') - mensagem clara
    if (stripos($msg, 'could not translate host name') !== false || strpos($host, '...') !== false) {
        die("Erro DNS/host: verifique se DATABASE_URL contém o host COMPLETO (sem '...') no painel do Render.");
    }

    // Se for erro de endpoint/SNI ou inconsistent project name -> tentar fallback com options=endpoint
    if (stripos($msg, 'Endpoint ID is not specified') !== false
        || stripos($msg, 'inconsistent project name') !== false
        || stripos($msg, 'SNI') !== false) {

        // cria URL com options=endpoint%3D...
        $url_with_options = addEndpointOptionToUrl($databaseUrl, $endpoint_id);

        // tenta conectar via pg_connect (bom para clientes libpq mais velhos)
        $conn = @pg_connect($url_with_options);

        if ($conn) {
            // Conexão via pg_connect bem-sucedida
            // Defina uma variável global se precisar usar pg_* functions
            $GLOBALS['pg_conn'] = $conn;
            return;
        } else {
            // pg_connect falhou — não chamamos pg_last_error sem conexão (evita fatal)
            die("Fallback com options=endpoint falhou. Verifique suas credenciais e se usou a string EXATA da Neon.");
        }
    }

    // Se for erro de autenticação de senha
    if (stripos($msg, 'password authentication failed') !== false) {
        die("Erro na conexão com o banco de dados: senha inválida. Verifique a variável DATABASE_URL no Render e/ou redefina a senha no Neon.");
    }

    // Caso geral
    die("Erro na conexão com o banco de dados (PDO): " . $msg);
}
?>
