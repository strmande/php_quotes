<?php
    // Headers   
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
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

    // Check if the id property exists in the JSON data
    if (empty($_GET['id'])) {
        echo json_encode(array('message' => 'Invalid or empty id.'));
        exit;
    }
    if (!empty($data->quote) && !empty($data->author_id) && !empty($data->category_id)) {

        // Set Id to update
        $quote->id = isset($_GET['id']) ? $_GET['id'] : die();
        $quote->quote = $data->quote;
        $quote->author_id = $data->author_id;
        $quote->category_id = $data->category_id;

        // Update quote 
        if ($quote->update()) {
            echo json_encode(array('message' => 'Quote Updated'));
        } else {
            echo json_encode(array('message' => 'Quote Not Updated'));
        }
    } else {
        echo json_encode(
            array('message' => 'Missing required parameters')
        );
    }
?>
