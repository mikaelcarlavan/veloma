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
 *  \file       htdocs/veloma/bookings.php
 *  \ingroup    veloma
 *  \brief      Page to list veloma
 */


$res = @include("../main.inc.php");                   // For root directory
if (!$res) $res = @include("../../main.inc.php");    // For "custom" directory

require_once DOL_DOCUMENT_ROOT . '/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';

dol_include_once("/veloma/class/veloma.class.php");
dol_include_once("/veloma/class/veloma.history.class.php");
dol_include_once("/veloma/class/veloma.booking.class.php");
dol_include_once("/veloma/class/veloma.sms.class.php");

if (!empty($conf->stand->enabled)) {
    dol_include_once("/stand/class/html.form.stand.class.php");
    dol_include_once("/stand/class/stand.class.php");
    $langs->load("stand@stand");
}

if (!empty($conf->bike->enabled)) {
    dol_include_once("/bike/class/html.form.bike.class.php");
    dol_include_once("/bike/class/bike.class.php");
    $langs->load("bike@bike");
}

$langs->load("veloma@veloma");

$action = GETPOST('action', 'aZ09');
$massaction = GETPOST('massaction', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$toselect = GETPOST('toselect', 'array');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'velomalist';

$optioncss = GETPOST('optioncss', 'alpha');
$search_btn = GETPOST('button_search', 'alpha');
$search_remove_btn = GETPOST('button_removefilter', 'alpha');

$search_cyear = GETPOST("search_cyear", "int");
$search_cmonth = GETPOST("search_cmonth", "int");
$search_cday = GETPOST("search_cday", "int");

$search_syear = GETPOST("search_syear", "int");
$search_smonth = GETPOST("search_smonth", "int");
$search_sday = GETPOST("search_sday", "int");

$search_eyear = GETPOST("search_eyear", "int");
$search_emonth = GETPOST("search_emonth", "int");
$search_eday = GETPOST("search_eday", "int");

$search_user_author_id = GETPOST('search_user_author_id', 'int');
$search_fk_bike = GETPOST('search_fk_bike', 'int');
$search_fk_user = GETPOST('search_fk_user', 'int');

// Security check
$id = GETPOST('id', 'int');
// $result = restrictedArea($user, 'veloma', '', '');

$diroutputmassaction = $conf->veloma->dir_output . '/temp/massgeneration/' . $user->id;

// Load variable for pagination
$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOST("page", 'int');
if (empty($page) || $page == -1 || !empty($search_btn) || !empty($search_remove_btn) || (empty($toselect) && $massaction === '0')) {
    $page = 0;
}     // If $page is not defined, or '' or -1
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (!$sortfield) $sortfield = 'e.rowid';
if (!$sortorder) $sortorder = 'DESC';

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$object = new Bike($db);
$hookmanager->initHooks(array('velomabookinglist'));

$arrayfields = array(
    'e.fk_bike' => array('label' => $langs->trans("VelomaBike"), 'checked' => 1, 'enabled' => $conf->bike->enabled),
    'e.fk_user' => array('label' => $langs->trans("VelomaUser"), 'checked' => 1),
    'e.dates' => array('label' => $langs->trans("VelomaBookStartDate"), 'checked' => 1),
    'e.datee' => array('label' => $langs->trans("VelomaBookEndDate"), 'checked' => 1),
    'e.datec' => array('label' => $langs->trans("DateCreation"), 'checked' => 1),
    'e.tms' => array('label' => $langs->trans("DateModificationShort"), 'checked' => 0, 'position' => 500),
);


/*
 * Actions
 */

$error = 0;

//if (! GETPOST('confirmmassaction','alpha')) { $massaction=''; }

$parameters = array('socid' => '');
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');


if (empty($reshook)) {
    // Purge search criteria
    if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')) // All tests are required to be compatible with all browsers
    {
        $search_user_author_id = '';
    }

    if ($action == 'confirm_delete') {
        $booking = new VelomaBooking($db);

        if ($booking->fetch($id) > 0) {
            $bike = $booking->bike;

            if (!$bike->id) {
                setEventMessages($langs->trans("VelomaBikeNotFound"), null, 'errors');
            } else {
                $booking->delete($user);
                setEventMessages($langs->trans("VelomaBookCanceled"), null, 'mesgs');

                // Send SMS
                $u = $booking->user;
                if ($u && $bike) {
                    $response = $langs->transnoentities('VelomaBikeBookCanceledByAdmin', $bike->ref, dol_print_date($booking->dates, 'dayhour'), dol_print_date($booking->datee, 'dayhour'));
                    $sms = new VelomaSMS($db);
                    $sms->create($u->user_mobile, $response, $user);
                }
            }
        } else {
            setEventMessages($langs->trans("VelomaBookNotFound"), null, 'errors');
        }
    }
}

/*
 * View
 */

$now = dol_now();
$form = new Form($db);
$formother = new FormOther($db);

$title = $langs->trans("VelomaBookings");
$help_url = "";

$sql = 'SELECT';
$sql .= " e.rowid, e.datee, e.dates, e.datec, e.fk_bike, e.fk_user, e.user_author_id, e.entity, e.tms, ";
$sql .= " b.rowid as bike_id, b.ref as bike_ref, b.name as bike_name, ";
$sql .= " u.rowid as user_id, u.login as user_login, u.firstname as user_firstname, u.lastname as user_lastname ";

$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListSelect', $parameters);    // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;
$sql .= ' FROM ' . MAIN_DB_PREFIX . 'veloma_booking as e';
$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'bike as b ON e.fk_bike = b.rowid';
$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'user as u ON e.fk_user = u.rowid';
$sql .= ' WHERE e.entity IN (' . getEntity('veloma_booking') . ')';

if ($search_cmonth > 0) {
    if ($search_cyear > 0 && empty($search_cday))
        $sql .= " AND e.datec BETWEEN '" . $db->idate(dol_get_first_day($search_cyear, $search_cmonth, false)) . "' AND '" . $db->idate(dol_get_last_day($search_cyear, $search_cmonth, false)) . "'";
    else if ($search_cyear > 0 && !empty($search_cday))
        $sql .= " AND e.datec BETWEEN '" . $db->idate(dol_mktime(0, 0, 0, $search_cmonth, $search_cday, $search_cyear)) . "' AND '" . $db->idate(dol_mktime(23, 59, 59, $search_cmonth, $search_cday, $search_cyear)) . "'";
    else
        $sql .= " AND date_format(e.datec, '%m') = '" . $search_cmonth . "'";
} else if ($search_cyear > 0) {
    $sql .= " AND e.datec BETWEEN '" . $db->idate(dol_get_first_day($search_cyear, 1, false)) . "' AND '" . $db->idate(dol_get_last_day($search_cyear, 12, false)) . "'";
}

if ($search_smonth > 0) {
    if ($search_syear > 0 && empty($search_sday))
        $sql .= " AND e.dates BETWEEN '" . $db->idate(dol_get_first_day($search_syear, $search_smonth, false)) . "' AND '" . $db->idate(dol_get_last_day($search_syear, $search_smonth, false)) . "'";
    else if ($search_syear > 0 && !empty($search_sday))
        $sql .= " AND e.dates BETWEEN '" . $db->idate(dol_mktime(0, 0, 0, $search_smonth, $search_sday, $search_syear)) . "' AND '" . $db->idate(dol_mktime(23, 59, 59, $search_smonth, $search_sday, $search_syear)) . "'";
    else
        $sql .= " AND date_format(e.dates, '%m') = '" . $search_smonth . "'";
} else if ($search_syear > 0) {
    $sql .= " AND e.dates BETWEEN '" . $db->idate(dol_get_first_day($search_syear, 1, false)) . "' AND '" . $db->idate(dol_get_last_day($search_syear, 12, false)) . "'";
}

if ($search_emonth > 0) {
    if ($search_eyear > 0 && empty($search_eday))
        $sql .= " AND e.datee BETWEEN '" . $db->idate(dol_get_first_day($search_eyear, $search_emonth, false)) . "' AND '" . $db->idate(dol_get_last_day($search_eyear, $search_emonth, false)) . "'";
    else if ($search_eyear > 0 && !empty($search_eday))
        $sql .= " AND e.datee BETWEEN '" . $db->idate(dol_mktime(0, 0, 0, $search_emonth, $search_eday, $search_eyear)) . "' AND '" . $db->idate(dol_mktime(23, 59, 59, $search_emonth, $search_eday, $search_eyear)) . "'";
    else
        $sql .= " AND date_format(e.datee, '%m') = '" . $search_emonth . "'";
} else if ($search_eyear > 0) {
    $sql .= " AND e.datee BETWEEN '" . $db->idate(dol_get_first_day($search_eyear, 1, false)) . "' AND '" . $db->idate(dol_get_last_day($search_eyear, 12, false)) . "'";
}

if ($search_fk_bike > 0) $sql .= " AND e.fk_bike = " . $search_fk_bike;
if ($search_fk_user > 0) $sql .= " AND e.fk_user = " . $search_fk_user;

if ($search_user_author_id > 0) $sql .= " AND e.user_author_id = " . $search_user_author_id;


// Add where from hooks
$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldListWhere', $parameters);    // Note that $action and $object may have been modified by hook
$sql .= $hookmanager->resPrint;

$sql .= $db->order($sortfield, $sortorder);

// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
    $result = $db->query($sql);
    $nbtotalofrecords = $db->num_rows($result);

    if (($page * $limit) > $nbtotalofrecords)  // if total resultset is smaller then paging size (filtering), goto and load page 0
    {
        $page = 0;
        $offset = 0;
    }
}

