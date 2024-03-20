<?php
   // Headers   
   header('Access-Control-Allow-Origin: *');
   header('Content-Type: application/json');
   header('Access-Control-Allow-Methods: PUT');
   header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
   
 
   include_once '../../config/Database.php';
   include_once '../../models/Author.php';
 
   // Instantiate DB & connect
   $database = new Database();
   $db = $database->connect();
 
   // Instantiate blog post object
   $author = new Author($db);

   // Get raw posted data
   $data = json_decode(file_get_contents("php://input"));
  

    // Check if the id property exists in the JSON data
    if (empty($_GET['id'])) {
        echo json_encode(array('message' => 'Invalid or empty id.'));
        exit;
    }
    if (!empty($data->author)) {

        // Set Id to update
        $author->id = isset($_GET['id']) ? $_GET['id'] : die();

        $author->author = $data->author;

        // Update post 
        if($author ->update()) {
            echo json_encode(
                array('message' => 'Author Updated')
            );
        } else {
            echo json_encode(
                array('message' => 'Author Not Updated')
            );
        }
    } else {
        echo json_encode(
            array('message' => 'Missing required parameters')
        );
    }
?>