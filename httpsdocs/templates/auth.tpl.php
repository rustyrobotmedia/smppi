<?php

include_once("templates/header.tpl.php");
include_once("templates/footer.tpl.php");

$login_html = $header_html;
$auth_header = AUTHORIZATION;
$auth_button = BTN_AUTH;

$login_html .= <<<HTML
	
	<style type="text/css">
		body {
			padding-top: 40px;
			padding-bottom: 40px;
			background-color: #eee;
		}

		.form-signin {
			max-width: 330px;
			padding: 15px;
			margin: 0 auto;
		}
		.form-signin .form-signin-heading,
		.form-signin .checkbox {
			margin-bottom: 10px;
		}
		.form-signin .checkbox {
			font-weight: normal;
		}
		.form-signin .form-control {
			position: relative;
			height: auto;
			-webkit-box-sizing: border-box;
				 -moz-box-sizing: border-box;
							box-sizing: border-box;
			padding: 10px;
			font-size: 16px;
		}
		.form-signin .form-control:focus {
			z-index: 2;
		}
		.form-signin input[type="login"] {
			margin-bottom: -1px;
			border-bottom-right-radius: 0;
			border-bottom-left-radius: 0;
		}
		.form-signin input[type="password"] {
			margin-bottom: 10px;
			border-top-left-radius: 0;
			border-top-right-radius: 0;
		}
	</style>
	
	<div class="container">	
	<form method="post" class="form-signin" role="form">
		<h4 class="form-signin-heading">{$auth_header} <span class="label label-primary">{$title}</span></h4>
		<p><span class="label label-danger">{$auth_error}</span></p>
		<input type="login" name="login" class="form-control" placeholder="Login" required autofocus>
		<input type="password" name="password" class="form-control" placeholder="Password" required>
		<button class="btn btn-lg btn-success btn-block" type="submit">{$auth_button}</button>
		</form>
	</div>

HTML;

$login_html .= $footer_html;
