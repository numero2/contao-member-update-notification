<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package member_update_notification
 * @author Benny Born <benny.born@numero2.de>
 * @copyright numero2 - Agentur für Internetdienstleistungen
 * @license Commercial
 */


// add save_callback to email field because it's most likely updateable from frontend all the time
$GLOBALS['TL_DCA']['tl_member']['fields']['email']['save_callback'][] =  array('tl_member_update_notification', 'updateUserData');


class tl_member_update_notification extends Backend {


	/**
	 * Send mail notification to administrator which fields has been updated
	 * @param string
	 * @param object
	 * @return string
	 */
	public function updateUserData( $fieldVal, $user ) {

		// return if there is no user (e.g. upon registration)
		if( !$user || TL_MODE != 'FE' ) {
			return $fieldVal;
		}

		// find changed fields
		$changedFields = array();

		foreach( $_POST as $fieldName => $fVal ) {

            if( empty($GLOBALS['TL_DCA']['tl_member']['fields'][$fieldName]) )
                continue;

            $fieldConfig = $GLOBALS['TL_DCA']['tl_member']['fields'][$fieldName];

			$newValue = Input::post($fieldName);

			if( !$fieldConfig['eval']['feEditable'] || in_array($fieldName,array('groups','password')) )
				continue;

			if( !$newValue )
				continue;

			$label = ($GLOBALS['TL_LANG']['tl_member'][$fieldName][0]) ? $GLOBALS['TL_LANG']['tl_member'][$fieldName][0] : $fieldName;

			if( !empty($fieldConfig['options']) ) {

                if( !empty($fieldConfig['eval']['multiple']) && is_array($newValue) ) {

                    foreach( $newValue as $v ) {

                        if( !empty($fieldConfig['reference']) ) {
                            $val = $fieldConfig['reference'][ $v ];
                        } else {
                            $val = $fieldConfig['options'][ $v ];
                        }

                        $changedFields[$label] .= $val.', ';
                    }

                } else {

                    if( !empty($fieldConfig['reference']) ) {
                        $changedFields[$label] = $fieldConfig['reference'][ $newValue ];
                    } else {
                        $changedFields[$label] = $fieldConfig['options'][ $newValue ];
                    }
                }

			} else {
				$changedFields[$label] = $newValue;
			}
		}

		if( empty($changedFields) )
			return $fieldVal;

		// prepare mail template
		$tempHTML = new \FrontendTemplate('member_update_notification_mail_html');
		$tempText = new \FrontendTemplate('member_update_notification_mail_text');

		$tempHTML->name = $tempText->name = $user->firstname.' '.$user->lastname;
		$tempHTML->fields = $tempText->fields = $changedFields;

		// send mail
		$objEmail = new \Email();

		$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
		$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
		$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['member_update_notification']['subject'], \Environment::get('host'));
		$objEmail->text = $tempText->parse();
		$objEmail->html = $tempHTML->parse();

		$objEmail->sendTo($GLOBALS['TL_ADMIN_EMAIL']);

		return $fieldVal;
	}
}

?>
