<?php namespace Watena\Core;

interface IContainerItem {

	public function getKeyForContainer(Container $oContainer);
	public function addedToContainer(Container $oContainer);
	public function removedFromContainer(Container $oContainer);
	public function removeFromAllContainers();
}
