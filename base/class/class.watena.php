<?php

class Watena extends Object {
	
	private $m_oContext = null;
	private $m_oCache = null;
	private $m_oMapping = null;
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	private $m_oOutput = null;
	private $m_oTime = null;
	private $m_bDebug = false;
	private $m_oConfig;
	
	public function __construct(WatenaConfig $oConfig) {	
		ob_start();
		$nTime = microtime(true);	
		parent::__construct();
		$this->m_oConfig = $oConfig;
		
		// Load debug flag
		$this->m_bDebug = $oConfig->debugDefault();
		if($oConfig->debugDefine() && defined('DEBUG')) {
			$this->m_bDebug |= (bool)DEBUG;
		}
		if($oConfig->debugSession() && isset($_SESSION['DEBUG'])) {
			$this->m_bDebug |= (bool)$_SESSION['DEBUG'];
		}
		if($oConfig->debugDefine() && isset($_REQUEST['DEBUG'])) {
			$this->m_bDebug |= (bool)$_REQUEST['DEBUG'];
		}
		function isDebug() {return watena()->isDebug();}
		
		// Init some static classes
		Encoding::init($this->getConfig()->charset());
		Request::init();
		Time::init($this->getConfig()->timeZone(), $this->getConfig()->timeFormat());
				
		// Create a default context and default cache
		$this->m_oTime = new Time();
		$this->m_oCache = new CacheEmpty();
		$this->m_oOutput = new OutputControl();
		$this->m_oContext = new Context();
		$this->m_oMapping = Mapping::LoadFromRequest();
		
		// Load the required libraries
		$this->m_oContext->loadLibraries($this->getConfig()->libraries());
		
		// Load all specified logProcessors
		Logger::setDefaultFilterLevel($this->getConfig()->loggerLevel());
		$aLogProcessors = $this->getConfig()->loggerProcessors();
		foreach($aLogProcessors as $sProcessor) {
			if($this->m_oContext->loadPlugin($sProcessor)) {
				$oProcessor = $this->m_oContext->getPlugin($sProcessor, 'ILogProcessor');
				Logger::registerProcessor($oProcessor);
			}
		}
				
		// Load the specified cachingengine
		$sCachePlugin = $this->getConfig()->cacheEngine();
		if($sCachePlugin) {
			$this->m_oContext->loadPlugin($sCachePlugin);
			$this->m_oCache = $this->m_oContext->GetPlugin($sCachePlugin, 'ICache');
		}
		
		// Log the end of the init
		$this->getLogger()->info('Watena was succesfully initialised in {time} sec.', array('time' => round(microtime(true) - $nTime, 5)));
	}
	
	public final function isDebug() {
		return $this->m_bDebug;
	}
	
	public final function mvc() {
		$nTime = microtime(true);
		$bCached = false;
		
		// Load the mapping and retrieve the appropriate controller
		list($this->m_oModel, $this->m_oView, $this->m_oController) = $this->m_oContext->getMVC($this->m_oMapping);
		while($this->m_oController instanceof Controller) {
			$this->m_oController->clearRemapping();
			$this->m_oController->clearNewModel();
			$this->m_oController->clearNewView();
			
			$sRequiredModelType = $this->m_oController->requiredModelType();
			$sRequiredViewType = $this->m_oController->requiredViewType();
			if(!empty($sRequiredModelType) && !empty($this->m_oModel) && !($this->m_oModel instanceof $sRequiredModelType)) {
				$this->getLogger()->error("Model is required to be of type {type_correct} instead of {type_wrong} as indicated by {controller}.", array(
					'type_correct' => $sRequiredModelType,
					'type_wrong' => $this->m_oModel->toString(),
					'controller' => $this->m_oController->toString(),
				));
			}
			if(!empty($sRequiredViewType) && !empty($this->m_oView) && !($this->m_oView instanceof $sRequiredViewType)) {
				$this->getLogger()->error("View is required to be of type {type_correct} instead of {type_wrong} as indicated by {controller}.", array(
					'type_correct' => $sRequiredViewType,
					'type_wrong' => $this->m_oView->toString(),
					'controller' => $this->m_oController->toString(),
				));
			}
				
			$this->m_oController->process($this->m_oModel, $this->m_oView);
			if($this->m_oController->hasRemapping()) {
				$this->m_oMapping = $this->m_oController->getRemapping();
				list($this->m_oModel, $this->m_oView, $this->m_oController) = $this->m_oContext->getMVC($this->m_oMapping);
			}
			if($this->m_oController->hasNewModel()) {
				$this->m_oModel = $this->m_oController->getNewModel();
			}
			if($this->m_oController->hasNewView()) {
				$this->m_oView = $this->m_oController->getNewView();
			}
			if(!$this->m_oController->hasRemapping()) {
				break;
			}
		}
		
		// Check cache validation if we have a model
		if(null === $this->m_oModel || !$this->m_oOutput->validateCache($this->m_oModel)) {
			if($this->m_oView instanceof View) {
				$sRequiredModelType = $this->m_oView->requiredModelType();
				if(!empty($sRequiredModelType) && !($this->m_oModel instanceof $sRequiredModelType)) {
					$this->getLogger()->error("Model is required to be of type {type_correct} instead of {type_wrong} as indicated by {view}.", array(
							'type_correct' => $sRequiredModelType,
							'type_wrong' => $this->m_oModel ? $this->m_oModel->toString() : 'null',
							'view' => $this->m_oView ? $this->m_oView->toString() : 'null'
					));
				}
					
				// Sent headers
				$this->m_oView->headers($this->m_oModel);
			}
			ob_end_flush();
			
			// Try output compression
			$this->m_oOutput->validateCompression($this->m_oModel);

			// Final rendering
			if($this->m_oView instanceof View && !$bCached)
				$this->m_oView->render($this->m_oModel);
		}
	
		// Log the end of Watena
		$this->getLogger()->debug('Watena loaded and rendered the page in {time} sec.', array('time' => round(microtime(true) - $nTime, 5)));
	}
	
