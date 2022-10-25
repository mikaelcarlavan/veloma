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
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/veloma/class/site.class.php
 *  \ingroup    veloma
 *  \brief      File of class to manage site
 */

require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions.lib.php';
dol_include_once("/veloma/class/veloma.class.php");
dol_include_once("/veloma/class/veloma.sms.class.php");


/**
 * Class to manage products or services
 */
class Site extends CommonObject
{
    public $element = 'site';
    public $table_element = '';
    public $fk_element = '';
    public $picto = '';
    public $ismultientitymanaged = 0;    // 0=No test on entity, 1=Test with field entity, 2=Test with link by societe


    /**
     *  Constructor
     *
     * @param DoliDB $db Database handler
     */
    function __construct($db)
    {
        global $langs;

        $this->db = $db;
    }

    /**
     *    Start
     *
     * @return int                        Id of gestion if OK, < 0 if KO
     */
    function start(&$user)
    {
        global $conf, $langs, $mysoc;

        $error = 0;

        if (isset($_SESSION["dol_login"])) {
            // We are already into an authenticated session
            $login = $_SESSION["dol_login"];
            $entity = $_SESSION["dol_entity"];

            $resultFetchUser = $user->fetch('', $login, '', 1, ($entity > 0 ? $entity : -1));
            if ($resultFetchUser <= 0) {
                $error++;
                $prefix = dol_getprefix('');
                $sessionname = 'VELSESSID_' . $prefix;

                // Account has been removed after login
                dol_syslog("Can't load user even if session logged. _SESSION['dol_login']=" . $login, LOG_WARNING);
                session_destroy();
                session_name($sessionname);
                session_set_cookie_params(0, '/', null, false, true); // Add tag httponly on session cookie
                session_start();

                $message = $resultFetchUser == 0 ? $langs->trans("ErrorCantLoadUserFromDolibarrDatabase", $login) : $user->error;
                $this->addError($message);
            } else {
                $this->load($user);
            }
        }

        return $error > 0 ? -$error : 1;
    }


    /**
     *    Start
     *
     * @return int                        Id of gestion if OK, < 0 if KO
     */
    function load(&$user)
    {
        global $conf, $langs, $mysoc;

        $error = 0;

        // Store value into session (values always stored)
        $_SESSION["dol_login"] = $user->login;
        $_SESSION["dol_authmode"] = '';
        $_SESSION["dol_tz"] = '';
        $_SESSION["dol_tz_string"] = '';
        $_SESSION["dol_dst"] = 3;
        $_SESSION["dol_dst_observed"] = 3;
        $_SESSION["dol_dst_first"] = 3;
        $_SESSION["dol_dst_second"] = 3;
        $_SESSION["dol_screenwidth"] = 3;
        $_SESSION["dol_screenheight"] = 3;
        $_SESSION["dol_company"] = $conf->global->MAIN_INFO_SOCIETE_NOM;
        $_SESSION["dol_entity"] = $conf->entity;

        $user->update_last_login_date();

        // Load permissions
        $user->getrights();

        if (!empty($user->firstname) && !empty($user->lastname)) {
            $user->initials = substr($user->firstname, 0, 1) . substr($user->lastname, 0, 1);
        } else {
            $user->initials = substr($user->email, 0, 1);
        }

        return 1;
    }

