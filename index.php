<?php
require_once 'database/database.php';

$pageTitle = "page d'accueil du blog";

// debut de tampon de la page desortir
ob_start();

// recupere la view (layout) de lapage d'acceuil
require_once 'resources/views/blog/index_html.php';

// recuperer le contenus du tampon de la page
$pageContent = ob_get_clean();

require_once 'resources/views/layouts/blog-layout/blog-layout_html.php';
?>


