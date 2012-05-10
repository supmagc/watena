<?php
require_plugin('DatabaseManager');
require_plugin('ToeVla');

class FestivalController extends Controller {

	private $m_aAllowed = array('website', 'locationType', 'twitterName', 'twitterHash', 'facebook', 'youtube', 'flickr', 'picasa', 'data', 'description_EN', 'logoFilename', 'afficheFilename', 'artists', 'quiz');

	public function process(Model $oModel = null, View $oView = null) {
		$oConnection = DatabaseManager::getConnection('toevla');
		if($this->getWatena()->getMapping()->getPart(1) == 'save') {

			$aErrors = array();
			$aData = array();
			foreach($_POST as $mIndex => $mData) {
				if(in_array($mIndex, $this->m_aAllowed)) {
					$aData[$mIndex] = $mData;
				}
			}
			
			if(isset($aData['picasa'])) {
				$bError = false;
				$aData['picasa'] = ToeVla::parsePicasa($aData['picasa'], $bError);
				if($bError) $aErrors []= 'picasa';
			}
			
			if(isset($aData['flickr'])) {
				$bError = false;
				$aData['flickr'] = ToeVla::parseFlickr($aData['flickr'], $bError);
				if($bError) $aErrors []= 'flickr';
			}
			
			if(isset($aData['twitterName'])) {
				$bError = false;
				$aData['twitterName'] = ToeVla::parseTwitterName($aData['twitterName'], $bError);
				if($bError) $aErrors []= 'twitterName';
			}
			
			if(isset($aData['twitterHash'])) {
				$bError = false;
				$aData['twitterHash'] = ToeVla::parseTwitterHash($aData['twitterHash'], $bError);
				if($bError) $aErrors []= 'twitterHash';
			}
			
			if(isset($aData['facebook'])) {
				$bError = false;
				$aData['facebook'] = ToeVla::parseFacebook($aData['facebook'], $bError);
				if($bError) $aErrors []= 'facebook';
			}
				
			if(isset($aData['youtube'])) {
				$bError = false;
				$aData['youtube'] = ToeVla::parseYoutube($aData['youtube'], $bError);
				if($bError) $aErrors []= 'youtube';
			}
				
			$oLogoFile = new Upload('logo');
			if($oLogoFile->exists()) {
				$sFilename = md5('logo' . microtime()) . '.' . $oLogoFile->getExtension();
				$oLogoFile->move("L/toevla/files/festival/$sFilename");
				$aData['logoFilename'] = $sFilename;
				if($oLogoFile->getError()) $aErrors []= 'logoUpload';
			}
			
			$oAfficheFile = new Upload('affiche');
			if($oAfficheFile->exists()) {
				$sFilename = md5('affiche' . microtime()) . '.' . $oAfficheFile->getExtension();
				$oAfficheFile->move("L/toevla/files/festival/$sFilename");
				$aData['afficheFilename'] = $sFilename;
				if($oAfficheFile->getError()) $aErrors []= 'posterUpload';
			}
			
			if(isset($_POST['hash'])) {
				$oConnection->getTable('festival', 'hash')->update($aData, $_POST['hash']);			
				echo implode(',', $aErrors);
			}
			else {
				echo "NO HASH !";
			}
		}
		else if($this->getWatena()->getMapping()->getPart(1) == 'load') {
			if(isset($_GET['hash'])) {
				$sHash = $_GET['hash'];
				$oStatement = DatabaseManager::getConnection('toevla')->getTable('festival', 'hash')->select($sHash);
				if($oStatement->rowCount() > 0) {
					$aRow = $oStatement->fetch(PDO::FETCH_ASSOC);
					$aRow['logoFilename'] = '' . new Mapping('/files/toevla/festival/' . $aRow['logoFilename']);
					$aRow['afficheFilename'] = '' . new Mapping('/files/toevla/festival/' . $aRow['afficheFilename']);
					$aRow['facebook'] = $aRow['facebook'] ? "http://www.facebook.com/$aRow[facebook]" : '';
					$aRow['flickr'] = unserialize($aRow['flickr']);
					$aRow['picasa'] = unserialize($aRow['picasa']);
					$aRow['flickr'] = isset($aRow['flickr']['source']) ? $aRow['flickr']['source'] : '';
					$aRow['picasa'] = isset($aRow['picasa']['source']) ? $aRow['picasa']['source'] : '';
					echo json_encode(array_intersect_key($aRow, array_combine($this->m_aAllowed, $this->m_aAllowed)));
				}
			}
		}
		else if($this->getWatena()->getMapping()->getPart(1) == 'download') {
			if(Encoding::length($this->getWatena()->getMapping()->getPart(2)) == 32) {
				$oTable = $oConnection->getTable('festival', 'hash');
				if(($oData = $oTable->select($this->getWatena()->getMapping()->getPart(2))->fetchObject()) !== false) {
					$sFilename = 'Editor-' . Encoding::regReplace('[^-a-zA-Z0-9_]', '_', $oData->name) . '.zip';
					$oZipper = new ZipFile(PATH_LIBS . '/toevla/files/editor/' . $sFilename);
					if(!$oZipper->exists()) {
						$oZipper->add('/', PATH_DATA . '/editor');
						$sSave = '' . new Mapping('/festival/save');
						$sLoad = '' . new Mapping('/festival/load');
						$oZipper->create('data', "$sSave
$sLoad
{$oData->hash}
{$oData->name}");
					}
					$this->redirect(new Mapping('/files/toevla/editor/' . $sFilename));
				}
				else {
					$this->display('No valid festival found!');
				}
			}
		}
		else {
			$oConnection = DatabaseManager::getConnection('toevla');
			var_export($oConnection->getTable('festival')->select(12)->fetch());
			echo 'UNKNOWN';
		}
	}
}

?>