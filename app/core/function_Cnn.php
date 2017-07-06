<?php
class Cnn {
	private $db = null;
	public function __construct() {
		try {
			//$this->db = new PDO('mysql:host=localhost;dbname='.$_SESSION['dbname'].';port='.$_SESSION['server_port'], $_SESSION['server_user'], $_SESSION['server_pass'],array(1006));
			$this->db = new PDO('mysql:host=tor.pp.ua;dbname='.$_SESSION['dbname'].';port=43306', $_SESSION['server_user'], $_SESSION['server_pass'],array(1006));
			//$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			Fn::errorToLog("PDO error!: ", $e->getMessage());
			die();
		}
	}
	
//user login and register
	public function login($login, $pass) {
		$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger'>ВНИМАНИЕ!<br><small>Неверно введен e-mail или пароль!</small></h4>";
		$stmt = $this->db->prepare("CALL pr_login_site('login', @id, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $login, PDO::PARAM_STR);
		$stmt->bindParam(2, $pass, PDO::PARAM_STR);
		$stmt->bindParam(3, $email, PDO::PARAM_STR);
		$stmt->bindParam(4, $fio, PDO::PARAM_STR);
		$stmt->bindParam(5, $phone, PDO::PARAM_STR);
		$stmt->bindParam(6, $company, PDO::PARAM_STR);
		$stmt->bindParam(7, $post, PDO::PARAM_STR);
		$stmt->bindParam(8, $codeauth, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) return false;
		$r = $stmt->fetch(PDO::FETCH_BOTH);
		if (!$r) return false;
		if ($stmt->rowCount()==0) return false;
//Fn::debugToLog("login", json_encode($r));
		if ($r[AccessLevel]==-1){
			$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger'>"
					. "ВНИМАНИЕ!<br><small>Вы не активировали Ваш аккаунт!<br>"
					. "Вход в систему возможен только после активации!</small></h4>";
			return false;
		}
		$_SESSION['UserID'] = $r[UserID];
		$_SESSION['UserName'] = $r[UserName];
		$_SESSION['UserEMail'] = $r[EMail];
		$_SESSION['UserPost'] = $r[Position];
		$_SESSION['ClientID'] = $r[ClientID];
		$_SESSION['ClientName'] = $r[ClientName];
		$_SESSION['CompanyName'] = $r[CompanyName];
		$_SESSION['AccessLevel'] = $r[AccessLevel];
		$_SESSION['access'] = true;
		$_SESSION['error_msg'] = "";
		return true;
	}
	public function registration($login, $email, $pass, $fio, $company, $phone, $post) {
		$codeauth = rand(1111111111, 9999999999);
		$company = "Сузирье™";
//проверяем валидность e-mail
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
				. "ВНИМАНИЕ!<br><small>Указан неверный e-mail!</small></h4>";
			return false;
		}
//вызов хранимой процедуры
		$stmt = $this->db->prepare("CALL pr_login_site('register', @id, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $login, PDO::PARAM_STR);
		$stmt->bindParam(2, $pass, PDO::PARAM_STR);
		$stmt->bindParam(3, $email, PDO::PARAM_STR);
		$stmt->bindParam(4, $fio, PDO::PARAM_STR);
		$stmt->bindParam(5, $phone, PDO::PARAM_STR);
		$stmt->bindParam(6, $company, PDO::PARAM_STR);
		$stmt->bindParam(7, $post, PDO::PARAM_STR);
		$stmt->bindParam(8, $codeauth, PDO::PARAM_STR);
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) {
			$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
					. "ВНИМАНИЕ!<br><small>Ошибка при регистрации пользователя!</small></h4>";
			return false;
		}
		$result = $stmt->fetch(PDO::FETCH_BOTH);
		if (!$result) return false;
		//echo 'result='.$result[0].'<br>';
		if ($result[0] < 0) {
			$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
				. "ВНИМАНИЕ!<br><small>Пользователь с указанным именем уже зарегистрирован!</small></h4>";
			return false;
		}
		if ($result[0] == 0) {
			$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
				. "ВНИМАНИЕ!<br><small>Не удалось зарегистрировать пользователя!</small></h4>";
			return false;
		}
		if ($result[0] > 0) {
//оправляем сообщение пользователю
			$subject = 'Регистрация аккаунта в инф. системе ';
			$message = "
Здравствуйте, ".$fio."!

Ваш email был зарегистрирован в информационной системе ".$_SESSION['company']."

Для полноценной работы в нашей системе Вам необходимо активировать 
Ваш аккаунт перейдя по ссылке: http://" . $_SERVER['HTTP_HOST'] . "/register_ok/activate?auth=" . $codeauth . "

После активации Вы сможете войти в информационную систему.

Если вы получили это сообщение по ошибке, не предпринимайте никаких действий. 

Если вы не нажмете эту ссылку, то адрес не будет добавлен в аккаунт.

Успехов!
------------------
admin@" . $_SERVER['HTTP_HOST'] . "
";
			$sended = Mail::smtpmail($email, $_SESSION['adminEmail'], $fio, $subject, $message);
			if (!$sended) {
				$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
						. "ВНИМАНИЕ!<br><small>При отправке сообщения по e-mail возникли проблемы!</small></h4>";
				return false;
			}
			$sended = Mail::smtpmail($_SESSION['adminEmail'], $email, $fio, $subject, $message . 'E-mail:' . $email);
			return true;
		}
	}
	public function registration_ok($codeauth) {
//вызов хранимой процедуры
		$stmt = $this->db->prepare("CALL pr_login_site('register_ok', @id, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $login, PDO::PARAM_STR);
		$stmt->bindParam(2, $pass, PDO::PARAM_STR);
		$stmt->bindParam(3, $email, PDO::PARAM_STR);
		$stmt->bindParam(4, $fio, PDO::PARAM_STR);
		$stmt->bindParam(5, $phone, PDO::PARAM_STR);
		$stmt->bindParam(6, $company, PDO::PARAM_STR);
		$stmt->bindParam(7, $post, PDO::PARAM_STR);
		$stmt->bindParam(8, $codeauth, PDO::PARAM_STR);
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) return false;
		$result = $stmt->fetch(PDO::FETCH_BOTH);
		if (!$result) return false;
		if ($result[0] == 0) {
			$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
				. "<br>ВНИМАНИЕ!<br><br><small>Возникла ошибка при активации аккаунта!<br><br>"
				. "Сообщите разработчику!<br><br></small></h4>";
			return false;
		}
		if ($result[0] > 0) {
//оправляем сообщение пользователю
			$email = $result['EMail'];
			$fio = $result[2];
			$subject = "Активация аккаунта успешно завершена!";
			$message = "
Добро пожаловать, " . $fio . "!

Ваш аккаунт был успешно активирован в информационной системе " . $_SESSION['company'] . "

Вы можете войти в систему по адресу http://" . $_SERVER['HTTP_HOST'] . "/logon

Если вы получили это сообщение по ошибке, не предпринимайте никаких действий. 

Успехов!
------------------
admin@" . $_SERVER['HTTP_HOST'] . "
";
			$sended = Mail::smtpmail($email, $_SESSION['siteEmail'], $fio, $subject, $message);
			if (!$sended) {
				$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
						. "ВНИМАНИЕ!<br><small>При отправке сообщения по e-mail возникли проблемы!</small></h4>";
				return false;
			}
			$sended = Mail::smtpmail($_SESSION['adminEmail'], $email, $fio, $subject, $message . 'E-mail:' . $email);
			return true;
		}
	}
	public function recovery($email) {
//вызов хранимой процедуры
		$stmt = $this->db->prepare("CALL pr_login_site('recovery', @id, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $login, PDO::PARAM_STR);
		$stmt->bindParam(2, $pass, PDO::PARAM_STR);
		$stmt->bindParam(3, $email, PDO::PARAM_STR);
		$stmt->bindParam(4, $fio, PDO::PARAM_STR);
		$stmt->bindParam(5, $phone, PDO::PARAM_STR);
		$stmt->bindParam(6, $company, PDO::PARAM_STR);
		$stmt->bindParam(7, $post, PDO::PARAM_STR);
		$stmt->bindParam(8, $codeauth, PDO::PARAM_STR);
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) return false;
		$result = $stmt->fetch(PDO::FETCH_BOTH);
		if (!$result) return false;
		if ($result[0] == 0) {
			$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
				. "<br>ВНИМАНИЕ!<br><br><small>E-mail ".$email." не найден!<br><br>"
				. "Возможно Вы неправильно указали e-mail!<br><br></small></h4>";
			return false;
		}
		if ($result[0] > 0) {
//оправляем сообщение пользователю
			$fio = $result[2];
			$subject = 'Восстановление пароля для доступа к информационной системе ' . $_SESSION['company'];
			$message = "
Здравствуйте, " . $fio . "!

Вы можете войти в систему по адресу http://" . $_SERVER['HTTP_HOST'] . "/logon

Ваш пароль: " . $result[1] . "

Если вы получили это сообщение по ошибке, не предпринимайте никаких действий. 

Успехов!
------------------
admin@" . $_SERVER['HTTP_HOST'] . "
";
			$sended = Mail::smtpmail($email, $_SESSION['siteEmail'], $fio, $subject, $message);
			if (!$sended) {
				$_SESSION['error_msg'] = "<h4 class='center list-group-item list-group-item-danger m0'>"
						. "ВНИМАНИЕ!<br><small>При отправке сообщения по e-mail возникли проблемы!</small></h4>";
				return false;
			}
			$sended = Mail::smtpmail($_SESSION['adminEmail'], $email, $fio, 'Регистрация: ' . $fio, $message_admin);
			return true;
		}
	}

//reports
	public function get_report1_data() {
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
		//echo $DT_start.'<br>';
		if(isset($DT_start)){
			$dt = DateTime::createFromFormat('d?m?Y', $DT_start);
		//echo $dt->format('Ymd');
			$date1 = $dt->format('Ymd');
		}
		if (isset($DT_stop)) {
			$dt = DateTime::createFromFormat('d?m?Y', $DT_stop);
			$date2 = $dt->format('Ymd');
		}
Fn::debugToLog('report1 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::debugToLog('report1 user:' . $_SESSION['UserName'], "".$date1."	".  $date2);
		//call pr_reports('avg_sum', @_id, '20141001', '20141031', '');
		$stmt = $this->db->prepare("CALL pr_reports('avg_sum', @id, ?, ?, ?)");
		$stmt->bindParam(1, $date1, PDO::PARAM_STR);
		$stmt->bindParam(2, $date2, PDO::PARAM_STR);
		$stmt->bindParam(3, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
//		$r = $stmt->fetch(PDO::FETCH_BOTH);
//		if (!$r)
//			return false;
		if ($stmt->rowCount() == 0)
			return false;
		$response = new stdClass();
//		$response->draw = 1;
//		$response->recordsTotal = 28;
//		$response->recordsFiltered = 28;
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				$i = 0;
				foreach ($rowset as $row) {
					$response->data[$i] = array(
												$row['ClientID'],
												$row['NameShort'],
												$row['City'],
												$row['Avg_Sum'],
												$row['Avg_Check'],
												$row['DT_start'],
												$row['DT_stop'],
												$row['DayWork']
					);
					$i++;
				}
			}
		} while ($stmt->nextRowset());
		//header("Content-type: application/json;charset=utf8");
		//Fn::debugToLog("resp", json_encode($response));
		return json_encode($response);
	}
	public function get_report2_data() {
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
		//echo $DT_start.' '.  $DT_stop . '<br>';
		if(isset($DT_start)){
			$dt = DateTime::createFromFormat('d?m?Y', $DT_start);
		//echo $dt->format('Ymd').'<br>';
			$date1 = $dt->format('Ymd');
		}else{return;}
		if (isset($DT_stop)) {
			$dt = DateTime::createFromFormat('d?m?Y', $DT_stop);
		//echo $dt->format('Ymd');
			$date2 = $dt->format('Ymd');
		}else{return;}
Fn::debugToLog('report2 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
		//call pr_reports('avg_sum', @_id, '20141001', '20141031', '');
		$stmt = $this->db->prepare("CALL pr_reports('sale_Trixie', @id, ?, ?, null)");
		$stmt->bindParam(1, $date1, PDO::PARAM_STR);
		$stmt->bindParam(2, $date2, PDO::PARAM_STR);
		//$stmt->bindParam(3, "nnn", PDO::PARAM_STR);
		//$stmt->bindParam(3, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
//		$r = $stmt->fetch(PDO::FETCH_BOTH);
//		if (!$r)
//			return false;
		if ($stmt->rowCount() == 0)
			return false;
		$response = new stdClass();
//		$response->draw = 1;
//		$response->recordsTotal = 28;
//		$response->recordsFiltered = 28;
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				$i = 0;
				foreach ($rowset as $row) {
					$response->data[$i] = array(
												$row['PointName'],
												$row['SellerName'],
												$row['GoodArticle'],
												$row['GoodName'],
												$row['Quantity'],
												$row['Sebest'],
												$row['Oborot'],
												$row['Dohod'],
												$row['Percent'],
					);
					$i++;
				}
			}
		} while ($stmt->nextRowset());
		header("Content-type: application/json;charset=utf8");
		return json_encode($response);
	}
	public function get_report4_data() {
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
Fn::debugToLog('report4 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::paramToLog();  
//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt = DateTime::createFromFormat('d?m?Y', $DT_start);
			//echo $dt->format('Ymd').'<br>';
			$date1 = $dt->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt = DateTime::createFromFormat('d?m?Y', $DT_stop);
			//echo $dt->format('Ymd');
			$date2 = $dt->format('Ymd');
		} else {
			return;
		}
		$action = '';
		if ($sid=='4')  $action = 'sale';
		if ($sid=='42') $action = 'sale42';
		if ($sid=='8')  $action = 'sale';
//Fn::debugToLog("report4", 'action='.$action);
		//$url = 'ddd=1&'.urldecode($_SERVER['QUERY_STRING']);
		//call pr_reports('avg_sum', @_id, '20141001', '20141031', '');
		$stmt = $this->db->prepare("CALL pr_reports(?, @id, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $date1, PDO::PARAM_STR);
		$stmt->bindParam(3, $date2, PDO::PARAM_STR);
		$stmt->bindParam(4, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		header("Content-type: application/json;charset=utf8");
		$response = new stdClass();
		$response->page = 1;
		$response->total = 1;
		$response->records = 0;
		$response->error = '';
		if (!Fn::checkErrorMySQLstmt($stmt)) $response->error = $stmt->errorInfo();
//	Fn::debugToLog("resp", json_encode($response));
		if ($stmt->rowCount() > 0){
			$t = 0;
			do {
				$rowset = $stmt->fetchAll();
				if ($rowset!=null) {
					if($t==1){
						foreach ($rowset as $row) {
	//			Fn::debugToLog($t, $row[0]);
							$response->query = $row[0];
							$response->records = $row[1];
						}
					}else if ($t == 0) {
				//Fn::debugToLog("columnCount 2", $stmt->columnCount());
						$columnCount = $stmt->columnCount();
						$i = 0;
						foreach ($rowset as $row) {
							$response->rows[$i]['id'] = $row[0];
							$ar = array();
							for($f=0;$f<$columnCount-6;$f++){
								$ar[] = $row[$f];
							}
							$ar = array_pad($ar,10,null);
							for ($f = $columnCount - 6; $f < $columnCount; $f++) {
								$ar[] = $row[$f];
							}
							$response->rows[$i]['cell'] = $ar;
							$i++;
						}
					}
				}
				$t++;
			} while ($stmt->nextRowset());
		}
//Fn::debugToLog("report4", json_encode($response));
		echo json_encode($response);
	}
	public function get_report5_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
Fn::debugToLog('report5 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_reports('goods', @id, ?, ?, ?)");
		$stmt->bindParam(1, $date1, PDO::PARAM_STR);
		$stmt->bindParam(2, $date2, PDO::PARAM_STR);
		$stmt->bindParam(3, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		header("Content-type: application/json;charset=utf8");
		$response = new stdClass();
		$response->page = 1;
		$response->total = 1;
		$response->records = 0;
		$response->query = "";
		$response->error = '';
		if (!Fn::checkErrorMySQLstmt($stmt)) $response->error = $stmt->errorInfo();
		//	Fn::debugToLog("resp", json_encode($response));
		if ($stmt->rowCount() > 0){
			$t = 0;
			do {
				$rowset = $stmt->fetchAll();
				if ($rowset != null) {
					if ($t == 1) {
						foreach ($rowset as $row) {
	//			Fn::debugToLog($t, $row[0]);
							$response->query = $row[0];
							$response->records = $row[1];
						}
					} else if ($t == 0) {
						//Fn::debugToLog("columnCount 2", $stmt->columnCount());
						$columnCount = $stmt->columnCount();
						$i = 0;
						foreach ($rowset as $row) {
							$response->rows[$i]['id'] = $row[0];
							$ar = array();
							for ($f = 0; $f < $columnCount - 7; $f++) {
								$ar[] = $row[$f];
							}
							$ar = array_pad($ar, 8, null);
							for ($f = $columnCount - 7; $f < $columnCount; $f++) {
								$ar[] = $row[$f];
							}
							$response->rows[$i]['cell'] = $ar;
							$i++;
						}
					}
				}
				$t++;
			} while ($stmt->nextRowset());
		}
		echo json_encode($response);
	}
	public function get_report6_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::debugToLog('report5 user:'.  $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::debugToLog('REQUEST_URI', urldecode($_SERVER['REQUEST_URI']));
Fn::debugToLog('report6 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_reports('goods_action', @id, ?, ?, ?)");
		$stmt->bindParam(1, $date1, PDO::PARAM_STR);
		$stmt->bindParam(2, $date2, PDO::PARAM_STR);
		$stmt->bindParam(3, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		header("Content-type: application/json;charset=utf8");
		$response = new stdClass();
		$response->page = 1;
		$response->total = 1;
		$response->records = 0;
		$response->query = "";
		$response->error = '';
		if (!Fn::checkErrorMySQLstmt($stmt))
			$response->error = $stmt->errorInfo();
		//	Fn::debugToLog("resp", json_encode($response));
		if ($stmt->rowCount() > 0) {
			$t = 0;
			do {
				$rowset = $stmt->fetchAll();
				if ($rowset != null) {
					if ($t == 1) {
						foreach ($rowset as $row) {
							//			Fn::debugToLog($t, $row[0]);
							$response->query = $row[0];
							$response->records = $row[1];
						}
					} else if ($t == 0) {
						//Fn::debugToLog("columnCount 2", $stmt->columnCount());
						$columnCount = $stmt->columnCount();
						$start_col = 3;
						$max_col = 15;
						$i = 0;
						foreach ($rowset as $row) {
							$response->rows[$i]['id'] = $row[0];
							$ar = array();
							for ($f = 0; $f < $start_col; $f++) {
								$ar[] = $row[$f];
							}
							$ar = array_pad($ar, $max_col - $columnCount + $start_col, null);
							for ($f = $start_col; $f < $columnCount; $f++) {
								$ar[] = $row[$f];
							}
							$response->rows[$i]['cell'] = $ar;
//Fn::debugToLog("ar", json_encode($ar));
							$i++;
						}
					}
				}
				$t++;
			} while ($stmt->nextRowset());
		}
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	public function get_report7_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
Fn::debugToLog('report7 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
		//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt = DateTime::createFromFormat('d?m?Y', $DT_start);
			//echo $dt->format('Ymd').'<br>';
			$date1 = $dt->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt = DateTime::createFromFormat('d?m?Y', $DT_stop);
			//echo $dt->format('Ymd');
			$date2 = $dt->format('Ymd');
		} else {
			return;
		}
//Fn::debugToLog('report', $date1.'	'.$date2);
//Fn::paramToLog();
		//$url = 'ddd=1&'.urldecode($_SERVER['QUERY_STRING']);
		//call pr_reports('avg_sum', @_id, '20141001', '20141031', '');
		$stmt = $this->db->prepare("CALL pr_reports('sale_opt', @id, ?, ?, ?)");
		$stmt->bindParam(1, $date1, PDO::PARAM_STR);
		$stmt->bindParam(2, $date2, PDO::PARAM_STR);
		//$stmt->bindParam(3, $url, PDO::PARAM_STR);
		$stmt->bindParam(3, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		header("Content-type: application/json;charset=utf8");
		$response = new stdClass();
		$response->page = 1;
		$response->total = 1;
		$response->records = 0;
		$response->error = '';
		if (!Fn::checkErrorMySQLstmt($stmt))
			$response->error = $stmt->errorInfo();
//	Fn::debugToLog("resp", json_encode($response));
		if ($stmt->rowCount() > 0) {
			$t = 0;
			do {
				$rowset = $stmt->fetchAll();
				if ($rowset != null) {
					if ($t == 1) {
						foreach ($rowset as $row) {
							//			Fn::debugToLog($t, $row[0]);
							$response->query = $row[0];
							$response->records = $row[1];
						}
					} else if ($t == 0) {
						//Fn::debugToLog("columnCount 2", $stmt->columnCount());
						$columnCount = $stmt->columnCount();
						$i = 0;
						foreach ($rowset as $row) {
							$response->rows[$i]['id'] = $row[0];
							$ar = array();
							for ($f = 0; $f < $columnCount - 5; $f++) {
								$ar[] = $row[$f];
							}
							$ar = array_pad($ar, 10, null);
							for ($f = $columnCount - 5; $f < $columnCount; $f++) {
								$ar[] = $row[$f];
							}
							$response->rows[$i]['cell'] = $ar;
							$i++;
						}
					}
				}
				$t++;
			} while ($stmt->nextRowset());
		}
		return json_encode($response);
	}
	public function get_report8_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		Fn::debugToLog('report8 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::paramToLog();  
//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt1 = DateTime::createFromFormat('d?m?Y', $DT_start);
			$date1 = $dt1->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt2 = DateTime::createFromFormat('d?m?Y', $DT_stop);
			$date2 = $dt2->format('Ymd');
		} else {
			return;
		}
		$action = '';
		if ($sid == '8') $action = 'sale8';
		$stmt = $this->db->prepare("CALL pr_reports(?, @id, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $date1, PDO::PARAM_STR);
		$stmt->bindParam(3, $date2, PDO::PARAM_STR);
		$stmt->bindParam(4, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$response = new stdClass();
		$response->error = '';
		$response->table1 = '';

		if (!Fn::checkErrorMySQLstmt($stmt)) $response->error = $stmt->errorInfo();
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		$dg = array();
		$dgi = array();
		$dgiall = array();
		
		if ($rowset) {
			//данные из таблицы переносим в массив
			foreach ($rowset as $row) {
				$value1 = $row['Quantity'];
				$value2 = $row['Oborot'];
				$value3 = $row['Dohod'];
				$dg[$row[0].$row[2]]['name1'] = $row[1];
				$dg[$row[0].$row[2]]['name2'] = $row[3];
				$dg[$row[0].$row[2]][$row['intervals']]['q'] = $value1;
				$dg[$row[0].$row[2]][$row['intervals']]['o'] = $value2;
				$dg[$row[0].$row[2]][$row['intervals']]['d'] = $value3;
				$dg[$row[0].$row[2]]['ID1'] = $row[0];
				$dgi[$row[0]]['name1'] = 'Итого:';
				if (!isset($dgi[$row[0]][$row['intervals']]['q'])) $dgi[$row[0]][$row['intervals']]['q'] = 0;
				if (!isset($dgi[$row[0]][$row['intervals']]['o'])) $dgi[$row[0]][$row['intervals']]['o'] = 0;
				if (!isset($dgi[$row[0]][$row['intervals']]['d'])) $dgi[$row[0]][$row['intervals']]['d'] = 0;
				$dgi[$row[0]][$row['intervals']]['q'] += $value1;
				$dgi[$row[0]][$row['intervals']]['o'] += $value2;
				$dgi[$row[0]][$row['intervals']]['d'] += $value3;
				if (!isset($dgiall[$row['intervals']]['q'])) $dgiall[$row['intervals']]['q'] = 0;
				if (!isset($dgiall[$row['intervals']]['o'])) $dgiall[$row['intervals']]['o'] = 0;
				if (!isset($dgiall[$row['intervals']]['d'])) $dgiall[$row['intervals']]['d'] = 0;
				$dgiall[$row['intervals']]['q'] += $value1;
				$dgiall[$row['intervals']]['o'] += $value2;
				$dgiall[$row['intervals']]['d'] += $value3;
			}
		}
		
//Fn::debugToLog('dg',json_encode($dg));
//Fn::debugToLog('dgi',json_encode($dgi));

		if ($interval=='day') {
			$increment = '+1 day'; $dt_format = 'Ymd'; $dt_html = 'Y-m-d';
		}elseif ($interval=='week') {
			$increment = '+1 week'; $dt_format = 'Y_W'; $dt_html = 'W(нед.)';
		}elseif ($interval=='month') {
			$increment = '+1 month'; $dt_format = 'Ym'; $dt_html = 'Y-m';
		}elseif ($interval=='year') {
			$increment = '+1 year'; $dt_format = 'Y'; $dt_html = 'Y';
		}
		$cnt_group = substr_count($grouping,";");
		$col = 1;
		if ($data=='all') $col = 3;
		//выводим шапку таблицы
		$str = '';
		$str .= '<table id="table1" class="table table-striped table-bordered" cellspacing="0"  width="100%">';
		$str .= '<thead><tr>
				<th rowspan=2>'.$groupName1.'</th>';
		if ($cnt_group==2) $str .= '<th rowspan=2>'.$groupName2.'</th>';
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			$str .= '<th colspan='. $col .'>' . $dt->format($dt_html) . '</th>';
		}
		$str .= '</tr><tr>';
		$cnt_col = 0;
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			if ($data == 'Quantity') $str .= '<th>Кол-во</th>';
			if ($data == 'Oborot') $str .= '<th>Оборот</th>';
			if ($data == 'Dohod') $str .= '<th>Доход</th>';
			if ($data == 'all') {
				$str .= '<th>Кол-во</th>';
				$str .= '<th>Оборот</th>';
				$str .= '<th>Доход</th>';
				$cnt_col+=2;
			}
			$cnt_col ++;
		}
		$str .= '</tr></thead>';
		//выводим данные в таблице
		$str .= '<tbody>';
		$a = array_keys($dg);
		$ai = array_keys($dgi);
//Fn::debugToLog('a', json_encode($a));
//Fn::debugToLog('ai', json_encode($ai));
		$total = $dg[$a[0]]['ID1'];
		$cnt_in_grp = 0;
		for ($i = 0;$i<count($a);$i++){
			$id1 = $dg[$a[$i]]['ID1'];
			if ($total !== $id1 && $cnt_group == 2)	$cnt_in_grp = 0;
//Fn::debugToLog("row", $total.'	'.$id1.'	'.  $cnt_in_grp.'	'.  $cnt_group.'	cnt_col='.  $cnt_col);
			if ($total !== $id1 && $cnt_group == 2){
				//выводим итоги
				//$cnt_in_grp = 0;
				$str .= '<tr>';
				$str .= '<th colspan=2 class="TAL">' . $dgi[$total][name1] . '</th>';
				for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
					if ($data == 'Quantity') $str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['q']) . '</th>';
					if ($data == 'Oborot') $str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['o']) . '</th>';
					if ($data == 'Dohod') $str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['d']) . '</th>';
					if ($data == 'all') {
						$str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['q']) . '</th>';
						$str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['o']) . '</th>';
						$str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['d']) . '</th>';
					}
				}
				$str .= '</tr>';
				$str .= '<tr><td colspan='.$cnt_group.'></td>';
				for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
					$str .= '<td colspan='.$col.'></td>';
				}
				$str .= '</tr>';
				$total = $id1;
			}
			if ($cnt_in_grp == 0 && $cnt_group == 2) {
				$str .= '<tr>';
				$str .= '<th colspan=' . ($cnt_col + 2) . ' class="TAL">' . $dg[$a[$i]][name1] . '</th>';
				$str .= '</tr>';
			}
			$cnt_in_grp ++;
			//выводим строки таблицы
			$str .= '<tr>';
			if ($cnt_group == 2) {
				$str .= '<td class="TAL max200"></td>';
				$str .= '<td class="TAL max200">'.$dg[$a[$i]][name2].'</td>';
			}else{
				$str .= '<td class="TAL max200">'.$dg[$a[$i]][name1].'</td>';
			}
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				if ($data == 'Quantity') $str .= '<td class="TAR">' . $dg[$a[$i]][$dt->format($dt_format)]['q'] . '</td>';
				if ($data == 'Oborot')	 $str .= '<td class="TAR">' . $dg[$a[$i]][$dt->format($dt_format)]['o'] . '</td>';
				if ($data == 'Dohod')	 $str .= '<td class="TAR">' . $dg[$a[$i]][$dt->format($dt_format)]['d'] . '</td>';;
				if ($data == 'all') {
					$str .= '<td class="TAR">'.$dg[$a[$i]][$dt->format($dt_format)]['q'] .'</td>';
					$str .= '<td class="TAR">'.$dg[$a[$i]][$dt->format($dt_format)]['o'] .'</td>';
					$str .= '<td class="TAR">'.$dg[$a[$i]][$dt->format($dt_format)]['d'] .'</td>';
				}
			}
			$str .= '</tr>';
		}
		//выводим итоги последней группы
		if ($cnt_group == 2){
			$str .= '<tr>';
			$str .= '<th colspan=2 class="TAL">' . $dgi[$total][name1] . '</th>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				if ($data == 'Quantity') $str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['q']) . '</th>';
				if ($data == 'Oborot')	 $str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['o']) . '</th>';
				if ($data == 'Dohod')	 $str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['d']) . '</th>';
				if ($data == 'all') {
					$str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['q']) . '</th>';
					$str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['o']) . '</th>';
					$str .= '<th class="TAR">' . Fn::nf($dgi[$total][$dt->format($dt_format)]['d']) . '</th>';
				}
			}
			$str .= '</tr>';
		}
		$str .= '<tr>';
		$str .= '<th colspan='.$cnt_group.' class="TAL">Общие итоги:</th>';
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			if ($data == 'Quantity') $str .= '<th class="TAR">' . Fn::nf($dgiall[$dt->format($dt_format)]['q']) . '</th>';
			if ($data == 'Oborot')	 $str .= '<th class="TAR">' . Fn::nf($dgiall[$dt->format($dt_format)]['o']) . '</th>';
			if ($data == 'Dohod')	 $str .= '<th class="TAR">' . Fn::nf($dgiall[$dt->format($dt_format)]['d']) . '</th>';
			if ($data == 'all') {
				$str .= '<th class="TAR">' . Fn::nf($dgiall[$dt->format($dt_format)]['q']) . '</th>';
				$str .= '<th class="TAR">' . Fn::nf($dgiall[$dt->format($dt_format)]['o']) . '</th>';
				$str .= '<th class="TAR">' . Fn::nf($dgiall[$dt->format($dt_format)]['d']) . '</th>';
			}
		}
		$str .= '</tr>';
		$str .= "</table>";
		$response->table1 = $str;
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	public function get_report9_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		Fn::debugToLog('report9 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::paramToLog();  
//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt1 = DateTime::createFromFormat('d?m?Y', $DT_start);
			$date1 = $dt1->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt2 = DateTime::createFromFormat('d?m?Y', $DT_stop);
			$date2 = $dt2->format('Ymd');
		} else {
			return;
		}
		$action = '';
		if ($sid == '9')
			$action = 'sale8';
		$stmt = $this->db->prepare("CALL pr_reports(?, @id, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $date1, PDO::PARAM_STR);
		$stmt->bindParam(3, $date2, PDO::PARAM_STR);
		$stmt->bindParam(4, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$response = new stdClass();
		$response->error = '';
		$response->table1 = '';

		if (!Fn::checkErrorMySQLstmt($stmt)) $response->error = $stmt->errorInfo();
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);

		$dg = array();
		if ($rowset) {
			//данные из таблицы переносим в массив
			foreach ($rowset as $row) {
				$value1 = $row['Quantity'];
				$value2 = $row['Oborot'];
				$value3 = $row['Dohod'];
				$dg[$row[0] . $row[2]]['name1'] = $row[1];
				$dg[$row[0] . $row[2]]['interval'] = $row['intervals'];
				$dg[$row[0] . $row[2]][$row['intervals']]['q'] = $value1;
				$dg[$row[0] . $row[2]][$row['intervals']]['o'] = $value2;
				$dg[$row[0] . $row[2]][$row['intervals']]['d'] = $value3;
				//$dg[$row[0] . $row[2]]['ID1'] = $row[0];
			}
		}
		if ($interval == 'day') {
			$increment = '+1 day';
			$dt_format = 'Ymd';
			$dt_html = 'Y-m-d';
		} elseif ($interval == 'week') {
			$increment = '+1 week';
			$dt_format = 'Y_W';
			$dt_html = 'W(нед.)';
		} elseif ($interval == 'month') {
			$increment = '+1 month';
			$dt_format = 'Ym';
			$dt_html = 'Y-m';
		} elseif ($interval == 'year') {
			$increment = '+1 year';
			$dt_format = 'Y';
			$dt_html = 'Y';
		}

		$a = array_keys($dg);
		$res= array();
		$lb = array();
		$ds = array();
		$el = array();
		$i	= 0;
		for ($dt = clone $dt1; $dt < $dt2; $dt->modify($increment)) {
			$lb[$i]= $dt->format($dt_html);
			$i++;
		}
//Fn::debugToLog("dg", json_encode($dg));
//Fn::debugToLog("a", json_encode($a));
//Fn::debugToLog("lb", json_encode($lb));
		$opacity = 0.5;
		if ($chart=='line')	$opacity = 0;
		if ($chart=='bar')	$opacity = 1;
		if ($chart=='radar')$opacity = 0.1;
		if ($chart == 'polar' || $chart == 'pie' || $chart == 'doughnut') {
			for ($i = 0;$i<count($a);$i++){
				$red = rand(10, 235); $green = rand(10, 235); $blue = rand(10, 235);
				$res[$i]['label'] = $lb[$i];
				$res[$i]['color'] = sprintf("#%'.02X%'.02X%'.02X", $red, $green, $blue);
				$res[$i]['highlight'] = sprintf("#%'.02X%'.02X%'.02X", $red+20, $green  + 20, $blue  + 20);
				//$res[$i]['highlight'] = $res[$i]['color'];
				if ($data == 'Quantity')
					$res[$i]['value'] = Fn::isnull($dg[$a[$i]][$dg[$a[$i]]['interval']]['q'], 0);
				if ($data == 'Oborot')
					$res[$i]['value'] = Fn::isnull($dg[$a[$i]][$dg[$a[$i]]['interval']]['o'], 0);
				if ($data == 'Dohod')
					$res[$i]['value'] = Fn::isnull($dg[$a[$i]][$dg[$a[$i]]['interval']]['d'], 0);
			}
		}elseif ($chart == 'polar2' || $chart == 'pie2' || $chart == 'doughnut2') {
			for ($i = 0;$i<count($a);$i++){
				$red = rand(10, 235); $green = rand(10, 235); $blue = rand(10, 235);
				$res[$i]['label'] = $dg[$a[$i]]['name1'];
				$res[$i]['color'] = sprintf("#%'.02X%'.02X%'.02X", $red, $green, $blue);
				$res[$i]['highlight'] = sprintf("#%'.02X%'.02X%'.02X", $red+20, $green  + 20, $blue  + 20);
				//$res[$i]['highlight'] = $res[$i]['color'];
				if (!isset($res[$i]['value'])) $res[$i]['value'] = 0;
				if ($data == 'Quantity')
					$res[$i]['value'] += Fn::isnull($dg[$a[$i]][$dg[$a[$i]]['interval']]['q'], 0);
				if ($data == 'Oborot')
					$res[$i]['value'] += Fn::isnull($dg[$a[$i]][$dg[$a[$i]]['interval']]['o'], 0);
				if ($data == 'Dohod')
					$res[$i]['value'] += Fn::isnull($dg[$a[$i]][$dg[$a[$i]]['interval']]['d'], 0);
				Fn::debugToLog("res", json_encode($res[$i]));
			}
		}else{
			$res['labels'] = $lb;
			for ($i=0;$i<count($a);$i++){
				$red = rand(0, 255); $green = rand(0, 255);	$blue = rand(0, 255);
				$el['label'] = $dg[$a[$i]]['name1'];
				$el['fillColor'] = "rgba($red,$green,$blue,$opacity)";
				$el['strokeColor'] = "rgba($red,$green,$blue,1)";
				$el['highlightFill'] = "rgba($red,$green,$blue,0.75)";
				$el['highlightStroke'] = "rgba($red,$green,$blue,1)";
				$el['pointColor'] = "rgba($red,$green,$blue,1)";
				//$el['pointStrokeColor'] = "rgba($red,$green,$blue,1)";
				$el['pointStrokeColor'] = "#fff";
				$el['pointHighlightFill'] = "#fff";
				$el['pointHighlightStroke'] = "rgba($red,$green,$blue,1)";
				$set = array();
				$n = 0;
				for ($dt = clone $dt1; $dt < $dt2; $dt->modify($increment)) {
					if ($data == 'Quantity')
						$set[$n] = Fn::isnull($dg[$a[$i]][$dt->format($dt_format)]['q'],0);
					if ($data == 'Oborot')
						$set[$n] = Fn::isnull($dg[$a[$i]][$dt->format($dt_format)]['o'],0);
					if ($data == 'Dohod')
						$set[$n] = Fn::isnull($dg[$a[$i]][$dt->format($dt_format)]['d'],0);
					$n++;
				}
	//$el['data'] = array(rand(0, 100),  rand(0, 100),  rand(0, 100),  rand(0, 100), rand(0, 100));
				$el['data'] = $set;
				$ds[$i] = $el;
			}
			$res['datasets'] = $ds;
		}
		$response->data = $res;
//Fn::debugToLog("response", json_encode($response));
//Fn::debugToLog("ar", json_encode($ar));
//Fn::debugToLog("ds", json_encode($ds));
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	public function get_report10_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
Fn::debugToLog('report10 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::paramToLog();  
//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt1 = DateTime::createFromFormat('d?m?Y', $DT_start);
			$date1 = $dt1->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt2 = DateTime::createFromFormat('d?m?Y', $DT_stop);
			$date2 = $dt2->format('Ymd');
		} else {
			return;
		}
		
		$action = 'discount_'.$repid;
		$stmt = $this->db->prepare("CALL pr_reports(?, @id, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $date1, PDO::PARAM_STR);
		$stmt->bindParam(3, $date2, PDO::PARAM_STR);
		$stmt->bindParam(4, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$response = new stdClass();
		$response->error = '';
		$response->table1 = '';

		if (!Fn::checkErrorMySQLstmt($stmt))
			$response->error = $stmt->errorInfo();
		$rowset = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$dg = array(); $total = array(); $total['cnt'] = 0;	$total['Qty'] = 0; $total['Sum'] = 0;
		if ($repid == 'rep1') {$grouping = 'cl.ClientID'; $groupkey = $grouping;}
		if ($repid == 'rep2') {$grouping = 'cl.ClientID'; $groupkey = $grouping;}
		if ($repid == 'rep3') {$grouping = 'cl.ClientID'; $groupkey = $grouping;}
		if ($repid == 'rep4') {$grouping = 'cl.ClientID'; $groupkey = $grouping;}
		if ($grouping == 'cl.ClientID')	$groupkey = $grouping;
		if ($grouping == 'GoodID')		$groupkey = $grouping;
		if ($grouping == 'g.Brand')		$groupkey = 'Brand';
		if ($grouping == 's.SellerID')	$groupkey = 'SellerName';
		if ($grouping == 'p.PromoID')	$groupkey = 'PromoName';
		if ($grouping == 'ch.CheckID')	$groupkey = 'CheckID';
		if ($grouping == 'cl.City')		$groupkey = 'City';
		if ($grouping == 'catName')		$groupkey = 'CatName';
		if ($rowset) {
			//данные из таблицы переносим в массив
			foreach ($rowset as $row) {
				if ($dg[$row[$groupkey]]==null) $dg[$row[$groupkey]] = 0;
				$dg[$row[$groupkey]] += $row['cnt'];
				$total['cnt'] += $row['cnt'];
				$total['Qty'] += $row['Qty'];
				$total['Sum'] += $row['Sum'];
			}
		}
//Fn::debugToLog("dg", json_encode($dg));
		//выводим шапку таблицы
		$str = '';
		$str .= '<table id="table1" class="table table-striped table-bordered" cellspacing="0"  width="100%">';
		$str .= '<thead><tr>';
		if($repid == 'rep3'){
			$str .= '<th>Торговая точка (где была активна карта)</th>';
			$str .= '<th>Кол-во карт</th>';
			$str .= '<th>% стартовый</th>';
			$str .= '<th>% текущий</th>';
		}elseif($repid == 'rep5'){
			if ($grouping == 'cl.ClientID')	$str .= '<th>Торговая точка (где совершена покупка)</th>';
			if ($grouping == 'GoodID')		$str .= '<th>Товар</th>';
			if ($grouping == 'g.Brand')		$str .= '<th>Бренд</th>';
			if ($grouping == 's.SellerID')	$str .= '<th>Сотрудник</th>';
			if ($grouping == 'p.PromoID')	$str .= '<th>Акция</th>';
			if ($grouping == 'ch.CheckID')	$str .= '<th>Чек</th>';
			if ($grouping == 'cl.City')		$str .= '<th>Город</th>';
			if ($grouping == 'catName')		$str .= '<th>Категория товара</th>';
			if ($grouping == 'cattypeName')	$str .= '<th>Вид животного</th>';
			$str .= '<th>Кол-во карт</th>';
			$str .= '<th>Кол-во товара</th>';
			$str .= '<th>Сумма товара</th>';
		}else{
			$str .= '<th>Торговая точка (которая выдала карту)</th>';
			$str .= '<th>Кол-во карт</th>';
			$str .= '<th>% стартовый</th>';
			$str .= '<th>% текущий</th>';
		}
		$str .= '</tr></thead>';
		//выводим данные в таблице
		$id = 0; 
		$str .= '<tbody>';
		foreach ($rowset as $row) {
			//выводим итоги группы
			if ($id != $row[$groupkey]){
				if ($dg[$id]!=null && $repid!='rep4' && $repid != 'rep2' && $repid != 'rep5') {
					$str .= '<tr><th>ИТОГО:</th><th class="TAC">'.$dg[$id].'</th><th colspan=2></th></tr>';
					$str .= '<tr><th colspan=4></th></tr>';
				}
				$id = $row[$groupkey];
			}
			$str .= '<tr>';
			if ($grouping == 'cl.ClientID')	$str .= '<td class="TAL">' . $row['NameShort'] . '</td>';
			if ($grouping == 'GoodID')		$str .= '<td class="TAL">' . $row['Article'] .'<br>'. $row['Name']. '</td>';
			if ($grouping == 'g.Brand')		$str .= '<td class="TAL">' . $row['Brand'] . '</td>';
			if ($grouping == 's.SellerID')	$str .= '<td class="TAL">' . $row['SellerName'] . '</td>';
			if ($grouping == 'p.PromoID')	$str .= '<td class="TAL">' . $row['PromoName'] . '</td>';
			if ($grouping == 'ch.CheckID')	$str .= '<td class="TAL">' . $row['CheckID'] .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row['NameShort']. '</td>';
			if ($grouping == 'cl.City')		$str .= '<td class="TAL">' . $row['City'] . '</td>';
			if ($grouping == 'catName')		$str .= '<td class="TAL">' . $row['CatName'] . '</td>';
			if ($grouping == 'cattypeName')	$str .= '<td class="TAL">' . $row['CattypeName'] . '</td>';
			$str .=		'<td>'.$row['cnt'].'</td>';
			if($repid != 'rep5'){
				$str .=		'<td>'.$row['StartPercent'].'</td>';
				$str .=		'<td>'.$row['PercentOfDiscount'].'</td>';
			}else{
				$str .= '<td>'.$row['Qty'].'</td>';
				$str .= '<td>'.$row['Sum'].'</td>';
			}
			$str .= '</tr>';
		}
		//выводим итоги последней группы
		if ($dg[$id] != null && $repid != 'rep4' && $repid != 'rep2' && $repid != 'rep5')
			$str .= '<tr><th>ИТОГО:</th><th class="TAC">' . $dg[$id] . '</th><th colspan=2></th></tr>';
		if ($repid == 'rep1' || $repid == 'rep2' || $repid == 'rep3' || $repid == 'rep4')
			$str .= '<tr><th><strong>ОБЩИЕ ИТОГИ:</strong></th><th class="TAC fontb">' . $total['cnt'] .'</th><th colspan=2></th></tr>';
		if ($repid == 'rep5')
			$str .= '<tr><th><strong>ОБЩИЕ ИТОГИ:</strong></th><th class="TAC fontb">' . $total['cnt'] .'</th><th class="TAC fontb">' . $total['Qty'] . '</th><th class="TAC fontb">' . $total['Sum'] . '</th></tr>';
		$str .= '</tbody>';
		$str .= "</table>";
		$response->table1 = $str;
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	public function get_report11_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();  
//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt1 = DateTime::createFromFormat('d?m?Y', $DT_start);
			$date1 = $dt1->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt2 = DateTime::createFromFormat('d?m?Y', $DT_stop);
			$date2 = $dt2->format('Ymd');
		} else {
			return;
		}
Fn::debugToLog('report11 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']).'	$date1='.  $date1.'	$date2='.  $date2);
//Fn::debugToLog("date1", $date1);
//Fn::debugToLog("date2", $date2);
		$action = 'conversion_' . $repid;
		$stmt = $this->db->prepare("CALL pr_reports(?, @id, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $date1, PDO::PARAM_STR);
		$stmt->bindParam(3, $date2, PDO::PARAM_STR);
		$stmt->bindParam(4, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$response = new stdClass();
		$response->error = '';
		$response->table1 = '';

		if (!Fn::checkErrorMySQLstmt($stmt))
			$response->error = $stmt->errorInfo();
		$rowset = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$dg = array(); $total = array(); $total['CountVisitor'] = 0; $total['CountCheck'] = 0;
		$groupkey = 'ClientID';
		if ($repid == 'rep3') $groupkey = 'DT';
		if ($repid == 'rep4') $groupkey = 'DT';
		if ($rowset) {
			//данные из таблицы переносим в массив
			foreach ($rowset as $row) {
				if ($dg[$row[$groupkey]]['CountVisitor'] == null) $dg[$row[$groupkey]]['CountVisitor'] = 0;
				$dg[$row[$groupkey]]['CountVisitor'] += $row['CountVisitor'];
				if ($dg[$row[$groupkey]]['CountCheck'] == null) $dg[$row[$groupkey]]['CountCheck'] = 0;
				$dg[$row[$groupkey]]['CountCheck'] += $row['CountCheck'];
				$total['CountVisitor'] += $row['CountVisitor'];
				$total['CountCheck'] += $row['CountCheck'];
			}
		}
//Fn::debugToLog("dg", json_encode($dg));

//выводим шапку таблицы
		$str = '';
		$str .= '<table id="table1" class="table table-striped table-bordered" cellspacing="0"  width="100%">';
		$str .= '<thead><tr>';
		if ($repid == 'rep1') {
			$str .= '<th>№</th>';
			$str .= '<th>Город</th>';
			$str .= '<th>Торговая точка</th>';
			$str .= '<th>Кол-во<br>посетителей</th>';
			$str .= '<th>Кол-во<br>чеков</th>';
			$str .= '<th>Конверсия<br>%</th>';
			$str .= '<th>Средняя<br>сумма чека</th>';
			$str .= '<th>Среднее<br>кол-во чеков</th>';
			$str .= '<th>Кол-во<br>раб. дней</th>';
		} elseif ($repid == 'rep2' || $repid == 'rep3') {
			$str .= '<th>№</th>';
			$str .= '<th>Город</th>';
			$str .= '<th>Торговая точка</th>';
			$str .= '<th>Дата</th>';
			$str .= '<th>Кол-во<br>посетителей</th>';
			$str .= '<th>Кол-во<br>чеков</th>';
			$str .= '<th>Конверсия<br>%</th>';
			$str .= '<th>Средняя<br>сумма чека</th>';
			$str .= '<th>Среднее<br>кол-во чеков</th>';
			$str .= '<th>Кол-во<br>раб. дней</th>';
		} else if ($repid == 'rep4') {
			$str .= '<th>Дата</th>';
			$str .= '<th>Кол-во<br>посетителей</th>';
			$str .= '<th>Кол-во<br>чеков</th>';
			$str .= '<th>Конверсия<br>%</th>';
			$str .= '<th>Средняя<br>сумма чека</th>';
			$str .= '<th>Среднее<br>кол-во чеков</th>';
		}
		$str .= '</tr></thead>';
		//выводим данные в таблице
		$id = 0;
		$str .= '<tbody>';
		foreach ($rowset as $row) {
			//выводим итоги группы
			if ($id != $row[$groupkey]) {
				$conversion = 0;
				if ($dg[$id] != null && $repid != 'rep1' && $repid != 'rep4') {
					if ($dg[$id]['CountCheck']>0)
						$conversion = round($dg[$id]['CountCheck'] / $dg[$id]['CountVisitor'] * 100,0);
					$str .= '<tr><th colspan=2></th><th colspan=2 class="TAC">ИТОГО:</th><th class="TAC">' . $dg[$id]['CountVisitor'] . '</th><th class="TAC">' . $dg[$id]['CountCheck'] . '</th><th class="TAC">' . $conversion . '</th><th colspan=3></th></tr>';
					$str .= '<tr><th colspan=10></th></tr>';
				}
				$id = $row[$groupkey];
			}
			$str .= '<tr>';
			if ($repid == 'rep1') {
				$str .= '<td>' . $row['ClientID'] . '</td>';
				$str .= '<td>' . $row['City'] . '</td>';
				$str .= '<td class="TAL">' . $row['NameShort'] . '</td>';
				$str .= '<td class="w100">' . $row['CountVisitor'] . '</td>';
				$str .= '<td class="w100">' . $row['CountCheck'] . '</td>';
				$str .= '<td class="w100">' . $row['Conversion'] . '</td>';
				$str .= '<td class="w100">' . $row['Avg_Sum'] . '</td>';
				$str .= '<td class="w100">' . $row['Avg_Count_Check'] . '</td>';
				$str .= '<td class="w100">' . $row['DayWork'] . '</td>';
				$str .= '</tr>';
			} elseif ($repid == 'rep2' || $repid == 'rep3') {
				$str .= '<td>' . $row['ClientID'] . '</td>';
				$str .= '<td>' . $row['City'] . '</td>';
				$str .= '<td class="TAL">' . $row['NameShort'] . '</td>';
				$str .= '<td class="w100">' . $row['DT'] . '</td>';
				$str .= '<td class="w100">' . $row['CountVisitor'] . '</td>';
				$str .= '<td class="w100">' . $row['CountCheck'] . '</td>';
				$str .= '<td class="w100">' . $row['Conversion'] . '</td>';
				$str .= '<td class="w100">' . $row['Avg_Sum'] . '</td>';
				$str .= '<td class="w100">' . $row['Avg_Count_Check'] . '</td>';
				$str .= '<td class="w100">' . $row['DayWork'] . '</td>';
				$str .= '</tr>';
			} elseif ($repid == 'rep4') {
				$str .= '<td class="w100">' . $row['DT'] . '</td>';
				$str .= '<td class="w100">' . $row['CountVisitor'] . '</td>';
				$str .= '<td class="w100">' . $row['CountCheck'] . '</td>';
				$str .= '<td class="w100">' . $row['Conversion'] . '</td>';
				$str .= '<td class="w100">' . $row['Avg_Sum'] . '</td>';
				$str .= '<td class="w100">' . $row['Avg_Count_Check'] . '</td>';
				$str .= '</tr>';
			}
		}
		//выводим итоги последней группы
		$conversion = 0;
		if ($dg[$id] != null && $repid != 'rep1' && $repid != 'rep4'){
			if ($dg[$id]['CountCheck'] > 0)
				$conversion = round($dg[$id]['CountCheck'] / $dg[$id]['CountVisitor'] * 100, 0);
			$str .= '<tr><th colspan=2></th><th colspan=2 class="TAC">ИТОГО:</th><th class="TAC">' . $dg[$id]['CountVisitor'] . '</th><th class="TAC">' . $dg[$id]['CountCheck'] . '</th><th class="TAC">' . $conversion . '</th><th colspan=3></th></tr>';
		}
		if ($repid == 'rep1'){
			if ($total['CountCheck'] > 0)
				$conversion = round($total['CountCheck'] / $total['CountVisitor'] * 100, 0);
			$str .= '<tr><th colspan=2></th><th colspan=1 class="TAC">ИТОГО:</th><th class="TAC">' . $total['CountVisitor'] . '</th><th class="TAC">' . $total['CountCheck'] . '</th><th class="TAC">' . $conversion . '</th><th colspan=3></th></tr>';
		}
		if ($repid == 'rep4'){
			if ($total['CountCheck'] > 0)
				$conversion = round($total['CountCheck'] / $total['CountVisitor'] * 100, 0);
			$str .= '<tr><th colspan=1 class="TAC">ИТОГО:</th><th class="TAC">' . $total['CountVisitor'] . '</th><th class="TAC">' . $total['CountCheck'] . '</th><th class="TAC">' . $conversion . '</th><th colspan=2></th></tr>';
		}
//		if ($repid == 'rep1' || $repid == 'rep2' || $repid == 'rep3' || $repid == 'rep4')
//			$str .= '<tr><th><strong>ОБЩИЕ ИТОГИ:</strong></th><th class="TAC fontb">' . $total['cnt'] . '</th><th colspan=2></th></tr>';
//		if ($repid == 'rep5')
//			$str .= '<tr><th><strong>ОБЩИЕ ИТОГИ:</strong></th><th class="TAC fontb">' . $total['cnt'] . '</th><th class="TAC fontb">' . $total['Qty'] . '</th><th class="TAC fontb">' . $total['Sum'] . '</th></tr>';
		$str .= '</tbody>';
		$str .= "</table>";
		$response->table1 = $str;
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	public function get_report12_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		Fn::debugToLog('report12 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::paramToLog();  
//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt1 = DateTime::createFromFormat('d?m?Y', $DT_start);
			$date1 = $dt1->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt2 = DateTime::createFromFormat('d?m?Y', $DT_stop);
			$date2 = $dt2->format('Ymd');
		} else {
			return;
		}
//Fn::debugToLog("date1", $date1);
//Fn::debugToLog("date2", $date2);
		$action = 'report' . $sid;
		$stmt = $this->db->prepare("CALL pr_reports(?, @id, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $date1, PDO::PARAM_STR);
		$stmt->bindParam(3, $date2, PDO::PARAM_STR);
		$stmt->bindParam(4, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$response = new stdClass();
		$response->error = '';
		$response->table1 = '';

		if (!Fn::checkErrorMySQLstmt($stmt))
			$response->error = $stmt->errorInfo();
		$rowset = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$dg = array(); $total = array(); $total['cntN'] = 0; $total['cntK'] = 0; $total['cnt'] = 0; $total['qtyN'] = 0; $total['qtyK'] = 0; 
		$groupkey = 'ClientID';
//		if ($repid == 'rep3') $groupkey = 'DT';
//		if ($repid == 'rep4') $groupkey = 'DT';
		if ($rowset) {
			//данные из таблицы переносим в массив
			foreach ($rowset as $row) {
//				if ($dg[$row[$groupkey]]['cntN'] == null)	$dg[$row[$groupkey]]['cntN'] = 0;
//				$dg[$row[$groupkey]]['cntN'] += $row['cntN'];
//				if ($dg[$row[$groupkey]]['cntK'] == null)	$dg[$row[$groupkey]]['cntK'] = 0;
//				$dg[$row[$groupkey]]['cntK'] += $row['cntK'];
				$total['cntN']	+= $row['cntN'];
				$total['cntK']	+= $row['cntK'];
				$total['cnt']	+= $row['cnt'];
				$total['qtyN']	+= $row['qtyN'];
				$total['qtyK']	+= $row['qtyK'];
			}
		}
//Fn::debugToLog("dg", json_encode($dg));
//выводим шапку таблицы
		$str = '';
		$str .= '<table id="table1" class="table table-striped table-bordered" cellspacing="0"  width="100%">';
		$str .= '<thead><tr>';
		if ($repid == 'rep1') {
			$str .= '<th>№</th>';
			$str .= '<th>Город</th>';
			$str .= '<th>Торговая точка</th>';
			$str .= '<th>Кол-во<br>чеков с<br>наполнителем</th>';
			$str .= '<th>Кол-во<br>чеков с<br>наполнителем<br>и кормом</th>';
			$str .= '<th>Кол-во<br>чеков только с<br>наполнителем</th>';
//			$str .= '<th>Кол-во<br>ед. с<br>наполнителем</th>';
//			$str .= '<th>Кол-во<br>ед. с<br>наполнителем<br>и кормами</th>';
		}
		$str .= '</tr></thead>';
		//выводим данные в таблице
		$id = 0;
		$str .= '<tbody>';
		foreach ($rowset as $row) {
			//выводим итоги группы
//			if ($id != $row[$groupkey]) {
//				$conversion = 0;
//				if ($dg[$id] != null && $repid != 'rep1' && $repid != 'rep4') {
//					if ($dg[$id]['CountCheck'] > 0)
//						$conversion = round($dg[$id]['CountCheck'] / $dg[$id]['CountVisitor'] * 100, 0);
//					$str .= '<tr><th colspan=2></th><th colspan=2 class="TAC">ИТОГО:</th><th class="TAC">' . $dg[$id]['CountVisitor'] . '</th><th class="TAC">' . $dg[$id]['CountCheck'] . '</th><th class="TAC">' . $conversion . '</th><th colspan=3></th></tr>';
//					$str .= '<tr><th colspan=10></th></tr>';
//				}
//				$id = $row[$groupkey];
//			}
			$str .= '<tr>';
			if ($repid == 'rep1') {
				$str .= '<td>' . $row['ClientID'] . '</td>';
				$str .= '<td>' . $row['City'] . '</td>';
				$str .= '<td class="TAL">' . $row['NameShort'] . '</td>';
				$str .= '<td class="w100">' . Fn::nfx0($row['cntN'],0) . '</td>';
				$str .= '<td class="w100">' . Fn::nfx0($row['cntK'],0) . '</td>';
				$str .= '<td class="w100">' . Fn::nfx0($row['cnt'],0) . '</td>';
//				$str .= '<td class="w100">' . Fn::nfx0($row['qtyN'],0) . '</td>';
//				$str .= '<td class="w100">' . Fn::nfx0($row['qtyK'],0) . '</td>';
				$str .= '</tr>';
			}
		}
		//выводим итоги последней группы
		$conversion = 0;
		if ($repid == 'rep1') {
			$str .= '<tr><th colspan=2></th><th colspan=1 class="TAC">ИТОГО:</th><th class="TAC">' . $total['cntN'] . '</th><th class="TAC">' . $total['cntK'] . '</th><th class="TAC">' . $total['cnt'] . '</th>';
			//$str .= '<th class="TAC">' . $total['qtyN'] . '</th><th class="TAC">' . $total['qtyK'] . '</th>';
			$str .= '</tr>';
		}
		$str .= '</tbody>';
		$str .= "</table>";
		$response->table1 = $str;
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	public function get_report14_data() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		Fn::debugToLog('report14 user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
//Fn::paramToLog();  
//echo $DT_start.' '.  $DT_stop . '<br>';
		if (isset($DT_start)) {
			$dt1 = DateTime::createFromFormat('d?m?Y', $DT_start);
			$date1 = $dt1->format('Y-m-d');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt2 = DateTime::createFromFormat('d?m?Y', $DT_stop);
			$date2 = $dt2->format('Y-m-d');
		} else {
			return;
		}
//Fn::debugToLog("date1", $date1);
//Fn::debugToLog("date2", $date2);
		//$param = "DT_start=".$date1."&DT_stop=".$date2."&".urldecode($_SERVER['QUERY_STRING']);
		$param = urldecode($_SERVER['QUERY_STRING']);
//Fn::debugToLog("param", $param);
		$stmt = $this->db->prepare("CALL pr_doc_2017(?, @id, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $param, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();

		$response = new stdClass();
		$response->error = '';
		$response->table1 = '';
		if (!Fn::checkErrorMySQLstmt($stmt))
			$response->error = $stmt->errorInfo();
		$min_week = 99;
		$max_week = 0;
		$min_dt = null;
		$cur_dt = new DateTime();
		$t = 0;
		$cal = array();
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_ASSOC);
//Fn::debugToLog("rowcount", $stmt->rowCount());
//Fn::debugToLog("rowset", json_encode($rowset));
			if ($stmt->rowCount() > 0){
				if ($t==0){
					if ($rowset) {
						foreach ($rowset as $row) {
							$min_dt = DateTime::createFromFormat('Y?m?d', $row['DT_start']);
						}
					}
					$t++;
					continue;
				}
				if ($rowset) {
					//данные из таблицы переносим в массив
					foreach ($rowset as $row) {
//Fn::debugToLog("row", json_encode($row));
						$cal[$row['WeekDay']][$row['Week']][$row['TaskID']] = $row;
						if ($min_week > $row['Week']) $min_week = $row['Week'];
						if ($max_week < $row['Week']) $max_week = $row['Week'];
						//Fn::debugToLog("info", json_encode($cal[$row['WeekDay']][$row['Week']][$row['TaskID']]['Info']));
					}
				}
			}
		} while ($stmt->nextRowset());
		
//выводим шапку таблицы
		$str = '';
		$str .= '<table id="table1" class="table table-striped table-bordered" cellspacing="0"  width="100%">';
		$str .= '<thead><tr>';
		$str .= '	<th class="minw150">ПН</th>';
		$str .= '	<th class="minw150">ВТ</th>';
		$str .= '	<th class="minw150">СР</th>';
		$str .= '	<th class="minw150">ЧТ</th>';
		$str .= '	<th class="minw150">ПТ</th>';
		$str .= '	<th class="minw150">СБ</th>';
		$str .= '	<th class="minw150">ВС</th>';
		$str .= '</tr></thead>';
		//выводим данные в таблице
		$id = 0;
		$str .= '<tbody>';
//Fn::debugToLog("cal", json_encode($cal));
		for ($iw = $min_week; $iw <= $max_week; $iw++) {
			$str .= '<tr>';
			for ($id = 0; $id < 7; $id++) {
				//$str .= '<td>Date:<br>';
//Fn::debugToLog("cal dt", $cal[$id][$iw]['DT'].' dt:'. $min_dt->modify('+'.$days.' day')->format('Y-m-d').'	days:'.$days);
				$tasks = $cal[$id][$iw];
				$str .= '<td>';
				//таблица за 1 день
				//$str .= '<table class="table" cellspacing="0" width="100%">';
				$str .= '<table class="table table-hover" cellspacing="0" width="100%">';
				$str .= '<thead><tr>';
				$str .= '	<th colspan=5 class="TAC minw150">' . $min_dt->format('Y-m-d') . '</th>';
				$str .= '</tr></thead>';
				$str .= '<tbody>';
				$min_dt->modify('+1 day');
				if ($tasks != null){
					foreach ($tasks as $task){
//Fn::debugToLog("task", json_encode($task));
						if(!is_array($task)) continue;
						$tr_class = 'class="row_data"';
						if($min_dt < $cur_dt) $tr_class = 'class="row_data bg-danger"';
						if($task['Status']==10) $tr_class = 'class="row_data bg-success"';
//Fn::debugToLog("dt", ''.$min_dt->format('Y-m-d') .' '. $cur_dt->format('Y-m-d').'	tr_class='.  $tr_class);
//Fn::debugToLog("task", json_encode($task));
						$notes = str_replace('\n', '<br>', $task['Notes']);
						$notes = str_replace('"', '&#34;', $notes);
						$sourceInfo = str_replace('\n', '<br>', $task['SourceInfo']);
						$sourceInfo = str_replace('"', '&#34;', $sourceInfo);
						$typepayment = $task['TypePayment']==0?'нал':'БН';
						$str .= '<tr ' . $tr_class . ' data-placement="auto" '
								. 'data-content="'
								. "<table class='table'><thead>"
								. "<tr><th class='w150'>Дата события:</th><th class='TAC'>".$task['DT_event'].'</th></tr>'
								. "<tr><th>Партнер:</th><th class='TAC'>".$task['Partner'].'</th></tr>'
								. "<tr><th>Плательщик:</th><th class='TAC'>".$task['Payer'].'</th></tr>'
								. "<tr><th>Сумма события: </th><th class='TAC'>". $task['SumEvent'].' '.  $task['CurrencyEvent'].'</th></tr>'
								. "<tr><th>Срок оплаты: </th><th class='TAC'>".  $task['DT_Payment']  . '</th></tr>'
								. "<tr><th>Дней отсрочки:</th><th class='TAC'>".  $task['DelayDays'].'</th></tr>'
								. "<tr><th>Сумма плановая: </th><th class='TAC'>".  $task['SumTask'].' '.  $task['Currency'].' ('.$typepayment.')</th></tr>'
								. "<tr><th colspan=2><strong>Иточник инф.: </th></tr>"
								. "<tr><th class='w50'></th><th class='w400'>". $sourceInfo.'</th></tr>'
								. "<tr><th colspan=2><strong>Описание платежа: </th></tr>"
								. "<tr><th class='w50'></th><th class='w400' colspan=2>". $notes .'</th></tr>'
								. '</thead></table>'
								. '">'
								//. '<td class="TAR">' . $task['TaskID'] . '</td>'
								. '<td class="w20 TAR">' . $task['SumTask'] . '</td>'
								. '<td class="w20 TAC">' . $task['Currency'] . '</td>'
								. '<td class="w50 TAL">' . $task['Partner'] . '</td>'
								. '<td class="w50 TAL">' . $task['Payer'] . '</td>'
								. '<td class="w20 TAL">' . $typepayment . '</td>';
						//$str .= $task['TaskID'].' - '.$task['SumTask'].' '.$task['Currency'].' - '.$task['Partner'].'<br>';
						$str .= '</tr>';
					}
				}
				$str .= '</tbody>';
				$str .= "</table>";
				$str .= '</td>';
			}
			$str .= '</tr>';
		}
		$str .= '</tbody>';
		$str .= "</table>";

		$response->table1 = $str;
//Fn::debugToLog("response", json_encode($response));
		//$response->table1 = json_encode($cal);
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}

	public function get_pendel_data2() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
Fn::debugToLog('pendel user:' . $_SESSION['UserName'], urldecode($_SERVER['QUERY_STRING']));
		if (isset($DT_start)) {
			$dt1 = DateTime::createFromFormat('d?m?Y', $DT_start);
			//echo $dt->format('Ymd').'<br>';
			$dateStart = $dt1->format('Ymd');
		} else {
			return;
		}
		if (isset($DT_stop)) {
			$dt2 = DateTime::createFromFormat('d?m?Y', $DT_stop);
			//echo $dt->format('Ymd');
			$dateStop = $dt2->format('Ymd');
		} else {
			return;
		}
//Fn::paramToLog();
		$response = new stdClass();
		$response->error = '';
		$response->table1 = '';
		if($interval=='%Y-%v')	{$increment = '+1 week'; $formatdt = 'Y-W';}
		if($interval=='%Y-%m')	{$increment = '+1 month'; $formatdt = 'Y-m';}
		if($interval=='%Y')		{$increment = '+1 year';  $formatdt = 'Y';}
		$str = '';
		$part = 4;
		$cnt_tbl = 0;
		if($part == 4){
//Fn::debugToLog('pendel', $dateStart . '	' . $dateStop.'	'.$part  . '	' . $interval.'	'.  $increment);
//запрос валовый доход
			$stmt = $this->db->prepare("CALL pr_reports_pendel(?, @id, ?, ?, ?)");
			$stmt->bindParam(1, $part, PDO::PARAM_STR);
			$stmt->bindParam(2, $dateStart, PDO::PARAM_STR);
			$stmt->bindParam(3, $dateStop, PDO::PARAM_STR);
			$stmt->bindParam(4, $interval, PDO::PARAM_STR);
			// вызов хранимой процедуры
			$stmt->execute();
			if (!Fn::checkErrorMySQLstmt($stmt))
				$response->error = $stmt->errorInfo();
			$gross = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($gross) {
				$dataGross = array();
				$columnCount = $stmt->columnCount();
				$rowCount = $stmt->rowCount();
				$dg_max_id_row = $gross[$rowCount - 1][0];
				$cnt_tbl = $dg_max_id_row - 1; //так как нет затрат по экспорту
//Fn::debugToLog('$dg_max_id_row', $dg_max_id_row.'	$cnt_tbl:'.$cnt_tbl);
				if ($dg_max_id_row != 0){
//конвертируем полученный rowset в удобный нам формат в array
					for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
						$period = $dt->format($formatdt);
						$dataGross[$dg_max_id_row][$period]['Sebest'] = 0;
						$dataGross[$dg_max_id_row][$period]['Oborot'] = 0;
						$dataGross[$dg_max_id_row][$period]['Dohod'] = 0;
					}
					for ($tr = 0; $tr < $dg_max_id_row; $tr++) {
						for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
							$period = $dt->format($formatdt);
							for ($row = 1; $row < $rowCount; $row++) {
								if ($gross[$row][0] != $tr + 1 || $period != $gross[$row]['Period']) continue;
								$dataGross[$tr]['Field1']['Name'] = $gross[$row]['Stream'];
								$dataGross[$tr][$period]['Period'] = $gross[$row]['Period'];
								$dataGross[$tr][$period]['CatID'] = $gross[$row]['CatID'];
								$dataGross[$tr][$period]['Sebest'] = $gross[$row]['Sebest'];
								$dataGross[$tr][$period]['Oborot'] = $gross[$row]['Oborot'];
								$dataGross[$tr][$period]['Percent'] = $gross[$row]['Percent'];
								$dataGross[$tr][$period]['Dohod'] = $gross[$row]['Dohod'];
								$dataGross[$dg_max_id_row]['Field1']['Name'] = 'Итого:';
								$dataGross[$dg_max_id_row][$period]['Sebest'] += $gross[$row]['Sebest'];
								$dataGross[$dg_max_id_row][$period]['Oborot'] += $gross[$row]['Oborot'];
								$dataGross[$dg_max_id_row][$period]['Dohod'] += $gross[$row]['Dohod'];
							}
						}
					}
				}
			}		
		}

//Fn::objectToLog($dataGross);

//запрос по затратам
//Fn::debugToLog('pendel', $dateStart . '	' . $dateStop.'	'.$action.'	'.  $interval.'	'.  $increment);
//Fn::debugToLog('pendel', 'dt1='.$dt1->format($formatdt) . '	dt2=' . $dt2->format($formatdt));
		$spent = array();
		$stmt = $this->db->prepare("CALL pr_reports_pendel(?, @id, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $dateStart, PDO::PARAM_STR);
		$stmt->bindParam(3, $dateStop, PDO::PARAM_STR);
		$stmt->bindParam(4, $interval, PDO::PARAM_STR);
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			$response->error = $stmt->errorInfo();
		$t = 0;
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				$spent[$t] = array(); 
				$columnCount = $stmt->columnCount();
				$rowCount = $stmt->rowCount();
				$spent[$t]['name'] = $rowset[0]['Stream'];
				$spent[$t]['max_id_row'] = $rowset[$rowCount - 1][0];
				$max_id_row = $spent[$t]['max_id_row'];
//Fn::debugToLog("spent t=", $t."	".$columnCount."	".$rowCount  . "	" . $rowset[0]['Stream']."		".  $rowset[$rowCount - 1][0]);
				//if ($max_id_row == 0) continue;
//конвертируем полученный rowset в удобный нам формат в array
				for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
					$period = $dt->format($formatdt);
//Fn::debugToLog("", $t." ".$max_id_row." ".$period);
					$spent[$t][$max_id_row][$period]['SumSpent'] = 0;
				}
				for ($tr = 0; $tr < $max_id_row; $tr++) {
					for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
						$period = $dt->format($formatdt);
						for ($row = 1; $row < $rowCount; $row++) {
							if ($rowset[$row][0] != $tr + 1 || $period != $rowset[$row]['Period']) continue;
							$spent[$t][$tr]['Field1']['Name'] = $rowset[$row]['Stream'];
							$spent[$t][$tr][$period]['Period'] = $rowset[$row]['Period'];
							$spent[$t][$tr][$period]['CatID'] = $rowset[$row]['CatID'];
							$spent[$t][$tr][$period]['SumSpent'] = $rowset[$row]['SumSpent'];
							$spent[$t][$max_id_row]['Field1']['Name'] = 'Итого:';
							$spent[$t][$max_id_row][$period]['SumSpent'] += $rowset[$row]['SumSpent'];
						}
					}
				}
