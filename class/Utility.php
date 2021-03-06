<?php

namespace XoopsModules\Oledrion;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * oledrion
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * A set of useful and common functions
 *
 * @author        Hervé Thouzard - Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 *
 * Note: You should be able to use it without the need to instanciate it.
 */

use WideImage\WideImage;
use Xmf\Request;
use XoopsModules\Oledrion;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class Oledrion\Utility
 */
class Utility extends \XoopsObject
{
    const MODULE_NAME = 'oledrion';

    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------

    /**
     * Access the only instance of this class
     *
     * @return Oledrion\Utility
     *
     * @static
     * @staticvar   object
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Returns a module's option (with cache)
     *
     * @param  string $option    module option's name
     * @param  bool   $withCache Do we have to use some cache ?
     * @return mixed   option's value
     */
    public static function getModuleOption($option, $withCache = true)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $repmodule = static::MODULE_NAME;
        static $options = [];
        if (is_array($options) && array_key_exists($option, $options) && $withCache) {
            return $options[$option];
        }

        $retval = null;
        if (null !== $xoopsModuleConfig && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
            if (isset($xoopsModuleConfig[$option])) {
                $retval = $xoopsModuleConfig[$option];
            }
        } else {
            /** @var \XoopsModuleHandler $moduleHandler */
            $moduleHandler = xoops_getHandler('module');
            $module        = $moduleHandler->getByDirname($repmodule);
            $configHandler = xoops_getHandler('config');
            if ($module) {
                $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
                if (isset($moduleConfig[$option])) {
                    $retval = $moduleConfig[$option];
                }
            }
        }
        $options[$option] = $retval;

