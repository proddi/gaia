<link rel="stylesheet" type="text/css" href="style.css" />
<?php

require_once 'Markdown.php';
require_once 'Markdown_Parser.php';

// $md = file_get_contents('syntax.md');
$md = file_get_contents('../../Readme.md');

$markdown = new Markdown_Parser;
//echo $markdown->transform($md);
//echo Markdown::transform(file_get_contents('../../Readme.md'));
echo Markdown::transform($md);

?>