//Fn::debugToLog("spent".$t, json_encode($spent[$t]));
			} $t++;
		} while ($stmt->nextRowset());
//запишем суммы затрат в таблицу валовый доход		
		$count_t = $t;
//		$count_spent = ($count_t < 5 ? $count_t : 5);
		$count_spent = ($count_t < $cnt_tbl ? $count_t : $cnt_tbl);
		for ($t = 0; $t < $count_spent; $t++) {
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$period = $dt->format($formatdt);
//				Fn::debugToLog("итоги", $t.'   '.$period);
//				Fn::debugToLog("итоги", Fn::nfPendel($spent[$t][$spent[$t]['max_id_row']][$period]['SumSpent']));
				$dataGross[$t][$period]['SumSpent'] = $spent[$t][$spent[$t]['max_id_row']][$period]['SumSpent'];
			}
		}
//Fn::objectToLog($spent);
//Fn::objectToLog($dataGross);
		$str .= '<table id="table1" class="table table-striped table-bordered" cellspacing="0"  width="100%">';
//формируем шапку Валовый доход
			$str .= '<thead><tr>
				<th rowspan=2>№ п-п</th>
				<th rowspan=2 style="border-right-width:5px;">Валовый доход</th>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$str .= '<th colspan=4 style="border-right-width:5px;">' . $dt->format($formatdt) . '</th>';
			}
			$str .= '</tr><tr>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$str .= '<th>Оборот</th>
				<th>Себест.</th>
				<th>Маржа</th>
				<th style="border-right-width:5px;">Прибыль</th>
			   ';
			}
			$str .= '</tr></thead>';
