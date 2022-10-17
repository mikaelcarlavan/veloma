<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2017 Mikael Carlavan <contact@mika-carl.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more detaile.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/veloma/class/veloma.class.php
 *  \ingroup    veloma
 *  \brief      File of class to manage SMS
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
dol_include_once("/veloma/class/veloma.history.class.php");
dol_include_once("/dolipush/class/dolipush.class.php");
dol_include_once("/bike/class/bike.class.php");
dol_include_once("/stand/class/stand.class.php");


/**
 * Class to manage products or services
 */
class Veloma extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'veloma';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = '';

	/**
	 * @var string Name of subtable line
	 */
	public $table_element_line = '';

	/**
	 * @var string Name of class line
	 */
	public $class_element_line = '';

	/**
	 * @var string Field name with ID of parent key if this field has a parent
	 */
	public $fk_element = 'fk_veloma';

	/**
	 * @var string String with name of icon for commande class. Here is object_order.png
	 */
	public $picto = 'veloma2@veloma';

	/**
	 * 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	 * @var int
	 */
	public $ismultientitymanaged = 1;
	/**
	 * {@inheritdoc}
	 */
	protected $table_ref_field = '';


	/**
	 *  'type' if the field format ('integer', 'integer:ObjectClass:PathToClass[:AddCreateButtonOrNot[:Filter]]', 'varchar(x)', 'double(24,8)', 'real', 'price', 'text', 'html', 'date', 'datetime', 'timestamp', 'duration', 'mail', 'phone', 'url', 'password')
	 *         Note: Filter can be a string like "(t.ref:like:'SO-%') or (t.date_creation:<:'20160101') or (t.nature:is:NULL)"
	 *  'label' the translation key.
	 *  'enabled' is a condition when the field must be managed.
	 *  'position' is the sort order of field.
	 *  'notnull' is set to 1 if not null in database. Set to -1 if we must set data to null if empty ('' or 0).
	 *  'visible' says if field is visible in list (Examples: 0=Not visible, 1=Visible on list and create/update/view forms, 2=Visible on list only, 3=Visible on create/update/view form only (not list), 4=Visible on list and update/view form only (not create). 5=Visible on list and view only (not create/not update). Using a negative value means field is not shown by default on list but can be selected for viewing)
	 *  'noteditable' says if field is not editable (1 or 0)
	 *  'default' is a default value for creation (can still be overwrote by the Setup of Default Values if field is editable in creation form). Note: If default is set to '(PROV)' and field is 'ref', the default value will be set to '(PROVid)' where id is rowid when a new record is created.
	 *  'index' if we want an index in database.
	 *  'foreignkey'=>'tablename.field' if the field is a foreign key (it is recommanded to name the field fk_...).
	 *  'searchall' is 1 if we want to search in this field when making a search from the quick search button.
	 *  'isameasure' must be set to 1 if you want to have a total on list for this field. Field type must be summable like integer or double(24,8).
	 *  'css' is the CSS style to use on field. For example: 'maxwidth200'
	 *  'help' is a string visible as a tooltip on field
	 *  'showoncombobox' if value of the field must be visible into the label of the combobox that list record
	 *  'disabled' is 1 if we want to have the field locked by a 'disabled' attribute. In most cases, this is never set into the definition of $fields into class, but is set dynamically by some part of code.
	 *  'arrayofkeyval' to set list of value if type is a list of predefined values. For example: array("0"=>"Draft","1"=>"Active","-1"=>"Cancel")
	 *  'comment' is not used. You can store here any text of your choice. It is not used by application.
	 *
	 *  Note: To have value dynamic, you can set value to 0 in definition and edit the value on the fly into the constructor.
	 */

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields = array();
	// END MODULEBUILDER PROPERTIES

    /**
	 *  Constructor
	 *
	 *  @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		global $langs;

		$this->db = $db;
	}

    /**
     *  Process SMS
     *
     */
    function process($object)
    {
        global $conf, $langs;

        $dolipush = new Dolipush($this->db);

        if ($object->type == Dolipush::TYPE_RECEIVED) {
            // Process SMS
            $text = $object->text;
            $number = $object->number;

            //$u = $veloma->getUser($number);
            dol_syslog("Veloma::process received from ".$number." text ".$text);

            $history = new VelomaHistory($this->db);

            $data = explode(' ', $text);
            if (count($data) > 0) {
                $action = trim(strtoupper($data[0]));

                $user = $this->getUser($number);

                $response = '';
                dol_syslog("Veloma::process action is ".$action);

                if ($user->id > 0) {
                    if ($action == $langs->transnoentities('VelomaHelpCommand')) {
                        $response = $this->getHelp($user);
                        $this->createHistory($user, $action, $text);
                    } else if ($action == 'CREDIT' && !empty($conf->global->VELOMA_USE_CREDIT)) {
                        $options = $user->array_options;
                        $credit = !empty($options['options_veloma_credit']) ? $options['options_veloma_credit'] : 0;
                        $response = $langs->transnoentities('VelomaUserCredit', $credit);

                        $this->createHistory($user, $action, $text);
                    } else if ($action == trim($langs->transnoentities('VelomaFreeCommand'))) {
                        // List free bikes
                        $bike = new Bike($this->db);
                        $bikes = $bike->liste_free_array();
                        $numBikes = array();
                        if (is_array($bikes) && count($bikes)) {
                            foreach ($bikes as $bike) {
                                if ($bike->active) {
                                    $numBikes[] =  $bike->ref;
                                }
                            }
                        }

                        if (count($numBikes)) {
                            $response = $langs->transnoentities('VelomaFreeBikes', implode(' ', $numBikes));
                        } else {
                            $response = $langs->transnoentities('VelomaNoFreeBikes');
                        }

                        $this->createHistory($user, $action, $text);
                    } else if ($action == trim($langs->transnoentities('VelomaRentCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $bike = new Bike($this->db);
                        $current_fk_stand = -1;
                        if ($bike->fetch(0, $ref) > 0) {
                            if ($bike->active) {
                                $current_fk_stand = $bike->fk_stand;
                                if ($bike->fk_user > 0) {
                                    $response = $langs->transnoentities('VelomaBikeIsNotFree');
                                } else {
                                    $credit = !empty($user->array_options['options_veloma_credit']) ? floatval($user->array_options['options_veloma_credit']) : 0;
                                    $limit = !empty($user->array_options['options_veloma_limit']) ? intval($user->array_options['options_veloma_limit']) : 0;

                                    $rents = $history->getTotalRentsForUser($user->id);

                                    if (!empty($conf->global->VELOMA_USE_CREDIT) && $credit < 0) {
                                        $response = $langs->transnoentities('VelomaUserCreditIsInsufficient');
                                    } else if ($rents >= $limit) {
                                        $response = $langs->transnoentities('VelomaUserTooManyBikesRented');
                                    } else {
                                        $bike->fk_user = $user->id;
                                        $bike->fk_stand = -1;
                                        $bike->update($user);
                                        $response = $langs->transnoentities('VelomaBikeRented', $bike->code);
                                    }
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeNotFound');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $current_fk_stand);
                    } else if ($action == trim($langs->transnoentities('VelomaReturnCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $refs = isset($data[2]) ? trim($data[2]) : '';
                        $bike = new Bike($this->db);
                        $current_fk_stand = -1;
                        if ($bike->fetch(0, $ref) > 0) {
                            if ($bike->active) {
                                $current_fk_stand = $bike->fk_stand;
                                if ($user->id == $bike->fk_user) {
                                    $stand = new Stand($this->db);
                                    if ($stand->fetch(0, $refs) > 0) {
                                        $oldcode = $bike->code;
                                        $newcode = sprintf("%04d", rand(0, 9999));
                                        $bike->fk_user = -1;
                                        $bike->fk_stand = $stand->id;
                                        $bike->code = $newcode;
                                        $bike->update($user);
                                        $response = $langs->transnoentities('VelomaBikeReturned', $oldcode, $newcode);

                                        if (!empty($conf->global->VELOMA_USE_CREDIT)) {
                                            $rent = $history->getLastActionForBikeAndUser($user->id, $bike->id, trim($langs->trans('VelomaRentCommand')));
                                            if ($rent) {
                                                // Decrease credit
                                                $start = $rent->datec;
                                                $end = dol_now();

                                                $duration = $end - $start;
                                                $duration = $duration > 0 ? ceil($duration/60) : 0;

                                                $cost = $conf->global->VELOMA_RENT_COST ? floatval($conf->global->VELOMA_RENT_COST) : 0;
                                                $period = $conf->global->VELOMA_RENT_DURATION ? intval($conf->global->VELOMA_RENT_DURATION) : 0;
                                                $free = $conf->global->VELOMA_FREE_DURATION ? intval($conf->global->VELOMA_FREE_DURATION) : 0;
                                                $total = 0;
                                                if ($duration > 0) {
                                                    if ($duration > $free) {
                                                        $duration -= $free;
                                                        $total = $period > 0 ? $cost * ($duration/$period) : 0;
                                                        $total = round($total, 2);
                                                    }

                                                    $credit = !empty($user->array_options['options_veloma_credit']) ? floatval($user->array_options['options_veloma_credit']) : 0;
                                                    $credit -= $total;
                                                    $user->array_options['options_veloma_credit'] = $credit;
                                                    //$user->insertExtraFields();

                                                    $response = $langs->transnoentities('VelomaBikeReturnedWithCredit', $oldcode, $newcode, price($credit));
                                                }
                                            }
                                        }
                                    } else {
                                        $response = $langs->transnoentities('VelomaStandNotFound');
                                    }
                                } else {
                                    $response = $langs->transnoentities('VelomaBikeIsNotRentedByYou');
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeNotFound');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $current_fk_stand);
                    } else if ($action == trim($langs->transnoentities('VelomaForceRentCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $bike = new Bike($this->db);
                        $current_fk_stand = -1;
                        if ($user->admin) {
                            if ($bike->fetch(0, $ref) > 0) {
                                $current_fk_stand = $bike->fk_stand;
                                $bike->fk_user = $user->id;
                                $bike->fk_stand = -1;
                                $bike->update($user);
                                $response = $langs->transnoentities('VelomaBikeRented', $bike->code);
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeCommandNotAllowed');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $current_fk_stand);

                    } else if ($action == trim($langs->transnoentities('VelomaForceReturnCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $refs = isset($data[2]) ? trim($data[2]) : '';
                        $bike = new Bike($this->db);
                        $current_fk_stand = -1;
                        if ($user->admin) {
                            if ($bike->fetch(0, $ref) > 0) {
                                $current_fk_stand = $bike->fk_stand;
                                $stand = new Stand($this->db);
                                if ($stand->fetch(0, $refs) > 0) {
                                    $bike->fk_user = -1;
                                    $bike->fk_stand = $stand->id;
                                    $bike->update($user);
                                    $response = $langs->transnoentities('VelomaBikeReturned');
                                } else {
                                    $response = $langs->transnoentities('VelomaStandNotFound');
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeCommandNotAllowed');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $current_fk_stand);
                    } else if ($action == trim($langs->transnoentities('VelomaWhereCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $bike = new Bike($this->db);
                        if ($bike->fetch(0, $ref) > 0) {
                            if ($bike->active) {
                                if ($bike->fk_stand > 0) {
                                    $s = new Stand($this->db);
                                    if ($s->fetch($bike->fk_stand) > 0) {
                                        $response = $langs->transnoentities('VelomaBikeIsLocatedAt', $s->name, $s->latitude, $s->longitude);
                                    } else {
                                        $response = $langs->transnoentities('VelomaBikeStandNotFound');
                                    }
                                } else {
                                    $response = $langs->transnoentities('VelomaBikeNotRented');
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeNotFound');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $bike->fk_stand ?: -1);
                    } else if ($action == trim($langs->transnoentities('VelomaWhoCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $bike = new Bike($this->db);

                        if ($user->admin) {
                            if ($bike->fetch(0, $ref) > 0) {
                                if ($bike->fk_user > 0) {
                                    $u = new User($this->db);
                                    if ($u->fetch($bike->fk_user) > 0) {
                                        $response = $langs->transnoentities('VelomaBikeIsRentedBy', $u->user_mobile);
                                    } else {
                                        $response = $langs->transnoentities('VelomaBikeUserNotFound');
                                    }
                                } else {
                                    $response = $langs->transnoentities('VelomaBikeNotRented');
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeCommandNotAllowed');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $bike->fk_stand ?: -1);

                    } else if ($action == trim($langs->transnoentities('VelomaInfoCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $stand = new Stand($this->db);
                        if ($stand->fetch(0, $ref) > 0) {
                            $response = $langs->transnoentities('VelomaStandInfo', $stand->name, $stand->description, $stand->latitude, $stand->longitude);
                        } else {
                            $response = $langs->transnoentities('VelomaStandNotFound');
                        }

                        $this->createHistory($user, $action, $text, -1, $stand->id ?: -1);

                    } else if ($action == trim($langs->transnoentities('VelomaNoteCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        // Remove action
                        if (count($data)) {
                            array_shift($data);
                        }
                        // Remove id
                        if (count($data)) {
                            array_shift($data);
                        }

                        $note = count($data) ? implode(' ', $data) : '';
                        $bike = new Bike($this->db);

                        if ($bike->fetch(0, $ref) > 0) {
                            if ($bike->active) {
                                if (!empty($note)) {
                                    $bike->addline($note, $user->id);
                                    $response = $langs->transnoentities('VelomaBikeNoteAdded');
                                } else {
                                    $response = $langs->transnoentities('VelomaBikeNoteIsEmpty');
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeNotFound');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $bike->fk_stand ?: -1);

                    } else if ($action == trim($langs->transnoentities('VelomaTagCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        // Remove action
                        if (count($data)) {
                            array_shift($data);
                        }
                        // Remove id
                        if (count($data)) {
                            array_shift($data);
                        }

                        $note = count($data) ? implode(' ', $data) : '';
                        $stand = new Stand($this->db);

                        if ($stand->fetch(0, $ref) > 0) {
                            if (!empty($note)) {
                                $stand->addline($note, $user->id);
                                $response = $langs->transnoentities('VelomaStandTagAdded');
                            } else {
                                $response = $langs->transnoentities('VelomaStandTagIsEmpty');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaStandNotFound');
                        }

                        $this->createHistory($user, $action, $text, -1, $stand->id ?: -1);

                    } else if ($action == trim($langs->transnoentities('VelomaDelNoteCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $bike = new Bike($this->db);

                        if ($bike->fetch(0, $ref) > 0) {
                            if ($bike->active) {
                                $lineid = $bike->fetch_last_lineid($user->id);

                                if ($lineid) {
                                    $bike->deleteline($user, $lineid);
                                    $response = $langs->transnoentities('VelomaBikeLastNoteDeleted');
                                } else {
                                    $response = $langs->transnoentities('VelomaBikeNoNoteFound');
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeNotFound');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $bike->fk_stand);

                    } else if ($action == trim($langs->transnoentities('VelomaUnTagCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $stand = new Stand($this->db);

                        if ($stand->fetch(0, $ref) > 0) {
                            $lineid = $stand->fetch_last_lineid($user->id);

                            if ($lineid > 0) {
                                $stand->deleteline($user, $lineid);
                                $response = $langs->transnoentities('VelomaStandLastTagDeleted');
                            } else {
                                $response = $langs->transnoentities('VelomaStandNoTagFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaStandNotFound');
                        }

                        $this->createHistory($user, $action, $text, -1, $stand->id ?: -1);

                    } else if ($action == trim($langs->transnoentities('VelomaListCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';

                        $stand = new Stand($this->db);

                        if ($stand->fetch(0, $ref) > 0) {
                            // List free bikes
                            $bike = new Bike($this->db);
                            $bikes = $bike->liste_stand_array($stand->id);
                            $numBikes = array();
                            if (is_array($bikes) && count($bikes)) {
                                foreach ($bikes as $bike) {
                                    if ($bike->active) {
                                        $numBikes[] =  $bike->id;
                                    }
                                }
                            }

                            if (count($numBikes)) {
                                $response = $langs->transnoentities('VelomaBikesInStand', implode(' ', $numBikes));
                            } else {
                                $response = $langs->transnoentities('VelomaNoBikesInStand');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaStandNotFound');
                        }

                        $this->createHistory($user, $action, $text, -1, $stand->id ?: -1);

                    } else if ($action == trim($langs->transnoentities('VelomaAddCommand'))) {
                        $firstName = isset($data[1]) ? trim($data[1]) : '';
                        $lastName = isset($data[2]) ? trim($data[2]) : '';
                        $email = isset($data[3]) ? trim($data[3]) : '';
                        $user_mobile = isset($data[4]) ? trim($data[4]) : '';
                        $credit = isset($data[5]) ? price2num(trim($data[5])) : 0;

                        $test = new User($this->db);
                        if ($user->admin) {
                            $result = $test->fetch(0, '', '', -1, $email);
                            if ($result > 0 && $test->id != $user->id) {
                                $response = $langs->transnoentities('VelomaUserAlreadyExist');
                            } else {
                                if (!empty($email)) {
                                    $user->email = $email;
                                }
                                if (!empty($user_mobile)) {
                                    $login = $this->getLogin($user_mobile);
                                    $user->user_mobile = $user_mobile;
                                    $user->login = $login;
                                }
                                if (!empty($lastName)) {
                                    $user->lastname = $lastName;
                                }
                                if (!empty($firstName)) {
                                    $user->firstname = $firstName;
                                }
                                if (!empty($conf->global->VELOMA_USE_CREDIT)) {
                                    if (!empty($credit)) {
                                        $user->array_options['options_veloma_credit'] = floatval($credit);
                                    } else {
                                        $user->array_options['options_veloma_credit'] = floatval($conf->global->VELOMA_INITIAL_CREDIT);
                                    }
                                }
                                $user->array_options['options_veloma_limit'] = $conf->global->VELOMA_INITIAL_LIMIT;

                                if ($user->create($user) > 0) {
                                    $response = $langs->transnoentities('VelomaUserAdded');
                                } else {
                                    $response = $langs->transnoentities('VelomaErrorInCreatingUser');
                                }
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeCommandNotAllowed');
                        }

                        $this->createHistory($user, $action, $text, -1, -1);

                    } else if ($action == trim($langs->transnoentities('VelomaRevertCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $bike = new Bike($this->db);
                        $current_fk_stand = -1;
                        if ($bike->fetch(0, $ref) > 0) {
                            if ($bike->active) {
                                $current_fk_stand = $bike->fk_stand;
                                if ($bike->fk_user > 0) {
                                    if ($user->id == $bike->fk_user) {
                                        $fk_stand = $history->getLastStandForBike($bike->id);
                                        $bike->fk_user = -1;
                                        $bike->fk_stand = $fk_stand;
                                        $bike->update($user);
                                        $response = $langs->transnoentities('VelomaBikeRentCanceled');
                                    } else {
                                        $response = $langs->transnoentities('VelomaBikeIsNotRentedByYou');
                                    }
                                } else {
                                    $response = $langs->transnoentities('VelomaBikeIsNotRented');
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeNotFound');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $current_fk_stand);

                    } else if ($action == trim($langs->transnoentities('VelomaLastCommand'))) {
                        $ref = isset($data[1]) ? trim($data[1]) : '';
                        $bike = new Bike($this->db);
                        if ($user->admin) {
                            if ($bike->fetch(0, $ref) > 0) {
                                if ($bike->fk_user > 0) {
                                    $u = new User($this->db);
                                    if ($u->fetch($bike->fk_user) > 0) {
                                        $response = $langs->transnoentities('VelomaBikeIsRentedBy', $u->user_mobile);
                                    } else {
                                        $response = $langs->transnoentities('VelomaBikeUserNotFound');
                                    }
                                } else {
                                    $fk_user = $history->getLastUserForBike($bike->id);
                                    $u = new User($this->db);
                                    if ($u->fetch($fk_user) > 0) {
                                        $response = $langs->transnoentities('VelomaBikeWasRentedBy', $u->user_mobile);
                                    } else {
                                        $response = $langs->transnoentities('VelomaBikeUserNotFound');
                                    }
                                }
                            } else {
                                $response = $langs->transnoentities('VelomaBikeNotFound');
                            }
                        } else {
                            $response = $langs->transnoentities('VelomaBikeCommandNotAllowed');
                        }

                        $this->createHistory($user, $action, $text, $bike->id ?: -1, $bike->fk_stand ?: -1);
                    } else {
                        $response = $langs->transnoentities('VelomaUnknownCommand');
                    }

                } else {
                    $response = $langs->transnoentities('VelomaNotRegistered');
                }

                dol_syslog("Veloma::process get response ".$response);

                if (!empty($response)) {
                    $dolipush->send($number, $response);
                }
            }
        }


        return 1;
    }

    /**
     *  Get user from number (or create a new userif not found)
     *
     */
    function getUser($number)
    {
        global $conf;

        $user = new User($this->db);
        $login = $this->getLogin($number);
        if ($user->fetch('', $login) <= 0) {
            if (!empty($conf->global->VELOMA_ALLOW_UNREGISTERED_USERS)) {
                $user->login = $login;
                $user->lastname = $number;
                $user->user_mobile = $number;
                $user->array_options['options_veloma_limit'] = $conf->global->VELOMA_INITIAL_LIMIT;
                if (!empty($conf->global->VELOMA_USE_CREDIT)) {
                    $user->array_options['options_veloma_credit'] = $conf->global->VELOMA_INITIAL_CREDIT;
                }

                if ($user->create($user) > 0) {
                    $user->fetch($user->id);
                }
            }
        }

        return $user;
    }

    /**
     *  Get user from number (or create a new userif not found)
     *
     */
    function getLogin($number)
    {
        $number = str_replace('+33', '', $number);
        if (substr($number, 0, 1) == '0') {
            $number = substr($number, 1);
        }

        return 'user'.$number;
    }

    /**
     *  Return help
     *
     */
    function getHelp($user)
    {
        global $conf, $langs;

        $result = $langs->transnoentities('VelomaListCommands')."\n";
        $result.= $langs->transnoentities('VelomaHelpCommandDetails', $langs->transnoentities('VelomaHelpCommand'))."\n";
        $result.= $langs->transnoentities('VelomaFreeCommandDetails', $langs->transnoentities('VelomaFreeCommand'))."\n";
        $result.= $langs->transnoentities('VelomaRentCommandDetails', $langs->transnoentities('VelomaRentCommand'))."\n";
        $result.= $langs->transnoentities('VelomaReturnCommandDetails', $langs->transnoentities('VelomaReturnCommand'))."\n";
        $result.= $langs->transnoentities('VelomaWhereCommandDetails', $langs->transnoentities('VelomaWhereCommand'))."\n";
        $result.= $langs->transnoentities('VelomaInfoCommandDetails', $langs->transnoentities('VelomaInfoCommand'))."\n";
        if ($user->admin) {
            $result.= $langs->transnoentities('VelomaWhoCommandDetails', $langs->transnoentities('VelomaWhoCommand'))."\n";
            $result.= $langs->transnoentities('VelomaListCommandDetails', $langs->transnoentities('VelomaListCommand'))."\n";
            $result.= $langs->transnoentities('VelomaAddCommandDetails', $langs->transnoentities('VelomaAddCommand'));
        }

        return $result;
    }

    function createHistory($user, $action, $text, $fk_bike = -1, $fk_stand = -1)
    {
        global $conf, $langs;

        $history = new VelomaHistory($this->db);
        $history->fk_user = $user->id;
        $history->action = $action;
        $history->parameters = $text;
        $history->fk_bike = $fk_bike;
        $history->fk_stand = $fk_stand;
        $history->create($user);
    }
}
