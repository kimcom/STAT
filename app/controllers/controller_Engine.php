<?php
class Controller_Engine extends Controller {
	function action_index() {
	}
//получение настроек пользователя
	function action_filter_save() {
//Fn::debugToLog('filter_save', ($_POST['filter']));
		$filename = "Users\\Setting\\".$_SESSION['UserID'].'_'.$_REQUEST['section'].'_'.$_REQUEST['gridid'].".txt";
		$bl = file_put_contents($filename, $_REQUEST['filter']);
		if ($bl==false) Fn::debugToLog ("Engine", 'ошибка при записи фильтра в файл: '.$filename );
	}
	function action_filter_restore() {
		$filename = "Users\\Setting\\".$_SESSION['UserID'].'_'.$_REQUEST['section'].'_'.$_REQUEST['gridid'].".txt";
		$handle = @fopen($filename, "r");
		$response = new stdClass();
		if ($handle != null) {
			$response->success = true;
			$response->message = 'ok';
			$response->data = fread($handle, filesize($filename));
			echo json_encode($response);
		} else {
			//Fn::debugToLog("Engine", 'ошибка при чтении фильтра из файла: ' . $filename);
			$response->success = false;
			$response->message = 'Возникла ошибка при получении настроек!<br><br>Сообщите разработчику!';
			$response->data = 0;
			echo json_encode($response);
		}
	}
	function action_filter_reset() {
		$filename = "Users\\Setting\\" . $_SESSION['UserID'] . '_' . $_REQUEST['section'] . '_' . $_REQUEST['gridid'] . ".txt";
		if (file_exists($filename)) {
			$fp = unlink($filename);
			if (!$fp) {
				Fn::debugToLog("filter_reset", "ошибка удаления файла ($filename)");
			}
		} else {
			Fn::debugToLog("filter_reset", "файл не найден ($filename)");
		}
	}

	function action_setting_set() {
		$cnn = new Cnn();
		return $cnn->set_report_setting();
	}
	function action_setting_get() {
		$cnn = new Cnn();
		return $cnn->get_report_setting_list();
	}
	function action_setting_get_byName() {
		$cnn = new Cnn();
		return $cnn->get_report_setting_byName();
	}

	function action_get_file() {
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
		$userFileName = $file_name;
		$report_name = "Users\\Files\\" . $_SESSION['UserID'] . '_' . $report_name . ".xls";
//Fn::debugToLog('get file', $report_name.' '.  $file_name);
		$path = 'php://output';
		$userFileName = $userFileName.' '.date('Y-m-d H:i:s').'.xls';
		$handle = @fopen($report_name, "r");
		if ($handle != null) {
//Fn::debugToLog('get file', filesize($report_name));
			$content = fread($handle, filesize($report_name));
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
//			header("Content-Disposition: attachment; filename=$userFileName;");
			header("Content-Disposition: attachment; filename=\"" . $userFileName . "\";");
			header('Content-Transfer-Encoding: binary');
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");
			header('Content-Length: ' . filesize($report_name));
			//header("Content-type: application/vnd.ms-excel");
			file_put_contents($path, $content);
		}
	}
	function action_set_file(){
		foreach ($_REQUEST as $arg => $val)	${$arg} = $val;
		$report_name = "Users\\Files\\" . $_SESSION['UserID'] . '_' . $report_name . ".xls";
//Fn::debugToLog('set file', $report_name);
		$html = iconv('utf-8', 'cp1251', $html);
		$content = <<<EOF
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=windows-1251">
</head>
<body>
	{$html}
</body>
</html>
EOF;
		$bl = file_put_contents($report_name, $content);
//Fn::debugToLog('set file',$bl);
	}

