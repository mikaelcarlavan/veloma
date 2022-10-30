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

dol_include_once("/stand/class/stand.class.php");
dol_include_once("/bike/class/bike.class.php");

$id = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');
$mode = GETPOST('mode', 'alpha');
$standId = GETPOST('stand-id', 'int');

$langs->loadLangs(array('main', 'errors'));
$langs->load('veloma@veloma');
$langs->load("other");

$veloma = new Veloma($db);
$history = new VelomaHistory($db);
$site = new Site($db);
$site->start($user);

if ($user->id == 0) {
    $site->addError($langs->trans('VelomaNotLoggedIn'));
    $url = dol_buildpath('/veloma/public/index.php', 1);
    header("Location: ".$url);		// Default behaviour is redirect to index.php page
    exit;
}

$confirmationModalOpened = false;
$returnModalOpened = false;

if ($action == 'return') {
    $returnModalOpened = true;
}

$stand = new Stand($db);
$stands = $stand->liste_array();

$bike = new Bike($db);
$bikes = $bike->liste_array();

if ($action == 'confirm') {

    if (!isset($stands[$standId])) {
        $site->addError($langs->trans('VelomaStandNotFound'));
    } else {

        if (!isset($bikes[$id])) {
            $site->addError($langs->trans('VelomaBikeNotFound'));
        } else {
            $bike = $bikes[$id];
            $stand = $stands[$standId];

            $current_fk_stand = $bike->fk_stand;

            $oldcode = $bike->code;
            $newcode = sprintf("%04d", rand(0, 9999));
            $bike->fk_user = -1;
            $bike->fk_stand = $standId;
            $bike->code = $newcode;
            $bike->update($user);

            if (!empty($conf->global->VELOMA_USE_CREDIT)) {
                $credit = $veloma->updateUserCredit($bike->id, $user);
                $message = $langs->transnoentities('VelomaBikeReturnedWithCredit', $oldcode, $newcode, price($credit));
            } else {
                $message = $langs->transnoentities('VelomaBikeReturned', $oldcode, $newcode);
            }

            $site->addMessage($message);
            $veloma->createHistory($user, $action, $langs->trans('VelomaReturnCommand').' '.$bike->ref.' '.$stand->ref, $bike->id ?: -1, $current_fk_stand);

        }
    }
}

$bikesRented = array();


$bike = new Bike($db);
$bikes = $bike->liste_array();

if (count($bikes)) {
    foreach ($bikes as $b) {
        if ($b->fk_user == $user->id) {
            $bikesRented[] = $b;
        }
    }
}
?>

<?php include_once('tpl/layouts/header.tpl.php'); ?>

<div class="px-4 sm:px-8 xl:pr-16" x-data="{returnModalOpened: <?php echo $returnModalOpened ? 'true' : 'false'; ?>}">
    <h1 class="text-4xl text-center font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
        <span class="block xl:inline"><?php echo $langs->trans('VelomaRentalsTitle'); ?></span>
        <span class="block text-green-600 xl:inline"><?php echo $langs->trans('VelomaRentBikes'); ?></span>
    </h1>
    <p class="mx-auto mt-3 text-center max-w-md text-lg text-gray-500 sm:text-xl md:mt-5 md:max-w-3xl"><?php echo $langs->trans('VelomaRentalsDetails'); ?></p>
    <div class="mt-10">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="mt-8 flex-col">
                <div class="-my-2 -mx-4 sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"><?php echo $langs->trans('VelomaBike'); ?></th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"><?php echo $langs->trans('VelomaBikeCode'); ?></th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"><?php echo $langs->trans('VelomaRentStartedAt'); ?></th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only"><?php echo $langs->trans('VelomaAction'); ?></span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white">
                                <?php if (count($bikesRented)): ?>
                                    <?php foreach ($bikesRented as $b): ?>
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"><?php echo $b->ref; ?></td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><?php echo $b->code; ?></td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><?php echo dol_print_date($b->datec, 'dayhour'); ?></td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <a href="<?php echo dol_buildpath('/veloma/public/rentals.php?action=return&id='.$b->id, 1); ?>" class="text-green-600 hover:text-green-900"><?php echo $langs->trans('VelomaReturnBike'); ?></a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"><?php echo $langs->trans('VelomaNoRentals'); ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div x-cloak x-show="returnModalOpened" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="returnModalOpened"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="returnModalOpened"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                        <button @click="returnModalOpened = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <span class="sr-only"><?php echo $langs->trans('VelomaClose'); ?></span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form id="return" name="return" action="<?php echo dol_buildpath('/veloma/public/rentals.php', 1); ?>" method="post">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="action" value="confirm">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                                <div class="mt-3 text-center sm:mt-0 sm:my-4 sm:text-left">
                                    <h3 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900" id="modal-title"><?php echo $langs->trans('VelomaReturnBike'); ?></h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500"><?php echo $langs->trans('VelomaReturnBikeDesc'); ?></p>
                                    </div>
                                    <div class="mt-6 space-y-6">
                                        <div>
                                            <label for="stand-id" class="block text-sm font-medium text-gray-700"><?php echo $langs->trans('VelomaStand'); ?></label>
                                            <div class="mt-1">
                                                <select id="stand-id" name="stand-id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-green-500 focus:outline-none focus:ring-green-500 sm:text-sm">
                                                    <?php foreach ($stands as $stand): ?>
                                                        <option value="<?php echo $stand->id; ?>"><?php echo $stand->name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaValidate'); ?></button>
                            <button @click="returnModalOpened = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"><?php echo $langs->trans('VelomaCancel'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>



<?php include_once('tpl/layouts/footer.tpl.php'); ?>