//формируем таблицу
			$str .= '<tbody>';
			for ($tr = 0; $tr < $dg_max_id_row; $tr++) {
				$str .= '<tr>';
				$str .= '<td>' . ($tr + 1) . '</td>';
				$str .= '<td class="TAL" style="border-right-width:5px;">' . $dataGross[$tr]['Field1']['Name'] . '</td>';
				for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
					$period = $dt->format($formatdt);
					$str .= '<td class="TAR"><a href="javascript:history(\'../reports_fin/pendel_dop?'
							. 'catid=' . $dataGross[$tr][$period]['CatID']
							. '&partner_period=' . $dataGross[$tr][$period]['Period']
							. '&interval='. $interval
							. '\');">'
							. Fn::nfPendel($dataGross[$tr][$period]['Oborot'])
							. '</a></td>';
					$str .= '<td class="TAR"><a href="javascript:history(\'../reports_fin/pendel_dop?'
							. 'catid=' . $dataGross[$tr][$period]['CatID']
							. '&partner_period=' . $dataGross[$tr][$period]['Period']
							. '&interval=' . $interval
							. '\');">'
							. Fn::nfPendel($dataGross[$tr][$period]['Sebest'])
							. '</a></td>';
					$str .= '<td class="TAR">'.Fn::nfPendelP($dataGross[$tr][$period]['Percent']).'</td>';
					$str .= '<td class="TAR" style="border-right-width:5px;"><a href="javascript:history(\'../reports_fin/pendel_dop?'
							. 'catid=' . $dataGross[$tr][$period]['CatID']
							. '&partner_period=' . $dataGross[$tr][$period]['Period']
							. '&interval=' . $interval
							. '\');">'
							. Fn::nfPendel($dataGross[$tr][$period]['Dohod'])
							. '</a></td>';
				}
				$str .= '</tr>';
			}
			$str .= "</tbody>";
