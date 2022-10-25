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
 *  \file       htdocs/veloma/class/veloma.class.php
 *  \ingroup    veloma
 *  \brief      File of class to manage velomas
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobjectline.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

if (!empty($conf->stand->enabled)) {
    dol_include_once("/stand/class/stand.class.php");
}
if (!empty($conf->bike->enabled)) {
    dol_include_once("/bike/class/bike.class.php");
}


/**
 * Class to manage products or services
 */
class VelomaHistory extends CommonObject
{
	/**
	 * @var string ID to identify managed object
	 */
	public $element = 'veloma_history';

	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'veloma_history';

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
	public $fk_element = 'fk_veloma_history';

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
	protected $table_ref_field = 'rowid';

	/**
     * History id
     * @var int
     */
	public $id = 0;

    /**
     * Current bike id
     * @var int
     */
    public $fk_bike;

    /**
     * Current stand id
     * @var int
     */
    public $fk_stand;

    /**
     * Current user id
     * @var int
     */
    public $fk_user;

    /**
     * Action.
     * @var string
     */
    public $action;

    /**
     * Parameters.
     * @var string
     */
    public $parameters;

	/**
	 * Creation date
	 * @var int
	 */
	public $datec;

	/**
	 * Author id
	 * @var int
	 */
	public $user_author_id = 0;

	/**
	 * Timestamp
	 * @var int
	 */
	public $tms;

	/**
     * Entity
     * @var int
     */
	public $entity;

    /**
     * Stand
     * @var Stand
     */
    public $stand = null;


    /**
     * Current user
     * @var User
     */
    public $user = null;

    /**
     * Current bike
     * @var Bike
     */
    public $bike = null;

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
	public $fields = array(
		'rowid' =>array('type'=>'integer', 'label'=>'TechnicalID', 'enabled'=>1, 'visible'=>-1, 'notnull'=>1, 'position'=>10),
		'entity' =>array('type'=>'integer', 'label'=>'Entity', 'default'=>1, 'enabled'=>1, 'visible'=>-2, 'notnull'=>1, 'position'=>15, 'index'=>1),
		'action' =>array('type'=>'varchar(255)', 'label'=>'VelomaAction', 'enabled'=>1, 'visible'=>1, 'position'=>25),
		'parameters' =>array('type'=>'text', 'label'=>'VelomaParameters', 'enabled'=>1, 'visible'=>1, 'position'=>30),
        'fk_user' =>array('type'=>'integer:User:user/class/user.class.php', 'label'=>'VelomaUser', 'enabled'=>1, 'visible'=>1, 'position'=>35),
		'fk_stand' =>array('type'=>'integer:Stand:stand/class/stand.class.php', 'label'=>'VelomaStand', 'enabled'=>1, 'visible'=>1, 'position'=>40),
        'fk_bike' =>array('type'=>'integer:Stand:bike/class/bike.class.php', 'label'=>'VelomaBike', 'enabled'=>1, 'visible'=>1, 'position'=>42),
        'datec' =>array('type'=>'datetime', 'label'=>'DateCreation', 'enabled'=>1, 'visible'=>-1, 'position'=>70),
		'user_author_id' =>array('type'=>'integer:User:user/class/user.class.php', 'label'=>'Fk user author', 'enabled'=>1, 'visible'=>-1, 'position'=>80),
		'tms' =>array('type'=>'timestamp', 'label'=>'DateModification', 'enabled'=>1, 'visible'=>-1, 'notnull'=>1, 'position'=>100)
		);
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
     *  Load last stand for bike
     *
     * @param $fk_bike
     * @return    int              <0 if KO, >0 if OK
     */
    public function getLastStandForBike($fk_bike)
    {
        $sql = 'SELECT e.fk_stand';
        $sql .= ' FROM '.MAIN_DB_PREFIX.'veloma_history as e';
        $sql .= ' WHERE e.fk_stand > 0 AND e.fk_bike = '.(int)$fk_bike;
        $sql .= ' ORDER BY e.rowid DESC';
        $sql .= ' LIMIT 1';
        $result = $this->db->query($sql);
        if ($result) {
            $objp = $this->db->fetch_object($result);
            if ($objp) {
                return $objp->fk_stand;
            } else {
                return 0;
            }
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
    }

    /**
     *  Load last user for bike
     *
     * @param $fk_bike
     * @return    int              <0 if KO, >0 if OK
     */
    public function getLastUserForBike($fk_bike)
    {
        $sql = 'SELECT e.fk_user';
        $sql .= ' FROM '.MAIN_DB_PREFIX.'veloma_history as e';
        $sql .= ' WHERE e.fk_user > 0 AND e.fk_bike = '.(int)$fk_bike;
        $sql .= ' ORDER BY e.rowid DESC';
        $sql .= ' LIMIT 1';
        $result = $this->db->query($sql);
        if ($result) {
            $objp = $this->db->fetch_object($result);
            if ($objp) {
                return $objp->fk_user;
            } else {
                return 0;
            }
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
    }

    /**
     *  Get number of current rents for user
     *
     * @param $fk_user
     * @param $action
     * @return    int              <0 if KO, >0 if OK
     */
    function getTotalRentsForUser($fk_user)
    {
        $sql = "SELECT e.*";
        $sql .= " FROM ".MAIN_DB_PREFIX."bike as e";
        $sql .= " WHERE e.fk_user = ".(int)$fk_user;
        $sql .= " AND e.fk_stand <= 0";

        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->num_rows($result);
        }

        return 0;
    }

    /**
     *  Load rent for bike and user
     *
     * @param $fk_bike
     * @return    int              <0 if KO, >0 if OK
     */
    public function getLastActionForBikeAndUser($fk_user, $fk_bike, $action)
    {
        $sql = "SELECT e.*";
        $sql .= " FROM ".MAIN_DB_PREFIX."veloma_history as e";
        $sql .= " WHERE e.fk_user = ".(int)$fk_user;
        $sql .= " AND e.fk_bike = ".(int)$fk_bike;
        $sql .= " AND e.action = '".$this->db->escape($action)."'";
        $sql .= " ORDER BY e.rowid DESC";
        $sql .= " LIMIT 1";

        $result = $this->db->query($sql);
        if ($result) {
            $objp = $this->db->fetch_object($result);
            if ($objp) {
                $history = new VelomaHistory($this->db);
                $history->fetch($objp->rowid);
                return $history;
            } else {
                return 0;
            }
        } else {
            $this->error = $this->db->lasterror();
            return -1;
        }
    }

	/**
	 *	Insert veloma into database
	 *
	 *	@param	User	$user     		User making insert
	 *  @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
	 * 
	 *	@return int			     		Id of gestion if OK, < 0 if KO
	 */
	function create($user, $notrigger=0)
	{
		global $conf, $langs, $mysoc;

        $error=0;

		dol_syslog(get_class($this)."::create", LOG_DEBUG);

		$this->db->begin();

		$this->datec = dol_now();
		$this->entity = $conf->entity;
		$this->user_author_id = $user->id;


        $now = dol_now();

        $sql = "INSERT INTO ".MAIN_DB_PREFIX."veloma_history (";
        $sql.= " action";
        $sql.= " , parameters";
        $sql.= " , fk_bike";
        $sql.= " , fk_user";
        $sql.= " , fk_stand";
        $sql.= " , datec";
        $sql.= " , user_author_id";
        $sql.= " , entity";
        $sql.= " , tms";
        $sql.= ") VALUES (";
        $sql.= " ".(!empty($this->action) ? "'".$this->db->escape($this->action)."'" : "null");
        $sql.= ", ".(!empty($this->parameters) ? "'".$this->db->escape($this->parameters)."'" : "null");
        $sql.= ", ".(!empty($this->fk_bike) ? $this->fk_bike : "0");
        $sql.= ", ".(!empty($this->fk_user) ? $this->fk_user : "0");
        $sql.= ", ".(!empty($this->fk_stand) ? $this->fk_stand : "0");
        $sql.= ", ".(!empty($this->datec) ? "'".$this->db->idate($this->datec)."'" : "null");
        $sql.= ", ".(!empty($this->user_author_id) ? $this->user_author_id : "0");
        $sql.= ", ".(!empty($this->entity) ? $this->entity : "0");
        $sql.= ", '".$this->db->idate($now)."'";
        $sql.= ")";

        dol_syslog(get_class($this)."::Create", LOG_DEBUG);
        $result = $this->db->query($sql);
        if ( $result )
        {
            $id = $this->db->last_insert_id(MAIN_DB_PREFIX."veloma");

            if ($id > 0)
            {
                $this->id				= $id;
            }
            else
            {
                $error++;
                $this->error='ErrorFailedToGetInsertedId';
            }
        }
        else
        {
            $error++;
            $this->error=$this->db->lasterror();
        }


		if (! $error)
		{
			$result = $this->insertExtraFields();
			if ($result < 0) $error++;
		}
	

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Call trigger
	            $result = $this->call_trigger('VELOMAHISTORY_CREATE',$user);
	            if ($result < 0) $error++;
	            // End call triggers
			}
		}

