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
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

dol_include_once("/veloma/class/site.class.php");
dol_include_once("/veloma/class/veloma.class.php");

$langs->loadLangs(array('main', 'errors'));
$langs->load('veloma@veloma');
$langs->load("other");

$site = new Site($db);
$site->start($user);

$confirmationModalOpened = false;

?>

<?php include_once('tpl/layouts/header.tpl.php'); ?>

<div class="px-4 sm:px-8 xl:pr-16">
    <h1 class="text-4xl text-center font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl">
        <span class="block xl:inline"><?php echo $langs->trans('VelomaHaveANice'); ?></span>
        <span class="block text-green-600 xl:inline"><?php echo $langs->trans('VelomaTrip'); ?></span>
    </h1>
    <div class="mt-10 sm:flex sm:justify-center lg:justify-center">
        <img src="img/thanks.png" class="w-1/2" />
    </div>
</div>

<?php include_once('tpl/layouts/footer.tpl.php'); ?>
