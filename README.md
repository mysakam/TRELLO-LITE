# Trello Lite (version simplifiée)

Petite application web en PHP MVC pour gérer des tâches façon Trello.

## Fonctionnalités

- 3 colonnes : À faire, En cours, Terminé
- Ajouter / Supprimer / Déplacer des tâches
- Interface simple

## Lancer le projet

1. Crée la base SQLite :
   ```sql
   CREATE TABLE tasks (
       id INTEGER PRIMARY KEY AUTOINCREMENT,
       title TEXT NOT NULL,
       status TEXT NOT NULL
   );
   ```
