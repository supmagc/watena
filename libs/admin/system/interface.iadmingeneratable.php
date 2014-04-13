<?php

interface IAdminGeneratable {
	
	public function generate(AdminModuleContentRequest $oRequest, AdminModuleContentResponse $oResponse);
}

?>