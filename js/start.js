$(document).ready(function(){

$(document).on('click', '.joinbutton', function(e) {
	e.preventDefault(); 
	$('#Main_login').slideUp('slow', function() {
		$('#Join_form').slideDown('slow');
	});
});

$(document).on('click', '.join_close', function(e) {
	e.preventDefault();
	$('#Join_form').slideUp('slow', function() {
		$('#Main_login').slideDown('slow');
	});
});
/*	$('#joinbutton').click(function() {
		$('#Main_login').slideDown('slow', function() {
		// Animation complete.
		});
	});*/
});