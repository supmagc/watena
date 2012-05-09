<?php

class Mail extends Object {
	
	const CHARSET_DEFAULT = 'us-ascii';

	private $m_aSendTo = array();
	private $m_aCc = array();
	private $m_aBcc = array();
	private $m_aAttach = array();
	private $m_sSubject = null;
	private $m_sFrom = null;
	private $m_sBody = null;
	private $m_sReplyTo = null;
	private $m_sOrganisation = null;
	private $m_aPriorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	private $m_sCharset = null;
	private $m_sPriority = '3 (Normal)';
	private $m_bReceipt = false;
	private $m_bCheckAdresses = false;

	public function __construct() {
		$this->autoCheck(true);
		$this->boundary= "--" . md5(uniqid("myboundary"));
	}

	/**
	 * Activate or desactivate the email addresses validator.
	 * By default autoCheck feature is on.
	 * 
	 * @param boolean $bCheck set to true to turn on the auto validation.
	 */
	public function autoCheck($bCheck) {
		$this->m_bCheckAdresses = $bCheck;
	}

	/**
	 * Define the subject line of the email.
	 * 
	 * @param string $sSubject Any monoline string.
	 */
	public function subject($sSubject ) {
		$this->m_sSubject = Encoding::translate("\r\n" , "  ", $sSubject);
	}

	/**
	 * Set the sender of the mail.
	 * 
	 * @param string $sFrom Should be an email address
	 */
	public function from($sFrom){
		if(!is_string($sFrom)) return false;
		$this->m_sFrom = $sFrom;
		return true;
	}

	/**
	 * Set the Reply-to header.
	 * 
	 * @param string $sAddress Should be an email address.
	 */
	public function replyTo($sAddress) {
		if(!is_string($address)) return false;
		$this->m_sReplyTo = $address;
		return true;
	}

	/**
	 * Add a m_bReceipt to the mail ie. A confirmation is returned to the "From" address (or "ReplyTo" if defined)
	 * when the receiver opens the message.
	 *
	 * @warning this functionality is *not* a standard, thus only some mail clients are compliants.
	 */
	public function receipt() {
		$this->m_bReceipt = true;
	}

	/**
	 * Set the mail recipient.
	 * 
	 * @param string|array $to email address, accept both a single address or an array of addresses
	 */
	public function to($mTo) {
		if($this->m_bCheckAdresses && !$this->checkAdresses($mTo)) return false;		
		if(is_array($mTo))
			$this->m_aSendTo = $mTo;
		else
			$this->m_aSendTo[] = '' . $mTo;
		return true;
	}

	/**
	 * Set the CC headers (carbon copy).
	 * 
	 * @param string|array $sCcc : email address(es), accept both array and string.
	 */
	public function cc($sCc) {
		if($this->m_bCheckAdresses && !$this->checkAdresses($sCc)) return false;		
		if(is_array($sCc))
			$this->m_aCc= $sCc;
		else
			$this->m_aCc[]= $sCc;
		return true;
	}

	/**
	 * Set the Bcc headers (blank carbon copy).
	 * 
	 * @param string|array $sBcc : email address(es), accept both array and string.
	 */
	public function bcc($sBcc ) {
		if($this->m_bCheckAdresses && !$this->checkAdresses($sBcc)) return false;		
		if(is_array($sBcc))
			$this->m_aBcc= $sBcc;
		else
			$this->m_aBcc[]= $sBcc;
		return true;
	}

	/**
	 * Set the body (message) of the mail.
	 * Define the m_sCharset if the message contains extended characters (accents).
	 */
	function body($sBody, $sCharset = null ) {
		$sCharset = Encoding::trim($sCharset);
		if($sCharset) $this->m_sCharset = $sCharset;
		$this->m_sBody = $sBody;
		return true;
	}

	/**
	 * Set the Organization header.
	 */
	public function organization($sOrganisation) {
		$sOrganisation = Encoding::trim($sOrganisation);
		if(!$sOrganisation) return false;
		$this->m_sOrganisation = $sOrganisation;
		return true;
	}

