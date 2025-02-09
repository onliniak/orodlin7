<?php
/**
 *   File functions:
 *   Add awaiting news
 *
 *   @name				 : addtext.php
 *   @copyright			: (C) 2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author			   : thindil <thindil@users.sourceforge.net>
 *   @author			   : eyescream <tduda@users.sourceforge.net>
 *   @version			  : 1.3
 *   @since				: 22.12.2006
 *
 */

//
//
//	   This program is free software; you can redistribute it and/or modify
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
// $Id: addtext.php 879 2007-01-23 17:19:03Z thindil $

$objText = $db -> Execute('SELECT `id`, `title`, `starter` FROM `news` WHERE `added`=\'N\' AND `lang`=\''.$player -> lang.'\' OR `lang`=\''.$player -> seclang.'\'');
$arrId = array();
$arrTitle = array();
$arrAuthor = array();
$i = 0;
while (!$objText -> EOF) {
    $arrId[$i] = $objText -> fields['id'];
    $arrTitle[$i] = $objText -> fields['title'];
    $arrAuthor[$i] = $objText -> fields['starter'];
    $i++;
    $objText -> MoveNext();
}
$objText -> Close();
$smarty -> assign(array('Ttitle' => $arrTitle,
                        'Tid' => $arrId,
                        'Tauthor' => $arrAuthor,
                        'Admininfo' => ADMIN_INFO,
                        'Admininfo2' => ADMIN_INFO2,
                        'Admininfo3' => ADMIN_INFO3,
                        'Admininfo4' => ADMIN_INFO4,
                        'Admininfo5' => ADMIN_INFO5,
                        'Tauthor2' => T_AUTHOR,
                        'Amodify' => A_MODIFY,
                        'Aadd' => A_ADD,
                        'Adelete' => A_DELETE));
/**
 * Modify text
 */
if (isset($_GET['action']) && $_GET['action'] == 'modify') {
    if (!preg_match("/^[1-9][0-9]*$/", $_GET['text'])) {
        error(ERROR);
    }
    require_once('includes/bbcode.php');
    $objText = $db -> Execute('SELECT `id`, `title`, `news` FROM `news` WHERE `id`='.$_GET['text']);
    $smarty -> assign(array('Ttitle' => $objText -> fields['title'],
                            'Tbody' => htmltobbcode($objText -> fields['news']),
                            'Tid' => $objText -> fields['id'],
                            'Ttitle2' => T_TITLE,
                            'Tbody2' => T_BODY,
                            'Achange' => A_CHANGE));
    $objText -> Close();
    if (isset($_POST['tid'])) {
        if (!preg_match("/^[1-9][0-9]*$/", $_POST['tid'])) {
            error(ERROR);
        }
        if (empty($_POST['ttitle']) || empty($_POST['body'])) {
            error(EMPTY_FIELDS);
        }
        $_POST['body'] = nl2br($_POST['body']);

        $_POST['body'] = bbcodetohtml($_POST['body']);
        $strBody = $db -> qstr($_POST['body'], get_magic_quotes_gpc());
        $strTitle = $db -> qstr($_POST['ttitle'], get_magic_quotes_gpc());
        $db -> Execute('UPDATE `news` SET `title`='.$strTitle.', `news`='.$strBody.' WHERE `id`='.$_POST['tid']);
        error(MODIFIED);
    }
}
/**
 * Add or delete news
 */
if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'delete')) {
    if (!preg_match("/^[1-9][0-9]*$/", $_GET['text'])) {
        error(ERROR);
    }
    $objText = $db -> Execute('SELECT `id`, `starter`, `title` FROM `news` WHERE `id`='.$_GET['text']);
    if (!$objText -> fields['id']) {
        error(NO_TEXT);
    }
    $strTitle = $objText -> fields['title'];
    $arrStarter = explode("(", $objText -> fields['starter']);
    $intStarter = preg_replace("/)/", "", $arrStarter[1]);
    $objText -> Close();
    $strDate = $db -> DBDate($newdate);
    if ($_GET['action'] == 'add') {
        $objQuery = $db -> Execute('SELECT `id` FROM `news` ORDER BY `id` DESC');
        $intId = $objQuery -> fields['id'] + 1;
        $objQuery -> Close();
        $db -> Execute('UPDATE `news` SET `added`=\'Y\', `id`='.$intId.', `show`=\'Y\' WHERE `id`='.$_GET['text']);
        $db -> Execute('INSERT INTO `log` (`owner`, `log`, `czas`) VALUES('.$intStarter.',\''.YOUR_NEWS.$strTitle.HAS_ADDED.'<b><a href="view.php?view='.$player -> id .'">'.$player -> user.'</a></b>'.L_ID.'<b>'.$player -> id.'</b>.\', '.$strDate.")");
        error(ADDED);
    } else {
        $db -> Execute('INSERT INTO `log` (`owner`, `log`, `czas`) VALUES('.$intStarter.',\''.YOUR_NEWS.$strTitle.HAS_DELETED.'<b><a href="view.php?view='.$player -> id .'">'.$player -> user.'</a></b>'.L_ID.'<b>'.$player -> id.'</b>.\', '.$strDate.")");
        $db -> Execute('DELETE FROM `news` WHERE `id`='.$_GET['text']);
        error(DELETED);
    }
}
