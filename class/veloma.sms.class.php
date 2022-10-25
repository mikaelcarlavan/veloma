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



/**
 * Class to manage products or services
 */
class VelomaSMS extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */
    public $element = 'veloma_sms';

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
    public $fk_element = '';

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
     * History id
     * @var int
     */
    public $id = 0;

    /**
     * Text.
     * @var string
     */
    public $text;

    /**
     * Number.
     * @var string
     */
    public $number;

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
        'number' =>array('type'=>'varchar(255)', 'label'=>'VelomaPhone', 'enabled'=>1, 'visible'=>1, 'position'=>25),
        'text' =>array('type'=>'text', 'label'=>'VelomaText', 'enabled'=>1, 'visible'=>1, 'position'=>30),
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
     *	Insert veloma into database
     *
     *	@param	User	$user     		User making insert
     *  @param  int		$notrigger	    0=launch triggers after, 1=disable triggers
     *
     *	@return int			     		Id of gestion if OK, < 0 if KO
     */
    function create($number, $text, $user)
    {
        global $conf, $langs, $mysoc;

        $error=0;

        dol_syslog(get_class($this)."::create", LOG_DEBUG);


        $this->text = $text;
        $this->number = $number;


        $result = $this->call_trigger('VELOMASMS_CREATE', $user);
        if ($result < 0) $error++;


        return $error > 0 ? -$error : 1;
    }
}
