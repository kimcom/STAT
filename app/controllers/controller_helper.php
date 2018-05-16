<?php
class Controller_Helper extends Controller {
	function action_control() {
		$this->view->generate('view_helper_controls.php', 'view_template.php');
	}
	function action_diagram1() {
		$this->view->generate('view_helper_diagram1.php', 'view_template.php');
	}
	function action_diagram2() {
		$this->view->generate('view_helper_diagram2.php', 'view_template.php');
	}
	function action_diagram3() {
		$this->view->generate('view_helper_diagram3.php', 'view_template.php');
	}
	function action_3D() {
		$this->view->generate('view_helper_3d.php', 'view_template.php');
	}
	function action_info() {
		$this->view->generate('view_project_attr.php', 'view_template.php');
	}
}
?>
