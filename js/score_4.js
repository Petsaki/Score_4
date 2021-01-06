var me={ nickname: null, token: null, color_picked: null };
var game_status={};
var last_update=new Date().getTime();
var timer=null;



//Ορισμός των click listeners 
$(function(){

	//Αλλαγή pointer
	document.getElementById('test').style.cursor = "pointer";
	document.getElementById('light').style.cursor = "pointer";


	$('#login').click(login_to_game);
	$('#reset_game').click(reset_board);
	$('#do_move').click(do_move);
	$('#move_div').hide();
	$('#reset_game').hide();
	game_status_update();
	
	draw_empty_board();
	if(game_status.status!='started' && me.token!=null){
		reset_board();
		fill_board();
	}
});

//click listener για πιο id κουτάκι πατήθηκε
$(document).on("click", "#score_table td", function(e) {
	var data = $(this).attr('id');
	var res = data.split("_");
	var piece = res[2];
	do_move_from_click(piece);
	
}); 

//Mouse over listener για έγκυρη κίνηση
$(document).on("mouseenter", "#score_table td", function(e) {
	if (game_status.status=='started' && game_status.color_turn==me.color_picked){
		var data = $(this).attr('id');
		var sthlh= document.getElementById(data);
		sthlh.style.cursor = "pointer";
		for (var i=1; i<7;i++){
			var res = data.split("_");
			var sthlh = res[2];
			var txt = data.replace(data,"cycle_"+ i +"_"+ sthlh);
			var kouti= document.getElementById(txt);
		if (kouti.classList.contains("Y_cycle")==false && kouti.classList.contains("R_cycle")==false){
			kouti.classList.add("hoverin");
			return;
		}
	}
}
});

//Mouse left listener
$(document).on("mouseleave", "#score_table td", function(e) {
	var data = $(this).attr('id');
	var sthlh= document.getElementById(data);
	for (var i=1; i<7;i++){
		var res = data.split("_");
		var sthlh = res[2];
		var txt = data.replace(data,"cycle_"+ i +"_"+ sthlh);
		var kouti= document.getElementById(txt);
	if (kouti.classList.contains("hoverin")){
		kouti.classList.remove("hoverin");
		return;
	}
}
});

//Light mode για να ενεργοποιήσει τα light-mode css
function light(){
	var element = document.body;
   element.classList.toggle("light-mode");
}

//Φτιάχνει τον πίνακα
function draw_empty_board(){
	var t='<table id="score_table">';
	for (var x=6; x>0;x--){
		t += '<tr>';
		for (var y=1; y<8;y++){
			//' + x +','+ y +' gia na moy deixnei tis syntatagmenes
			t += '<td class="score_cycle" id="cycle_'+ x +'_'+ y +'"></td>';
		}
		t +='<tr>';
	}
	t+='</table>';
	$('#score_board').html(t);
}

//Ajax request για επιστροφή του πίνακα board
function fill_board() {
	$.ajax({url: "score_4.php/board/", 
		method: 'GET',
        dataType: 'json',
        headers: { "X-Token": me.token },
		success: fill_board_by_data });

}

//Ajax request για αρχικοποίηση του παιχνιδιού
function reset_board() {
	$.ajax({url: "score_4.php/board/",
		headers: {"X-Token": me.token},
		method: 'POST',  success: draw_empty_board });
	me = { nickname: null, token: null, color_picked: null };
	$('#move_div').hide();
	game_status_update();
	$('#game_initializer').show(1000);
	game_status_update();
}

//Γέμισμα του πίνακα συμφωνα με τα δεδομενα του board
function fill_board_by_data(data) {
	for(var i=0;i<data.length;i++) {
		var o = data[i];
		var id = '#cycle_'+ o.x +'_' + o.y;

		$(id).addClass(o.color+'_cycle').html('');
	}
}

//Έλεγχο εισόδου και ajax request για εισαγωγή του παίχτη
function login_to_game() {
	if($('#username').val()=='') {
		alert('You have to set a username!');
		return;
	}
	var color_picked_2 = $('#color_select').val();
	
	$.ajax({url: "score_4.php/players/", 
			method: 'POST',
			dataType: 'json',
			headers: {"X-Token": me.token},
			contentType: 'application/json',
			data: JSON.stringify( {username: $('#username').val(), color_picked: color_picked_2}),
			success: login_result,
			error: login_error});
}

//Εισαγωγή των στοιχείων του παίχτη και ενημέρωση του games_status
function login_result(data) {
	me = data[0];
	$('#game_initializer').hide();
	update_info();
	game_status_update();
}

//Μήνυμα λάθους
function login_error(data) {
	var x = data.responseJSON;
	alert(x.errormesg);
}

//Ajax request για επιστροφή του game_status
function game_status_update() {
	clearTimeout(timer);
	$.ajax({url: "score_4.php/status/", success: update_status ,headers: {"X-Token": me.token}});
}

