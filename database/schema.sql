-- Schéma de la base de données Trello-like
-- Création des tables

-- Table des tableaux
CREATE TABLE IF NOT EXISTS boards (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des listes
CREATE TABLE IF NOT EXISTS lists (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    board_id INTEGER,
    title TEXT NOT NULL,
    position INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(board_id) REFERENCES boards(id) ON DELETE CASCADE
);

-- Table des cartes
CREATE TABLE IF NOT EXISTS cards (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    list_id INTEGER,
    title TEXT NOT NULL,
    position INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(list_id) REFERENCES lists(id) ON DELETE CASCADE
);

-- Index pour améliorer les performances
CREATE INDEX IF NOT EXISTS idx_lists_board_id ON lists(board_id);
CREATE INDEX IF NOT EXISTS idx_cards_list_id ON cards(list_id);
CREATE INDEX IF NOT EXISTS idx_lists_position ON lists(position);
CREATE INDEX IF NOT EXISTS idx_cards_position ON cards(position);