//формируем итоги
			$str .= '<thead>';
			$str .= '<tr>';
			$str .= '<th></th>';
			$str .= '<th class="TAL" style="border-right-width:5px;">' . $dataGross[$tr]['Field1']['Name'] . '</th>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$period = $dt->format($formatdt);
				$percent = 0;
				if($dataGross[$tr][$period]['Oborot']<>0){
					$percent = round(100 * (1-$dataGross[$tr][$period]['Sebest']/$dataGross[$tr][$period]['Oborot']),2);
				}
				$str .= '<th class="TAR">' . Fn::nfPendel($dataGross[$tr][$period]['Oborot']) . '</th>';
				$str .= '<th class="TAR">' . Fn::nfPendel($dataGross[$tr][$period]['Sebest']) . '</th>';
				$str .= '<th class="TAR">' . Fn::nfPendelP($percent).'</th>';
				$str .= '<th class="TAR" style="border-right-width:5px;">' . Fn::nfPendel($dataGross[$tr][$period]['Dohod']) . '</th>';
			}
			$str .= '</tr>';
			$str .= "</thead>";
//отступ
		$str .= '<thead><tr><th colspan=1000 class="h10"></th></tr></thead>';
//формируем шапку Валовая прибыль
		$str .= '<thead><tr>
				<th rowspan=2>№ п-п</th>
				<th rowspan=2 style="border-right-width:5px;">Валовая прибыль</th>';
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			$str .= '<th colspan=4 style="border-right-width:5px;">' . $dt->format($formatdt) . '</th>';
			$total[$period]['Dohod'] = 0;
			$total[$period]['SumSpent'] = 0;
			$total[$period]['Prib'] = 0;
		}
		$str .= '</tr><tr>';
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			$str .= '<th>Вал.прибыль</th>
					<th>Затраты</th>
					<th>% затрат</th>
					<th style="border-right-width:5px;">Прибыль</th>
				   ';
		}
		$str .= '</tr></thead>';
//формируем таблицу Валовая прибыль
		$total = array();
		$str .= '<tbody>';
		for ($tr = 0; $tr < $dg_max_id_row; $tr++) {
			$str .= '<tr>';
			$str .= '<td>' . ($tr + 1) . '</td>';
			$str .= '<td class="TAL" style="border-right-width:5px;">' . $dataGross[$tr]['Field1']['Name'] . '</td>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$period = $dt->format($formatdt);
				$dohod = $dataGross[$tr][$period]['Dohod'];
				$sumspent = $dataGross[$tr][$period]['SumSpent'];
				$total[$period]['Dohod'] += $dohod;
				$total[$period]['SumSpent'] += $sumspent;
				$total[$period]['Prib'] += $dohod;
				$total[$period]['Prib'] -= $sumspent;
				$percent = 0;
				if($dohod<>0) $percent = round(100 * $sumspent / $dohod,2);
				$str .= '<td class="TAR">'.Fn::nfPendel($dohod).'</a></td>';
				$str .= '<td class="TAR">'.Fn::nfPendel($sumspent).'</a></td>';
				$str .= '<td class="TAR">'.Fn::nfPendelP($percent).'</a></td>';
				$str .= '<td class="TAR" style="border-right-width:5px;">'.Fn::nfPendel($dohod - $sumspent).'</a></td>';
			}
			$str .= '</tr>';
		}
		$str .= "</tbody>";
//формируем итоги Валовая прибыль
		$str .= '<thead>';
		$str .= '<tr>';
		$str .= '<th></th>';
		$str .= '<th class="TAL" style="border-right-width:5px;">' . $dataGross[$tr]['Field1']['Name'] . '</th>';
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			$period = $dt->format($formatdt);
			$percent = 0;
			if ($total[$period]['Dohod'] <> 0) $percent = round(100 * $total[$period]['SumSpent'] / $total[$period]['Dohod'], 2);
			$str .= '<th class="TAR">' . Fn::nfPendel($total[$period]['Dohod']) . '</th>';
			$str .= '<th class="TAR">' . Fn::nfPendel($total[$period]['SumSpent']) . '</th>';
			$str .= '<th class="TAR">' . Fn::nfPendelP($percent) . '</th>';
			$str .= '<th class="TAR" style="border-right-width:5px;">' . Fn::nfPendel($total[$period]['Prib']) . '</th>';
		}
		$str .= '</tr>';
		$str .= "</thead>";
//отступ
		$str2 = '<thead><tr><th colspan=1000 class="h10"></th></tr></thead>';
//формируем шапку Затраты
		$str2 .= '<thead><tr>
				<th rowspan=1>№ п-п</th>
				<th rowspan=1 style="border-right-width:5px;">Затраты</th>';
//		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
//			$str .= '<th colspan=4>' . $dt->format($formatdt) . '</th>';
//		}
//		$str .= '</tr><tr>';
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			$str2 .= '<th colspan=4 style="border-right-width:5px;">Сумма затрат '.  $dt->format($formatdt) .'</th>';
		}
		$str2 .= '</tr></thead>';

//отступ
		$str2 .= '<thead><tr><th colspan=1000 class="h10"></th></tr></thead>';
//		$str2 .= '</table><table id="example" class="table table-striped table-bordered" cellspacing="0"  width="100%">';
//формируем шапку ЗАТРАТЫ Переменные расходы
			$str2 .= '<thead><tr><th></th>
					<th style="border-right-width:5px;">Переменные расходы</th>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$period = $dt->format($formatdt);
				$total[$period]['SumSpent'] = 0;
				$str2 .= '<th colspan=4 style="border-right-width:5px;"></th>';
			}
			$str2 .= '</tr>';
			$str2 .= '</thead>';
		
		$str3 = '<thead><tr class="bc12"><th></th><th style="border-right-width:5px;">EBITDA</th>';
		for($t = 0; $t < $count_t; $t++){
			$max_id_row = $spent[$t]['max_id_row'];
//отступ
//			if(!strpos($spent[$t]['name'],'EBITDA')){
			if ($t <= 9){
				$str2 .= '<thead><tr><th colspan=1000 class="h10"></th></tr></thead>';
			}
//формируем шапку для таблиц затрат (их может быть много)
			$str2 .= '<thead><tr><th></th>
					<th style="border-right-width:5px;">'.$spent[$t]['name'].'</th>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$period = $dt->format($formatdt);
				if(!strpos($spent[$t]['name'],'EBITDA')){
					$str2 .= '<th colspan=4 style="border-right-width:5px;"></th>';
				}else{
//					Fn::debugToLog("$period", $total[$period]['Prib'].'	'.$total[$period]['SumSpent']);
					$str2 .= '<th colspan=4 style="border-right-width:5px;">'.Fn::nfPendel($total[$period]['Prib'] - $total[$period]['SumSpent']).'</th>';
					$str3 .= '<th colspan=4 style="border-right-width:5px;">'.Fn::nfPendel($total[$period]['Prib'] - $total[$period]['SumSpent']).'</th>';
				}
			}
			if(!strpos($spent[$t]['name'],'EBITDA')){}else{
				$str3 .= '</tr>';
				$str3 .= '</thead>';
			}
			$str2 .= '</tr>';
			$str2 .= '</thead>';
