<?php

/**
 * Class representing (non)-virtual directories on fileSystems
 * extends TMD_HdItem
 *
 * @author Voet Jelle [ToMo-design.be]
 * @version 1.0.3 RC2
 */
class TMD_Directory extends TMD_HdItem {	
	
	protected $filter;
	protected $recursiveCount;
	
	public function __construct($path = null, $name = "") {
		parent::__construct($path, $name);
		$this->filter = array();
	}
	
	public function makeDirectory($makePath = true) {
		if($this->exists()) {
			return true;
		}
		return !$this->isRoot() && @mkdir($this->getTotalPath(), 0777, $makePath);
	}
	
	public function setExtFilter(Array $filter = array()) {
		$this->filter = $filter;
	}
	
	public function getExtFilter() {
		return $this->filter;
	}
	
	public function getDirectoryList() {
		if(false !== ($list = $this->getItems())) {
			$result = array();
			foreach($list as $item) {
				if($item != "." && $item != ".." && is_dir($this->getTotalPath() . DIRECTORY_SEPARATOR . $item)) {
					$result[] = new TMD_Directory($this->getTotalPath(), $item);
				}
			}
			return $result;
		}
		else {
			return false;
		}
	}
			
	public function getFilteredFileList() {
		if(false !== ($list = $this->getItems())) {
			$result = array();
			foreach($list as $item) {
				if($item != "." && $item != ".." && is_file($this->getTotalPath() . DIRECTORY_SEPARATOR . $item)) {
					$ext = ereg_replace('^(.*)\.([^.]*)$', '\\2', $item);
					$name = ereg_replace('^(.*)\.([^.]*)$', '\\1', $item);
					if(in_array($ext, $this->filter)) {
						$result[] = new TMD_File($this->getTotalPath(), $name, $ext);
					}
				}
			}
			return $result;
		}
		else {
			return false;
		}
	}
	
	public function getUnfilteredFileList() {
		if(false !== ($list = $this->getItems())) {
			$result = array();
			foreach($list as $item) {
				if($item != "." && $item != ".." && is_file($this->getTotalPath() . DIRECTORY_SEPARATOR . $item)) {
					$result[] = TMD_File::createFromPath($this->getTotalPath() . DIRECTORY_SEPARATOR . $item);
				}
			}
			return $result;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Retrieve all the items in this directory as a simple array
	 * This function is equivalent to the scandir function as long as this directory exists
	 *
	 * @return mixed false if directeory does not exists, array if it does
	 */
	public function getItems() {
		if(!$this->exists()) {
			return false;
		}
		return scandir($this->getTotalPath());
	}
	
	/**
	 * Check if this directory is the root of the filrsystem
	 *
	 * @return bool
	 */
	public function isRoot() {
		return $this->path === null && ($this->getName() == "" || eregi('^([A-Z]\:|/)$', $this->getName()));
	}
	
	/**
	 * CHeck if this directory exists
	 *
	 * @return bool
	 */
	public function exists() {
		return @file_exists($this->getTotalPath()) && @is_dir($this->getTotalPath());
	}
	
	/**
	 * retrieve the totalPath to this directory as specified by the fileSystem
	 *
	 * @return String the total path,  NOT ending with a backslash
	 */
	public function getTotalPath() {
		$result = $this->isRoot() ? $this->setName() : ($this->path . DIRECTORY_SEPARATOR . $this->setName());
		return TMD_HdItem::$USE_SLASHES ? addslashes($result) : $result;
	}
	
	/**
	 * Copy this directory (as seen from the parent folder) to the given destination
	 *
	 * @param TMD_Directory $destination
	 * @param bool $makePath
	 * @return bool
	 */
	public function copy(TMD_Directory $destination = null, $makePath = true) {
		if($this->isRoot() || !$this->exists()) {
			return false;
		}
		$tmp = new TMD_Directory($destination->getTotalPath(), $this->getName());
		if(!$tmp->makeDirectory($makePath)) {
			return false;
		}
		return $this->copyContent($tmp, $makePath);
	}
	
	/**
	 * Copy the content of this directory to the given directory
	 *
	 * @param TMD_Directory $destination
	 * @param bool $makePath
	 * @return bool
	 */
	public function copyContent(TMD_Directory  $destination = null, $makePath = true) {
		if(!$this->exists()) {
			return false;
		}
		$dirs = $this->getDirectoryList();
		foreach($dirs as $item) {
			if(!$item->copy($destination, $makePath)) {
				return false;
			}
		}
		$dirs = $this->GetUnfilteredFileList();
		foreach($dirs as $item) {
			if(!$item->copy($destination, $makePath)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Recursivly delete all the items in this directory, and delete this directiry itself
	 *
	 * @return bool
	 */
	public function delete() {
		if($this->isRoot()) {
			return false;
		}
		$dirs = $this->getDirectoryList();
		foreach($dirs as $item) {
			if(!$item->Delete()) {
				return false;
			}
		}
		$dirs = $this->getUnfilteredFileList();
		foreach($dirs as $item) {
			if(!$item->delete()) {
				return false;
			}
		}
		return @rmdir($this->getTotalPath());
	}
	
	/**
	 * Has this directory a valid name
	 *
	 * @return bool
	 */
	public function isValidName() {
		return TMD_Directory::isValidDirectoryName($this->getName());
	}
	
	/**
	 * Create a new TMD_Directory instance based on the given object
	 *
	 * @param TMD_Directory $directory
	 * @return TMD_Directory
	 */
	static public function createFromObject(TMD_Directory $object = null) {
		$tmp = new TMD_Directory($object->getPath()->getTotalPath(), $object->getName());
		$tmp->setExtFilter($object->getExtFilter());
		return $tmp;
	}
	
	/**
	 * Creatre a new TMD_Directory instance based on the given path
	 *
	 * @param String $path
	 * @return TMD_Directory
	 */
	static public function createFromPath($path = "") {
		$info = pathinfo(TMD_HdItem::$USE_SLASHES ? addslashes($path) : $path);
		if(!isset($info['dirname']) || !isset($info['basename'])) {throw new Exception('Problem creating new directoryObject, invalid path:' . $path);}
		return new TMD_Directory(TMD_HdItem::$USE_SLASHES ? stripslashes($info['dirname']) : stripslashes($info['dirname']), $info['basename']);
	}
	
	/**
	 * Check if the given name is valid
	 *
	 * @param string $name
	 * @return bool
	 */
	static public function isValidDirectoryName($name = null) {
		return eregi('^[-a-z0-9_ ]+$', $name);
	}
}
?>