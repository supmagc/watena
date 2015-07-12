<?php namespace Watena\Core;

/**
 * Static class that manages the cross library/plugins/models/... callbacks.
 * Any object can register itselfs for some specific event(s).
 * These events can be invoked with a custom set of parameters, and will
 * be triggered on the arlier provided callbacks.
 * 
 * This enables a basic plugin functionality without the caller needing to know 
 * about the callee(s) and their implementations.
 * 
 * Changelog:
 * 0.1.0 1/11/14
 * - Initial version
 * 
 * @author Jelle Voet
 * @version 0.1.0
 */
class Events {
	
	private static $s_aEventsCallbacks = array();

	/**
	 * Invoke all the functions matching the named event.
	 * 
	 * @param string $sEvent
	 * @param array $aParameters
	 * @throws EventCallbackException
	 */
	public final static function invoke($sEvent, array $aParameters = array()) {
		$sEventLower = Encoding::toLower($sEvent);
		
		if(isset(self::$s_aEventsCallbacks[$sEventLower])) {
			foreach(self::$s_aEventsCallbacks[$sEventLower] as $cbEvent) {
				try {
					call_user_func_array($cbEvent, $aParameters);
				}
				catch(Exception $e) {
					throw new EventCallbackException($sEvent, $cbEvent, $e);
				}
			}
		}
	}
	
	/**
	 * Register the given callback to the given event.
	 * 
	 * @param string $sEvent
	 * @param callback $cbEvent
	 */
	public final static function registerEventCallback($sEvent, $cbEvent) {
		$sEventLower = Encoding::toLower($sEvent);
		if(!is_callable($cbEvent)) {
			Logger::getInstance('Events')->warning("Unable to register un-callable function {callback} for event {event}.", array('event' => $sEvent, 'callback' => $cbEvent));
		}
		else {
			if(!isset(self::$s_aEventsCallbacks[$sEventLower])) {
				self::$s_aEventsCallbacks[$sEventLower] = array();
			}
			
			self::$s_aEventsCallbacks[$sEventLower] []= $cbEvent;
		}
	} 
	
	/**
	 * Retrieve a list with all registered events.
	 * 
	 * @return array
	 */
	public final static function getRegisteredEvents() {
		return array_keys(self::$s_aEventsCallbacks);
	}
	
	/**
	 * Retrieve a list with all the registered callbacks for the given event. (if any)
	 * 
	 * @param string $sEvent
	 * @return array
	 */
	public final static function getEventCallbacks($sEvent) {
		$sEventLower = Encoding::toLower($sEvent);
		return isset(self::$s_aEventsCallbacks[$sEventLower]) ? self::$s_aEventsCallbacks[$sEventLower] : array();
	}
}
