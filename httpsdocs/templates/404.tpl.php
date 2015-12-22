<?php

include_once("templates/header.tpl.php");
include_once("templates/footer.tpl.php");

$html = $header_html;

$html .= <<<HTML
		<div class="container">	
			<h1>Error 404</h1>
			<h2>404 Page not found</h2>
			<h3>Return to <a href="/">main page</></h3>
		</div>
HTML;

$html .= $footer_html;