//формируем таблицу 3
			$str2 .= '<tbody>';
			for ($tr = 0; $tr < $max_id_row; $tr++) {
				$str2 .= '<tr>';
				$str2 .= '<td>' . ($tr + 1) . '</td>';
				$str2 .= '<td class="TAL" style="border-right-width:5px;">' . $spent[$t][$tr]['Field1']['Name'] . '</td>';
				for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
					$period = $dt->format($formatdt);
					$str2 .= '<td colspan=4 style="border-right-width:5px;">'
							. '<a href="javascript:history(\'../reports_fin/pendel_dop?'
							. 'catid=' . $spent[$t][$tr][$period]['CatID']
							. '&spent_period=' . $spent[$t][$tr][$period]['Period']
							. '&interval=' . $interval
							. '\');">'
							. Fn::nfPendel($spent[$t][$tr][$period]['SumSpent'])
							. '</a>'
							. '</td>';
				}
				$str2 .= '</tr>';
			}
			$str2 .= "</tbody>";
//формируем итоги 3
			$str2 .= '<thead>';
			$str2 .= '<tr>';
			$str2 .= '<th></th>';
			$str2 .= '<th class="TAL" style="border-right-width:5px;">' . $spent[$t][$tr]['Field1']['Name'] . '</th>';
			for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
				$period = $dt->format($formatdt);
				if($t >= $cnt_tbl && $t<>10){ 
					//Fn::debugToLog("вошло:	$t	$tr	$period	SumSpent=", $spent[$t][$tr][$period]['SumSpent']);
					$total[$period]['SumSpent'] += $spent[$t][$tr][$period]['SumSpent'];
				}
				$str2 .= '<th colspan=4 style="border-right-width:5px;">' . Fn::nfPendel($spent[$t][$tr][$period]['SumSpent']) . '</th>';
			}
			$str2 .= '</tr>';
			$str2 .= "</thead>";
		}
//отступ
//		if (!strpos($spent[$t]['name'], 'EBITDA'))
//			$str2 .= '<thead><tr><th colspan=1000 class="h10"></th></tr></thead>';
//формируем конечный итог
		$str41 .= '<thead><tr>';
		$str42 .= '<thead><tr class="bc13">';
		$str5 .= '<th></th>';
		$str5 .= '<th class="TAL" style="border-right-width:5px;">ИТОГО ЧИСТАЯ ПРИБЫЛЬ:</th>';
		for ($dt = clone $dt1; $dt <= $dt2; $dt->modify($increment)) {
			$period = $dt->format($formatdt);
			//$total[$period]['SumSpent'] -= $spent[$t][$tr][$period]['SumSpent'];
			$str5 .= '<th colspan=4 style="border-right-width:5px;">' . Fn::nfPendel($total[$period]['Prib']-$total[$period]['SumSpent']) . '</th>';
		}
		$str5 .= '</tr>';
		$str5 .= "</thead>";
		$str .= $str3.$str42.$str5.$str2.$str41.$str5;
		$str .= "</table>";
		$response->table1 = $str;
//Fn::debugToLog("", $str3);
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}

//report setting
	public function set_report_setting() {
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_report_setting('set', @id, ?, ?, ?, ?)");
		$stmt->bindParam(1, $_SESSION['UserID'], PDO::PARAM_STR);
		$stmt->bindParam(2, $sid, PDO::PARAM_STR);
		$stmt->bindParam(3, $sname, PDO::PARAM_STR);
		$stmt->bindParam(4, urldecode($_SERVER['QUERY_STRING']), PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$result = false;
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				foreach ($rowset as $row) {
					$result = $row[0];
				}
			}
		} while ($stmt->nextRowset());
		echo $result;
	}
	public function get_report_setting_list() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_report_setting('get list', @id, ?, ?, ?, ?)");
		$stmt->bindParam(1, $_SESSION['UserID'], PDO::PARAM_STR);
		$stmt->bindParam(2, $sid, PDO::PARAM_STR);
		$stmt->bindParam(3, $sname, PDO::PARAM_STR);
		$stmt->bindParam(4, $url, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$result = false;
		//$response = new stdClass();
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				$i = 0;
				foreach ($rowset as $row) {
					$response[$i] = array('id' => $row['SettingName'], 
										  'text' => $row['SettingName']
					);
					$i++;
				}
			}
		} while ($stmt->nextRowset());
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
		return;
	}
	public function get_report_setting_byName() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		if (isset($_SESSION['report9_setting']) && $sid==9){
			$response = new stdClass();
			$response->Setting = $_SESSION['report9_setting'];
			header("Content-type: application/json;charset=utf-8");
			echo json_encode($response);
			//unset($_SESSION['report9_setting']);
			return;
		}
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
//		$url = urldecode($_SERVER['QUERY_STRING']);
		$stmt = $this->db->prepare("CALL pr_report_setting('get by name', @id, ?, ?, ?, ?)");
		$stmt->bindParam(1, $_SESSION['UserID'], PDO::PARAM_STR);
		$stmt->bindParam(2, $sid, PDO::PARAM_STR);
		$stmt->bindParam(3, $sname, PDO::PARAM_STR);
		$stmt->bindParam(4, $url, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$result = false;
		$response = new stdClass();
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				foreach ($rowset as $row) {
					//Fn::debugToLog("get by name", explode("&", $row['Setting']));
					$response->Setting = $row['Setting'];
					$response->UserID = $_SESSION['UserID'];
				}
			}
		} while ($stmt->nextRowset());
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}
//setting
	public function config() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$response = new stdClass();
		$response->success = false;
		$response->message = "Нет данных для отображения!";
		$stmt = $this->db->prepare("call pr_setting(:action, @_id, :_UserID, :_Section, :_Object, :_Param, :_Value)");
		$stmt->bindParam(":action", $action);
		$stmt->bindParam(":_UserID", $_SESSION['UserID']);
		$stmt->bindParam(":_Section", $section);
		$stmt->bindParam(":_Object", $object);
		$stmt->bindParam(":_Param", $param);
		$stmt->bindParam(":_Value", $value);
		// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) {
			$ar = $stmt->errorInfo();
			$response->success = false;
			$response->message = "Ошибка!";
		} else {
			do {
				$rowset = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($rowset) {
					$response->success = $rowset[0]['State'];
					$response->message = $rowset[0]['Message'];
//					$message2 = iconv('utf-8', 'cp1251', $rowset[0]['Message']);
					$response->setting = $rowset;
				}
				break;
			} while ($stmt->nextRowset());
		}
//Fn::debugToLog("resp", json_encode($response));
//Fn::debugToLog("rowset", json_encode($rowset));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}

//project
	public function project_info() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_project('info', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $projectid, PDO::PARAM_STR);
		$stmt->bindParam(2, $departmentID, PDO::PARAM_STR);
		$stmt->bindParam(3, $name, PDO::PARAM_STR);
		$stmt->bindParam(4, $description, PDO::PARAM_STR);
		$stmt->bindParam(5, $status, PDO::PARAM_STR);
		$stmt->bindParam(6, $userID_resp, PDO::PARAM_STR);
		$stmt->bindParam(7, $userID_create, PDO::PARAM_STR);
		$stmt->bindParam(8, $DT_plan, PDO::PARAM_STR);
		$stmt->bindParam(9, $DT_fact, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			break; //берем первую запись из результата
		}
		return $row;
	}
	public function project_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		if ($projectid == '')	$projectid = null;
		if ($unitID == '')		$unitID = null;
		if ($status == '')		$status = 0;
		if ($userID_resp == '')	$userID_resp = null;
		if(isset($DT_plan) && $DT_plan!=''){
			$DT_plan = DateTime::createFromFormat('d?m?Y', $DT_plan);
			$DT_plan = $DT_plan->format('Ymd');
		}
		if(isset($DT_fact) && $DT_fact!=''){
			$DT_fact = DateTime::createFromFormat('d?m?Y', $DT_fact);
			$DT_fact = $DT_fact->format('Ymd');
		}
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_project('save', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $projectid, PDO::PARAM_STR);
		$stmt->bindParam(2, $unitID, PDO::PARAM_STR);
		$stmt->bindParam(3, $name, PDO::PARAM_STR);
		$stmt->bindParam(4, $description, PDO::PARAM_STR);
		$stmt->bindParam(5, $status, PDO::PARAM_STR);
		$stmt->bindParam(6, $userID_resp, PDO::PARAM_STR);
		$stmt->bindParam(7, $_SESSION['UserID'], PDO::PARAM_STR);
		$stmt->bindParam(8, $DT_plan, PDO::PARAM_STR);
		$stmt->bindParam(9, $DT_fact, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}

