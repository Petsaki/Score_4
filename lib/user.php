<?php

//SQL request για επιστροφή των πίνακα players
function show_users() {
	global $mysqli;
	$sql = 'select username,color_picked from players';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

//SQL request για επιστροφή τον παίκτη με το όρισμα $b
function show_user($b) {
	global $mysqli;
	$sql = 'select username,color_picked from players where color_picked=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

//Έλεγχος εάν δεν έχει συμπληρώση το username,αλλιώς ενημέρωση της βάσης για τον καινούργιο παίχτη ενώ έχουμε δει ότι δεν υπάρχει άλλος
function set_user($input) {
	
	if(!isset($input['username'])) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"No username given."]);
		exit;
	}

	$username=$input['username'];
	$color=$input['color_picked'];
	global $mysqli;

	//Έλεγχος εάν υπάρχουν ήδη παίχτες που παίζουν.
	$sql = 'select count(*) as c from players where username is not null';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	$r = $res->fetch_all(MYSQLI_ASSOC);
	if ($r[0]['c']==2){
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Other players have started the game already."]);
		exit;
	}

	//Έλεγχος εάν υπάρχει ήδη ο παίχτης με το ίδιο χρώμα.
	global $mysqli;
	$sql = 'select count(*) as c from players where color_picked=? and username is not null';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$color);
	$st->execute();
	$res = $st->get_result();
	$r = $res->fetch_all(MYSQLI_ASSOC);
	if($r[0]['c']>0) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Player $color is already set. Please select another color."]);
		exit;
	}

	$sql = 'update players set username=?, token=md5(CONCAT( ?, NOW()))  where color_picked=?';
	$st2 = $mysqli->prepare($sql);
	$st2->bind_param('sss',$username,$username,$color);
	$st2->execute();


	
	update_game_status();
	$sql = 'select * from players where color_picked=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$color);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	
	
}

//Έλεγχος εάν η method είναι POST/GET
function handle_user($method, $b,$input) {
	if($method=='GET') {
		show_user($b);
	} else if($method=='POST') {
        set_user($input);
    }
}

//Έλεγχος του token ότι υπάρχει και επιστροφή του παίχτη με το αυτό το token
function current_color($token) {
	global $mysqli;
	if($token==null){
		return(null);
	}
	$sql = 'select * from players where token=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$token);
	$st->execute();
	$res = $st->get_result();
	if($row=$res->fetch_assoc()) {
		return($row['color_picked']);
	}
	return(null);
}

//SQL request για να αρχικοποιήσει του παίχτες
function remove_user(){
	global $mysqli;
	$sql = 'update players set username=null, token="", last_action=null where token!="" ';
	$st = $mysqli->prepare($sql);
	$st->execute();
}
?>