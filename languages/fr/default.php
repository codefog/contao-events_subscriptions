<?php

/**
 * FR Translation for events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @author    Web ex Machina <https://www.webexmachina.fr>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribe']               = 'Inscription';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribe']             = 'Désinscription';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation']   = 'Vous vous êtes inscrit à cet événément.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeConfirmation'] = 'Vous vous êtes désinscrit de cet événément.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeNotAllowed']     = 'Il n\'est plus possible de s\'inscrire à cet événément.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.unsubscribeNotAllowed']   = 'Il n\'est plus possible de se désinscrire de cet événément.';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.onWaitingList']           = 'sur la liste d\'attente';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname']     = 'Prénom';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname']      = 'Nom';
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email']         = 'Adresse email';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['events_subscriptions.memberAlreadySubscribed'] = 'Le membre ID %s a déjà été inscrit à cet événement!';

/**
 * Export
 */
$GLOBALS['TL_LANG']['MSC']['events_subscriptions.exportHeaderFields'] = [
    'event_id'                 => 'ID de l\'événement',
    'event_title'              => 'Intitulé de l\'événement',
    'event_start'              => 'Début de l\'événement',
    'event_end'                => 'Fin de l\'événement',
    'subscription_type'        => 'Type d\'inscription',
    'subscription_waitingList' => 'Liste d\'attente',
    'subscription_firstname'   => 'Prénom',
    'subscription_lastname'    => 'Nom',
    'subscription_email'       => 'Adresse email',
];

$GLOBALS['TL_LANG']['MSC']['events_subscriptions.memberExportHeaderFields'] = [
    'member_id'       => 'ID du membre',
    'member_username' => 'Identifiant du membre',
];
