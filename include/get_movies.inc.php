<?php
    if(isset($_GET['id'])) {

        require_once('autoloader.php');
        $id = Sanitize::sanitizeString($_GET['id']);
        Validate::isNumber('id', $id);
        if(Message::getError()) {
            echo json_encode(array());
            exit();
        }
        $name = Sanitize::sanitizeString($_GET['search']);
        $limit = 10;
        if($name === '') {
            $rowsCount = Movie::countRows();
            $offset = ($id - 1) * 10;
        } else {
            $rowsCount = Movie::countRowsWithParam($name);
            $offset = 0;
        }
        if(isset($_GET['paginate'])) {
            $offset = ($id - 1) * 10;
        }
        $pages = ceil($rowsCount / 10);
        $response = array();
        $response['numberOfPages'] = $pages;
        $response['movies'] = Movie::findAllWhereNameLike('DESC', $limit, $offset, $name);
        echo json_encode($response);
        exit();

    } else {

    }

?>