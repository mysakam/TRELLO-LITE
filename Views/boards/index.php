<?php ob_start(); ?>
<div class="container">
    <header>
        <h1>Mes Tableaux</h1>
        <form method="POST" action="/create-board" class="inline-form">
            <input type="text" name="title" placeholder="Nom du tableau" required>
            <button type="submit" class="btn-primary">Créer</button>
        </form>
    </header>

    <div class="boards-grid">
        <?php foreach ($boards as $board): ?>
            <a href="/board/<?= $board['id'] ?>" class="board-card">
                <h3><?= htmlspecialchars($board['title']) ?></h3>
                <span class="board-date"><?= date('d/m/Y', strtotime($board['created_at'])) ?></span>
            </a>
        <?php endforeach; ?>

        <?php if (empty($boards)): ?>
            <div class="empty-state">
                <p>Aucun tableau créé pour le moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require 'layout.php'; ?>