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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_override']           = [
    'Modifier les paramètres d\'inscription',
    'Cocher pour modifier les paramètres généraux utilisés dans la configuration du calendrier.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_types']              = [
    'Types d\'inscription autorisées',
    'Choisir les types d\'inscription autorisées.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_maximum']            = [
    'Nombre maximum d\'inscriptions',
    'Indiquer si souhaité un nombre d\'inscription maximum par événement. Indiquer 0 pour désactiver la limite.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_subscribeEndTime']   = [
    'Fin de la période d\'inscription',
    'Indiquer si souhaité le temps d\'inscription limite précédent le début de l\'événement. Laisser vide pour ne pas limiter.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_unsubscribeEndTime'] = [
    'Fin de la période de désinscription',
    'Indiquer si souhaité le temps de désinscription limite précédent le début de l\'événement Laisser vide pour ne pas limiter.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingList']        = [
    'Activer la liste d\'attente',
    'Autoriser les utilisateurs à s\'inscrire sur une liste d\'attente.',
];
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_waitingListLimit']   = [
    'Limiter la liste d\'attente',
    'Indiquer si souhaité une limite d\'inscriptions pour la liste d\'attente. Indiquer 0 pour désactiver la limite.',
];

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_legend'] = 'paramètres d\'inscription';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscriptions'] = [
    'Inscriptions',
    'Afficher les inscriptions de l\'événement ID %s',
];

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_calendar_events']['subscription_timeRef'] = [
    'seconds' => 'seconde(s)',
    'minutes' => 'minute(s)',
    'hours'   => 'heure(s)',
    'days'    => 'jour(s)',
    'weeks'   => 'semaine(s)',
    'months'  => 'mois',
    'years'   => 'année(s)',
];