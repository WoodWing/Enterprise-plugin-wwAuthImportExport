<?php

require_once dirname(__FILE__).'/../../../config.php';
require_once dirname(__FILE__).'/../../../configserver.php';

require_once BASEDIR.'/server/secure.php';
require_once BASEDIR.'/server/admin/global_inc.php';

require_once dirname(__FILE__).'/../config.php';

//
//	get reference to brand to be reset
//

$id = isset($_REQUEST['id'])  	? intval($_REQUEST['id'])  : 0;

//
// 	check valid admin user
//
$ticket = checkSecure('publadmin');

//
//	load helper class and reset the authorizations
//
require_once dirname(__FILE__).'/../wwAuthImportExport.class.php';
$aie = new wwAuthImportExport();
$aie->resetAuthorizations( $id );

//
//	relocate to web admin UI
//
header('Location:../../../../server/admin/webappindex.php?webappid=Admin&plugintype=config&pluginname=wwAuthImportExport');

