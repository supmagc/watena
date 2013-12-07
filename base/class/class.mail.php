<?php

class Mail extends Object {
	
	const CHARSET_DEFAULT = 'us-ascii';
	const DISPOSITION_INLINE = 'inline';
	const DISPOSITION_ATTACHMENT = 'attachment';

	private $m_aTo = array();
	private $m_aCc = array();
	private $m_aBcc = array();
	private $m_aAttach = array();
	private $m_sSubject = null;
	private $m_sFrom = null;
	private $m_sContextText = null;
	private $m_sContextHtml = null;
	private $m_sReplyTo = null;
	private $m_sReturnPath = null;
	private $m_sOrganisation = null;
	private $m_aPriorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	private $m_sCharsetText = null;
	private $m_sCharsetHtml = null;
	private $m_sPriority = '3 (Normal)';
	private $m_bReceipt = false;

	public function __construct() {
	}

	public function setSubject($sSubject ) {
		$this->m_sSubject = $sSubject;
	}
	
	public function getSubject() {
		return $this->m_sSubject;
	}

	public function setFrom($sAddress, $sName = null){
		if(!is_string($sAddress)) return false;
		$this->m_sFrom = $this->formatAddress($sAddress, $sName);
		return true;
	}
	
	public function getFrom() {
		return $this->m_sFrom;
	}

	public function setReplyTo($sAddress, $sName = null) {
		if(!is_string($sAddress)) return false;
		$this->m_sReplyTo = $this->formatAddress($sAddress, $sName);
		return true;
	}
	
	public function getReplyTo() {
		return $this->m_sReplyTo;
	}
	
	public function setReturnPath($sAddress, $sName = null) {
		if(!is_string($sAddress)) return false;
		$this->m_sReturnPath = $this->formatAddress($sAddress, $sName);
		return true;
	}
	
	public function getReturnPath() {
		return $this->m_sReturnPath;
	}
	
	public function setReceipt($bEnabled) {
		$this->m_bReceipt = $bEnabled;
		return true;
	}
	
	public function getReceipt() {
		return $this->m_bReceipt;
	}

	public function setTo($mAddress, $mName = null) {
		return $this->clearTo() && $this->addTo($mAddress, $mName);
	}
	
	public function clearTo() {
		$this->m_aTo = array();
		return true;
	}
	
	public function addTo($mAddress, $mName = null) {
		if(is_array($mAddress))
			$this->m_aTo = $this->formatAddress($mAddress, $mName);
		else
			$this->m_aTo[] = $this->formatAddress($mAddress, $mName);
		return true;
	}
	
	public function getTo() {
		return $this->m_aTo;
	}
	
	public function setCc($mAddress, $mName = null) {
		return $this->clearCc() && $this->addCc($mAddress, $mName);
	}
	
	public function clearCc() {
		$this->m_aCc = array();
		return true;
	}
	
	public function addCc($mAddress, $mName = null) {
		if(is_array($mAddress))
			$this->m_aCc = $this->formatAddress($mAddress, $mName);
		else
			$this->m_aCc[] = $this->formatAddress($mAddress, $mName);
		return true;
	}
	
	public function getCc() {
		return $this->m_aCc;
	}
	
	public function setBcc($mAddress, $mName = null) {
		return $this->clearBcc() && $this->addBcc($mAddress, $mName);
	}
	
	public function clearBcc() {
		$this->m_aBcc = array();
		return true;
	}
	
	public function addBcc($mAddress, $mName = null) {
		if(is_array($mAddress))
			$this->m_aBcc = $this->formatAddress($mAddress, $mName);
		else
			$this->m_aBcc[] = $this->formatAddress($mAddress, $mName);
		return true;
	}
	
	public function getBcc() {
		return $this->m_aBcc;
	}
	
	public function setOrganisation($sOrganisation) {
		$sOrganisation = Encoding::trim($sOrganisation);
		if(!$sOrganisation) return false;
		$this->m_sOrganisation = $sOrganisation;
		return true;
	}
	
	public function getOrganisation() {
		return $this->m_sOrganisation;
	}

	public function setPriority($nPriority) {
		if(!is_int($nPriority) || ! isset($this->m_aPriorities[$nPriority-1])) return false;
		$this->m_sPriority = $this->m_aPriorities[$nPriority-1];
		return true;
	}
	