//goods
	public function good_info() {
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
//CALL pr_goods(action, _GoodID, _Good1C, _Article, _Name, _Division, _Unit_in_pack, _Unit, _Weight, _DiscountMax, _FreeBalance, _PriceBase, _Price1, _Price2, _Price3, _Price4, _id);
		$stmt = $this->db->prepare("CALL pr_goods_site('info', @_id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $goodid, PDO::PARAM_STR);
		$stmt->bindParam(2, $article, PDO::PARAM_STR);
		$stmt->bindParam(3, $name, PDO::PARAM_STR);
		$stmt->bindParam(4, $namestickers, PDO::PARAM_STR);
		$stmt->bindParam(5, $unit, PDO::PARAM_STR);
		$stmt->bindParam(6, $trademark, PDO::PARAM_STR);
		$stmt->bindParam(7, $countryproducer, PDO::PARAM_STR);
		$stmt->bindParam(8, $typesticker, PDO::PARAM_STR);
		$stmt->bindParam(9, $packtype, PDO::PARAM_STR);
		$stmt->bindParam(10, $packmaterial, PDO::PARAM_STR);
		$stmt->bindParam(11, $foldorder, PDO::PARAM_STR);
		$stmt->bindParam(12, $segment, PDO::PARAM_STR);
		$stmt->bindParam(13, $visible, PDO::PARAM_STR);
		$stmt->bindParam(14, $markupid, PDO::PARAM_STR);
		$stmt->bindParam(15, $service, PDO::PARAM_STR);
		$stmt->bindParam(16, $division, PDO::PARAM_STR);
		$stmt->bindParam(17, $length, PDO::PARAM_STR);
		$stmt->bindParam(18, $width, PDO::PARAM_STR);
		$stmt->bindParam(19, $height, PDO::PARAM_STR);
		$stmt->bindParam(20, $weight, PDO::PARAM_STR);
		$stmt->bindParam(21, $unit_in_pack, PDO::PARAM_STR);
		$stmt->bindParam(22, $perioddelivery, PDO::PARAM_STR); 
		$stmt->bindParam(23, $discountmax, PDO::PARAM_STR); 
		$stmt->bindParam(24, $visibleinorder, PDO::PARAM_STR);
		$stmt->bindParam(25, $description, PDO::PARAM_STR);
		$stmt->bindParam(26, $imageURL, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			break; //берем первую запись из результата
		}
//Fn::debugToLog("good info", json_encode($row));
		return $row;
	}
	public function good_save() {
		foreach ($_REQUEST as $arg => $val){
			${$arg} = $val;	if($val=='') ${$arg} = null;
		}
//Fn::paramToLog();
//		if ($visible == 1)	{
//			$visible = true;
//		} else {
//			$visible = false;
//		}
		$stmt = $this->db->prepare("CALL pr_goods_site('save', @_id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $goodid, PDO::PARAM_STR);
		$stmt->bindParam(2, $article, PDO::PARAM_STR);
		$stmt->bindParam(3, $name, PDO::PARAM_STR);
		$stmt->bindParam(4, $namestickers, PDO::PARAM_STR);
		$stmt->bindParam(5, $unit, PDO::PARAM_STR);
		$stmt->bindParam(6, $trademark, PDO::PARAM_STR);
		$stmt->bindParam(7, $countryproducer, PDO::PARAM_STR);
		$stmt->bindParam(8, $typesticker, PDO::PARAM_STR);
		$stmt->bindParam(9, $packtype, PDO::PARAM_STR);
		$stmt->bindParam(10, $packmaterial, PDO::PARAM_STR);
		$stmt->bindParam(11, $foldorder, PDO::PARAM_STR);
		$stmt->bindParam(12, $segment, PDO::PARAM_INT);
		$stmt->bindParam(13, $visible, PDO::PARAM_STR);
		$stmt->bindParam(14, $markupid, PDO::PARAM_STR);
		$stmt->bindParam(15, $service, PDO::PARAM_STR);
		$stmt->bindParam(16, $division, PDO::PARAM_STR);
		$stmt->bindParam(17, $length, PDO::PARAM_STR);
		$stmt->bindParam(18, $width, PDO::PARAM_STR);
		$stmt->bindParam(19, $height, PDO::PARAM_STR);
		$stmt->bindParam(20, $weight, PDO::PARAM_STR);
		$stmt->bindParam(21, $unit_in_pack, PDO::PARAM_STR);
		$stmt->bindParam(22, $perioddelivery, PDO::PARAM_STR);
		$stmt->bindParam(23, $discountmax, PDO::PARAM_STR);
		$stmt->bindParam(24, $visibleinorder, PDO::PARAM_STR);
		$stmt->bindParam(25, $description, PDO::PARAM_STR);
		$stmt->bindParam(26, $imageURL, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}
	public function good_param_save(){
		foreach ($_REQUEST as $arg => $val) {
			${$arg} = $val;
			if ($val == '')	${$arg} = null;
		}
//Fn::paramToLog();

		$stmt = $this->db->prepare("CALL pr_goods_param( ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $goodid, PDO::PARAM_STR);
		$stmt->bindParam(3, $value, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
//		$this->echo_response($stmt);

		$response = new stdClass();
		$response->success	= Fn::checkErrorMySQLstmt($stmt);
		$response->goodid	= $goodid;
		$response->article	= '';
		$response->name		= '';
		$response->value_old= '';
		$response->value_new= '';

		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			$response->success = $row[0];
			$response->goodid = $row[1];
			$response->article = $row[2];
			$response->name = $row[3];
			$response->value_old = $row[4];
			$response->value_new = $row[5];
		}
//Fn::debugToLog("set param", json_encode($response));
		if ($action!='ImageURL') {
			header("Content-type: application/json;charset=utf-8");
			echo json_encode($response);
		} else {
			return $response;
		}
	}
//balance_min
	public function balance_min_set(){
		foreach ($_REQUEST as $arg => $val) {
			${$arg} = $val;
			if ($val == '')	${$arg} = null;
		}
		$goodid = $id;
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_balance_min( ?, @id, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $clientid, PDO::PARAM_STR);
		$stmt->bindParam(3, $goodid, PDO::PARAM_STR);
		$stmt->bindParam(4, $BalanceMinM, PDO::PARAM_STR);
		$stmt->bindParam(5, $_AVG, PDO::PARAM_STR);
		$stmt->bindParam(6, $_STD, PDO::PARAM_STR);
		$stmt->bindParam(7, $_DT_start, PDO::PARAM_STR);
		$stmt->bindParam(8, $_DT_stop, PDO::PARAM_STR);
		$stmt->bindParam(9, $_param, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
//		$this->echo_response($stmt);

		$response = new stdClass();
		$response->success	= Fn::checkErrorMySQLstmt($stmt);
		$response->goodid	= $goodid;
		$response->value_old = '';
		$response->value_new = '';

		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			$response->success = $row[0];
			$response->goodid = $row[1];
			$response->value_old = $row[2];
			$response->value_new = $row[3];
		}
//Fn::debugToLog("set param", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}
	public function balance_min_set_auto(){
		foreach ($_REQUEST as $arg => $val) {
			${$arg} = $val;
			if ($val == '')	${$arg} = null;
		}
		$goodid = $id;
		$url = urldecode($_SERVER['QUERY_STRING']);
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_balance_min( ?, @id, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $point_balance_min, PDO::PARAM_STR);
		$stmt->bindParam(3, $_goodid, PDO::PARAM_STR);
		$stmt->bindParam(4, $_BalanceMinM, PDO::PARAM_STR);
		$stmt->bindParam(5, $_AVG, PDO::PARAM_STR);
		$stmt->bindParam(6, $_STD, PDO::PARAM_STR);
		$stmt->bindParam(7, $_DT_start, PDO::PARAM_STR);
		$stmt->bindParam(8, $_DT_stop, PDO::PARAM_STR);
		$stmt->bindParam(9, $url, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
//		$this->echo_response($stmt);

		$response = new stdClass();
		$response->success	= Fn::checkErrorMySQLstmt($stmt);
		$response->goodid	= $goodid;
		$response->value_old = '';
		$response->value_new = '';
		$response->clientid = $point_balance_min;

		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			$response->success = $row[0];
			$response->goodid = $row[1];
			$response->value_old = $row[2];
			$response->value_new = $row[3];
		}
//Fn::debugToLog("set param", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}

//point
	public function point_info() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_point('info', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $clientID, PDO::PARAM_STR);
		$stmt->bindParam(2, $matrixID, PDO::PARAM_STR);
		$stmt->bindParam(3, $nameShort, PDO::PARAM_STR);
		$stmt->bindParam(4, $nameValid, PDO::PARAM_STR);
		$stmt->bindParam(5, $city, PDO::PARAM_STR);
		$stmt->bindParam(6, $address, PDO::PARAM_STR);
		$stmt->bindParam(7, $telephone, PDO::PARAM_STR);
		$stmt->bindParam(8, $countTerminal, PDO::PARAM_STR);
		$stmt->bindParam(9, $rD, PDO::PARAM_STR);
		$stmt->bindParam(10, $priceType, PDO::PARAM_STR);
		$stmt->bindParam(11, $appVersion, PDO::PARAM_STR);
		$stmt->bindParam(12, $statusID, PDO::PARAM_STR);
		$stmt->bindParam(13, $balanceActivity, PDO::PARAM_STR);
		$stmt->bindParam(14, $x1C, PDO::PARAM_STR);
		$stmt->bindParam(15, $label, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			break; //берем первую запись из результата
		}
		return $row;
	}
	public function point_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		if ($matrixID == '') $matrixID = null;
		if ($version == '')	$version = null;
		if ($priceType == '') $priceType = null;
		if ($balanceActivity == '') $balanceActivity = null;
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_point('save', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $clientID, PDO::PARAM_STR);
		$stmt->bindParam(2, $matrixID, PDO::PARAM_STR);
		$stmt->bindParam(3, $nameShort, PDO::PARAM_STR);
		$stmt->bindParam(4, $nameValid, PDO::PARAM_STR);
		$stmt->bindParam(5, $city, PDO::PARAM_STR);
		$stmt->bindParam(6, $address, PDO::PARAM_STR);
		$stmt->bindParam(7, $telephone, PDO::PARAM_STR);
		$stmt->bindParam(8, $countTerminal, PDO::PARAM_STR);
		$stmt->bindParam(9, $rD, PDO::PARAM_STR);
		$stmt->bindParam(10, $priceType, PDO::PARAM_STR);
		$stmt->bindParam(11, $appVersion, PDO::PARAM_STR);
		$stmt->bindParam(12, $version, PDO::PARAM_STR);
		$stmt->bindParam(13, $balanceActivity, PDO::PARAM_STR);
		$stmt->bindParam(14, $x1C, PDO::PARAM_STR);
		$stmt->bindParam(15, $label, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}
	public function point_users_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_point('point_users_save', @id, ?, ?, null, null, null, null, null, null, null, null, null, null, ?, null, null)");
		$stmt->bindParam(1, $pointid, PDO::PARAM_STR);
		$stmt->bindParam(2, $userid, PDO::PARAM_STR);
		$stmt->bindParam(3, $value, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}

//task
	public function task_info() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_project_content('info', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $taskid, PDO::PARAM_STR);
		$stmt->bindParam(2, $projectid, PDO::PARAM_STR);
		$stmt->bindParam(3, $parentID, PDO::PARAM_STR);
		$stmt->bindParam(4, $status, PDO::PARAM_STR);
		$stmt->bindParam(5, $unitID, PDO::PARAM_STR);
		$stmt->bindParam(6, $name, PDO::PARAM_STR);
		$stmt->bindParam(7, $description, PDO::PARAM_STR);
		$stmt->bindParam(8, $userID_resp, PDO::PARAM_STR);
		$stmt->bindParam(9, $DT_plan, PDO::PARAM_STR);
		$stmt->bindParam(10, $DT_fact, PDO::PARAM_STR);
		$stmt->bindParam(11, $userID_create, PDO::PARAM_STR);
		$stmt->bindParam(12, $DT_create, PDO::PARAM_STR);
		$stmt->bindParam(13, $orderID, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			break; //берем первую запись из результата
		}
		return $row;
	}
	public function task_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		if ($taskid == '') $taskid = null;
		if ($projectid == '') $projectid = null;
		if ($parentID == '') $parentID = null;
		if ($status == '') $status = 0;
		if ($unitID == '') $unitID = null;
		if ($userID_resp == '')	$userID_resp = null;
		if ($orderID == '')	$orderID = null;
		if (isset($DT_plan) && $DT_plan != '') {
			$DT_plan = DateTime::createFromFormat('d?m?Y', $DT_plan);
			$DT_plan = $DT_plan->format('Ymd');
		}
		if (isset($DT_fact) && $DT_fact != '') {
			$DT_fact = DateTime::createFromFormat('d?m?Y', $DT_fact);
			$DT_fact = $DT_fact->format('Ymd');
		}
//Fn::paramToLog();
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_project_content('save', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $taskid, PDO::PARAM_STR);
		$stmt->bindParam(2, $projectid, PDO::PARAM_STR);
		$stmt->bindParam(3, $parentID, PDO::PARAM_STR);
		$stmt->bindParam(4, $status, PDO::PARAM_STR);
		$stmt->bindParam(5, $unitID, PDO::PARAM_STR);
		$stmt->bindParam(6, $name, PDO::PARAM_STR);
		$stmt->bindParam(7, $description, PDO::PARAM_STR);
		$stmt->bindParam(8, $userID_resp, PDO::PARAM_STR);
		$stmt->bindParam(9, $DT_plan, PDO::PARAM_STR);
		$stmt->bindParam(10, $DT_fact, PDO::PARAM_STR);
		$stmt->bindParam(11, $_SESSION['UserID'], PDO::PARAM_STR);
		$stmt->bindParam(12, $DT_create, PDO::PARAM_STR);
		$stmt->bindParam(13, $orderID, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}

//discount cards 
	public function discountcard_info() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$id = 0;
		$stmt = $this->db->prepare("CALL pr_card('info_site', :_BarCode, @_id)");
		$stmt->bindValue(":_BarCode", $cardid);
// вызов хранимой процедуры
		//$stmt->execute(array($id));
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			break; //берем первую запись из результата
		}
//Fn::debugToLog("card", json_encode($row));
		return $row;
	}
	public function discoundcard_history() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		if ($checkid == '')	$checkid = null;
		$action = "history_".$oper;
		$checkid = str_replace(",",".",$checkid);
//Fn::paramToLog();
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_discountCard(?, @id, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $cardid, PDO::PARAM_STR);
		$stmt->bindParam(3, $name, PDO::PARAM_STR);
		$stmt->bindParam(4, $dateOfIssue, PDO::PARAM_STR);
		$stmt->bindParam(5, $dateOfCancellation, PDO::PARAM_STR);
		$stmt->bindParam(6, $clientID, PDO::PARAM_STR);
		$stmt->bindParam(7, $address, PDO::PARAM_STR);
		$stmt->bindParam(8, $eMail, PDO::PARAM_STR);
		$stmt->bindParam(9, $phone, PDO::PARAM_STR);
		$stmt->bindParam(10, $animal, PDO::PARAM_STR);
		$stmt->bindParam(11, $startPercent, PDO::PARAM_STR);
		$stmt->bindParam(12, $startSum, PDO::PARAM_STR);
		$stmt->bindParam(13, $checkid, PDO::PARAM_STR);
		$stmt->bindParam(14, $percentOfDiscount, PDO::PARAM_STR);
		$stmt->bindParam(15, $howWeLearn, PDO::PARAM_STR);
		$stmt->bindParam(16, $notes, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}
	public function discoundcard_save() {
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
//Fn::paramToLog();
		if ($clientID == '') $clientID = null;
		if ($startPercent == '') $startPercent = 0;
		if ($startSum == '') $startSum = 0;
		if ($dopSum == '') $dopSum = 0;
		if ($percentOfDiscount == '') $percentOfDiscount = 0;
		if (isset($dateOfIssue) && $dateOfIssue != '') {
			if(strlen($dateOfIssue)<=10)$dateOfIssue .= "00:00:00";
			$dateOfIssue = DateTime::createFromFormat('Y?m?d H?i?s', $dateOfIssue);
			$dateOfIssue = $dateOfIssue->format('Ymd');
		}
		if (isset($dateOfCancellation) && $dateOfCancellation != '') {
			if (strlen($dateOfCancellation) <= 10)	$dateOfCancellation .= "00:00:00";
			$dateOfCancellation = DateTime::createFromFormat('Y?m?d H?i?s', $dateOfCancellation);
			//$dateOfCancellation = DateTime::createFromFormat('d?m?Y H?i?s', $dateOfCancellation);
			$dateOfCancellation = $dateOfCancellation->format('Ymd');
		}
//Fn::paramToLog();
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_card_attribute('card_attr_edit_site', :_CardID, :_Family, :_Name, :_MiddleName, :_Address, :_Phone1, :_Phone2, :_EMail, :_AnimalType, :_AnimalBreed, :_Notes, :_DateOfIssue, :_PercentOfDiscount, :_AmountOfBuying, :_DateOfCancellation, :_TradePointID, :_HowWeLearn, :_ParentCardID, @_id)");
		$stmt->bindParam(":_CardID", $cardid);
		$stmt->bindParam(":_Family", $family);
		$stmt->bindParam(":_Name", $name);
		$stmt->bindParam(":_MiddleName", $middlename);
		$stmt->bindParam(":_Address", $address);
		$stmt->bindParam(":_Phone1", $phone1);
		$stmt->bindParam(":_Phone2", $phone2);
		$stmt->bindParam(":_EMail", $eMail);
		$stmt->bindParam(":_AnimalType", $startPercent);
		$stmt->bindParam(":_AnimalBreed", $startSum);
		$stmt->bindParam(":_Notes", $notes);
		$stmt->bindParam(":_DateOfIssue", $dateOfIssue);
		$stmt->bindParam(":_PercentOfDiscount", $percentOfDiscount);
		$stmt->bindParam(":_AmountOfBuying", $dopSum);
		$stmt->bindParam(":_DateOfCancellation", $dateOfCancellation);
		$stmt->bindParam(":_TradePointID", $clientID);
		$stmt->bindParam(":_HowWeLearn", $howWeLearn);
		$stmt->bindParam(":_ParentCardID", $parent);

// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}
	public function card_animal(){
		foreach ($_REQUEST as $arg => $val) ${$arg} = $val;
//Fn::paramToLog();
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		if ($animalid=="") $action = "add_animal";
		if ($animalid!="") $action = "edit_animal";
		//if ($animalid!="") $action = "del_animal";
		$stmt = $this->db->prepare("CALL pr_card_attribute(:action, :_CardID, :_Family, :_Name, :_MiddleName, :_Address, :_Phone1, :_Phone2, :_EMail, :_AnimalType, :_AnimalBreed, :_Notes, :_DateOfIssue, :_PercentOfDiscount, :_AmountOfBuying, :_DateOfCancellation, :_TradePointID, :_HowWeLearn, :_ParentCardID, @_id)");
		$stmt->bindParam(":action", $action);
		$stmt->bindParam(":_CardID", $cardid);
		$stmt->bindParam(":_Family", $family);
		$stmt->bindParam(":_Name", $name);
		$stmt->bindParam(":_MiddleName", $middlename);
		$stmt->bindParam(":_Address", $address);
		$stmt->bindParam(":_Phone1", $phone1);
		$stmt->bindParam(":_Phone2", $phone2);
		$stmt->bindParam(":_EMail", $eMail);
		$stmt->bindParam(":_AnimalType", $AnimalType);
		$stmt->bindParam(":_AnimalBreed", $AnimalBreed);
		$stmt->bindParam(":_Notes", $notes);
		$stmt->bindParam(":_DateOfIssue", $dateOfIssue);
		$stmt->bindParam(":_PercentOfDiscount", $percentOfDiscount);
		$stmt->bindParam(":_AmountOfBuying", $dopSum);
		$stmt->bindParam(":_DateOfCancellation", $dateOfCancellation);
		$stmt->bindParam(":_TradePointID", $clientID);
		$stmt->bindParam(":_HowWeLearn", $howWeLearn);
		$stmt->bindParam(":_ParentCardID", $animalid);

// вызов хранимой процедуры
		$stmt->execute();
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		$this->echo_response($stmt);
	}

//sellers
	public function seller_info() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
//return;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_seller('info', @id, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $sellerID, PDO::PARAM_STR);
		$stmt->bindParam(2, $kod1C, PDO::PARAM_STR);
		$stmt->bindParam(3, $clientID, PDO::PARAM_STR);
		$stmt->bindParam(4, $name, PDO::PARAM_STR);
		$stmt->bindParam(5, $post, PDO::PARAM_STR);
		$stmt->bindParam(6, $postID, PDO::PARAM_STR);
		$stmt->bindParam(7, $fired, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			break; //берем первую запись из результата
		}
		return $row;
	}
	public function seller_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		if ($sellerID == '') $sellerID = null;
		if ($sellerID == '-1') $sellerID = null;
		if ($postID == '')	$postID = null;
		if ($clientID == '') $clientID = 0;
		$stmt = $this->db->prepare("CALL pr_seller('save', @id, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $sellerID, PDO::PARAM_STR);
		$stmt->bindParam(2, $kod1C, PDO::PARAM_STR);
		$stmt->bindParam(3, $clientID, PDO::PARAM_STR);
		$stmt->bindParam(4, $name, PDO::PARAM_STR);
		$stmt->bindParam(5, $post, PDO::PARAM_STR);
		$stmt->bindParam(6, $postID, PDO::PARAM_STR);
		$stmt->bindParam(7, $fired, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}

//users
	public function user_info($userVal) {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
                if (isset($userVal)) $userID = $userVal;
//Fn::paramToLog();
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$stmt = $this->db->prepare("CALL pr_user_info('info', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $userID, PDO::PARAM_STR);
		$stmt->bindParam(2, $login, PDO::PARAM_STR);
		$stmt->bindParam(3, $password, PDO::PARAM_STR);
		$stmt->bindParam(4, $email, PDO::PARAM_STR);
		$stmt->bindParam(5, $clientid, PDO::PARAM_STR);
		$stmt->bindParam(6, $userName, PDO::PARAM_STR);
		$stmt->bindParam(7, $userPhone, PDO::PARAM_STR);
		$stmt->bindParam(8, $accessLevel, PDO::PARAM_STR);
		$stmt->bindParam(9, $position, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		foreach ($rowset as $row) {
			break; //берем первую запись из результата
		}
//		Fn::debugToLog("row", json_encode($row));
		return $row;
	}
	public function user_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		if ($userid == null)
			$userid = 0;
		if ($eMail == null)
			$eMail = '';
		if ($password == null)
			$password = '';
		if ($companyName == null)
			$companyName = '';
		if ($clientid == null)
			$clientid = 0;
////Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_user_info('save', @id, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $userid, PDO::PARAM_STR);
		$stmt->bindParam(2, $login, PDO::PARAM_STR);
		$stmt->bindParam(3, $password, PDO::PARAM_STR);
		$stmt->bindParam(4, $eMail, PDO::PARAM_STR);
		$stmt->bindParam(5, $clientid, PDO::PARAM_STR);
		$stmt->bindParam(6, $userName, PDO::PARAM_STR);
		$stmt->bindParam(7, $userPhone, PDO::PARAM_STR);
		$stmt->bindParam(8, $accessLevel, PDO::PARAM_STR);
		$stmt->bindParam(9, $position, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}

//jqgrid
	public function get_jqgrid3() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::debugToLog('QUERY_STRING', urldecode($_SERVER['QUERY_STRING']));
		$url = urldecode($_SERVER['QUERY_STRING']);
		$url = str_replace("field1", $f1, $url);
		$url = str_replace("field2", $f2, $url);
		$url = str_replace("field3", $f3, $url);
		$url = str_replace("field4", $f4, $url);
		$url = str_replace("field5", $f5, $url);
		$url = str_replace("field6", $f6, $url);
		$url = str_replace("field7", $f7, $url);
		$url = str_replace("field8", $f8, $url);
		$url = str_replace("field9", $f9, $url);
		$url = str_replace("field10", $f10, $url);
		$url = str_replace("field11", $f11, $url);
		$url = str_replace("field12", $f12, $url);
		$url = str_replace("field13", $f13, $url);
		$url = str_replace("field14", $f14, $url);
		$url = str_replace("field15", $f15, $url);

		$url = str_replace("pr.Status=-1", "pr.Status<>100", $url);
		$url = str_replace("pc.Status=-1", "pc.Status<>100", $url);

		$url = str_replace("==", "=", $url);
		$url = str_replace("=>", ">", $url);
		$url = str_replace("=<", "<", $url);
		$url = str_replace("=<>", "<>", $url);
if ($filters == '') 
		$url = str_replace("&filters=", "", $url);
if ($action == 'good_list_doc') {
	if (isset($Name) || isset($Article))
		$url = str_replace("&group=$group", "", $url);
	$url .= '&good_list_DocID=' . $_SESSION['CurrentDocID'];
	//Fn::debugToLog('jqgrid3 проверка', "&group=$group");
}
if ($action == 'package_list' || 
	$action == 'cancel_list'  || $action == 'difference_list') {
	$url .= '&o.UserID=' . $_SESSION['UserID'];
}
//Fn::debugToLog('jqgrid3 action', $action);
Fn::debugToLog('jqgrid3 url', $url);
//Fn::paramToLog();

		$stmt = $this->db->prepare("CALL pr_jqgrid(?, @id, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $url, PDO::PARAM_STR);
	// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$response = new stdClass();
		$response->records = 0;
		$response->page = 0;
		$response->total = 0;
		$r = 0;
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				if ($r == 1) {
					$i = 0;
					foreach ($rowset as $row) {
						$response->records = $row['_rows_count'];
						$response->page = $row['_page'];
						$response->total = $row['_total_pages'];
						$i++;
					}
				} else {
					$i = 0;
					$colCount = $stmt->columnCount();
					foreach ($rowset as $row) {
						$response->rows[$i]['id'] = str_replace('.','_',$row[0]);
						$response->rows[$i]['cell'] = array($row[$f1],
							$row[$f2],
							$row[$f3],
							$row[$f4],
							$row[$f5],
							$row[$f6],
							$row[$f7],
							$row[$f8],
							$row[$f9],
							$row[$f10],
							$row[$f11],
							$row[$f12],
							$row[$f13],
							$row[$f14],
							$row[$f15],
						);
//						$response->rows[$i]['cell'] = array_values($row);
						//$a1 = array_fill_keys(array_keys($row),$row);
						//$a1 = array_fill(0,$colCount-1,$row);
						//$a1 = array_fill(0,1,array_values($row));
//						if (1 == 0) {
//							ob_start();
//							var_dump($response->rows[$i]['cell']);
//							var_dump(array_values($row));
//							//Fn::DebugToLog("test param\n" . $_SERVER['SCRIPT_FILENAME'] . "\n" . $_SERVER['REQUEST_URI'] . "\n", ob_get_clean());
//							Fn::DebugToLog("тест а1", ob_get_clean());
//							ob_end_clean();
//						}
						$i++;
					}
				}
			}
			$r++;
		} while ($stmt->nextRowset());
		
//Fn::DebugToLog("тест jqgrid3", json_encode($response));
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
}
//дерево
	public function tree_NS() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_tree_NS('category', 'CatID', ?, ?, ?, ?)");
		$stmt->bindParam(1, $nodeid, PDO::PARAM_STR);
		$stmt->bindParam(2, $n_level, PDO::PARAM_STR);
		$stmt->bindParam(3, $n_left, PDO::PARAM_STR);
		$stmt->bindParam(4, $n_right, PDO::PARAM_STR);
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		//$tree = array();
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_ASSOC);
			//$tree = $this->createTree($rowset, $rowset[0]['lft'] - 1);
			break;
		} while ($stmt->nextRowset());

//Fn::DebugToLog('tree result: ', json_encode($rowset));
//return;
		//$result = Shop::GetCategoryTreeNS($this->dbi, $nodeid, $n_level, $n_left, $n_right);
		if ($nodeid > 0) {
			$n_level = $n_level + 1;
		} else {
			$n_level = 0;
		}
		$response = new stdClass();
		$response->page = 1;
		$response->total = 1;
		$response->records = 1;
		$i = 0;
//		while ($row = $result->fetch_array(MYSQLI_BOTH)) {
		if ($rowset) {
			foreach ($rowset as $row) {
				if ($row['rgt'] == $row['lft'] + 1)
					$leaf = 'true';
				else
					$leaf = 'false';
				if ($n_level == $row['level']) { // we output only the needed level
					$response->rows[$i]['id'] = $row['CatID'];
					$response->rows[$i]['cell'] = array($row['CatID'],
						//$row['name'].' ('.$row['CatID'].')',
						$row['name'],
						$row['level'],
						$row['lft'],
						$row['rgt'],
						$leaf,
						'false'
					);
				}
				$i++;
			}
		}
//Fn::DebugToLog('tree result2: ', json_encode($response));
		header("Content-type: text/html;charset=utf-8");
		echo json_encode($response);
	}

//for select2
	public function select2() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		if ($action=='point') $type = $_SESSION['UserID'];
		$stmt = $this->db->prepare("CALL pr_select2(?, @id, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $name, PDO::PARAM_STR);
		$stmt->bindParam(3, $type, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$response = array();
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				$i = 0;
				foreach ($rowset as $row) {
					$response[$i] = array('id' => $row[0], 'text' => $row[1]);
					$i++;
				}
			}
		} while ($stmt->nextRowset());
//Fn::debugToLog("select2", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}
	public function select_search() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$type = 1;
		$stmt = $this->db->prepare("CALL shop.pr_select2(?, @id, ?, ?)");
		$stmt->bindParam(1, $action, PDO::PARAM_STR);
		$stmt->bindParam(2, $name, PDO::PARAM_STR);
		$stmt->bindParam(3, $type, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$response = array();
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				$i = 0;
				foreach ($rowset as $row) {
					$response[$i] = array('id' => $row[0], 'name' => $row[1]);
					$i++;
				}
			}
		} while ($stmt->nextRowset());
//Fn::debugToLog("select_search", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}
	public function select() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_card('list_animal', :_BarCode, @_id)");
		$stmt->bindValue(":_BarCode", $cardid);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		$response = '<select role="select" class="FormElement ui-widget-content ui-corner-all"><option value=""></option>';
		do {
			$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
			if ($rowset) {
				foreach ($rowset as $row) {
					//$response .= $row[0].':'.$row[0].';';
					$response .= '<option value="' . $row[0] . '">' . $row[0] . '</option>';
				}
			}
		} while ($stmt->nextRowset());
		$response .= "</select>";
//Fn::debugToLog("select2", json_encode($response));
		//header("Content-type: application/json;charset=utf-8");
		echo $response;
	}

//menu
	public function menu_list() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		$stmt = $this->db->prepare("CALL pr_menu('list',@id,?,?,?)");
		$stmt->bindParam(1, $menuid, PDO::PARAM_STR);
		$stmt->bindParam(2, $_SESSION['UserID'], PDO::PARAM_STR);
		$stmt->bindParam(3, $value, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt))
			return false;
		if ($stmt->rowCount() == 0){
			$stmt = $this->db->prepare("CALL pr_menu('all_menu_users',@id,?,?,?)");
			$stmt->bindParam(1, $menuid, PDO::PARAM_STR);
			$stmt->bindParam(2, $_SESSION['UserID'], PDO::PARAM_STR);
			$stmt->bindParam(3, $value, PDO::PARAM_STR);
			$stmt->execute();
			$stmt = $this->db->prepare("CALL pr_menu('list',@id,?,?,?)");
			$stmt->bindParam(1, $menuid, PDO::PARAM_STR);
			$stmt->bindParam(2, $_SESSION['UserID'], PDO::PARAM_STR);
			$stmt->bindParam(3, $value, PDO::PARAM_STR);
			$stmt->execute();
		}
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
		return $rowset;
	}
	public function menu_users_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$stmt = $this->db->prepare("CALL pr_menu('menu_users_save',@id,?,?,?)");
		$stmt->bindParam(1, $menuid, PDO::PARAM_STR);
		$stmt->bindParam(2, $userid, PDO::PARAM_STR);
		$stmt->bindParam(3, $value, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}
//reasons - основания для ручной скидки
	public function reasons_save() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		if (isset($oper)){
			$reason = $Reason;
			$value = $NoShow;
			if ($oper == 'edit') $typeReasonID = $id;
			if ($oper == 'add')  $typeReasonID = null;
		}
		$stmt = $this->db->prepare("CALL pr_reason('reasons_save',@id,?,?,?)");
		$stmt->bindParam(1, $typeReasonID, PDO::PARAM_STR);
		$stmt->bindParam(2, $reason, PDO::PARAM_STR);
		$stmt->bindParam(3, $value, PDO::PARAM_STR);
// вызов хранимой процедуры
		$stmt->execute();
		$this->echo_response($stmt);
	}

