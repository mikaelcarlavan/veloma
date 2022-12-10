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

/**
 *  \file       htdocs/veloma/admin/setup.php
 *  \ingroup    veloma
 *  \brief      Admin page
 */


$res=@include("../../main.inc.php");                   // For root directory
if (! $res) $res=@include("../../../main.inc.php");    // For "custom" directory

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
dol_include_once("/veloma/lib/veloma.lib.php");

// Translations
$langs->load("veloma@veloma");
$langs->load("admin");

// Access control
if (! $user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');
$value = GETPOST('value', 'alpha');

$reg = array();

/*
 * Actions
 */


include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';

$error=0;

// Action mise a jour ou ajout d'une constante
if ($action == 'update')
{
	$constname=GETPOST('constname','alpha');
	$constvalue=(GETPOST('constvalue_'.$constname) ? GETPOST('constvalue_'.$constname) : GETPOST('constvalue'));


	$consttype=GETPOST('consttype','alpha');
	$constnote=GETPOST('constnote');
	$res = dolibarr_set_const($db,$constname,$constvalue,$type[$consttype],0,$constnote,$conf->entity);

	if (! $res > 0) $error++;

	if (! $error)
	{
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	}
	else
	{
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}

if (preg_match('/set_(.*)/',$action,$reg))
{
    $code=$reg[1];
    $value=(GETPOST($code) ? GETPOST($code) : 1);
    if (dolibarr_set_const($db, $code, $value, 'chaine', 0, '', $conf->entity) > 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

else if (preg_match('/del_(.*)/',$action,$reg))
{
    $code=$reg[1];
    if (dolibarr_del_const($db, $code, $conf->entity) > 0)
    {
        Header("Location: ".$_SERVER["PHP_SELF"]);
        exit;
    }
    else
    {
        dol_print_error($db);
    }
}

/*
 * View
 */

llxHeader('', $langs->trans('VelomaSetup'));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';

print load_fiche_titre($langs->trans('VelomaSetup'), $linkback);

// Configuration header
$head = veloma_prepare_admin_head();
dol_fiche_head(
	$head,
	'settings',
	$langs->trans("ModuleVelomaName"),
	0,
	"veloma2@veloma"
);

$form = new Form($db);

print load_fiche_titre($langs->trans("VelomaOptions"),'','');


print '<table class="noborder" width="100%">';
print '<tbody>';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td align="center">'.$langs->trans("Action").'</td>';
print "</tr>\n";

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<tr class="oddeven">';
print '<td>';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="constname" value="VELOMA_MAP_LATITUDE">';
print '<input type="hidden" name="constnote" value="">';
print $langs->trans('DescVELOMA_MAP_LATITUDE');
print '</td>';
print '<td>';
print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_MAP_LATITUDE.'" />';
print '<input type="hidden" name="consttype" value="chaine">';
print '</td>';
print '<td align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
print '</td>';
print '</tr>';
print '</form>';

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<tr class="oddeven">';
print '<td>';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="constname" value="VELOMA_MAP_LONGITUDE">';
print '<input type="hidden" name="constnote" value="">';
print $langs->trans('DescVELOMA_MAP_LONGITUDE');
print '</td>';
print '<td>';
print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_MAP_LONGITUDE.'" />';
print '<input type="hidden" name="consttype" value="chaine">';
print '</td>';
print '<td align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
print '</td>';
print '</tr>';
print '</form>';

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<tr class="oddeven">';
print '<td>';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="constname" value="VELOMA_MAP_ZOOM">';
print '<input type="hidden" name="constnote" value="">';
print $langs->trans('DescVELOMA_MAP_ZOOM');
print '</td>';
print '<td>';
print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_MAP_ZOOM.'" />';
print '<input type="hidden" name="consttype" value="chaine">';
print '</td>';
print '<td align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
print '</td>';
print '</tr>';
print '</form>';

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<tr class="oddeven">';
print '<td>';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="constname" value="VELOMA_INITIAL_LIMIT">';
print '<input type="hidden" name="constnote" value="">';
print $langs->trans('DescVELOMA_INITIAL_LIMIT');
print '</td>';
print '<td>';
print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_INITIAL_LIMIT.'" />';
print '<input type="hidden" name="consttype" value="chaine">';
print '</td>';
print '<td align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
print '</td>';
print '</tr>';
print '</form>';

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<tr class="oddeven">';
// Show constant
print '<td>';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="constname" value="VELOMA_USE_CREDIT">';
print '<input type="hidden" name="constnote" value="">';
print $langs->trans('DescVELOMA_USE_CREDIT');
print '</td>';
print '<td>';
if (!empty($conf->global->VELOMA_USE_CREDIT)) {
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_VELOMA_USE_CREDIT&amp;token='.newToken().'">'.img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_VELOMA_USE_CREDIT&amp;token='.newToken().'">'.img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '<input type="hidden" name="consttype" value="chaine">';
print '</td>';
print '<td align="center">';
print '&nbsp;';
print '</td>';
print '</tr>';
print '</form>';

if (!empty($conf->global->VELOMA_USE_CREDIT)) {
    print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
    print '<tr class="oddeven">';
    print '<td>';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="constname" value="VELOMA_INITIAL_CREDIT">';
    print '<input type="hidden" name="constnote" value="">';
    print $langs->trans('DescVELOMA_INITIAL_CREDIT');
    print '</td>';
    print '<td>';
    print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_INITIAL_CREDIT.'" />';
    print '<input type="hidden" name="consttype" value="chaine">';
    print '</td>';
    print '<td align="center">';
    print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
    print '</td>';
    print '</tr>';
    print '</form>';

    print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
    print '<tr class="oddeven">';
    print '<td>';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="constname" value="VELOMA_RENT_COST">';
    print '<input type="hidden" name="constnote" value="">';
    print $langs->trans('DescVELOMA_RENT_COST');
    print '</td>';
    print '<td>';
    print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_RENT_COST.'" />';
    print '<input type="hidden" name="consttype" value="chaine">';
    print '</td>';
    print '<td align="center">';
    print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
    print '</td>';
    print '</tr>';
    print '</form>';

    print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
    print '<tr class="oddeven">';
    print '<td>';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="constname" value="VELOMA_RENT_DURATION">';
    print '<input type="hidden" name="constnote" value="">';
    print $langs->trans('DescVELOMA_RENT_DURATION');
    print '</td>';
    print '<td>';
    print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_RENT_DURATION.'" />';
    print '<input type="hidden" name="consttype" value="chaine">';
    print '</td>';
    print '<td align="center">';
    print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
    print '</td>';
    print '</tr>';
    print '</form>';

    print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
    print '<tr class="oddeven">';
    print '<td>';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="constname" value="VELOMA_FREE_DURATION">';
    print '<input type="hidden" name="constnote" value="">';
    print $langs->trans('DescVELOMA_FREE_DURATION');
    print '</td>';
    print '<td>';
    print '<input type="text" class="flat" name="constvalue" size="60" value="'.$conf->global->VELOMA_FREE_DURATION.'" />';
    print '<input type="hidden" name="consttype" value="chaine">';
    print '</td>';
    print '<td align="center">';
    print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
    print '</td>';
    print '</tr>';
    print '</form>';
}

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<tr class="oddeven">';
// Show constant
print '<td>';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="constname" value="VELOMA_ALLOW_UNREGISTERED_USERS">';
print '<input type="hidden" name="constnote" value="">';
print $langs->trans('DescVELOMA_ALLOW_UNREGISTERED_USERS');
print '</td>';
print '<td>';
if (!empty($conf->global->VELOMA_ALLOW_UNREGISTERED_USERS)) {
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=del_VELOMA_ALLOW_UNREGISTERED_USERS&amp;token='.newToken().'">'.img_picto($langs->trans("Enabled"), 'switch_on').'</a>';
} else {
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_VELOMA_ALLOW_UNREGISTERED_USERS&amp;token='.newToken().'">'.img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print '<input type="hidden" name="consttype" value="chaine">';
print '</td>';
print '<td align="center">';
print '&nbsp;';
print '</td>';
print '</tr>';
print '</form>';

print '</tbody>';
print '</table>';


// Page end
dol_fiche_end();
llxFooter();
