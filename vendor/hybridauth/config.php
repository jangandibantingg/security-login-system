<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------
return
	array(
		"base_url" => SOCIAL_CALLBACK_URI,

		"providers" => array ( 
			// openid providers
			"OpenID" => array (
				"enabled" => false
			),

			"Yahoo" => array ( 
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ),
			),

			"AOL"  => array ( 
				"enabled" => false 
			),

			"Google" => array ( 
				"enabled" => GOOGLE_ENABLED,
				"keys"    => array ( "id" => GOOGLE_ID, "secret" => GOOGLE_SECRET ), 
			),

			"Facebook" => array ( 
				"enabled" => FACEBOOK_ENABLED,
				"keys"    => array ( "id" => FACEBOOK_ID, "secret" => FACEBOOK_SECRET ), 
			),

			"Twitter" => array ( 
				"enabled" => TWITTER_ENABLED,
				"keys"    => array ( "key" => TWITTER_KEY, "secret" => TWITTER_SECRET ) 
			),

			// windows live
			"Live" => array ( 
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ) 
			),

			"MySpace" => array ( 
				"enabled" => false,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			),

			"LinkedIn" => array ( 
				"enabled" => false,
				"keys"    => array ( "key" => "", "secret" => "" ) 
			),

			"Foursquare" => array (
				"enabled" => false,
				"keys"    => array ( "id" => "", "secret" => "" ) 
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => "",
	);
