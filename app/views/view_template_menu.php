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
	$name = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_FILENAME);
	$controller = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME);
//	Fn::debugToLog('menu', $name.'	'.  $controller);
	if ($name == "category1" ||
		$name == "category2" ||
		$name == "category3" ||
		$name == "cat_partner3" ||
		$controller == "/category") {
		$active_menu1 = 'active';
	}else if (
		$name == "goods" || 
		$name == "barcodes" || 
		$name == "without_barcodes" || 
		$name == "barcode_verify" ||
		$name == "promo_list" ||
		$name == "points" ||
		$name == "sellers" ||
		$name == "discountCards" ||
		$name == "user_list" || 
		$name == "promo_list" ||
		$controller == "/goods" ||
		$controller == "/lists" ||
		substr($name,0,13) == "promo_control"){
		$active_menu2 = 'active';
	}else if (
		$controller == "/reports"){
		$active_menu5 = 'active';
	}else if (
		$controller == "/reports_fin"){
		$active_menu9 = 'active';
	}else if (
		$controller == "/documents"){
		$active_menu10 = 'active';
	}else if (
		$controller == "/project") {
		$active_menu6 = 'active';
	}else if (
		$controller == "/helper") {
		$active_menu8 = 'active';
	} else if (
		$controller == "/documents") {
		$active_menu10 = 'active';
	}else if (
		$controller == "/task") {
		$active_menu7 = 'active';
	}  else if (
        $controller == "/register") {
        $active_menu11 = 'active';
    }
//$_SESSION['AccessLevel'] = 2000;
	$cnn = new Cnn();
	$rowset = $cnn->menu_list();
	$cur_level = 1;
	echo '<ul class = "nav navbar-nav">';
	foreach ($rowset as $row) {
		if ($_SESSION['AccessLevel'] < $row['AccessLevel'])
			continue;
		if ($cur_level > $row['Level'])
			echo '      </ul></li>';
		if ($row['ParentID'] == null) {
			echo '   <li class = "menu-item dropdown ' . $active_menu1000 . '">';
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
	echo '<ul class="nav navbar-nav navbar-right">';
	if ($_SESSION['access']){
		echo '<li><a id="a_name_cabinet" href="/register/user_cabinet">';
		echo $_SESSION['UserName'] . '<br />' . $_SESSION['UserPost'];
		echo '</a></li>';
		echo '<li class="active"><a href="/login/logout">Выход</a></li>';
	} else {
		echo '<li class="active"><a href="/login">Вход</a></li>';
	}
    echo '</ul>';
}
?>
        </div><!--/.nav-collapse -->
    </div>
</nav>
