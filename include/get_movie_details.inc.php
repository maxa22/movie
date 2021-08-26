<?php
    if(isset($_GET['id'])) {
        require_once('autoloader.php');
        $id = Sanitize::sanitizeString($_GET['id']);
        Validate::isNumber('id', $id);
        if(Message::getError()) {
            echo json_encode(array('error' => 'error'));
            exit();
        }
        $movie = Movie::findById($id);
        if(empty($movie)) {
            echo json_encode(array('error' => 'error'));
            exit();
        }
        echo json_encode($movie);
        exit();
    } else {
        header('Location: ../');
        exit();
    }

?>