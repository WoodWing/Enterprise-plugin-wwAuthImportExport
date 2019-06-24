<?php

//
//	Home page for versions up til 9.x, which have no admin webapps
//


	$htmlBody .= "<h1>Export authorizations and access profiles<br>";
	$htmlBody .= '<br>';
	$htmlBody .= "<input type=button onClick=\" parent.location='webapps/list_profiles.php?export=1'; \" value='Export profiles'>";
	$htmlBody .= '<br>';
	$htmlBody .= "<input type=button onClick=\" parent.location='webapps/export.php'; \" value='Export authorizations'>";

	echo $htmlBody;
	
	
