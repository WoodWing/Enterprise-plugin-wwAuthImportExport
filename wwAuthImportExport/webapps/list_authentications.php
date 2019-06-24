<?php

require_once dirname(__FILE__).'/../../../config.php';
require_once BASEDIR.'/config/configserver.php';

require_once BASEDIR.'/server/secure.php';
require_once BASEDIR.'/server/admin/global_inc.php';

require_once dirname(__FILE__).'/../config.php';

//
// check valid admin user
//
$ticket = checkSecure('publadmin');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta http-equiv="PRAGMA" content="NO-CACHE"/>
	<meta http-equiv='Expires' content="-1"/>
</head>

<?php

show_current_authorizations();


function show_current_authorizations() {
	
	require_once dirname(__FILE__).'/../wwAuthImportExport.class.php';

	$aie = new wwAuthImportExport();
	
	$printheader=true;
	
	$sof = '<table border=1 width="100%">';
	$sol = '<tr><td>';
	$del = '</td><td>';
	$eol = '</td></tr>';
	$eof = '</table>';
	
	$context = $aie->getPublicationsContext();
	
	echo '<table border=1>';
	foreach ($context as $pub) {
		echo $sol;			
		echo $pub->Name;

		echo $del;			
		echo "<input type=button onClick=\" parent.location='../../../../config/plugins/wwAuthImportExport/webapps/export_authentications.php?id=$pub->Id'; \" value='Export'>";
		echo "&nbsp";			
		echo "<input type=button onClick=\" var r=confirm('Are you sure?'); if (r) { parent.location='../../../../config/plugins/wwAuthImportExport/webapps/reset_authentications.php?id=$pub->Id';}\" value='Reset'>";
		echo $eol;
	}
	echo $eof;
	echo '<hr>';
			
	$authorizations = $aie->getAuthorizations();
		
	echo $sof;
	
	foreach ($authorizations as $row) {
		if ($printheader) {
			echo $sol;
			echo implode($del, array_keys($row));
			$printheader=false;
			echo $eol;
		}
		echo $sol;
		foreach ($row as $i=>$field) {
			if ($field == NULL) $row[$i] = "*";
		}
		echo implode($del, $row);
		echo $eol;
	}

	echo $eof;
}