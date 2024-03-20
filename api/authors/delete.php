<?php
   // Headers   
   header('Access-Control-Allow-Origin: *');
   header('Content-Type: application/json');
   header('Access-Control-Allow-Methods: DELETE');
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

   // Set Id to update
   $author->id = isset($data->id) ? $data->id : die();

   // Delete post 
   if($author ->delete()) {
       $author_item = array(
           'id' =>$author->id
         );
       // Make JSON
       echo json_encode($author_item);
   } else {
    echo json_encode(
        array('message' => 'Author Not Deleted')
    );
   }