	public function getPriority() {
		return $this->m_sPriority;
	}

	public function addAttachment($sFilepath, $sFilename = null, $sFiletype = 'application/x-unknown-content-type', $sDisposition = self::DISPOSITION_INLINE) {
		$sFilepath = $this->getWatena()->getPath($sFilepath);
		if(!$sFilepath) return false;
		if(!$sFilename) $sFilename = basename($sFilepath);
		$this->m_aAttach[$sFilepath] = array(
			'path' => $sFilepath,
			'name' => $sFilename,
			'type' => $sFiletype,
			'disposition' => $sDisposition
		);
		return true;
	}
	
	public function removeAttachment($sFilepath) {
		$sFilepath = $this->getWatena()->getPath($sFilepath);
		unset($this->m_aAttach[$sFilepath]);
		return true;
	}
	
	public function isAttachments($sFilepath) {
		$sFilepath = $this->getWatena()->getPath($sFilepath);
		return isset($this->m_aAttach[$sFilepath]);
	}
	
	function setContentText($sContent, $sCharset = self::CHARSET_DEFAULT) {
		$sCharset = Encoding::trim($sCharset);
		$this->m_sCharsetText = $sCharset;
		$this->m_sContextText = $sContent;
		return true;
	}
	
	public function getContentText() {
		return $this->m_sContextText;
	}
	
	public function setContentHtml($sContent, $sCharset = self::CHARSET_DEFAULT) {
		$sCharset = Encoding::trim($sCharset);
		$this->m_sCharsetHtml = $sCharset;
		$this->m_sContextHtml = $sContent;
		return true;
	}
	
	public function getContentHtml() {
		return $this->m_sContextHtml;
	}
	
	public function convertHtmlToText() {
		if(!$this->getContentHtml()) return false;
		$oHtml2Text = new html2text($this->getContentHtml());
		$this->setContentText($oHtml2Text->getText());
	}
	
	public function buildMail() {

		// build the headers
		$sBody = '';
		$aHeaders = array();
		
		if($this->getFrom())
			$aHeaders['From'] = $this->getFrom();
		
		if($this->getReplyTo())
			$aHeaders['Reply-To'] = $this->getReplyTo();
		else if($this->getFrom())
			$aHeaders['Reply-To'] = $this->getFrom();
		
		if($this->getReturnPath())
			$aHeaders['Return-Path'] = $this->getReturnPath();
		else if($this->getReplyTo())
			$aHeaders['Return-Path'] = $this->getReplyTo();
		else if($this->getFrom())
			$aHeaders['Return-Path'] = $this->getFrom();
		
		if($this->getSubject())
			$aHeaders['Subject'] = $this->getSubject();
		
		if(count($this->getCc()) > 0)
			$aHeaders['CC'] = implode(', ', $this->getCc());

		if(count($this->getBcc()) > 0 )
			$aHeaders['BCC'] = implode(', ', $this->getBcc());
		
		if($this->getOrganisation())
			$aHeaders['Organization'] = $this->getOrganisation();		
		
		if($this->getPriority())
			$aHeaders['X-Priority'] = $this->getPriority();
		
		$aHeaders["X-Mailer"] = "Php/Watena";
		$aHeaders['Date'] = $this->getWatena()->getTime()->formatRfc2822();
		
		if($this->getReceipt()) {
			if($this->getReplyTo())
				$aHeaders["Disposition-Notification-To"] = $this->getReplyTo();
			else
				$aHeaders["Disposition-Notification-To"] = $this->getFrom();
		}
		
		if(count($this->m_aAttach) > 0 || $this->getContentHtml()) {
			$sBoundary = md5(uniqid('watena-boundary') . microtime(true));
			$aHeaders["Mime-Version"] = "1.0";
			$aHeaders["Content-Type"] = "multipart/alternative; boundary=$sBoundary";
			$sBody = "This is a multi-part message in MIME format.\r\n";
			
			if($this->getContentText()) {
				$sBody .= "--$sBoundary\r\nContent-Type: text/plain; charset=$this->m_sCharsetText\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n";				
				$sBody .= quoted_printable_encode(Encoding::convert($this->getContentText(), $this->m_sCharsetText)) ."\r\n";
			}
			
			if($this->getContentHtml()) {
				$sBody .= "--$sBoundary\r\nContent-Type: text/html; charset=$this->m_sCharsetHtml\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n";
				$sBody .= quoted_printable_encode(Encoding::convert($this->getContentHtml(), $this->m_sCharsetText)) ."\r\n";
			}
			
			foreach($this->m_aAttach as $aData) {
				if(is_readable($aData['path']) ) {
					$sBody .= "--$sBoundary\r\nContent-type: $aData[type]; name=\"$aData[name]\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: $aData[disposition]; filename=\"$aData[name]\"\r\n\r\n";
					$nLength = filesize($aData['path'])+1;
					$hFile = fopen($aData['path'], 'r' );
					$sBody .= chunk_split(base64_encode(fread($hFile, $nLength)));
					fclose($hFile);
				}
			}
			$sBody .= "--$sBoundary--";
		}
		else {
			$aHeaders["Mime-Version"] = "1.0";
			$aHeaders["Content-Type"] = "text/plain; charset=$this->m_sCharsetText";
			$aHeaders["Content-Transfer-Encoding"] = $this->m_sCharsetText == self::CHARSET_DEFAULT ? '7bit' : '8bit';
			$sBody = wordwrap(Encoding::convert($this->getContentText(), $this->m_sCharsetText), 999) ."\r\n";
		}
		
		$sHeaders = '';
		foreach($aHeaders as $sKey => $sValue) {
			$sHeaders .= "$sKey: $sValue\r\n";
		}
		
		return array(implode( ", ", $this->getTo()), $this->getSubject(), $sBody, $sHeaders);
	}

