<?php 
/**
 * Contrôleur de la partie admin.
 */
 
class AdminController
{
    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        Utils::checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm() : void 
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     * @throws Exception
     */
    public function connectUser() : void 
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser() : void 
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        Utils::checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Affichage de la page de monitoring
     * @return void
     * @throws Exception
     */
    public function showMonitoring() : void
    {
        Utils::checkIfUserIsConnected();

        // On récupère tous les articles avec leur nombre de commentaires
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticlesWithCommentsCount();

        // Boucle if permettant d'alterner l'ordre de tri
        $order = 'asc';
        if (isset($_GET['order']) && $_GET['order'] === 'asc') {
            $order = 'desc';
        }

        // Switch permettant de changer le sens du triangle qui s'affiche en en-tête en fonction de l'ordre de tri
        $arrowTitle = $arrowViews = $arrowComments = $arrowDate = '&#9652;/&#9662;';
        if (isset($_GET['sort']) && isset($_GET['order'])) {
            switch ([$_GET['sort'], $_GET['order']]) {
                case ['title', 'asc'] :
                    $arrowTitle = '&#9652;';
                    break;
                case ['title', 'desc'] :
                    $arrowTitle = '&#9662;';
                    break;
                case ['nb_views', 'asc'] :
                    $arrowViews = '&#9652;';
                    break;
                case ['nb_views', 'desc'] :
                    $arrowViews = '&#9662;';
                    break;
                case ['nb_comments', 'asc'] :
                    $arrowComments = '&#9652;';
                    break;
                case ['nb_comments', 'desc'] :
                    $arrowComments = '&#9662;';
                    break;
                case ['date', 'asc'] :
                    $arrowDate = '&#9652;';
                    break;
                case ['date', 'desc'] :
                    $arrowDate = '&#9662;';
                    break;
            }
        }

        // On affiche la page de monitoring
        $view = new View("Monitoring");
        $view->render("monitoring", [
            'articles' => $articles,
            'order' => $order,
            'arrowTitle' => $arrowTitle,
            'arrowViews' => $arrowViews,
            'arrowComments' => $arrowComments,
            'arrowDate' => $arrowDate
        ]);
    }

    /**
     * Ajout et modification d'un article.
     * On sait si un article est ajouté, car l'id vaut -1.
     * @return void
     * @throws Exception
     */
    public function updateArticle() : void 
    {
        Utils::checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        Utils::checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }
}