<?php

//SQL request για εμφάνιση ενός συγκεκριμένου κουτιού από το board
function show_piece($x,$y) {
	global $mysqli;
	
	$sql = 'select * from board where x=? and y=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('ii',$x,$y);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

//Γύρισμα του board από το αποτέλεσμα της read_board
function show_board() {
	global $mysqli;
		header('Content-type: application/json');
		print json_encode(read_board(), JSON_PRETTY_PRINT);
}

//SQL request για αρχικοποίηση του πίκανα board
function reset_board() {
	global $mysqli;
	$sql = 'call clean_board()';
	$mysqli->query($sql);
}

//Έλεγχοι πρωτού κάνει την κίνηση
function put_piece($y,$token) {
	
	
	if($token==null || $token=='') {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Token is not set."]);
		exit;
	}
	
	$color = current_color($token);
	if($color==null ) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
	}
	
	$status = read_status();
	if($status['status']!='started') {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Game is not in action."]);
		exit;
	}
	
	if($status['color_turn']!=$color) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"It is not your turn."]);
		exit;
	}
	
	//Έλεγχος εάν η στήλη που κάνει κίνηση είναι γεμάτη
	$full=check_collumn($y);
	if($full['color']!= null){
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"The column $y is full!"]);
		exit;
	}
	
	do_move($y,$color);

	//Έλεγχος εάν νίκησε
	$result=check_winner();
	if ($result!=null){
		end_game($result);
		
	}
		
}

//SQL request για ενημέρωση της κίνησης και του game_status
function do_move($y,$color) {
	global $mysqli;
	$sql = 'call `put_piece`(?,?);';
	$st = $mysqli->prepare($sql);
	$st->bind_param('is',$y,$color);
	$st->execute();

	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);	
}

//SQL request για επιστροφή του board
function read_board() {
	global $mysqli;
	$sql = 'select * from board';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	return($res->fetch_all(MYSQLI_ASSOC));
}

//SQL request για επιστροφή της θέσης (6,y), όπου το y είναι η κίνηση του παίχτη
function check_collumn($y){
	global $mysqli;
	
	$sql = 'select color from board where x=6 and y=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('i',$y);
	$st->execute();
	$res = $st->get_result();
	$status = $res->fetch_assoc();
	return($status);
}

?>