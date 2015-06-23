var cms = new Object();
cms.bModal = false;
cms.szPopupClass = '';
cms.szPopupId = '';

cms.tabSetTitle = function ($tabId,$newTitle) {
	var tab = document.getElementById("title_"+$tabId);
	if (tab == null)
		return
	tab.innerHTML = $newTitle;
}

cms.tabSetCurrent = function ($sender,$tabId) {
	if ($sender != null)
		if ($sender.id == "title_".$tabId)
			return ;
	var tabs=document.getElementById("tabCntTitle").childNodes;
	var cls = "tabBeforeCurrent";
	for(var i=0;i<tabs.length;i++){
		if (tabs[i].tagName == "DIV") {
			if (tabs[i].id == "title_"+$tabId) {
				tabs[i].className = "tabCurrent";
				cls = "tabAfterCurrent";
			} else {
				tabs[i].className = cls;
			}
		}
	}
	var contents=document.getElementById("tabCntContent").childNodes;
	for(var i=0;i<contents.length;i++){
		if (contents[i].tagName == "DIV") {
			if (contents[i].id == $tabId) {
				contents[i].style.display = "block";
			} else {
				contents[i].style.display = "none";
			}
		}
	}

}
cms.listRow = function ($oTable) {
	$newRow = $oTable.insertRow(-1);
	return $newRow;
}
cms.fullRow = function (oTable,content) {
	var oNewRow,oNewCell;
	oNewRow = oTable.insertRow(-1);
	oNewCell = oNewRow.insertCell(-1);
	oNewCell.colSpan =100;
	oNewCell.innerHTML = content;


}

cms.tableHeader = function($oTable,$aHeader) {
	var $nKey,$oRow,$oCell;
	$oRow = this.listRow($oTable);
	for ($nKey in $aHeader )
	{
		this.listCell($oRow,$aHeader[$nKey]['width'],$aHeader[$nKey]['name'],true);
	}
	// формируем сплиттер
	$oRow = this.listRow($oTable);
	$oCell = $oRow.insertCell(-1);
	$oCell.className = 'HeaderSplitter';
	$oCell.colSpan = 100;
	$oCell.innerHTML = '<div></div>';
}

cms.listCell = function ($obj,width,content,is_bold) {
	var $newTD;
	$newTD = $obj.insertCell(-1);
	$newTD.width = width+ '%';
	if (is_bold)
	{
		$newTD.innerHTML = '<strong>' + content + '</strong>';
	} else {
		$newTD.innerHTML = content;
	}

}
cms.addButton = function ($id,$caption,$url) {

}
/**
*   @desc ��������� �������� �����
function closePopup()
{}
*/
cms.closePopup = function () {

	if (document.getElementById(this.szPopupId) == null)
		return
	cms.bModal = false;
	obj = document.getElementById(this.szPopupId);

	document.body.removeChild(document.getElementById('popupWindow'));
	obj.className = this.szPopupClass;
	this.szPopupId = '';
	this.szPopupClass = '';

	document.body.appendChild(obj);



}
cms.popup = function (id,title,width,height) {
	if (!width)
	{
		width = 400;
	}
	if (!height)
	{
		height = 300;
	}
	// �������� �������
	cms.bModal = true;
	var targetFrame = document.getElementById(id);
	if (!targetFrame)
	{
		return;
	}
	// ������������� ����� � ������
	this.szPopupId = id;
	this.szPopupClass = targetFrame.className;

	// ������������� HTML
	var szHTML;
	szHTML = '<table bgcolor="#EEEEEE" border=1 width="'+ width +'px" height=100% style="border-collapse:collapse"cellpadding=0 cellspacing=0><tr><td width="'+ (width - 10) + 'px">' + title + '</td><td width="10px" ><a href="#"id="popupWindowCloser" onclick="cms.closePopup()">[X]</a></td></tr>';
	szHTML += '<tr><td colspan="2" id="popupOutputCell" >'
	//szHTML = '<span style="background-color:white"> ' + title + ' </span><span style="position:absolute;left:'+ (width - 6) +'px;top:0px;text-align:right"></span><hr>' + "\r\n";
	szHTML += '</td></tr></table>';

	// ������� ������
	var popup = document.createElement('div');
	popup.id = 'popupWindow';
	popup.innerHTML = szHTML;
	// ����������� ����� � �������
	popup.style.position = 'absolute';
	popup.style.cssText = 'position:absolute;background-color:#EEEEEE;';
	popup.style.width = width + 'px';
	popup.style.height = height + 'px';
	popup.style.marginLeft =  (Math.round(-width) / 2) + 'px';
	popup.style.marginTop = (Math.round(-height) / 2) + 'px';
	popup.style.left = '50%';
	popup.style.top = '50%';
	popup.style.zIndex = 500;
	popup.style.visibility = 'visible';
	// �������

	document.body.appendChild(popup);
	// ���������� div
	obj = document.getElementById('popupOutputCell');

	targetFrame.className = targetFrame.className.replace('popupFrame','');
	obj.appendChild(targetFrame);
}