<?php
   // Headers   
   header('Access-Control-Allow-Origin: *');
   header('Content-Type: application/json');
   header('Access-Control-Allow-Methods: PUT');
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

    // Check if the id property exists in the JSON data
    if (empty($data->id)) {
        echo json_encode(array('message' => 'Invalid or empty id.'));
        exit;
    }
    if (!empty($data->category)) {
        // Set Id to update
        $category->id = isset($data->id) ? $data->id : die();

        $category->category = $data->category;

        // Update post 
        if($category ->update()) {
          $category_item = array(
              'id' =>$category->id, // use this
              'category' => $category->category
            );
          // Make JSON
          echo json_encode($category_item);
        } else {
            echo json_encode(
                array('message' => 'category_id Not Found')
            );
        }
    } else {
        echo json_encode(
            array('message' => 'Missing Required Parameters')
        );
    }
?>