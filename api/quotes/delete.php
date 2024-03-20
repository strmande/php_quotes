<?php
   // Headers   
   header('Access-Control-Allow-Origin: *');
   header('Content-Type: application/json');
   header('Access-Control-Allow-Methods: DELETE');
   header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
   
 
   include_once '../../config/Database.php';
   include_once '../../models/Quote.php';
 
   // Instantiate DB & connect
   $database = new Database();
   $db = $database->connect();
 
   // Instantiate blog post object
   $quote = new Quote($db);

   // Get raw posted data
   $data = json_decode(file_get_contents("php://input"));

   // Set Id to update
   $quote->id = isset($_GET['id']) ? $_GET['id'] : die();

   // Delete quote 
   if($quote ->delete()) {
    echo json_encode(
        array('message' => 'Quote Deleted')
    );
   } else {
    echo json_encode(
        array('message' => 'Quote Not Deleted')
    );
   }