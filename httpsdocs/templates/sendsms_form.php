<?php

$send_title = SEND_SMS;
$option_gsm = OPTION_GSM;
$option_smpp = OPTION_SMPP;
$label_method = LABEL_METHOD;
$label_phone = LABEL_PHONE;
$label_msg = LABEL_MSG;
$label_translit = LABEL_TRANSLIT;
$btn_send = BTN_SEND;

$sendsms_html = <<<HTML
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{$send_title} {$send_error}</h3>
			</div>
			<div class="panel-body">
				<form name="sendsms" method="post" action="/send.act.php" class="form-horizontal" role="form">
				<div class="form-group">
					<label for="method" class="col-sm-2 control-label">{$label_method}</label>
					<div class="col-sm-10">
						<select class="form-control" id="method" name="method">
						<option value="gsm">{$option_gsm}</option>
						<option value="smpp">{$option_smpp}</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="phone" class="col-sm-2 control-label">{$label_phone}</label>
					<div class="col-sm-10">
						<input type="phone" class="form-control" id="phone" name="phone" placeholder="79123456789">
					</div>
				</div>
				<div class="form-group">
					<label for="msg" class="col-sm-2 control-label">{$label_msg} <span class="badge" id="char_count">0</span></label>
					<div class="col-sm-10">
						<textarea class="form-control" id="msg" name="msg"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="msg" class="col-sm-3 control-label">{$label_translit}</label>
					<div class="col-sm-9 checkbox">
						<input type="checkbox" id="translit" name="translit" value="1" checked>
					</div>
				</div>
				<div class="form-group">
					<label for="msg" class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						<button class="btn btn-success" type="submit" id="sendsms" disabled><span class="glyphicon glyphicon-envelope"></span>  {$btn_send}</button>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>

HTML;