    /**
     *    Login
     *
     * @return int                        Id of gestion if OK, < 0 if KO
     */
    function login(&$user)
    {
        global $conf, $langs, $mysoc;

        $error = 0;
        $langs->load('veloma@veloma');

        $usertotest = GETPOST("login-username", "alpha");
        $passwordtotest = GETPOST('login-password', 'none');
        $entitytotest = !empty($conf->entity) ? $conf->entity : 1;

        $veloma = new Veloma($this->db);
        $usertotest = $veloma->getLogin($veloma->formatNumber($usertotest));

        $result = checkLoginPassEntity($usertotest, $passwordtotest, $entitytotest, array('dolibarr'));

        if ($result) {
            $resultFetchUser = $user->fetch('', $result, '', 1, ($entitytotest > 0 ? $entitytotest : -1));
            if ($resultFetchUser <= 0) {
                dol_syslog('User not found, connexion refused');
                $prefix = dol_getprefix('');
                $sessionname = 'VELSESSID_' . $prefix;
                session_destroy();
                session_name($sessionname);
                session_set_cookie_params(0, '/', null, false, true); // Add tag httponly on session cookie
                session_start();

                $message = $resultFetchUser == 0 ? $langs->trans("VelomaUserNotFound") : $user->error;
                $this->addError($message);
                $error++;
            } else {
                $_SESSION["dol_login"] = $user->login;
                $_SESSION["dol_entity"] = $user->entity;
            }
        } else {
            $error++;
            // Bad password. No authmode has found a good password.
            // We set a generic message if not defined inside function checkLoginPassEntity or subfunctions
            $this->addError($langs->trans("ErrorBadLoginPassword"));
        }


        return $error > 0 ? -$error : $this->start($user);
    }


    function passwordrequest()
    {
        global $conf, $langs;

        $langs->load('veloma@veloma');

        $login = GETPOST("password-username", "alpha");
        $veloma = new Veloma($this->db);
        $login = $veloma->formatNumber($login);

        $error = 0;
        if ($login) {
            $sql = "SELECT u.*";
            $sql .= " FROM " . MAIN_DB_PREFIX . "user as u";
            $sql .= " WHERE u.user_mobile = '" . $this->db->escape($login) . "'";
            $sql .= " LIMIT 1";
            $result = $this->db->query($sql);

            if ($this->db->num_rows($result) <= 0) {
                $this->addError($langs->trans('VelomaUserNotFound'));
            } else {
                $obj = $this->db->fetch_object($result);

                $id = $obj->rowid;

                $user = new User($this->db);
                if ($user->fetch($id) > 0) {
                    $newpassword = $user->setPassword($user, '', 1);

                    if (!empty($newpassword)) {

                        $confirmation_code = dol_hash($newpassword, 'sha1');
                        $confirmation_code = substr($confirmation_code, -4);

                        /*
                        $outputlangs = new Translate("", $conf);
                        if (isset($conf->global->MAIN_LANG_DEFAULT) && $conf->global->MAIN_LANG_DEFAULT != 'auto') {    // If user has defined its own language (rare because in most cases, auto is used)
                            $outputlangs->setDefaultLang($conf->global->MAIN_LANG_DEFAULT);
                        } else {    // If user has not defined its own language, we used current language
                            $outputlangs = $langs;
                        }

                        // Load translation files required by the page
                        $outputlangs->loadLangs(array("main", "errors", "users", "other"));

                        $outputlangs->load('veloma@veloma');

                        $subject = $outputlangs->transnoentitiesnoconv("VelomaSubjectRequestPassword");

                        $mesg = $outputlangs->transnoentitiesnoconv("VelomaRequestPasswordReceived") . "<br /><br />";
                        $mesg .= $outputlangs->transnoentitiesnoconv("VelomaConfirmationCode", $confirmation_code) . "<br /><br />";
                        $mesg .= $outputlangs->transnoentitiesnoconv("VelomaForgetIfNothing") . "<br /><br />";

                        $msgishtml = 1;

                        $mailfile = new CMailFile(
                            $subject,
                            $user->email,
                            $conf->global->MAIN_MAIL_EMAIL_FROM,
                            $mesg,
                            array(),
                            array(),
                            array(),
                            '',
                            '',
                            0,
                            $msgishtml
                        );

                        // Success
                        if ($mailfile->sendfile()) {
                            $this->addMessage($langs->trans('VelomaConfirmationCodeSent'));
                        } else {
                            $this->addError($mailfile->error);
                            $error++;
                        }*/


                        $this->addMessage($langs->trans('VelomaConfirmationCodeSent'));

                        $response = $langs->transnoentities('VelomaConfirmationCodeSms', $confirmation_code);
                        $sms = new VelomaSMS($this->db);
                        $sms->create($user->user_mobile, $response, $user);

                    } else {
                        $this->addError($langs->trans('VelomaErrorWhileGeneratingConfirmationCode'));
                        $error++;
                    }
                } else {
                    $this->addError($langs->trans('VelomaUserNotFound'));
                    $error++;
                }
            }
        } else {
            $this->addError($langs->trans('VelomaUserNotFound'));
            $error++;
        }

        return $error > 0 ? -$error : 1;
    }


