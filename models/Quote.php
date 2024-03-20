<?php
class Quote {
    // DB stuff
    private $conn;
    private $table = 'quotes';

    // Post Properties
    public $id;
    public $quote;
    public $author_id; // Added author_id property
    public $category_id; // Added category_id property

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        // Create query
        $query = 'SELECT q.id, q.quote, a.author, c.category
            FROM ' . $this->table . ' q
            INNER JOIN authors a ON a.id = q.author_id
            INNER JOIN categories c ON c.id = q.category_id
            ORDER BY q.id';
    
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        // Get row count
        $num = $stmt->rowCount();

        // check if any posts
        if($num > 0) {
            // Post Array
            $quotes_arr = array();
            $quotes_arr['data'] = array();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $quote_item = array(
                'id' => $id,
                'quote' => $quote,
                'author' => $author,
                'category' => $category
                );

                // Push to "data"
                array_push($quotes_arr['data'], $quote_item);
            }

            // Turn to JSON & output
            return json_encode($quotes_arr);
        }

        return json_encode([]);
    }

    public function read_single($type) {
        // Create query based on type
        $stmt = null;
        if ($type === "id") {
            $query = 'SELECT q.id, q.quote, a.author, c.category
                FROM ' . $this->table . ' q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
                WHERE q.id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
        } else if ($type === "author_id") {
            $query = 'SELECT q.id, q.quote, a.author, c.category
                FROM ' . $this->table . ' q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
                WHERE q.author_id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->author_id);
        } else if ($type === "category_id") {
            $query = 'SELECT q.id, q.quote, a.author, c.category
                FROM ' . $this->table . ' q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
                WHERE q.category_id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->category_id);
        } else if ($type === "author_id_category_id") {
            $query = 'SELECT q.id, q.quote, a.author, c.category
                FROM ' . $this->table . ' q
                INNER JOIN authors a ON a.id = q.author_id
                INNER JOIN categories c ON c.id = q.category_id
                WHERE q.author_id = ? AND q.category_id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->author_id);
            $stmt->bindParam(2, $this->category_id);
        }

        // Execute query
        $stmt->execute();

        // Check if the query returned any rows
        // echo ($stmt->rowCount());
        if ($stmt->rowCount() === 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $category_item = array(
                'id' =>$row['id'], // use this
                'quote' => $row['quote'],
                'author_id' => $row['author'],
                'category_id' => $row['category']
              );
            // Make JSON
            return json_encode($category_item);
           
        } else {
            // return array, logic in read()
            // Post Array
            $quotes_arr = array();
            $quotes_arr['data'] = array();
            if ($stmt->rowCount() < 2) {
                $quotes_arr['error'] = array('message' => 'No quote found');
            }
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $quote_item = array(
                'id' => $id,
                'quote' => $quote,
                'author' => $author,
                'category' => $category
                );

                // Push to "data"
                array_push($quotes_arr['data'], $quote_item);
            }
            return $quotes_arr;
        }
    }


    // Create Quote
    public function create() {
        // Create query
        $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)';
        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data            
        $this->quote = htmlspecialchars(strip_tags($this->quote));
        $this->author_id = htmlspecialchars(strip_tags($this->author_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        // Bind data
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);

        return false;
    }
    // Update Quote
    public function update() {
        try {
            // Check if the author_id exists in the authors table
            $authorExistsQuery = 'SELECT COUNT(*) as count FROM authors WHERE id = :author_id';
            $authorExistsStmt = $this->conn->prepare($authorExistsQuery);
            // clean data
            
            $authorExistsStmt->bindParam(':author_id', $this->author_id);
            $authorExistsStmt->execute();
            $authorExistsResult = $authorExistsStmt->fetch(PDO::FETCH_ASSOC);

            if ($authorExistsResult['count'] == 0) {
                throw new Exception('Author with id ' . $this->author_id . ' does not exist.');
            }

            // Update query
            $query = 'UPDATE ' . $this->table . ' 
                    SET quote = :quote, author_id = :author_id, category_id = :category_id
                    WHERE id = :id';
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->author_id = (int) $this->author_id; // Ensure it's an integer
            $this->category_id = (int) $this->category_id; // Ensure it's an integer
            $this->id = (int) $this->id; // Ensure it's an integer

            // Bind data
            $stmt->bindParam(':quote', $this->quote);
            $stmt->bindParam(':author_id', $this->author_id);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':id', $this->id);

            // Execute query
            if($stmt->execute()) {
                return true;
            } else {
                throw new Exception('Failed to execute the update query.');
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



    // Delete Quote
    public function delete() {
        try {
            // Delete query
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':id', $this->id);

            // Execute query
            if($stmt->execute()) {
                return true;
            }
        } catch (Exception $e) {
            // Print error message
            echo json_encode(array('message' => $e->getMessage()));
            return false;
        } catch (Exception $e) {
            // Print error message
            echo json_encode(array('message' => $e->getMessage()));
            return false;
        }

    }
}
?>
