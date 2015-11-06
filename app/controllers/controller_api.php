<?php
class Controller_Api extends Controller {
	private $access = false;
	
	function __construct(){
		foreach ($_REQUEST as $arg => $val)
			${$arg} = $val;
		if ($auth==='bc3939d76f543f3efe46be026f4ddfdd') {
			$this->access = true;
			if (isset($goodid)) {
				if ($goodid > 500) $this->access = false;
			}
			if (isset($cardid)) {
				if (($cardid < 9800000220000 || $cardid > 9800000223000) && $cardid != 2020202000019) $this->access = false;
			}
		} else {return;}
	}

	function std2simplexml($object, $recursive = false) {
		$xml = new DOMDocument;
		$root = $xml->createElement('root');
		$xml->appendChild($root);

		Fn::debugToLog("object", json_encode($object));
		foreach ($object as $key => $child) {
			Fn::debugToLog("key", json_encode($key));
			Fn::debugToLog("child", json_encode($child));
			Fn::debugToLog("child is", is_object($child));
			if (is_object($child)) {
				$new_xml = std2simplexml($child, true);
				$new_xml = str_replace(array('', '', ''), '', $new_xml);
				$el = $xml->createElement($key, $new_xml);
			$root->appendChild($el);
			} else {
				//$el = $xml->createElement($key, $child);
			}
		}

		if (!$recursive) {
			$simple_xml = simplexml_load_string(html_entity_decode($xml->saveXml()));
			return $simple_xml;
		} else {
			return $xml->saveXml();
		}
	}

	function action_jqgrid3() {
		$cnn = new Cnn();
		return $cnn->get_jqgrid3();
	}

	function action_good_getinfo(){
		if (!$this->access) return;
		include "app/views/view_template_header_api.php";
		include 'app/views_bitrix/view_goods_info.php';
	}
	function action_good_getinfo2(){
		if (!$this->access) return;
		$cnn = new Cnn();
		$response = $cnn->good_info();
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}
	function action_good_getlist_data(){
		$cnn = new Cnni();
		return $cnn->get_goods_list();
	}
	function action_good_getlist(){
		if (!$this->access) return;
		include "app/views/view_template_header_api.php";
		include 'app/views_bitrix/view_goods_list.php';
	}
	function action_good_barcode(){
		$cnn = new Cnni();
		return $cnn->get_good_barcode_list();
	}
	
	function action_card_getlist() {
		if (!$this->access)	return;
		include "app/views/view_template_header_api.php";
		include 'app/views_bitrix/view_discountCard_list.php';
	}
	function action_card_getinfo() {
		if (!$this->access)	return;
		include "app/views/view_template_header_api.php";
		include 'app/views_bitrix/view_discountCard_attr.php';
	}
	function action_card_getinfo2() {
		if (!$this->access)	return;
		$cnn = new Cnn();
		$response = $cnn->discountcard_info();
		header("Content-type: application/json;charset=utf8");
		echo json_encode($response);
	}

}
?>
