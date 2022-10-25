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
 *     	\file       htdocs/signalement/public/index.php
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
include_once DOL_DOCUMENT_ROOT.'/core/lib/security2.lib.php';


if (session_status() === PHP_SESSION_ACTIVE)
{
    session_destroy();
}

// Not sure this is required
unset($_SESSION['dol_login']);
unset($_SESSION['dol_entity']);
unset($_SESSION['urlfrom']);
unset($_SESSION['dol_profiles']);
unset($_SESSION['dol_reports']);

$url = dol_buildpath('/veloma/public/index.php', 1);

header("Location: ".$url);		// Default behaviour is redirect to index.php page
exit;
