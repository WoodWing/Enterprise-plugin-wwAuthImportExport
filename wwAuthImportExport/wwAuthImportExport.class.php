<?php
/****************************************************************************
   Copyright 2013 WoodWing Software BV

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
****************************************************************************/

require_once dirname(__FILE__).'/../../config.php';
require_once dirname(__FILE__).'/../../configserver.php';

require_once BASEDIR.'/server/secure.php';
require_once BASEDIR.'/server/admin/global_inc.php';

require_once dirname(__FILE__).'/config.php';

class wwAuthImportExport
{
	public function wwAuthImportExport() {		
		$this->dbh = DBDriverFactory::gen();
	}

	//
	//	-getContext-
	//
	//	Create a structure to reverse lookup brand,category,states names to ids
	//

	public function getPublicationsContext() {
	
		$context = array();
	
		require_once BASEDIR.'/server/bizclasses/BizPublication.class.php';
		$pubs = BizPublication::getPublicationInfos(BizSession::getShortUserName());

		foreach ($pubs as $pub) {
	
			$context[$pub->Name] = $pub;
		
			foreach ($pub->Categories as $cat) {		
				$pub->Categories[$cat->Name] = $cat;
			}	
		
			foreach ($pub->States as $state) {
				$pub->States[$state->Type.'::'.$state->Name] = $state;
			}	
		}

		return $context;
	}


	public function getAuthorizations( $brand = false ) {
	
		if ($brand === false) {
			$where = "1";
		}
		else {
			$where = "pub.id = '$brand'"; 
			$sql = "SELECT `publication` as 'brand' from `smart_publications` where id = '$brand'";
			$sth = $this->dbh->query($sql);
			$row = $this->dbh->fetch($sth);
		}
	
		//
		//	get all relevant fields
		//
	
		$sql = "SELECT  ug.name as 'usergroup', pub.publication as 'brand', sec.section as 'category', CONCAT(st.type, '::', st.state) as 'status',  prof.profile
		FROM `smart_authorizations` au  
		LEFT JOIN `smart_groups` ug on (au.grpid = ug.id) 
		LEFT JOIN `smart_publications` pub on (au.publication = pub.id)
		LEFT JOIN `smart_publsections` sec on (au.section = sec.id)
		LEFT JOIN `smart_profiles` prof on (au.profile = prof.id)
		LEFT JOIN `smart_states` st on (au.state = st.id)
		WHERE $where
		ORDER BY usergroup, brand, category, st.type, st.code";

		$sth = $this->dbh->query($sql);
		
		$result = Array();
		
		while ($row = $this->dbh->fetch($sth) ) {
			$result[] = $row;
		}
		
		return $result;
	}
	

	public function resetAuthorizations( $brand ) {
	
		$dba = $this->dbh->tablename("authorizations");

		$sql = "DELETE FROM $dba where grpid > 2";
		if ($brand) $sql .= " AND publication = $brand";

		$sth = $this->dbh->query($sql);

	}


	public function insertAuthorization( $publ, $issue, $grp, $section, $state, $profile ) {
	
		$dba = $this->dbh->tablename("authorizations");

		$sql = "SELECT * FROM $dba where `publication` = $publ AND `issue` = $issue AND `grpid` = $grp". 
						" AND `section` = $section AND `state` = $state AND `profile` = $profile ";

		$sth = $this->dbh->query($sql);
		
		//
		//	if no duplicate found, proceeed with the insert
		//
		if (!$this->dbh->fetch($sth)) {								
			$sql = "INSERT INTO $dba (`publication`, `issue`, `grpid`, `section`, `state`, `profile`) ".
							"VALUES ($publ, $issue, $grp, $section, $state, $profile)";
			$sql = $this->dbh->autoincrement($sql);
			$sth = $this->dbh->query($sql);
		}

	}

	public function getProfile( $name ) {
	
		$dbp = $this->dbh->tablename("profiles");
		$sql = "select * from $dbp where `profile`='$name'";
		$sth = $this->dbh->query($sql);
		if ($r = $this->dbh->fetch($sth) ) 
			return $r['id'];
		else
			return false;
	}
	
