<?php
/**
 * Default Model for standarized HTML output.
 * The view is however still responsible to catch the data and siaplay it appropriatly.
 * 
 * Configuration:
 * - description: meta-description of the page, defaults to ''
 * - keywords: meta-keywords of the page, defaults to ''
 * - charset: expected character-encoding of the page, defaults to Encoding::charset()
 * - content-type: expected content-type of the page, defaults to text/html
 * - title: standard html title-tag, defaults to 'hostname'
 * - head: additional content between the <head></head> tags, defaults to ''
 * - body: additional content between the <body></body> tags, defaults to ''
 * 
 * @author Jelle
 * @version 0.1.0
 */
class HtmlModel extends Model {

	private $m_sCharset = null;
	private $m_sContentType = null;
	private $m_sTitle = null;
	private $m_sDescription = null;
	private $m_sKeywords = null;
	private $m_aJavascriptLinks = array();
	private $m_aCssLinks = array();
	
	public function getRoot() {
		return Request::root();
	}
	
	public function getHost() {
		return Request::host();
	}
	
	public function getPath() {
		return Request::path();
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
	
	public function setKeywords($sKeywords) {
		$this->m_sKeywords = $aKeywords;
	}
	
	public function getKeywords() {
		return $this->m_sKeywords ?: $this->getConfig('keywords', '');
	}
	
	public function addJavascriptLink($sLink, $bAbsolute = false) {
		$this->m_aJavascriptLinks []= array(
			'link' => $sLink,
			'absolute' => $bAbsolute
		);
	}
	
	public function addJavascriptCode($sCode) {
		$this->m_aJavascriptCode [] = array(
			'code' => $sCode
		);
	}
	
	public function addCssLink($sLink, $sMedia = 'all', $bAbsolute = false) {
		$this->m_aCssLinks []= array(
			'link' => $sLink,
			'media' => $sMedia,
			'absolute' => $bAbsolute
		);
	}
	
	public function addCssCode($sCode) {
		$this->m_aCssCode []= array(
			'code' => $sCode
		);
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
	
	public function prepare() {
		Events::invoke('prepareHtmlModel', array($this));
	}
	
	public function getHeadAsString() {
		$aReturn = array();
		
		$aReturn []= "<title>{$this->getTitle()}</title>";
		$aReturn []= "<meta http-equiv=\"Content-Type\" content=\"{$this->getContentType()}; charset={$this->getCharset()}\" />";
		$aReturn []= "<meta http-equiv=\"Description\" content=\"{$this->getDescription()}\" />";
		$aReturn []= "<meta http-equiv=\"Keywords\" content=\"{$this->getKeywords()}\" />";
		
		foreach($this->m_aCssLinks as $aCssLink) {
			$sLink = $aCssLink['link'];
			if(!$aCssLink['absolute']) $sLink = $this->getRoot() . '/minifie/' . $sLink;
			$aReturn []= "<link href=\"$sLink\" rel=\"stylesheet\" type=\"text/css\" media=\"$aCssLink[media]\" />";
		}
		
		return implode("\r\n", $aReturn);
	}
	
	public function getBodyAsString() {
		return '';
	}
}

?>