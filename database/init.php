<?php
function initDatabase()
{
    $dbPath = __DIR__ . '/trello.db';
    $schemaPath = __DIR__ . '/schema.sql';

    // Vérifier si la base de données existe déjà
    $dbExists = file_exists($dbPath);

    // Connexion à SQLite
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Si la base n'existe pas ou est vide, initialiser avec le schéma
    if (!$dbExists || filesize($dbPath) == 0) {
        $sql = file_get_contents($schemaPath);
        if ($sql) {
            $pdo->exec($sql);
            error_log("Base de données initialisée avec le schéma SQL");
        } else {
            error_log("Erreur: Impossible de lire le fichier schema.sql");
        }
    }

    return $pdo;
}

/**
 * Fonction utilitaire pour exporter la base en SQL
 */
function exportDatabaseToSQL()
{
    $dbPath = __DIR__ . '/trello.db';
    $exportPath = __DIR__ . '/export_' . date('Y-m-d_H-i-s') . '.sql';

    if (!file_exists($dbPath)) {
        return false;
    }

    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = ['boards', 'lists', 'cards'];
    $sqlContent = "-- Export de la base de données Trello-like\n";
    $sqlContent .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($tables as $table) {
        // Structure de la table
        $stmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$table'");
        $createTable = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($createTable) {
            $sqlContent .= $createTable['sql'] . ";\n\n";
        }

        // Données de la table
        $stmt = $pdo->query("SELECT * FROM $table");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $sqlContent .= "-- Données pour la table: $table\n";

            foreach ($rows as $row) {
                $columns = implode(', ', array_keys($row));
                $values = implode("', '", array_map(function ($value) {
                    return str_replace("'", "''", $value);
                }, $row));

                $sqlContent .= "INSERT INTO $table ($columns) VALUES ('$values');\n";
            }
            $sqlContent .= "\n";
        }
    }

    file_put_contents($exportPath, $sqlContent);
    return $exportPath;
}
