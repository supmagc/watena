<?php

abstract class UserConnectionProvider {
	
	/**
	 * Helper method to identify the connectionprovider.
	 * 
	 * @return string The name of the connection-provider-class.
	 */
	public function getName() {
		return get_class($this);
	}
	
	/**
	 * Update the userdata according to the current connection.
	 * This method is meant to update the userdata such:
	 * - locale (if not set)
	 * - timezone (if not set)
	 * - birthday (if not set)
	 * - add email adresses
	 * 
	 * This method can only be run when isConnected() returns true.
	 * And when getConnectionData() returns valid data.
	 * 
	 * @param User $oUser The user to be updated.
	 * @param bool $bForceOverwrite Indicate if we should overwrite current settings.
	 * @return bool Returns true when the update succeeded.
	 */
	abstract public function update(User $oUser, $bForceOverwrite = false);

	/**
	 * Retrieve the user-id linked to the connection.
	 * If no connection is found this method returns false.
	 * 
	 * @return int|false The internal connection's user-id, or false when not connected.
	 */
	abstract public function getConnectionId();
	
	/**
	 * Retrieve the username linked to the connection.
	 * If no connection is found this method returns false.
	 *
	 * @return string|false The internal connection's username, or false when not connected.
	 */
	abstract public function getConnectionName();
	
	/**
	 * Retrieve the email-adress linked to the connection.
	 * If no connection is found this method returns false.
	 * Some connections may not provide this (ex: Twitter)
	 *
	 * @return string|false The internal connection's username, or false when not connected, or not provided.
	 */
	abstract public function getConnectionEmail();
	
	/**
	 * Retrieve any userdata linked to the connection.
	 * This might also include the user-id.
	 * This data is connection-specific, and should not 
	 * be relied upon outside of the connectionprovider.
	 * If no connection is found this method returns false.
	 * 
	 * @return array|false The internal connection's user-data, or false when not connected.
	 */
	abstract public function getConnectionData();
	
	/**
	 * Retrieve the tokens required to make requests to the 
	 * connection-provider on the user's behalve.
	 * This data is connection pecific and not guaranteed to exists
	 * or to stay valid outside the user's session.
	 * If no connection is found this method returns false.
	 * 
	 * @return mixed|false The internal connection's user-tokens, or false when not connected.
	 */
	abstract public function getConnectionTokens();

	/**
	 * Since connectionproviders use an external system to verify the user's
	 * linkage to other sites every login procedure starts with a redirect.
	 * This method returns the url to break the connection.
	 * 
	 * If a disconnect option is not part of the underlying provider,
	 * this method returns null;
	 * 
	 * @param string $sRedirect A Fully qualified url to which the user will be redirected at the end of the procedure.
	 * @return string|false A string with the correct url on succes, false on failure, null if the operation is not possible.
	 */
	abstract public function getDisconnectUrl($sRedirect);
	
	/**
	 * Since connectionproviders use an external system to verify the user's
	 * linkage to other sites every login procedure starts with a redirect.
	 * This method returns the url to create the connection.
	 * 
	 * @param string $sRedirect A Fully qualified url to which the user will be redirected at the end of the procedure.
	 * @param string $sScope An optional list to pass on during authentication, identifying the required permission scope.
	 * @return string|false A string with the correct url on succes, false on failure.
	 */
	abstract public function getConnectUrl($sRedirect, $sScope = null);
	
	/**
	 * Check if the underlying provider had a valid connection.
	 * Even when a connection is found, there is no guarantee it's connected to the logged-in user.
	 * When creating a connection, attention should be given to assure linkage to the correct user.
	 * 
	 * @return bool True when connected, false when not connected.
	 */
	abstract public function isConnected();
	
	/**
	 * Thid method should be called on the disconnect-redirect-page.
	 * It's meant to validate the last part of the disconnect-procedure.
	 * 
	 * @return bool True when the procedure succesfylly completed.
	 */
	abstract public function disconnect();
	
	/**
	 * Thid method should be called on the connect-redirect-page.
	 * It's meant to validate the last part of the connect-procedure.
	 * 
	 * @return bool True when the procedure succesfylly completed.
	 */
	abstract public function connect();
}

?>