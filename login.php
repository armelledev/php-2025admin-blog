<?php

session_start();
require_once 'database/database.php';

$pageTitle = 'Page  login';

$errors = [];
if (isset($_POST['login'])) {
    if (! empty($_POST['email']) && ! empty($_POST['password'])) {
        $query = 'SELECT * FROM users WHERE (email = :email OR username = :email)';

        $req = $pdo->prepare($query);
        $req->execute([
            'email' => $_POST['email'],
            'password' => $_POST['password'],
        ]);

        $user = $req->fetch();

        if ($user && password_verify($_POST['password'], $user['password'])) {

            $_SESSION['auth'] = $user;
            $_SESSION['auth'] = $user['role'];

            switch ($user['role']) {
                case 'admin':
                    header('location: admin-dashboard.php');
                    break;
                default:
                    header('location: index.php');
                    break;
            }
        } else {
            $errors['email'] = 'email ou mot de passe incorrect';
        }

    } else {
        $errors['login'] = 'tous les champ doivent etre  remplis';

    }

}

// Début du tampon de la page de sortie
ob_start();

// Inclure le layout de la page d'accueil
require_once 'resources/views/users/login_html.php';

// Récupération du contenu du tampon de la page d'accueil
$pageContent = ob_get_clean();
// resources/fonts/OperatorMono-LightItalic.otf ;

// Inclure le layout de la page de sortie
require_once 'resources/views/layouts/blog-layout/blog-layout_html.php';
