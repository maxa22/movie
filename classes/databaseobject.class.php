<?php

    /**
     * Class communicating with database
     */

    class DatabaseObject {

        /**
         * get properties from class instance static database fields
         * use the fields as array keys and class instance attributes as values
         *
         * @return array
         */
        protected function properties() {

            $properties = array();
            foreach (static::$dbFields  as $dbField) {
                if(property_exists($this, $dbField)) {
                    $properties[$dbField] = $this->$dbField;
                }   
            }
            return $properties;
        }

        /**
         * if object id is not set save the object to database
         * if object id is set update the object
         */
        public function save() {
            if(!$this->id) {
                $this->create();
            } else {
                $this->update();
            }
        }


        /**
         * Create new row in database
         * getting static object properties
         * inserting in database table 
         *
         * @return void
         */
        public function create() {
            try 
            {
                $properties = $this->properties();
                $database = Database::instance();
                $connection = $database->connect();
                $placeholders = implode(',:', array_keys($properties));
                $placeholders = ':' . $placeholders;
                $sql = "INSERT INTO " . static:: $dbTable . " (" . implode(', ', array_keys($properties)) . ") VALUES (" . $placeholders . ")";
                $stmt = $connection->prepare($sql);
                $stmt->execute($properties);
                $this->id = $connection->lastInsertId();
                return;
            } 
            catch (PDOException $e)
            {
                // uncomment only in development
                // Message::addError('error', $e->getMessage());
            }
            
        }

        /**
         * updating database table row
         * removing last item from object properties, should be foreign key
         * if updating object with no foreign key insert if statement before array_pop
         *
         * @return void
         */
        public function update() {
            try 
            {
                $properties = $this->properties();
                
                $database = Database::instance();
                $connection = $database->connect();
                $placeholders = str_repeat('?, ', count($properties) - 1) . '?';
                
                $properties_pairs = array();
                foreach ($properties as $key => $value) {
                    $properties_pairs[] = "{$key}=:{$key}";
                }

                $sql = "UPDATE " . static:: $dbTable . " SET " . implode(',', $properties_pairs) . " WHERE id=:id";
                $stmt = $connection->prepare($sql);
                $properties['id'] = $this->id;
                $stmt->execute($properties);
                return;
            }
            catch (PDOException $e)
            {
                // Message::addError('error', $e->getMessage());
            }
        }


        /**
         * find element from database where id is equal to provided id param
         *
         * @param [int] $id
         * @return array
         */
        public static function findById($id) {
            try 
            {
                $database = Database::instance();
                $connection = $database->connect();

                $sql = "SELECT * FROM " . static::$dbTable . " WHERE id = ?;";
                $stmt = $connection->prepare($sql);
                $stmt->bindParam('1', $id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result;
            }
            catch(PDOException $e) 
            {
                // Message::addError('error', $e->getMessage());
            }
        }

        /**
         * find element from database where id is equal to provided id param
         *
         * @param [int] $id
         * @return array
         */
        public static function findAll($order = 'DESC', $limit = '', $offset) {
            try 
            {
                if(!in_array($order, array('ASC', 'DESC'))) {
                    exit();
                }
                $database = Database::instance();
                $connection = $database->connect();
                if($limit) {
                    $sql = "SELECT * FROM " . static::$dbTable . " ORDER BY id $order LIMIT :limit OFFSET :offset;";
                } else {
                    $sql = "SELECT * FROM " . static::$dbTable . " ORDER BY id $order;";
                }
                $stmt = $connection->prepare($sql);
                if($limit) {
                    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
                }
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }
            catch(PDOException $e) 
            {
                // Message::addError('error', $e->getMessage());
            }
        }

        

        /**
         * Get number of rows from db table
         *
         * @return int
         */ 
        public static function countRows() {
            try 
            {
                $database = Database::instance();
                $connection = $database->connect();
                $sql = "SELECT COUNT(*) FROM " . static::$dbTable . ";";
                $stmt = $connection->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchColumn();
                return $rows;
            }
            catch(PDOException $e)
            {
                // Message::addError('error', $e->getMessage());
            }

        }

        public static function countRowsWithParam($name) {
            try 
            {
                $database = Database::instance();
                $connection = $database->connect();
                $sql = "SELECT COUNT(*) FROM " . static::$dbTable . " WHERE name LIKE :needle;";
                $stmt = $connection->prepare($sql);
                $needle = '%' . $name . '%';
                $stmt->bindValue(':needle', $needle);
                $stmt->execute();
                $rows = $stmt->fetchColumn();
                return $rows;
            }
            catch(PDOException $e)
            {
                // Message::addError('error', $e->getMessage());
            }
        }
        

        /**
         * return class instance id
         *
         * @return int
         */
        public function getId() {
            return $this->id;
        }

    } //end of class