<?php
require_plugin('DatabaseManager');

class PortfolioItem extends DbObject {
	
	public static function loadItem($mId) {
		$oTable = DatabaseManager::getConnection('portfolio')->getTable('portfolioitem');
		return DbObject::loadObject($oTable, $mId);
	}
	
	public static function loadItems() {
		$oConnection = DatabaseManager::getConnection('portfolio');
		$oTable = $oConnection->getTable('portfolioitem');
		$oStatement = $oConnection->select('portfolioitem', 1, 'frontpage');
		return DbObject::loadObjectList($oTable, $oStatement);
	}
}
