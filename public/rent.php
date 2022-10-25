<?php
/* Copyright (C) 2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *     	\file       htdocs/veloma/public/index.php
 *		\ingroup    core
 */
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
define('NOREQUIREMENU', 1);
define('NOLOGIN', 1);

$res=@include("../../main.inc.php");                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");    // For "custom" directory

require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/CMailFile.class.php';

dol_include_once("/veloma/class/site.class.php");
dol_include_once("/veloma/class/veloma.class.php");
dol_include_once("/veloma/class/veloma.history.class.php");
dol_include_once("/veloma/class/veloma.booking.class.php");

dol_include_once("/stand/class/stand.class.php");

$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');
$mode = GETPOST('mode', 'alpha');
$startDate = GETPOST('start-date', 'int');
$endDate = GETPOST('end-date', 'int');

$start = dol_now();
$end = dol_now();

if ($mode == 'book') {
    $end += 3600;

    if (!empty($startDate)) {
        $start = $startDate;
    }

    if (!empty($endDate)) {
        $end = $endDate;
    }
}


$langs->loadLangs(array('main', 'errors'));
$langs->load('veloma@veloma');
$langs->load("other");

$veloma = new Veloma($db);
$history = new VelomaHistory($db);
$booking = new VelomaBooking($db);

$site = new Site($db);
$site->start($user);

$confirmationModalOpened = false;

$bookings = $booking->liste_array(0, $start, $end, $mode);

$bookingsByBike = array();
if (count($bookings)) {
    foreach ($bookings as $b) {
        if ($b->fk_bike > 0) {
            $bookingsByBike[$b->fk_bike][] = $b;
        }
    }
}

$stand = new Stand($db);
$stands = $stand->liste_array();

$bike = new Bike($db);
$bikes = $bike->liste_array();

$bikesByStand = array();

if (count($bikes)) {
    foreach ($bikes as $bike) {
        if ($bike->fk_stand > 0) {
            if (!isset($bookingsByBike[$bike->id])) {
                $bikesByStand[$bike->fk_stand][] = $bike;
            }
        }
    }
}

$displayConfirm = false;

if ($id > 0) {
   if (isset($bikes[$id]) && !isset($bookingsByBike[$bike->id])) {
       $bike = $bikes[$id];

       if ($bike->active) {
           if ($bike->fk_user > 0) {
               $site->addError($langs->trans('VelomaBikeIsRented'));
           } else {
               if ($action == 'confirm') {
                   $site->register($user, 'confirm');
               }

                if ($user->id > 0) {
                    $credit = !empty($user->array_options['options_veloma_credit']) ? floatval($user->array_options['options_veloma_credit']) : 0;
                    $limit = !empty($user->array_options['options_veloma_limit']) ? intval($user->array_options['options_veloma_limit']) : 0;

                    $rents = $history->getTotalRentsForUser($user->id);

                    if (!empty($conf->global->VELOMA_USE_CREDIT) && $credit < 0) {
                        $site->addError($langs->transnoentities('VelomaUserCreditIsInsufficient'));
                    } else if ($rents >= $limit) {
                        $site->addError($langs->transnoentities('VelomaUserTooManyBikesRented'));
                    } else {
                        if ($mode == 'book') {
                            if (empty($end) || empty($start)) {
                                $site->addError($langs->transnoentities('VelomaDatesAreEmpty'));
                            } else if ($end < $start) {
                                $site->addError($langs->transnoentities('VelomaEndBeforeStart'));
                            } else {
                                $booking = new VelomaBooking($db);
                                $booking->fk_bike = $bike->id;
                                $booking->dates = $start;
                                $booking->datee = $end;
                                $booking->fk_user = $user->id;

                                $booking->create($user);

                                $site->addMessage($langs->trans('VelomaBikeBooked', $bike->ref, dol_print_date($start, 'dayhour'), dol_print_date($end, 'dayhour')));
                            }
                        } else {
                            $bike->fk_user = $user->id;
                            $bike->fk_stand = -1;
                            //$bike->update($user);
                            $site->addMessage($langs->trans('VelomaBikeRented', $bike->code));
                        }

                    }

                    $url = dol_buildpath('/veloma/public/index.php', 1);
                    header("Location: ".$url);		// Default behaviour is redirect to index.php page
                    exit;
                } else {
                    if ($conf->global->VELOMA_ALLOW_UNREGISTERED_USERS) {
                        $displayConfirm = true;
                    } else {
                       $site->addError($langs->trans('VelomaNotLoggedIn'));
                    }
                }
           }
       } else {
           $site->addError($langs->trans('VelomaBikeNotFound'));
       }
   } else {
       $site->addError($langs->trans('VelomaBikeNotFound'));
   }
}

?>

<?php include_once('tpl/layouts/header.tpl.php'); ?>