//Έλεγχος του status (από το games_status)----------------------------------------------------------------
function update_status(data) {
	last_update=new Date().getTime();
	var game_stat_old = game_status;
	game_status=data[0];
	winner = game_status.result;
	update_info();
	clearTimeout(timer);
	
	if (me.token!=null){
		$('#reset_game').show();
	}else{
		$('#reset_game').hide();
	}
	
	if (game_stat_old.status=='initialized' && game_status.status=='not active' && me.token!=null){
		alert('No player found. Game reset.');
		reset_board();
	}
	
	if(game_stat_old.status=='started' && game_status.status=='not active' && me.color_picked!=game_status.color_turn){
			alert('Enemy player surrendered. Game restarted');
			reset_board();
			document.querySelector('#reset_game').innerText = 'Reset';
			update_info();
		}
		
	if (game_status.status=='started' && me.token!=null){
		document.querySelector('#reset_game').innerText = 'Surrender';
		}
	
	//-----------------------------------------------------------------------------------------------------------
	if (game_status.status == 'aborded' && game_stat_old.status != 'aborded') {
		update_info();
        $('#move_div').hide(2000);
		opponent_aborded(game_status);
		after_game_finished();
		update_info();
		return;
    } else if (game_status.status == 'ended' && game_stat_old.status != 'ended') {
		fill_board();
		update_info();
		$('#move_div').hide(2000);
		alert_winner();
		after_game_finished();
		update_info();
		return;
	}else if(game_status.color_turn==me.color_picked  &&  me.color_picked!=null) {
		fill_board();
		if(game_stat_old.color_turn!=me.color_picked) {
			fill_board();
		}

		$('#move_div').show(1000);
		timer= setTimeout(function() { game_status_update();}, 500);
	}else{
	
		$('#move_div').hide(1000);
		timer= setTimeout(function() { game_status_update();}, 500);
	}
	
 	
}

//Ενημέρωση των παιχτών για το αποτέλεσμα του παιχνίδι
function alert_winner() {
	winner = game_status.result;
	if (me.token!=null){
		if (winner=='D'){
			alert('Draw.');
		}else if (winner=='Y'){
			alert('Player Yellow has won!');
		}else{
			alert('Player Red has won!');
		}
	}
}

//Ενημέρωση των παιχτών για aborded
function opponent_aborded(data){
	winner= data.result;
	if (me.token!=null){
		if (winner=='R'){
			player='Yellow';
		}else{
			player='Red';
		}
	alert('Player '+  player +' has aborded the game.');
	}
}

//Αρχικοποίηση του παιχνιδιού και disabled reset button
function after_game_finished(){
	document.getElementById('reset_game').disabled = true;
	setTimeout(function() { reset_board(); }, 1800);
	setTimeout(function() { document.getElementById('reset_game').disabled = false; }, 1800);

}

//Ενημέρωση παιχτών για τα στοιχεία τους
function update_info(){
	if (me.color_picked=='Y'){
		color='Yellow';
	}else if (me.color_picked=='R'){
		color='Red';
	}else {
		color=null;
	}

	if (game_status.color_turn=='Y'){
		color_turn='Yellow';
	}else if (game_status.color_turn=='R'){
		color_turn='Red';
	}else {
		color_turn=null;
	}

	if(game_status.status=='started' && me.token!=null){
		$('#game_info').html("<h4>Player Status:</h4>You are the: <b>"+color+"</b> player <br/> Name: "+me.username +'<br/> <br/> <h4>Game Status:</h4>Game state: '+game_status.status+'<br/> <b>'+ color_turn+'</b> must play now.');
		
	}else{
		$('#game_info').html("<h4>Player Status:</h4>You are the: <b>"+color+"</b> player <br/> Name: "+me.username +'<br/> <br/> <h4>Game Status:</h4>Game state: '+game_status.status);
	}
}


//Έλεγχος κίνησης και ajax request για εκτέλεση της κίνησης
function do_move() {
	var s = $('#the_move').val();
	
	if(s<1 || s>7) {
		alert('You must enter a value from 1-7!');
		return;
	}

	$.ajax({url: "score_4.php/board/put_piece/", 
			method: 'POST',
			dataType: "json",
			headers: { "X-Token": me.token },
			contentType: 'application/json',
			data: JSON.stringify( {y:s}),
			success: move_result,
			error: login_error});
	
}

//Αjax request για εκτέλεση της κίνησης
function do_move_from_click(piece){
	$.ajax({url: "score_4.php/board/put_piece/", 
			method: 'POST',
			dataType: "json",
			headers: { "X-Token": me.token },
			contentType: 'application/json',
			//, color: me.color_picked μπορει να βαλω εδω το χρωμα του παιχτη
			data: JSON.stringify( {y:piece}),
			success: move_result,
			error: login_error});
}

//Ενημέρωση του πίνακα παιχνιδιού για την καινούργια κίνηση
function move_result(data){
	fill_board_by_data(data);
	$('#move_div').hide(1000);
}

