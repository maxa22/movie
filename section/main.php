<?php

/*** Function for loading the page ***/
function load_page($page) {
    // Check is page empty. If it is, load the default page
    if($page === ''){
        require 'pages/index.php';
        return;
    }
    // Create whitelist so nonexisting page cannot be loaded, and pages with third argument can be loaded without problems
    switch($page){
        // Normal pages in whitelist
        case 'index':
        case 'add_movie':
        case 'movie':
            require 'pages/' . $page . '.php';
        break;
        default:
            // Load index by default
            require 'pages/index.php';
        return;
    }
}

if(isset($_GET['page']) && !empty($_GET['page'])){
    /*** Call loading function from includes/functions.php ***/
    load_page($_GET['page']);
} else {
    require 'pages/index.php';
}

?>