<div class="px-4 sm:px-8 xl:pr-16">
    <h1 class="text-4xl text-center font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
        <?php if ($mode == 'book'): ?>
            <span class="block xl:inline"><?php echo $langs->trans('VelomaBookTitle'); ?></span>
            <span class="block text-green-600 xl:inline"><?php echo $langs->trans('VelomaBookBike'); ?></span>
        <?php else: ?>
            <span class="block xl:inline"><?php echo $langs->trans('VelomaRentTitle'); ?></span>
            <span class="block text-green-600 xl:inline"><?php echo $langs->trans('VelomaRentBike'); ?></span>
        <?php endif; ?>
    </h1>
    <?php if ($displayConfirm): ?>
        <p class="mx-auto mt-3 text-center max-w-md text-lg text-gray-500 sm:text-xl md:mt-5 md:max-w-3xl"><?php echo $mode == 'book' ? $langs->trans('VelomaConfirmBookDetails') : $langs->trans('VelomaConfirmRentDetails'); ?></p>
        <div class="mt-10 sm:flex sm:justify-center lg:justify-center">
            <form class="w-3/5" id="confirm" name="confirm" action="<?php echo dol_buildpath('/veloma/public/rent.php', 1); ?>" method="post">
                <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                <input type="hidden" name="action" value="confirm">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="mode" value="<?php echo $mode; ?>">
                <input type="hidden" name="start-date" value="<?php echo $start; ?>">
                <input type="hidden" name="end-date" value="<?php echo $end; ?>">

                <div class="mt-6 space-y-6">
                    <div>
                        <label for="confirm-phone" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaPhone'); ?></label>
                        <div class="mt-1">
                            <input name="confirm-phone" id="confirm-phone" type="tel" autocomplete="tel" value="<?php echo GETPOST('confirm-phone'); ?>" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="confirm-firstname" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaFirstName'); ?></label>
                        <div class="mt-1">
                            <input name="confirm-firstname" id="confirm-firstname" type="text" autocomplete="given-name" value="<?php echo GETPOST('confirm-firstname'); ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="confirm-lastname" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaLastName'); ?></label>
                        <div class="mt-1">
                            <input name="confirm-lastname" id="confirm-lastname" type="text" autocomplete="family-name" value="<?php echo GETPOST('confirm-lastname'); ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="confirm-email" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaEmail'); ?></label>
                        <div class="mt-1">
                            <input name="confirm-email" id="confirm-email" type="email" autocomplete="email" value="<?php echo GETPOST('confirm-email'); ?>" class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div class="mt-2 py-3 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaValidate'); ?></button>
                    <a href="<?php echo dol_buildpath('/veloma/public/rent.php', 1); ?>" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaCancel'); ?></a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <p class="mx-auto mt-3 text-center max-w-md text-lg text-gray-500 sm:text-xl md:mt-5 md:max-w-3xl"><?php echo $langs->trans('VelomaBookDetails'); ?></p>
        <?php if ($mode == 'book'): ?>
        <div class="mt-10 sm:flex sm:justify-center lg:justify-center">
            <form method="post" class="flex space-x-8" name="book" id="book" action="<?php echo dol_buildpath('/veloma/public/rent.php', 1); ?>">
                <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                <input type="hidden" name="action" value="confirm">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="mode" value="<?php echo $mode; ?>">

                <div>
                    <label for="start-date" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaBookStartDate'); ?></label>
                    <div class="mt-1">
                        <input name="start-date" id="start-date" type="datetime-local" required value="<?php echo dol_print_date($start, '%Y-%m-%d %H:%M'); ?>" class="book-date block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="end-date" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaBookEndDate'); ?></label>
                    <div class="mt-1">
                        <input name="end-date" id="end-date" type="datetime-local" required value="<?php echo dol_print_date($end, '%Y-%m-%d %H:%M'); ?>" class="book-date block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="start-date" class="block text-sm font-medium text-gray-700">&nbsp;</label>
                    <div class="mt-1">
                        <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700"><?php echo $langs->trans('VelomaBookSearch'); ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
        <div class="mt-10 sm:flex sm:justify-center lg:justify-center">
            <div id="map" style="width: 100%; height: 32rem;">

            </div>
        </div>
    <?php endif; ?>
</div>


<script type="text/javascript">
    var lat = <?php echo (!empty($conf->global->VELOMA_MAP_LATITUDE) ? $conf->global->VELOMA_MAP_LATITUDE : 48.852969); ?>;
    var lon = <?php echo (!empty($conf->global->VELOMA_MAP_LONGITUDE) ? $conf->global->VELOMA_MAP_LONGITUDE : 2.349903); ?>;

    var map = null;
    var marker = null;

    function initMap() {
        map = L.map('map').setView([lat, lon], <?php echo !empty($conf->global->VELOMA_MAP_ZOOM) ? $conf->global->VELOMA_MAP_ZOOM : 13; ?>);
        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            attribution: 'Données &copy; Contributeurs <a href="http://openstreetmap.org">OpenStreetMap</a> | <a href="https://creativecommons.org/licenses/by/2.0/">CC-BY</a>',
            minZoom: 1,
            maxZoom: 20
        }).addTo(map);

        <?php if (count($bikesByStand)): ?>
            <?php foreach ($bikesByStand as $standId => $bikes): ?>
                var markers = L.markerClusterGroup();

                <?php foreach ($bikes as $bike): ?>
                marker = L.marker([<?php echo $stands[$standId]->latitude; ?>, <?php echo $stands[$standId]->longitude; ?>]);
                marker.bindPopup("<?php echo addslashes($langs->transnoentities($mode == 'book' ? 'VelomaBookBikeMarker' : 'VelomaRentBikeMarker', $bike->ref, dol_buildpath(sprintf('/veloma/public/rent.php?mode=%s&id=%d&start-date=%s&end-date=%s', $mode, $bike->id, $start, $end), 1))); ?>");
                markers.addLayer(marker);
                <?php endforeach; ?>

                map.addLayer(markers);
            <?php endforeach; ?>
        <?php endif; ?>
    }

    window.addEventListener("load", function (event) {
        if (document.querySelector("#map")) {
            initMap();
        }

        document.querySelectorAll(".book-date").forEach(function(item) {
            item.addEventListener('blur', function () {
                let form = document.querySelector("#book");
                if (form) {
                    form.submit();
                }
            })
        })
    });
</script>


<?php include_once('tpl/layouts/footer.tpl.php'); ?>
