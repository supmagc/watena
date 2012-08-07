<?php

/**
 * Response class, that will be used to format the data that is to be send back to the server
 * 
 * @author Voet Jelle - ToMo-design
 * @version 2.0.3 beta
 * 
 * VERSION-LOG
 * -----------
 * 
 * 31-8-2010: 2.0.2 => 2.0.3
 * - Added sequential logic
 * 
 * 1-8-2010: 2.0.0 => 2.0.2
 * - Remove whitespaces when parsing HTML2DOM
 * - Added the entity-lists when parsing HTML2DOM
 * 
 * 30-7-2010: 2.0.0 => 1.0.0
 * - Completely revamped everything (backwards compatibility-break)
 * - No longer use 'innerHTML' in favor of DOM-manipulation
 * - Usage of JSON
 * - Automated parsing, ... no longer sending data trough string manipulation
 * - Integrated the TMX_Selector class for more advanced selection options
 * - AddCode is [DEPRECATED], will no longer work and will trigger an error
 */
class AJAX_Response {
	
	private static $ARRAY_ENTITIES_NAME = array('&nbsp;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup;', '&sup;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup;', '&ordm;', '&raquo;', '&frac;', '&frac;', '&frac;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '&OElig;', '&oelig;', '&Scaron;', '&scaron;', '&Yuml;', '&fnof;', '&circ;', '&tilde;', '&Alpha;', '&Beta;', '&Gamma;', '&Delta;', '&Epsilon;', '&Zeta;', '&Eta;', '&Theta;', '&Iota;', '&Kappa;', '&Lambda;', '&Mu;', '&Nu;', '&Xi;', '&Omicron;', '&Pi;', '&Rho;', '&Sigma;', '&Tau;', '&Upsilon;', '&Phi;', '&Chi;', '&Psi;', '&Omega;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigmaf;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&thetasym;', '&upsih;', '&piv;', '&ensp;', '&emsp;', '&thinsp;', '&zwnj;', '&zwj;', '&lrm;', '&rlm;', '&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;', '&dagger;', '&Dagger;', '&bull;', '&hellip;', '&permil;', '&prime;', '&Prime;', '&lsaquo;', '&rsaquo;', '&oline;', '&frasl;', '&euro;', '&image;', '&weierp;', '&real;', '&trade;', '&alefsym;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&crarr;', '&lArr;', '&uArr;', '&rArr;', '&dArr;', '&hArr;', '&forall;', '&part;', '&exist;', '&empty;', '&nabla;', '&isin;', '&notin;', '&ni;', '&prod;', '&sum;', '&minus;', '&lowast;', '&radic;', '&prop;', '&infin;', '&ang;', '&and;', '&or;', '&cap;', '&cup;', '&int;', '&there;', '&sim;', '&cong;', '&asymp;', '&ne;', '&equiv;', '&le;', '&ge;', '&sub;', '&sup;', '&nsub;', '&sube;', '&supe;', '&oplus;', '&otimes;', '&perp;', '&sdot;', '&lceil;', '&rceil;', '&lfloor;', '&rfloor;', '&lang;', '&rang;', '&loz;', '&spades;', '&clubs;', '&hearts;', '&diams;');
	private static $ARRAY_ENTITIES_NUMBER = array('&#160;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;', '&#338;', '&#339;', '&#352;', '&#353;', '&#376;', '&#402;', '&#710;', '&#732;', '&#913;', '&#914;', '&#915;', '&#916;', '&#917;', '&#918;', '&#919;', '&#920;', '&#921;', '&#922;', '&#923;', '&#924;', '&#925;', '&#926;', '&#927;', '&#928;', '&#929;', '&#931;', '&#932;', '&#933;', '&#934;', '&#935;', '&#936;', '&#937;', '&#945;', '&#946;', '&#947;', '&#948;', '&#949;', '&#950;', '&#951;', '&#952;', '&#953;', '&#954;', '&#955;', '&#956;', '&#957;', '&#958;', '&#959;', '&#960;', '&#961;', '&#962;', '&#963;', '&#964;', '&#965;', '&#966;', '&#967;', '&#968;', '&#969;', '&#977;', '&#978;', '&#982;', '&#8194;', '&#8195;', '&#8201;', '&#8204;', '&#8205;', '&#8206;', '&#8207;', '&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8218;', '&#8220;', '&#8221;', '&#8222;', '&#8224;', '&#8225;', '&#8226;', '&#8230;', '&#8240;', '&#8242;', '&#8243;', '&#8249;', '&#8250;', '&#8254;', '&#8260;', '&#8364;', '&#8465;', '&#8472;', '&#8476;', '&#8482;', '&#8501;', '&#8592;', '&#8593;', '&#8594;', '&#8595;', '&#8596;', '&#8629;', '&#8656;', '&#8657;', '&#8658;', '&#8659;', '&#8660;', '&#8704;', '&#8706;', '&#8707;', '&#8709;', '&#8711;', '&#8712;', '&#8713;', '&#8715;', '&#8719;', '&#8721;', '&#8722;', '&#8727;', '&#8730;', '&#8733;', '&#8734;', '&#8736;', '&#8743;', '&#8744;', '&#8745;', '&#8746;', '&#8747;', '&#8756;', '&#8764;', '&#8773;', '&#8776;', '&#8800;', '&#8801;', '&#8804;', '&#8805;', '&#8834;', '&#8835;', '&#8836;', '&#8838;', '&#8839;', '&#8853;', '&#8855;', '&#8869;', '&#8901;', '&#8968;', '&#8969;', '&#8970;', '&#8971;', '&#9001;', '&#9002;', '&#9674;', '&#9824;', '&#9827;', '&#9829;', '&#9830;');
	
	const ERROR_NONE    = 1;
	const ERROR_PHP     = 2;
	const ERROR_POST    = 3;
	const ERROR_REPONSE = 4;
	const ERROR_PROCESS = 5;
	
	private $m_nWorkingState = 1;
	private $m_sErrorMessage = '';

	private $m_mHTMLCallback = null;
	private $m_aData = array();
	
	/**
	 * Create a new Response object with a default state
	 *
	 */
	public function __construct($mHTMLCallback = null) {
		$this->m_mHTMLCallback = $mHTMLCallback;
		$this->m_nWorkingState = 1;
	}
	
	/**
	 * Add some plain javascript-code to be executed on the client
	 * !!! DEPRECATED
	 */
	public function AddCode($sCode, $bIsEncapsulated = false) {
		$this->m_nWorkingState = 4;
		$this->m_sErrorMessage = "AddCode is [DEPRECATED]\n";
	}

	/**
	 * Short notation to 'alert' the page
	 * 
	 * @param string $sText
	 */
	public function Alert($sText) {
		$this->CallFunction('alert', array($sText));
	}
	
	/**
	 * Call the specified function on the source-page
	 * 
	 * @param string $sFunction
	 * @param array $aParam
	 */
	public function CallFunction($sFunction, array $aParam = array()) {
		if($this->m_nWorkingState == 1) {
			$this->_AddToDataArray('call', array($sFunction, $aParam));
		}
		else {
			$this->_AddErrorMessage('Due to an error the following is impossible: calling JS-function - function:' . $sFunction . "\n");
		}
	}
	
	/**
	 * Change the DOM of the node specified by ID
	 * 
	 * @param string $sID
	 * @param string $sHTML
	 * @param boolean $bAppend
	 */
	public function DOMChangeByID($sID, $sHTML, $bAppend = false) {
		$this->DOMChange(new TMX_Selector($sID), $sHTML, $bAppend);
	}
	
	/**
	 * Change the DOM of the selected node(s)
	 * 
	 * @param TMX_Selector $oSelector
	 * @param string $sHTML
	 * @param boolean $bAppend
	 */
	public function DOMChange(TMX_Selector $oSelector, $sHTML, $bAppend = false) {
		if($this->m_nWorkingState == 1 && $oSelector->IsValid() && is_string($sHTML)) {
			if($this->m_mHTMLCallback !== null) $sHTML = call_user_func($this->m_mHTMLCallback, $sHTML);
			$this->_AddToDataArray('DOM_ins', array($oSelector->GetPath(), $this->_ParseHtml($sHTML), !$bAppend));
		}
		else {
			$this->_AddErrorMessage('DOM-change - selector: ' . var_export($oSelector->GetPath(), true) . "\n");
		}
	}
	
	/**
	 * Clean all childs inside the DOM node specified by ID
	 * 
	 * @param string $sID
	 */
	public function DOMCleanByID($sID) {
		$this->DOMRemove(TMX_Selector::Create($sID)->Childs());
	}

	/**
	 * Clean all the childs inside the selected DOM node(s)
	 * 
	 * @param TMX_Selector $oSelector
	 */
	public function DOMClean(TMX_Selector $oSelector) {
		$this->DOMRemove($oSelector->Childs());
	}
	
	/**
	 * Remove the DOM node specified by ID
	 * 
	 * @param string $sID
	 */
	public function DOMRemoveByID($sID) {
		$this->DOMRemove(new TMX_Selector($sID));
	}
	
	/**
	 * Remove all the selected DOM node(s)
	 * 
	 * @param $oSelector
	 */
	public function DOMRemove(TMX_Selector $oSelector) {
		if($this->m_nWorkingState == 1 && $oSelector->IsValid()) {
			$this->_AddToDataArray('DOM_del', array($oSelector->GetPath()));
		}
		else {
			$this->_AddErrorMessage('DOM-remove - selector: ' . var_export($oSelector->GetPath(), true) . "\n");
		}
	}
	
	/**
	 * Change the style-option of the DOM node specified by ID
	 * 
	 * @param string $sID
	 * @param string $sPName
	 * @param string $sPValue
	 */
	public function StyleChangeByID($sID, $sPName, $sPValue) {
		$this->StyleChange(new TMX_Selector($sID), $sPName, $sPValue);
	}
	
	/**
	 * Change the style-option(s) of the selected DOM node(s)
	 * 
	 * @param TMX_Selector $oSelector
	 * @param string $sPName
	 * @param string $sPValue
	 */
	public function StyleChange(TMX_Selector $oSelector, $sPName, $sPValue) {
		if($this->m_nWorkingState == 1 && $oSelector->IsValid() && is_string($sPName) && is_string($sPValue)) {
			$this->_AddToDataArray('STYLE', array($oSelector->GetPath(), $sPName, $sPValue));
		}
		else {
			$this->_AddErrorMessage('style-change - name: ' . $sName . "\n");
		}
	}
	
	/**
	 * Change the class of the DOM node specified by ID
	 * 
	 * @param string $sID
	 * @param string $sClass
	 */
	public function ClassChangeByID($sID, $sClass) {
		$this->ClassChange(new TMX_Selector($sID), $sClass);
	}

	/**
	 * Change the class of the selected DOM node(s)
	 * 
	 * @param TMX_Selector $oSelector
	 * @param string $sClass
	 */
	public function ClassChange(TMX_Selector $oSelector, $sClass) {
		if($this->m_nWorkingState == 1 && $oSelector->IsValid() && is_string($sClass)) {
			$this->_AddToDataArray('CLASS', array($oSelector->GetPath(), $sClass));
		}
		else {
			$this->_AddErrorMessage('class-change - name/class: ' . $sName . '/' . $sClass . "\n");
		}
	}
	
	/**
	 * Add a new JS file
	 * 
	 * @param string $sNewFile
	 */
	public function AddJSFile($sNewFile) {
		if($this->m_nWorkingState == 1 && is_string($sNewFile) && preg_match('%^[-a-z0-9_./:]+$%i', $sNewFile)) {
			$this->_AddToDataArray('JS_add', array($sNewFile));
		}
		else {
			$this->_AddErrorMessage('JSFile-add - filename: ' . $sNewFile . "\n");
		}
	}
	
	/**
	 * Replace an existing JS file
	 * 
	 * @param string $sOldFile
	 * @param string $sNewFile
	 */
	public function ReplaceJSFile($sOldFile, $sNewFile) {
		if($this->m_nWorkingState == 1 && is_string($sNewFile) && preg_match('%^[-a-z0-9_./:]+$%i', $sNewFile) && is_string($sOldFile) && eregi('^[-a-z0-9_./:]+$', $sOldFile)) {
			$this->_AddToDataArray('JS_rep', array($sOldFile, $sNewFile));
		}
		else {
			$this->_AddErrorMessage('JSFile-replace - oldFilename: ' . $sOldFile . ' - newFilename: ' . $sNewFile . "\n");
		}
	}
	
	/**
	 * Remove an old JS file
	 * 
	 * @param string $sOldFile
	 */
	public function RemoveJSFile($sOldFile) {
		if($this->m_nWorkingState == 1 && is_string($sOldFile) && preg_match('%^[-a-z0-9_./:]+$%i', $sOldFile)) {
			$this->_AddToDataArray('JS_rem', array($sOldFile));
		}
		else {
			$this->_AddErrorMessage('JSFile-remove - oldFilename: ' . $sOldFile . "\n");
		}
	}
	
	/**
	 * Add a new CSS file
	 * 
	 * @param string $sNewFile
	 */
	public function AddCSSFile($sNewFile) {
		if($this->m_nWorkingState == 1 && is_string($sNewFile) && preg_match('%^[-a-z0-9_./:]+$%i', $sNewFile)) {
			$this->_AddToDataArray('CSS_add', array($sNewFile));
		}
		else {
			$this->_AddErrorMessage('CSSFile-add - filename: ' . $sNewFile . "\n");
		}
	}
	
	/**
	 * Replace an existing CSS file
	 * 
	 * @param string $sOldFile
	 * @param string $sNewFile
	 */
	public function ReplaceCSSFile($sOldFile, $sNewFile) {
		if($this->m_nWorkingState == 1 && is_string($sNewFile) && preg_match('%^[-a-z0-9_./:]+$%i', $sNewFile) && is_string($sOldFile) && eregi('^[-a-z0-9_./:]+$', $sOldFile)) {
			$this->_AddToDataArray('CSS_rep', array($sNewFile));
		}
		else {
			$this->_AddErrorMessage('CSSFile-replace - oldFilename: ' . $sOldFile . ' - newFilename: ' . $sNewFile . "\n");
		}
	}
	
	/**
	 * Remove an old CSS file
	 * 
	 * @param string  $sOldFile
	 */
	public function RemoveCSSFile($sOldFile) {
		if($this->m_nWorkingState == 1 && is_string($sOldFile) && preg_match('%^[-a-z0-9_./:]+$%i', $sOldFile)) {
			$this->_AddToDataArray('CSS_rem', array($sNewFile));
		}
		else {
			$this->_AddErrorMessage('CSSFile-remove - oldFilename: ' . $sOldFile . "\n");
		}
	}
	
	/**
	 * Process this response, and make it ready to be send to the client
	 *
	 * @param boolean $bEcho define if this response will automatically be echo-ed
	 * @return string
	 */
	public function Process($bEcho = true) {
		$sRet = $this->m_nWorkingState;
		if($sRet == 1) {
			$sRet .= json_encode($this->m_aData);
		}
		else {
			$sRet .= $this->m_sErrorMessage;
		}
		
		//$sRet = preg_replace('/[\n\t\r]/', '', $sRet);
		if($bEcho) echo $sRet;
		return $sRet;
	}
	
	/**
	 * No comment
	 */
	private function _AddToDataArray($sDomain, $aData) {
		//if(!array_key_exists($sDomain, $this->m_aData)) $this->m_aData[$sDomain] = array();
		$this->m_aData []= array($sDomain, $aData);
	}
	
	/**
	 * No comment
	 */
	private function _AddErrorMessage($sMessage) {
		if($this->m_nWorkingState != 0) {
			$this->m_nWorkingState = 4;
			$this->m_sErrorMessage .= $sMessage;
		}
	}

	/**
	 * No comment
	 */
	private function _ParseHtml($sHTML) {
		$oDOM = new DOMDocument('1.0', TMD_Page::IN_CHARSET);
		$oDOM->preserveWhiteSpace = false;
		$oDOM->loadXML('<root>'.str_replace(self::$ARRAY_ENTITIES_NAME, self::$ARRAY_ENTITIES_NUMBER, $sHTML).'</root>', LIBXML_NOENT);
		$aData = $this->_ParseDOMNode($oDOM->childNodes->item(0));
		return is_array($aData) && array_key_exists('c', $aData) ? $aData['c'] : array();
	}

	/**
	 * No comment
	 */
	private function _ParseDOMNode($root) {
		if($root->nodeType == XML_TEXT_NODE) {
			return $root->nodeValue;
		}
		else {
			$result = array('n' => $root->nodeName);
			if($root->hasAttributes()) {
				$result['a'] = array();
				$attrs = $root->attributes;
				foreach($attrs as $i => $attr) $result['a'][$attr->name] = $attr->value;
			}
		    if($root->hasChildNodes()) {	
		    	$result['c'] = array();
		    	$children = $root->childNodes;
			    for($i = 0; $i < $children->length; $i++) {
			        $child = $children->item($i);
		            $result['c'] []= $this->_ParseDOMNode($child);
			    }
		    }
		    return $result;
		}
	}
	
	/**
	 * Create a new response oject with an error-code
	 *
	 * @param int $nCode
	 * @param string $sMessage
	 * @return TMX_Response
	 */
	public static function CreateErrorResponse($nCode, $sMessage) {
		$oTmp = new TMX_Response();
		$oTmp->m_nWorkingState = $nCode;
		$oTmp->m_sErrorMessage = 'Due to an error the following is impossible: ' . $sMessage;
		return $oTmp;
	}
	
	/**
	 * Add slashes as needed to be valid javascript parameters
	 * !!! DEPRECATED
	 * 
	 * @param string $sData
	 */
	public static function AddSlashes($sData) {
		return addcslashes($sData, '"\\');
	}
}
?>