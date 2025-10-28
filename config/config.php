<?php
// config.php - Conexão PDO segura com Neon (Render)
$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
    // fallback local (opcional)
    $host = 'ep-green-salad-aderih8r-pooler.c-2.us-east-1.aws.neon.tech';
    $port = 5432;
    $user = 'neondb_owner';
    $password = 'npg_xZdsYvp34nNJ'; // <== preferível usar variável de ambiente
    $dbname = 'loja_database';
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";
    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados (fallback): " . $e->getMessage());
    }
} else {
    // Se DATABASE_URL estiver no formato postgresql://user:pass@host:port/db?params
    $parts = parse_url($databaseUrl);

    if ($parts === false || !isset($parts['host'])) {
        die('DATABASE_URL inválida.');
    }

    $host = $parts['host'];
    $port = isset($parts['port']) ? $parts['port'] : 5432;
    $user = isset($parts['user']) ? $parts['user'] : null;
    $password = isset($parts['pass']) ? $parts['pass'] : null;
    $dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : null;

    // monta DSN sem options conflitantes (removemos qualquer "options='endpoint=...'" manual)
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        //echo "Conectado com sucesso!";
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}
?>
