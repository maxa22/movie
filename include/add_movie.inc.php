<?php

    if(isset($_POST['submit'])) {

        require_once('autoloader.php');

        $description = Sanitize::sanitizeString($_POST['desc']);
        $name = Sanitize::sanitizeString($_POST['name']);
        Validate::isEmpty('name', $name);
        Validate::validatelength('name', $name, 50);
        Validate::isEmpty('desc', $description);
        Validate::isValidFile('image', 'image');
        if(Message::getError()) {
            echo json_encode(Message::getError());
            exit();
        }

        $image = $_FILES['image']['error'] !== 4 ? new Image('image') : '';
        $imagePath = $image !== '' ? $image -> imagePath : '';

        $movie = new Movie($name, $description, $imagePath);
        $movie->save();

        echo json_encode(Message::getError());
        exit();

    } else {
        header('Location: ../');
    }