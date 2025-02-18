<?php
/**
 *   File functions:
 *   Adding news in game
 *
 *   @name                 : addnews.php                            
 *   @copyright            : (C) 2004-2005 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @version              : 1.0 rc1
 *   @since                : 06.12.2005
 *
 */

//
//
//       This program is free software; you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation; either version 2 of the License, or
//   (at your option) any later version.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License
//   along with this program; if not, write to the Free Software
//   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// 

$title = "Dodaj Plotkę"; 
require_once("includes/head.php");

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/addnews.php");

if ($player -> rank != "Admin" && $player -> rank != 'Staff' && $player -> rank != 'Kronikarz') 
{
	error (NOT_HAVE);
}

/**
* Check avaible languages
*/    
$path = 'languages/';
$dir = opendir($path);
$arrLanguage = array();
$i = 0;
while ($file = readdir($dir))
{
    if (!preg_match("/.htm*$/", $file))
    {
        if (!preg_match("/\.$/", $file))
        {
            $arrLanguage[$i] = $file;
            $i = $i + 1;
        }
    }
}
closedir($dir);

/**
* Assign variables and display page
*/
$smarty -> assign(array("Ntitle" => N_TITLE,
    "Ntext" => N_TEXT,
    "Nadd" => N_ADD,
    "Nlangsel" => N_LANG_S,
    "Nlang" => $arrLanguage));
$smarty -> display('addnews.tpl');

if (isset ($_GET['action']) && $_GET['action'] == 'add') 
{
	if (empty ($_POST['addtitle']) || empty ($_POST['addnews'])) 
    {
		error (EMPTY_FIELDS);
	}
	$_POST['addnews'] = nl2br($_POST['addnews']);	
	$db -> Execute("INSERT INTO news (starter, title, news, lang, added) VALUES('".$player -> user." (".$player -> id.")','".$_POST['addtitle']."','".$_POST['addnews']."', '".$_POST['addlang']."', 'N')") or error(E_DB);
	error (N_SUCCES);
}

require_once("includes/foot.php");
?>