	function action_deletefile() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		$dir_name = "shopv2/shop-k/";
		$filename = "goods_".$clientID.".csv";
		if (file_exists($dir_name.$filename)){
			echo "файл найден ($dir_name$filename)\n";
			$fp = unlink($dir_name . $filename);
			if (!$fp) {
				echo "ошибка удаления файла ($dir_name$filename)\n";
			}else{
				echo "файл удален ($dir_name$filename)\n";
			}
		}else{
				echo "файл не найден ($dir_name$filename)\n";
		}
	}
	function action_rename_image() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		$dir_name = "images_goods/";
		$goodid = $new_name;
		$ext = explode('.', $old_name);
		$new_name .= '.' . $ext[1];
		$response = new stdClass();
		$response->error = '';
		$response->success = false;
		$response->url = '';
		if (file_exists($dir_name.$old_name)){
			if (rename($dir_name . $old_name,  $dir_name . $new_name)) {
				$response->url = $_SERVER['HTTP_ORIGIN'].'/'.$dir_name . $new_name;
				$response->success = true;
				if($img_good) {
					$response->success = false;
					$_REQUEST['action'] = 'ImageURL';
					$_REQUEST['goodid'] = $goodid;
					$_REQUEST['value'] = $new_name;
					$cnn = new Cnn();
					$res = $cnn->good_param_save();
					$response->success = $res->success;
					if ($response->success===false) {
						$response->error = 'Ошибка при записи данных в базу данных!';
					}
				}
			} else {
				$response->error = 'Ошибка при переименовании файла';
			}
		}else{
				$response->error = "Ошибка!<br>Файл $dir_name$old_name не найден.";
		}
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	
	function action_jqgrid3() {
		$cnn = new Cnn();
		return $cnn->get_jqgrid3();
	}

	function action_select2() {//for select2
		$cnn = new Cnn();
		return $cnn->select2();
	}
	function action_select() {//for select
		$cnn = new Cnn();
		return $cnn->select();
	}
	function action_user_list() {//for select2
		$cnn = new Cnn();
		return $cnn->user_list();
	}
	
	function action_discoundcard_history(){
		$cnn = new Cnn();
		return $cnn->discoundcard_history();
	}
	function action_discoundcard_save(){
		$cnn = new Cnn();
		return $cnn->discoundcard_save();
	}

	function action_point_save() {
		$cnn = new Cnn();
		return $cnn->point_save();
	}
	function action_project_save() {
		$cnn = new Cnn();
		return $cnn->project_save();
	}
	function action_seller_save() {
		$cnn = new Cnn();
		return $cnn->seller_save();
	}
	function action_task_save() {
		$cnn = new Cnn();
		return $cnn->task_save();
	}

	function action_task_info(){
        $taskid = $_REQUEST['taskid'];
		if ($taskid != ''){
//			Fn::debugToLog("task", "info ".$taskid);
			$cnn = new Cnn();
			$response = new stdClass();
			$response->success = $cnn->task_info();
//			$response->message = 'Возникла ошибка при получении информации!<br>Сообщите разработчику!';
//			$response->new_id = 0;
			echo json_encode($response);
			return;
//			return $cnn->task_info();
		}
		//return $cnn->discoundcard_save();
	} 
	
	function action_user_save() {
		$cnn = new Cnn();
		return $cnn->user_save();
	}
	function action_menu_users_save() {
		$cnn = new Cnn();
		return $cnn->menu_users_save();
	}
	function action_point_users_save() {
		$cnn = new Cnn();
		return $cnn->point_users_save();
	}

	function action_good_set_param(){
		$cnn = new Cnn();
		return $cnn->good_param_save();
	}
	function action_balance_min_set(){
		$cnn = new Cnn();
		return $cnn->balance_min_set();
	}
	function action_balance_min_set_auto(){
		$cnn = new Cnn();
		return $cnn->balance_min_set_auto();
	}

	function action_reasons_save() {
		$cnn = new Cnn();
		return $cnn->reasons_save();
	}

	public function action_doc_edit() {
		$cnn = new Cnn();
		$cnn->doc_edit();
	}
	public function action_doc_info_full() {
		$cnn = new Cnn();
		$cnn->doc_info_full();
	}
	public function action_doc_info() {
		$cnn = new Cnn();
		$cnn->doc_info();
	}
	public function action_config() {
		$cnn = new Cnn();
		return $cnn->config();
	}

	public function action_tree_NS() {
		$cnn = new Cnn();
		$cnn->tree_NS();
	}

	public function action_select_search() {//for select2
		$cnn = new Cnn();
		return $cnn->select_search();
	}
	public function action_card_animal() {//for select2
		$cnn = new Cnn();
		return $cnn->card_animal();
	}

	public function action_captcha() {
		// создаем случайное число и сохраняем в сессии
		$randomnr = rand(1000, 9999);
		$_SESSION['captcha'] = md5($randomnr);
//Fn::debugToLog("captcha set", $randomnr);
//Fn::debugToLog("captcha set", $_SESSION['captcha']);
		//создаем изображение
		$im = imagecreatetruecolor(120, 60);

		//цвета:
		$white = imagecolorallocate($im, 255, 255, 255);
		$blue = imagecolorallocate($im, 0, 0, 255);
		$grey = imagecolorallocate($im, 130, 200, 130);
		$green = imagecolorallocate($im, 0, 255, 0);
		$black = imagecolorallocate($im, 0, 0, 0);

		//imagefilledrectangle($im, 0, 0, 200, 35, $black);
		imagefilledrectangle($im, 0, 0, 120, 60, $white);

		//путь к шрифту:
		$font = $_SERVER["DOCUMENT_ROOT"] . "/css/fonts/Karate.ttf";
		//рисуем текст:
		imagettftext($im, 30, 6, 17, 45, $grey, $font, $randomnr);
		imagettftext($im, 26, 8, 15, 50, $blue, $font, $randomnr);

		// предотвращаем кэширование на стороне пользователя
		header("Expires: Wed, 1 Jan 1997 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		//отсылаем изображение браузеру
		header("Content-type: image/gif");
		imagegif($im);
		imagedestroy($im);
	}

	
}
?>
