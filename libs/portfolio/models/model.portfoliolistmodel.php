<?php
require_model('HtmlBaseModel');

class PortfolioListModel extends HtmlBaseModel {
	
	public function make() {
		$this->setCharset('UTF-8');
		$this->setTitle('ToMo-design - powered by Watena');
	}
	
	public function getQuote() {
		return array('text' => 'This is my world !!', 'author' => 'Voet Jelle');
	}
}

?>