        return $retval;
    }

    /**
     * Is Xoops 2.3.x ?
     *
     * @return bool
     */
    public static function isX23()
    {
        $x23 = false;
        $xv  = str_replace('XOOPS ', '', XOOPS_VERSION);
        if ((int)mb_substr($xv, 2, 1) >= 3) {
            $x23 = true;
        }

        return $x23;
    }

    /**
     * Is Xoops 2.0.x ?
     *
     * @return bool
     */
    public static function isX20()
    {
        $x20 = false;
        $xv  = str_replace('XOOPS ', '', XOOPS_VERSION);
        if ('0' == mb_substr($xv, 2, 1)) {
            $x20 = true;
        }

        return $x20;
    }

    /**
     * Retreive an editor according to the module's option "form_options"
     *
     * @param  string $caption Caption to give to the editor
     * @param  string $name    Editor's name
     * @param  string $value   Editor's value
     * @param  string $width   Editor's width
     * @param  string $height  Editor's height
     * @param  string $supplemental
     * @return bool|\XoopsFormEditor The editor to use
     */
    public static function getWysiwygForm(
        $caption,
        $name,
        $value = '',
        $width = '100%',
        $height = '400px',
        $supplemental = '')
    {
        /** @var Oledrion\Helper $helper */
        $helper                   = Oledrion\Helper::getInstance();
        $editor                   = false;
        $editor_configs           = [];
        $editor_configs['name']   = $name;
        $editor_configs['value']  = $value;
        $editor_configs['rows']   = 35;
        $editor_configs['cols']   = 60;
        $editor_configs['width']  = '100%';
        $editor_configs['height'] = '400px';

        $editor_option = mb_strtolower(static::getModuleOption('editorAdmin'));
        //        $editor = new \XoopsFormEditor($caption, $editor_option, $editor_configs);
        //        public function __construct($caption, $name, $configs = null, $nohtml = false, $OnFailure = '')

        if ($helper->isUserAdmin()) {
            $editor = new \XoopsFormEditor($caption, $helper->getConfig('editorAdmin'), $editor_configs, $nohtml = false, $onfailure = 'textarea');
        } else {
            $editor = new \XoopsFormEditor($caption, $helper->getConfig('editorUser'), $editor_configs, $nohtml = false, $onfailure = 'textarea');
        }

        return $editor;
    }

    /**
     * Create (in a link) a javascript confirmation's box
     *
     * @param  string $message Message to display
     * @param  bool   $form    Is this a confirmation for a form ?
     * @return string  the javascript code to insert in the link (or in the form)
     */
    public static function javascriptLinkConfirm($message, $form = false)
    {
        if (!$form) {
            return "onclick=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
        }

        return "onSubmit=\"javascript:return confirm('" . str_replace("'", ' ', $message) . "')\"";
    }

    /**
     * Get current user IP
     *
     * @return string IP address (format Ipv4)
     */
    public static function IP()
    {
        $proxy_ip = '';
        if (\Xmf\Request::hasVar('HTTP_X_FORWARDED_FOR', 'SERVER')) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_VIA'])) {
            $proxy_ip = $_SERVER['HTTP_VIA'];
        } elseif (!empty($_SERVER['HTTP_X_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
        } elseif (!empty($_SERVER['HTTP_COMING_FROM'])) {
            $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
        }
        $regs = [];
        //if (!empty($proxy_ip) && $is_ip = ereg('^([0-9]{1,3}\.){3,3}[0-9]{1,3}', $proxy_ip, $regs) && count($regs) > 0) {
        if (!empty($proxy_ip) && filter_var($proxy_ip, FILTER_VALIDATE_IP) && count($regs) > 0) {
            $the_IP = $regs[0];
        } else {
            $the_IP = $_SERVER['REMOTE_ADDR'];
        }

        return $the_IP;
    }

    /**
     * Set the page's title, meta description and meta keywords
     * Datas are supposed to be sanitized
     *
     * @param  string $pageTitle       Page's Title
     * @param  string $metaDescription Page's meta description
     * @param  string $metaKeywords    Page's meta keywords
     */
    public static function setMetas($pageTitle = '', $metaDescription = '', $metaKeywords = '')
    {
        global $xoTheme, $xoTheme, $xoopsTpl;
        $xoopsTpl->assign('xoops_pagetitle', $pageTitle);
        if (null !== $xoTheme && is_object($xoTheme)) {
            if (!empty($metaKeywords)) {
                $xoTheme->addMeta('meta', 'keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoTheme->addMeta('meta', 'description', $metaDescription);
            }
        } elseif (null !== $xoopsTpl && is_object($xoopsTpl)) {
            // Compatibility for old Xoops versions
            if (!empty($metaKeywords)) {
                $xoopsTpl->assign('xoops_meta_keywords', $metaKeywords);
            }
            if (!empty($metaDescription)) {
                $xoopsTpl->assign('xoops_meta_description', $metaDescription);
            }
        }
    }

    /**
     * Send an email from a template to a list of recipients
     *
     * @param         $tplName
     * @param  array  $recipients List of recipients
     * @param  string $subject    Email's subject
     * @param  array  $variables  Varirables to give to the template
     * @return bool   Result of the send
     * @internal param string $tpl_name Template's name
     */
    public static function sendEmailFromTpl($tplName, $recipients, $subject, $variables)
    {
        global $xoopsConfig;
        require_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';
        if (!is_array($recipients)) {
            if ('' === trim($recipients)) {
                return false;
            }
        } else {
            if (0 === count($recipients)) {
                return false;
            }
        }
        if (function_exists('xoops_getMailer')) {
            $xoopsMailer = xoops_getMailer();
        } else {
            $xoopsMailer = xoops_getMailer();
        }

        $xoopsMailer->useMail();
        $templateDir = XOOPS_ROOT_PATH . '/modules/' . static::MODULE_NAME . '/language/' . $xoopsConfig['language'] . '/mail_template';
        if (!is_dir($templateDir)) {
            $templateDir = XOOPS_ROOT_PATH . '/modules/' . static::MODULE_NAME . '/language/english/mail_template';
        }
        $xoopsMailer->setTemplateDir($templateDir);
        $xoopsMailer->setTemplate($tplName);
        $xoopsMailer->setToEmails($recipients);
        // TODO: Change !
        // $xoopsMailer->setFromEmail('contact@monsite.com');
        //$xoopsMailer->setFromName('MonSite');
        $xoopsMailer->setSubject($subject);
        foreach ($variables as $key => $value) {
            $xoopsMailer->assign($key, $value);
        }
        $res = $xoopsMailer->send();
        unset($xoopsMailer);
        // B.R. $filename = XOOPS_UPLOAD_PATH . '/logmail_' . static::MODULE_NAME . '.php';
        $filename = OLEDRION_UPLOAD_PATH . '/logmail_' . static::MODULE_NAME . '.php';
        if (!file_exists($filename)) {
            $fp = @fopen($filename, 'ab');
            if ($fp) {
                fwrite($fp, "<?php exit(); ?>\n");
                fclose($fp);
            }
        }
        $fp = @fopen($filename, 'ab');

        if ($fp) {
            fwrite($fp, str_repeat('-', 120) . "\n");
            fwrite($fp, date('d/m/Y H:i:s') . "\n");
            fwrite($fp, 'Template name : ' . $tplName . "\n");
            fwrite($fp, 'Email subject : ' . $subject . "\n");
            if (is_array($recipients)) {
                fwrite($fp, 'Recipient(s) : ' . implode(',', $recipients) . "\n");
            } else {
                fwrite($fp, 'Recipient(s) : ' . $recipients . "\n");
            }
            fwrite($fp, 'Transmited variables : ' . implode(',', $variables) . "\n");
            fclose($fp);
        }

        return $res;
    }

    /**
     * Remove module's cache
     */
    public static function updateCache()
    {
        global $xoopsModule;
        $folder  = $xoopsModule->getVar('dirname');
        $tpllist = [];
        require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        /** @var \XoopsTplfileHandler $tplfileHandler */
        $tplfileHandler = xoops_getHandler('tplfile');
        $tpllist        = $tplfileHandler->find(null, null, null, $folder);
        xoops_template_clear_module_cache($xoopsModule->getVar('mid')); // Clear module's blocks cache

        /** @var \XoopsTplfile $onetemplate */
        foreach ($tpllist as $onetemplate) {
            // Remove cache for each page.
            if ('module' === $onetemplate->getVar('tpl_type')) {
                //  Note, I've been testing all the other methods (like the one of Smarty) and none of them run, that's why I have used this code
                $files_del = [];
                $files_del = glob(XOOPS_CACHE_PATH . '/*' . $onetemplate->getVar('tpl_file') . '*');
                if ($files_del && is_array($files_del)) {
                    foreach ($files_del as $one_file) {
                        if (is_file($one_file)) {
                            if (false === @unlink($one_file)) {
                                throw new \RuntimeException('The file ' . $one_file . ' could not be deleted.');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Redirect user with a message
     *
     * @param string $message message to display
     * @param string $url     The place where to go
     * @param mixed  $time
     */
    public static function redirect($message = '', $url = 'index.php', $time = 2)
    {
        redirect_header($url, $time, $message);
    }

    /**
     * Internal function used to get the handler of the current module
     *
     * @return \XoopsModule The module
     */
    protected static function _getModule()
    {
        static $mymodule;
        if (null === $mymodule) {
            global $xoopsModule;
            if (null !== $xoopsModule && is_object($xoopsModule) && OLEDRION_DIRNAME == $xoopsModule->getVar('dirname')) {
                $mymodule = $xoopsModule;
            } else {
                /** @var \XoopsModuleHandler $moduleHandler */
                $moduleHandler = xoops_getHandler('module');
                $mymodule      = $moduleHandler->getByDirname(OLEDRION_DIRNAME);
            }
        }

        return $mymodule;
    }

    /**
     * Returns the module's name (as defined by the user in the module manager) with cache
     * @return string Module's name
     */
    public static function getModuleName()
    {
        static $moduleName;
        if (null === $moduleName) {
            $mymodule   = static::_getModule();
            $moduleName = $mymodule->getVar('name');
        }

        return $moduleName;
    }

    /**
     * Create a title for the href tags inside html links
     *
     * @param  string $title Text to use
     * @return string Formated text
     */
    public static function makeHrefTitle($title)
    {
        $s = "\"'";
        $r = '  ';

        return strtr($title, $s, $r);
    }

    /**
     * Retourne la liste des utilisateurs appartenants à un groupe
     *
     * @param  int $groupId Searched group
     * @return array Array of XoopsUsers
     */
    public static function getUsersFromGroup($groupId)
    {
        $users = [];
        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        $users         = $memberHandler->getUsersByGroup($groupId, true);

        return $users;
    }

    /**
     * Retourne la liste des emails des utilisateurs membres d'un groupe
     *
     * @param $groupId
     * @return array Emails list
     * @internal param int $group_id Group's number
     */
    public static function getEmailsFromGroup($groupId)
    {
        $ret   = [];
        $users = static::getUsersFromGroup($groupId);
        foreach ($users as $user) {
            $ret[] = $user->getVar('email');
        }

        return $ret;
    }

    /**
     * Vérifie que l'utilisateur courant fait partie du groupe des administrateurs
     *
     * @return bool Admin or not
     */
    public static function isAdmin()
    {
        global $xoopsUser, $xoopsModule;
        if (is_object($xoopsUser)) {
            if (in_array(XOOPS_GROUP_ADMIN, $xoopsUser->getGroups())) {
                return true;
            }

            if (null !== $xoopsModule && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the current date in the Mysql format
     *
     * @return string Date in the Mysql format
     */
    public static function getCurrentSQLDate()
    {
        return date('Y-m-d'); // 2007-05-02
    }

    /**
     * @return bool|string
     */
    public static function getCurrentSQLDateTime()
    {
        return date('Y-m-d H:i:s'); // 2007-05-02
    }

    /**
     * Convert a Mysql date to the human's format
     *
     * @param  string $date The date to convert
     * @param  string $format
     * @return string The date in a human form
     */
    public static function SQLDateToHuman($date, $format = 'l')
    {
        if ('0000-00-00' !== $date && '' !== xoops_trim($date)) {
            return formatTimestamp(strtotime($date), $format);
        }

        return '';
    }

    /**
     * Convert a timestamp to a Mysql date
     *
     * @param int $timestamp The timestamp to use
     * @return string  The date in the Mysql format
     */
    public static function timestampToMysqlDate($timestamp)
    {
        return date('Y-m-d', (int)$timestamp);
    }

    /**
     * Conversion d'un dateTime Mysql en date lisible en français
     * @param $dateTime
     * @return bool|string
     */
    public static function sqlDateTimeToFrench($dateTime)
    {
        return date('d/m/Y H:i:s', strtotime($dateTime));
    }

    /**
     * Convert a timestamp to a Mysql datetime form
     * @param int $timestamp The timestamp to use
     * @return string  The date and time in the Mysql format
     */
    public static function timestampToMysqlDateTime($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * This function indicates if the current Xoops version needs to add asterisks to required fields in forms
     *
     * @return bool Yes = we need to add them, false = no
     */
    public static function needsAsterisk()
    {
        if (static::isX23()) {
            return false;
        }
        if (false !== mb_stripos(XOOPS_VERSION, 'impresscms')) {
            return false;
        }
        if (false === mb_stripos(XOOPS_VERSION, 'legacy')) {
            $xv = xoops_trim(str_replace('XOOPS ', '', XOOPS_VERSION));
            if ((int)mb_substr($xv, 4, 2) >= 17) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark the mandatory fields of a form with a star
     *
     * @param  \XoopsForm $sform The form to modify
     * @return \XoopsForm The modified form
     * @internal param string $character The character to use to mark fields
     */
    public static function &formMarkRequiredFields(&$sform)
    {
        if (static::needsAsterisk()) {
            $required = [];
            foreach ($sform->getRequired() as $item) {
                $required[] = $item->_name;
            }
            $elements = [];
            $elements = $sform->getElements();
            $cnt      = count($elements);
            foreach ($elements as $i => $iValue) {
                if (is_object($elements[$i]) && in_array($iValue->_name, $required)) {
                    $iValue->_caption .= ' *';
                }
            }
        }

        return $sform;
    }

    /**
     * Create an html heading (from h1 to h6)
     *
     * @param  string $title The text to use
     * @param int     $level Level to return
     * @return string  The heading
     */
    public static function htitle($title = '', $level = 1)
    {
        printf('<h%01d>%s</h%01d>', $level, $title, $level);
    }

    /**
     * Create a unique upload filename
     *
     * @param  string $folder   The folder where the file will be saved
     * @param  string $fileName Original filename (coming from the user)
     * @param  bool   $trimName Do we need to create a "short" unique name ?
     * @return string  The unique filename to use (with its extension)
     */
    public static function createUploadName($folder, $fileName, $trimName = false)
    {
        $uid           = '';
        $workingfolder = $folder;
        if ('/' !== xoops_substr($workingfolder, mb_strlen($workingfolder) - 1, 1)) {
            $workingfolder .= '/';
        }
        $ext  = basename($fileName);
        $ext  = explode('.', $ext);
        $ext  = '.' . $ext[count($ext) - 1];
        $true = true;
        while ($true) {
            $ipbits = explode('.', $_SERVER['REMOTE_ADDR']);
            list($usec, $sec) = explode(' ', microtime());
            $usec *= 65536;
            $sec  = ((int)$sec) & 0xFFFF;

            if ($trimName) {
                $uid = sprintf('%06x%04x%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            } else {
                $uid = sprintf('%08x-%04x-%04x', ($ipbits[0] << 24) | ($ipbits[1] << 16) | ($ipbits[2] << 8) | $ipbits[3], $sec, $usec);
            }
            if (!file_exists($workingfolder . $uid . $ext)) {
                $true = false;
            }
        }

        return $uid . $ext;
    }

    /**
     * Replace html entities with their ASCII equivalent
     *
     * @param  string $chaine The string undecode
     * @return string The undecoded string
     */
    public static function unhtml($chaine)
    {
        $search = $replace = [];
        $chaine = html_entity_decode($chaine);

        for ($i = 0; $i <= 255; ++$i) {
            $search[]  = '&#' . $i . ';';
            $replace[] = chr($i);
        }
        $replace[] = '...';
        $search[]  = '';
        $replace[] = "'";
        $search[]  = '';
        $replace[] = "'";
        $search[]  = '';
        $replace[] = '-';
        $search[]  = '&bull;'; // $replace[] = '';
        $replace[] = '';
        $search[]  = '&mdash;';
        $replace[] = '-';
        $search[]  = '&ndash;';
        $replace[] = '-';
        $search[]  = '&shy;';
        $replace[] = '"';
        $search[]  = '&quot;';
        $replace[] = '&';
        $search[]  = '&amp;';
        $replace[] = '';
        $search[]  = '&circ;';
        $replace[] = '¡';
        $search[]  = '&iexcl;';
        $replace[] = '¦';
        $search[]  = '&brvbar;';
        $replace[] = '¨';
        $search[]  = '&uml;';
        $replace[] = '¯';
        $search[]  = '&macr;';
        $replace[] = '´';
        $search[]  = '&acute;';
        $replace[] = '¸';
        $search[]  = '&cedil;';
        $replace[] = '¿';
        $search[]  = '&iquest;';
        $replace[] = '';
        $search[]  = '&tilde;';
        $replace[] = "'";
        $search[]  = '&lsquo;'; // $replace[]='';
        $replace[] = "'";
        $search[]  = '&rsquo;'; // $replace[]='';
        $replace[] = '';
        $search[]  = '&sbquo;';
        $replace[] = "'";
        $search[]  = '&ldquo;'; // $replace[]='';
        $replace[] = "'";
        $search[]  = '&rdquo;'; // $replace[]='';
        $replace[] = '';
        $search[]  = '&bdquo;';
        $replace[] = '';
        $search[]  = '&lsaquo;';
        $replace[] = '';
        $search[]  = '&rsaquo;';
        $replace[] = '<';
        $search[]  = '&lt;';
        $replace[] = '>';
        $search[]  = '&gt;';
        $replace[] = '±';
        $search[]  = '&plusmn;';
        $replace[] = '«';
        $search[]  = '&laquo;';
        $replace[] = '»';
        $search[]  = '&raquo;';
        $replace[] = '×';
        $search[]  = '&times;';
        $replace[] = '÷';
        $search[]  = '&divide;';
        $replace[] = '¢';
        $search[]  = '&cent;';
        $replace[] = '£';
        $search[]  = '&pound;';
        $replace[] = '¤';
        $search[]  = '&curren;';
        $replace[] = '¥';
        $search[]  = '&yen;';
        $replace[] = '§';
        $search[]  = '&sect;';
        $replace[] = '©';
        $search[]  = '&copy;';
        $replace[] = '¬';
        $search[]  = '&not;';
        $replace[] = '®';
        $search[]  = '&reg;';
        $replace[] = '°';
        $search[]  = '&deg;';
        $replace[] = 'µ';
        $search[]  = '&micro;';
        $replace[] = '¶';
        $search[]  = '&para;';
        $replace[] = '·';
        $search[]  = '&middot;';
        $replace[] = '';
        $search[]  = '&dagger;';
        $replace[] = '';
        $search[]  = '&Dagger;';
        $replace[] = '';
        $search[]  = '&permil;';
        $replace[] = 'Euro';
        $search[]  = '&euro;'; // $replace[]=''
        $replace[] = '¼';
        $search[]  = '&frac14;';
        $replace[] = '½';
        $search[]  = '&frac12;';
        $replace[] = '¾';
        $search[]  = '&frac34;';
        $replace[] = '¹';
        $search[]  = '&sup1;';
        $replace[] = '²';
        $search[]  = '&sup2;';
        $replace[] = '³';
        $search[]  = '&sup3;';
        $replace[] = 'á';
        $search[]  = '&aacute;';
        $replace[] = 'Á';
        $search[]  = '&Aacute;';
        $replace[] = 'â';
        $search[]  = '&acirc;';
        $replace[] = 'Â';
        $search[]  = '&Acirc;';
        $replace[] = 'à';
        $search[]  = '&agrave;';
        $replace[] = 'À';
        $search[]  = '&Agrave;';
        $replace[] = 'å';
        $search[]  = '&aring;';
        $replace[] = 'Å';
        $search[]  = '&Aring;';
        $replace[] = 'ã';
        $search[]  = '&atilde;';
        $replace[] = 'Ã';
        $search[]  = '&Atilde;';
        $replace[] = 'ä';
        $search[]  = '&auml;';
        $replace[] = 'Ä';
        $search[]  = '&Auml;';
        $replace[] = 'ª';
        $search[]  = '&ordf;';
        $replace[] = 'æ';
        $search[]  = '&aelig;';
        $replace[] = 'Æ';
        $search[]  = '&AElig;';
        $replace[] = 'ç';
        $search[]  = '&ccedil;';
        $replace[] = 'Ç';
        $search[]  = '&Ccedil;';
        $replace[] = 'ð';
        $search[]  = '&eth;';
        $replace[] = 'Ð';
        $search[]  = '&ETH;';
        $replace[] = 'é';
        $search[]  = '&eacute;';
        $replace[] = 'É';
        $search[]  = '&Eacute;';
        $replace[] = 'ê';
        $search[]  = '&ecirc;';
        $replace[] = 'Ê';
        $search[]  = '&Ecirc;';
        $replace[] = 'è';
        $search[]  = '&egrave;';
        $replace[] = 'È';
        $search[]  = '&Egrave;';
        $replace[] = 'ë';
        $search[]  = '&euml;';
        $replace[] = 'Ë';
        $search[]  = '&Euml;';
        $replace[] = '';
        $search[]  = '&fnof;';
        $replace[] = 'í';
        $search[]  = '&iacute;';
        $replace[] = 'Í';
        $search[]  = '&Iacute;';
        $replace[] = 'î';
        $search[]  = '&icirc;';
        $replace[] = 'Î';
        $search[]  = '&Icirc;';
        $replace[] = 'ì';
        $search[]  = '&igrave;';
        $replace[] = 'Ì';
        $search[]  = '&Igrave;';
        $replace[] = 'ï';
        $search[]  = '&iuml;';
        $replace[] = 'Ï';
        $search[]  = '&Iuml;';
        $replace[] = 'ñ';
        $search[]  = '&ntilde;';
        $replace[] = 'Ñ';
        $search[]  = '&Ntilde;';
        $replace[] = 'ó';
        $search[]  = '&oacute;';
        $replace[] = 'Ó';
        $search[]  = '&Oacute;';
        $replace[] = 'ô';
        $search[]  = '&ocirc;';
        $replace[] = 'Ô';
        $search[]  = '&Ocirc;';
        $replace[] = 'ò';
        $search[]  = '&ograve;';
        $replace[] = 'Ò';
        $search[]  = '&Ograve;';
        $replace[] = 'º';
        $search[]  = '&ordm;';
        $replace[] = 'ø';
        $search[]  = '&oslash;';
        $replace[] = 'Ø';
        $search[]  = '&Oslash;';
        $replace[] = 'õ';
        $search[]  = '&otilde;';
        $replace[] = 'Õ';
        $search[]  = '&Otilde;';
        $replace[] = 'ö';
        $search[]  = '&ouml;';
        $replace[] = 'Ö';
        $search[]  = '&Ouml;';
        $replace[] = '';
        $search[]  = '&oelig;';
        $replace[] = '';
        $search[]  = '&OElig;';
        $replace[] = '';
        $search[]  = '&scaron;';
        $replace[] = '';
        $search[]  = '&Scaron;';
        $replace[] = 'ß';
        $search[]  = '&szlig;';
        $replace[] = 'þ';
        $search[]  = '&thorn;';
        $replace[] = 'Þ';
        $search[]  = '&THORN;';
        $replace[] = 'ú';
        $search[]  = '&uacute;';
        $replace[] = 'Ú';
        $search[]  = '&Uacute;';
        $replace[] = 'û';
        $search[]  = '&ucirc;';
        $replace[] = 'Û';
        $search[]  = '&Ucirc;';
        $replace[] = 'ù';
        $search[]  = '&ugrave;';
        $replace[] = 'Ù';
        $search[]  = '&Ugrave;';
        $replace[] = 'ü';
        $search[]  = '&uuml;';
        $replace[] = 'Ü';
        $search[]  = '&Uuml;';
        $replace[] = 'ý';
        $search[]  = '&yacute;';
        $replace[] = 'Ý';
        $search[]  = '&Yacute;';
        $replace[] = 'ÿ';
        $search[]  = '&yuml;';
        $replace[] = '';
        $search[]  = '&Yuml;';
        $chaine    = str_replace($search, $replace, $chaine);

        return $chaine;
    }

    /**
     * Création d'une titre pour être utilisé par l'url rewriting
     *
     * @param  string $content  Le texte à utiliser pour créer l'url
     * @param int     $urw      La limite basse pour créer les mots
     * @return string  Le texte à utiliser pour l'url
     *                          Note, some parts are from Solo's code
     */
    public static function makeSeoUrl($content, $urw = 1)
    {
        $s       = "ÀÁÂÃÄÅÒÓÔÕÖØÈÉÊËÇÌÍÎÏÙÚÛÜÑàáâãäåòóôõöøèéêëçìíîïùúûüÿñ '()";
        $r       = 'AAAAAAOOOOOOEEEECIIIIUUUUYNaaaaaaooooooeeeeciiiiuuuuyn----';
        $content = static::unhtml($content); // First, remove html entities
        $content = strtr($content, $s, $r);
        $content = strip_tags($content);
        $content = mb_strtolower($content);
        $content = htmlentities($content, ENT_QUOTES | ENT_HTML5); // TODO: Vérifier
        $content = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde);/', '$1', $content);
        $content = html_entity_decode($content);
        $content = str_ireplace('quot', ' ', $content);
        $content = preg_replace("/'/i", ' ', $content);
        $content = preg_replace('/-/i', ' ', $content);
        $content = preg_replace('/[[:punct:]]/i', '', $content);

        // Selon option mais attention au fichier .htaccess !
        // $content = eregi_replace('[[:digit:]]','', $content);
        $content = preg_replace('/[^a-z|A-Z|0-9]/', '-', $content);

        $words    = explode(' ', $content);
        $keywords = '';
        foreach ($words as $word) {
            if (mb_strlen($word) >= $urw) {
                $keywords .= '-' . trim($word);
            }
        }
        if (!$keywords) {
            $keywords = '-';
        }
        // Supprime les tirets en double
        $keywords = str_replace('---', '-', $keywords);
        $keywords = str_replace('--', '-', $keywords);
        // Supprime un éventuel tiret à la fin de la chaine
        if ('-' == mb_substr($keywords, mb_strlen($keywords) - 1, 1)) {
            $keywords = mb_substr($keywords, 0, -1);
        }

        return $keywords;
    }

    /**
     * Create the meta keywords based on the content
     *
     * @param  string $content Content from which we have to create metakeywords
     * @return string The list of meta keywords
     */
    public static function createMetaKeywords($content)
    {
        $keywordscount = static::getModuleOption('metagen_maxwords');
        $keywordsorder = static::getModuleOption('metagen_order');

        $tmp = [];
        // Search for the "Minimum keyword length"
        if (\Xmf\Request::hasVar('oledrion_keywords_limit', 'SESSION')) {
            $limit = $_SESSION['oledrion_keywords_limit'];
        } else {
            $configHandler                       = xoops_getHandler('config');
            $xoopsConfigSearch                   = $configHandler->getConfigsByCat(XOOPS_CONF_SEARCH);
            $limit                               = $xoopsConfigSearch['keyword_min'];
            $_SESSION['oledrion_keywords_limit'] = $limit;
        }
        $myts            = \MyTextSanitizer::getInstance();
        $content         = str_replace('<br>', ' ', $content);
        $content         = $myts->undoHtmlSpecialChars($content);
        $content         = strip_tags($content);
        $content         = mb_strtolower($content);
        $search_pattern  = [
            '&nbsp;',
            "\t",
            "\r\n",
            "\r",
            "\n",
            ',',
            '.',
            "'",
            ';',
            ':',
            ')',
            '(',
            '"',
            '?',
            '!',
            '{',
            '}',
            '[',
            ']',
            '<',
            '>',
            '/',
            '+',
            '-',
            '_',
            '\\',
            '*',
        ];
        $replace_pattern = [
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
        $content         = str_replace($search_pattern, $replace_pattern, $content);
        $keywords        = explode(' ', $content);
        switch ($keywordsorder) {
            case 0: // Ordre d'apparition dans le texte

                $keywords = array_unique($keywords);

                break;
            case 1: // Ordre de fréquence des mots

                $keywords = array_count_values($keywords);
                asort($keywords);
                $keywords = array_keys($keywords);

                break;
            case 2: // Ordre inverse de la fréquence des mots

                $keywords = array_count_values($keywords);
                arsort($keywords);
                $keywords = array_keys($keywords);

                break;
        }
        // Remove black listed words
        if ('' !== xoops_trim(static::getModuleOption('metagen_blacklist'))) {
            $metagen_blacklist = str_replace("\r", '', static::getModuleOption('metagen_blacklist'));
            $metablack         = explode("\n", $metagen_blacklist);
            array_walk($metablack, 'trim');
            $keywords = array_diff($keywords, $metablack);
        }

        foreach ($keywords as $keyword) {
            if (!is_numeric($keyword) && mb_strlen($keyword) >= $limit) {
                $tmp[] = $keyword;
            }
        }
        $tmp = array_slice($tmp, 0, $keywordscount);
        if (count($tmp) > 0) {
            return implode(',', $tmp);
        }

        if (null === $configHandler || !is_object($configHandler)) {
            $configHandler = xoops_getHandler('config');
        }
        $xoopsConfigMetaFooter = $configHandler->getConfigsByCat(XOOPS_CONF_METAFOOTER);
        if (isset($xoopsConfigMetaFooter['meta_keywords'])) {
            return $xoopsConfigMetaFooter['meta_keywords'];
        }

        return '';
    }

    /**
     * Fonction chargée de gérer l'upload
     *
     * @param int       $indice L'indice du fichier à télécharger
     * @param  string   $dstpath
     * @param  null     $mimeTypes
     * @param  null|int $uploadMaxSize
     * @param  null|int $maxWidth
     * @param  null|int $maxHeight
     * @return mixed   True si l'upload s'est bien déroulé sinon le message d'erreur correspondant
     */
    public static function uploadFile(
        $indice,
        $dstpath = XOOPS_UPLOAD_PATH,
        $mimeTypes = null,
        $uploadMaxSize = null,
        $maxWidth = null,
        $maxHeight = null)
    {
        //        require_once XOOPS_ROOT_PATH . '/class/uploader.php';
        global $destname;
        if (\Xmf\Request::hasVar('xoops_upload_file', 'POST')) {
            require_once XOOPS_ROOT_PATH . '/class/uploader.php';
            $fldname = '';
            $fldname = $_FILES[$_POST['xoops_upload_file'][$indice]];
            $fldname = get_magic_quotes_gpc() ? stripslashes($fldname['name']) : $fldname['name'];
            if (xoops_trim('' !== $fldname)) {
                $destname = static::createUploadName($dstpath, $fldname, true);
                if (null === $mimeTypes) {
                    $permittedtypes = explode("\n", str_replace("\r", '', static::getModuleOption('mimetypes')));
                    array_walk($permittedtypes, 'trim');
                } else {
                    $permittedtypes = $mimeTypes;
                }
                $uploadSize = $uploadMaxSize;
                if (null === $uploadMaxSize) {
                    $uploadSize = static::getModuleOption('maxuploadsize');
                }
                $uploader = new \XoopsMediaUploader($dstpath, $permittedtypes, $uploadSize, $maxWidth, $maxHeight);
                //$uploader->allowUnknownTypes = true;
                $uploader->setTargetFileName($destname);
                if ($uploader->fetchMedia($_POST['xoops_upload_file'][$indice])) {
                    if ($uploader->upload()) {
                        return true;
                    }

                    return _ERRORS . ' ' . htmlentities($uploader->getErrors(), ENT_QUOTES | ENT_HTML5);
                }

                return htmlentities($uploader->getErrors(), ENT_QUOTES | ENT_HTML5);
            }

            return false;
        }

        return false;
    }

    /**
     * Resize a Picture to some given dimensions (using the wideImage library)
     *
     * @param  string $src_path      Picture's source
     * @param  string $dst_path      Picture's destination
     * @param int     $param_width   Maximum picture's width
     * @param int     $param_height  Maximum picture's height
     * @param  bool   $keep_original Do we have to keep the original picture ?
     * @param  string $fit           Resize mode (see the wideImage library for more information)
     * @return bool
     */
    public static function resizePicture(
        $src_path,
        $dst_path,
        $param_width,
        $param_height,
        $keep_original = false,
        $fit = 'inside')
    {
        //        require_once OLEDRION_PATH . 'class/wideimage/WideImage.inc.php';
        $resize = true;
        if (OLEDRION_DONT_RESIZE_IF_SMALLER) {
            if (false === @getimagesize($src_path)) {
                $message = 'The picture ' . $src_path . ' could not be found and resized.';
                //                throw new \RuntimeException($message);
                self::redirect($message);

                return false;
            }
            $pictureDimensions = getimagesize($src_path);
            if (is_array($pictureDimensions)) {
                $width  = $pictureDimensions[0];
                $height = $pictureDimensions[1];
                if ($width < $param_width && $height < $param_height) {
                    $resize = false;
                }
            }
        }

        $img = WideImage::load($src_path);
        if ($resize) {
            $result = $img->resize($param_width, $param_height, $fit);
            $result->saveToFile($dst_path);
        } else {
            if (false === @copy($src_path, $dst_path)) {
                throw new \RuntimeException('The file ' . $src_path . ' could not be copied.');
            }
        }

        if (!$keep_original) {
            if (false === @unlink($src_path)) {
                throw new \RuntimeException('The file ' . $src_path . ' could not be deleted.');
            }
        }

        return true;
    }

    /**
     * Triggering a Xoops alert after an event
     *
     * @param int   $category The category ID of the event
     * @param int   $itemId   The ID of the element (too general to be precisely described)
     * @param mixed $event    The event that is triggered
     * @param mixed $tags     Variables to pass to the template
     */
    public static function notify($category, $itemId, $event, $tags)
    {
        /** @var \XoopsNotificationHandler $notificationHandler */
        $notificationHandler  = xoops_getHandler('notification');
        $tags['X_MODULE_URL'] = OLEDRION_URL;
        $notificationHandler->triggerEvent($category, $itemId, $event, $tags);
    }

    /**
     * Ajoute des jours à une date et retourne la nouvelle date au format Date de Mysql
     *
     * @param  int $duration
     * @param int  $startingDate Date de départ (timestamp)
     * @return bool|string
     * @internal param int $durations Durée en jours
     */
    public static function addDaysToDate($duration = 1, $startingDate = 0)
    {
        if (0 == $startingDate) {
            $startingDate = time();
        }
        $endingDate = $startingDate + ($duration * 86400);

        return date('Y-m-d', $endingDate);
    }

    /**
     * Returns a breadcrumb based on the parameters passed and starting (automatically) from the root of the module     *
     * @param array  $path  The complete path (except root) of the breadcrumb as key = url value = title
     * @param string $raquo The default separator to use
     * @return string the breadcrumb
     */
    public static function breadcrumb($path, $raquo = ' &raquo; ')
    {
        $breadcrumb        = '';
        $workingBreadcrumb = [];
        if (is_array($path)) {
            $moduleName          = static::getModuleName();
            $workingBreadcrumb[] = "<a href='" . OLEDRION_URL . "' title='" . static::makeHrefTitle($moduleName) . "'>" . $moduleName . '</a>';
            foreach ($path as $url => $title) {
                $workingBreadcrumb[] = "<a href='" . $url . "'>" . $title . '</a>';
            }
            $cnt = count($workingBreadcrumb);
            foreach ($workingBreadcrumb as $i => $iValue) {
                if ($i == $cnt - 1) {
                    $workingBreadcrumb[$i] = strip_tags($workingBreadcrumb[$i]);
                }
            }
            $breadcrumb = implode($raquo, $workingBreadcrumb);
        }

        return $breadcrumb;
    }

    /**
     * @param $string
     * @return string
     */
    public static function close_tags($string)
    {
        // match opened tags
        if (preg_match_all('/<([a-z\:\-]+)[^\/]>/', $string, $start_tags)) {
            $start_tags = $start_tags[1];

            // match closed tags
            if (preg_match_all('/<\/([a-z]+)>/', $string, $end_tags)) {
                $complete_tags = [];
                $end_tags      = $end_tags[1];

                foreach ($start_tags as $key => $val) {
                    $posb = array_search($val, $end_tags, true);
                    if (is_int($posb)) {
                        unset($end_tags[$posb]);
                    } else {
                        $complete_tags[] = $val;
                    }
                }
            } else {
                $complete_tags = $start_tags;
            }

            $complete_tags = array_reverse($complete_tags);
            for ($i = 0, $iMax = count($complete_tags); $i < $iMax; ++$i) {
                $string .= '</' . $complete_tags[$i] . '>';
            }
        }

        return $string;
    }

    /**
     * @param               $string
     * @param  int          $length
     * @param  string       $etc
     * @param  bool         $break_words
     * @return mixed|string
     */
    public static function truncate_tagsafe($string, $length = 80, $etc = '...', $break_words = false)
    {
        if (0 == $length) {
            return '';
        }

        if (mb_strlen($string) > $length) {
            $length -= mb_strlen($etc);
            if (!$break_words) {
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1));
                $string = preg_replace('/<[^>]*$/', '', $string);
                $string = static::close_tags($string);
            }

            return $string . $etc;
        }

        return $string;
    }

    /**
     * Create an infotip
     * @param $text
     * @return string
     */
    public static function makeInfotips($text)
    {
        $ret      = '';
        $infotips = static::getModuleOption('infotips');
        if ($infotips > 0) {
            $myts = \MyTextSanitizer::getInstance();
            $ret  = $myts->htmlSpecialChars(xoops_substr(strip_tags($text), 0, $infotips));
        }

        return $ret;
    }

    /**
     * Mise en place de l'appel à la feuille de style du module dans le template
     * @param string $url
     */
    public static function setCSS($url = '')
    {
        global $xoopsTpl, $xoTheme;
        if ('' == $url) {
            $url = OLEDRION_URL . 'assets/css/oledrion.css';
        }

        if (!is_object($xoTheme)) {
            $xoopsTpl->assign('xoops_module_header', $xoopsTpl->get_template_vars('xoops_module_header') . "<link rel=\"stylesheet\" type=\"text/css\" href=\"$url\">");
        } else {
            $xoTheme->addStylesheet($url);
        }
    }

    /**
     * Mise en place de l'appel à la feuille de style du module dans le template
     * @param string $language
     */
    public static function setLocalCSS($language = 'english')
    {
        global $xoopsTpl, $xoTheme;

        $localcss = OLEDRION_URL . 'language/' . $language . '/style.css';

        if (!is_object($xoTheme)) {
            $xoopsTpl->assign('xoops_module_header', $xoopsTpl->get_template_vars('xoops_module_header') . "<link rel=\"stylesheet\" type=\"text/css\" href=\"$localcss\">");
        } else {
            $xoTheme->addStylesheet($localcss);
        }
    }

    /**
     * Calcul du TTC à partir du HT et de la TVA
     *
     * @param int     $ht     Montant HT
     * @param int     $vat    Taux de TVA
     * @param  bool   $edit   Si faux alors le montant est formaté pour affichage sinon il reste tel quel
     * @param  string $format Format d'affichage du résultat (long ou court)
     * @return mixed   Soit une chaine soit un flottant
     */
    public static function getTTC($ht = 0, $vat = 0, $edit = false, $format = 's')
    {
        $ht               = (int)$ht;
        $vat              = (int)$vat;
        $oledrionCurrency = Oledrion\Currency::getInstance();
        $ttc              = $ht * (1 + ($vat / 100));
        if (!$edit) {
            return $oledrionCurrency->amountForDisplay($ttc, $format);
        }

        return $ttc;
    }

    /**
     * Renvoie le montant de la tva à partir du montant HT
     * @param $ht
     * @param $vat
     * @return float
     */
    public static function getVAT($ht, $vat)
    {
        return (($ht * $vat) / 100);
    }

    /**
     * Retourne le montant TTC
     *
     * @param  float $product_price Le montant du produit
     * @param int    $vat_id        Le numéro de TVA
     * @return float Le montant TTC si on a trouvé sa TVA sinon
     */
    public static function getAmountWithVat($product_price, $vat_id)
    {
        $vat = null;
        static $vats = [];
        $vat_rate   = null;
        $vatHandler = new Oledrion\VatHandler(\XoopsDatabaseFactory::getDatabaseConnection());
        if (is_array($vats) && in_array($vat_id, $vats)) {
            $vat_rate = $vats[$vat_id];
        } else {
            //            $handlers = \HandlerManager::getInstance();
            require_once dirname(__DIR__) . '/include/common.php';
            $vat = $vatHandler->get($vat_id);
            if (is_object($vat)) {
                $vat_rate      = $vat->getVar('vat_rate', 'e');
                $vats[$vat_id] = $vat_rate;
            }
        }

        if (null !== $vat_rate) {
            return ((float)$product_price * (float)$vat_rate / 100) + (float)$product_price;
        }

        return $product_price;
    }

    /**
     * @param $datastream
     * @param $url
     * @return string
     */
    public static function postIt($datastream, $url)
    {
        $url     = preg_replace('@^http://@i', '', $url);
        $host    = mb_substr($url, 0, mb_strpos($url, '/'));
        $uri     = mb_strstr($url, '/');
        $reqbody = '';
        foreach ($datastream as $key => $val) {
            if (!empty($reqbody)) {
                $reqbody .= '&';
            }
            $reqbody .= $key . '=' . urlencode($val);
        }
        $contentlength = mb_strlen($reqbody);
        $reqheader     = "POST $uri HTTP/1.1\r\n" . "Host: $host\n" . "Content-Type: application/x-www-form-urlencoded\r\n" . "Content-Length: $contentlength\r\n\r\n" . "$reqbody\r\n";

        return $reqheader;
    }

    /**
     * Retourne le type Mime d'un fichier en utilisant d'abord finfo puis mime_content
     *
     * @param  string $filename Le fichier (avec son chemin d'accès complet) dont on veut connaître le type mime
     * @return string
     */
    public static function getMimeType($filename)
    {
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open();
            $mimetype = finfo_file($finfo, $filename, FILEINFO_MIME_TYPE);
            finfo_close($finfo);

            return $mimetype;
        }

        if (function_exists('mime_content_type')) {
            return mime_content_type($filename);
        }

        return '';
    }

    /**
     * Retourne un criteria compo qui permet de filtrer les produits sur le mois courant
     *
     * @return \CriteriaCompo
     */
    public static function getThisMonthCriteria()
    {
        $start             = mktime(0, 1, 0, date('n'), date('j'), date('Y'));
        $end               = mktime(0, 0, 0, date('n'), date('t'), date('Y'));
        $criteriaThisMonth = new \CriteriaCompo();
        $criteriaThisMonth->add(new \Criteria('product_submitted', $start, '>='));
        $criteriaThisMonth->add(new \Criteria('product_submitted', $end, '<='));

        return $criteriaThisMonth;
    }

    /**
     * Retourne une liste d'objets XoopsUsers à partir d'une liste d'identifiants
     *
     * @param  array $xoopsUsersIDs La liste des ID
     * @return array Les objets XoopsUsers
     */
    public static function getUsersFromIds($xoopsUsersIDs)
    {
        $users = [];
        if ($xoopsUsersIDs && is_array($xoopsUsersIDs)) {
            $xoopsUsersIDs = array_unique($xoopsUsersIDs);
            sort($xoopsUsersIDs);
            if (count($xoopsUsersIDs) > 0) {
                /** @var \XoopsUserHandler $userHandler */
                $userHandler = xoops_getHandler('user');
                $criteria    = new \Criteria('uid', '(' . implode(',', $xoopsUsersIDs) . ')', 'IN');
                $criteria->setSort('uid');
                $users = $userHandler->getObjects($criteria, true);
            }
        }

        return $users;
    }

    /**
     * Retourne l'ID de l'utilisateur courant (s'il est connecté)
     * @return int L'uid ou 0
     */
    public static function getCurrentUserID()
    {
        global $xoopsUser;
        $uid = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;

        return $uid;
    }

    /**
     * Retourne la liste des groupes de l'utilisateur courant (avec cache)
     * @param  int $uid
     * @return array Les ID des groupes auquel l'utilisateur courant appartient
     */
    public function getMemberGroups($uid = 0)
    {
        static $buffer = [];
        if (0 == $uid) {
            $uid = static::getCurrentUserID();
        }

        if (is_array($buffer) && count($buffer) > 0 && isset($buffer[$uid])) {
            return $buffer[$uid];
        }

        if ($uid > 0) {
            /** @var \XoopsMemberHandler $memberHandler */
            $memberHandler = xoops_getHandler('member');
            $buffer[$uid]  = $memberHandler->getGroupsByUser($uid, false); // Renvoie un tableau d'ID (de groupes)
        } else {
            $buffer[$uid] = [XOOPS_GROUP_ANONYMOUS];
        }

        return $buffer[$uid];
    }

    /**
     * Indique si l'utilisateur courant fait partie d'une groupe donné (avec gestion de cache)
     *
     * @param int  $group Groupe recherché
     * @param  int $uid
     * @return bool    vrai si l'utilisateur fait partie du groupe, faux sinon
     */
    public static function isMemberOfGroup($group = 0, $uid = 0)
    {
        static $buffer = [];
        $retval = false;
        if (0 == $uid) {
            $uid = static::getCurrentUserID();
        }
        if (is_array($buffer) && array_key_exists($group, $buffer)) {
            $retval = $buffer[$group];
        } else {
            /** @var \XoopsMemberHandler $memberHandler */
            $memberHandler  = xoops_getHandler('member');
            $groups         = $memberHandler->getGroupsByUser($uid, false); // Renvoie un tableau d'ID (de groupes)
            $retval         = in_array($group, $groups);
            $buffer[$group] = $retval;
        }

        return $retval;
    }

    /**
     * Fonction chargée de vérifier qu'un répertoire existe, qu'on peut écrire dedans et création d'un fichier index.html
     *
     * @param  string $folder Le chemin complet du répertoire à vérifier
     */
    public static function prepareFolder($folder)
    {
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0777) && !is_dir($folder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $folder));
            }
            file_put_contents($folder . '/index.html', '<script>history.go(-1);</script>');
        }
        chmod($folder, 0777);
    }

    /**
     * Duplicate a file in local
     *
     * @param  string $path     The file's path
     * @param  string $filename The filename
     * @return mixed  If the copy succeed, the new filename else false
     * @since 2.1
     */
    public static function duplicateFile($path, $filename)
    {
        $newName = static::createUploadName($path, $filename);
        if (copy($path . '/' . $filename, $path . '/' . $newName)) {
            return $newName;
        }

        return false;
    }

    /**
     * Load a language file
     *
     * @param string $languageFile     The required language file
     * @param string $defaultExtension Default extension to use
     * @since 2.2.2009.02.13
     */
    public static function loadLanguageFile($languageFile, $defaultExtension = '.php')
    {
        global $xoopsConfig;
        $root = OLEDRION_PATH;
        if (false === mb_strpos($languageFile, $defaultExtension)) {
            $languageFile .= $defaultExtension;
        }
        /** @var Oledrion\Helper $helper */
        $helper = Oledrion\Helper::getInstance();
        $helper->loadLanguage($languageFile);
    }

    /**
     * Formatage d'un floattant pour la base de données
     *
     * @param mixed $amount
     * @return string le montant formaté
     * @since 2.2.2009.02.25
     */
    public static function formatFloatForDB($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * Appelle un fichier Javascript à la manière de Xoops
     *
     * @note, l'url complète ne doit pas être fournie, la méthode se charge d'ajouter
     * le chemin vers le répertoire js en fonction de la requête, c'est à dire que si
     * on appelle un fichier de langue, la méthode ajoute l'url vers le répertoire de
     * langue, dans le cas contraire on ajoute l'url vers le répertoire JS du module.
     *
     * @param string $javascriptFile
     * @param bool   $inLanguageFolder
     * @param bool   $oldWay
     * @since 2.3.2009.03.14
     */
    public static function callJavascriptFile($javascriptFile, $inLanguageFolder = false, $oldWay = false)
    {
        global $xoopsConfig, $xoTheme;
        $fileToCall = $javascriptFile;
        if ($inLanguageFolder) {
            $root    = OLEDRION_PATH;
            $rootUrl = OLEDRION_URL;
            if (file_exists($root . 'language/' . $xoopsConfig['language'] . '/' . $javascriptFile)) {
                $fileToCall = $rootUrl . 'language/' . $xoopsConfig['language'] . '/' . $javascriptFile;
            } else {
                // Fallback
                $fileToCall = $rootUrl . 'language/english/' . $javascriptFile;
            }
        } else {
            $fileToCall = OLEDRION_JS_URL . $javascriptFile;
        }

        $xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
        $xoTheme->addScript($fileToCall);
    }

    /**
     * Create the <option> of an html select
     *
     * @param  array $array   Array of index and labels
     * @param  mixed $default the default value
     * @param  bool  $withNull
     * @return string
     * @since 2.3.2009.03.13
     */
    public static function htmlSelectOptions($array, $default = 0, $withNull = true)
    {
        $ret      = [];
        $selected = '';
        if ($withNull) {
            if (0 === $default) {
                $selected = " selected = 'selected'";
            }
            $ret[] = '<option value=0' . $selected . '>---</option>';
        }

        foreach ($array as $index => $label) {
            $selected = '';
            if ($index == $default) {
                $selected = " selected = 'selected'";
            }
            $ret[] = '<option value="' . $index . '"' . $selected . '>' . $label . '</option>';
        }

        return implode("\n", $ret);
    }

    /**
     * Creates an html select
     *
     * @param  string $selectName Selector's name
     * @param  array  $array      Options
     * @param  mixed  $default    Default's value
     * @param  bool   $withNull   Do we include a null option ?
     * @return string
     * @since 2.3.2009.03.13
     */
    public static function htmlSelect($selectName, $array, $default, $withNull = true)
    {
        $ret = '';
        $ret .= "<select name='" . $selectName . "' id='" . $selectName . "'>\n";
        $ret .= static::htmlSelectOptions($array, $default, $withNull);
        $ret .= "</select>\n";

        return $ret;
    }

    /**
     * Extrait l'id d'une chaine formatée sous la forme xxxx-99 (duquel on récupère 99)
     *
     * @note: utilisé par les attributs produits
     * @param  string $string    La chaine de travail
     * @param  string $separator Le séparateur
     * @return string
     */
    public static function getId($string, $separator = '_')
    {
        $pos = mb_strrpos($string, $separator);
        if (false === $pos) {
            return $string;
        }

        return (int)mb_substr($string, $pos + 1);
    }

    /**
     * Fonction "inverse" de getId (depuis xxxx-99 on récupère xxxx)
     *
     * @note: utilisé par les attributs produits
     * @param  string $string    La chaine de travail
     * @param  string $separator Le séparateur
     * @return string
     */
    public static function getName($string, $separator = '_')
    {
        $pos = mb_strrpos($string, $separator);
        if (false === $pos) {
            return $string;
        }

        return mb_substr($string, 0, $pos);
    }

    /**
     * Renvoie un montant nul si le montant est négatif
     *
     * @param  float $amount
     */
    public static function doNotAcceptNegativeAmounts(&$amount)
    {
        if ($amount < 0) {
            $amount = 0.0;
        }
    }

    /**
     * Returns a string from the request
     *
     * @param  string $valueName    Name of the parameter you want to get
     * @param  mixed  $defaultValue Default value to return if the parameter is not set in the request
     * @return mixed
     */
    public static function getFromRequest($valueName, $defaultValue = '')
    {
        return isset($_REQUEST[$valueName]) ? $_REQUEST[$valueName] : $defaultValue;
    }

    /**
     * Verify that a mysql table exists
     *
     * @author        Instant Zero (http://xoops.instant-zero.com)
     * @copyright (c) Instant Zero
     * @param $tablename
     * @return bool
     */
    public static function tableExists($tablename)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW TABLES LIKE '$tablename'");

        return ($xoopsDB->getRowsNum($result) > 0);
    }

    /**
     * Verify that a field exists inside a mysql table
     *
     * @author        Instant Zero (http://xoops.instant-zero.com)
     * @copyright (c) Instant Zero
     * @param $fieldname
     * @param $table
     * @return bool
     */
    public static function fieldExists($fieldname, $table)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW COLUMNS FROM $table LIKE '$fieldname'");

        return ($xoopsDB->getRowsNum($result) > 0);
    }

    /**
     * Retourne la définition d'un champ
     *
     * @param  string $fieldname
     * @param  string $table
     * @return array|string
     */
    public static function getFieldDefinition($fieldname, $table)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("SHOW COLUMNS FROM $table LIKE '$fieldname'");
        if ($result) {
            return $xoopsDB->fetchArray($result);
        }

        return '';
    }

    /**
     * Add a field to a mysql table
     *
     * @author        Instant Zero (http://xoops.instant-zero.com)
     * @copyright (c) Instant Zero
     * @param $field
     * @param $table
     * @return bool|\mysqli_result
     */
    public static function addField($field, $table)
    {
        global $xoopsDB;
        $result = $xoopsDB->queryF("ALTER TABLE $table ADD $field;");

        return $result;
    }

    /**
     * @param $info
     * @return string
     */
    public static function packingHtmlSelect($info)
    {
        $ret = '';
        $ret .= '<div class="oledrion_htmlform">';
        $ret .= '<img class="oledrion_htmlimage" src="' . $info['packing_image_url'] . '" alt="' . $info['packing_title'] . '">';
        $ret .= '<h3>' . $info['packing_title'] . '</h3>';
        if ($info['packing_price'] > 0) {
            $ret .= '<p><span class="bold">' . _OLEDRION_PRICE . '</span> : ' . $info['packing_price_fordisplay'] . '</p>';
        } else {
            $ret .= '<p><span class="bold">' . _OLEDRION_PRICE . '</span> : ' . _OLEDRION_FREE . '</p>';
        }
        $ret .= '<p>' . $info['packing_description'] . '</p>';
        $ret .= '</div>';

        return $ret;
    }

    /**
     * @param $info
     * @return string
     */
    public static function deliveryHtmlSelect($info)
    {
        $ret = '';
        $ret .= '<div class="oledrion_htmlform">';
        $ret .= '<img class="oledrion_htmlimage" src="' . $info['delivery_image_url'] . '" alt="' . $info['delivery_title'] . '">';
        $ret .= '<h3>' . $info['delivery_title'] . '</h3>';
        if ($info['delivery_price'] > 0) {
            $ret .= '<p><span class="bold">' . _OLEDRION_PRICE . '</span> : ' . $info['delivery_price_fordisplay'] . '</p>';
        } else {
            $ret .= '<p><span class="bold">' . _OLEDRION_PRICE . '</span> : ' . _OLEDRION_FREE . '</p>';
        }
        $ret .= '<p><span class="bold">' . _OLEDRION_DELIVERY_TIME . '</span> : ' . $info['delivery_time'] . _OLEDRION_DELIVERY_DAY . '</p>';
        $ret .= '<p>' . $info['delivery_description'] . '</p>';
        $ret .= '</div>';

        return $ret;
    }

    /**
     * @param $info
     * @return string
     */
    public static function paymentHtmlSelect($info)
    {
        $ret = '';
        $ret .= '<div class="oledrion_htmlform">';
        $ret .= '<img class="oledrion_htmlimage" src="' . $info['payment_image_url'] . '" alt="' . $info['payment_title'] . '">';
        $ret .= '<h3>' . $info['payment_title'] . '</h3>';
        $ret .= '<p>' . $info['payment_description'] . '</p>';
        $ret .= '</div>';

        return $ret;
    }

    /**
     * @return array
     */
    public static function getCountriesList()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

        return \XoopsLists::getCountryList();
    }

    /**
     * Retourne la liste des groupes de l'utlisateur courant (avec cache)
     * @return array Les ID des groupes auquel l'utilisateur courant appartient
     */
    public static function getCurrentMemberGroups()
    {
        static $buffer = [];

        if ($buffer && is_array($buffer)) {
            return $buffer;
        }
        $uid = self::getCurrentUserID();
        if ($uid > 0) {
            /** @var \XoopsMemberHandler $memberHandler */
            $memberHandler = xoops_getHandler('member');
            $buffer        = $memberHandler->getGroupsByUser($uid, false);    // Renvoie un tableau d'ID (de groupes)
        } else {
            $buffer = [XOOPS_GROUP_ANONYMOUS];
        }

        return $buffer;
    }
}
