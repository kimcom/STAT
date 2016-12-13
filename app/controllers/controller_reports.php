<?php
class Controller_Reports extends Controller {
	function action_report1() {
		$this->view->generate('view_reports_1.php', 'view_template.php');
	}
	function action_report4() {
		$this->view->generate('view_reports_4.php', 'view_template.php');
	}
	function action_report5() {
		$this->view->generate('view_reports_5.php', 'view_template.php');
	}
	function action_report42() {
		$this->view->generate('view_reports_42.php', 'view_template.php');
	}
	function action_report7() {
		$this->view->generate('view_reports_7.php', 'view_template.php');
	}
	function action_report8() {
		$this->view->generate('view_reports_8.php', 'view_template.php');
	}
	function action_report9() {
		unset($_SESSION['report9_setting']);
		$this->view->generate('view_reports_9.php', 'view_template.php');
	}
	function action_report9_start() {
		$this->view->generate('view_reports_9.php', 'view_template.php');
	}
	function action_report9_setting() {
		$_SESSION['report9_setting'] = urldecode($_SERVER['QUERY_STRING']);
	}
	function action_report10() {
		$this->view->generate('view_reports_10.php', 'view_template.php');
	}
	function action_report11() {
		$this->view->generate('view_reports_11.php', 'view_template.php');
	}
	function action_report12() {
		$this->view->generate('view_reports_12.php', 'view_template.php');
	}

	function action_report1_data() {
		$this->model = new Model_Reports();
		echo $this->model->get_report1_data();
	}
	function action_report2_data() {
		$this->model = new Model_Reports();
		echo $this->model->get_report2_data();
	}
	function action_report3_data() {
		$this->model = new Model_Reports();
		echo $this->model->get_report2_data();
	}

	function action_report4_data() {
		$cnn = new Cnn();
		return $cnn->get_report4_data();
	}
	function action_report42_data() {
		$cnn = new Cnn();
		return $cnn->get_report4_data();
	}
	function action_report5_data() {
		$this->model = new Model_Reports();
		echo $this->model->get_report5_data();
	}
	function action_report6_data() {
		$cnn = new Cnn();
		return $cnn->get_report6_data();
	}
	function action_report7_data() {
		$this->model = new Model_Reports();
		echo $this->model->get_report7_data();
	}
	function action_report8_data() {
		$cnn = new Cnn();
		return $cnn->get_report8_data();
	}
	function action_report9_data() {
		$cnn = new Cnn();
		return $cnn->get_report9_data();
	}
	function action_report10_data() {
		$cnn = new Cnn();
		return $cnn->get_report10_data();
	}
	function action_report11_data() {
		$cnn = new Cnn();
		return $cnn->get_report11_data();
	}
	function action_report12_data() {
		$cnn = new Cnn();
		return $cnn->get_report12_data();
	}

	function action_jqgrid3() {
		$this->model = new Model_Reports();
		echo $this->model->get_jqgrid3_data();
	}
}
?>
