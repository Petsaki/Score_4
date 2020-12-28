<?php

require_once "lib/dbconnect.php";
require_once "lib/board.php";
require_once "lib/game.php";
require_once "lib/user.php";


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/',trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

//Ολα τα πιθανα requests και αναλογα ποιο είναι, καλεί και την αντίστοιχη function
switch ($r=array_shift($request)){
	case 'board':
		switch($b=array_shift($request)){
			case '':
			case null: 
				handle_board($method);
				break;
			case 'piece':
				handle_piece($method, $request[0],$request[1],$input);
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

//Βλεπει εάν  η method είναι get ή post
function handle_board($method){
	if($method=='GET'){
		show_board();
	}else if($method=='POST'){
		reset_board();
		show_board();
	}
}

//
function handle_piece($method, $x,$y,$input) {
	
}

//Βλέπει εάν είναι κενή τότε να επιστρέφει να στοιχεία των παιχτών αλλιώς να καλέσει την handle_user
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