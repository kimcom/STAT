<?php
class Controller_Documents extends Controller {
	function action_sale() {
		$this->view->generate('view_doc_sale.php', 'view_template.php');
	}
    function action_package_list() {
		$this->view->generate('view_doc_package.php', 'view_template.php');
	}
    function action_receipt_list() {
		$this->view->generate('view_doc_receipt.php', 'view_template.php');
	}
    function action_cancel_list() {
		$this->view->generate('view_doc_cancel.php', 'view_template.php');
	}
    function action_difference_list() {
		$this->view->generate('view_doc_difference.php', 'view_template.php');
	}
	function action_check_list() {
		$this->view->generate('view_doc_check.php', 'view_template.php');
	}
	function action_checkContent_list() {
		$this->view->generate('view_doc_check.php', 'view_template.php');
	}
	function action_request() {
		$this->view->generate('view_doc_request.php', 'view_template.php');
	}
    
}

