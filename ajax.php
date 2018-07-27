<?
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

use Bitrix\Main,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

if (isset($_REQUEST['admin']) && is_string($_REQUEST['admin']) && $_REQUEST['admin'] == 'Y')
	define('ADMIN_SECTION', true);
if (isset($_REQUEST['site']) && !empty($_REQUEST['site']))
{
	if (!is_string($_REQUEST['site']))
		die();
	if (preg_match('/^[a-z0-9_]{2}$/i', $_REQUEST['site']))
		define('SITE_ID', $_REQUEST['site']);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
Loc::loadMessages(__FILE__);

global $APPLICATION;

if(!Loader::includeModule('iblock'))
{
	echo Loc::getMessage("BT_COMP_MLI_AJAX_ERR_MODULE_ABSENT");
	die();
}

CUtil::JSPostUnescape();

$iblockId = (isset($_REQUEST['IBLOCK_ID']) && is_string($_REQUEST['IBLOCK_ID']) ? (int)$_REQUEST['IBLOCK_ID'] : 0);
$withoutIblock = (isset($_REQUEST['WITHOUT_IBLOCK']) && $_REQUEST['WITHOUT_IBLOCK'] == 'Y');
$bSection = (isset($_REQUEST['TYPE']) && $_REQUEST['TYPE'] == 'SECTION');
$resultCount = (isset($_REQUEST['RESULT_COUNT']) && is_string($_REQUEST['RESULT_COUNT']) ? (int)$_REQUEST['RESULT_COUNT'] : 0);
if ($resultCount <= 0)
	$resultCount = 20;

	$filter['IBLOCK_ID'] = $iblockId;

$strBanSym = trim($_REQUEST['BAN_SYM']);
$arBanSym = str_split($strBanSym, 1);
$strRepSym = trim($_REQUEST['REP_SYM']);
$arRepSym = array_fill(0, sizeof($arBanSym), $strRepSym);


	$APPLICATION->RestartBuffer();

	$arResult = array();
	$search = trim($_REQUEST['search']);


	$filter['%NAME'] = $search;
	if ($bSection)
		$dbRes = CIBlockSection::GetList(array(), $filter, false, array("ID", "NAME"), array("nTopCount" => $resultCount));
	else
		$dbRes = CIBlockElement::GetList(array(), $filter, false, array("nTopCount" => $resultCount), array("ID", "NAME"));

	while($arRes = $dbRes->Fetch())
	{
		$arResult[] = array(
			'ID' => $arRes['ID'],
			'NAME' => str_replace($arBanSym, $arRepSym, $arRes['NAME']),
		);
	}

	header('Content-Type: application/json');
	//echo Main\Web\Json::encode($arResult);
	echo json_encode($arResult);
	die();
