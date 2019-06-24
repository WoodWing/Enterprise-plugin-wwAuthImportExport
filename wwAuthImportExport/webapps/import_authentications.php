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
//	and start importing the file
//
import_file();
	
	
//
//	when the work is done, move back to the originating admin page
//	
header('Location:../../../../server/admin/webappindex.php?webappid=Admin&plugintype=config&pluginname=wwAuthImportExport');


//
//	- import_file -
//
//	Import the records from the uploaded file $_FILES['userFile']['tmp_name']
//  with the following structure:
//		usergroup, brand, category, status, access profile \r
//		...
//
//	If usergroup does not exist, it is created.
//	If brand does not exist, the record is skipped
//	If category or status do not exist, the value 0 == <all> is used
//	If access profile does not exist, the record is skipped
//
//	After finishing the import, the new list is displayed.
//
 
function import_file() {
	
	require_once dirname(__FILE__).'/../wwAuthImportExport.class.php';
	
	$aie = new wwAuthImportExport();
	$context = $aie->getPublicationsContext();	
	$authorizations = $aie->getAuthorizations();
	$verbose = false;
	
//
//	read contents of the file
//
	$fc = file_get_contents($_FILES['userFile']['tmp_name']);
	
	if ($fc) {
		if ($verbose) echo '<table border=1>';
		
		//
		//	split in records (by line endings)
		//
		$lines = preg_split( '/\r\n|\r|\n/', $fc );
		
		//
		//	then process all lines sequentially
		//
		foreach ($lines as $rowi=>$row) {

			if ($verbose) echo '<HR>'.$row.'<BR>';

			//
			//	split first record either on ";" or "," to get the header keys
			//	and save the right delimiter
			//		
			if ($rowi == 0) {
				$keys = explode($del=";",$row); 
				if (sizeof($keys) == 1)
					$keys = explode($del=",",$row); 
					
				continue;
			}
			
			if ($verbose) echo '<tr>';
			
			//
			// split the record to get field values
			// skip record if not complete
			//
			$values = explode($del,$row);
			if (sizeof($values) != 5) 
				continue;
				
			//	
			// process all values and match them to the keys
			//	
			$keyrow = array();
			foreach ($values as $i=>$value) {
				
				if ($verbose) echo '<td>';
				$key = $keys[$i];
				$keyrow[$key] = $value;
				if ($verbose) echo $value;

				//
				// do special things depending on key
				//
				switch ($key) {
					case 'brand':
						$brand = $value;
						$keyrow[$key] = $context[$value]->Id;
						break;

					case 'category':
						if ($value == '*')
							$keyrow[$key] = 0;
						else	
							$keyrow[$key] = $context[$brand]->Categories[$value]->Id;
						break;

					case 'status':
						if ($verbose) print_r($context[$brand]->States);
						if ($value == '*')
							$keyrow[$key] = 0;
						else	
							$keyrow[$key] = $context[$brand]->States[$value]->Id;
						break;

					case 'usergroup':	
						$usergroup = $value;
						require_once BASEDIR.'/server/bizclasses/BizUser.class.php';
						$groupinfo = BizUser::findUserGroup($value);						
						$keyrow[$key] = $groupinfo['id'];
						break;

					case 'profile':
						$accessprofile = $value;
						$pid = $aie->getProfile( $value );
						
						if (!$pid) { // if not available, create empty profile
							$aie->insertProfileFeature( $accessprofile, "" );
							$pid = $aie->getProfile( $value );
						} 
						
						$keyrow[$key] = $pid;
						
						break;
				}

				if ($verbose) echo '</td>';
			}
			if ($verbose) echo '<td>';
			
			
			if ($rowi != 0) {
				$error = false;
			
				// 
				// check for missing or wrong data
				//	
				$publ = $keyrow['brand'];				
				if (!$publ) {echo "ERROR UNKNOWN BRAND $brand<BR>"; $error=true;}
				
				//	
				// if user group does not exist, create it
				//
				$grp = $keyrow['usergroup'];
				if (!$grp) {
					require_once BASEDIR . '/server/services/adm/AdmCreateUserGroupsService.class.php';
					require_once BASEDIR . '/server/interfaces/services/adm/DataClasses.php';

					$groupObj = new AdmUserGroup( null, $usergroup, $usergroup , false/*admin*/, true/*routing*/, null);
					$service = new AdmCreateUserGroupsService();
					global $ticket;
					$request = new AdmCreateUserGroupsRequest($ticket, array(), array($groupObj));
					$response = $service->execute($request);
					$grp = $response->UserGroups[0]->Id;			
				}
				
				$section = ($keyrow['category'])?$keyrow['category']:0;
				$state = ($keyrow['status'])?$keyrow['status']:0;
				$profile = $keyrow['profile'];
				if (!$profile) {
					echo "ERROR UNKNOWN PROFILE $accessprofile<BR>";$error=false;
					// create empty profile
					//$profile = $aie->insertProfileFeature( $accessprofile, "" );
				}
				
				if (!$error) {
					$issue = 0;
					$aie->insertAuthorization( $publ, $issue, $grp, $section, $state, $profile  );					
				} else {
					print_r($row);
					echo '<hr>';
				}
			}
			if ($verbose) echo '</td>';
			if ($verbose) echo '</tr>';
		}

		if ($verbose) echo '</table>';
	}
	
}
