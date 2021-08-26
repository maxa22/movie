<?php

    class Movie extends DatabaseObject {
        protected static $dbTable = 'movie';
        protected static $dbFields = array('name', 'image', 'description');
        protected $id;
        public $name;
        public $image;
        public $description;
 
        public function __construct($name, $description, $image) {
            $this->id =    $args['id'] ?? '';
            $this->name =  $name;
            $this->description =  $description;
            $this->image = $image;
        }

        public static function findAllWhereNameLike($order = 'DESC', $limit = '', $offset, $name) {
            try 
            {
                if(!in_array($order, array('ASC', 'DESC'))) {
                    exit();
                }
                $database = Database::instance();
                $connection = $database->connect();
                if($name) {
                    $sql = "SELECT * FROM " . static::$dbTable . " WHERE name LIKE :needle ORDER BY id $order LIMIT :limit OFFSET :offset;";
                } else {
                    $sql = "SELECT * FROM " . static::$dbTable . " ORDER BY id $order LIMIT :limit OFFSET :offset;";
                }
                $stmt = $connection->prepare($sql);
                if($name) {
                    $needle = '%' . $name . '%';
                    $stmt->bindValue(':needle', $needle);
                }
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
            catch(PDOException $e) 
            {
                // Message::addError('error', $e->getMessage());
            }
        }
    }