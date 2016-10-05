<!-- Fixed navbar -->
<nav class="navbar navbar-<?php echo $_SESSION['nav_style'];?> navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
			<a class="navbar-brand" href="..">
				<img class="img-rounded border1 floatL h30 mt10" src="../../img/logo.png">
				<div class="caption floatL pl5">
				<?php echo $_SESSION['company']; ?>
				</div>
			</a>
        </div>
        <div class="navbar-collapse collapse">
<?php 
if ($_SESSION['access'] and $_SESSION['AccessLevel'] > 0) {
	//$name = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_FILENAME);
	$controller = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME);
//Fn::debugToLog("url", $_SERVER['REQUEST_URI']);
//$routes = explode('/', $_SERVER['REQUEST_URI']);
//Fn::debugToLog("rout", json_encode($routes));
//Fn::debugToLog('menu', $name.'	'.  $controller);
//Fn::debugToLog('REDIRECT_URL', $_SERVER['REDIRECT_URL']);
	$cnn = new Cnn();
	$rowset = $cnn->menu_list();
	$cur_level = 1;
	echo '<ul class = "nav navbar-nav">';
	foreach ($rowset as $row) {
		if ($_SESSION['AccessLevel'] < $row['AccessLevel'])
			continue;
		if ($cur_level > $row['Level'])
			echo '      </ul></li>';
//Fn::debugToLog("row", json_encode($row));
//		$pos = strpos($row['Action'],'/'.$controller);
		$active_menu = '';
//		if ($pos !== FALSE) $active_menu = 'active';
//Fn::debugToLog("row", 'action:'.$row['Action'].  '	'.$pos.'	'.  $active_menu);
		if ($row['ParentID'] == null) {
			echo '   <li class = "menu-item dropdown ' . $active_menu . '">';
			echo '      <a href = "#" class = "dropdown-toggle" data-toggle = "dropdown">' . $row['Name'] . '<b class = "caret"></b></a>';
			echo '      <ul class="dropdown-menu">';
		} else {
			if ($row['Name'] == null && $row['Action'] == null) {
				echo '         <li class="divider"></li>';
			} else {
				echo '         <li><a href = "' . $row['Action'] . '">' . $row['Name'] . '</a></li>';
			}
		}
		$cur_level = $row['Level'];
	}
	echo '      </ul></li></ul>';
}
	echo '<ul class="nav navbar-nav navbar-right">';
	if ($_SESSION['access']){
		if ($_SESSION['AccessLevel'] > 0) {
			echo '<li><a id="a_name_cabinet" href="/register/user_cabinet">';
			echo $_SESSION['UserName'] . '<br />' . $_SESSION['UserPost'];
			echo '</a></li>';
		}
		echo '<li class="active"><a href="/login/logout">Выход</a></li>';
	} else {
		echo '<li class="active"><a href="/login">Вход</a></li>';
	}
    echo '</ul>';
?>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<?php 
	if ($_SESSION['access'] && $_SESSION['AccessLevel'] == 0) {
		echo "<h4 class='center list-group-item list-group-item-danger'>"
		. "ВНИМАНИЕ!<br><small>Ваш аккаунт имеет ограниченный доступ!<br>"
		. "Для получения доступа к функциям системы,<br>"
		. "обратитесь к администратору системы: <a href='mailto:" . $_SESSION['adminEmail'] . "'>" . $_SESSION['adminEmail'] . "</a></small></h4>";
	}
//echo phpinfo();
?>