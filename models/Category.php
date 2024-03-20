<?php
    class Category {
        // DB stuff
        private $conn;
        private $table = 'categories';

        // Post Properties
        public $id;
        public $category;

        // Constructor with DB
        public function __construct($db) {
            $this->conn = $db;
        }

        public function read() {
            //  Create query
            $query = 'SELECT * FROM ' . $this->table . ' ORDER BY id';

            // Prepare statement
            $stmt = $this->conn->prepare($query);
            // Execute query
            $stmt->execute();

            return $stmt;
        }

        public function read_single() {
            //  Create query
            $query = 'SELECT *
                FROM
                    ' . $this->table . '
                WHERE
                    id = ?';
        
            // Prepare statement
            $stmt = $this->conn->prepare($query);
        
            // Bind ID
            $stmt->bindParam(1, $this->id);
        
            //Execute query
            $stmt->execute();
        
            // Check if the query returned any rows
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // Set properties
                $this->category = $row['category'];
                return true;
            } else {
                // Handle the case where the ID is not found
                echo json_encode(array('message' => 'category_id Not Found'));
                return false;
            }

        }

        // Create category
        public function create() {
            // Create query
            $query = 'INSERT INTO ' . $this->table . ' (category) VALUES (:category)';
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data            
            $this->category = htmlspecialchars(strip_tags($this->category));

            // Bind data
            $stmt->bindParam(':category', $this->category);

            // Execute query
            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }

            // Print error if sth goes wrong
            printf("Error: %s.\n", $stmt->error);

            return false;
        }

        // Update Post
        public function update() {
            // Update query
            $query = 'UPDATE ' .
                $this->table . ' 
              SET
                category = :category
              WHERE 
                id = :id';
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':id', $this->id);

            // Execute query
            if($stmt->execute()) {
                return true;
            }

            // Print error if sth goes wrong
            printf("Error: %s.\n", $stmt->error);

            return false;
        }

        // Delete post
        public function delete() {
            try {
                // Create query
                $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

                // Prepare statement
                $stmt = $this->conn->prepare($query);

                //Clean data
                $this->id = htmlspecialchars(strip_tags($this->id));

                // Bind data
                $stmt->bindParam(':id', $this->id);

                // Execute query
                if($stmt->execute()) {
                    return true;
                }
            } catch (PDOException $e) {
                // Print error message
                echo json_encode(array('message' => 'Error: ' . $e->getMessage()));
                return false;
            } catch (Exception $e) {
                // Print error message
                echo json_encode(array('message' => $e->getMessage()));
                return false;
            }

        }
    }