    function passwordvalidation()
    {
		global $conf, $langs;
		$langs->load('veloma@veloma');

        $login = GETPOST("validation-username", "alpha");
        $confirmation_code = GETPOST("validation-code", "alpha");
        $veloma = new Veloma($this->db);
        $login = $veloma->formatNumber($login);
        $error = 0;
		if ($login)
		{
			$sql = "SELECT u.*";
			$sql.= " FROM ".MAIN_DB_PREFIX."user as u";
			$sql.= " WHERE u.user_mobile = '".$this->db->escape($login)."'";
			$sql.= " LIMIT 1";
			$result = $this->db->query($sql);

			if ($this->db->num_rows($result) <= 0)
			{
                $this->addError($langs->transnoentities('VelomaUserNotFound'));
                $error++;
			}
			else
			{
				$obj = $this->db->fetch_object($result);

				$id = $obj->rowid;

				$user = new User($this->db);
				if ($user->fetch($id) > 0)
				{
					$newpassword = $user->pass_temp;

					$confirmation = dol_hash($newpassword, 'sha1');
					$confirmation = substr($confirmation, -4);

					if ($confirmation_code == $confirmation)
					{
						$newpassword = $user->setPassword($user, $user->pass_temp, 0);

						/*$outputlangs = new Translate("", $conf);
						if (isset($conf->global->MAIN_LANG_DEFAULT) && $conf->global->MAIN_LANG_DEFAULT != 'auto')
						{	// If user has defined its own language (rare because in most cases, auto is used)
							$outputlangs->setDefaultLang($conf->global->MAIN_LANG_DEFAULT);
						}
						else
						{	// If user has not defined its own language, we used current language
							$outputlangs = $langs;
						}

						// Load translation files required by the page
						$outputlangs->loadLangs(array("main", "errors", "users", "other"));

						$outputlangs->load('veloma@veloma');

						$subject = $outputlangs->transnoentitiesnoconv("VelomaSubjectResetPassword");

						$mesg = $outputlangs->transnoentitiesnoconv("VelomaResetPassword")."<br /><br />";
						$mesg.= $outputlangs->transnoentitiesnoconv("VelomaNewPassword", $newpassword)."<br /><br />";

						$msgishtml = 1;
			
						$mailfile = new CMailFile(
							$subject,
							$user->email,
							$conf->global->MAIN_MAIL_EMAIL_FROM,
							$mesg,
							array(),
							array(),
							array(),
							'',
							'',
							0,
							$msgishtml
						);
				
						// Success
						if ($mailfile->sendfile())
						{
							$this->addMessage($langs->trans('VelomaPasswordSent'));
						}
						else
						{
                            $this->addError($langs->trans('VelomaErrorWhileSendingNewPassword'));
                            $error++;
						}*/

                        $this->addMessage($langs->trans('VelomaPasswordSent'));

                        $response = $langs->transnoentities('VelomaPasswordSms', $newpassword);
                        $sms = new VelomaSMS($this->db);
                        $sms->create($user->user_mobile, $response, $user);

					}
					else
					{
                        $this->addError($langs->trans('VelomaConfirmationCodeDoesNotMatch'));
                        $error++;
					}
				}
				else
				{
                    $this->addError($langs->trans('VelomaUserNotFound'));
                    $error++;
				}
			}
		}
		else
		{
            $this->addError($langs->trans('VelomaUserNotFound'));
            $error++;
		}

	    return $error > 0 ? -$error : 1;
    }

