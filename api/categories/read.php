<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Category.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate blog category object
$category = new Category($db);

// Author query
$result = $category->read();
// Get row count
$num = $result->rowCount();

// check if any categories
if ($num > 0) {
    // Category Array
    $categories_arr = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $category_item = array(
            'id' => $id,
            'category' => $category
        );

        // Push to array
        array_push($categories_arr, $category_item);
    }

    // Turn to JSON & output
    echo json_encode($categories_arr);
} else {
    // No authors found
    echo json_encode(
        array('message' => 'No Categories Found')
    );
}
?>