//category
	public function get_tree_NS_category() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		$result = Shop::GetCategoryTreeNS($this->dbi, $nodeid, $n_level, $n_left, $n_right);
		if ($nodeid > 0) {
			$n_level = $n_level + 1;
		} else {
			$n_level = 0;
		}
		$response->page = 1;
		$response->total = 1;
		$response->records = 1;
		$i = 0;
		while ($row = $result->fetch_array(MYSQLI_BOTH)) {
			if ($row['rgt'] == $row['lft'] + 1)
				$leaf = 'true';
			else
				$leaf = 'false';
			if ($n_level == $row['level']) { // we output only the needed level
				$response->rows[$i]['id'] = $row['CatID'];
				$response->rows[$i]['cell'] = array($row['CatID'],
					//$row['name'].' ('.$row['CatID'].')',
					$row['name'],
					$row['level'],
					$row['lft'],
					$row['rgt'],
					$leaf,
					'false'
				);
			}
			$i++;
		}
		header("Content-type: text/html;charset=utf-8");
		echo json_encode($response);
	}
	public function category_tree_oper() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		if ($oper == 'add') {
			$id = Shop::CreateNewElementTreeNS($this->dbi, 'category', $id, $parent_id, $name);
			if ($id == false) {
				$response->success = false;
				$response->message = 'Возникла ошибка при добавлении!<br>Сообщите разработчику!';
				$response->new_id = 0;
			} else {
				$response->success = true;
				$response->message = '';
				$response->new_id = $id;
			}
			echo json_encode($response);
		}
		if ($oper == 'edit') {
			$response->success = Shop::SetNewNameforElementTreeNS($this->dbi, 'category', $id, $name);
			$response->message = 'Возникла ошибка сохранения изменений!<br>Сообщите разработчику!';
			$response->new_id = 0;
			echo json_encode($response);
		}
		if ($oper == 'del') {
			//$response->success = DeleteElementTreeNS('category',$id);
			$response->success = Shop::MoveElementTreeNS($this->dbi, 'category', $id, 90);
			$response->message = 'Возникла ошибка при удалении!<br>Сообщите разработчику!';
			$response->new_id = 0;
			echo json_encode($response);
		}
		if ($oper == 'copy') {
			//echo CopyTreeByID('category',$source,$target);
			echo Shop::CopyTreeNS($this->dbi, 'category', $source, $target);
		}
		if ($oper == 'move') {
			//echo SetParentIDforTree('category','CatID',$source,$target);
			echo Shop::MoveElementTreeNS($this->dbi, 'category', $source, $target);
		}
	}
	public function add_in_cat() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		echo Shop::AddToCategory($this->dbi, $cat_id, $source);
	}
	public function del_from_cat() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		echo Shop::DelFromCategory($this->dbi, $cat_id, $source);
	}

//documents
	public function doc_edit() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		if ($docid == '')	$docid = $_SESSION['CurrentDocID'];
		if ($docid == '')	$docid = 0;
		$response = new stdClass();
		$response->success = false;
		$response->message = "";
		$response->docid = null;
		if ($qty == '')	 $qty = null;
		if ($info == '') $info = null;
		if ($clientid == '') $clientid = $_SESSION['ClientID'];
//Fn::debugToLog("docid", $docid);
//Fn::debugToLog("clientid", $clientid);
//Fn::debugToLog("UserID", $_SESSION['UserID']);
//Fn::debugToLog("operid", $operid);
//Fn::debugToLog("partnerid", $partnerid);
		$stmt = $this->db->prepare("call pr_doc(:action, @_id, :_ClientID, :_PartnerID, :_DocID, :_OperID, :_GoodID, :_Qty, :_Info, :_Status, :_UserID, :_Notes, :_Invoice)");
		$stmt->bindParam(":action", $action);
		$stmt->bindParam(":_ClientID", $clientid);
		$stmt->bindParam(":_PartnerID", $partnerid);
		$stmt->bindParam(":_DocID", $docid);
		$stmt->bindParam(":_OperID", $operid);
		$stmt->bindParam(":_GoodID", $goodid);
		$stmt->bindParam(":_Qty", $qty);
		$stmt->bindParam(":_Info", $info);
		$stmt->bindParam(":_Status", $status);
		$stmt->bindParam(":_UserID", $_SESSION['UserID']);
		$stmt->bindParam(":_Notes", $notes);
		$stmt->bindParam(":_Invoice", $invoice);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) {
			$ar = $stmt->errorInfo();
			$response->success = false;
			$response->message = "Ошибка при изменении документа!";
		} else {
			do {
				$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
				if ($rowset) {
					foreach ($rowset as $row) {
						$response->success = ($row[0] != 0);
						$response->message = $row[1];
						$response->docid = $row['CurrentDocID'];
						if ($row['CurrentDocID'] != null)
							$_SESSION['CurrentDocID'] = $row['CurrentDocID'];
						break;
					}
				}
			} while ($stmt->nextRowset());
		}
//Fn::debugToLog("resp", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}
	public function doc_info_full() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$response = new stdClass();
		$response->success = false;
		$response->clientid = $_SESSION['ClientID'];
		$response->partnerid = 0;
		$response->period = "";
		$response->message = "";
		$response->html = "";

		//$action = 'sale_info';
		if ($docid == '')	$docid = $_SESSION['CurrentDocID'];
		if ($docid == '')	$docid = 0;
		if ($action == 'package_info') $doctype = 'package';
		if ($action == 'receipt_info') $doctype = 'receipt';
		if ($action == 'cancel_info')  $doctype = 'cancel';
		if ($action == 'difference_info')  $doctype = 'difference';
		if ($action == 'timesheet_info') $doctype = 'timesheet';
		if ($action == 'inventory_info')  $doctype = 'inventory';
		if ($view) $notes = 'view';

		$stmt = $this->db->prepare("call pr_doc(:action, @_id, :_ClientID, :_PartnerID, :_DocID, :_OperID, :_GoodID, :_Qty, :_Info, :_Status, :_UserID, :_Notes, :_Invoice)");
		$stmt->bindParam(":action", $action);
		$stmt->bindParam(":_ClientID", $_SESSION['ClientID']);
		$stmt->bindParam(":_PartnerID", $partnerid);
		$stmt->bindParam(":_DocID", $docid);
		$stmt->bindParam(":_OperID", $operid);
		$stmt->bindParam(":_GoodID", $goodid);
		$stmt->bindParam(":_Qty", $qty);
		$stmt->bindParam(":_Info", $info);
		$stmt->bindParam(":_Status", $status);
		$stmt->bindParam(":_UserID", $_SESSION['UserID']);
		$stmt->bindParam(":_Notes", $notes);
		$stmt->bindParam(":_Invoice", $invoice);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) {
			$ar = $stmt->errorInfo();
			$response->success = false;
			$response->message = "Ошибка при получении информации о документе!";
		} else {
			$cnt = 1;
			$str = '';
			do {
				$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
				$response->success = true;
				if ($cnt == 1) {
					foreach ($rowset as $row) {
						$response->clientid = $row['ClientID'];
						$response->partnerid = $row['PartnerID'];
						$response->period = $row['Period'];
						$str .= '
								 <input id="docid" type="hidden" value="' . $row['DocID'] . '"/>';
						if (!$view) {
							$str .= '
								 <div class="row">
									<div id="div_doc_buttons" class = "col-md-12 col-xs-12 TAL hidden-print">';
							if ($action != 'timesheet_info') $str .= '<button id="good_add"	type="button" class="btn btn-primary	btn-sm minw150 mb5"><span class="glyphicon glyphicon-plus mr5"></span>Добавить товар</button>';
$str .= '	
<button id="delete"		type="button" class="btn btn-danger		btn-sm minw150 mb5"><span class="glyphicon glyphicon-trash mr5"></span>Удалить документ</button>
<button id="doc_add"	type="button" class="btn btn-lilac		btn-sm minw150 mb5"><span class="glyphicon glyphicon-plus		mr5"></span>Новый документ</button>
<button id="print"		type="button" class="btn btn-info		btn-sm minw150 mb5"><span class="glyphicon glyphicon-print mr5"></span>Печать документа</button>
<button id="state"		type="button" class="btn btn-success	btn-sm minw150 mb5" title="Провести документ?"><span class="glyphicon glyphicon-ok mr5"></span>Провести</button>
<button					type="button" class="btn btn-link btn-sm minw150 mb5" disabled >Автор: ' . $row['UserName'] . '</button>
									</div>
								 </div>';
						}
						$str .= '
								 <div class="row">
									<div class = "col-md-12 col-xs-12 '.(($action == 'timesheet_info')?'hidden-print':'').'">
										<div class = "floatL">
											<div class="input-group input-group-sm w300">
											   <span class = "input-group-addon w130">Документ №</span>
											   <span class = "input-group-addon form-control TAC">' . $row['DocID'] . '</span>
											   <span class = "input-group-addon w32"></span>
											</div>
											<div class="input-group input-group-sm w300">
											   <span class = "input-group-addon w130">Статус:</span>
											   <span class = "input-group-addon form-control TAC">' . $row['State'] . '</span>
											   <span class = "input-group-addon w32"></span>
											</div>
										</div>
										<div class="floatL ml5">&nbsp</div>
									';
						if (!$view) {
							$str .= '
										<div class="floatL">
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Магазин:</span>
												<div id="select_companyID" class="w210"></div>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							if ($action == 'timesheet_info')
								$str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Период расчета:</span>
												<div id="select_periodID" class="w210"></div>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							if ($action=='receipt_info' || $action=='difference_info') $str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Контрагент:</span>
												<div id="select_partnerID" class="w210"></div>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							if ($action == 'inventory_info') $str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Тип документа:</span>
												<span class = "input-group-addon form-control w210 TAС">' . (($row['TypeDoc'] == 0) ? 'обычный' : 'сводный') . '</span>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							$str .= '	</div>';
						} else {
							$str .= '
										<div class="floatL">
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w80">Магазин:</span>
												<span class = "input-group-addon form-control w230 TAL">' . $row['Name'] . '</span>
												<span class = "input-group-addon w40"></span>
										   </div>
										   ';
							if ($action=='receipt_info' || $action=='difference_info') $str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w80">Контрагент:</span>
												<span class = "input-group-addon form-control w230 TAL">' . $row['PartnerName'] . '</span>
												<span class = "input-group-addon w40"></span>
										   </div>
										   ';
							if ($action=='inventory_info') $str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w80">Тип документа:</span>
												<span class = "input-group-addon form-control w230 TAС">' . (($row['TypeDoc']==0)?'обычный':'сводный') . '</span>
												<span class = "input-group-addon w40"></span>
										   </div>
										   ';
							$str .= '	</div>';
						}
						$str .= '
										<div class="floatL ml5">&nbsp</div>
										<div class="floatL">
										   <div class="input-group input-group-sm w450">
											  <span class = "input-group-addon w100">Примечание:</span>
											  <input type = "text" class = "form-control" ' . ((!$view) ? '' : 'disabled') . ' autofocus value = "' . $row['Notes'] . '" onchange="good_edit(\''. $doctype .'_edit_notes\',this,0,0,0,0,0,$(this).val());">
											  <span class = "input-group-addon w32"></span>
										   </div>
										   ';
						if ($action == 'timesheet_info')
						$str .= '
										   <div class="input-group input-group-sm w450">
												<span class="input-group-btn w50p"><a id="doc_fill" class="btn btn-default w100p" type="button">Заполнить документ</a></span>
												<span class="input-group-btn w50p"><a href="javascript:doc_info()" class="btn btn-default w100p" type="button">Пересчитать итоги</a></span>
										   </div>
										   ';
		//<img class="img-rounded h20 m0" src="../../images/save-as.png">
						if ($action=='receipt_info' || $action=='difference_info') $str .= '
										   <div class="input-group input-group-sm w450">
											  <span class = "input-group-addon w100">№ док. пост.:</span>
											  <input type = "text" class = "form-control" ' . ((!$view) ? '' : 'disabled') . ' autofocus value = "' . $row['1CID'] . '" onchange="good_edit(\''. $doctype .'_edit_invoice\',this,0,0,0,0,0,0,0,0,0,$(this).val());">
											  <span class = "input-group-addon w32"></span>
										   </div>
										   ';
						$str .= '
										</div>
									</div>
								 </div>
								 ';
					}
				}
				if ($cnt == 2) {
					if ($action == 'timesheet_info'){
						if ($response->period!='') { //$response->period = '2017-01';
						$dt1 = DateTime::createFromFormat('Y?m?d', $response->period.'-01');
						$dateStart = $dt1->format('Ymd');
						$dt2 = DateTime::createFromFormat('Y?m?d', $response->period.'-01')->modify('+1 month')->modify('-1 day');
						$dateStop = $dt2->format('Ymd');
						}else{
							$dt1 = new DateTime();
							$dateStart = $dt1->format('Ymd');
							$dt2 = new DateTime();
							$dateStop = $dt2->format('Ymd');
						}
						$data = array();
						foreach ($rowset as $row) {
							$data[$row['SellerID']]['SellerID'] = $row['SellerID'];
							$data[$row['SellerID']]['SellerName'] = $row['Name'];
							$data[$row['SellerID']]['Post'] = $row['Post'];
							$data[$row['SellerID']][$row['DT']] = $row['Value'];
						}
						$str .= '<div class="panel panel-default mt10 mr5">';
						$str .= '<table id="table_doc" class="table table-striped table-bordered font12 minw400" cellspacing="0"  width="100%">';
						$str .= '<thead><tr style="height0:100px;">
										<th rowspan=2 class="w50 center">№</th>
										<th rowspan=2 class="w150 center">Ф.И.О.</th>
										<th rowspan=2 class="w150 center">Должность</th>
										<th rowspan=2 class="w20 center trans90">Ставка</th>
										';
						$str .= '		<th rowspan=1 colspan='. $dt2->format('d') .'>'.Fn::rusm($response->period).'</th>';
						$str .= '		<th rowspan=2 class="w50 center">Итого часов</th>
										<th rowspan=2 class="w50 center">Отп.</th>
										<th rowspan=2 class="w50 center">Больн.</th>
										<th rowspan=2 class="w50 center">Раб. дни</th>
										';
						$str .= '		</tr><tr>';
						for ($dt = clone $dt1; $dt <= $dt2; $dt->modify('+1 day')) {
						$str .= '		<th rowspan=1 class="center trans90 wwn" style="">'.$dt->format('d').'</th>';
						}
						$str .= '		</tr>';
						$str .= '	 </thead><tbody>';
						$nn = 1;
						foreach ($data as $d) {
							$str .= '<tr>
										<td class="TAC">' . $nn . '</td>
										<td class="TAL wwn">' . $d['SellerName'] . '</td>
										<td class="TAL wwn">' . $d['Post'] . '</td>
										<td class="TAC">' . 1 . '</td>
									';
							$tHours = 0; $tOtpus = 0; $tBoln = 0; $tWorkDay = 0; $maxWorkDay = 5;
							for ($dt = clone $dt1; $dt <= $dt2; $dt->modify('+1 day')) {
								if($d[$dt->format('Y-m-d')] == 'О'){
									$tOtpus++;
									$class = 'holiday';	$value = 'О'; $maxWorkDay = 5;
								}else if($d[$dt->format('Y-m-d')] == 'Б'){
									$tBoln++;
									$class = 'hospital'; $value = 'Б'; $maxWorkDay = 5;
								}else if($d[$dt->format('Y-m-d')] == 'Х'){
									$tBoln++;
									$class = 'noworking'; $value = 'Х'; $maxWorkDay = 5;
								}else if($d[$dt->format('Y-m-d')] > 0){
									$tWorkDay++; $tHours = $tHours + $d[$dt->format('Y-m-d')];
									$class = 'workday';
									if ($maxWorkDay==0) {
										$class = 'orangeday';
										$maxWorkDay = 5;
									}else{
										$maxWorkDay --;
									}
									$value = $d[$dt->format('Y-m-d')];
								}else{
									$class = 'freeday'; $value = 'В'; $maxWorkDay = 5;
								}
								$str .= '		<td class="TAC ' . $class . '" onclick="javascript:doc_cell(\''.$d['SellerID'].'_'.$dt->format('Y-m-d').'\');" id='.$d['SellerID'].'_'.$dt->format('Y-m-d').'>' . $value . '</th>';
							}
							$nn++;
							$str .= '		<td class="TAC">'.$tHours.'</td>
											<td class="TAC">'.$tOtpus.'</td>
											<td class="TAC">'.$tBoln.'</td>
											<td class="TAC">'.$tWorkDay.'</td>
										';
						}
						$str .= '</tr>';
						$str .= '</tbody>';
						$str .= '</table></div>';
						$str .= '<div class="panel panel-default mt10 mr5 w300">';
						$str .= '<table id="table_doc" class="table table-striped table-bordered font12 minw200" cellspacing="0"  width="100%">';
						$str .= '<thead><tr style="height:40px;">
										<th colspan=2 class="w150 center">Условные обозначения</th>
										';
						$str .= '</tr>';
						$str .= '	 </thead><tbody>';
						$str .= '		<tr><td class="TAC">Выходной</td><td class="freeday">В</td></tr>';
						$str .= '		<tr><td class="TAC">Отпуск</td><td class="holiday">О</td></tr>';
						$str .= '		<tr><td class="TAC">Больничный</td><td class="hospital">Б</td></tr>';
						$str .= '		<tr><td class="TAC">Рабочий день</td><td class="workday">8</td></tr>';
						$str .= '		<tr><td class="TAC">Не числился</td><td class="noworkday">Х</td></tr>';
						$str .= '</tbody>';
						$str .= '</table></div>';
					}else{
						$total_qty = 0; 
						$col_cnt = 4;
						$str .= '<div class="panel panel-default mt10 mr5">';
						$str .= '<table id="table_doc" class="table table-striped table-bordered font12 minw400" cellspacing="0"  width="100%">';
						$str .= '<thead><tr>
										<th class="w50 center">Артикул</th>
										<th class="w150 center">Название</th>';
						if ($action!='receipt_info')
							$str .= '	<th class="w100  center">Инфо</th>';
						$str .= '		<th class="w40  center">Кол-во</th>';
						if ($action=='receipt_info'){
							$str .=	'		<th class="w40  center">Цена розн.</th>';
							$str .=	'		<th class="w40  center">Сумма</th>';
						}
						$str .= '	 </thead><tbody>';
						foreach ($rowset as $row) {
							$total_qty += $row['Quantity'];
							$str .= '<tr>
										<td class="TAL">' . $row['Article'] . '</td>
										<td class="TAL">' . $row['Name'] . '</td>';
							if (!$view){
							if ($action != 'receipt_info')
								$str .= '<td class="TAL">
											<input type="text" class="TAL editable inline-edit-cell" style="line-height:17px;width:100%;" min=0 onchange="good_edit(\''. $doctype .'_edit_good_info\',this,'. $row['GoodID'] .',null,null,$(this).val());" value="' . $row['Info'] . '">
										</td>';
							$str .= '	<td class="TAC">
											<input type="number" class="TAR editable inline-edit-cell" style="line-height:17px;width:60%;min-width:40px;" onchange="good_edit(\''. $doctype .'_edit\',this,'. $row['GoodID'] .',null,$(this).val(),null);" value="' . $row['Quantity'] . '">
											<span id="qty" class="hidden">' . $row['Quantity'] . '</span>
											<span class="ml5 mr5 glyphicon glyphicon-remove hidden-print" onclick="good_edit(\''. $doctype .'_edit\',$(this).prev(),'. $row['GoodID'] .',null,0);"></span>
										</td>';
								if ($action == 'receipt_info') {
									$str .= '<th class="w40  center">' . $row['Price'] . '</th>';
									$str .= '<th class="w40  center">' . $row['Sum'] . '</th>';
								}
							$str .= '</tr>';
							} else {
								$str .= '	<td class = "w40  center">' . $row['Info'] . '</td>';
								$str .= '	<td class = "w40  center">' . $row['Quantity'] . '</td>';
							}
						}
						if ($stmt->rowCount() == 0){
							$str .= '<tr><td colspan='. $col_cnt .' class="TAC">В документе нет товаров</td></tr>';
						}
						$str .= '</tbody>';
					}
				}
				if ($cnt == 3) {
					if ($action == 'timesheet_info'){
						
					}else{
						foreach ($rowset as $row) {
						if ($action=='receipt_info'){
							$str .= '<tfoot>
										<tr><th colspan=' . ($col_cnt-2) . '>Всего кол-во в документе:</th>
										<th class="TAC">' . $total_qty . ' ед.</th>
										<th colspan=2 class="TAC"></th></tr>
									 </tfoot>';
						}else if ($action=='package_info') {
							$str .= '<tfoot>
										<tr><th colspan=' . ($col_cnt-1) . '>Всего кол-во в документе:</th>
										<th class="TAC">' . $total_qty . ' ед.</th></tr>
										<th class="TAC"></th></tr>
									 </tfoot>';
						}else{
							$str .= '<tfoot>
										<tr><th colspan=' . ($col_cnt-1) . '>Всего кол-во в документе:</th><th class="TAC">' . $total_qty . ' ед.</th></tr>
									 </tfoot>';
						}
						}
						$str .= '</table></div>';
					}
				}
				$cnt++;
			} while ($stmt->nextRowset());
		}
		$response->html = $str;
