<?php

function log2file($type,$msg){
	file_put_contents(PATH_LOG."debug.log",date('Y-m-d H:i:s')."\t[{$type}]\n{$msg}\n",FILE_APPEND);
}
