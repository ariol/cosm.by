<?php
/*
	* $offer = new OfferYmt($id);
	* $offer->setUrl('http://ghghg.ghghg.ru/url/');
	* $offer->setRequired($price, $currencyId, $categoryId, $vendor, $model);
	*/
	class OfferYmt {
	//http://partner.market.yandex.ru/legal/tt/#id1164057815703
	//offer DTD type
	protected
	$sDtdElem =  'url?, buyurl?, price, wprice?, currencyId, xCategory?, categoryId+,
				picture?, store?, pickup?, delivery?, deliveryIncluded?,
				local_delivery_cost?, orderingTime,
				typePrefix?, vendor, vendorCode?, model,
				aliases?, additional*, description?, sales_notes?, promo?,
				manufacturer_warranty?, country_of_origin?, downloadable?, adult?,
				barcode*, param*',
	$aOffer = array(),
	$aElems = array(),
	$sOfferElem = '';

	public function __construct($id, $available=1, $bid=null, $cid=null) {
	$a = array('available'=>$available? 'true' : 'false' ,'bid'=>$bid, 'cid'=>$cid);
	$this->sOfferElem = "<offer id=\"$id\" ";
	$this->sOfferElem .= YandexMarket::setAttr($a);
	$this->sOfferElem .= ">\r\n%s\r\n</offer>";


	$this->aElems = array_keys(explode(',', $this->sDtdElem));
	$this->aElems = array_map(array($this, '_cbTrim'), $this->aElems);
	/*
	 * сформировали массив элементов в правильной последовательности описанной в DTD
	 * для Яндекс Маркета это важно!!!!
	 */
	$this->aElems = array_fill_keys($this->aElems, null);
	}

	public function setRequired($price, $currencyId, $categoryId, $name, $vendor, $picture = null){
		$this->setElem('price', $price);
		$this->setElem('currencyId', $currencyId);
		$this->setElem('categoryId', $categoryId);
		if ($picture) {
			$this->setElem('picture', $picture);
		}
		$this->setElem('name', $name);
		$this->setElem('vendor', $vendor);
	}

	public function setUrl($s){ $this->aElems['url'] = $s;}

	public function save(){
		$s = '';
		foreach($this->aElems as $k=>$v) if(!is_null($v)) $s .= YandexMarket::setXmlNode($k,$v)."\n";

		$this->sOfferElem = sprintf($this->sOfferElem, $s);
		return $this->sOfferElem;
	}

	public function setElem($sName, $sTitle){
		$this->aElems[$sName] = YandexMarket::filterElem($sTitle);
	}
	
	private function _cbTrim($s) { return trim($s, "+?* \r\n\t");}
}