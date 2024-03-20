<?php
    class Author {
        // DB stuff
        private $conn;
        private $table = 'authors';

        // Post Properties
        public $id;
        public $author;

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
        
        // public function read() {
        //     //  Create query
        //     $query = 'SELECT a.id, a.author, 
        //         JSON_AGG(JSON_BUILD_OBJECT("quote", q.quote,
        //                                    "category", c.category)) AS quotes 
        //         FROM ' . $this->table . ' a
        //         LEFT JOIN
        //             quotes q ON a.id = q.author_id
        //         LEFT JOIN 
        //             categories c ON c.id = q.category_id
        //         GROUP BY a.id, a.author
        //         ORDER BY a.id';

        //     // Prepare statement
        //     $stmt = $this->conn->prepare($query);
        //     // Execute query
        //     $stmt->execute();

        //     return $stmt;
        // }

        
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
                $this->author = $row['author'];
                return true;
            } else {
                // Handle the case where the ID is not found
                echo json_encode(array('message' => 'author id not found'));
                return false;
            }

        }
        

        // public function read_single() {
        //     try {
        //         if (empty($this->id)) {
        //             throw new Exception('ID cannot be null.');
        //         }
        //         //  Create query
        //         $query = 'SELECT *
        //         FROM
        //             ' . $this->table . '
        //         WHERE
        //             id = ?';

        //         // Prepare statement
        //         $stmt = $this->conn->prepare($query);

        //         // Bind ID
        //         $stmt->bindParam(1, $this->id);

        //         //Execute query
        //         $stmt->execute();

        //         $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //         // Set properties
        //         $this->author = $row['author'];
        //     } catch (Exception $e) {
        //         // Print error message
        //         echo json_encode(array('message' => $e->getMessage()));
        //         return false;
        //     }

        // }

        // Create Author
        public function create() {
            // Create query
            $query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:author)';
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data            
            $this->author = htmlspecialchars(strip_tags($this->author));

            // Bind data
            $stmt->bindParam(':author', $this->author);

            // Execute query
            if($stmt->execute()) {
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
                author = :author
              WHERE 
                id = :id';
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->author = htmlspecialchars(strip_tags($this->author));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':author', $this->author);
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