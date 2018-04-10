<?php
// vim: set ai ts=4 sw=4 ft=php:
namespace FreePBX\modules;
/*
 * Class stub for BMO Module class
 * In getActionbar change "modulename" to the display value for the page
 * In getActionbar change extdisplay to align with whatever variable you use to decide if the page is in edit mode.
 *
 */

class Returnontransfer implements \BMO
{

	// Note that the default Constructor comes from BMO/Self_Helper.
	// You may override it here if you wish. By default every BMO
	// object, when created, is handed the FreePBX Singleton object.

	// Do not use these functions to reference a function that may not
	// exist yet - for example, if you add 'testFunction', it may not
	// be visibile in here, as the PREVIOUS Class may already be loaded.
	//
	// Use install.php or uninstall.php instead, which guarantee a new
	// instance of this object.
	public function install()
	{
	}
	public function uninstall()
	{
	}

	// The following two stubs are planned for implementation in FreePBX 15.
	public function backup()
	{
	}
	public function restore($backup)
	{
	}

	// http://wiki.freepbx.org/display/FOP/BMO+Hooks#BMOHooks-HTTPHooks(ConfigPageInits)
	//
	// This handles any data passed to this module before the page is rendered.
	public function doConfigPageInit($page) {
                error_log($_REQUEST['actiondd']);
		//Handle form submissions
		switch ($_REQUEST['action']) {
		case 'save':
                    $dbh = \FreePBX::Database();
                    foreach (['timeout','prefix','alertinfo'] as $keyword) {
                        $sql = "REPLACE INTO returnontransfer (keyword,value) VALUES (?,?)";
                        $stmt = $dbh->prepare($sql);
                        $stmt->execute(array($keyword,$_REQUEST[$keyword]));
                    }
                    $sql = array();
                    if ($_REQUEST['enabled']==1) {
                        $sql[] = "REPLACE INTO `returnontransfer` (keyword,value) VALUES ('enabled','true')";
                        $sql[] = "UPDATE `freepbx_settings` SET `value` = 'blindxfer_ringback' WHERE `keyword` = 'TRANSFER_CONTEXT'";
                    } else {
                        $sql[] = "REPLACE INTO `returnontransfer` (keyword,value) VALUES ('enabled','false')";
                        $sql[] = "UPDATE `freepbx_settings` SET `value` = 'from-internal-xfer' WHERE `keyword` = 'TRANSFER_CONTEXT'";
                    }
                    foreach ($sql as $query) {
                        $stmt = $dbh->prepare($query);
                        $stmt->execute(array());
                    }
                    needreload();
		    break;
		}
	}

	// http://wiki.freepbx.org/pages/viewpage.action?pageId=29753755
	public function getActionBar($request)
	{
		$buttons = array();
		switch ($request['display']) {
		case 'returnontransfer':
			$buttons = array(
				'submit' => array(
					'name' => 'submit',
					'id' => 'submit',
					'value' => _('Submit')
				)
			);
			if (empty($request['extdisplay'])) {
				unset($buttons['delete']);
			}
			break;
		}
		return $buttons;
	}

	// http://wiki.freepbx.org/display/FOP/BMO+Ajax+Calls
	public function ajaxRequest($req, &$setting)
	{
		switch ($req) {
		case 'getJSON':
			return true;
			break;
		default:
			return false;
			break;
		}
	}

	public function showPage()
	{
            $subhead = _('Return on Blind Transfer Options');
            $content = load_view(__DIR__.'/views/form.php');
            echo load_view(__DIR__.'/views/default.php', array('subhead' => $subhead, 'content' => $content));
	}
}
