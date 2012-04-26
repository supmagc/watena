<?php
require_plugin('DatabaseManager');

class FestivalController extends Controller {

	private $m_aAllowed = array('website', 'locationType', 'twitterName', 'twitterHash', 'facebook', 'youtube', 'flickr', 'picasa', 'data', 'description_EN', 'logoFilename', 'afficheFilename', 'artists', 'quiz');

	public function process(Model $oModel = null, View $oView = null) {
		if($this->getWatena()->getMapping()->getLocal() == '/festival/save') {
			$oConnection = DatabaseManager::getConnection('toevla');

			$aErrors = array();
			$aData = array();
			foreach($_POST as $mIndex => $mData) {
				if(in_array($mIndex, $this->m_aAllowed)) {
					$aData[$mIndex] = $mData;
				}
			}
			
			if(isset($aData['picasa']) && $aData['picasa']) {
				$aMatches = array();
				$sSource = $aData['picasa'];
				if(Encoding::regFind('(user/[0-9]+/albumid/[0-9]+)', $aData['picasa'], $aMatches))
					$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => $aMatches[1]);
				else if(Encoding::regFind('(user/[0-9]+)', $aData['picasa'], $aMatches))
					$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => $aMatches[1]);
				else if(Encoding::regFind('google\\.com/([0-9]+)/photos/([0-9]+)', $aData['picasa'], $aMatches))
					$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => "user/$aMatches[1]/albumid/$aMatches[2]");
				else if(Encoding::regFind('google\\.com/([0-9]+)', $aData['picasa'], $aMatches))
					$aData['picasa'] = array('source' => $sSource, 'type' => 'default', 'url' => "user/$aMatches[1]");
				else {
					$aData['picasa'] = array('source' => $sSource, 'type' => 'unknown', 'url' => '');
					$aErrors []= 'picasa';
				}
				$aData['picasa'] = serialize($aData['picasa']);
			}
			
			if(isset($aData['flickr']) && $aData['flickr']) {
				$aMatches = array();
				$sSource = $aData['flickr'];
				if(Encoding::regFind('/photos/([0-9@N]+)/sets/([0-9]+)', $sSource, $aMatches))
					$aData['flickr'] = array('source' => $sSource, 'type' => 'set', 'url' => "id=$aMatches[1]");
				else if(Encoding::regFind('/photos/([0-9@N]+)/favorites', $sSource, $aMatches))
					$aData['flickr'] = array('source' => $sSource, 'type' => 'faves', 'url' => "nsid=$aMatches[1]");
				else if(Encoding::regFind('/photos/([0-9@N]+)', $sSource, $aMatches))
					$aData['flickr'] = array('source' => $sSource, 'type' => 'public', 'url' => "id=$aMatches[1]");
				else if(Encoding::regFind('/photoset\\.gne\\?(set=[0-9]+&nsid=[0-9@N]+)', $sSource, $aMatches))
					$aData['flickr'] = array('source' => $sSource, 'type' => 'set', 'url' => $aMatches[1]);
				else if(Encoding::regFind('/photos_public\\.gne\\?(id=[0-9@N]+)', $sSource, $aMatches))
					$aData['flickr'] = array('source' => $sSource, 'type' => 'public', 'url' => $aMatches[1]);
				else if(Encoding::regFind('/photos_faves\\.gne\\?(nsid=[0-9@N]+)', $sSource, $aMatches))
					$aData['flickr'] = array('source' => $sSource, 'type' => 'faces', 'url' => $aMatches[1]);
				else {
					$aData['flickr'] = array('source' => $sSource, 'type' => 'unknown', 'url' => '');
					$aErrors []= 'flickr';
				}
				$aData['flickr'] = serialize($aData['flickr']);
			}
			
			if(isset($aData['twitterName']) && $aData['twitterName']) {
				$aMatches = array();
				if(Encoding::regFind('^@?([a-zA-Z0-9_]+)$', Encoding::trim($aData['twitterName']), $aMatches))
					$aData['twitterName'] = $aMatches[1];
				else 
					$aErrors []= 'twitterName';
			}
			
			if(isset($aData['twitterHash']) && $aData['twitterHash']) {
				$aMatches = array();
				if(Encoding::regFind('^#?([a-zA-Z0-9_]+)$', Encoding::trim($aData['twitterHash']), $aMatches))
					$aData['twitterHash'] = $aMatches[1];
				else
					$aErrors []= 'twitterHash';
			}
			
			if(isset($aData['facebook']) && $aData['facebook']) {
				$aMatches = array();
				if(Encoding::regFind('^(http://(www\\.)?facebook\\.com/)?([-a-z_]+)$', Encoding::trim($aData['facebook']), $aMatches))
					$aData['facebook'] = $aMatches[1];
				else
					$aErrors []= 'facebook';
			}
				
			if(isset($aData['youtube']) && $aData['youtube']) {
				$aMatches = array();
				if(Encoding::regFind('youtu\\.be/([-a-zA-Z0-9]+)', $aData['youtube'], $aMatches))
					$aData['youtube'] = $aMatches[1];
				else if(Encoding::regFind('youtube\\.com/watch\\?.*v=([-a-zA-Z0-9]+)', $aData['youtube'], $aMatches))
					$aData['youtube'] = $aMatches[1];
				else if(Encoding::regFind('youtube-nocookie\\.com/embed/([-a-zA-Z0-9]+)', $aData['youtube'], $aMatches))
					$aData['youtube'] = $aMatches[1];
				else 
					$aErrors []= 'youtube';
				$aData['youtube'] = "http://www.youtube-nocookie.com/embed/$aData[youtube]?version=3&feature=player_embedded&autoplay=1&controls=0&rel=0&showinfo=0";
			}
				
			$oLogoFile = new Upload('logo');
			if($oLogoFile->exists()) {
				$sFilename = md5('logo' . microtime()) . '.' . $oLogoFile->getExtension();
				$oLogoFile->move("L/toevla/files/festival/$sFilename");
				$aData['logoFilename'] = $sFilename;
				if($oLogoFile->getError()) $aErrors []= 'logo';
			}
			
			$oAfficheFile = new Upload('affiche');
			if($oAfficheFile->exists()) {
				$sFilename = md5('affiche' . microtime()) . '.' . $oAfficheFile->getExtension();
				$oAfficheFile->move("L/toevla/files/festival/$sFilename");
				$aData['afficheFilename'] = $sFilename;
				if($oAfficheFile->getError()) $aErrors []= 'affiche';
			}
			
			if(isset($_POST['hash'])) {
				$oConnection->getTable('festival', 'hash')->update($aData, $_POST['hash']);			
				echo implode(',', $aErrors);
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
					$aRow['facebook'] = $aRow['facebook'] ? "http://www.facebook.com/$aRow[facebook]" : '';
					$aRow['flickr'] = unserialize($aRow['flickr']);
					$aRow['picasa'] = unserialize($aRow['picasa']);
					$aRow['flickr'] = isset($aRow['flickr']['source']) ? $aRow['flickr']['source'] : '';
					$aRow['picasa'] = isset($aRow['picasa']['source']) ? $aRow['picasa']['source'] : '';
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