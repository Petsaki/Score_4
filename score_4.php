<?php

require_once "lib/dbconnect.php";
require_once "lib/board.php";
require_once "lib/game.php";
require_once "lib/user.php";

$method = $_SERVER['REQUEST_METHOD'];
//Διαχώριση του request ανά /
$request = explode('/',trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
//Εάν υπάρχει HTTP_X_TOKEN να πάρει τα δεδομένα του
if(isset($_SERVER['HTTP_X_TOKEN'])) {
	$input['token']=$_SERVER['HTTP_X_TOKEN'];
}

//Έλεγχος των requests για την εκτέλεση της ανάλογης ενέργειας
switch ($r=array_shift($request)){
	case 'board':
		switch($b=array_shift($request)){
			case '':
			case null: 
				handle_board($method);
				break;
			case 'show_piece':
				show_piece($request[0],$request[1]);
				break;
			case 'put_piece':
				put_piece($input['y'],$input['token']);
				break;
			default:
				header("HTTP/1.1 404 Not Found");
				break;
		}
		break;
	case 'status':
		if (sizeof($request)==0){
			show_status();
		}else{
			header("HTTP/1.1 404 Not Found");
		}
		break;
	case 'players':
		handle_player($method,$request,$input);
		break;
	default:
		header("HTTP/1.1 404 Not Found");
		exit;
}

//Έλεγχος της method (Από το path /board)
function handle_board($method){
	if($method=='GET'){
		show_board();
	}else if($method=='POST'){
		reset_board();
		show_board();
	}
}


//Έλεγχος για των pathes  από /players
function handle_player($method, $request,$input) {
	switch ($b=array_shift($request)) {
		case '':
		case null:
			if($method=='GET'){
				show_users($method);
			}else if($method=='POST'){
				handle_user($method, $b,$input);
			}else{
				header("HTTP/1.1 400 Bad Request"); 
				print json_encode(['errormesg'=>"Method $method not allowed here."]);
			}
            break;
        case 'Y': 
			case 'R': 
				handle_user($method, $b,$input);
				break;
		default: 
			header("HTTP/1.1 404 Not Found");
			print json_encode(['errormesg'=>"Player $b not found."]);
            break;
	}
}

?>