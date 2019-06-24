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

//
//	read file to import 
//
$fc = file_get_contents($_FILES['userFile']['tmp_name']);

if ($fc) {

	require_once dirname(__FILE__).'/../wwAuthImportExport.class.php';
	$aie = new wwAuthImportExport();

	//
	//	split up in lines
	//
	$lines = preg_split( '/\r\n|\r|\n/', $fc );
		
	foreach ($lines as $rowi=>$row) {

		//
		// skip comment rows
		//
		if ($row[0] != '#') {
			//
			//	split in fields
			//
			$values = explode(DELIMITER, $row);

			//
			// and insert when value = 1
			//
			if (sizeof($values) == 3) {
				$aie->insertProfileFeature($values[0],$values[1],$values[2]);				
			}
		}
	}
}

//
//	relocate to web admin UI
//
header('Location:../../../../server/admin/webappindex.php?webappid=AccessProfiles&plugintype=config&pluginname=wwAuthImportExport');