	public function send() {
		list($sTo, $sSubject, $sContent, $sHeaders) = $this->buildMail();
		$bResult = @mail($sTo, $sSubject, $sContent, $sHeaders);
	}

	public function get() {
		list($sTo, $sSubject, $sContent, $sHeaders) = $this->buildMail();
		return "To: $sTo\nSubject: $sSubject\n\n$sHeaders\n\n$sContent";
	}

	public function checkAdresses($mAdresses){
		if(!is_array($mAdresses)) return is_email($mAdresses);
		foreach($mAdresses as $sAdress) {
			if(!is_email($sAdress)) {
				return false;
			}
		}
		return true;
	}
	
	public function formatAddress($mAddress, $mName = null) {
		if(is_array($mAddress) && is_array($mName) && count($mAddress) == count($mName)) {
			return array_map(array($this, 'formatAddress'), $mAddress, $mName);
		}
		if(is_assoc($mAddress)) {
			if(is_email(reset($mAddress)))
				return array_map(array($this, 'formatAddress'), array_values($mAddress), array_keys($mAddress));
			else 
				return array_map(array($this, 'formatAddress'), array_keys($mAddress), array_values($mAddress));
		}
		if(!is_array($mAddress) && !is_array($mName)) {
			return $mName ? "\"$mName\" <$mAddress>" : $mAddress;
		}
		return false;
	}

// 	/**
// 	 * Check and encode attach file(s) . internal use only
// 	 */
// 	private function buildAttachement() {
// 		$sBody = '';
// 		$this->m_aXHeaders["Content-Type"] = "multipart/mixed;\n boundary=\"$this->boundary\"";

// 		$sBody = "This is a multi-part message in MIME format.\n--$this->boundary\n";
// 		$sBody .= "Content-Type: text/plain; m_sCharset=$this->m_sCharset\nContent-Transfer-Encoding: $this->m_sCtEncoding\n\n" . $this->body ."\n";

// 		$sep= chr(13) . chr(10);

// 		$ata= array();
// 		$k=0;

// 		// for each attached file, do...
// 		for($i=0 ; $i<count($this->m_aAttach) ; ++$i) {
// 			$sFilepath = $this->m_aAttach[$i];
// 			$sBasename = basename($sFilepath);
// 			$sContentType = $this->actype[$i];	// content-type
// 			$sDisposition = $this->adispo[$i];

// 			if( ! file_exists( $filename) ) {
// 				echo "Class Mail, method attach : file $filename can't be found"; exit;
// 			}
// 			$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
// 			$ata[$k++] = $subhdr;
// 			// non encoded line length
// 			$linesz= filesize( $filename)+1;
// 			$fp= fopen( $filename, 'r' );
// 			$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
// 			fclose($fp);
// 		}
// 		$this->fullBody .= implode($sep, $ata);
// 	}
}

?>