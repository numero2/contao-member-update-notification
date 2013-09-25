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

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'member_update_notification_mail_html' => 'system/modules/member_update_notification/templates/member'
,	'member_update_notification_mail_text' => 'system/modules/member_update_notification/templates/member'
));

?>