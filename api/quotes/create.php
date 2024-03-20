<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Quote.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate quote object
$quote = new Quote($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if data is valid
if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {
    // Set data
    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    // Create quote
    $result = $quote ->create();
    if (empty($result['error'])) {
        $quote_item = array(
            'id' =>$quote->id, // use this
            'quote' => $quote->quote,
            'author_id' => $quote->author_id,
            'category_id' => $quote->category_id
          );
        // Make JSON
        echo json_encode($quote_item);
     } else {
        echo json_encode($result['error']);
      // echo json_encode(
      //     array('message' => 'No Quotes Found')
      // );
     }
} else {
    echo json_encode(
        array('message' => 'Missing Required Parameters')
    );
}
?>
