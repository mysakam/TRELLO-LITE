<?php
require_once __DIR__ . '/../database/init.php';

class Database
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = initDatabase();
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    // Méthode pour exécuter du SQL brut (utile pour les migrations)
    public function executeSQL($sql)
    {
        return $this->pdo->exec($sql);
    }

    // Méthode pour vérifier si une table existe
    public function tableExists($tableName)
    {
        $stmt = $this->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name=?",
            [$tableName]
        );
        return $stmt->fetch() !== false;
    }

    // Méthode pour réinitialiser la base de données
    public function resetDatabase()
    {
        $tables = ['cards', 'lists', 'boards'];
        foreach ($tables as $table) {
            $this->query("DELETE FROM $table");
        }
        $this->query("DELETE FROM sqlite_sequence WHERE name IN ('boards', 'lists', 'cards')");
    }

    // Méthodes métier existantes
    public function getBoards()
    {
        return $this->query("SELECT * FROM boards ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBoard($id)
    {
        $stmt = $this->query("SELECT * FROM boards WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLists($board_id)
    {
        return $this->query("
            SELECT l.*, 
                   (SELECT COUNT(*) FROM cards WHERE list_id = l.id) as card_count
            FROM lists l 
            WHERE l.board_id = ? 
            ORDER BY l.position
        ", [$board_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCardsByList($list_id)
    {
        return $this->query("SELECT * FROM cards WHERE list_id = ? ORDER BY position", [$list_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBoard($title)
    {
        $this->query("INSERT INTO boards (title) VALUES (?)", [$title]);
        return $this->lastInsertId();
    }

    public function createList($title, $board_id)
    {
        $maxPos = $this->query("SELECT COALESCE(MAX(position), 0) as max_pos FROM lists WHERE board_id = ?", [$board_id])
            ->fetch(PDO::FETCH_ASSOC)['max_pos'];
        $this->query("INSERT INTO lists (title, board_id, position) VALUES (?, ?, ?)", [$title, $board_id, $maxPos + 1]);
        return $this->lastInsertId();
    }

    public function createCard($title, $list_id)
    {
        $maxPos = $this->query("SELECT COALESCE(MAX(position), 0) as max_pos FROM cards WHERE list_id = ?", [$list_id])
            ->fetch(PDO::FETCH_ASSOC)['max_pos'];
        $this->query("INSERT INTO cards (title, list_id, position) VALUES (?, ?, ?)", [$title, $list_id, $maxPos + 1]);
        return $this->lastInsertId();
    }

    public function moveCard($card_id, $new_list_id)
    {
        $this->query("UPDATE cards SET list_id = ? WHERE id = ?", [$new_list_id, $card_id]);

        $stmt = $this->query("SELECT board_id FROM lists WHERE id = ?", [$new_list_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['board_id'] : null;
    }

    // Statistiques de la base de données
    public function getStats()
    {
        $stats = [];

        $stmt = $this->query("SELECT COUNT(*) as count FROM boards");
        $stats['boards'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        $stmt = $this->query("SELECT COUNT(*) as count FROM lists");
        $stats['lists'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        $stmt = $this->query("SELECT COUNT(*) as count FROM cards");
        $stats['cards'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $stats;
    }
}
