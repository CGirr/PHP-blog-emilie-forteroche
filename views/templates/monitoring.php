<?php
/** Ce template affiche la page "monitoring" */
?>

<h2>Outil de monitoring du blog</h2>

<table class="adminTable">
    <thead>
        <tr>
            <th>Titre de l'article</th>
            <th>Nombre de vues</th>
            <th>Nombre de commentaires</th>
            <th>Date de publication</th>
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
