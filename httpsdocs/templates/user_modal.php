<?php

$label_login = LABEL_LOGIN;
$label_password = LABEL_PASSWORD;
$label_ip = LABEL_IP;
$label_interface = LABEL_INTERFACE;
$label_rights = LABEL_RIGHTS;
$btn_close = BTN_CLOSE;
$btn_save = BTN_SAVE;

$modal_html = <<<HTML
	<div class="modal fade" id="create_user" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<form name="create_user" method="post" action="/user.act.php" class="form-horizontal" role="form">
					<div class="form-group">
						<label for="user_login" class="col-sm-2 control-label">{$label_login}</label>
						<div class="col-sm-10">
							<input type="user_login" class="form-control" id="user_login" name="user_login" value="">
						</div>
					</div>
					<div class="form-group">
						<label for="user_password" class="col-sm-2 control-label">{$label_password}</label>
						<div class="col-sm-10">
							<input type="user_password" class="form-control" id="user_password" name="user_password">
						</div>
					</div>
					<div class="form-group">
						<label for="user_ip" class="col-sm-2 control-label">{$label_ip}</label>
						<div class="col-sm-10">
							<input type="user_ip" class="form-control" id="user_ip" name="user_ip" value="" placeholder="123.123.123.123 or *">
						</div>
					</div>
					<div class="form-group">
						<label for="user_interface" class="col-sm-2 control-label">{$label_interface}</label>
						<div class="col-sm-10">
							<select class="form-control" id="user_interface" name="user_interface">
								
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="user_rights" class="col-sm-2 control-label">{$label_rights}</label>
						<div class="col-sm-10" id="user_rights">
							
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="user_id" id="user_id" value="">
					<button type="button" class="btn btn-default" data-dismiss="modal">{$btn_close}</button>
					<button type="submit" class="btn btn-success">{$btn_save}</button>
					</form>
					<div id="delete_form" class="pull-left">
						
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

HTML;

