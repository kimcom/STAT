<?php
ini_set('display_errors', 1);
if ($_SERVER['REQUEST_URI'] == '/goods/uploadfile') {
	require_once 'app/core/function_Fn.php';
	require_once 'UploadFile.php';
} else {
	require_once 'session.php';
	require_once 'app/bootstrap.php';
}
?>
