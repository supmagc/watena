<?php

interface IContainerItem {

	public function getKeyForContainer(Container $oContainer);
	public function addToContainer(Container $oContainer);
	public function removeFromContainer(Container $oContainer);
	public function removeFromAllContainers();
}
