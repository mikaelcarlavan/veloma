<?php
/* Copyright (C) 2017 Mikael Carlavan <contact@mika-carl.fr>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    core/triggers/interface_99_modVeloma_VelomaTriggers.class.php
 * \ingroup veloma
 * \brief   Example trigger.
 *
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
include_once DOL_DOCUMENT_ROOT.'/core/class/html.formmail.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';

dol_include_once("/veloma/class/veloma.class.php");


/**
 *  Class of triggers for Veloma module
 */
class InterfaceVelomaTriggers extends DolibarrTriggers
{
	/**
	 * @var DoliDB Database handler
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		$this->name = preg_replace('/^Interface/i', '', get_class($this));
		$this->family = "crm";
		$this->description = "Veloma triggers.";
		// 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0.0';
		$this->picto = 'veloma2@veloma';
	}

	/**
	 * Trigger name
	 *
	 * @return string Name of trigger file
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Trigger description
	 *
	 * @return string Description of trigger file
	 */
	public function getDesc()
	{
		return $this->description;
	}


	/**
	 * Function called when a Dolibarrr business event is done.
	 * All functions "runTrigger" are triggered if file
	 * is inside directory core/triggers
	 *
	 * @param string 		$action 	Event action code
	 * @param CommonObject 	$object 	Object
	 * @param User 			$user 		Object user
	 * @param Translate 	$langs 		Object langs
	 * @param Conf 			$conf 		Object conf
	 * @return int              		<0 if KO, 0 if no triggered ran, >0 if OK
	 */
	public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
	{
        if (empty($conf->veloma->enabled)) return 0;     // Module not active, we do nothing

	    // Put here code you want to execute when a Dolibarr business events occurs.
		// Data and type of action are stored into $object and $action

        $langs->load("other");

        switch ($action) {

            case 'DOLISMS_RECEIVED':

		        dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

		        $langs->load("veloma@veloma");

                $veloma = new Veloma($this->db);
                $veloma->process($object);

			break;

            case 'VELOMABOOKING_CREATE':

                dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

                $langs->load("veloma@veloma");

                $event = new ActionComm($this->db);

                $object->fetch($object->id);

                $username = $object->user ? $object->user->getFullName($langs) : '';
                $bike = $object->bike ? $object->bike->ref : '';

                $label = $langs->trans('VelomaBookingEvent', $bike);

                $event->priority = 0;
                $event->fulldayevent = 0;
                $event->location = '';
                $event->label = $label;
                $event->fk_project = 0;
                $event->datep = $object->dates;
                $event->datef = $object->datee;
                $event->percentage = 0;
                $event->duree = 0;

                $event->userassigned = array();
                $event->note_private = '';
                $event->type_code = 'AC_RDV';

                $fk_user = $object->fk_user ? $object->fk_user : 0;

                $event->userownerid = $fk_user;
                $result = $event->create($user);

                if ($result > 0) {
                    $object->fk_action_comm = $result;
                    $object->update($user, 1);
                }
                break;

            case 'VELOMABOOKING_DELETE':

                dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

                $langs->load("veloma@veloma");

                $actioncomm = new ActionComm($this->db);
                if ($actioncomm->fetch($object->fk_action_comm) > 0) {
                    $actioncomm->delete();
                }
                break;
		}

		return 0;
	}
}
