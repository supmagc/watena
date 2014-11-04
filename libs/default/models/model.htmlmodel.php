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
	private $m_aJavascript = array();
	private $m_aCss = array();
	
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
		$this->m_aJavascript []= array(
			'link' => $sLink,
			'absolute' => $bAbsolute
		);
	}
	
	public function addJavascriptCode($sCode) {
		$this->m_aJavascript [] = array(
			'code' => $sCode
		);
	}
	
	public function addCssLink($sLink, $sMedia = 'all', $bAbsolute = false) {
		$this->m_aCss []= array(
			'link' => $sLink,
			'media' => $sMedia,
			'absolute' => $bAbsolute
		);
	}
	
	public function addCssCode($sCode, $sMedia = 'all') {
		$this->m_aCss []= array(
			'code' => $sCode,
			'media' => $sMedia
		);
	}
	
	public function getJavascriptLoader($sNotify) {
		/*$sLinks = json_encode($this->m_aJavascriptLinks);
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
EOD;*/
		return '';
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
		
		foreach($this->m_aCss as $aCss) {
			if(isset($aCss['link'])) {
				$sLink = $aCss['link'];
				if(!$aCss['absolute']) $sLink = $this->getRoot() . '/' . $sLink;
				$aReturn []= "<link href=\"$sLink\" rel=\"stylesheet\" type=\"text/css\" media=\"$aCss[media]\" />";
			}
			if(isset($aCss['code'])) {
				$aReturn = "<style type=\"text/css\" media=\"$aCss[media]\">\r\n$aCss[code]\r\n</style>";
			}
		}

		$aJsData = array();
		foreach($this->m_aJavascript as $aJavascript) {
			if(isset($aJavascript['link'])) {
				$sLink = $aJavascript['link'];
				if(!$aJavascript['absolute']) $sLink = $this->getRoot() . '/minifie/' . $sLink;
				$aJsData []= array('link' => $sLink);
			}
			if(isset($aJavascript['code'])) {
				$aJsData []= array('code' => rawurlencode($aJavascript['code']));
			}
		}
		if(count($aJsData) > 0) {
			$sJsData = json_encode($aJsData);
			$aReturn []= <<<EOD
<script language="javascript 1.8" type="text/javascript"><!--
new (function(d, n) {
	this.data = d;
	this.notify = n;
	this.callback = function() {
		if(this.obj.data.length > 0) this.obj.load(this.obj); 
		else if(window[this.obj.notify]) window[this.obj.notify].call(window);
	};
	this.load = function(o) {
		t = o.data.shift(); 
		e = document.createElement('script'); e.obj = o; 
		if(t.link) {e.async = 1; e.src = t.link; e.addEventListener('load', o.callback, false);}
		if(t.code) {e.innerHTML = decodeURIComponent(t.code);}
		document.getElementsByTagName('head')[0].appendChild(e);
		if(t.code) {this.load(o);}
	};
	this.load(this);
})($sJsData, 'BLABLA');
--></script>
EOD;
		}
		
		return implode("\r\n", $aReturn);
	}
	
	public function getBodyAsString() {
		return '';
	}
}

?>