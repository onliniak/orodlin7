<?php
/**
 *   File functions:
 *   Clans - info, manage, herbs, minerals and battles
 *
 *   @name                 : tribes.php
 *   @copyright            : (C) 2004,2005,2006,2007 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @author               : mori <ziniquel@users.sourceforge.net>
 *   @author               : eyescream <tduda@users.sourceforge.net>
 *   @author               : Erechail <kuba.stasiak at gmail.com>
 *   @version              : 1.3a
 *   @since                : 17.07.2007
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
// $Id: tribes.php 918 2007-03-03 17:55:42Z thindil $

$title = "Klany";
require_once("includes/head.php");
require_once('includes/security.php');

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/tribes.php");

if ($player -> location != 'Altara' && $player -> location != 'Ardulith') {
    error(ERROR, RET_LOC);
}

$smarty -> assign(array("Logo" => '',
                        "WWW" => '',
                        "Message" => '',
                        "Message2" => '',
                        "Pubmessage" => '',
                        "Privmessage" => '',
                        "Hospass1" => '',
                        "Message1" => '',
                        "Step4" => '',
                        "New" => '',
                        "Perm" => '',
                        "Victory" => '',
                        "Link" => '',
                        "Menu" => '',
                        "Empty" => '',
                        "Change" => '',
                        "Itemid" => '',
                        "Perm1" => 0,
                        "Perm2" => 0,
                        "Perm3" => 0,
                        "Perm4" => 0,
                        "Perm5" => 0,
                        "Perm6" => 0,
                        "Perm7" => 0,
                        "Perm8" => 0,
                        "Perm9" => 0,
                        "Perm10" => 0,
                        "Perm11" => 0,
                        "Rank1" => 0,
                        "Rank2" => 0,
                        "Rank3" => 0,
                        "Rank4" => 0,
                        "Rank5" => 0,
                        "Rank6" => 0,
                        "Rank7" => 0,
                        "Rank8" => 0,
                        "Rank9" => 0,
                        "Rank10" => 0));

/**
* Main menu
*/
if (!isset($_GET['view']) && !isset($_GET['join'])) {
    $smarty -> assign(array("Claninfo" => CLAN_INFO,
        "Ashow" => A_SHOW));
    if ($player -> tribe) {
        $mytribe = $db -> Execute("SELECT `name` FROM `tribes` WHERE `id`=".$player -> tribe);
        $smarty -> assign("Mytribe", "<li><a href=\"tribes.php?view=my\">".MY_TRIBE."</a> (".$mytribe -> fields['name'].")</li>");
    } else {
        $smarty -> assign("Mytribe", "<li>".MY_TRIBE."</li>");
    }
    if (!$player -> tribe && $player -> credits >= 2500000) {
        $smarty -> assign("Make", "<li><a href=\"tribes.php?view=make\">".MAKE_NEW."</a></li>");
    } else {
        $smarty -> assign("Make", "<li>".MAKE_NEW."</li>");
    }
}

/**
* List of clans
*/
if (isset($_GET['view']) && $_GET['view'] == 'all') {
    $query = $db -> Execute("SELECT count(*) FROM `tribes`");
    $numt = $query -> fields['count(*)'];
    $query -> Close();
    if ($numt <= 0) {
        $smarty -> assign(array("Text" => NO_CLANS,
                           "Showinfo" => ''));
    } else {
        $smarty -> assign("Text", "<ul>");
        $tribe = $db -> Execute("SELECT `id`, `name`, `owner` FROM `tribes` ORDER BY `id`");
        $arrid = array();
        $arrname = array();
        $arrowner = array();
        $ID2Name = array();
        $i = 0;
        while (!$tribe -> EOF) {
            $arrid[$i] = $tribe -> fields['id'];
            $arrname[$i] = $tribe -> fields['name'];
            $arrownerid[$i] = $tribe -> fields['owner'];
            if (!isset($ID2Name [$i])) {
                $objname = $db->Execute('SELECT user FROM players WHERE id = \''.$arrownerid[$i].'\'');
                $ID2Name[$arrownerid[$i]] = $objname->fields['user'];
            }

            $arrowner[$i] = $ID2Name[$arrownerid[$i]];
            $tribe -> MoveNext();
            $i = $i + 1;
        }
        $tribe -> Close();
        $smarty -> assign(array("Tribeid" => $arrid,
            "Name" => $arrname,
            "OwnerID" => $arrownerid,
            'Owner'	=>	$arrowner,
            "Showinfo" => SHOW_INFO,
            "Leader" => LEADER));
    }
}

/**
* Clan info
*/
if (isset($_GET['view']) && $_GET['view'] == 'view') {
    if (!isset($_GET['step'])) {
        if (!preg_match("/^[1-9][0-9]*$/", $_GET['id'])) {
            error(ERROR);
        }
        $tribe = $db -> Execute("SELECT `id`, `name`, `owner`, `wygr`, `przeg`, `public_msg`, `www`, `logo` FROM `tribes` WHERE `id`=".$_GET['id']);
        if (!$tribe -> fields['id']) {
            error(NO_CLAN);
        }
        $plik = 'images/tribes/'.$tribe -> fields['logo'];
        $query = $db -> Execute("SELECT count(*) FROM `players` WHERE `tribe`=".$tribe -> fields['id']);
        $memnum = $query -> fields['count(*)'];
        $query -> Close();
        if (is_file($plik)) {
            $arrImageparams = getimagesize($plik);
            if ($arrImageparams[0] > 200) {
                $arrImageparams[0] = 200;
            }
            if ($arrImageparams[1] > 100) {
                $arrImageparams[1] = 100;
            }
            $smarty -> assign("Logo", "<center><img src=\"".$plik."\" width=\"".$arrImageparams[0]."\" height=\"".$arrImageparams[1]."\" /></center><br />");
        }

        $objAstral = $db -> Execute("SELECT `used` FROM `astral_machine` WHERE `owner`=".$tribe -> fields['id']);
        if ($objAstral -> fields['used']) {
            $intPercent = round($objAstral -> fields['used'] / 200, 2);
            $arrPercent = array(20.99, 40.99, 70.99, 85.99, 99.99, 100);
            $arrAstral = array(ASTRAL1, ASTRAL2, ASTRAL3, ASTRAL4, ASTRAL5, ASTRAL6);
            for ($i = 0; $i < 5; $i++) {
                if ($intPercent <= $arrPercent[$i]) {
                    break;
                }
            }
            $strAstral = ASTRAL.$arrAstral[$i]."<br />";
        } else {
            $strAstral = '';
        }
        $objAstral -> Close();

        $objname = $db->Execute('SELECT user FROM players WHERE id = \''.$tribe->fields['owner'].'\'');
        $owner = $objname->fields['user'];

        $smarty -> assign(array("Name" => $tribe -> fields['name'],
                                "OwnerID" => $tribe -> fields['owner'],
                                'Owner'	=>	$owner,
                                "Members" => $memnum,
                                "Tribeid" => $tribe -> fields['id'],
                                "Wins"=> $tribe -> fields['wygr'],
                                "Lost" => $tribe -> fields['przeg'],
                                "Pubmessage" => $tribe -> fields['public_msg'],
                                "Astral" => $strAstral,
                                "Yousee" => YOU_SEE,
                                "Leader2" => LEADER2,
                                "Memamount" => MEM_AMOUNT,
                                "Amembers" => A_MEMBERS,
                                "Winamount" => WIN_AMOUNT,
                                "Lostamount" => LOST_AMOUNT,
                                "Jointo" => JOIN_TO,
                                "Ajoin" => A_JOIN));
        if ($tribe -> fields['www']) {
            $smarty -> assign("WWW", CLAN_PAGE.": <a href=\"http://".$tribe -> fields['www']."\">".$tribe -> fields['www']."</a><br />");
        }

        if ($player -> clas == 'Złodziej' && $player -> tribe != $tribe -> fields['id']) {
            $objAstralcrime = $db -> Execute("SELECT `astralcrime` FROM `players` WHERE `id`=".$player -> id);
            if ($objAstralcrime -> fields['astralcrime'] == 'Y') {
                $smarty -> assign("Asteal", "<br /><br /><a href=\"tribes.php?view=view&id=".$_GET['id']."&amp;step=steal\">".A_STEAL."</a>");
            } else {
                $smarty -> assign("Asteal", '');
            }
            $objAstralcrime -> Close();
        } else {
            $smarty -> assign("Asteal", '');
        }

        $tribe -> Close();
    }

    /**
     * Steal astral components
     */
    if (isset($_GET['step']) && $_GET['step'] == 'steal') {
        if (!preg_match("/^[1-9][0-9]*$/", $_GET['id'])) {
            error(ERROR);
        }
        $objTribe = $db -> Execute("SELECT `id`, `owner` FROM `tribes` WHERE `id`=".$_GET['id']);
        if (!$objTribe -> fields['id']) {
            error(NO_CLAN." (<a href=\"tribes.php?view=view&id=".$_GET['id']."\">".BACK."</a>)");
        }
        if ($player -> clas != 'Złodziej' || $player -> clas == 'Lochy') {
            error(ERROR." (<a href=\"tribes.php?view=view&id=".$_GET['id']."\">".BACK."</a>)");
        }
        $objAstralcrime = $db -> Execute("SELECT `astralcrime` FROM `players` WHERE `id`=".$player -> id);
        if ($objAstralcrime -> fields['astralcrime'] == 'N') {
            error(NO_CRIME." (<a href=\"tribes.php?view=view&id=".$_GET['id']."\">".BACK."</a>)");
        }
        $objAstralcrime -> Close();
        if ($player -> hp <= 0) {
            error(YOU_DEAD." (<a href=\"tribes.php?view=view&id=".$_GET['id']."\">".BACK."</a>)");
        }
        if ($player -> tribe == $_GET['id']) {
            error(SAME_CLAN." (<a href=\"tribes.php?view=view&id=".$_GET['id']."\">".BACK."</a>)");
        }

        require_once('includes/astralsteal.php');
        astralsteal($_GET['id'], 'C', $objTribe -> fields['owner']);

        require_once('includes/checkastral.php');
        checkastral($objTribe -> fields['id']);

        $objTribe -> Close();
    }

    if (isset($_GET['step']) && $_GET['step'] == 'members') {
        if (!preg_match("/^[1-9][0-9]*$/", $_GET['tid'])) {
            error(ERROR);
        }
        $tribename = $db -> Execute("SELECT `name`, `owner` FROM `tribes` WHERE `id`=".$_GET['tid']);
        $mem = $db -> Execute("SELECT `id`, `user`, `tribe_rank` FROM `players` WHERE `tribe`=".$_GET['tid']);
        $arrlink = array();
        $i = 0;
        while (!$mem -> EOF) {
            if ($mem -> fields['id'] == $tribename -> fields['owner']) {
                $arrlink[$i] = "- <a href=\"view.php?view=".$mem -> fields['id']."\">".$mem -> fields['user']."</a> (".$mem -> fields['id'].") (".$mem -> fields['tribe_rank'].") (".LEADER.")<br />";
            } else {
                $arrlink[$i] = "- <a href=\"view.php?view=".$mem -> fields['id']."\">".$mem -> fields['user']."</a> (".$mem -> fields['id'].") (".$mem -> fields['tribe_rank'].")<br />";
            }
            $mem -> MoveNext();
            $i = $i + 1;
        }
        $mem -> Close();
        $smarty -> assign(array("Name" => $tribename -> fields['name'],
                                "Link" => $arrlink,
                                "Memberlist" => MEMBER_LIST));
        $tribename -> Close();
    }
}