		if (! $error)
		{
			$this->db->commit();
			return $this->id;
		}
		else
		{
			$this->db->rollback();
			return -$error;
		}

	}


	/**
	 *  Load a slice in memory from database
	 *
	 *  @param	int		$id      			Id of slide
	 *  @return int     					<0 if KO, 0 if not found, >0 if OK
	 */
	function fetch($id)
	{
		global $langs, $conf;

		dol_syslog(get_class($this)."::fetch id=".$id);


		// Check parameters
        if (empty($id))
        {
            $this->error = 'ErrorWrongParameters';
            //dol_print_error(get_class($this)."::fetch ".$this->error);
            return -1;
        }

		$sql = "SELECT e.rowid, e.action, e.datec, e.parameters, e.tms, e.fk_bike, e.fk_user, e.fk_stand, ";
		$sql.= " e.user_author_id, e.entity ";
		$sql.= " FROM ".MAIN_DB_PREFIX."veloma_history e";
        if ($id > 0) {
            $sql.= " WHERE e.rowid=".$id;
        }

		$resql = $this->db->query($sql);
		if ( $resql )
		{
			if ($this->db->num_rows($resql) > 0)
			{
				$obj = $this->db->fetch_object($resql);

				$this->id				= $obj->rowid;

				$this->user_author_id 	= $obj->user_author_id;
				$this->datec 			= $this->db->jdate($obj->datec);
				$this->tms 			    = $this->db->jdate($obj->tms);

                $this->action 		    = $obj->action;
				$this->parameters 	        = $obj->parameters;
				$this->fk_user 	        = $obj->fk_user;
                $this->fk_stand 	     = $obj->fk_stand;
                $this->fk_bike 	        = $obj->fk_bike;

				$this->entity			= $obj->entity;

                if ($this->fk_stand > 0 && !empty($conf->stand->enabled)) {
                    $this->stand = new Stand($this->db);
                    $this->stand->fetch($this->fk_stand);
                }

                if ($this->fk_user > 0) {
                    $this->user = new User($this->db);
                    $this->user->fetch($this->fk_user);
                }


                if ($this->fk_bike > 0 && !empty($conf->bike->enabled)) {
                    $this->bike = new Bike($this->db);
                    $this->bike->fetch($this->fk_bike);
                }

				$this->db->free($resql);

				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}
}
