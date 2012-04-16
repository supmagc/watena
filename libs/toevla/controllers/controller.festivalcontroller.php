<?php
require_plugin('DatabaseManager');

class FestivalController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		if($this->getWatena()->getMapping()->getLocal() == '/festival/save') {
			$oConnection = DatabaseManager::getConnection('toevla');
			
			$aAllowed = array('website', 'location', 'twitterName', 'twitterHash', 'facebook', 'youtube', 'flickr', 'picasa', 'data');
			
			$aData = array();
			foreach($_POST as $mIndex => $mData) {
				if(in_array($mIndex, $aAllowed))
					$aData[$mIndex] = $mData;
			}
			
			$oLogoFile = new Upload('logo');
			if($oLogoFile->exists()) {
				$sFilename = md5(microtime()) . '.' . $oLogoFile->getExtension();
				$oLogoFile->move("L:/toevla/files/festival/$sFilename");
				$aData['logoFilename'] = $sFilename;
			}
			
			$oAfficheFile = new Upload('affiche');
			if($oAfficheFile->exists()) {
				$sFilename = md5(microtime()) . '.' . $oAfficheFile->getExtension();
				$oAfficheFile->move("L:/toevla/files/festival/$sFilename");
				$aData['afficheFilename'] = $sFilename;
			}
			
			if(isset($_POST['hash'])) {
				$oConnection->getTable('festival', 'hash')->update($aData, $_POST['hash']);			
				echo "SAVED ?!?";
			}
			else {
				echo "NO HASH !";
			}
		}
		else if($this->getWatena()->getMapping()->getLocal() == '/festival/download') {
		}
		else {
			$oConnection = DatabaseManager::getConnection('toevla');
			var_export($oConnection->getTable('festival')->select(12)->fetch());
			echo 'UNKNOWN';
		}
	}
}

?>