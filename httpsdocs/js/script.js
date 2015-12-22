$(document).ready(function() {
	
	$('#phone').keyup(function(){
		if($('#phone').val() == "" || $('#msg').val() == ""){
			$('#sendsms').attr('disabled',true);
		}
		else{
			$('#sendsms').attr('disabled',false);
		}
	});
	
	$('#msg').keyup(function(){
		if($('#phone').val() == "" || $('#msg').val() == ""){
			$('#sendsms').attr('disabled',true);
		}
		else{
			$('#sendsms').attr('disabled',false);
		}
	});
	
	$('#msg').keyup(function(){
		msg = $('#msg').val();
		$('#char_count').text(msg.length);
	});
	
	
});

function modal_show(id){

	$.ajax({
		url: '/ajax/get_user_fields.php',
		type: "POST",
		dataType: 'json',
		data: {'id':id},
		success: function(data) {
			$('#user_id').val(data.id);
			$('.modal-title').html(data.modal_header);
			$('#user_login').val(data.login);
			$('#user_ip').val(data.ip);
			var interfaces_html = '';
			data.interfaces.forEach(function(interface_name){
				interfaces_html += '<option value="'+interface_name+'">'+interface_name+'</option>';
			});
			var rights_html = '';
			data.rights.forEach(function(right_one){
				rights_html += '<li class="list-group-item"><input name="user_rights[]" type="checkbox" value="'+right_one.right+'" '+right_one.checked+'>&nbsp;'+right_one.descr+'</li>';
			});
			
			$('#user_interface').html(interfaces_html);
			$('#user_rights').html(rights_html);
		}
	});
	if(id > 0){
		$.ajax({
			url: '/ajax/delete_user_form.php',
			type: "POST",
			data: {'id':id},
			success: function(form) {
				$('#delete_form').html(form);
			}
		});
	}
	
}
