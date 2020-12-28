var me={};
var game_status={};

//Function που καλειτε μόνη της
$(function(){
	draw_empty_board();
	fill_board();
	
	$('#login').click(login_to_game);
	$('#reset_game').click(reset_board);
	$('#do_move').click(do_move);
	$('#move_div').hide();
	game_status_update();
});

//Φτιαχνει τον πίνακα
function draw_empty_board(){
	var t='<table id="score_table">';
	for (var x=6; x>0;x--){
		t += '<tr>';
		for (var y=1; y<8;y++){
			t += '<td class="score_cycle" id="cycle_'+ x +'_'+ y +'">' + x +','+ y +'</td>';
		}
		t +='<tr>';
	}
	t+='</table>';
	$('#score_board').html(t);
}

//Παιρνει να δεδομενα απο το board και καλη την fill_board_by_data αφου πηγαν ολα καλα
function fill_board() {
	$.ajax({url: "score_4.php/board/", success: fill_board_by_data });

}

//Αρχικοποιεί τον πίνακα
function reset_board() {
	$.ajax({url: "score_4.php/board/", method: 'POST',  success: fill_board_by_data });
	$('#move_div').hide();
	$('#game_initializer').show(2000);
}

//Γεμιζει τον πινικα συμφωνα με τα δεδομενα του board
function fill_board_by_data(data) {
	for(var i=0;i<data.length;i++) {
		var o = data[i];
		var id = '#cycle_'+ o.x +'_' + o.y;
		
		// ΤΟ im=o.color μαζι με το .html(im) είναι για να δείχνει το χρώμα στο κουτάκι
		//im= o.color;
		$(id).addClass(o.color+'_cycle')//.html(im);
	}
}

//Εαν έχει βάλει όνομα και έχει διαλέξει χρώμα τότε να κάνει put στην βάση
function login_to_game() {
	if($('#username').val()=='') {
		alert('You have to set a username');
		return;
	}
	var color_picked_2 = $('#color_select').val();
	$.ajax({url: "score_4.php/players/", 
			method: 'POST',
			dataType: 'json',
			contentType: 'application/json',
			data: JSON.stringify( {username: $('#username').val(), color_picked: color_picked_2}),
			success: login_result,
			error: login_error});
}

//Κρύβει ένα div(περιέχει τα input username και το select) και καλεί άλλες δύο functions
function login_result(data) {
	me = data[0];
	$('#game_initializer').hide();
	update_info();
	game_status_update();
}

//Μήνυμα λάθους (το καλεί η login_to_game)
function login_error(data) {
	var x = data.responseJSON;
	alert(x.errormesg);
}

//Βλέπει το status (τον πίνακα game_status) και καλεί την update_status
function game_status_update() {
	$.ajax({url: "score_4.php/status/", success: update_status });
}

//Βλέπει ποιανού είναι η σειρά για να κρύψει ή να εμφανίσει το div με id move_div
function update_status(data) {
	game_status=data[0];
	update_info();
	if(game_status.color_turn==me.color_picked  &&  me.color_picked!=null) {
		x=0;
		// do play
		$('#move_div').show(1000);
		setTimeout(function() { game_status_update();}, 15000);
	} else {
		// must wait for something
		$('#move_div').hide(1000);
		setTimeout(function() { game_status_update();}, 4000);
	}
 	
}

//
function update_info(){
	$('#game_info').html("I am Player: "+me.color_picked+", my name is "+me.username +'<br>Token='+me.token+'<br>Game state: '+game_status.status+', '+ game_status.color_turn+' must play now.');
	
}


//
function do_move() {
	var s = $('#the_move').val();
	
	var a = s.trim().split(/[ ]+/);
	if(a.length!=2) {
		alert('Must give 2 numbers');
		return;
	}
	$.ajax({url: "score_4.php/board/piece/", 
			method: 'POST',
			dataType: "json",
			contentType: 'application/json',
			data: JSON.stringify( {x: a[0], y: a[1]}),
			success: move_result,
			error: login_error});
	
}

function move_result(data){
	
}

