<?php
/** Ce template affiche la page "monitoring" */
?>

<h2>Outil de monitoring du blog</h2>

<table class="adminTable">
    <thead>
        <tr>
            <th><a href='?action=monitoring&sort=title&order=<?= $order?>'>Titre de l'article <?= $arrowTitle ?></a></th>
            <th><a href='?action=monitoring&sort=views&order=<?= $order?>'>Nombre de vues <?= $arrowViews ?></a></th>
            <th><a href='?action=monitoring&sort=comments&order=<?= $order?>'>Nombre de commentaires <?= $arrowComments ?></a></th>
            <th><a href='?action=monitoring&sort=date&order=<?= $order?>'>Date de publication <?= $arrowDate ?></a></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article) { ?>
            <tr>
                <td><b><?= $article->getTitle() ?></b></td>
                <td><?= $article->getNbViews() ?></td>
                <td><?= $article->getNbComments() ?></td>
                <td><?= Utils::convertDateToFrenchFormat($article->getDateCreation()) ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
