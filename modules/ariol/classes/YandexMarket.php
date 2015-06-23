<?php
class YandexMarket {
	/**
	* Валидный yml генератор для Яндекс Маркет
	* @version 0.3  Date: 02.02.13
	* @author: fStrange
	* @url: http://fstrange.ru
	*/

	protected
	  $name = '',
	  $company = '',
	  $url = '',
	  $date = '',
	  $rootElem = '',
	  $shopElem = '',
	  $aOffer = array(),
	  $aCurr = array(), //валюты
	  $aCat = array(); //категории

	public function __construct($name = '', $company = '', $url = '') {
	$this->name = self::filterElem($name);
	$this->company = self::filterElem($company);
	$this->url = $url;
	$this->date = date("Y-m-d H:i");
	$this->rootElem = "<?xml version='1.0' encoding='windows-1251'?>
			<!DOCTYPE yml_catalog SYSTEM 'shops.dtd'>
	<yml_catalog date='{$this->date}'>\r\n%s\r\n</yml_catalog>";
	$this->shopElem =
		"<shop>\r\n<name>{$this->name}</name>
		<company>$this->company</company>
		<url>{$this->url}</url>
		<currencies>\r\n%s\r\n</currencies>
		<categories>\r\n%s\r\n</categories>
		<offers>\r\n%s\r\n</offers>
		</shop>\r\n";

	}

	/*
	* setCurr('RUR', 1);
	* setCurr('USD', 'CBRF');
	*/
	public function addCurr($id, $rate, $plus = null) {
	$this->aCurr[] = "<currency ".self::setAttr(array('id'=>$id, 'rate' => $rate, 'plus'=>$plus)). " />";
	}
	
	public function addCat($sName, $id, $parentId=null) {
	$tag = "<category id=\"$id\"";
	if ($parentId) {
		$tag .= " parentId=\"$parentId\"";
	}
	$tag .= ">".self::filterElem($sName)."</category>";
	
	$this->aCat[] = $tag;
	}

	public function addOffer($sOffer){
	$this->aOffer[] = $sOffer;
	}

	public function save() {
	$this->shopElem = sprintf($this->shopElem, implode("\n", $this->aCurr), implode("\n", $this->aCat), implode("\n", $this->aOffer));
	return sprintf($this->rootElem, $this->shopElem);
	}

	public function setOffer($id, $sType){

	}

	public static function setXmlNode($k, $v){
	return "<$k>$v</$k>";
	}
	public static function setAttr($a) {
	$s = '';

	//удалить пустые элементы массива
	$array_empty = array(null);
	$a = array_diff($a, $array_empty);

	foreach ($a as $k => $v) {
	  $s .= "$k=\"$v\" ";
	}
	return trim($s);
	}

	public static function filterElem($s) {
		return str_replace(array('"', '&', '>', '<', "'"), array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;'), $s);
	}
}