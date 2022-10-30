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

$action = GETPOST('action', 'alpha');

$langs->loadLangs(array('main', 'errors'));
$langs->load('veloma@veloma');
$langs->load("other");

$veloma = new Veloma($db);

$site = new Site($db);
$site->start($user);

$confirmationModalOpened = false;

if ($action == 'login')
{
	$site->login($user);
}

if ($action == 'register')
{
	$site->register($user);
}

if ($action == 'account')
{
    $site->account($user);
}

if ($action == 'passrequest')
{
    $result = $site->passwordrequest();
	if ($result > 0) {
        $confirmationModalOpened = true;
	}
}

if ($action == 'passvalidation')
{
	$site->passwordvalidation();
}

?>

<?php include_once('tpl/layouts/header.tpl.php'); ?>

<div class="px-4 sm:px-8 xl:pr-16">
    <h1 class="text-4xl text-center font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
        <span class="block xl:inline"><?php echo $langs->trans('VelomaWelcomeTitle'); ?></span>
        <span class="block text-green-600 xl:inline"><?php echo $langs->trans('VelomaWelcomeBikes'); ?></span>
    </h1>
    <p class="mx-auto text-center mt-3 max-w-md text-lg text-gray-500 sm:text-xl md:mt-5 md:max-w-3xl"><?php echo $langs->trans('VelomaWelcomeDetails'); ?></p>
    <div class="mt-10 sm:flex sm:justify-center lg:justify-center">
        <div class="rounded-md shadow">
            <a href="<?php echo dol_buildpath('/veloma/public/rent.php', 1); ?>" class="flex w-full items-center justify-center rounded-md border border-transparent bg-green-600 px-8 py-3 text-base font-medium text-white hover:bg-green-700 md:py-4 md:px-10 md:text-lg"><?php echo $langs->trans('VelomaRentABike'); ?></a>
        </div>
        <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
            <a href="<?php echo dol_buildpath('/veloma/public/rent.php?mode=book', 1); ?>" class="flex w-full items-center justify-center rounded-md border border-transparent bg-white px-8 py-3 text-base font-medium text-green-600 hover:bg-gray-50 md:py-4 md:px-10 md:text-lg"><?php echo $langs->trans('VelomaBookABike'); ?></a>
        </div>
    </div>
</div>

<?php include_once('tpl/layouts/footer.tpl.php'); ?>
