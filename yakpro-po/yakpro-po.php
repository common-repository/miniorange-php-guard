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
// if (isset($_SERVER["SERVER_SOFTWARE"]) && ($_SERVER["SERVER_SOFTWARE"]!="") ){ echo "<h1>Comand Line Interface Only!</h1>"; die; }
$ROOT = MO_PHP_GUARD_PLUGIN_PATH;
require $ROOT."/vendor/autoload.php";

const PHP_PARSER_DIRECTORY  = 'PHP-Parser';


// require_once 'include/check_version.php';

require_once 'include/get_default_defined_objects.php';     // include this file before defining something....


require_once 'include/classes/config.php';
require_once 'include/classes/scrambler.php';
require_once 'include/functions.php';
require_once 'version.php';

include      'include/retrieve_config_and_arguments.php';

require_once 'include/classes/parser_extensions/my_autoloader.php';
require_once 'include/classes/parser_extensions/my_pretty_printer.php';
require_once 'include/classes/parser_extensions/my_node_visitor.php';

//$parser             = new PhpParser\Parser(new PhpParser\Lexer\Emulative);      // $parser = new PhpParser\Parser(new PhpParser\Lexer);
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;

$parser_mode = ParserFactory::PREFER_PHP7;

global $parser;
$parser = (new ParserFactory)->create($parser_mode);


//$traverser          = new PhpParser\NodeTraverser;
global $traverser;
$traverser          = new NodeTraverser;

global $prettyPrinter;
$prettyPrinter      = new myPrettyprinter;

global $t_scrambler;
$t_scrambler = array();
foreach(array('variable','function','method','property','class','class_constant','constant','label') as $scramble_what)
{
    $t_scrambler[$scramble_what] = new Scrambler($scramble_what, $conf, ($process_mode=='directory') ? $target_directory : null);
}
$whatis = '';
if ($whatis!=='')
{
    if ($whatis[0] == '$') $whatis = substr($whatis,1);
    foreach(array('variable','function','method','property','class','class_constant','constant','label') as $scramble_what)
    {
        if ( ( $s = $t_scrambler[$scramble_what]-> unscramble($whatis)) !== '')
        {
            switch($scramble_what)
            {
                case 'variable':
                case 'property':
                    $prefix = '$';
                    break;
                default:
                    $prefix = '';
            }
            echo "$scramble_what: {$prefix}{$s}".PHP_EOL;
        }
    }
    exit;
}

$traverser->addVisitor(new MyNodeVisitor);

switch($process_mode)
{
    case 'file':
        $obfuscated_str =  obfuscate($source_file);
        if ($obfuscated_str===null) { exit;                                         }
        if ($target_file   ===''  ) { echo $obfuscated_str.PHP_EOL.PHP_EOL; exit;   }
        file_put_contents($target_file,$obfuscated_str);
        break;
    case 'directory':
        obfuscate_directory($source_directory,"$target_directory/miniorange/obfuscated");
        break;
}
$directories = glob($source_directory.'/*' , GLOB_ONLYDIR);
foreach($directories as $dir) {
    process($source_directory."/", $target_directory, basename($dir));
}

/*8888888888888888888888888888888888888888888888888888888888888888888888888888888888*/
function process($source_directory, $target_directory, $foldername){
	
	$temp_source = $source_directory.$foldername."/";
	$source = "$target_directory/miniorange/obfuscated/".$foldername."/";
	//$dest= "ldap-login-for-intranet-sites-obfuscated";
    //mkdir($dest, 0755);
    if (!file_exists($temp_source)) {
        mkdir($temp_source, 0755, true);
    }
	// exit();
	foreach ( $iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item ) {
	  if ($item->isFile()) {
		$source_file_path = $source . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
		$ext = pathinfo($source_file_path, PATHINFO_EXTENSION);
		if($ext=="php")
			removecommentsAndPhpTokens($source_file_path);
	  }
	}

	foreach ( $iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($temp_source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item ) {
	  if ($item->isFile()) {
        $source_file_path = $temp_source . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
		$ext = pathinfo($source_file_path, PATHINFO_EXTENSION);
		if($ext=="php") {
			if(addplugininfo($source_file_path, $temp_source, $source))
                break;
        }
	  }
	}	
}

function removecommentsAndPhpTokens($filepath){
	// echo "<br>---------------removecommentsAndPhpTokens---------------<br>";
	// echo $filepath."<br>";
	$fileStr = file_get_contents($filepath);
	$newStr  = '';
	$commentTokens = array(T_COMMENT);
	if (defined('T_DOC_COMMENT'))
		$commentTokens[] = T_DOC_COMMENT; // PHP 5
	$tokens = token_get_all($fileStr);
	foreach ($tokens as $token) {    
		$flag = 0;
		if (is_array($token)) {
			if (in_array($token[0], $commentTokens)){
				if(strpos($token[1], "/*")===false)
					continue;
				else{
					if(strpos($token[1], "Obfuscated by miniOrange")!==false)
						continue;
					$flag = 1;
				}
			}
			$token = $token[1];
		}
		if($flag)
			$newStr .= $token;
		else{
			//$token = preg_replace('!\s+!', ' ', $token);
			$newStr .= $token;
		}
	}

    //echo $newStr;	
    $myfile = fopen($filepath, "w") or die("Unable to open file!");
	fwrite($myfile, $newStr);
	fclose($myfile);
}


function addplugininfo($filepath, $temp_source, $source){
    // echo "<br>---------------addplugininfo---------------<br>";
	// echo "1. ".$filepath."<br>";
	$fileStr = file_get_contents($filepath);
	$newStr  = '';
	$commentTokens = array(T_COMMENT);
	if (defined('T_DOC_COMMENT'))
		$commentTokens[] = T_DOC_COMMENT; // PHP 5
	$tokens = token_get_all($fileStr);
	$flag = 0;
    $wpcomment = "";
    // var_dump($commentTokens); 
	foreach ($tokens as $token) {  
		if (is_array($token)) {
			if (in_array($token[0], $commentTokens)){
				if(strpos($token[1], "/*")===false) {
                    continue;
                } else{
					if(strpos($token[1], "Plugin Name:")!==false){
						$flag = 1;
						$wpcomment = $token[1];
						break;
					}
				}
			}
		}
	}

	
	if($flag==1){
		$filepath = str_replace($temp_source, $source, $filepath);
        // exit();
		// echo $filepath;
		$fileStr = file_get_contents($filepath);
		$fileStr = str_replace("<?php", "<?php\n".$wpcomment, $fileStr);
		$myfile = fopen($filepath, "w") or die("Unable to open file!");
		fwrite($myfile, $fileStr);
		fclose($myfile);
		return true;
	}
	return false;
	
	
}


?>