<?php

	abstract class Session extends Kohana_Session {
		
		protected $flash_expire = array();
		
		public function set_flash( $key, $value ) {
			
			return $this->set( 'flash::' . $key, $value );
			
		}
		
		public function get_flash( $key, $default = null ) {
			
			return $this->get( 'flash::' . $key, $default );
			
		}
		
		public function __construct(array $config = NULL, $id = NULL) {
			
			parent::__construct( $config, $id );
						
			foreach ( $this->_data as $key => $value ) {
				
				if ( UTF8::substr( $key, 0, UTF8::strlen('flash::') ) == 'flash::' ) {
					
					// trim off the flash:: prefix, we store them as blank keys
					$flash_key = UTF8::substr( $key, UTF8::strlen('flash::') );
					
					// save it to the list of flashes to expire
					$this->flash_expire[ $flash_key ] = $flash_key;
										
				} 
				
			}
			
		}
		
		public function write ( ) {
			
			// expire flash data before we write the session
			$this->expire_flash();
			
			// do whatever else we would have done
			return parent::write();
			
		}
		
		public function expire_flash ( ) {

			foreach ( $this->flash_expire as $key ) {
				
				$this->delete( 'flash::' . $key );
				
			}
			
		}
		
		/**
		 * Keep one, multiple, or all flash variables.
		 * 
		 * Normally flash values are only around for a single request. Sometimes this is
		 * not desirable. For example, on an AJAX request you may not wish to expire
		 * flash values - they are generally used to display user confirmations.
		 * 
		 * In those cases, you can preserve any flash values for another request.
		 * 
		 * // don't delete message1 on this request
		 * $session->keep_flash( 'message1' );
		 * 
		 * // don't delete message1 or message2 on this request
		 * $session->keep_flash( 'message1', 'message2' );
		 * 
		 * // don't delete any flash values on this request
		 * $session->keep_flash();
		 * 
		 * @param $keys Any number of keys to keep or null for all.
		 */
		public function keep_flash ( $keys = null ) {
			
			if ( $keys == null ) {
				$keys = $this->flash_expire;
			}
			else {
				$keys = func_get_args();
			}
						
			foreach ( $keys as $key ) {
				
				// remove the key from the list of flash values to expire when the session is saved
				unset( $this->flash_expire[ $key ] );
				
			}
						
		}
		
	}
	
?>