<?php
/*

Usage:

    Copy clone.php in <xoops_root>
    Change current working directory to <xoops_root>
    Update mappings as per new modulename.

    php -q clone.php

*/

// ##########################################################
//  Define your mapping here
// ##########################################################
$patterns = [
    // first one must be module directory name
    'oledrion' => 'bouquins',
    'OLEDRION' => 'BOUQUINS',
    'Oledrion' => 'Bouquins',
    'Oledrion' => 'Bouquins'
];

$patKeys   = array_keys($patterns);
$patValues = array_values($patterns);

// work around for PHP < 5.0.x
if (!function_exists('file_put_contents')) {
    /**
     * @param      $filename
     * @param      $data
     * @param bool $file_append
     */
    function file_put_contents($filename, $data, $file_append = false)
    {
        $fp = fopen($filename, (!$file_append ? 'w+' : 'a+'));
        if (!$fp) {
            trigger_error('file_put_contents cannot write in file.', E_USER_ERROR);

            return;
        }
        fwrite($fp, $data);
        fclose($fp);
    }
}

// recursive clonning script
/**
 * @param $path
 */
function cloneFileFolder($path)
{
    global $patKeys;
    global $patValues;

    $newPath = str_replace($patKeys[0], $patValues[0], $path);

    if (is_dir($path)) {
        // create new dir
        mkdir($newPath);

        // check all files in dir, and process it
        if ($handle = opendir($path)) {
            while ($file = readdir($handle)) {
                if ('.' !== $file && '..' !== $file) {
                    cloneFileFolder("$path/$file");
                }
            }
            closedir($handle);
        }
    } else {
        if (preg_match('/(.jpg|.gif|.png|.zip)$/i', $path)) {
            copy($path, $newPath);
        } else {
            // file, read it
            $content = file_get_contents($path);
            $content = str_replace($patKeys, $patValues, $content);
            file_put_contents($newPath, $content);
        }
    }
}

cloneFileFolder('modules/oledrion');

echo "Happy cloning...\n";
echo 'check directory modules/' . $patterns['oledrion'] . " for cloned module \n";
echo "Consider modifying new module by editing language/english/modinfo.php and assets/images/oledrion_logo.png manually (if you care)\n";
