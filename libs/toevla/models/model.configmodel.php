<?php
require_plugin('DatabaseManager');

class ConfigModel extends Model {
	
	public function getConfigData() {
		$aData = array(
			'developmentLogin' => '' . new Mapping('/debug/login')
		);
		$oStatement = DatabaseManager::getConnection('toevla')->select('game_config');
		foreach($oStatement as $aRow) {
			$aData[$aRow['name']] = $aRow['value'];
		}
		return $aData;
	}
}

?>