    function account(&$user, $prefix = 'account')
    {
        global $conf, $langs;

        $error = 0;

        $veloma = new Veloma($this->db);

        $email = GETPOST(sprintf('%s-email', $prefix));
        $phone = GETPOST(sprintf('%s-phone', $prefix));
        $firstname = GETPOST(sprintf('%s-firstname', $prefix));
        $lastname = GETPOST(sprintf('%s-lastname', $prefix));
        $password = GETPOST(sprintf('%s-password', $prefix));

        $phone = preg_replace('/\s+/', '', $phone);

        // check mandatory fields
        if (!empty($email)) {
            if ($this->checkEmail($email) < 0) {
                $error++;
            }
        }

        if (empty($phone)) {
            $error++;
            $this->addError($langs->transnoentities('VelomaPhoneFieldIsMissing'));
        } else {
            $phone = $veloma->formatNumber($phone);

            if ($this->checkPhone($phone) < 0) {
                $error++;
            }
        }

        /* if (empty($firstname)) {
             $error++;
             $this->addError($langs->transnoentities('VelomaFirstNameFieldIsMissing'));
         }

         if (empty($lastname)) {
             $error++;
             $this->addError($langs->transnoentities('VelomaLastNameFieldIsMissing'));
         }*/

        /*if (empty($password)) {
            $error++;
            $this->addError($langs->transnoentities('VelomaPasswordFieldIsMissing'));
        }*/

        if (!$error) {

            $user->login = $veloma->getLogin($phone);
            $user->lastname = empty($lastname) ? $phone : $lastname;
            $user->firstname = $firstname;
            $user->email = $email;
            $user->user_mobile = $phone;

            $user->api_key = dol_hash($user->login . uniqid() . $conf->global->MAIN_API_KEY, 1);

            if ($user->update($user) < 0) {
                $error++;
                $this->addError($langs->transnoentities('VelomaErrorWhileUpdatingUser'));

                return -$error;
            } else {
                if (!empty($password)) {
                    $user->setPassword($user, $password, 0);
                }
                
                $user->fetch($user->id);
                $this->addMessage($langs->transnoentities('VelomaAccountUpdated'));

                return 1;
            }
        } else {
            return -$error;
        }
    }

    function register(&$user, $prefix = 'register')
    {
        global $conf, $langs;

        $error = 0;

        $veloma = new Veloma($this->db);

        $email = GETPOST(sprintf('%s-email', $prefix));
        $phone = GETPOST(sprintf('%s-phone', $prefix));
        $firstname = GETPOST(sprintf('%s-firstname', $prefix));
        $lastname = GETPOST(sprintf('%s-lastname', $prefix));

        $phone = preg_replace('/\s+/', '', $phone);

        // check mandatory fields
        if (!empty($email)) {
            if ($this->checkEmail($email) < 0) {
                $error++;
            }
        }

        if (empty($phone)) {
            $error++;
            $this->addError($langs->transnoentities('VelomaPhoneFieldIsMissing'));
        } else {
            $phone = $veloma->formatNumber($phone);

            if ($this->checkPhone($phone) < 0) {
                $error++;
            }
        }

       /* if (empty($firstname)) {
            $error++;
            $this->addError($langs->transnoentities('VelomaFirstNameFieldIsMissing'));
        }

        if (empty($lastname)) {
            $error++;
            $this->addError($langs->transnoentities('VelomaLastNameFieldIsMissing'));
        }*/

        /*if (empty($password)) {
            $error++;
            $this->addError($langs->transnoentities('VelomaPasswordFieldIsMissing'));
        }*/

        if (!$error) {
            $fuser = new User($this->db);

            $fuser->login = $veloma->getLogin($phone);
            $fuser->pass = '';
            $fuser->lastname = empty($lastname) ? $phone : $lastname;
            $fuser->firstname = $firstname;
            $fuser->email = $email;
            $fuser->user_mobile = $phone;

            $fuser->api_key = dol_hash($fuser->login . uniqid() . $conf->global->MAIN_API_KEY, 1);

            if ($fuser->create($user) < 0) {
                $error++;
                $this->addError($langs->transnoentities('VelomaErrorWhileCreatingUser'));

                return -$error;
            } else {
                $user->fetch($fuser->id);
                $newpassword = $user->setPassword($user, $user->pass_temp, 0);

                // Ajout au groupe
                if (isset($conf->global->VELOMA_USERS_GROUP_ID) && $conf->global->VELOMA_USERS_GROUP_ID > 0) {
                    $user->SetInGroup($conf->global->VELOMA_USERS_GROUP_ID, $conf->entity);
                }

                $this->addMessage($langs->transnoentities('VelomaWelcomeNewUser'));

                $response = $langs->transnoentities('VelomaWelcomeNewUserSms', $newpassword);
                $sms = new VelomaSMS($this->db);
                $sms->create($phone, $response, $user);

                return $this->load($user);
            }
        } else {
            return -$error;
        }
    }

