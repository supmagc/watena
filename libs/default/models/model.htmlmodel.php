<?php

class HtmlModel extends Model {

	private $m_sCharset = null;
	private $m_sContentType = null;
	private $m_sTitle = null;
	private $m_sDescription = null;
	private $m_aKeywords = null;
	private $m_aHeads = array();
	private $m_aBodies = array();
	private $m_aJavascriptLinks = array();
	
	public function getRoot() {
		return $this->getWatena()->getMapping()->getRoot();
	}
	
	public function getHost() {
		return $this->getWatena()->getMapping()->getHost();
	}
	
	public function getLocal() {
		return $this->getWatena()->getMapping()->getLocal();
	}
	
	public function addHead($mContent) {
		$this->clearHead();
		$this->addHead($mContent);
	}
	
	public function setHead($mContent) {
		$this->m_aHeads = array('' . $mContent);
	}
	
	public function getHead() {
		return implode("\n", $this->m_aHeads);
	}
	
	public function clearHead() {
		$this->m_aHeads = array();
	}
	
	public function addBody($mContent) {
		$this->clearBody();
		$this->addBody($mContent);
	}
	
	public function setBody($mContent) {
		$this->m_aBodies = array('' . $mContent);
	}
	
	public function getBody() {
		return implode("\n", $this->m_aBodies);
	}
	
	public function clearBody() {
		$this->m_aBodies = array();
	}
	
	public function setTitle($sTitle) {
		$this->m_sTitle = $sTitle;
	}
	
	public function getTitle() {
		return $this->m_sTitle ?: $this->getConfig('title', Request::host());
	}
	
	public function setContentType($sContentType) {
		$this->m_sContentType = $sContentType;
	}
	
	public function getContentType() {
		return $this->m_sContentType ?: $this->getConfig('contenttype', 'text/html');
	}
	
	public function setCharset($sCharset) {
		$this->m_sCharset = $sCharset;
	}
	
	public function getCharset() {
		return $this->m_sCharset ?: $this->getConfig('charset', Encoding::charset());
	}
	
	public function setDescription($sDescription) {
		$this->m_sDescription = $sDescription;
	}
	
	public function getDescription() {
		return $this->m_sDescription ?: $this->getConfig('description', '');
	}
	
	public function setKeywords(array $aKeywords) {
		$this->m_aKeywords = $aKeywords;
	}
	
	public function clearKeywords() {
		$this->m_aKeywords = null;
	}
	
	public function addKeyword($sKeyword) {
		if(!is_array($this->m_aKeywords)) $this->m_aKeywords = array();
		$this->m_aKeywords []= $sKeyword;
	}
	
	public function getKeywords() {
		return is_array($this->m_aKeywords) ? implode(', ', $this->m_aKeywords) : $this->getConfig('keywords', '');
	}
	
	public function addJavascriptLink($sLink) {
		$this->m_aJavascriptLinks []= $sLink;
	}
	
	public function getJavascriptLoader($sNotify) {
		$sLinks = json_encode($this->m_aJavascriptLinks);
		return <<<EOD
<script language="javascript 1.8" type="text/javascript"><!--
new (function(l, n) {
	this.jsLoader = {
		'links': l,
		'notify': n,
		'callback': function(e) {
			if(this.loader.links.length > 0) this.loader.load(this.loader); 
			else if(window[this.loader.notify]) window[this.loader.notify].call(window);
		},
		'load': function(l) {
			ele = document.createElement('script');
			ele.async = 1;
			ele.loader = l;
			ele.src = l.links.shift();
			ele.addEventListener('load', l.callback, false);
			document.getElementsByTagName('head')[0].appendChild(ele);
		}
	};
	this.jsLoader.load(this.jsLoader);
})($sLinks, '$sNotify');
--></script>
EOD;
	}
	
	public function url($sUrl) {
		return Request::make($sUrl)->toString();
	}
}

?>