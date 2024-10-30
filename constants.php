<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MoConstants {
	static function langTranslate( $text ) {
		switch ( $text ) {
			case 'Successfully validated.':
				return mo_lt( 'Successfully validated.' );
				break;
			case 'EMAIL_MANDATORY':
				Return mo_lt( 'Please submit your query with email' );
				break;
			case 'ERROR_WHILE_SUBMITTING_QUERY':
				Return mo_lt( 'Your query could not be submitted. Please try again.' );
				break;
			case 'QUERY_SUBMITTED_SUCCESSFULLY':
				Return mo_lt( 'Thanks for getting in touch! We shall get back to you shortly.' );
				break;
			
			default:
				return $text;
		}
	}
}

new MoConstants;
?>