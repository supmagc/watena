<?php
require_plugin('DatabaseManager');

class FestivalController extends Controller {

	private $m_aAllowed = array('website', 'location', 'locationType', 'twitterName', 'twitterHash', 'facebook', 'youtube', 'flickr', 'picasa', 'data', 'description_EN', 'logoFilename', 'afficheFilename');
	
	
	public function process(Model $oModel = null, View $oView = null) {
		if($this->getWatena()->getMapping()->getLocal() == '/festival/save') {
			$oConnection = DatabaseManager::getConnection('toevla');
						
			$aData = array();
			foreach($_POST as $mIndex => $mData) {
				if(in_array($mIndex, $this->m_aAllowed)) {
					$aData[$mIndex] = $mData;
				}
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
		else if($this->getWatena()->getMapping()->getLocal() == '/festival/load') {
			if(isset($_GET['hash'])) {
				$sHash = $_GET['hash'];
				$oStatement = DatabaseManager::getConnection('toevla')->getTable('festival', 'hash')->select($sHash);
				if($oStatement->rowCount() > 0) {
					$aRow = $oStatement->fetch(PDO::FETCH_ASSOC);
					$aRow['logoFilename'] = '' . new Mapping('/files/toevla/festival/' . $aRow['logoFilename']);
					$aRow['afficheFilename'] = '' . new Mapping('/files/toevla/festival/' . $aRow['afficheFilename']);
					echo json_encode(array_intersect_key($aRow, array_combine($this->m_aAllowed, $this->m_aAllowed)));
				}
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