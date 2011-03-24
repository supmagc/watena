<?php

/**
 * HdItem: abstract class, super class of TMD_DIRECTORY and TMD_FILE
 * 
 * @author Voet Jelle [ToMo-design.be]
 * @version 1.1.2 RC2
 *
 */
abstract class TMD_HdItem {
	
	/**
	 * Contains a path
	 *
	 * @var TMD_Directory
	 */
	protected $path;
	protected $name;
	
	protected function __construct($path, $name) {
		$this->path = $path;
		//if($path !== null) {
			/*while(strstr($this->path, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR)) {
				$this->path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $this->path);
			}*/
			if(Encoding::endsWith($this->path, DIRECTORY_SEPARATOR)) {
				$this->path = Encoding::substring($this->path, 0, Encoding::length($this->path) - 1);
			}
		//}
		//if($name !== false && !$name) {
		//	$this->name = "/";
		//}
		//else {
			$this->name = $name; 
		//}
	}
	
	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function getPathAsString() {
		return $this->path;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return TMD_Directory
	 */
	public function getPathAsObject() {
		return TMD_Directory::createFromPath($this->getPathAsString());
	}
	
	public function getName() {
		return $this->name;
	}
		
	public function isInRoot() {
		return /*$this->path === null || */$this->path->isRoot();
	}
	
	public function isWriteable() {
		return $this->exists() && is_writeable($this->getTotalPath());
	}
	
	public function isReadable() {
		return $this->exists() && is_readable($this->getTotalPath());
	}
	
	public function isExecutable() {
		return $this->exists() && is_executable($this->getTotalPath());
	}
	
	/**
	 * Try to change the mode to the according Chmod class
	 *
	 * @param int $mode
	 * @return bool
	 */
	public function chmod(TMD_Chmod $mode = null) {
		if($mode === null) trigger_error("Invalid mode-object", E_USER_ERROR);
		return $this->Exists() && @chmod($this->GetTotalPath(), $mode->getMode());
	}
	
	abstract public function exists();
	abstract public function isValidName();
	abstract public function getTotalPath();
	abstract public function copy(TMD_Directory $destination = null, $makePath = true);
	abstract public function delete();
}
?>