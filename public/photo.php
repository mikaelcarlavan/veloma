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
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

dol_include_once("/veloma/class/site.class.php");
dol_include_once("/bike/class/bike.class.php");
dol_include_once("/stand/class/stand.class.php");

$id = GETPOST('id', 'int');
$modulepart = GETPOST('modulepart', 'alpha');

$filename = DOL_DOCUMENT_ROOT.'/public/theme/common/nophoto.png';

if ($id > 0) {
    $object = null;
    $dirOutput = '';
    if ($modulepart == 'bike') {
        $object = new Bike($db);
        $dirOutput = $conf->bike->dir_output;
    } else if ($modulepart == 'stand') {
        $object = new Stand($db);
        $dirOutput = $conf->stand->dir_output;
    }

    if ($object) {
        if ($object->fetch($id) > 0) {
            $uploadDir = $dirOutput.'/'.dol_sanitizeFileName($object->ref);
            // Build file list
            $files = dol_dir_list($uploadDir, "files", 0, '', '(\.meta|_preview.*\.png)$','', SORT_ASC,1);
            if (count($files) > 0) {
                $file = array_pop($files);
                if ($file) {
                    $filename = $file['fullname'];
                }
            }
        }
    }
}


$type = dol_mimetype($filename);

top_httphead($type);
header('Content-Disposition: inline; filename="'.basename($filename).'"');

readfile($filename);