$sql .= $db->plimit($limit + 1, $offset);
//print $sql;

$resql = $db->query($sql);
if ($resql) {
    $title = $langs->trans('VelomaBookings');

    $num = $db->num_rows($resql);

    llxHeader('', $title, $help_url);


    $param = '';

    if (!empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param .= '&contextpage=' . urlencode($contextpage);
    if ($limit > 0 && $limit != $conf->liste_limit) $param .= '&limit=' . urlencode($limit);
    if ($optioncss != '') $param .= '&optioncss=' . urlencode($optioncss);

    $formconfirm = '';

    // Confirmation to delete
    if ($action == 'delete') {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id, $langs->trans('VelomaDeleteBooking'), $langs->trans('VelomaConfirmDeleteBooking'), 'confirm_delete', '', 0, 1);
    }

    print $formconfirm;

    // Lines of title fields
    print '<form method="POST" id="searchFormList" action="' . $_SERVER["PHP_SELF"] . '">';
    if ($optioncss != '') print '<input type="hidden" name="optioncss" value="' . $optioncss . '">';
    print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
    print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
    print '<input type="hidden" name="action" value="list">';
    print '<input type="hidden" name="sortfield" value="' . $sortfield . '">';
    print '<input type="hidden" name="sortorder" value="' . $sortorder . '">';
    print '<input type="hidden" name="page" value="' . $page . '">';
    print '<input type="hidden" name="contextpage" value="' . $contextpage . '">';


    print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num, $nbtotalofrecords, 'veloma2@veloma', 0, '', '', $limit);

    $moreforfilter = '';

    // If the user can view other users
    if ($user->rights->user->user->lire) {
        $moreforfilter .= '<div class="divsearchfield">';
        $moreforfilter .= $langs->trans('CreatedByUsers') . ': ';
        $moreforfilter .= $form->select_dolusers($search_user_author_id, 'search_user_author_id', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'maxwidth200');
        $moreforfilter .= '</div>';
    }

    $parameters = array();
    $reshook = $hookmanager->executeHooks('printFieldPreListTitle', $parameters);    // Note that $action and $object may have been modified by hook
    if (empty($reshook)) $moreforfilter .= $hookmanager->resPrint;
    else $moreforfilter = $hookmanager->resPrint;

    if (!empty($moreforfilter)) {
        print '<div class="liste_titre liste_titre_bydiv centpercent">';
        print $moreforfilter;
        print '</div>';
    }

    $varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;

    print '<div class="div-table-responsive">';
    print '<table class="tagtable liste' . ($moreforfilter ? " listwithfilterbefore" : "") . '">' . "\n";

    print '<tr class="liste_titre_filter">';

    if (!empty($arrayfields['e.fk_bike']['checked']) && !empty($conf->bike->enabled)) {
        $bikeform = new BikeForm($db);
        print '<td class="liste_titre">';
        print $bikeform->select_bike($search_fk_bike, 'search_fk_bike', '', 1);
        print '</td>';
    }

    if (!empty($arrayfields['e.fk_user']['checked'])) {
        print '<td class="liste_titre">';
        print $form->select_dolusers($search_fk_user,  'search_fk_user', 1);
        print '</td>';
    }

    if (!empty($arrayfields['e.dates']['checked'])) {
        print '<td class="liste_titre nowraponall" align="left">';
        if (!empty($conf->global->MAIN_LIST_FILTER_ON_DAY)) print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="search_sday" value="' . $search_sday . '">';
        print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="search_smonth" value="' . $search_smonth . '">';
        $formother->select_year($search_syear ? $search_syear : -1, 'search_syear', 1, 20, 5);
        print '</td>';
    }

    if (!empty($arrayfields['e.datee']['checked'])) {
        print '<td class="liste_titre nowraponall" align="left">';
        if (!empty($conf->global->MAIN_LIST_FILTER_ON_DAY)) print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="search_eday" value="' . $search_eday . '">';
        print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="search_emonth" value="' . $search_cmonth . '">';
        $formother->select_year($search_eyear ? $search_eyear : -1, 'search_eyear', 1, 20, 5);
        print '</td>';
    }

    // Extra fields
    include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_list_search_input.tpl.php';
    // Fields from hook
    $parameters = array('arrayfields' => $arrayfields);
    $reshook = $hookmanager->executeHooks('printFieldListOption', $parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;

    // Date de saisie
    if (!empty($arrayfields['e.datec']['checked'])) {
        print '<td class="liste_titre nowraponall" align="left">';
        if (!empty($conf->global->MAIN_LIST_FILTER_ON_DAY)) print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="search_cday" value="' . $search_cday . '">';
        print '<input class="flat width25 valignmiddle" type="text" maxlength="2" name="search_cmonth" value="' . $search_cmonth . '">';
        $formother->select_year($search_cyear ? $search_cyear : -1, 'search_cyear', 1, 20, 5);
        print '</td>';
    }

    // Date modification
    if (!empty($arrayfields['e.tms']['checked'])) {
        print '<td class="liste_titre">';
        print '</td>';
    }

    // Action column
    print '<td class="liste_titre" align="middle">';
    $searchpicto = $form->showFilterButtons();
    print $searchpicto;
    print '</td>';

    print "</tr>\n";

    // Fields title
    print '<tr class="liste_titre">';
    if (!empty($arrayfields['e.fk_bike']['checked']) && !empty($conf->bike->enabled)) print_liste_field_titre($arrayfields['e.fk_bike']['label'], $_SERVER["PHP_SELF"], 'e.fk_bike', '', $param, '', $sortfield, $sortorder, '');
    if (!empty($arrayfields['e.fk_user']['checked'])) print_liste_field_titre($arrayfields['e.fk_user']['label'], $_SERVER["PHP_SELF"], 'e.fk_user', '', $param, '', $sortfield, $sortorder, '');
    if (!empty($arrayfields['e.dates']['checked'])) print_liste_field_titre($arrayfields['e.dates']['label'], $_SERVER["PHP_SELF"], 'e.dates', '', $param, '', $sortfield, $sortorder);
    if (!empty($arrayfields['e.datee']['checked'])) print_liste_field_titre($arrayfields['e.datee']['label'], $_SERVER["PHP_SELF"], 'e.datee', '', $param, '', $sortfield, $sortorder);
    if (!empty($arrayfields['e.datec']['checked'])) print_liste_field_titre($arrayfields['e.datec']['label'], $_SERVER["PHP_SELF"], 'e.datec', '', $param, '', $sortfield, $sortorder);
    if (!empty($arrayfields['e.tms']['checked'])) print_liste_field_titre($arrayfields['e.tms']['label'], $_SERVER["PHP_SELF"], "e.tms", "", $param, 'align="left" class="nowrap"', $sortfield, $sortorder);

    print_liste_field_titre('', $_SERVER["PHP_SELF"], "", '', $param, 'align="center"', $sortfield, $sortorder, 'maxwidthsearch ');

    print '</tr>' . "\n";

    $generic_user = new User($db);
    $generic_bike = new Bike($db);
    $generic_stand = new Stand($db);

    $i = 0;
    $totalarray = array('nbfield' => 0);
    while ($i < min($num, $limit)) {
        $obj = $db->fetch_object($resql);


        $generic_bike->id = $obj->bike_id;
        $generic_bike->ref = $obj->bike_ref;
        $generic_bike->name = $obj->bike_name;

        $generic_user->id = $obj->user_id;
        $generic_user->login = $obj->user_login;
        $generic_user->firstname = $obj->user_firstname;
        $generic_user->lastname = $obj->user_lastname;

        print '<tr class="oddeven">';

        //

        if (!empty($arrayfields['e.fk_bike']['checked']) && !empty($conf->bike->enabled)) {
            print '<td align="left">';
            print $obj->fk_bike > 0 ? $generic_bike->getNomUrl(1) : '&nbsp;';
            print '</td>';
            if (!$i) $totalarray['nbfield']++;
        }

        if (!empty($arrayfields['e.fk_user']['checked'])) {
            print '<td align="left">';
            print $obj->fk_user > 0 ? $generic_user->getNomUrl(1) : '&nbsp;';
            print '</td>';
            if (!$i) $totalarray['nbfield']++;
        }

        if (!empty($arrayfields['e.dates']['checked'])) {
            print '<td align="left">';
            print dol_print_date($db->jdate($obj->dates), 'dayhour');
            print '</td>';
            if (!$i) $totalarray['nbfield']++;
        }

        if (!empty($arrayfields['e.datee']['checked'])) {
            print '<td align="left">';
            print dol_print_date($db->jdate($obj->datee), 'dayhour');
            print '</td>';
            if (!$i) $totalarray['nbfield']++;
        }
        //
        if (!empty($arrayfields['e.datec']['checked'])) {
            print '<td align="left">';
            print dol_print_date($db->jdate($obj->datec), 'dayhour');
            print '</td>';
            if (!$i) $totalarray['nbfield']++;
        }

        // Date modification
        if (!empty($arrayfields['e.tms']['checked'])) {
            print '<td align="left" class="nowrap">';
            print dol_print_date($db->jdate($obj->tms), 'dayhour', 'tzuser');
            print '</td>';
            if (!$i) $totalarray['nbfield']++;
        }

        // Action column
        print '<td class="nowrap" align="center">';
        print '<a href="'.$_SERVER["PHP_SELF"].'?action=delete&amp;id='.$obj->rowid.'">'.img_delete().'</a>';
        print '</td>';
        if (!$i) $totalarray['nbfield']++;

        print "</tr>\n";

        $i++;
    }

    $db->free($resql);

    $parameters = array('arrayfields' => $arrayfields, 'sql' => $sql);
    $reshook = $hookmanager->executeHooks('printFieldListFooter', $parameters);    // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;

    print '</table>' . "\n";
    print '</div>';

    print '</form>' . "\n";

} else {
    dol_print_error($db);
}

// End of page
llxFooter();
$db->close();