	/**
	 * Set the mail priority.
	 * 
	 * @param int $priority Integer taken between 1 (highest) and 5 ( lowest ).
	 */
	public function priority($nPriority) {
		if(!is_int($nPriority) || ! isset($this->m_aPriorities[$nPriority-1])) return false;
		$this->m_sPriority = $this->m_aPriorities[$nPriority-1];
		return true;
	}

	/**
	 * Attach a file to the mail.
	 * 
	 * @param string $sFilename : path of the file to attach
	 * @param string $sFiletype : MIME-type of the file. default to 'application/x-unknown-content-type'
	 * @param string $sDisposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
 	 */
	public function attach($sFilename, $sFiletype = null, $sDisposition = "inline" ) {
		if(!$sFiletype)
			$sFiletype = "application/x-unknown-content-type";

		$this->m_aAttach[] = $sFilename;
		$this->actype[] = $sFiletype;
		$this->adispo[] = $sDisposition;
	}

	/**
	 * Build the email message.
	 */
	public function buildMail() {

		// build the headers
		$sBody = '';
		$aHeaders = array();
		
		if($this->m_sFrom)
			$aHeaders['From'] = $this->m_sFrom;
		
		if($this->m_sReplyTo)
			$aHeaders['Reply-To'] = $this->m_sReplyTo;
		
		if($this->m_sSubject)
			$aHeaders['Subject'] = $this->m_sSubject;
		
		if(count($this->m_aCc) > 0)
			$aHeaders['CC'] = implode( ", ", $this->m_aCc );

		if(count($this->m_aBcc) > 0 )
			$aHeaders['BCC'] = implode( ", ", $this->m_aBcc );
		
		if($this->m_sPriority)
			$aHeaders['X-Priority'] = $this->m_sPriority;
		
		if($this->m_sOrganisation)
			$aHeaders['Organisation'] = $this->m_sOrganisation;		
		
		if($this->m_bReceipt) {
			if($this->m_sReplyTo)
				$aHeaders["Disposition-Notification-To"] = $this->m_sReplyTo;
			else
				$aHeaders["Disposition-Notification-To"] = $this->m_sFrom;
		}
		
		if($this->m_sCharset && Encoding::toLower($this->m_sCharset) != Encoding::toLower(self::CHARSET_DEFAULT)) {
			$aHeaders["Mime-Version"] = "1.0";
			$aHeaders["Content-Type"] = "text/plain; charset=$this->m_sCharset";
			$aHeaders["Content-Transfer-Encoding"] = 'base64';
		}

		$aHeaders["X-Mailer"] = "Php/Watena";

		if(count($this->m_aAttach) > 0)
			NYI(); //$sBody = $this->buildAttachement();
		else
			$sBody = Encoding::convert($this->m_sBody, $this->m_sCharset);

		
		$sHeaders = '';
		foreach($aHeaders as $sKey => $sValue) {
			$sHeaders .= "$sKey: $sValue\n";
		}
		
		return array(implode( ", ", $this->m_aSendTo), $this->m_sSubject, $this->m_sBody, $sHeaders);
	}

	/**
	 * Format and send the mail.
	 */
	public function send() {
		list($sTo, $sSubject, $sContent, $sHeaders) = $this->buildMail();
		$bResult = @mail($sTo, $sSubject, $sContent, $sHeaders);
	}

	/**
	 * Return the whole e-mail , headers + message.
	 * Can be used for displaying the message in plain text or logging it
	 * 
	 * @return string
	 */
	function get() {
		list($sTo, $sSubject, $sContent, $sHeaders) = $this->buildMail();
		return "To: $sTo\nSubject: $sSubject\n\n$sHeaders\n\n$sContent";
	}

	/**
	 * Check validity of email addresses.
	 * 
	 * @param string|array $mAdresses
	 * @return if unvalid, output an error message and exit, this may -should- be customized
	 */
	public function checkAdresses($mAdresses){
		if(!is_array($mAdresses)) return is_email($mAdresses);
		foreach($mAdresses as $sAdress) {
			if(!is_email($sAdress)) {
				return false;
			}
		}
		return true;
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