//Fn::debugToLog("resp", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}
	public function doc_info() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$response = new stdClass();
		$response->success = false;
		$response->message = "";
		$response->html = "";

		if ($docid == '')
			$docid = $_SESSION['CurrentDocID'];
		if ($docid == '')
			$docid = 0;
		$docname = 'Документ';
		if ($action=='package_info') $docname = 'Расфасовка';
		if ($action=='receipt_info' && $operid==1) $docname = 'Приход';
		if ($action=='receipt_info' && $operid==-1) $docname = 'Возврат';
		if ($action=='cancel_info' && $operid==1) $docname = 'Оприх-ние';
		if ($action=='cancel_info' && $operid==-1) $docname = 'Списание';
		if ($action=='difference_info') $docname = 'Акт разногл.';
		
		$stmt = $this->db->prepare("call pr_doc(:action, @_id, :_ClientID, :_PartnerID, :_DocID, :_OperID, :_GoodID, :_Qty, :_Info, :_Status, :_UserID, :_Notes, :_Invoice)");
		$stmt->bindParam(":action", $action);
		$stmt->bindParam(":_ClientID", $_SESSION['ClientID']);
		$stmt->bindParam(":_PartnerID", $partnerid);
		$stmt->bindParam(":_DocID", $docid);
		$stmt->bindParam(":_OperID", $operid);
		$stmt->bindParam(":_GoodID", $goodid);
		$stmt->bindParam(":_Qty", $qty);
		$stmt->bindParam(":_Info", $info);
		$stmt->bindParam(":_Status", $status);
		$stmt->bindParam(":_UserID", $_SESSION['UserID']);
		$stmt->bindParam(":_Notes", $notes);
		$stmt->bindParam(":_Invoice", $invoice);

// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) {
			$ar = $stmt->errorInfo();
			$response->success = false;
			$response->message = "Ошибка при получении информации о заказе!";
		} else {
			$cnt = 1; $total_cnt = 0;
			$str = '';
			do {
				$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
				$response->success = true;
				if ($cnt == 1) {
					$str .= '<table id="table_doc" class="table table-striped table-bordered font11 minw300 maxw300" cellspacing="0"  width="100%">';
					foreach ($rowset as $row) {
						$str .= '<thead>
									<tr><th colspan=2 class="btn-warning" style="height:36px;vertical-align:middle;text-align:left;"><span class="font14 fontb">'.$docname.' № ' . $row['DocID'] . '</span></th></tr>
									<tr><th colspan=2 style="height:27px;text-align:left;">Статус: ' . (($row['Status'] == 0) ? 'предварительный' : 'в обработке') . '</th></tr>
								 </thead>';
					}
				}
				if ($cnt == 2) {
					$str .= '<thead><tr style="height:24px;">
									<th class="w100 center">Название</th>
									<th class="w30  center">К-во</th>
								 </thead><tbody>';
//									<th class="w30  center">Цена</th>
//									<th class="w30  center">Сумма</th></tr>
					foreach ($rowset as $row) {
						$str .= '<tr>
									<td class="TAL">' . $row['Name'] . '</td>
									<td class="TAR">' . $row['Quantity'] . '</td>
								 </tr>';
//									<td class="TAR">' . $row['Price'] . '</td>
//									<td class="TAR">' . $row['Sum'] . '</td>
						$total_cnt += $row['Quantity'];
					}
					if ($stmt->rowCount() == 0) {
						$str .= '<tr><td colspan=2 class="TAC">В заказе нет товаров</td></tr>';
					}
					$str .= '</tbody>';
				}
				if ($cnt == 3) {
					foreach ($rowset as $row) {
						$str .= '<thead>
									<tr><th colspan=1>Общее кол-во:</th><th class="TAR">' . $total_cnt . '</th></tr>
								 </thead>';
//									<tr><th colspan=1>Сумма скидки:</th><th class="TAR">' . $row['SumDiscount'] . '</th></tr>
//									<tr><th colspan=1>Сумма заказа:</th><th class="TAR">' . $row['Sum'] . '</th></tr>
					}
					$str .= '</table>';
				}
				$cnt++;
			} while ($stmt->nextRowset());
		}
		$response->html = $str;
//Fn::debugToLog("resp", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}

//event
	public function event_info_full() {
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
//Fn::paramToLog();
		$response = new stdClass();
		$response->success = false;
		$response->message = "";
		$response->html = "";

		if ($docid == '') $docid = $_SESSION['CurrentDocID'];
		if ($docid == '') $docid = 0;
		if ($action == 'event_info') $doctype = 'event';
		if ($view) $notes = 'view';

		$param = "DocID=$docid&UserID=". $_SESSION['UserID'];
		$stmt = $this->db->prepare("call pr_doc_2017(:action, @_id, :_param)");
		$stmt->bindParam(":action", $action);
		$stmt->bindParam(":_param", $param);
// вызов хранимой процедуры
		$stmt->execute();
		if (!Fn::checkErrorMySQLstmt($stmt)) {
			$ar = $stmt->errorInfo();
			$response->success = false;
			$response->message = "Ошибка при получении информации о документе!";
		} else {
			$cnt = 1;
			$str = '';
			do {
				$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
				$response->success = true;
				if ($cnt == 1) {
					foreach ($rowset as $row) {
						$response->clientid = $row['ClientID'];
						$response->partnerid = $row['PartnerID'];
						$response->period = $row['Period'];
						$str .= '
								 <input id="docid" type="hidden" value="' . $row['DocID'] . '"/>';
						if (!$view) {
							$str .= '
								 <div class="row">
									<div id="div_doc_buttons" class = "col-md-12 col-xs-12 TAL hidden-print">';
							if ($action != 'timesheet_info')
								$str .= '<button id="good_add"	type="button" class="btn btn-primary	btn-sm minw150 mb5"><span class="glyphicon glyphicon-plus mr5"></span>Добавить товар</button>';
							$str .= '	
<button id="delete"		type="button" class="btn btn-danger		btn-sm minw150 mb5"><span class="glyphicon glyphicon-trash mr5"></span>Удалить документ</button>
<button id="doc_add"	type="button" class="btn btn-lilac		btn-sm minw150 mb5"><span class="glyphicon glyphicon-plus		mr5"></span>Новый документ</button>
<button id="print"		type="button" class="btn btn-info		btn-sm minw150 mb5"><span class="glyphicon glyphicon-print mr5"></span>Печать документа</button>
<button id="state"		type="button" class="btn btn-success	btn-sm minw150 mb5" title="Провести документ?"><span class="glyphicon glyphicon-ok mr5"></span>Провести</button>
<button					type="button" class="btn btn-link btn-sm minw150 mb5" disabled >Автор: ' . $row['UserName'] . '</button>
									</div>
								 </div>';
						}
						$str .= '
								 <div class="row">
									<div class = "col-md-12 col-xs-12 ' . (($action == 'timesheet_info') ? 'hidden-print' : '') . '">
										<div class = "floatL">
											<div class="input-group input-group-sm w300">
											   <span class = "input-group-addon w130">Документ №</span>
											   <span class = "input-group-addon form-control TAC">' . $row['DocID'] . '</span>
											   <span class = "input-group-addon w32"></span>
											</div>
											<div class="input-group input-group-sm w300">
											   <span class = "input-group-addon w130">Статус:</span>
											   <span class = "input-group-addon form-control TAC">' . $row['State'] . '</span>
											   <span class = "input-group-addon w32"></span>
											</div>
										</div>
										<div class="floatL ml5">&nbsp</div>
									';
						if (!$view) {
							$str .= '
										<div class="floatL">
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Магазин:</span>
												<div id="select_companyID" class="w210"></div>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							if ($action == 'timesheet_info')
								$str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Период расчета:</span>
												<div id="select_periodID" class="w210"></div>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							if ($action == 'receipt_info' || $action == 'difference_info')
								$str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Контрагент:</span>
												<div id="select_partnerID" class="w210"></div>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							if ($action == 'inventory_info')
								$str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w110">Тип документа:</span>
												<span class = "input-group-addon form-control w210 TAС">' . (($row['TypeDoc'] == 0) ? 'обычный' : 'сводный') . '</span>
												<span class = "input-group-addon w30"></span>
										   </div>
										   ';
							$str .= '	</div>';
						} else {
							$str .= '
										<div class="floatL">
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w80">Магазин:</span>
												<span class = "input-group-addon form-control w230 TAL">' . $row['Name'] . '</span>
												<span class = "input-group-addon w40"></span>
										   </div>
										   ';
							if ($action == 'receipt_info' || $action == 'difference_info')
								$str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w80">Контрагент:</span>
												<span class = "input-group-addon form-control w230 TAL">' . $row['PartnerName'] . '</span>
												<span class = "input-group-addon w40"></span>
										   </div>
										   ';
							if ($action == 'inventory_info')
								$str .= '
										   <div class="input-group input-group-sm w350">
												<span class = "input-group-addon w80">Тип документа:</span>
												<span class = "input-group-addon form-control w230 TAС">' . (($row['TypeDoc'] == 0) ? 'обычный' : 'сводный') . '</span>
												<span class = "input-group-addon w40"></span>
										   </div>
										   ';
							$str .= '	</div>';
						}
						$str .= '
										<div class="floatL ml5">&nbsp</div>
										<div class="floatL">
										   <div class="input-group input-group-sm w450">
											  <span class = "input-group-addon w100">Примечание:</span>
											  <input type = "text" class = "form-control" ' . ((!$view) ? '' : 'disabled') . ' autofocus value = "' . $row['Notes'] . '" onchange="good_edit(\'' . $doctype . '_edit_notes\',this,0,0,0,0,0,$(this).val());">
											  <span class = "input-group-addon w32"></span>
										   </div>
										   ';
						if ($action == 'timesheet_info')
							$str .= '
										   <div class="input-group input-group-sm w450">
												<span class="input-group-btn w50p"><a id="doc_fill" class="btn btn-default w100p" type="button">Заполнить документ</a></span>
												<span class="input-group-btn w50p"><a href="javascript:doc_info()" class="btn btn-default w100p" type="button">Пересчитать итоги</a></span>
										   </div>
										   ';
						//<img class="img-rounded h20 m0" src="../../images/save-as.png">
						if ($action == 'receipt_info' || $action == 'difference_info')
							$str .= '
										   <div class="input-group input-group-sm w450">
											  <span class = "input-group-addon w100">№ док. пост.:</span>
											  <input type = "text" class = "form-control" ' . ((!$view) ? '' : 'disabled') . ' autofocus value = "' . $row['1CID'] . '" onchange="good_edit(\'' . $doctype . '_edit_invoice\',this,0,0,0,0,0,0,0,0,0,$(this).val());">
											  <span class = "input-group-addon w32"></span>
										   </div>
										   ';
						$str .= '
										</div>
									</div>
								 </div>
								 ';
					}
				}
				if ($cnt == 2) {
					if ($action == 'timesheet_info') {
						if ($response->period != '') { //$response->period = '2017-01';
							$dt1 = DateTime::createFromFormat('Y?m?d', $response->period . '-01');
							$dateStart = $dt1->format('Ymd');
							$dt2 = DateTime::createFromFormat('Y?m?d', $response->period . '-01')->modify('+1 month')->modify('-1 day');
							$dateStop = $dt2->format('Ymd');
						} else {
							$dt1 = new DateTime();
							$dateStart = $dt1->format('Ymd');
							$dt2 = new DateTime();
							$dateStop = $dt2->format('Ymd');
						}
						$data = array();
						foreach ($rowset as $row) {
							$data[$row['SellerID']]['SellerID'] = $row['SellerID'];
							$data[$row['SellerID']]['SellerName'] = $row['Name'];
							$data[$row['SellerID']]['Post'] = $row['Post'];
							$data[$row['SellerID']][$row['DT']] = $row['Value'];
						}
						$str .= '<div class="panel panel-default mt10 mr5">';
						$str .= '<table id="table_doc" class="table table-striped table-bordered font12 minw400" cellspacing="0"  width="100%">';
						$str .= '<thead><tr style="height0:100px;">
										<th rowspan=2 class="w50 center">№</th>
										<th rowspan=2 class="w150 center">Ф.И.О.</th>
										<th rowspan=2 class="w150 center">Должность</th>
										<th rowspan=2 class="w20 center trans90">Ставка</th>
										';
						$str .= '		<th rowspan=1 colspan=' . $dt2->format('d') . '>' . Fn::rusm($response->period) . '</th>';
						$str .= '		<th rowspan=2 class="w50 center">Итого часов</th>
										<th rowspan=2 class="w50 center">Отп.</th>
										<th rowspan=2 class="w50 center">Больн.</th>
										<th rowspan=2 class="w50 center">Раб. дни</th>
										';
						$str .= '		</tr><tr>';
						for ($dt = clone $dt1; $dt <= $dt2; $dt->modify('+1 day')) {
							$str .= '		<th rowspan=1 class="center trans90 wwn" style="">' . $dt->format('d') . '</th>';
						}
						$str .= '		</tr>';
						$str .= '	 </thead><tbody>';
						$nn = 1;
						foreach ($data as $d) {
							$str .= '<tr>
										<td class="TAC">' . $nn . '</td>
										<td class="TAL wwn">' . $d['SellerName'] . '</td>
										<td class="TAL wwn">' . $d['Post'] . '</td>
										<td class="TAC">' . 1 . '</td>
									';
							$tHours = 0;
							$tOtpus = 0;
							$tBoln = 0;
							$tWorkDay = 0;
							$maxWorkDay = 5;
							for ($dt = clone $dt1; $dt <= $dt2; $dt->modify('+1 day')) {
								if ($d[$dt->format('Y-m-d')] == 'О') {
									$tOtpus++;
									$class = 'holiday';
									$value = 'О';
									$maxWorkDay = 5;
								} else if ($d[$dt->format('Y-m-d')] == 'Б') {
									$tBoln++;
									$class = 'hospital';
									$value = 'Б';
									$maxWorkDay = 5;
								} else if ($d[$dt->format('Y-m-d')] == 'Х') {
									$tBoln++;
									$class = 'noworking';
									$value = 'Х';
									$maxWorkDay = 5;
								} else if ($d[$dt->format('Y-m-d')] > 0) {
									$tWorkDay++;
									$tHours = $tHours + $d[$dt->format('Y-m-d')];
									$class = 'workday';
									if ($maxWorkDay == 0) {
										$class = 'orangeday';
										$maxWorkDay = 5;
									} else {
										$maxWorkDay --;
									}
									$value = $d[$dt->format('Y-m-d')];
								} else {
									$class = 'freeday';
									$value = 'В';
									$maxWorkDay = 5;
								}
								$str .= '		<td class="TAC ' . $class . '" onclick="javascript:doc_cell(\'' . $d['SellerID'] . '_' . $dt->format('Y-m-d') . '\');" id=' . $d['SellerID'] . '_' . $dt->format('Y-m-d') . '>' . $value . '</th>';
							}
							$nn++;
							$str .= '		<td class="TAC">' . $tHours . '</td>
											<td class="TAC">' . $tOtpus . '</td>
											<td class="TAC">' . $tBoln . '</td>
											<td class="TAC">' . $tWorkDay . '</td>
										';
						}
						$str .= '</tr>';
						$str .= '</tbody>';
						$str .= '</table></div>';
						$str .= '<div class="panel panel-default mt10 mr5 w300">';
						$str .= '<table id="table_doc" class="table table-striped table-bordered font12 minw200" cellspacing="0"  width="100%">';
						$str .= '<thead><tr style="height:40px;">
										<th colspan=2 class="w150 center">Условные обозначения</th>
										';
						$str .= '</tr>';
						$str .= '	 </thead><tbody>';
						$str .= '		<tr><td class="TAC">Выходной</td><td class="freeday">В</td></tr>';
						$str .= '		<tr><td class="TAC">Отпуск</td><td class="holiday">О</td></tr>';
						$str .= '		<tr><td class="TAC">Больничный</td><td class="hospital">Б</td></tr>';
						$str .= '		<tr><td class="TAC">Рабочий день</td><td class="workday">8</td></tr>';
						$str .= '		<tr><td class="TAC">Не числился</td><td class="noworkday">Х</td></tr>';
						$str .= '</tbody>';
						$str .= '</table></div>';
					} else {
						$total_qty = 0;
						$col_cnt = 4;
						$str .= '<div class="panel panel-default mt10 mr5">';
						$str .= '<table id="table_doc" class="table table-striped table-bordered font12 minw400" cellspacing="0"  width="100%">';
						$str .= '<thead><tr>
										<th class="w50 center">Артикул</th>
										<th class="w150 center">Название</th>';
						if ($action != 'receipt_info')
							$str .= '	<th class="w100  center">Инфо</th>';
						$str .= '		<th class="w40  center">Кол-во</th>';
						if ($action == 'receipt_info') {
							$str .= '		<th class="w40  center">Цена розн.</th>';
							$str .= '		<th class="w40  center">Сумма</th>';
						}
						$str .= '	 </thead><tbody>';
						foreach ($rowset as $row) {
							$total_qty += $row['Quantity'];
							$str .= '<tr>
										<td class="TAL">' . $row['Article'] . '</td>
										<td class="TAL">' . $row['Name'] . '</td>';
							if (!$view) {
								if ($action != 'receipt_info')
									$str .= '<td class="TAL">
											<input type="text" class="TAL editable inline-edit-cell" style="line-height:17px;width:100%;" min=0 onchange="good_edit(\'' . $doctype . '_edit_good_info\',this,' . $row['GoodID'] . ',null,null,$(this).val());" value="' . $row['Info'] . '">
										</td>';
								$str .= '	<td class="TAC">
											<input type="number" class="TAR editable inline-edit-cell" style="line-height:17px;width:60%;min-width:40px;" onchange="good_edit(\'' . $doctype . '_edit\',this,' . $row['GoodID'] . ',null,$(this).val(),null);" value="' . $row['Quantity'] . '">
											<span id="qty" class="hidden">' . $row['Quantity'] . '</span>
											<span class="ml5 mr5 glyphicon glyphicon-remove hidden-print" onclick="good_edit(\'' . $doctype . '_edit\',$(this).prev(),' . $row['GoodID'] . ',null,0);"></span>
										</td>';
								if ($action == 'receipt_info') {
									$str .= '<th class="w40  center">' . $row['Price'] . '</th>';
									$str .= '<th class="w40  center">' . $row['Sum'] . '</th>';
								}
								$str .= '</tr>';
							} else {
								$str .= '	<td class = "w40  center">' . $row['Info'] . '</td>';
								$str .= '	<td class = "w40  center">' . $row['Quantity'] . '</td>';
							}
						}
						if ($stmt->rowCount() == 0) {
							$str .= '<tr><td colspan=' . $col_cnt . ' class="TAC">В документе нет товаров</td></tr>';
						}
						$str .= '</tbody>';
					}
				}
				if ($cnt == 3) {
					if ($action == 'timesheet_info') {
						
					} else {
						foreach ($rowset as $row) {
							if ($action == 'receipt_info') {
								$str .= '<tfoot>
										<tr><th colspan=' . ($col_cnt - 2) . '>Всего кол-во в документе:</th>
										<th class="TAC">' . $total_qty . ' ед.</th>
										<th colspan=2 class="TAC"></th></tr>
									 </tfoot>';
							} else if ($action == 'package_info') {
								$str .= '<tfoot>
										<tr><th colspan=' . ($col_cnt - 1) . '>Всего кол-во в документе:</th>
										<th class="TAC">' . $total_qty . ' ед.</th></tr>
										<th class="TAC"></th></tr>
									 </tfoot>';
							} else {
								$str .= '<tfoot>
										<tr><th colspan=' . ($col_cnt - 1) . '>Всего кол-во в документе:</th><th class="TAC">' . $total_qty . ' ед.</th></tr>
									 </tfoot>';
							}
						}
						$str .= '</table></div>';
					}
				}
				$cnt++;
			} while ($stmt->nextRowset());
		}
		$response->html = $str;
//Fn::debugToLog("resp", json_encode($response));
		header("Content-type: application/json;charset=utf-8");
		echo json_encode($response);
	}

//test grid for print result
	function printResultSet(&$rowset, $i) {
		echo "Result set $i:<br>";
		foreach ($rowset as $row) {
			foreach ($row as $col) {
				echo $col . " | ";
			}
			echo '<br>';
		}
		echo '<br>';
	}
//
	private function echo_response($stmt) {
		$response = new stdClass();
		$response->new_id = 0;
		$response->success = Fn::checkErrorMySQLstmt($stmt);
		$response->sql_message = "";
		if ($response->success != true) {
			$response->message = 'Возникла ошибка при внесении информации!<br><br>Сообщите разработчику!';
			if (strlen($response->sql_message) == 0)
				$response->sql_message = $response->message;
			echo json_encode($response);
			return;
		}
		//$response->success = false;
//	do {
		$rowset = $stmt->fetchAll(PDO::FETCH_BOTH);
//		$response->rowset = $rowset;
//Fn::debugToLog("s", "1");
		foreach ($rowset as $row) {
//Fn::debugToLog("s", "2");
//Fn::debugToLog("s", json_encode($row));
			if ($response->success)
				$response->success = $row[0];
				$response->new_id = $row[1];
				$response->sql_message = $row[2];
			break;
		}
//	} while ($stmt->nextRowset());

		if ($response->success == true) {
			$response->message = 'Информация успешно сохранена!';
			if (strlen($response->sql_message) == 0)
				$response->sql_message = $response->message;
			echo json_encode($response);
		} else {
			$response->message = 'Вы ничего не изменили!';
			if (strlen($response->sql_message) == 0)
				$response->sql_message = $response->message;
			echo json_encode($response);
		}
		return;
	}
}
?>
