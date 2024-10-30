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

$yakpro_po_base_directory   = dirname(realpath($argv[0]));
//$php_parser_git_commandline = 'git clone --branch=4.x https://github.com/nikic/PHP-Parser.git';
$php_parser_git_commandline = 'git clone https://github.com/nikic/PHP-Parser.git';

if(!file_exists("$yakpro_po_base_directory/PHP-Parser/composer.json"))
{
    echo "Error:\tPHP-Parser is not correctly installed!<br>You can try to use the following command:<br>\t# $php_parser_git_commandline<br>";
    exit(-1);
}

$t_composer             = json_decode(file_get_contents("$yakpro_po_base_directory/PHP-Parser/composer.json"));   //print_r($t_composer);
$php_parser_branch      = $t_composer->{'extra'}->{'branch-alias'}->{'dev-master'};
$required_php_version   = $t_composer->{'require'}->{'php'};

$operator = '';for($i=0;!ctype_digit($c=$required_php_version{$i});++$i) $operator.=$c; $required_php_version = substr($required_php_version,$i);

if (substr($php_parser_branch,0,2)!='4.')
{
    echo "Error:\tWrong version of PHP-Parser detected!<br>Currently, only 4.x branch of PHP-Parser is supported!<br>\tYou can try to use the following command:<br>\t# $php_parser_git_commandline<br>";
    exit(-1);
}

if (!version_compare(PHP_VERSION,$required_php_version,$operator))
{
    echo "Error:\tPHP Version must be $operator $required_php_version<br>";
    exit(-1);
}


?>