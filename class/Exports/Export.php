<?php namespace XoopsModules\Oledrion\Exports;

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
 * Every export script must extend this class
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

abstract class Export
{
    protected $separator; // Fields separator
    protected $filename; // Filename of the exported file
    protected $folder; // Folder's path (where to create the file) WITHOUT TRAILING SLASH
    protected $url; // Folder's URL (where to download the file) WITHOUT TRAILING SLASH
    protected $orderType; // Type of order to treat
    protected $commandsHandler;
    protected $caddyHandler;
    protected $success = false;
    protected $handlers;

    /**
     * Export constructor.
     * @param string $parameters
     */
    public function __construct($parameters = '')
    {
        if (is_array($parameters)) {
            $this->separator = $parameters['separator'];
            $this->filename  = $parameters['filename'];
            $this->folder    = $parameters['folder'];
            $this->url       = $parameters['url'];
            $this->orderType = $parameters['orderType'];
        }
        $this->getHandlers();
    }

    private function getHandlers()
    {
        //        $this->handlers = HandlerManager::getInstance();
    }

    /**
     * @param $separator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    /**
     * @param $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param $orderType
     */
    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;
    }

    /**
     * Export orders according to all the options
     * @return true if export was successful or false
     *
     */
    abstract public function export();

    abstract public function getDownloadUrl();

    abstract public function getDownloadPath();
}
