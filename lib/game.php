<?php


//Έλεγχος για aborted και SQL request για επιστροφή του πίνακα game_status
function show_status() {
	
	global $mysqli;
	
	check_abort();
	


	$sql = 'select * from game_status';
	$st = $mysqli->prepare($sql);

	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);

}

//SQL request για έλεγχο εάν ο αντίπαλος έχει ώρα να παίξει
function check_abort() {
	global $mysqli;

	$sql = "update game_status set status='aborded', result=if(color_turn='Y','R','Y'),color_turn=null where color_turn is not null and last_change<(now()-INTERVAL 5 MINUTE) and status='started'";
	$st = $mysqli->prepare($sql);
	$r = $st->execute();
}

//SQL request για επιστροφή του πίνακα game_status
function read_status() {
	global $mysqli;
	
	$sql = 'select * from game_status';
	$st = $mysqli->prepare($sql);

	$st->execute();
	$res = $st->get_result();
	$status = $res->fetch_assoc();
	return($status);
}

//Ενημερώνη το games_status ανάλογα το status.
function update_game_status() {
	global $mysqli;
	
	$status=read_status();
	$new_status=null;
	$new_turn=null;
	
	$st3=$mysqli->prepare('select count(*) as aborted from players WHERE last_action< (NOW() - INTERVAL 6 MINUTE)');
	$st3->execute();
	$res3 = $st3->get_result();
	$aborted = $res3->fetch_assoc()['aborted'];
	if($aborted>0) {
		if ($status['status']=='started' || $status['status']=='ended'){
			$sql = "UPDATE players SET username=NULL, token=NULL, last_action =null";
			$st2 = $mysqli->prepare($sql);
			$st2->execute();
		}
		if($status['status']=='started') {
			$new_status='aborted';
		}
	}
	
	
	$sql = 'select count(*) as c from players where username is not null';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	$active_players = $res->fetch_assoc()['c'];
	
	
	switch($active_players) {
		case 0:
			$new_status='not active'; 
			break;
		case 1:
			$new_status='initialized'; 
			break;
		case 2: 
			$new_status='started'; 
			if($status['color_turn']==null) {
				//$new_turn='Y';
				$random_turn=rand(1,2);
				if ($random_turn==1){	
					$new_turn='Y';
				}else{
					$new_turn='R';
				}
				$sql = 'update players set last_action=(NOW());';
				$st = $mysqli->prepare($sql);
				$st->execute();
			}
			break;
			
	}

	$sql = 'update game_status set status=?, color_turn=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('ss',$new_status,$new_turn);
	$st->execute();
	
}


//Έλεγχος νικητή
function check_winner(){	
	
	$orig_board=read_board();
	$board=convert_board($orig_board);
	$status = read_status();
	
	$winner=null;
	if($status['status']!='started'){
		return($winner);
	}

	if ($status['color_turn']!=null){

		if ($status['color_turn']=='Y'){
			$status['color_turn']='R';
		}else{
			$status['color_turn']='Y';
		}
		

	//οριζόντια
	for ($x=1; $x <7; $x++){
		for ($i=1; $i < (8-3);$i++){
			if ($board[$x][$i]['color']==$status['color_turn'] && $board[$x][$i+1]['color']==$status['color_turn'] && $board[$x][$i+2]['color']==$status['color_turn'] && $board[$x][$i+3]['color']==$status['color_turn']){
				$winner=$status['color_turn'];
				return($winner);
			}
		}
	}
	
	//κάθετα
	for ($x=1; $x <(7-3); $x++){
		for ($i=1; $i < 8;$i++){
			if ($board[$x][$i]['color']==$status['color_turn'] && $board[$x+1][$i]['color']==$status['color_turn'] && $board[$x+2][$i]['color']==$status['color_turn'] && $board[$x+3][$i]['color']==$status['color_turn']){
				$winner=$status['color_turn'];
				return($winner);
			}
		}
	}
	
	//διαγώνια (Αριστερα προς δεξια)
	for ($x=1; $x <(7-3); $x++){
		for ($i=1; $i < (8-3);$i++){
			if ($board[$x][$i]['color']==$status['color_turn'] && $board[$x+1][$i+1]['color']==$status['color_turn'] && $board[$x+2][$i+2]['color']==$status['color_turn'] && $board[$x+3][$i+3]['color']==$status['color_turn']){
				$winner=$status['color_turn'];
				return($winner);
			}
		}
	}
	
	//διαγώνια (δεξια προς αριστερα)
	for ($x=3; $x <7; $x++){
		for ($i=1; $i < (8-3);$i++){
			if ($board[$x][$i]['color']==$status['color_turn'] && $board[$x-1][$i+1]['color']==$status['color_turn'] && $board[$x-2][$i+2]['color']==$status['color_turn'] && $board[$x-3][$i+3]['color']==$status['color_turn']){
				$winner=$status['color_turn'];
				return($winner);
			}
		}
	}
	
	//Εαν είναι γεμάτος ο πίνακας τότε είναι draw
	if ($board[6][1]['color']!=null && $board[6][2]['color']!=null && $board[6][3]['color']!=null && $board[6][4]['color']!=null && $board[6][5]['color']!=null && $board[6][6]['color']!=null && $board[6][7]['color']!=null){
		$winner='D';
		return($winner);
	}
	}
	return($winner);
					
}

//SQL request για ενημέρωση του result του παιχνιδιού
function end_game($winner){
	global $mysqli;
	
	$sql = "update game_status set status='ended', result=?  where color_turn is not null and status='started'";
    $st = $mysqli->prepare($sql);
	$st->bind_param('s',$winner);
	$st->execute();
	show_status();
	
}

//Επιστροφή του πίνακα (με δείκτη όρισμα)
function convert_board(&$orig_board){
	$board=[];
	foreach($orig_board as $i=>&$row) {
		$board[$row['x']][$row['y']] = &$row;
	} 
	return($board);
}

?>