<?php

require_once dirname(__FILE__).'/../../../config.php';
require_once dirname(__FILE__).'/../../../configserver.php';

require_once BASEDIR.'/server/secure.php';
require_once BASEDIR.'/server/admin/global_inc.php';

require_once dirname(__FILE__).'/../config.php';

//
// check valid admin user
//
$ticket = checkSecure('publadmin');


if ($_REQUEST['export']) {
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".DBSELECT."_accessprofiles.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta http-equiv="PRAGMA" content="NO-CACHE"/>
	<meta http-equiv='Expires' content="-1"/>
</head>
<?php
}



//
// list all profiles

require_once dirname(__FILE__).'/../wwAuthImportExport.class.php';
$aie = new wwAuthImportExport();
$profilefeatures = $aie->getProfileFeatures();

//print_r($profilefeatures);

if ($_REQUEST['export']) $eol = EOL; else $eol = '<BR>';

echo '# '.$eol;
echo '# EXPORT ACCESS PROFILES'.$eol;
echo '# '.date('Y-m-d H:i').$eol;
echo '# '.$eol;
echo '# '.$eol;

foreach ($profilefeatures as $row) {
	
	echo $row['profile'];
	echo DELIMITER;

	echo $row['feature'];
	echo DELIMITER;

	echo $row['value'];
	echo $eol;
}


