<?php

/**
 * Class representing (non)-virtual files on fileSystems
 * extends TMD_HdItem
 *
 * @author Voet Jelle [ToMo-design.be]
 * @version 1.1.6 RC3
 */
class TMD_File extends TMD_HdItem {
	
	protected $extention;
	protected $content;
	
	public function __construct($path = null, $name = null, $extention = "") {
		parent::__construct($path, $name);
		$this->extention = $extention;
	}
	
	public function exists() {
		return file_exists($this->getTotalPath());
	}
	
	public function getTotalPath() {
		return $this->getPathAsObject()->getTotalPath() . 
			(TMD_HdItem::$USE_SLASHES ? (DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : DIRECTORY_SEPARATOR) . 
			$this->name . "." . $this->extention;
	}
	
	public function getExtention() {
		return $this->extention;
	}
	
	public function getFileName() {
		return parent::getName() . '.' . $this->getExtention();
	}
	
	/**
	 * Return the content of the file that's included in this object
	 * If the file hasn't been read before, this will be done now
	 *
	 * @return string
	 */
	public function getContent() {
		if(empty($this->content))
			$this->readContent();
		return $this->content;
	}
	
	/**
	 * Set the active content, returns the same content
	 *
	 * @param string $content
	 * @return string $content
	 */
	public function setContent($content = "") {
		return $this->content = $content;
	}
	
	public function addStartContent($content = "") {
		return $this->content = $content . $this->getContent();
	}
	
	public function addEndContent($content = "") {
		return $this->content = $this->getContent() . $content;
	}
	
	/**
	 * Read the content from a file, and returns that it if possible
	 *
	 * @param int $offset
	 * @param int $length
	 * @return mixed false if error, content on success
	 */
	public function readContent($offset = false, $length = false) {
		if(false === $offset) $offset = 0;
		$tmp = false === $length ? 
			@file_get_contents($this->getTotalPath(), false, null, $offset) :
			@file_get_contents($this->getTotalPath(), false, null, $offset, $length);
		if($this->isValidName() && false !== $tmp) {
			return $this->setContent($tmp);
		}
		else {
			return false;
		}
	}
	
	/**
	 * Write Content to the filesystem (if possible)
	 *
	 * @param bool $makePath
	 * @return bool
	 */
	public function writeContent($makePath = true) {
		if($this->isValidName()) {
			if($makePath && !$this->getPathAsObject()->exists()) {
				$this->getPathAsObject()->makeDirectory(true);
			}	
			return $this->getPathAsObject()->exists() && @file_put_contents($this->getTotalPath(), $this->getContent());
		}
		return false;
	}
	
	/**
	 * Actual filesize if the file exists, otherwise zero
	 *
	 * @return bool
	 */
	public function getFileSize() {
		return $this->exists() ? @filesize($this->getTotalPath()) : 0;
	}
	
	public function getContentLength() {
		return strlen($this->getContent());
	}
	
	/**
	 * Try to copy this file with the same name and extension to the given directory
	 * by creating a new file-object, and calling the CopyToFile function
	 * When copyying this file will be copied directly into the given directory
	 *
	 * @param TMD_Directory $destination
	 * @param bool $makePath
	 * @return bool (result from CopyToFile)
	 */
	public function copy(TMD_Directory $destination = null, $makePath = true) {
		return $this->copyToFile(new TMD_File($destination->getTotalPath(), $this->getName(), $this->getExtention()), $makePath);
	}
	
	/**
	 * Try to copy a file if it exists, and if the destination-name is valid
	 * If this file does not exists, YOU need to write it YOURSELF
	 * This function will try to create the target-directory (if needed), and pass the makePath-bool
	 *
	 * @param TMD_File $destination
	 * @param bool $makePath
	 * @return bool
	 */
	public function copyToFile(TMD_File $destination = null, $makePath = true) {
		if($this->exists() && $destination->isValidName() && ($destination->getPathAsObject()->exists() || $makePath && $destination->getPathAsObject()->makeDirectory($makePath))) {
			return @copy($this->getTotalPath(), $destination->getTotalPath());
		}
		return false;
	}
	
	/**
	 * Try to delete a file, but surpress error-messages
	 * return-value is the result of the PHP @unlink()
	 *
	 * @return bool
	 */
	public function delete() {
		return @unlink($this->getTotalPath());
	}
	
	/**
	 * Try to rename this file
	 *
	 * @param string $name
	 * @param bool $virtual Only rename the object (and not the real file)
	 * @return bool
	 */
	public function rename($name = "", $virtual = false) {
		if(TMD_File::isValidName($name)) {
			$result = true;
			if(!$virtual) {
				$result = rename($this->getTotalPath(), $this->getPathAsString() . 
					(TMD_HdItem::$USE_SLASHES ? (DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR) : DIRECTORY_SEPARATOR) . 
					$this->getName() . "." . $this->getExtention());
			}
			if($result) {
				$this->name = $name;
			}
			return $result;
		}
		return false;
	}
	
	/**
	 * Check if the name contained for this object is valid
	 * (meaning it validates following REGEX/i ^[-a-z0-9_. ]+$)
	 *
	 * @return bool
	 */
	public function isValidName() {
		return TMD_File::isValidFileName($this->getName());
	}
	
	/**
	 * Create a new instance of the TMD_File class by the given object
	 * including a copy of the file-object-content
	 *
	 * @param TMD_File $object
	 * @return TMD_File
	 */
	public static function createFromObject(TMD_File $object = null) {
		$tmp = new TMD_File($object->getPath()->getTotalPath(), $object->getName(), $object->getExtention());
		$tmp->setContent($object->getContent());
		return $tmp;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param TMD_Directory $object
	 * @param unknown_type $sFilename
	 * @param unknown_type $sExtension
	 * @return TMD_File
	 */
	public static function createInDirectory(TMD_Directory $object = null, $sFilename = '', $sExtension = '') {
		return new TMD_File($object->getTotalPath(), $sFilename, $sExtension);
	}
	
	/**
	 * Create a new instance of the TMD_File class by the given path
	 * if USE_SLASHES are set, this method will first secure the path, but stripping the slashes afterwards
	 * (makes use of pathinfo())
	 *
	 * @param String $path
	 * @return TMD_File
	 */
	public static function createFromPath($path = "") {
		$info = pathinfo(TMD_HdItem::$USE_SLASHES ? addslashes($path) : $path);
		if(!isset($info['dirname']) || !isset($info['filename']) || !isset($info['extension'])) {throw new Exception('Problem creating new file-object, invalid path:' . $path);}
		return new TMD_File(TMD_HdItem::$USE_SLASHES ? stripslashes($info['dirname']) : stripslashes($info['dirname']), $info['filename'], $info['extension']);
	}
	
	/**
	 * Check if the given name is valid
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function isValidFileName($name = "") {
		return eregi('^[-a-z0-9_. ]+$', $name);
	}
}
?>