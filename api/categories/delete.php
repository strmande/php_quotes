<?php
   // Headers   
   header('Access-Control-Allow-Origin: *');
   header('Content-Type: application/json');
   header('Access-Control-Allow-Methods: DELETE');
   header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
   
 
   include_once '../../config/Database.php';
   include_once '../../models/Category.php';
 
   // Instantiate DB & connect
   $database = new Database();
   $db = $database->connect();
 
   // Instantiate blog post object
   $category = new Category($db);

   // Get raw posted data
   $data = json_decode(file_get_contents("php://input"));

   // Set Id to update
   $category->id = isset($data->id) ? $data->id : die();

   // Delete post 
   if($category ->delete()) {
       $category_item = array(
           'id' =>$category->id
         );
       // Make JSON
       echo json_encode($category_item);
   } else {
    echo json_encode(
        array('message' => 'category_id Not Found')
    );
   }