<?php
/* Copyright (C) 2022	Mikael Carlavan	    <contact@mika-carl.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
/**
 *	\file       htdocs/veloma/lib/veloma.lib.php
 *	\brief      Ensemble de fonctions de base pour le module veloma
 * 	\ingroup	veloma
 */

/**
 * Prepare array with list of tabs
 *
 * @return  array				Array of tabs to show
 */
function veloma_prepare_admin_head()
{
	global $db, $langs, $conf, $user;
	$langs->load("veloma@veloma");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/veloma/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

    complete_head_from_modules($conf, $langs, null, $head, $h, 'veloma_admin');

	$head[$h][0] = dol_buildpath("/veloma/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

    complete_head_from_modules($conf, $langs, null, $head, $h, 'veloma_admin', 'remove');

    return $head;
}

