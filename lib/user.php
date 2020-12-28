<?php

//Εμφανίζει όλους τους χρήστες
function show_users() {
	global $mysqli;
	$sql = 'select username,color_picked from players';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

//Εμφανιζεί των χρήστη με παράμετρο το χρώμα
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

//Μήνυμα λάθους εάν δεν έχει συμπληρώση το username,αλλιώς ενημερώνη την βάση τον καινούργιο παίχτη ενώ έχουμε δει ότι δεν υπάρχει άλλος
function set_user($input) {
	
	if(!isset($input['username'])) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"No username given."]);
		exit;
	}
	$username=$input['username'];
	$color=$input['color_picked'];
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

//Βλέπει άμα η method είναι post ή get για να καλέσει την ανάλογη function
function handle_user($method, $b,$input) {
	if($method=='GET') {
		show_user($b);
	} else if($method=='POST') {
        set_user($input);
    }
}




?>