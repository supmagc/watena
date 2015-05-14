<?php
/**
 * Default Model for standarized HTML output.
 * This model provides a lot of header data and asynchonous loading facilities.
 * The view is however still responsible to catch the data and display it appropriatly.
 * 
 * Configuration:
 * - description: meta-description of the page, defaults to ''
 * - keywords: meta-keywords of the page, defaults to ''
 * - charset: expected character-encoding of the page, defaults to Encoding::charset()
 * - content-type: expected content-type of the page, defaults to text/html
 * - title: standard html title-tag, defaults to 'hostname'
 * 
 * @author Jelle
 * @version 0.2.0
 */
class HtmlModel extends Model {

	private $m_sCharset = null;
	private $m_sContentType = null;
	private $m_sTitle = null;
	private $m_sDescription = null;
	private $m_sKeywords = null;
	private $m_aJavascript = array();
	private $m_aCss = array();
	private $m_sJavascriptLoaderCallback = null;
	
	/**
	 * Event called when the model is being prepared to render.
	 */
	const EVENT_PREPAREHTMLMODEL = 'prepareHtmlModel';
	
	/**
	 * Get the root portion of the request.
	 * 
	 * @return string
	 */
	public function getRoot() {
		return Request::root();
	}

	/**
	 * Get the host portion of the request.
	 * 
	 * @return string
	 */
	public function getHost() {
		return Request::host();
	}

	/**
	 * Get the path portion of the request.
	 * 
	 * @return string
	 */
	public function getPath() {
		return Request::path();
	}
	
	/**
	 * Set an overwrite for the title.
	 * 
	 * @see getTitle()
	 * @param string|null $sTitle
	 */
	public function setTitle($sTitle) {
		$this->m_sTitle = $sTitle;
	}
	
	/**
	 * Get the title.
	 * Use the overwrite if available, or use the configuration value instead.
	 * If no configuration value exists, the request host will be returned.
	 * 
	 * @see setTitle()
	 * @see Request::host()
	 * @return string
	 */
	public function getTitle() {
		return $this->m_sTitle ?: $this->getConfig('title', Request::host());
	}
	
	/**
	 * Set an overwrite for the content-type.
	 * 
	 * @see getContentType()
	 * @param string|null $sContentType
	 */
	public function setContentType($sContentType) {
		$this->m_sContentType = $sContentType;
	}
	
	/**
	 * Get the content-type.
	 * Use the overwrite value if available, or use the configuration value instead.
	 * If no configuration value exists, the default 'text/html' will be returned.
	 * 
	 * @see setContentType()
	 * @return string
	 */
	public function getContentType() {
		return $this->m_sContentType ?: $this->getConfig('contenttype', 'text/html');
	}
	
	/**
	 * Set an overwrite for the charset.
	 * 
	 * @see getCharset()
	 * @param string|null $sCharset
	 */
	public function setCharset($sCharset) {
		$this->m_sCharset = $sCharset;
	}
	
	/**
	 * Get the charset.
	 * Use the overwrite value if available, or use the configuration value instead.
	 * If no configuration value exists, the default charset will be returned.
	 * 
	 * @see setCharset()
	 * @see Encoding::charset()
	 * @return string
	 */
	public function getCharset() {
		return $this->m_sCharset ?: $this->getConfig('charset', Encoding::charset());
	}
	
	/**
	 * Set an overwrite for the description.
	 * 
	 * @see getDescription()
	 * @param string|null $sDescription
	 */
	public function setDescription($sDescription) {
		$this->m_sDescription = $sDescription;
	}
	
	/**
	 * Get the description.
	 * Use the overwrite value if available, or use the configuration value instead.
	 * If no configuration value exists, an empty string will be returned.
	 * 
	 * @see setDescription()
	 * @return string
	 */
	public function getDescription() {
		return $this->m_sDescription ?: $this->getConfig('description', '');
	}
	
	/**
	 * Set an overwrite for the keywords.
	 * 
	 * @see getKeywords()
	 * @param string|null $sKeywords
	 */
	public function setKeywords($sKeywords) {
		$this->m_sKeywords = $aKeywords;
	}
	
	/**
	 * Get the keywords.
	 * Use the overwrite value if available, or use the configuration value instead.
	 * If no configuration value exists, an empty string will be returned.
	 * 
	 * @see setKeywords()
	 * @return string
	 */
	public function getKeywords() {
		return $this->m_sKeywords ?: $this->getConfig('keywords', '');
	}
	
	/**
	 * Add a javascript link to the page.
	 * All javascript links and code snippets will asynchronously be loaded in order of addition.
	 * 
	 * @param string $sLink Link to the file.
	 * @param bool $bAbsolute Should the link be considered absolute.
	 * @param bool $bMinifie Should the file be minified. (only applicable when !$bAbsolute)
	 */
	public function addJavascriptLink($sLink, $bAbsolute = false, $bMinifie = true) {
		$this->m_aJavascript []= array(
			'link' => $sLink,
			'absolute' => $bAbsolute,
			'minifie' => $bMinifie
		);
	}
	
