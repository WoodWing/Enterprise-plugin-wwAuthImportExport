<?php
/**
 * Admin web application to configure this plugin. Called by core once opened by admin user
 * through app icon shown at the the Integrations admin page.
 *
 * @package Enterprise
 * @subpackage ServerPlugins
 * @since v9.0.0.
 * @copyright WoodWing Software bv. All Rights Reserved.
 */

require_once BASEDIR . '/server/utils/htmlclasses/EnterpriseWebApp.class.php';

class wwAuthImportExport_AccessProfiles_EnterpriseWebApp extends EnterpriseWebApp 
{
	/** 
	 * List of pub channels for which this plugin can publish (with PublishSystem set to
	 * Facebook) and where the admin user has access to.
	 *
	 * @var array $pubChannelInfos List of PubChannelInfo data objects
	 */
	
	public function getTitle()
		{ return 'Access Profiles Maintenance'; }

	public function isEmbedded()
		{ return true; }

	public function getAccessType()
		{ return 'admin'; }
	
	/**
	 * Called by the core server. Builds the HTML body of the web application.
	 *
	 * @return string HTML
	 */
	public function getHtmlBody() 
	{	
		$htmlBody = "<table width=800px><tr>";
		
		$htmlBody .= "<td  valign='top'>";
		$htmlBody .= "<input type=button onClick=\" parent.location='../../config/plugins/wwAuthImportExport/webapps/list_profiles.php?export=1'; \" value='Export profiles'>";
		$htmlBody .= "</td>";

		$htmlBody .= "<td  valign='top'>";
		$htmlBody .= "
			<form action='../../config/plugins/wwAuthImportExport/webapps/import_profiles.php?import=1' method='POST' enctype='multipart/form-data'>
			<!--input type='submit' name='upload_btn' value='Import'-->
			<input type='file' name='userFile'  onchange='this.form.submit();' value='Import...'>
			</form>";
		$htmlBody .= "</td>";

		$htmlBody .= "<td  valign='top' align='right'>";
		$htmlBody .= "<input type=button onClick=\" parent.location='../../server/admin/serverapps.php'; \" value='Back'>";
		$htmlBody .= "</td>";
		
		$htmlBody .= '</tr></table>';

		$htmlBody .= "<br><iframe id='framy' border=0 width=800px height=800px src='../../config/plugins/wwAuthImportExport/webapps/list_profiles.php'/>";

		return $htmlBody;
	}
}
