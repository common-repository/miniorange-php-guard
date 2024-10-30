<?php
//========================================================================
// Author:  Pascal KISSIAN
// Resume:  http://pascal.kissian.net
//
// Copyright (c) 2015-2018 Pascal KISSIAN
//
// Published under the MIT License
//          Consider it as a proof of concept!
//          No warranty of any kind.
//          Use and abuse at your own risks.
//========================================================================

$yakpro_po_dirname      = $ROOT."/yakpro-po";

$config_filename        = '';
$process_mode           = '';   // can be: 'file' or 'directory'
$src = '';
$targ = '';
$t_args                 = array();
// $t_args now containes remaining parameters.
// We will first look for config file, and then we will analyze $t_args accordingly

$config_file_namepart = 'yakpro-po.cnf';

$config_filename = "$yakpro_po_dirname/yakpro-po.cnf";     //source_code_directory/default_conf_filename

global $conf;
$conf = new Config;


if ($config_filename=='')   {
    echo "Warning:No config found... using default values!",PHP_EOL;
}
else
{
    $config_filename = realpath($config_filename);
    // if (!$conf->silent) {
        //     echo "Info:\tUsing ".$config_filename." Config File...<br>",PHP_EOL;
    // }
    require_once $config_filename;
    $conf->validate();
}
//var_dump($conf);

if (!$conf->silent) {
    echo "Info:\tyakpro-po version = $yakpro_po_version<br>";
}

$conf->source_directory = MO_PHP_GUARD_SOURCE_PATH;
$conf->target_directory = MO_PHP_GUARD_DESTINATION_PATH;
switch(count($t_args))
{
    case 0:
    if (isset($conf->source_directory) && isset($conf->target_directory))
    {
        $process_mode       = 'directory';
        $source_directory   = $conf->source_directory;
        $target_directory   = $conf->target_directory;
        create_context_directories($target_directory);
        break;
    }
    echo "<br>Error:\tsource_directory and target_directory not specified!<br>\tneither within command line parameter,<br>\tnor in config file!<br>";
    exit(-1);
    case 1:
    $source_file = realpath($t_args[0]);
    if (($source_file!==false) && file_exists($source_file))
    {
        if (is_file($source_file) && is_readable($source_file))
            {
                $process_mode   = 'file';
                $target_file    = $target;
                if ( ($target_file!=='') && file_exists($target_file) )
                {
                    $x = realpath($target_file);
                    if (is_dir($x))
                    {
                        echo "Error:\tTarget file [".($x!==false) ? $x : $target_file."] is a directory!<br>";
                        exit(-1);
                    }
                    if ( is_readable($x) && is_writable($x) && is_file($x) && (file_get_contents($x)!=='') )
                    {
                        $fp = fopen($target_file,"r");
                        $y = fgets($fp);
                        $y = fgets($fp).fgets($fp).fgets($fp).fgets($fp).fgets($fp);
                        if (strpos($y,'    |  Obfuscated by miniOrange - Php Obfuscator ')===false)       // comment is a magic string, used to not overwrite wrong files!!!
                        {
                            $x = realpath($target_file);
                            echo "Error:\tTarget file [".($x!==false) ? $x : $target_file."] exists and is not an obfuscated file!<br>";
                            exit(-1);
                        }
                        fclose($fp);
                    }
                }
                break;
            }
            if (is_dir($source_file))
            {
                $process_mode       = 'directory';
                $source_directory   = $source_file;
                $target_directory   = $target;
                if (($target_directory=='') && isset($conf->target_directory)) $target_directory = $conf->target_directory;
                if ( $target_directory=='')
                {
                    echo "Error:\tTarget directory is not specified!<br>";
                    exit(-1);
                }
                create_context_directories($target_directory);
                break;
            }            
        }
        if($source_file !== false) {
            echo "Error:\tSource file ".$source_file." is not readable!<br>";
        }
        exit(-1);
    default:
        echo "Error:\tToo many parameters specified!",PHP_EOL;
        exit(-1);
}
//print_r($t_args);

if (!$conf->silent) echo "Info:\tProcess Mode\t\t= $process_mode<br>";
switch($process_mode)
{
    case 'file':
        if (!$conf->silent) echo "Info:\tsource_file\t\t= [$source_file]<br>";
        if (!$conf->silent) echo "Info:\ttarget_file\t\t= [".($target_file!=='') ? $target_file : 'stdout'."]<br>";
        break;
    case 'directory':
        if (!$conf->silent) echo "Info:\tsource_directory\t= [$source_directory]<br>";
        if (!$conf->silent) echo "Info:\ttarget_directory\t= [$target_directory]<br>";
        break;
}


?>