	/**
	 * Add a javascript snippet to the page.
	 * All javascript links and code snippets will asynchronously be loaded in order of addition.
	 * 
	 * @param string $sCode Javascript snippet.
	 */
	public function addJavascriptCode($sCode) {
		$this->m_aJavascript [] = array(
			'code' => $sCode
		);
	}
	
	/**
	 * Add a css link to the page.
	 * 
	 * @param string $sLink Link to the file.
	 * @param string $sMedia Css media type.
	 * @param bool $bAbsolute Should the link be considered absolute.
	 * @param bool $bMinifie Should the file be minified. (only applicable when !$bAbsolute)
	 */
	public function addCssLink($sLink, $sMedia = 'all', $bAbsolute = false, $bMinifie = true) {
		$this->m_aCss []= array(
			'link' => $sLink,
			'media' => $sMedia,
			'absolute' => $bAbsolute,
			'minifie' => $bMinifie
		);
	}
	
	/**
	 * Add a css snippet to the page.
	 * 
	 * @param string $sCode Css snippet.
	 * @param string $sMedia Css media type.
	 */
	public function addCssCode($sCode, $sMedia = 'all') {
		$this->m_aCss []= array(
			'code' => $sCode,
			'media' => $sMedia
		);
	}
	
	/**
	 * Set an overwrite for the name of the function to be called after
	 * all javascript files and snippets are loaded.
	 * 
	 * @see getJavascriptLoaderCallback()
	 * @param string|null $sCallback
	 */
	public function setJavascriptLoaderCallback($sCallback) {
		$this->m_sJavascriptLoaderCallback = $sCallback;
	}
	
	/**
	 * Get the name of the function to be called after all javascript files and snippets are loaded.
	 * Use the overwrite value if available, or use the configuration value instead.
	 * If no configuration value exists, the functionname will be 'loaderCallback'.
	 * 
	 * @see setJavascriptLoaderCallback()
	 * @return string
	 */
	public function getJavascriptLoaderCallback() {
		return $this->m_sJavascriptLoaderCallback ?: $this->getConfig('javascriptloadercallback', 'loaderCallback');
	}
	
	/**
	 * Make the given url absolute for the current request.
	 * 
	 * @param string $sUrl
	 * @return string
	 */
	public function url($sUrl) {
		return Request::make($sUrl)->toString();
	}
	
	/**
	 * @see Model::prepare()
	 */
	public function prepare() {
		Events::invoke('prepareHtmlModel', array($this));
	}

	/**
	 * Get the data to be injected in the <head></head> portion of the html page.
	 * This contains all data that might have been set earlier:
	 * - title
	 * - description
	 * - keywords
	 * - charset
	 * - content-type
	 * - javascript links
	 * - javascript snippets
	 * - css links
	 * - css snippets
	 * 
	 * @return string
	 */
	public function getHeadAsString() {
		$aReturn = array();
		
		$aReturn []= "<title>{$this->getTitle()}</title>";
		$aReturn []= "<meta http-equiv=\"Content-Type\" content=\"{$this->getContentType()}; charset={$this->getCharset()}\" />";
		$aReturn []= "<meta http-equiv=\"Description\" content=\"{$this->getDescription()}\" />";
		$aReturn []= "<meta http-equiv=\"Keywords\" content=\"{$this->getKeywords()}\" />";
		
		foreach($this->m_aCss as $aCss) {
			if(isset($aCss['link'])) {
				$sLink = $aCss['link'];
				if(!$aCss['absolute']) {
					if($aCss['minifie']) $sLink = $sLink;
					$sLink = $this->getRoot() . '/' . $sLink;
				}
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
				if(!$aJavascript['absolute']) {
					if($aJavascript['minifie']) $sLink = 'minifie/' . $sLink;
					$sLink = $this->getRoot() . '/' . $sLink;
				}
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
		e = document.createElement('script'); e.obj = o; e.type = 'application/javascript'; 
		if(t.link) {e.async = true; e.src = t.link; e.addEventListener('load', o.callback, false);}
		if(t.code) {e.innerHTML = decodeURIComponent(t.code);}
		document.getElementsByTagName('head')[0].appendChild(e);
		if(t.code) {this.load(o);}
	};
	this.load(this);
})($sJsData, '{$this->getJavascriptLoaderCallback()}');
--></script>
EOD;
		}
		
		return implode("\r\n", $aReturn);
	}
	
	/**
	 * Get the data to be injected in the <body></body> portion of the html page.
	 * 
	 * @return string
	 */
	public function getBodyAsString() {
		return '';
	}
}
