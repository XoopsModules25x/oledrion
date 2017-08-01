<?php
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
 * Gestion des plugins du module
 *
 * On distingue les "filtres" (plugin qui reçoit du contenu qu'il peut modifier et dont le retour est passé au filtre suivant)
 * des "actions" (plugin qui est généralement appelé sur un évènement et qui ne doit rien modifier).
 *
 * Chaque plugin dispose d'une priorité de 1 à 5, 1 étant la priorité la plus haute, 5 la plus basse
 *
 * Dans le répertoire plugins on a 2 sous répertoires, "actions" et "filters", en fonction du type du plugin
 *
 * Chaque plugin doit se trouver dans son propre sous-répertoire, par exemple :
 *     /xoops/modules/oledrion/plugins/pdf/
 *
 * Cela permet aux plugins d'avoir plusieurs fichiers et de ne pas les mélanger avec les autres plugins
 *
 * Le module scrute ces 2 répertoires ("actions" et "filters") pour charger les plugins.
 * Chaque répertoire doit contenir un script "plugins.php" qui permet la descriptions des plugins qui se trouvent dans ce répertoire.
 * A titre d'exemple, voir ceux fournits de base avec le module
 *
 * Les modèles de classe à étendre se trouvent dans /xoops/modules/oledrion/plugins/modules/oledrion_action.php et oledrion_filter.php
 *
 * @since 2.31
 */
class Oledrion_plugins
{
    /**
     * Dictionnaire des évènements
     */
    const EVENT_ON_PRODUCT_CREATE = 'onProductCreate';
    const EVENT_ON_CATEGORY_CREATE = 'onCategoryCreate';
    const EVENT_ON_PRODUCT_DOWNLOAD = 'onProductDownload';
    // **************************************************************

    /**
     * Types d'évènements
     */
    const PLUGIN_ACTION = 0;
    const PLUGIN_FILTER = 1;

    /**
     * Nom du script Php inclut qui contient l'inscription des plugins
     */
    const PLUGIN_SCRIPT_NAME = 'plugins.php';

    /**
     * Dans le fichier Php qui contient l'inscription des plugins, méthode à appeler pour récupérer la liste des plugins
     */
    const PLUGIN_DESCRIBE_METHOD = 'registerEvents';

    /**
     * Nom de la variable de session qui contient la liste des plugins détachés
     */
    const PLUGIN_UNPLUG_SESSION_NAME = 'oledrion_plugins';

    /**
     * Priorités des plugins
     * @var constant
     */
    const EVENT_PRIORITY_1 = 1; // Priorité la plus haute
    const EVENT_PRIORITY_2 = 2;
    const EVENT_PRIORITY_3 = 3;
    const EVENT_PRIORITY_4 = 4;
    const EVENT_PRIORITY_5 = 5; // Priorité la plus basse

    /**
     * Utilisé pour construire le nom de la classe
     */
    private $pluginsTypeLabel = array(self::PLUGIN_ACTION => 'Action', self::PLUGIN_FILTER => 'Filter');

    /**
     * Nom des classes qu'il faut étendre en tant que plugin
     */
    private $pluginsClassName = array(
        self::PLUGIN_ACTION => 'oledrion_action',
        self::PLUGIN_FILTER => 'oledrion_filter'
    );

    /**
     * Nom de chacun des dossiers en fonction du type de plugin
     */
    private $pluginsTypesFolder = array(self::PLUGIN_ACTION => 'actions', self::PLUGIN_FILTER => 'filters');

    /**
     * Contient l'unique instance de l'objet
     * @var Oledrion_plugins
     */
    private static $instance = false;

    /**
     * Liste des évènements
     * @var array
     */
    private static $events = array();

    /**
     * Retourne l'instance unique de la classe
     *
     * @return Oledrion_plugins
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
     * Chargement des 2 types de plugins
     *
     */
    private function __construct()
    {
        $this->events = array();
        $this->loadPlugins();
    }

    /**
     * Chargement des plugins (actions et filtres)
     * @return void
     */
    public function loadPlugins()
    {
        $this->loadPluginsFiles(OLEDRION_PLUGINS_PATH . $this->pluginsTypesFolder[self::PLUGIN_ACTION], self::PLUGIN_ACTION);
        $this->loadPluginsFiles(OLEDRION_PLUGINS_PATH . $this->pluginsTypesFolder[self::PLUGIN_FILTER], self::PLUGIN_FILTER);
    }

    /**
     * Vérifie que le fichier Php passé en paramètre contient bien une classe de filtre ou d'action et si c'est le cas, le charge dans la liste des plugins
     * @param  string  $fullPathName Chemin complet vers le fichier (répertoire + nom)
     * @param  integer $type         Type de plugin recherché (action ou filtre)
     * @param  string  $pluginFolder Le nom du répertoire dans lequel se trouve le fichier (le "dernier nom")
     * @return void
     */
    private function loadClass($fullPathName, $type, $pluginFolder)
    {
        require_once $fullPathName;
        $className = strtolower($pluginFolder) . $this->pluginsTypeLabel[$type];
        if (class_exists($className) && get_parent_class($className) == $this->pluginsClassName[$type]) {
            // TODO: Vérifier que l'évènement n'est pas déjà en mémoire
            $events = call_user_func(array($className, self::PLUGIN_DESCRIBE_METHOD));
            foreach ($events as $event) {
                $eventName                                         = $event[0];
                $eventPriority                                     = $event[1];
                $fileToInclude                                     = OLEDRION_PLUGINS_PATH . $this->pluginsTypesFolder[$type] . '/' . $pluginFolder . '/' . $event[2];
                $classToCall                                       = $event[3];
                $methodToCall                                      = $event[4];
                $this->events[$type][$eventName][$eventPriority][] = array(
                    'fullPathName' => $fileToInclude,
                    'className'    => $classToCall,
                    'method'       => $methodToCall
                );
            }
        }
    }

    /**
     * Part à la recherche d'un type de plugin dans les répertoires
     *
     * @param  string  $path La racine
     * @param  integer $type Le type de plugin recherché (action ou filtre)
     * @return void
     */
    private function loadPluginsFiles($path, $type)
    {
        $objects = new DirectoryIterator($path);
        foreach ($objects as $object) {
            if ($object->isDir() && !$object->isDot()) {
                $file = $path . '/' . $object->current() . '/' . self::PLUGIN_SCRIPT_NAME;
                if (file_exists($file)) {
                    $this->loadClass($file, $type, $object->current());
                }
            }
        }
    }

    /**
     * Déclenchement d'une action et appel des plugins liés
     *
     * @param  string              $eventToFire L'action déclenchée
     * @param  Oledrion_parameters $parameters  Les paramètres à passer à chaque plugin
     * @return Oledrion_plugins                     L'objet lui même pour chaîner
     */
    public function fireAction($eventToFire, Oledrion_parameters $parameters = null)
    {
        if (!isset($this->events[self::PLUGIN_ACTION][$eventToFire])) {
            trigger_error(sprintf(_OLEDRION_PLUGINS_ERROR_1, $eventToFire));

            return $this;
        }
        ksort($this->events[self::PLUGIN_ACTION][$eventToFire]); // Tri par priorité
        foreach ($this->events[self::PLUGIN_ACTION][$eventToFire] as $priority => $events) {
            foreach ($events as $event) {
                if ($this->isUnplug(self::PLUGIN_ACTION, $eventToFire, $event['fullPathName'], $event['className'], $event['method'])) {
                    continue;
                }
                require_once $event['fullPathName'];
                if (!class_exists($event['className'])) {
                    $class = new $event['className'];
                }
                if (!method_exists($event['className'], $event['method'])) {
                    continue;
                }
                call_user_func(array($event['className'], $event['method']), $parameters);
                unset($class);
            }
        }

        return $this;
    }

    /**
     * Déclenchement d'un filtre et appel des plugins liés
     *
     * @param  string              $eventToFire Le filtre appelé
     * @param  Oledrion_parameters $parameters  Les paramètres à passer à chaque plugin
     * @return Oledrion_parameters|Oledrion_plugins                     Le contenu de l'objet passé en paramètre
     */
    public function fireFilter($eventToFire, Oledrion_parameters $parameters)
    {
        if (!isset($this->events[self::PLUGIN_FILTER][$eventToFire])) {
            trigger_error(sprintf(_OLEDRION_PLUGINS_ERROR_1, $eventToFire));

            return $this;
        }
        ksort($this->events[self::PLUGIN_FILTER][$eventToFire]); // Tri par priorité
        foreach ($this->events[self::PLUGIN_FILTER][$eventToFire] as $priority => $events) {
            foreach ($events as $event) {
                if ($this->isUnplug(self::PLUGIN_FILTER, $eventToFire, $event['fullPathName'], $event['className'], $event['method'])) {
                    continue;
                }
                require_once $event['fullPathName'];
                if (!class_exists($event['className'])) {
                    $class = new $event['className'];
                }
                if (!method_exists($event['className'], $event['method'])) {
                    continue;
                }
                call_user_func(array($event['className'], $event['method']), $parameters);
                unset($class);
            }
        }

        if (null !== $parameters) {
            return $parameters;
        }
    }

    /**
     * Indique si un plugin s'est détaché d'un évènement particulier
     *
     * @param  integer $eventType
     * @param  string  $eventToFire
     * @param  string  $fullPathName
     * @param  string  $className
     * @param  string  $method
     * @return boolean
     */
    public function isUnplug($eventType, $eventToFire, $fullPathName, $className, $method)
    {
        $unplug = array();
        if (isset($_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME])) {
            $unplug = $_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME];
        } else {
            return false;
        }

        return isset($unplug[$eventType][$eventToFire][$fullPathName][$className][$method]);
    }

    /**
     * Permet à un plugin de se détacher d'un évènement
     *
     * @param  integer $eventType
     * @param  string  $eventToFire
     * @param  string  $fullPathName
     * @param  string  $className
     * @param  string  $method
     * @return void
     */
    public function unplugFromEvent($eventType, $eventToFire, $fullPathName, $className, $method)
    {
        $unplug = array();
        if (isset($_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME])) {
            $unplug = $_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME];
        }
        $unplug[$eventType][$eventToFire][$fullPathName][$className][$method] = true;
        $_SESSION[self::PLUGIN_UNPLUG_SESSION_NAME]                           = $unplug;
    }
}
