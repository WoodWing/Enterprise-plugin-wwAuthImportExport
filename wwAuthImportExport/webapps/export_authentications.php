<?php

require_once dirname(__FILE__).'/../../../config.php';
require_once dirname(__FILE__).'/../../../configserver.php';

require_once BASEDIR.'/server/secure.php';
require_once BASEDIR.'/server/admin/global_inc.php';

require_once dirname(__FILE__).'/../config.php';

//
// 	check valid admin user
//
$ticket = checkSecure('publadmin');

//
// 	prepare environment 
//	
$dbh = DBDriverFactory::gen();
$dba = $dbh->tablename("authorizations");

//
//	check if brand id is specified, if not set to 0 to export all brands
//
$id = isset($_REQUEST['id'])  	? intval($_REQUEST['id'])  : false;

//
// 	- export authorizations -
//
export_authorizations($id);


//
//	export_authorizations
//
//	For all brands / the selected brand, list all authorization records 
//	and export them as a csv download with the following structure:
//
//	usergroup, brand, category, status, access profile \r
//

function export_authorizations($id) {
	
	require_once dirname(__FILE__).'/../wwAuthImportExport.class.php';
	
	$aie = new wwAuthImportExport();
	$context = $aie->getPublicationsContext();
	
	
	$authorizations = $aie->getAuthorizations($id);

	$name = DBSELECT;
	if ($id) $name .= "_" . $authorizations[0]['brand'];
			
	//	
	// start download	
	//
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$name."_authorizations.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	
	//
	//	set record and line delimiter
	//
	
	$del = DELIMITER;
	$eol = EOL;
	
	//
	// first line will be the header
	//
	$printheader=true;
	
	foreach ($authorizations as $row) {
		if ($printheader) {
			echo implode($del, array_keys($row));
			$printheader=false;
			echo $eol;
		}

		foreach ($row as $i=>$field) {
			if ($field == NULL) $row[$i] = "*";
		}
		echo implode($del, $row);
		echo $eol;
	}
}


