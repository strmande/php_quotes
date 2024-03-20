

<?php
	// Headers   
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');

	include_once '../../config/Database.php';
	include_once '../../models/Quote.php';

	// Instantiate DB & connect
	$database = new Database();
	$db = $database->connect();
	$type = null;

	// Instantiate blog quote object
	$quote = new Quote($db);
	// Get quote
	if (!empty($_GET['id'])) {
		$type = "id"; 
	} else if (!empty($_GET['author_id']) && !empty($_GET['category_id'])) {
		$type = "author_id_category_id";
	} else if (!empty($_GET['author_id'])) {
		$type = "author_id";
	} else if (!empty($_GET['category_id'])) {
		$type = "category_id";
	}

   // Set quote ID or handle error
	if ($type === "id") {
		$quote->id = $_GET['id'];
	} else if ($type === "author_id") {
		$quote->author_id = $_GET['author_id'];
	} else if ($type === "category_id") {
		$quote->category_id = $_GET['category_id'];
	} else if ($type === "author_id_category_id") {
		$quote->author_id = $_GET['author_id'];
		$quote->category_id = $_GET['category_id'];
	}
   
//    $quote->id = isset($_GET['id']) ? $_GET['id'] : die();
	$result = $quote->read_single($type);
	// echo $result;

	if(!empty($result['error'])) {
		echo $result['error']['message'];
	} else {
		echo json_encode($result);
	}


	// if ($result) {
	// 	// Create array
	// 	$quotes_arr = array();
    // 	$quotes_arr['data'] = array();
	// 	while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	// 		extract($row);
	// 		$quote_item = array(
	// 		'id' => $quote->id,
	// 		'quote' => $quote->quote,
	// 		'author' => $quote->author_id,
	// 		'category' => $quote->category_id
	// 		);
	// 		// Push to "data"
	// 		array_push($quotes_arr['data'], $quote_item);
	// 	}
	// 	// Make JSON
	// 	// print_r(json_encode($quotes_arr));
	// 	echo json_encode($quotes_arr);
	// } else {
	// 	// No posts
	// 	echo json_encode(
	// 		array('message' => 'No Quote Found')
	// 	);
	
	//   }