//Function που καλειτε μόνη της
$(function(){
	draw_empty_board();
	fill_board();
})

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