/**
* Join to clan
*/
if (isset($_GET['join'])) {
    if (!preg_match("/^[1-9][0-9]*$/", $_GET['join'])) {
        error(ERROR);
    }
    $tribe = $db -> Execute("SELECT * FROM tribes WHERE id=".$_GET['join']);
    $test = $db -> Execute("SELECT gracz FROM tribe_oczek WHERE gracz=".$player -> id);
    if (!isset($_GET['change'])) {
        if ($player -> tribe) {
            error(YOU_IN_CLAN);
        }
        if ($test -> fields['gracz']) {
            $smarty -> assign(array("Tribeid" => $_GET['join'],
                "Playerid" => $test -> fields['gracz'],
                "Check" => 1,
                "Youwait" => YOU_WAIT,
                "Ayes" => YES,
                "Ano" => NO));
        } else {
            $strDate = $db -> DBDate($newdate);
            $db -> Execute("INSERT INTO `tribe_oczek` (`gracz`, `klan`) VALUES(".$player -> id.",".$tribe -> fields['id'].")");
            $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$tribe -> fields['owner'].",'".L_PLAYER."<b><a href=\"view.php?view=".$player -> id."\">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_WANT."', ".$strDate.")");
            error(YOU_SEND.$tribe -> fields['name'].".");
        }
    } else {
        if ($player -> tribe) {
            error(YOU_IN_CLAN);
        }
        $strDate = $db -> DBDate($newdate);
        $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$tribe -> fields['owner'].",'".L_PLAYER." <b><a href=view.php?view=".$player -> id.">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_WANT."', ".$strDate.")");
        $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$tribe -> fields['id']." AND `wait`=1");
        while (!$objPerm -> EOF) {
            $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$objPerm -> fields['player'].",'".L_PLAYER." <b><a href=view.php?view=".$player -> id.">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_WANT."', ".$strDate.")");
            $objPerm -> MoveNext();
        }
        $objPerm -> Close();
        $db -> Execute("UPDATE `tribe_oczek` SET `klan`=".$tribe -> fields['id']." WHERE `gracz`=".$player -> id);
        error(YOU_SEND.$tribe -> fields['name'].".");
    }
}

/**
* Make clan
*/
if (isset($_GET['view']) && $_GET['view'] == 'make') {
    $smarty -> assign(array("Clanname" => CLAN_NAME,
        "Amake" => A_MAKE));
    if ($player -> credits < 2500000) {
        error(NO_MONEY);
    }
    if ($player -> tribe) {
        error(YOU_IN_CLAN);
    }
    if ($player -> hp < 1) {
        error(YOU_DEAD);
    }
    if (isset($_GET['step']) && $_GET['step'] == 'make') {
        if (!$_POST['name']) {
            error(NO_NAME);
        }
        $db -> Execute("INSERT INTO tribes (name,owner) VALUES('".strip_tags($_POST['name'])."',".$player -> id.")");
        $db -> Execute("UPDATE players SET credits=credits-2500000 WHERE id=".$player -> id);
        $newt = $db -> Execute("SELECT id FROM tribes WHERE owner=".$player -> id);
        $db -> Execute("UPDATE players SET tribe=".$newt -> fields['id']." WHERE id=".$player -> id);
        $newt -> Close();
        error(YOU_MAKE.strip_tags($_POST['name'])."</i>.<br />");
    }
}

