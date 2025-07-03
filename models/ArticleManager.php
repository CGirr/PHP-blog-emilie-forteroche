<?php

/**
 * Classe qui gère les articles.
 */
class ArticleManager extends AbstractEntityManager 
{
    /**
     * Récupère tous les articles.
     * @return array : un tableau d'objets Article.
     */
    public function getAllArticles() : array
    {
        $sql = "SELECT * FROM article";
        $result = $this->db->query($sql);
        $articles = [];

        while ($article = $result->fetch()) {
            $articles[] = new Article($article);
        }
        return $articles;
    }

    /**
     * Récupère tous les articles ainsi que le nombre de commentaires associés.
     * @return array
     * @throws Exception
     */
    public function getAllArticlesWithCommentsCount() : array
    {
        $sql = "SELECT article.id, article.title, article.date_creation, article.nb_views, COUNT(comment.id) AS nb_comments FROM article LEFT JOIN comment on article.id = comment.id_article GROUP BY article.id";

        if (isset($_GET['sort'] ) && isset($_GET['order'])) {

            //On restreint les valeurs possibles pour éviter les injections SQL, on lève une exception sinon
            $orderMap = ["asc" => "asc", "desc" => "desc", ];
            $sortingOrder = $orderMap[$_GET['order']] ?? throw new Exception("Invalid order parameter");
            $columnMap = ["title" => "title", "nb_views" => "nb_views", "nb_comments" => "nb_comments", "date_creation" => "date_creation"];
            $sortingColumn = $columnMap[$_GET['sort']] ?? throw new Exception("Invalid column parameter");

            //On ajoute le tri à la requête SQL
            //La boucle sert à gérer le cas où l'on clique sur la colonne nombre de commentaires
            if($_GET['sort'] == 'nb_comments') {
                $sql .= " ORDER BY nb_comments" . " " . $sortingOrder;
            } else {
                $sql .= " ORDER BY article." . $sortingColumn . " " . $sortingOrder;
            }
        }  else {
            $sql = $sql;
        }

        $result = $this->db->query($sql);
        $articles = [];

        while ($data = $result->fetch()) {
            $articles[] = new Article($data);
        }
        return $articles;
    }
    
    /**
     * Récupère un article par son id.
     * @param int $id : l'id de l'article.
     * @return Article|null : un objet Article ou null si l'article n'existe pas.
     */
    public function getArticleById(int $id) : ?Article
    {
        $sql = "SELECT * FROM article WHERE id = :id";
        $result = $this->db->query($sql, ['id' => $id]);
        $article = $result->fetch();
        if ($article) {
            return new Article($article);
        }
        return null;
    }

    /**
     * Ajoute ou modifie un article.
     * On sait si l'article est un nouvel article car son id sera -1.
     * @param Article $article : l'article à ajouter ou modifier.
     * @return void
     */
    public function addOrUpdateArticle(Article $article) : void 
    {
        if ($article->getId() == -1) {
            $this->addArticle($article);
        } else {
            $this->updateArticle($article);
        }
    }

    /**
     * Ajoute un article.
     * @param Article $article : l'article à ajouter.
     * @return void
     */
    public function addArticle(Article $article) : void
    {
        $sql = "INSERT INTO article (id_user, title, content, date_creation, date_update) VALUES (:id_user, :title, :content, NOW(), NOW())";
        $this->db->query($sql, [
            'id_user' => $article->getIdUser(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ]);
    }

    /**
     * Modifie un article.
     * @param Article $article : l'article à modifier.
     * @return void
     */
    public function updateArticle(Article $article) : void
    {
        $sql = "UPDATE article SET title = :title, content = :content, date_update = NOW() WHERE id = :id";
        $this->db->query($sql, [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'id' => $article->getId()
        ]);
    }

    /**
     * Supprime un article.
     * @param int $id : l'id de l'article à supprimer.
     * @return void
     */
    public function deleteArticle(int $id) : void
    {
        $sql = "DELETE FROM article WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }

    /**
     * Augmente le nombre de vues de l'article
     * @param int $id
     * @return void
     */
    public function incrementViews(int $id) : void
    {
        $sql = "UPDATE article SET nb_views = nb_views + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
    }
}