	/**
	 * @return WatenaConfig
	 */
	public final function getConfig() {
		return $this->m_oConfig;
	}

	/**
	 * Retrieve the application context
	 * 
	 * @return Context
	 */
	public final function getContext() {
		return $this->m_oContext;
	}
	
	/**
	 * Retrieve the current output-control.
	 * 
	 * @return OutputControl
	 */
	public final function getOutputControl() {
		return $this->m_oOutput;
	}
	
	/**
	 * Retrieve a valid watena/system path.
	 * The input path should specify a protocol such as:
	 * r (or R, or root) => for Root
	 * d (or D, or data) => for Data
	 * l (or L, or libs) => for Libs
	 * ex: data:/HelloWorld/data.txt
	 * Or '@' use the library specifier:
	 * ex library@folder/stuff.txt
	 * 
	 * @param string $sPath The path to resolve.
	 * @param bool $bVerify Indicate if you cant the path to be verified for existance (returns false on failure)
	 * @return string|bool
	 */
	public final function getPath($sPath, $bVerify = true) {
		$aMatches = array();
		$aPositions = array();
		// TODO: optimize with indexOf
		if(Encoding::regFind('^([rdl]|root|data|libs)[:/\\\\]([^:/\\\\].*?)/?$', '' . $sPath, $aMatches, $aPositions, 'msri')) {
			switch($aMatches[1]) {
				case 'd' :
				case 'D' : $sPath = PATH_DATA . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : ''); break;
				case 'r' :
				case 'R' : $sPath = PATH_ROOT . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : ''); break;
				case 'l' :
				case 'L' : $sPath = PATH_LIBS . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : ''); break;
			}
		}
		else if(Encoding::regFind('^([-a-z0-9_. ]+)@([^:/\\\\].*?)/?$', '' . $sPath, $aMatches, $aPositions, 'msri')) {
			$sPath = PATH_LIBS . "/$aMatches[1]" . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : '');
		}
		if($bVerify) {
			return realpath($sPath);
		}
		else {
			$sPath = Encoding::replace('\\', '/', $sPath);	
			$sPath = Encoding::regReplaceAll('/[^/]+/\.\./', '/', $sPath);
			return $sPath;
		}
	}
	
	/**
	 * Retrieve the mapping for the current request.
	 * 
	 * @return Mapping
	 */
	public final function getMapping() {
		return $this->m_oMapping;
	}
	
	/**
	 * String based representation of the version
	 * 
	 * @return string
	 */
	public final function getVersion() {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 0,
			'state' => 'dev'
		);
	}

	/**
	 * The time object from watena as system time
	 * 
	 * @return Time
	 */
	public final function getTime() {
		return $this->m_oTime;
	}
	
	/**
	 * Retrieve the caching engine.
	 * 
	 * @return ICache
	 */
	public final function getCache() {
		return $this->m_oCache;
	}

	/**
	 * Retrieve the model part of the MVC
	 * 
	 * @return Model
	 */
	public final function getModel() {
		return $this->m_oModel;
	}

	/**
	 * Retrieve the view part of the MVC
	 * 
	 * @return View
	 */
	public final function getView() {
		return $this->m_oView;
	}

	/**
	 * Return the controller part of the MVC
	 * 
	 * @return Controller
	 */
	public final function getController() {
		return $this->m_oController;
	}
	
	/**
	 * Check some install prerequisites
	 */
	public final function validate() {
		// Check data folder
		if(!is_writable(PATH_DATA)) {
			$this->getLogger()->warning("The data folder '".PATH_DATA."' should be writable.");
		}
		
		// Check compression and zlib settings
		if($this->getConfig()->compression()) {
			$bZlibIni = ini_get('zlib.output_compression');
			if($bZlibIni && Encoding::toLower($bZlibIni) !== "off") {
				$this->getLogger()->warning("Global compression can not be enabled when 'zlib.output_compression' is true.");
			}
		}
	}
}

?>