/**
* My clan menu
*/
if (isset($_GET['view']) && $_GET['view'] == 'my') {
    if (!$player -> tribe) {
        error(NOT_IN);
    }
    $mytribe = $db -> Execute("SELECT * FROM tribes WHERE id=".$player -> tribe);
    $perm = $db -> Execute("SELECT * FROM tribe_perm WHERE tribe=".$mytribe -> fields['id']." AND player=".$player -> id);
    $smarty -> assign(array("Name" => $mytribe -> fields['name'],
                             "Myclan" => MY_CLAN,
                             "Menu1" => MENU1,
                             "Menu2" => MENU2,
                             "Menu3" => MENU3,
                             "Menu4" => MENU4,
                             "Menu5" => MENU5,
                             "Menu6" => MENU6,
                             "Menu7" => MENU7,
                             "Menu8" => MENU8,
                             "Menu9" => MENU9,
                             "Menu10" => MENU10,
                             "Menu11" => MENU11));
    if (!isset($_GET['step'])) {
        $plik = 'images/tribes/'.$mytribe -> fields['logo'];
        if (is_file($plik)) {
            $smarty -> assign("Logo", "<center><img src=\"".$plik."\" height=\"100\"></center><br>");
        }
        $query = $db -> Execute("SELECT count(*) FROM `players` WHERE `tribe`=".$mytribe -> fields['id']);
        $memnum = $query -> fields['count(*)'];
        $query -> Close();
        $owner = $db -> Execute("SELECT `id`, `user` FROM `players` WHERE `id`=".$mytribe -> fields['owner']);
        if ($player -> id == $mytribe -> fields['owner'] || !$perm -> fields['info']) {
            $smarty -> assign(array("Gold" => $mytribe -> fields['credits'],
                                    "Mithril" => $mytribe -> fields['platinum'],
                                    "Soldiers" => $mytribe -> fields['zolnierze'],
                                    "Forts" => $mytribe -> fields['forty']));
        } else {
            $smarty -> assign(array("Gold" => UNKNOWN,
                                    "Mithril" => UNKNOWN,
                                    "Soldiers" => UNKNOWN,
                                    "Forts" => UNKNOWN));
        }
        $objAstral = $db -> Execute("SELECT `aviable`, `used` FROM `astral_machine` WHERE `owner`=".$mytribe -> fields['id']);
        if ($objAstral -> fields['aviable'] == 'Y') {
            $strAstral = "<a href=\"tribes.php?view=my&amp;step=astral\">".A_MACHINE."</a>";
        } else {
            $strAstral = A_MACHINE;
        }
        if ($objAstral -> fields['used']) {
            $intPercent = round($objAstral -> fields['used'] / 200, 2);
        } else {
            $intPercent = 0;
        }
        $objAstral -> Close();
        $smarty -> assign(array("Members" => $memnum,
                                "Owner" => $owner -> fields['user'],
                                "Ownerid" => $owner -> fields['id'],
                                "Wins" => $mytribe -> fields['wygr'],
                                "Lost" => $mytribe -> fields['przeg'],
                                "Privmessage" => $mytribe -> fields['private_msg'],
                                "Amachine" => $strAstral,
                                "Percent" => $intPercent,
                                "Apercent" => A_PERCENT,
                                "Clanname" => CLAN_NAME,
                                "Welcome" => WELCOME,
                                "Memamount" => MEM_AMOUNT,
                                "Leader" => LEADER,
                                "Goldcoins" => GOLD_COINS,
                                "Mithcoins" => MITH_COINS,
                                "Winamount" => WIN_AMOUNT,
                                "Lostamount" => LOST_AMOUNT,
                                "Tsoldiers" => T_SOLDIERS,
                                "Tforts" => T_FORTS));
        if ($mytribe -> fields['www']) {
            $smarty -> assign("WWW", "<li>".CLAN_PAGE.": <a href=\"http://".$mytribe -> fields['www']."\" target=\"_blank\">".$mytribe -> fields['www']."</a></li>");
        }
    }

    /**
     * Build astral machine
     */
    if (isset($_GET['step']) && $_GET['step'] == 'astral') {
        $objAstral = $db -> Execute("SELECT `used`, `directed`, `aviable` FROM `astral_machine` WHERE `owner`=".$mytribe -> fields['id']);
        if ($objAstral -> fields['aviable'] != 'Y') {
            error(NO_COMPONENTS);
        }
        $objTest = $db -> Execute("SELECT `value` FROM `settings` WHERE `setting`='tribe'");
        if ($objTest -> fields['value']) {
            error(ASTRAL_BUILD);
        }
        $smarty -> assign(array("Aused" => A_USED,
                                "Aenergy" => A_ENERGY,
                                "Atoday" => A_TODAY,
                                "Amax" => A_MAX,
                                "Adirect" => A_DIRECT,
                                "Aform" => A_FORM,
                                "Message" => '',
                                "Eused" => $objAstral -> fields['used'],
                                "Edirected" => $objAstral -> fields['directed']));

        /**
         * Add energy to astral machine
         */
        if (isset($_GET['step2']) && $_GET['step2'] == 'add') {
            if (!preg_match("/^[1-9][0-9]*$/", $_POST['amount'])) {
                error(ERROR);
            }
            if ($_POST['amount'] > $player -> energy) {
                error(NO_ENERGY);
            }
            $intDirected = $objAstral -> fields['directed'] + $_POST['amount'];
            if ($intDirected > 1000) {
                error(TOO_MUCH);
            }
            $intUsed = $objAstral -> fields['used'] + $intDirected;
            if ($intUsed > 20000) {
                error(TOO_MUCH2);
            }
            $db -> Execute("UPDATE `players` SET `energy`=`energy`-".$_POST['amount']." WHERE `id`=".$player -> id);
            $db -> Execute("UPDATE `astral_machine` SET `directed`=".$intDirected." WHERE `owner`=".$mytribe -> fields['id']);
            $smarty -> assign("Message", YOU_ADD.$_POST['amount']." ".A_ENERGY." <a href=\"tribes.php?view=my&amp;step=astral\">".A_REFRESH."</a>");
        }

        $objAstral -> Close();
    }

    /**
    * Donations to clan
    */
    if (isset($_GET['step']) && $_GET['step'] == 'donate') {
        $smarty -> assign(array("Doninfo" => DON_INFO,
                                "Adonate" => A_DONATE,
                                "Goldcoins" => GOLD_COINS2,
                                "Mithcoins" => MITH_COINS2,
                                "Toclan" => TO_CLAN));
        if (isset($_GET['step2']) && $_GET['step2'] == 'donate') {
            if ($_POST['type'] == 'credits') {
                $dot = GOLD_COINS;
            }
            if ($_POST['type'] == 'platinum') {
                $dot = MITH_COINS;
            }
            integercheck($_POST['amount']);
            if (!preg_match("/^[1-9][0-9]*$/", $_POST['amount'])) {
                error(ERROR);
            }
            if ($_POST['type'] != 'credits' && $_POST['type'] != 'platinum') {
                error(ERROR);
            }
            if ($_POST['amount'] > $player -> $_POST['type']) {
                $smarty -> assign("Message", NO_AMOUNT.$dot.".");
            } else {
                $db -> Execute("UPDATE players SET ".$_POST['type']."=".$_POST['type']."-".$_POST['amount']." WHERE id=".$player -> id);
                $db -> Execute("UPDATE tribes set ".$_POST['type']."=".$_POST['type']."+".$_POST['amount']." WHERE id=".$mytribe -> fields['id']);
                $smarty -> assign("Message", YOU_GIVE.$_POST['amount']." ".$dot."</b>.");
                $strDate = $db -> DBDate($newdate);
                $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".L_PLAYER." <b><a href=view.php?view=".$player -> id.">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_ADD.$_POST['amount']." ".$dot.".', ".$strDate.")");
                $objPerm = $db -> Execute("SELECT player FROM tribe_perm WHERE tribe=".$mytribe -> fields['id']." AND loan=1");
                while (!$objPerm -> EOF) {
                    $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".L_PLAYER." <b><a href=view.php?view=".$player -> id.">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_ADD.$_POST['amount']." ".$dot.".', ".$strDate.")");
                    $objPerm -> MoveNext();
                }
                $objPerm -> Close();
            }
        }
    }

    /**
    * Clan herbs
    */
    if (isset($_GET['step']) && $_GET['step'] == 'zielnik') {
        $arrName = array(HERB1, HERB2, HERB3, HERB4, HERB5, HERB6, HERB7, HERB8);
        $arrSqlname = array('illani', 'illanias', 'nutari', 'dynallca', 'ilani_seeds', 'illanias_seeds', 'nutari_seeds', 'dynallca_seeds');
        $arrAmount = array($mytribe -> fields['illani'],
                           $mytribe -> fields['illanias'],
                           $mytribe -> fields['nutari'],
                           $mytribe -> fields['dynallca'],
                           $mytribe -> fields['ilani_seeds'],
                           $mytribe -> fields['illanias_seeds'],
                           $mytribe -> fields['nutari_seeds'],
                           $mytribe -> fields['dynallca_seeds']);
        if (!isset($_GET['step2']) && !isset($_GET['step3']) && !isset($_GET['daj']) && !isset($_GET['step4'])) {
            $arrTable = array();
            $i = 0;
            foreach ($arrName as $strName) {
                if ($player -> id == $mytribe -> fields['owner'] || $perm -> fields['herbs']) {
                    $arrTable[$i] = "<td width=\"100\"><a href=\"tribes.php?view=my&amp;step=zielnik&amp;daj=".$arrSqlname[$i]."\"><b><u>".$strName."</u></b></a></td>";
                } else {
                    $arrTable[$i] = "<td width=\"100\"><b><u>".$strName."</u></b></td>";
                }
                $i++;
            }
            $smarty -> assign(array("Tamount" => $arrAmount,
                                    "Ttable" => $arrTable,
                                    "Herbsinfo" => HERBS_INFO,
                                    "Whatyou" => WHAT_YOU,
                                    "Agiveto" => A_GIVE_TO));
        }
        /**
         * Give herbs to player
         */
        if (isset($_GET['daj']) && $_GET['daj']) {
            if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['herbs']) {
                error(ERROR);
            }
            if (!in_array($_GET['daj'], $arrSqlname)) {
                error(ERROR);
            }
            $intKey = array_search($_GET['daj'], $arrSqlname);
            $min1 = $arrName[$intKey];
            $smarty -> assign(array("Giveplayer" => GIVE_PLAYER,
                                    "Agive" => A_GIVE,
                                    "Tamount" => T_AMOUNT,
                                    "Hamount2" => H_AMOUNT2,
                                    "Tamount2" => $arrAmount[$intKey],
                                    "Nameherb" => $min1,
                                    "Itemid" => $_GET['daj']));
            if (isset($_GET['step4']) && $_GET['step4'] == 'add') {
                if (!preg_match("/^[1-9][0-9]*$/", $_POST['ilosc'])) {
                    error(ERROR);
                }
                if (!preg_match("/^[1-9][0-9]*$/", $_POST['did'])) {
                    error(ERROR);
                }
                $dtrib = $db -> Execute("SELECT `tribe` FROM `players` WHERE `id`=".$_POST['did']);
                if ($dtrib -> fields['tribe'] != $mytribe -> fields['id']) {
                    error(NOT_IN_CLAN);
                }
                $give = $_GET['daj'];
                if ($mytribe -> fields[$give] < $_POST['ilosc']) {
                    error(NO_AMOUNT.$min1."!");
                }
                $kop = $db -> Execute("SELECT * FROM `herbs` WHERE `gracz`=".$_POST['did']);
                if (!$kop -> fields['id']) {
                    $db -> Execute("INSERT INTO `herbs` (`gracz`, `".$_GET['daj']."`) VALUES(".$_POST['did'].",".$_POST['ilosc'].")");
                } else {
                    $db -> Execute("UPDATE `herbs` SET `".$_GET['daj']."`=`".$_GET['daj']."`+".$_POST['ilosc']." WHERE `gracz`=".$_POST['did']);
                }
                $db -> Execute("UPDATE `tribes` SET `".$_GET['daj']."`=`".$_GET['daj']."`-".$_POST['ilosc']." WHERE `id`=".$mytribe -> fields['id']);

                // Get name of the person which receives herbs.
                $objGetName = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$_POST['did'].';');
                $strReceiversName = $objGetName -> fields['user'];
                $objGetName -> Close();
                unset($objGetName);

                $smarty -> assign("Message", YOU_SEND1.'<b><a href="view.php?view='.$_POST['did'].'">'.$strReceiversName.'</a></b>'.YOU_SEND2.$_POST['did']." ".$_POST['ilosc']." ".$min1.'.');

                /**
                 * Send information about give herbs to player
                 */
                $strDate = $db -> DBDate($newdate);

                $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".YOU_SEND1.'<b><a href="view.php?view='.$_POST['did'].'">'.$strReceiversName.'</a></b>'.YOU_SEND2.'<b>'.$_POST['did']."</b> ".$_POST['ilosc']." ".$min1.".', ".$strDate.")");
                $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `herbs`=1");
                while (!$objPerm -> EOF) {
                    $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".YOU_SEND1.'<b><a href="view.php?view='.$_POST['did'].'">'.$strReceiversName.'</a></b>'.YOU_SEND2.'<b>'.$_POST['did']."</b> ".$_POST['ilosc']." ".$min1.".', ".$strDate.")");
                    $objPerm -> MoveNext();
                }
                $objPerm -> Close();
                $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$_POST['did'].", '".YOU_GET.$_POST['ilosc']." ".$min1.".', ".$strDate.")");
            }
        }
        if (isset($_GET['step2']) && $_GET['step2'] == 'daj') {
            $smarty -> assign(array("Addherb" => ADD_HERB,
                                    "Aadd" => A_ADD,
                                    "Herb" => HERB,
                                    "Hamount" => H_AMOUNT,
                                    "Herbname" => $arrName,
                                    "Sqlname" => $arrSqlname));
            if (isset($_GET['step3']) && $_GET['step3'] == 'add') {
                $gr = $db -> Execute("SELECT * FROM `herbs` WHERE `gracz`=".$player -> id);
                if (!in_array($_POST['mineral'], $arrSqlname)) {
                    error(ERROR);
                }
                $intKey = array_search($_POST['mineral'], $arrSqlname);
                $nazwa = $arrName[$intKey];
                $min = $arrSqlname[$intKey];
                if ($_POST['ilosc'] > $gr -> fields[$min]) {
                    error(NO_AMOUNT2.$nazwa.".");
                }
                if ($_POST['ilosc'] <= 0 || !preg_match("/^[1-9][0-9]*$/", $_POST['ilosc'])) {
                    error(ERROR);
                }
                $db -> Execute("UPDATE `tribes` SET `".$min."`=`".$min."`+".$_POST['ilosc']." WHERE `id`=".$mytribe -> fields['id']);
                $db -> Execute("UPDATE `herbs` SET `".$min."`=`".$min."`-".$_POST['ilosc']." WHERE `gracz`=".$player -> id);
                $smarty -> assign("Message", YOU_ADD.$_POST['ilosc']." ".$nazwa.TO_CLAN);
                /**
                 * Send information about give herbs to tribe
                 */
                $strDate = $db -> DBDate($newdate);
                $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".L_PLAYER." <b><a href=view.php?view=".$player -> id.">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_ADD.$_POST['ilosc']." ".$nazwa.".', ".$strDate.")");
                $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `herbs`=1");
                while (!$objPerm -> EOF) {
                    $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".L_PLAYER." <b><a href=view.php?view=".$player -> id.">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_ADD.$_POST['ilosc']." ".$nazwa.".', ".$strDate.")");
                    $objPerm -> MoveNext();
                }
                $objPerm -> Close();
            }
        }
    }

    /**
    * Clan minerals
    */
    if (isset($_GET['step']) && $_GET['step'] == 'skarbiec') {
        $arrSqlname = array('copperore', 'zincore', 'tinore', 'ironore', 'copper', 'bronze', 'brass', 'iron', 'steel', 'coal', 'adamantium', 'meteor', 'crystal', 'pine', 'hazel', 'yew', 'elm');
        $arrName = array(MINE1, MINE2, MINE3, MINE4, MINE5, MINE6, MINE7, MINE8, MINE9, MINE10, MINE11, MINE12, MINE13, MINE14, MINE15, MINE16, MINE17);
        $arrAmount = array($mytribe -> fields['copperore'],
                           $mytribe -> fields['zincore'],
                           $mytribe -> fields['tinore'],
                           $mytribe -> fields['ironore'],
                           $mytribe -> fields['copper'],
                           $mytribe -> fields['bronze'],
                           $mytribe -> fields['brass'],
                           $mytribe -> fields['iron'],
                           $mytribe -> fields['steel'],
                           $mytribe -> fields['coal'],
                           $mytribe -> fields['adamantium'],
                           $mytribe -> fields['meteor'],
                           $mytribe -> fields['crystal'],
                           $mytribe -> fields['pine'],
                           $mytribe -> fields['hazel'],
                           $mytribe -> fields['yew'],
                           $mytribe -> fields['elm']);
        if (!isset($_GET['step2']) && !isset($_GET['step3']) && !isset($_GET['daj']) && !isset($_GET['step4'])) {
            $arrTable = array();
            $i = 0;
            foreach ($arrName as $strName) {
                if ($player -> id == $mytribe -> fields['owner'] || $perm -> fields['bank']) {
                    $arrTable[$i] = "<tr><td><a href=\"tribes.php?view=my&amp;step=skarbiec&amp;daj=".$arrSqlname[$i]."\">".$strName."</a></td><td width=\"75%\" align=\"center\"><b>".$arrAmount[$i]."</b></td></tr>";
                } else {
                    $arrTable[$i] = "<tr><td>".$strName."</td><td width=\"75%\" align=\"center\"><b>".$arrAmount[$i]."</b></td></tr>";
                }
                $i++;
            }
            $smarty -> assign(array("Mininfo" => MIN_INFO,
                                    "Whatyou" => WHAT_YOU,
                                    "Agiveto" => A_GIVE_TO,
                                    "Ttable" => $arrTable));
        }
        if (isset($_GET['daj']) && $_GET['daj']) {
            if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['bank']) {
                error(ERROR);
            }
            if (!in_array($_GET['daj'], $arrSqlname)) {
                error(ERROR);
            }
            $intKey = array_search($_GET['daj'], $arrSqlname);
            $smarty -> assign(array("Itemid" => $_GET['daj'],
                                    "Giveplayer" => GIVE_PLAYER,
                                    "Agive" => A_GIVE,
                                    "Tamount" => T_AMOUNT,
                                    "Mamount2" => M_AMOUNT2,
                                    "Namemin" => $arrName[$intKey],
                                    "Tamount2" => $arrAmount[$intKey]));
            if (isset($_GET['step4']) && $_GET['step4'] == 'add') {
                if (!preg_match("/^[1-9][0-9]*$/", $_POST['ilosc'])) {
                    error(ERROR);
                }
                $daj = $_GET['daj'];
                if (!preg_match("/^[1-9][0-9]*$/", $_POST['did'])) {
                    error(ERROR);
                }
                $dtrib = $db -> Execute("SELECT `tribe` FROM `players` WHERE `id`=".$_POST['did']);
                if ($dtrib -> fields['tribe'] != $mytribe -> fields['id']) {
                    error(NOT_IN_CLAN);
                }
                $dtrib -> Close();
                $give = $_GET['daj'];
                if ($mytribe -> fields[$give] < $_POST['ilosc']) {
                    error(NO_AMOUNT.$_POST['min']."!");
                }
                $kop = $db -> Execute("SELECT `owner` FROM `minerals` WHERE `owner`=".$_POST['did']);
                if (!$kop -> fields['owner']) {
                    $db -> Execute("INSERT INTO `minerals` (`owner`, `".$daj."`) VALUES(".$_POST['did'].",".$_POST['ilosc'].")");
                } else {
                    $db -> Execute("UPDATE `minerals` SET `".$daj."`=`".$daj."`+".$_POST['ilosc']." WHERE `owner`=".$_POST['did']);
                }
                $kop -> Close();
                $db -> Execute("UPDATE `tribes` SET `".$daj."`=`".$daj."`-".$_POST['ilosc']." WHERE `id`=".$mytribe -> fields['id']);

                // Get name of the person which receives minerals.
                $objGetName = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$_POST['did'].';');
                $strReceiversName = $objGetName -> fields['user'];
                $objGetName -> Close();
                unset($objGetName);

                $smarty -> assign("Message", CLAN_SEND1.'<b><a href="view.php?view='.$_POST['did'].'">'.$strReceiversName.'</a></b>'.CLAN_SEND2.$_POST['did']." ".$_POST['ilosc']." ".$_POST['min']);
                /**
                 * Send information about give minerals to player
                 */
                $strDate = $db -> DBDate($newdate);
                $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".CLAN_SEND1.'<b><a href="view.php?view='.$_POST['did'].'">'.$strReceiversName.'</a></b>'.CLAN_SEND2.'<b>'.$_POST['did']."</b> ".$_POST['ilosc']." ".$_POST['min'].".', ".$strDate.")");
                $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `bank`=1");
                while (!$objPerm -> EOF) {
                    $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".CLAN_SEND1.'<b><a href="view.php?view='.$_POST['did'].'">'.$strReceiversName.'</a></b>'.CLAN_SEND2.'<b>'.$_POST['did']."</b> ".$_POST['ilosc']." ".$_POST['min'].".', ".$strDate.")");
                    $objPerm -> MoveNext();
                }
                $objPerm -> Close();
                $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$_POST['did'].", '".YOU_GET.$_POST['ilosc']." ".$_POST['min'].".', ".$strDate.")");
            }
        }
        if (isset($_GET['step2']) && $_GET['step2'] == 'daj') {
            $smarty -> assign(array("Addmin" => ADD_MIN,
                                    "Aadd" => A_ADD,
                                    "Mineral" => MINERAL,
                                    "Mamount" => M_AMOUNT,
                                    "Minsql" => $arrSqlname,
                                    "Minname" => $arrName));
            if (isset($_GET['step3']) && $_GET['step3'] == 'add') {
                if (!in_array($_POST['mineral'], $arrSqlname)) {
                    error(ERROR);
                }
                $gr = $db -> Execute("SELECT ".$_POST['mineral']." FROM `minerals` WHERE `owner`=".$player -> id);
                $intKey = array_search($_POST['mineral'], $arrSqlname);
                if ($_POST['ilosc'] > $gr -> fields[$arrSqlname[$intKey]]) {
                    error(NO_AMOUNT2.$arrName[$intKey].".");
                }
                if ($_POST['ilosc'] <= 0 || !preg_match("/^[1-9][0-9]*$/", $_POST['ilosc'])) {
                    error(ERROR);
                }
                $db -> Execute("UPDATE `tribes` SET `".$arrSqlname[$intKey]."`=`".$arrSqlname[$intKey]."`+".$_POST['ilosc']." WHERE `id`=".$mytribe -> fields['id']);
                $db -> Execute("UPDATE `minerals` SET `".$arrSqlname[$intKey]."`=`".$arrSqlname[$intKey]."`-".$_POST['ilosc']." WHERE `owner`=".$player -> id);
                $smarty -> assign("Message", YOU_ADD.$_POST['ilosc'].PIECES.$arrName[$intKey].TO_CLAN);
                /**
                 * Send information about give minerals to tribe
                 */
                $strDate = $db -> DBDate($newdate);
                $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".L_PLAYER." <b><a href=\"view.php?view=".$player -> id."\">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_ADD.$_POST['ilosc'].PIECES.$arrName[$intKey].".', ".$strDate.")");
                $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `bank`=1");
                while (!$objPerm -> EOF) {
                    $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".L_PLAYER." <b><a href=\"view.php?view=".$player -> id."\">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.HE_ADD.$_POST['ilosc'].PIECES.$arrName[$intKey].".', ".$strDate.")");
                    $objPerm -> MoveNext();
                }
                $objPerm -> Close();
            }
        }
    }

    /**
    * Members list
    */
    if (isset($_GET['step']) && $_GET['step'] == 'members') {
        $mem = $db -> Execute("SELECT id, user, tribe_rank FROM players WHERE tribe=".$mytribe -> fields['id']);
        $arrlink = array();
        $i = 0;
        while (!$mem -> EOF) {
            if ($mem -> fields['id'] == $mytribe -> fields['owner']) {
                $arrlink[$i] = "- <a href=\"view.php?view=".$mem -> fields['id']."\">".$mem -> fields['user']."</a> (".$mem -> fields['id'].") (".$mem -> fields['tribe_rank'].") (".LEADER.")<br />";
            } else {
                $arrlink[$i] = "- <a href=\"view.php?view=".$mem -> fields['id']."\">".$mem -> fields['user']."</a> (".$mem -> fields['id'].") (".$mem -> fields['tribe_rank'].")<br />";
            }
            $mem -> MoveNext();
            $i = $i + 1;
        }
        $mem -> Close();
        $smarty -> assign("Link", $arrlink);
    }

    /**
    * Leave clan
    */
    if (isset($_GET['step']) && $_GET['step'] == 'quit') {
        $smarty -> assign(array("Ayes" => YES,
            "Ano" => NO));
        if ($mytribe -> fields['owner'] == $player -> id) {
            $smarty -> assign(array("Owner" => 1,
                "Qleader" => Q_LEADER));
            if (isset($_GET['dalej'])) {
                $db -> Execute("UPDATE players SET tribe=0 WHERE tribe=".$mytribe -> fields['id']);
                $db -> Execute("DELETE FROM tribes WHERE id=".$mytribe -> fields['id']);
                $db -> Execute("DELETE FROM tribe_zbroj WHERE klan=".$mytribe -> fields['id']);
                $db -> Execute("DELETE FROM tribe_mag WHERE klan=".$mytribe -> fields['id']);
                $db -> Execute("DELETE FROM tribe_oczek WHERE klan=".$mytribe -> fields['id']);
                $db -> Execute("DELETE FROM tribe_perm WHERE tribe=".$mytribe -> fields['id']);
                $db -> Execute("UPDATE players SET tribe_rank='' WHERE id=".$player -> id);
                error(L_LEADER);
            }
        } else {
            $smarty -> assign(array("Owner" => 0,
                "Qmember" => Q_MEMBER));
            if (isset($_GET['dalej'])) {
                $db -> Execute("UPDATE players SET tribe=0, tribe_rank='' WHERE id=".$player -> id);
                $db -> Execute("DELETE FROM tribe_perm WHERE tribe=".$mytribe -> fields['id']." AND player=".$player -> id);
                $strDate = $db -> DBDate($newdate);
                $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".L_PLAYER." <b><a href=\"view.php?view=".$player -> id."\">".$player -> user.L_ID.'<b>'.$player -> id.'</b>'.M_LEAVE."', ".$strDate.")");
                error(L_MEMBER);
            }
        }
    }

    /**
    * Clan control menu
    */
    if (isset($_GET['step']) && $_GET['step'] == 'owner') {
        $smarty -> assign(array("Panelinfo" => PANEL_INFO,
                                "Aperm" => A_PERM,
                                "Arank" => A_RANK,
                                "Adesc" => A_DESC,
                                "Awaiting" => A_WAITING,
                                "Akick" => A_KICK,
                                "Aarmy" => A_ARMY,
                                "Aattack2" => A_ATTACK2,
                                "Aloan" => A_LOAN,
                                "Amisc" => A_MISC,
                                "Amail2" => A_MAIL2));
        $test = array($perm -> fields['messages'],$perm -> fields['wait'],$perm -> fields['kick'],$perm -> fields['army'],$perm -> fields['attack'],$perm -> fields['loan'],$perm -> fields['armory'],$perm -> fields['warehouse'],$perm -> fields['bank'],$perm -> fields['herbs'], $perm -> fields['mail'], $perm -> fields['ranks']);
        $intTest = 0;
        for ($i = 0; $i < 12; $i++) {
            if ($test[$i]) {
                $intTest = 1;
                break;
            }
        }
        if ($player -> id == $mytribe -> fields['owner'] || $intTest) {

            /**
            * Set ranks for members
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'rank') {
                if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['ranks']) {
                    error(NO_PERM2);
                }
                if (!isset($_GET['step3'])) {
                    $smarty -> assign(array("Ranksinfo" => RANKS_INFO,
                        "Aaddranks" => A_ADD_RANKS));
                    $test = $db -> Execute("SELECT id FROM tribe_rank WHERE tribe_id=".$mytribe -> fields['id']);
                    if ($test -> fields['id']) {
                        $smarty -> assign("Menu", "<li><a href=\"tribes.php?view=my&amp;step=owner&amp;step2=rank&amp;step3=get\">".GIVE_RANK."</a></li>");
                    } else {
                        $smarty -> assign("Menu", '');
                    }
                    $test -> Close();
                }
                if (isset($_GET['step3']) && $_GET['step3'] == 'set') {
                    $ranks = $db -> Execute("select * from tribe_rank where tribe_id=".$mytribe -> fields['id']);
                    if (!$ranks -> fields['id']) {
                        $smarty -> assign(array("Empty" => 1,
                            "Noranks2" => NO_RANKS2,
                            "Amake" => A_MAKE));
                    } else {
                        $RankArr = array();
                        $TagPrefixArr = array();
                        for ($i = 1; $i <= 10; $i++) {
                            $RankArr[$i] = $ranks->fields['rank'.$i];
                            $TagPrefixArr[$i] = $ranks->fields['tag_prefix_'.$i];
                        }

                        $TagSufix = $ranks->fields['tag_sufix'];

                        $smarty -> assign(array("Empty" => 0,
                            "Editranks" => EDIT_RANKS,
                            "Asave" => A_SAVE,
                            "Rank" => RANK,
                            'RankArr' => $RankArr,
                            'TagPrefixArr' => $TagPrefixArr,
                            'TagSufix' => $TagSufix));
                    }


                    $TagLenSumMax = 9;

                    if (isset($_GET['step4']) && $_GET['step4'] == 'add') {
                        $arrRank = array();
                        $arrTagPrefix = array();
                        for ($i=1; $i<=10; $i++) {
                            $number = "rank".$i;
                            $tag = "tag_prefix_".$i;
                            $j = $i - 1;
                            if (strlen($_POST[$tag]) + strlen($_POST['tag_sufix']) > $TagLenSumMax) {
                                error(TOO_LONG_TAG.' <a href="tribes.php?view=my&amp;step=owner&amp;step2=rank&amp;step3=set">'.BACK.'</a>');
                            }

                            $_POST[$number] = strip_tags($_POST[$number]);
                            $_POST[$tag] = strip_tags($_POST[$tag]);

                            $arrRank[$j] = $db -> qstr($_POST[$number], get_magic_quotes_gpc());
                            $arrTagPrefix[$j] = $db->qstr($_POST[$tag], get_magic_quotes_gpc());
                        }

                        $TagSufix = strip_tags($_POST['tag_sufix']);
                        $TagSufix = $db->qstr($TagSufix, get_magic_quotes_gpc());

                        $query = "INSERT INTO tribe_rank (tribe_id, rank1, rank2, rank3, rank4, rank5, rank6, rank7, rank8, rank9, rank10, tag_sufix, tag_prefix_1, tag_prefix_2, tag_prefix_3, tag_prefix_4, tag_prefix_5, tag_prefix_6, tag_prefix_7, tag_prefix_8, tag_prefix_9, tag_prefix_10) VALUES(".$mytribe -> fields['id'].", ".$arrRank[0].", ".$arrRank[1].", ".$arrRank[2].", ".$arrRank[3].", ".$arrRank[4].", ".$arrRank[5].", ".$arrRank[6].", ".$arrRank[7].", ".$arrRank[8].", ".$arrRank[9].", ".$TagSufix.", ".$arrTagPrefix[0].", ".$arrTagPrefix[1].", ".$arrTagPrefix[2].", ".$arrTagPrefix[3].", ".$arrTagPrefix[4].", ".$arrTagPrefix[5].", ".$arrTagPrefix[6].", ".$arrTagPrefix[7].", ".$arrTagPrefix[8].", ".$arrTagPrefix[9].")";

                        $db->Execute($query);

                        error(RANK_CREATED.'. <a href="tribes.php?view=my&amp;step=owner&amp;step2=rank&amp;step3=set">'.BACK_TO.'</a>');
                    }
                    if (isset($_GET['step4']) && $_GET['step4'] == 'edit') {
                        $arrRank = array();
                        $arrTagPrefix = array();
                        for ($i=1; $i<=10; $i++) {
                            $number = "rank".$i;
                            $tag = "tag_prefix_".$i;
                            $j = $i - 1;
                            if (strlen($_POST[$tag]) + strlen($_POST['tag_sufix']) > $TagLenSumMax) {
                                error(TOO_LONG_TAG.' <a href="tribes.php?view=my&amp;step=owner&amp;step2=rank&amp;step3=set">'.BACK.'</a>');
                            }

                            $_POST[$number] = strip_tags($_POST[$number]);
                            $_POST[$tag] = strip_tags($_POST[$tag]);

                            $arrRank[$j] = $db -> qstr($_POST[$number], get_magic_quotes_gpc());
                            $arrTagPrefix[$j] = $db->qstr($_POST[$tag], get_magic_quotes_gpc());
                        }

                        $TagSufix = strip_tags($_POST['tag_sufix']);
                        $TagSufix = $db->qstr($TagSufix, get_magic_quotes_gpc());

                        $db -> Execute("UPDATE tribe_rank SET rank1=".$arrRank[0].", rank2=".$arrRank[1].", rank3=".$arrRank[2].", rank4=".$arrRank[3].", rank5=".$arrRank[4].", rank6=".$arrRank[5].", rank7=".$arrRank[6].", rank8=".$arrRank[7].", rank9=".$arrRank[8].", rank10=".$arrRank[9].", tag_sufix=".$TagSufix.", tag_prefix_1=".$arrTagPrefix[0].", tag_prefix_2=".$arrTagPrefix[1].", tag_prefix_3=".$arrTagPrefix[2].", tag_prefix_4=".$arrTagPrefix[3].", tag_prefix_5=".$arrTagPrefix[4].", tag_prefix_6=".$arrTagPrefix[5].", tag_prefix_7=".$arrTagPrefix[6].", tag_prefix_8=".$arrTagPrefix[7].", tag_prefix_9=".$arrTagPrefix[8].", tag_prefix_10=".$arrTagPrefix[9]." WHERE tribe_id=".$mytribe -> fields['id']);

                        error(RANK_CHANGED.'. <a href="tribes.php?view=my&amp;step=owner&amp;step2=rank&amp;step3=set">'.BACK_TO.'</a>');
                    }
                }
                if (isset($_GET['step3']) && $_GET['step3'] == 'get') {
                    $test = $db -> Execute("SELECT id FROM tribe_rank WHERE tribe_id=".$mytribe -> fields['id']);
                    if (!$test -> fields['id']) {
                        error(NO_RANKS);
                    }
                    $test -> Close();
                    $rank = $db -> Execute("select rank1, rank2, rank3, rank4, rank5, rank6, rank7, rank8, rank9, rank10 from tribe_rank where tribe_id=".$mytribe -> fields['id']);
                    $name = array('rank1','rank2','rank3','rank4','rank5','rank6','rank7','rank8','rank9','rank10');
                    $arrname = array();
                    $j = 0;
                    for ($i=0;$i<10;$i++) {
                        $number = $name[$i];
                        if ($rank -> fields[$number]) {
                            $arrname[$j] = $rank -> fields[$number];
                            $j = $j + 1;
                        }
                    }
                    $rank -> Close();
                    $smarty -> assign(array("Rank" => $arrname,
                        "Aset" => A_SET,
                        "Setrank" => SET_RANK,
                        "Rankplayer" => RANK_PLAYER));
                    if (isset($_GET['step4']) && $_GET['step4'] == 'add') {
                        $_POST['rank'] = strip_tags($_POST['rank']);
                        if (!preg_match("/^[1-9][0-9]*$/", $_POST['rid'])) {
                            error(ERROR);
                        }
                        $test = $db -> Execute("SELECT tribe FROM players WHERE id=".$_POST['rid']);
                        if ($test -> fields['tribe'] != $mytribe -> fields['id']) {
                            error(NOT_IN_CLAN);
                        }
                        $test -> Close();
                        $strRank = $db -> qstr($_POST['rank'], get_magic_quotes_gpc());
                        $db -> Execute("UPDATE players SET tribe_rank=".$strRank." WHERE id=".$_POST['rid']);
                        $smarty -> assign("Message", YOU_GIVE.$_POST['rid'].T_RANK.$_POST['rank']."<br />");
                    }
                }
            }

            /**
            * Add members permission in clan - only clan leader
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'permissions') {
                if ($player -> id != $mytribe -> fields['owner']) {
                    error(ONLY_LEADER);
                }
                if (!isset($_GET['step3'])) {
                    $objMembers = $db -> Execute("SELECT user, id FROM players WHERE tribe=".$mytribe -> fields['id']." AND id!=".$mytribe -> fields['owner']);
                    $arrMembers = array();
                    $arrMemid = array();
                    $i = 0;
                    while (!$objMembers -> EOF) {
                        $arrMembers[$i] = $objMembers -> fields['user'];
                        $arrMemid[$i] = $objMembers -> fields['id'];
                        $i = $i + 1;
                        $objMembers -> MoveNext();
                    }
                    $objMembers -> Close();
                    if (!isset($_GET['next'])) {
                        $_GET['next'] = '';
                    }
                    $smarty -> assign(array("Perminfo" => PERM_INFO,
                                            "Tperm1" => T_PERM1,
                                            "Tperm2" => T_PERM2,
                                            "Tperm3" => T_PERM3,
                                            "Tperm4" => T_PERM4,
                                            "Tperm5" => T_PERM5,
                                            "Tperm6" => T_PERM6,
                                            "Tperm7" => T_PERM7,
                                            "Tperm8" => T_PERM8,
                                            "Tperm9" => T_PERM9,
                                            "Tperm10" => T_PERM10,
                                            "Tperm11" => T_PERM11,
                                            "Tperm12" => T_PERM12,
                                            "Tperm13" => T_PERM13,
                                            "Tperm14" => T_PERM14,
                                            "Tperm15" => T_PERM15,
                                            "Asave" => A_SAVE,
                                            "Anext" => A_NEXT,
                                            "Next" => $_GET['next'],
                                            "Members" => $arrMembers,
                                            "Memid" => $arrMemid,
                                            "Yes" => YES,
                                            "No" => NO));
                    if (isset($_GET['next']) && $_GET['next'] == 'add') {
                        if (!preg_match("/^[1-9][0-9]*$/", $_POST['memid'])) {
                            error(ERROR);
                        }
                        $objTest = $db -> Execute("SELECT * FROM tribe_perm WHERE player=".$_POST['memid']);
                        $arrTest = array($objTest -> fields['messages'],
                                         $objTest -> fields['wait'],
                                         $objTest -> fields['kick'],
                                         $objTest -> fields['army'],
                                         $objTest -> fields['attack'],
                                         $objTest -> fields['loan'],
                                         $objTest -> fields['armory'],
                                         $objTest -> fields['warehouse'],
                                         $objTest -> fields['bank'],
                                         $objTest -> fields['herbs'],
                                         $objTest -> fields['forum'],
                                         $objTest -> fields['ranks'],
                                         $objTest -> fields['mail'],
                                         $objTest -> fields['info'],
                                         $objTest -> fields['astralvault']);
                        $objTest -> Close();
                        $objName = $db -> Execute("SELECT user FROM players WHERE id=".$_POST['memid']);
                        $arrSelected = array();
                        $i = 0;
                        foreach ($arrTest as $intTest) {
                            if ($intTest) {
                                $arrSelected[$i] = 'selected';
                            } else {
                                $arrSelected[$i] = '';
                            }
                            $i ++;
                        }
                        $smarty -> assign(array("Memid2" => $_POST['memid'],
                            "Tselected" => $arrSelected,
                            "Tname" => $objName -> fields['user'],
                            "Tuser" => T_USER));
                        $objName -> Close();
                    }
                }
                if (isset($_GET['step3'])) {
                    $test = array($_POST['messages'],
                                  $_POST['wait'],
                                  $_POST['kick'],
                                  $_POST['army'],
                                  $_POST['attack'],
                                  $_POST['loan'],
                                  $_POST['armory'],
                                  $_POST['warehouse'],
                                  $_POST['bank'],
                                  $_POST['herbs'],
                                  $_POST['forum'],
                                  $_POST['ranks'],
                                  $_POST['mail'],
                                  $_POST['info'],
                                  $_POST['astralvault']);
                    for ($i=0; $i<15; $i++) {
                        if (!preg_match("/^[0-1]*$/", $test[$i])) {
                            error(ERROR);
                        }
                        if (!preg_match("/^[1-9][0-9]*$/", $_POST['memid'])) {
                            error(ERROR);
                        }
                        $ttribe = $db -> Execute("SELECT tribe FROM players WHERE id=".$_POST['memid']);
                        if ($ttribe -> fields['tribe'] != $mytribe -> fields['id']) {
                            error(NOT_IN_CLAN." <a href=\"tribes.php?view=my&amp;step=owner\">".BACK_TO."</a>");
                        }
                    }
                }
                if (isset($_GET['step3']) && $_GET['step3'] == 'add') {
                    $objTest = $db -> Execute("SELECT id FROM tribe_perm WHERE tribe=".$mytribe -> fields['id']." AND player=".$_POST['memid']);
                    if (!$objTest -> fields['id']) {
                        $db -> Execute("INSERT INTO tribe_perm (tribe, player, messages, wait, kick, army, attack, loan, armory, warehouse, bank, herbs, forum, ranks, mail, info, astralvault) VALUES(".$mytribe -> fields['id'].", ".$_POST['memid'].", ".$_POST['messages'].", ".$_POST['wait'].", ".$_POST['kick'].", ".$_POST['army'].", ".$_POST['attack'].", ".$_POST['loan'].", ".$_POST['armory'].", ".$_POST['warehouse'].", ".$_POST['bank'].", ".$_POST['herbs'].", ".$_POST['forum'].", ".$_POST['ranks'].", ".$_POST['mail'].", ".$_POST['info'].", ".$_POST['astralvault'].")");
                    } else {
                        $db -> Execute("UPDATE tribe_perm SET messages=".$_POST['messages'].", wait=".$_POST['wait'].", kick=".$_POST['kick'].", army=".$_POST['army'].", attack=".$_POST['attack'].", loan=".$_POST['loan'].", armory=".$_POST['armory'].", warehouse=".$_POST['warehouse'].", bank=".$_POST['bank'].", herbs=".$_POST['herbs'].", forum=".$_POST['forum'].", ranks=".$_POST['ranks'].", mail=".$_POST['mail'].", info=".$_POST['info'].", astralvault=".$_POST['astralvault']." WHERE id=".$objTest -> fields['id']);
                    }
                    $objTest -> Close();
                    $smarty -> assign("Message", YOU_SET." <a href=\"tribes.php?view=my&amp;step=owner\">".BACK_TO."</a>");
                }
            }

            /**
            * Send mail to all members
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'mail') {
                if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['mail']) {
                    error(NO_PERM2);
                }
                if (isset($_GET['step3']) && $_GET['step3'] == 'send') {
                    $strBody = strip_tags($_POST['body']);
                    $strTitle = strip_tags($_POST['mtitle']);
                    if (empty($strBody) || empty($strTitle)) {
                        error(EMPTY_FIELDS);
                    }
                    require_once('includes/bbcode.php');
                    $strBody = bbcodetohtml($strBody);
                    $strTitle = bbcodetohtml($strTitle);
                    $strTitle = T_CLAN.$strTitle;
                    $objOwner = $db -> Execute("SELECT `id` FROM `players` WHERE `tribe`=".$mytribe -> fields['id']." AND `id`!=".$player -> id);
                    $strDate = $db -> DBDate($newdate);
                    while (!$objOwner -> EOF) {
                        $db -> Execute("INSERT INTO `mail` (`sender`, `senderid`, `owner`, `subject`, `body`, `date`) VALUES('".$player -> user."','".$player -> id."',".$objOwner -> fields['id'].",'".$strTitle."','".$strBody."', ".$strDate.")");
                        $objOwner -> MoveNext();
                    }
                    $objOwner -> Close();
                    $smarty -> assign("Message", YOU_SEND);
                } else {
                    $smarty -> assign(array("Tbody" => T_BODY,
                                            "Asend" => A_SEND,
                                            "Ttitle" => T_TITLE));
                }
            }

            /**
            * Buy army and barricades to clan
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'wojsko') {
                if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['army']) {
                    error(NO_PERM);
                }
                $smarty -> assign(array("Armyinfo" => ARMY_INFO,
                                        "Howmanys" => HOW_MANY_S,
                                        "Howmanyf" => HOW_MANY_F,
                                        "Abuy" => A_BUY));
                if (isset($_GET['action']) && $_GET['action'] == 'kup') {
                    if (!preg_match("/^[0-9]*$/", $_POST['zolnierze'])) {
                        error(ERROR);
                    }
                    if (!preg_match("/^[0-9]*$/", $_POST['forty'])) {
                        error(ERROR);
                    }
                    if ($_POST["zolnierze"] == 0 && $_POST["forty"] == 0) {
                        error(EMPTY_FIELDS);
                    }
                    $cenaz = ($_POST["zolnierze"] * 1000);
                    $cenaf = ($_POST["forty"] * 1000);
                    $suma = $cenaz + $cenaf;
                    if ($suma > $mytribe -> fields['credits']) {
                        error(NO_MONEY);
                    }
                    $message = '';
                    $strLog = '';
                    if ($_POST["zolnierze"] > 0) {
                        $db -> Execute("UPDATE `tribes` SET `zolnierze`=`zolnierze`+".$_POST['zolnierze']." WHERE `id`=".$mytribe -> fields['id']);
                        $message =  $message.YOU_BUY.$_POST['zolnierze'].SOLDIERS.$cenaz.FOR_A;
                        $strLog =  $strLog.SOMEONE_BUY.$_POST['zolnierze'].SOLDIERS.$cenaz.FOR_A;
                    }
                    if ($_POST["forty"] > 0) {
                        $db -> Execute("UPDATE `tribes` SET `forty`=`forty`+".$_POST['forty']." WHERE `id`=".$mytribe -> fields['id']);
                        $message = $message.YOU_BUY.$_POST['forty'].BARRICADES.$cenaf.FOR_A;
                        $strLog = $strLog.SOMEONE_BUY.$_POST['forty'].BARRICADES.$cenaf.FOR_A;
                    }
                    $db -> Execute("UPDATE `tribes` SET `credits`=`credits`-".$suma." WHERE `id`=".$mytribe -> fields['id']);
                    $message = $message.ALL_COST.$suma.FOR_A;
                    $strLog = $strLog.ALL_COST2.$suma.FOR_A;
                    $smarty -> assign("Message", $message);
                    /**
                     * Send informations about buy army
                     */
                    $strDate = $db -> DBDate($newdate);
                    $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".$strLog."', ".$strDate.")");
                    $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `army`=1");
                    while (!$objPerm -> EOF) {
                        $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".$strLog."', ".$strDate.")");
                        $objPerm -> MoveNext();
                    }
                    $objPerm -> Close();
                }
            }

            /**
            * Waiting for join to clan
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'nowy') {
                if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['wait']) {
                    error(NO_PERM2);
                }
                $smarty -> assign("New", 0);
                if (!isset($_GET['odrzuc']) && !isset($_GET['dodaj'])) {
                    $smarty -> assign("New", 1);
                    $czeka = $db -> Execute("SELECT * FROM tribe_oczek WHERE klan=".$mytribe -> fields['id']);
                    $arrlink = array();
                    $i = 0;
                    while (!$czeka -> EOF) {
                        $arrlink[$i] = "<tr><td><a href=\"view.php?view=".$czeka -> fields['gracz']."\">".$czeka -> fields['gracz']."</a></td><td><a href=\"tribes.php?view=my&amp;step=owner&amp;step2=nowy&dodaj=".$czeka -> fields['id']."\">".YES."</a></td><td><a href=\"tribes.php?view=my&amp;step=owner&amp;step2=nowy&odrzuc=".$czeka -> fields['id']."\">".YES."</a></td></tr>";
                        $czeka -> MoveNext();
                        $i = $i + 1;
                    }
                    $czeka -> Close();
                    $smarty -> assign(array("Link" => $arrlink,
                        "Waitlist" => WAIT_LIST,
                        "Tid" => T_ID,
                        "Taccept" => T_ACCEPT,
                        "Tdrop" => T_DROP));
                }
                if (isset($_GET['odrzuc']) && strictInt($_GET['odrzuc'])) {
                    $del = $db -> Execute('SELECT * FROM `tribe_oczek` WHERE `id`='.$_GET['odrzuc'].' AND `klan`='.$mytribe -> fields['id']);
                    if (!$del -> fields['id']) {
                        error(ERROR);
                    }
                    $smarty -> assign("Message", YOU_DROP.$del -> fields['gracz'].".");
                    $strDate = $db -> DBDate($newdate);
                    $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$del -> fields['gracz'].",'".L_TRIBE.'<b><a href="tribes.php?view=view&amp;id='.$mytribe -> fields['id'].'">'.$mytribe -> fields['name'].'</a></b>'.L_DROP."', ".$strDate.")");
                    $db -> Execute("DELETE FROM tribe_oczek WHERE id=".$del -> fields['id']);
                    $del -> Close();
                    $objPerm = $db -> Execute("SELECT player FROM tribe_perm WHERE tribe=".$mytribe -> fields['id']." AND wait=1");
                    while (!$objPerm -> EOF) {
                        $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".YOU_DROP.'<b>'.$del -> fields['gracz']."</b>', ".$strDate.")");
                        $objPerm -> MoveNext();
                    }
                    $objPerm -> Close();
                }
                if (isset($_GET['dodaj']) && strictInt($_GET['dodaj'])) {
                    $dod = $db -> Execute('SELECT * FROM `tribe_oczek` WHERE `id`='.$_GET['dodaj'].' AND `klan`='.$mytribe -> fields['id']);
                    $smarty -> assign("Message", YOU_ACCEPT.$dod -> fields['gracz'].".");
                    $strDate = $db -> DBDate($newdate);
                    $db -> Execute("INSERT INTO log (owner, log, czas) VALUES(".$dod -> fields['gracz'].",'".L_TRIBE.'<b><a href="tribes.php?view=my">'.$mytribe -> fields['name'].'</a></b>'.L_ACCEPT."', ".$strDate.")");
                    $db -> Execute("UPDATE players SET tribe=".$dod -> fields['klan']." WHERE id=".$dod -> fields['gracz']);
                    $db -> Execute("DELETE FROM tribe_oczek WHERE id=".$dod -> fields['id']);
                    $dod -> Close();
                    $objPerm = $db -> Execute("SELECT player FROM tribe_perm WHERE tribe=".$mytribe -> fields['id']." AND wait=1");
                    while (!$objPerm -> EOF) {
                        $db -> Execute("INSERT INTO log (owner,log, czas) VALUES(".$objPerm -> fields['player'].", '".YOU_ACCEPT.'<b>'.$dod -> fields['gracz']."</b>', ".$strDate.")");
                        $objPerm -> MoveNext();
                    }
                    $objPerm -> Close();
                }
            }

            /**
            * Clan's fight
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'walka') {
                require_once('includes/tribefight.php');
            }

            /**
            * Messages about clan, logo and www site
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'messages') {
                if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['messages']) {
                    error(NO_PERM2);
                }
                require_once('includes/bbcode.php');
                $mytribe -> fields['public_msg'] = htmltobbcode($mytribe -> fields['public_msg']);
                $mytribe -> fields['private_msg'] = htmltobbcode($mytribe -> fields['private_msg']);
                $smarty -> assign(array("Pubmessage" => $mytribe -> fields['public_msg'],
                                        "Privmessage" => $mytribe -> fields['private_msg'],
                                        "WWW" => $mytribe -> fields['www'],
                                        "Clandesc" => CLAN_DESC,
                                        "Msgtomem" => MSG_TO_MEM,
                                        "Aset" => A_SET,
                                        "Asend" => A_SEND,
                                        "Adelete" => A_DELETE,
                                        "Achange" => A_CHANGE,
                                        "Clansite" => CLAN_SITE,
                                        "Logoinfo" => LOGO_INFO,
                                        "Logoname" => LOGO_NAME));
                $avatar = $db -> Execute("SELECT logo FROM tribes WHERE id=".$mytribe -> fields['id']);
                $logocheck = (empty($avatar)) ? '' : $avatar -> fields['logo'];
                $plik = 'images/tribes/'.$avatar -> fields['logo'];
                if (is_file($plik)) {
                    $smarty -> assign("Logo1", "<center><img src=\"".$plik."\" heigth=\"100\" width=\"100\" /></center>");
                }
                if (is_file($plik)) {
                    $smarty -> assign(array("Logo" => $avatar -> fields['logo'],
                            "Change" => "Y"));
                } else {
                    $smarty -> assign("Change", '');
                }
                if (isset($_GET['action']) && $_GET['action'] == 'www') {
                    $_POST['www'] = str_replace("'", "", strip_tags($_POST['www']));
                    $strWWW = $db -> qstr($_POST['www'], get_magic_quotes_gpc());
                    $db -> Execute("UPDATE tribes SET www=".$strWWW." WHERE id=".$mytribe -> fields['id']);
                    $smarty -> assign("Message", WWW_SET." <a href=\"http://".$_POST['www']."\" target=\"_blank\">".$_POST['www']."</a>. <a href=\"tribes.php?view=my&amp;step=owner&amp;step2=messages\">".A_REFRESH."</a><br />");
                }
                if (isset($_GET['step4']) && $_GET['step4'] == 'usun') {
                    global $newdate;
                    if ($_POST['av'] != $logocheck || $logocheck == '') {
                        $db -> Execute("Insert into `mail` (`sender`,`senderid`,`owner`,`subject`,`body`,`date`) VALUES ('".($player -> user)."', ".($player -> id).", 1, 'wlasnie probowalem sie wlamac wpisujac ".($_SERVER['REQUEST_URI'])."', ".($db -> DBDate($newdate)).")");
                        error(ERROR);
                    }
                    $plik = 'images/tribes/'.$_POST['av'];
                    if (is_file($plik)) {
                        unlink($plik);
                        $db -> Execute("UPDATE tribes SET logo='' where id=".$mytribe -> fields['id']) or error("nie mogÄ skasowaÄ");
                        $smarty -> assign("Message", LOGO_DEL." <a href=\"tribes.php?view=my&amp;step=owner&amp;step2=messages\">".A_REFRESH."</a><br />");
                    } else {
                        error(NO_FILE);
                    }
                }
                if (isset($_GET['step4']) && $_GET['step4'] == 'dodaj') {
                    $plik = $_FILES['plik']['tmp_name'];
                    $nazwa = $_FILES['plik']['name'];
                    $typ = $_FILES['plik']['type'];
                    if ($typ != 'image/pjpeg' && $typ != 'image/jpeg' && $typ != 'image/gif'  && $typ != 'image/png') {
                        error(BAD_TYPE);
                    }
                    if ($typ == 'image/pjpeg' || $typ == 'image/jpeg') {
                        $liczba = rand(1, 1000000);
                        $newname = md5($liczba).'.jpg';
                        $miejsce = 'images/tribes/'.$newname;
                    }
                    if ($typ == 'image/gif') {
                        $liczba = rand(1, 1000000);
                        $newname = md5($liczba).'.gif';
                        $miejsce = 'images/tribes/'.$newname;
                    }
                    if ($typ == 'image/png') {
                        $liczba = rand(1, 1000000);
                        $newname = md5($liczba).'.png';
                        $miejsce = 'images/tribes/'.$newname;
                    }
                    if (is_uploaded_file($plik)) {
                        if (!move_uploaded_file($plik, $miejsce)) {
                            error(NO_COPY);
                        }
                    } else {
                        error(ERROR);
                    }
                    $db -> Execute("UPDATE tribes SET logo='".$newname."' where id=".$mytribe -> fields['id']);
                    $smarty -> assign("Message", LOGO_LOAD." <a href=\"tribes.php?view=my&amp;step=owner&amp;step2=messages\">".A_REFRESH."</a><br />");
                }
                /**
                 * Set new description and message
                 */
                if (isset($_GET['action']) && $_GET['action'] == 'edit') {
                    $strPublicmsg =  bbcodetohtml($_POST['public_msg']);
                    $strPrivatemsg =  bbcodetohtml($_POST['private_msg']);
                    $strPrivatemsg = $db -> qstr($strPrivatemsg, get_magic_quotes_gpc());
                    $strPublicmsg = $db -> qstr($strPublicmsg, get_magic_quotes_gpc());
                    $db -> Execute("UPDATE `tribes` SET `private_msg`=".$strPrivatemsg.",  `public_msg`=".$strPublicmsg." WHERE `id`=".$mytribe -> fields['id']);
                    $smarty -> assign(array("Message" => MSG_CHANGED,
                                            "Pubmessage" => $_POST['public_msg'],
                                            "Privmessage" => $_POST['private_msg']));
                }
            }

            /**
            * Drop members from clan
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'kick') {
                if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['kick']) {
                    error(NO_PERM2);
                }
                $smarty -> assign(array("Kickid" => KICK_ID,
                                        "Fromclan" => FROM_CLAN,
                                        "Akick" => A_KICK2));
                if (isset($_GET['action']) && $_GET['action'] == 'kick') {
                    if (!isset($_POST['id'])) {
                        error(NO_ID);
                    }
                    if (!preg_match("/^[1-9][0-9]*$/", $_POST['id'])) {
                        error(ERROR);
                    }
                    $objTribe = $db -> Execute("SELECT `tribe` FROM `players` WHERE `id`=".$_POST['id']);
                    if ($objTribe -> fields['tribe'] != $mytribe -> fields['id']) {
                        error(NOT_IN_CLAN);
                    }
                    $objTribe -> Close();
                    if ($_POST['id'] != $mytribe -> fields['owner']) {
                        $strDate = $db -> DBDate($newdate);
                        $db -> Execute("UPDATE `players` SET `tribe`=0, `tribe_rank`='' WHERE `id`=".$_POST['id']." AND `tribe`=".$mytribe -> fields['id']);
                        $db -> Execute("DELETE FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `player`=".$_POST['id']);
                        /**
                         * Send info about kick
                         */
                        // Get name of the person which is being kicked.
                        $objGetName = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$_POST['id'].';');
                        $strReceiversName = $objGetName -> fields['user'];
                        $objGetName -> Close();

                        $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$_POST['id'].",'".YOU_DROP.'<b><a href="tribes.php?view=view&amp;id='.$mytribe -> fields['id'].'">'.$mytribe -> fields['name']."</a></b>.', ".$strDate.")");
                        $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".CLAN_KICK1.'<b><a href="view.php?view='.$_POST['id'].'">'.$strReceiversName.'</a></b>'. CLAN_KICK2.'<b>'.$_POST['id'].'</b>'.HAS_BEEN.$mytribe -> fields['name'].".', ".$strDate.")");
                        $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `kick`=1");
                        while (!$objPerm -> EOF) {
                            $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".CLAN_KICK1.'<b><a href="view.php?view='.$_POST['did'].'">'.$strReceiversName.'</a></b>'. CLAN_KICK2.'<b>'.$_POST['id'].'</b>'.HAS_BEEN.$mytribe -> fields['name'].".', ".$strDate.")");
                            $objPerm -> MoveNext();
                        }
                        $objPerm -> Close();
                        $smarty -> assign("Message", D_ID.$_POST['id'].NOT_IS);
                    } else {
                        $smarty -> assign("Message", IS_LEADER);
                    }
                }
            }

            /**
            * Give money from clan to members
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'loan') {
                if ($player -> id != $mytribe -> fields['owner'] && !$perm -> fields['loan']) {
                    error(NO_PERM2);
                }
                $smarty -> assign(array("Aloan2" => A_LOAN2,
                                        "Playerid" => PLAYER_ID,
                                        "Goldcoins" => GOLD_COINS,
                                        "Mithcoins" => MITHRIL_COINS));
                if (isset($_GET['action']) && $_GET['action'] == 'loan') {
                    integercheck($_POST['amount']);
                    if (!preg_match("/^[1-9][0-9]*$/", $_POST['amount'])) {
                        error(ERROR);
                    }
                    if ($_POST['currency'] != 'credits' && $_POST['currency'] != 'platinum') {
                        error(ERROR);
                    }
                    if ($_POST['currency'] == 'credits') {
                        $poz = GOLD_COINS;
                    }
                    if ($_POST['currency'] == 'platinum') {
                        $poz = MITHRIL_COINS;
                    }
                    $rec = $db -> Execute("SELECT tribe FROM players WHERE id=".$_POST['id']);
                    if ($rec -> fields['tribe'] != $mytribe -> fields['id']) {
                        $smarty -> assign("Message", NOT_IN_CLAN);
                    } else {
                        if (!$_POST['amount'] || !$_POST['id']) {
                            $smarty -> assign("Message", EMPTY_FIELDS);
                        } else {
                            if ($_POST['amount'] > $mytribe -> fields[$_POST['currency']] || !preg_match("/^[1-9][0-9]*$/", $_POST['amount'])) {
                                $smarty -> assign("Message", NO_AMOUNT.$poz.".");
                            } else {
                                $db -> Execute("UPDATE `players` SET `".$_POST['currency']."`=`".$_POST['currency']."`+".$_POST['amount']." WHERE `id`=".$_POST['id']);
                                $db -> Execute("UPDATE `tribes` SET `".$_POST['currency']."`=`".$_POST['currency']."`-".$_POST['amount']." WHERE `id`=".$mytribe -> fields['id']);
                                $strDate = $db -> DBDate($newdate);
                                // Get name of the person which is receiving money.
                                $objGetName = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$_POST['id'].';');
                                $strReceiversName = $objGetName -> fields['user'];
                                $objGetName -> Close();
                                $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$_POST['id'].",'".SEND_YOU.$_POST['amount']." ".$poz.".', ".$strDate.")");
                                /**
                                 * Send informations about donation to player
                                 */
                                $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$mytribe -> fields['owner'].", '".CLAN_GIVE1.'<b><a href="view.php?view='.$_POST['id'].'">'.$strReceiversName.'</a></b>'.CLAN_GIVE2.'<b>'.$_POST['id']."</b> ".$_POST['amount']." ".$poz.".', ".$strDate.")");
                                $objPerm = $db -> Execute("SELECT `player` FROM `tribe_perm` WHERE `tribe`=".$mytribe -> fields['id']." AND `loan`=1");
                                while (!$objPerm -> EOF) {
                                    $db -> Execute("INSERT INTO `log` (`owner`,`log`, `czas`) VALUES(".$objPerm -> fields['player'].", '".CLAN_GIVE1.'<b><a href="view.php?view='.$_POST['id'].'">'.$strReceiversName.'</a></b>'.CLAN_GIVE2.'<b>'.$_POST['id']."</b> ".$_POST['amount']." ".$poz.".', ".$strDate.")");
                                    $objPerm -> MoveNext();
                                }
                                $objPerm -> Close();
                                $smarty -> assign("Message", YOU_GIVE.$_POST['id']." ".$_POST['amount']." ".$poz.".");
                            }
                        }
                    }
                }
            }

            /**
            * Special options in clan
            */
            if (isset($_GET['step2']) && $_GET['step2'] == 'te') {
                $smarty -> assign(array("Miscinfo" => MISC_INFO,
                    "Afreeheal" => A_FREE_HEAL,
                    "Youbuy" => YOU_BUY,
                    "Aback" => A_BACK));
                if (isset($_GET['step3']) && $_GET['step3'] == 'hospass') {
                    if ($mytribe -> fields['platinum'] < 100) {
                        error(NO_MITH."<br /><a href=\"tribes.php?view=my&amp;step=owner\">...".A_BACK."</a>");
                    } else {
                        $db -> Execute("UPDATE tribes SET platinum=platinum-100 WHERE id=".$mytribe -> fields['id']);
                        $db -> Execute("UPDATE tribes SET hospass='Y' WHERE id=".$mytribe -> fields['id']);
                        $smarty -> assign("Hospass1", 1);
                    }
                }
                if ($mytribe -> fields['hospass'] == "Y") {
                    error(CLAN_HAVE."<br /><a href=\"tribes.php?view=my&amp;step=owner\">...".A_BACK."</a>");
                }
            }
        } else {
            error(NOT_LEADER);
        }
    }
}

/**
* Initialization of variables
*/
if (!isset($_GET['join'])) {
    $_GET['join'] = '';
}
if (!isset($_GET['step'])) {
    $_GET['step'] = '';
}
if (!isset($_GET['step2'])) {
    $_GET['step2'] = '';
}
if (!isset($_GET['step3'])) {
    $_GET['step3'] = '';
}
if (!isset($_GET['daj'])) {
    $_GET['daj'] = '';
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
if (!isset($_GET['view'])) {
    $_GET['view'] = '';
}

/**
* Assign variables to template and display page
*/
$smarty -> assign(array("View" => $_GET['view'],
    "Join" => $_GET['join'],
    "Step" => $_GET['step'],
    "Step2" => $_GET['step2'],
    "Step3" => $_GET['step3'],
    "Give" => $_GET['daj'],
    "Action" => $_GET['action']));
$smarty -> display('tribes.tpl');

require_once("includes/foot.php");
