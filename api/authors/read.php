<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Author.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate blog author object
$author = new Author($db);

// Author query
$result = $author->read();
// Get row count
$num = $result->rowCount();

// check if any authors
if ($num > 0) {
    // Author Array
    $authors_arr = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $author_item = array(
            'id' => $id,
            'author' => $author
        );

        // Push to array
        array_push($authors_arr, $author_item);
    }

    // Turn to JSON & output
    echo json_encode($authors_arr);
} else {
    // No authors found
    echo json_encode(
        array('message' => 'No Authors Found')
    );
}
?>
