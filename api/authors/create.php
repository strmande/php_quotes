<?php
   // Headers   
   header('Access-Control-Allow-Origin: *');
   header('Content-Type: application/json');
   header('Access-Control-Allow-Methods: POST');
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

   if (!empty($data->author)) {

        $author->author = $data->author;

        // Create author 
        if($author ->create()) {
          $author_item = array(
              'id' =>$author->id, // use this
              'author' => $author->author
            );
          // Make JSON
          echo json_encode($author_item);
        } else {
            echo json_encode(
                array('message' => 'Author Not Created')
            );
        }
    }  else {
        echo json_encode(
            array('message' => 'Missing Required Parameters')
        );
    }
    ?>