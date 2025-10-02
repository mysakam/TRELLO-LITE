<?php ob_start(); ?>
<div class="board-container">
    <header class="board-header">
        <h1><?= htmlspecialchars($board['title']) ?></h1>
        <a href="/" class="btn-secondary">← Retour aux tableaux</a>
    </header>

    <div class="lists-container">
        <?php foreach ($lists as $list): ?>
            <div class="list">
                <div class="list-header">
                    <h3><?= htmlspecialchars($list['title']) ?> (<?= $list['card_count'] ?>)</h3>
                </div>

                <div class="cards-container">
                    <?php foreach ($cards[$list['id']] as $card): ?>
                        <div class="card">
                            <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
                            <form method="POST" action="/move-card" class="move-form">
                                <input type="hidden" name="card_id" value="<?= $card['id'] ?>">
                                <select name="new_list_id" onchange="this.form.submit()">
                                    <option value="">Déplacer vers...</option>
                                    <?php foreach ($lists as $targetList): ?>
                                        <?php if ($targetList['id'] != $list['id']): ?>
                                            <option value="<?= $targetList['id'] ?>"><?= htmlspecialchars($targetList['title']) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form method="POST" action="/create-card" class="add-card-form">
                    <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                    <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                    <input type="text" name="title" placeholder="Ajouter une carte..." required>
                    <button type="submit" class="btn-primary">+</button>
                </form>
            </div>
        <?php endforeach; ?>

        <div class="list add-list">
            <form method="POST" action="/create-list" class="add-list-form">
                <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                <input type="text" name="title" placeholder="Nouvelle liste..." required>
                <button type="submit" class="btn-primary">+ Ajouter une liste</button>
            </form>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>
<?php require 'layout.php'; ?>