    /**
     *    Check phone
     *
     * @return int                        Id if OK, < 0 if KO
     */
    function checkPhone($phone)
    {
        global $conf, $user, $langs;

        $langs->load('veloma@veloma');

        $sql = "SELECT u.*";
        $sql .= " FROM " . MAIN_DB_PREFIX . "user as u";
        $sql .= " WHERE u.user_mobile = '" . $this->db->escape($phone) . "'";
        $sql .= " AND u.rowid <> " . $user->id;

        $result = $this->db->query($sql);

        if ($this->db->num_rows($result) > 0) {
            $this->addError($langs->trans('VelomaPhoneAlreadyUsed'));
            return -1;
        }

        return 1;
    }

    /**
     *	Check email
     *
     *	@return int			     		Id if OK, < 0 if KO
     */
    function checkEmail($email)
    {
        global $conf, $user, $langs;

        $langs->load('veloma@veloma');

        if (!isValidEmail($email))
        {
            $this->addError($langs->trans('VelomaIncorrectEmailAddress'));
            return -1;
        }

        $sql = "SELECT u.*";
        $sql.= " FROM ".MAIN_DB_PREFIX."user as u";
        $sql.= " WHERE u.email = '".$this->db->escape($email)."'";
        $sql.= " AND u.rowid <> ".$user->id;

        $result = $this->db->query($sql);

        if ($this->db->num_rows($result) > 0)
        {
            $this->addError($langs->trans('VelomaEmailAddressAlreadyUsed'));
            return -1;
        }

        return 1;
    }

    function addError($error)
    {
        global $conf, $langs, $user;

        $errors = isset($_SESSION['dol_errors']) ? $_SESSION['dol_errors'] : array();

        $errors[] = $error;
        $_SESSION['dol_errors'] = $errors;
    }

    function addMessage($message)
    {
        global $conf, $langs, $user;

        $messages = isset($_SESSION['dol_messages']) ? $_SESSION['dol_messages'] : array();

        $messages[] = $message;
        $_SESSION['dol_messages'] = $messages;
    }

    function getErrors()
    {
        global $conf, $langs, $user;
        $errors = isset($_SESSION['dol_errors']) ? $_SESSION['dol_errors'] : array();
        $_SESSION['dol_errors'] = array();
        return $errors;
    }

    function getMessages()
    {
        global $conf, $langs, $user;
        $messages = isset($_SESSION['dol_messages']) ? $_SESSION['dol_messages'] : array();
        $_SESSION['dol_messages'] = array();
        return $messages;
    }
}