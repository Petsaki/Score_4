<?php

//Δειχνει τον πινακα απο την βαση
function show_board($input) {
	global $mysqli;
	
	$sql = 'select * from board';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}
//Αρχικοποιει τον πινακα απο την βαση
function reset_board() {
	global $mysqli;
	$sql = 'call clean_board()';
	$mysqli->query($sql);
}
?>