	private function getFeatures() {
		if (file_exists(BASEDIR.'/server/bizclasses/BizAccessFeatureProfiles.class.php')) 
		{
			require_once BASEDIR.'/server/bizclasses/BizAccessFeatureProfiles.class.php';
			$profs  = BizAccessFeatureProfiles::getAllFeaturesAccessProfiles();
			$features = Array();
			foreach ($profs as $i=>$p) {
				$features[$i] = $p->Name;
			}
		} else {
			// fall back for older versions
			$features = unserialize('a:65:{i:1;s:4:"View";i:11;s:16:"List_PubOverview";i:2;s:4:"Read";i:9;s:9:"Open_Edit";i:3;s:5:"Write";i:4;s:6:"Delete";i:10;s:5:"Purge";i:5;s:21:"Change_Status_Forward";i:6;s:13:"Change_Status";i:7;s:15:"Restore_Version";i:8;s:11:"Keep_Locked";i:12;s:16:"Download_Preview";i:13;s:17:"Download_Original";i:101;s:15:"ApplyParaStyles";i:102;s:14:"EditParaStyles";i:104;s:15:"ApplyCharStyles";i:105;s:14:"EditCharStyles";i:103;s:16:"ApplyParaFormats";i:106;s:19:"ApplyCharFontFamily";i:107;s:18:"ApplyCharFontStyle";i:108;s:21:"ApplyCharBasicFormats";i:109;s:24:"ApplyCharAdvancedFormats";i:110;s:7:"CopyFit";i:117;s:16:"CompositionPrefs";i:125;s:17:"ForceTrackChanges";i:126;s:16:"EditTrackChanges";i:114;s:14:"ChangeLanguage";i:115;s:14:"EditDictionary";i:118;s:13:"ResizeTFLines";i:119;s:8:"ResizeTF";i:111;s:13:"ApplySwatches";i:112;s:12:"EditSwatches";i:84;s:15:"AddInCopyImages";i:85;s:7:"Publish";i:86;s:12:"Create_Tasks";i:87;s:30:"AllowMultipleArticlePlacements";i:88;s:13:"ChangeEdition";i:90;s:13:"CreateDossier";i:91;s:23:"CheckinArticleFromLayer";i:92;s:26:"CheckinArticleFromDocument";i:93;s:13:"AbortCheckOut";i:98;s:20:"RestrictedProperties";i:99;s:9:"ChangePIS";i:70;s:15:"EditStickyNotes";i:71;s:9:"ViewNotes";i:72;s:11:"DeleteNotes";i:113;s:8:"EditTags";i:116;s:9:"ShortCuts";i:120;s:14:"EditTextMacros";i:136;s:16:"AdvElementsPanel";i:130;s:8:"DSUpdate";i:131;s:19:"DSWriteDataToServer";i:132;s:13:"DSCreateField";i:133;s:23:"DSUpdateContentDatabase";i:134;s:12:"EditTextComp";i:135;s:17:"InsertInlineImage";i:1001;s:12:"Query_Browse";i:1002;s:20:"Publication_Overview";i:1003;s:6:"Upload";i:1004;s:9:"Reporting";i:1006;s:10:"Web_Editor";i:1007;s:9:"MyProfile";i:1008;s:8:"Planning";i:1009;s:17:"ContentStationPro";i:1501;s:5:"Elvis";}');
		}
		return $features;
	}
	
	private function getProfiles( ) {	
		$profiles = Array();
		$dbp = $this->dbh->tablename("profiles");
		$sql = "select * from $dbp";
		$sth = $this->dbh->query($sql);
		while( ($row = $this->dbh->fetch($sth) ) ) {
			$profiles[$row['id']] = $row;
		}
		return $profiles;
	}
	
	public function getProfileFeatures() {
	
		$profiles = $this->getProfiles();
		$features = $this->getFeatures();
		$profilefeatures = Array();
		
		$dbpv = $this->dbh->tablename("profilefeatures");
		$sql = "select `feature`,`profile`,`value` from $dbpv order by `profile`,`feature`";
		$sth = $this->dbh->query($sql);
		while( ($row = $this->dbh->fetch($sth) ) ) {
			if ($features[$row['feature']]) {

				$profilefeatures[] = Array( 'profile' => $profiles[$row['profile']]['profile'],
											'feature' => $features[$row['feature']],
											'value'	  => $row['value'] == 'Yes' );
			}
		}
		return $profilefeatures;
	}	
	
	public function insertProfileFeature( $profile, $feature, $description="Created by Import" ) {

		$dbp = $this->dbh->tablename('profiles');
		$dbpv = $this->dbh->tablename('profilefeatures');
		$dba = $this->dbh->tablename('authorizations');

		$sql = "select `id` from $dbp where `profile` = '" . $profile . "'";
		$sth = $this->dbh->query($sql);
		$row = $this->dbh->fetch($sth);

		if (!is_array($row)) {
			$sql = "insert INTO $dbp (`profile`, `description`, `code`) VALUES ('" . $profile . "', '" . $description . "', 0)";
			$sql = $this->dbh->autoincrement($sql);
			$sth = $this->dbh->query($sql);
			$id = $this->dbh->newid($dbp,true);
		} else {
			$id = $row['id'];
		}				 
		
		if ($feature) {
			$features = array_flip($this->getFeatures());
		
			$fid = $features[$feature];
			$sql = "select `id` from $dbpv where `profile` = '" . $id . "' and `feature` = '". $fid . "'";
			$sth = $this->dbh->query($sql);
			$row = $this->dbh->fetch($sth);

			if (!is_array($row)) {
				$sql = "INSERT INTO $dbpv (`profile`, `feature`, `value`) ".
				"VALUES ($id, $fid, 'Yes')";
				$sql = $this->dbh->autoincrement($sql);
				$this->dbh->query($sql);				
			}
		}
		return $id;
	}
}

