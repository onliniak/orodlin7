<?php
/**
 *   File functions:
 *   Astral market - add, buy astral components from other players
 *
 *   @name                 : amarket.php                            
 *   @copyright            : (C) 2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @author               : eyescream <tduda@users.sourceforge.net>
 *   @version              : 1.3
 *   @since                : 27.11.2006
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
// $Id$

$title = "Astralny rynek";
require_once("includes/head.php");

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/amarket.php");

if ($player -> location != 'Altara' && $player -> location != 'Ardulith') 
{
    error (ERROR);
}

/**
* Assign variables to template
*/
$smarty -> assign(array("Message" => '', 
    "Previous" => '', 
    "Next" => ''));

/**
* Main menu
*/
if (!isset($_GET['view']) && !isset($_GET['buy']) && !isset($_GET['wyc']))
{
    $smarty -> assign(array("Minfo" => M_INFO,
        "Aview" => A_VIEW,
        "Asearch" => A_SEARCH,
        "Aadd" => A_ADD,
        "Adelete" => A_DELETE,
        "Alist" => A_LIST,
        "Aback2" => A_BACK2));
}

/**
* Search astral components on market
*/
if (isset ($_GET['view']) && $_GET['view'] == 'szukaj') 
{
    $smarty -> assign(array("Sinfo" => S_INFO,
        "Sinfo2" => S_INFO2,
        "Astral" => ASTRAL,
        "Asearch" => A_SEARCH));
}

$arrNames = array(MAP1, MAP2, MAP3, MAP4, MAP5, MAP6, MAP7, PLAN1, PLAN2, PLAN3, PLAN4, PLAN5, RECIPE1, RECIPE2, RECIPE3, RECIPE4, RECIPE5, FORMULA1, FORMULA2, FORMULA3, FORMULA4, FORMULA5);
$arrNames2 = array(COMP1, COMP2, COMP3, COMP4, COMP5, COMP6, COMP7, CONST1, CONST2, CONST3, CONST4, CONST5, POTION1, POTION2, POTION3, POTION4, POTION5, JEWELLERY1, JEWELLERY2, JEWELLERY3, JEWELLERY4, JEWELLERY5);

/**
* View oferts on market
*/
if (isset ($_GET['view']) && $_GET['view'] == 'market') 
{
    $arrNames = array_merge($arrNames, $arrNames2);
    if (empty($_POST['szukany'])) 
    {
        $msel = $db -> Execute("SELECT count(*) FROM `amarket`");
        $strSearch = '';
    } 
        else 
    {
        $_POST['szukany'] = strip_tags($_POST['szukany']);
        $intKey = array_search($_POST['szukany'], $arrNames);
        if ($intKey == NULL)
        {
            $strSearch = 'A';
        }
        if ($intKey < 7)
        {
            $strSearch = "M".$intKey;
        }
        if ($intKey > 6 && $intKey < 12)
        {
            $intNumber = $intKey - 7;
            $strSearch = "P".$intNumber;
        }
        if ($intKey > 11 && $intKey < 17)
        {
            $intNumber = $intKey - 12;
            $strSearch = "R".$intNumber;
        }
        if ($intKey > 16 && $intKey < 24)
        {
            $intNumber = $intKey - 17;
            $strSearch = "Y".$intNumber;
        }
        if ($intKey > 23 && $intKey < 29)
        {
            $intNumber = $intKey - 24;
            $strSearch = "C".$intNumber;
        }
        if ($intKey > 28 && $intKey < 34)
        {
            $intNumber = $intKey - 29;
            $strSearch = "O".$intNumber;
        }
        if ($intKey > 33 && $intKey < 39)
        {
            $intNumber = $intKey - 34;
            $strSearch = "T".$intNumber;
        }
        if ($intKey > 38 && $intKey < 44)
        {
            $intNumber = $intKey - 39;
            $strSearch = "J".$intNumber;
        }        
        $msel = $db -> Execute("SELECT count(*) FROM `amarket` WHERE `type`='".$strSearch."'");
    }
    $oferty = $msel -> fields['count(*)'];
    $msel -> Close();
    if (!isset($_GET['limit'])) 
    {
        $_GET['limit'] = 0;
    }
    if (!isset($_GET['lista'])) 
    {
        $_GET['lista'] = 0;
    }
    if ($oferty == 0) 
    {
        error(NO_OFERTS);
    }
    if ($_GET['limit'] < $oferty) 
    {
        if (empty($_POST['szukany'])) 
        {
            $pm = $db -> SelectLimit("SELECT * FROM `amarket` ORDER BY ".$_GET['lista']." DESC", 30, $_GET['limit']);
        } 
            else 
        {
            $pm = $db -> SelectLimit("SELECT * FROM `amarket` WHERE `type`='".$strSearch."' ORDER BY ".$_GET['lista']." DESC", 30, $_GET['limit']);
        }
        $arrname = array();
        $arramount = array();
        $arrcost = array();
        $arrseller = array();
        $arraction = array();
        $arruser = array();
        $arrNumber = array();
        $i = 0;
        while (!$pm -> EOF) 
        {
            if (preg_match("/^M[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("M", "", $pm -> fields['type']);
                $intNumber = $intKey;
                $arrNumber[$i] = $pm -> fields['number'] + 1;
            }
            if (preg_match("/^P[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("P", "", $pm -> fields['type']);
                $intNumber = $intKey + 7;
                $arrNumber[$i] = $pm -> fields['number'] + 1;
            }
            if (preg_match("/^R[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("R", "", $pm -> fields['type']);
                $intNumber = $intKey + 12;
                $arrNumber[$i] = $pm -> fields['number'] + 1;
            }
            if (preg_match("/^Y[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("Y", "", $pm -> fields['type']);
                $intNumber = $intKey + 17;
                $arrNumber[$i] = '-';
            }
            if (preg_match("/^C[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("C", "", $pm -> fields['type']);
                $intNumber = $intKey + 24;
                $arrNumber[$i] = '-';
            }
            if (preg_match("/^O[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("O", "", $pm -> fields['type']);
                $intNumber = $intKey + 29;
                $arrNumber[$i] = '-';
            }
            if (preg_match("/^T[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("T", "", $pm -> fields['type']);
                $intNumber = $intKey + 34;
                $arrNumber[$i] = '-';
            }
            if (preg_match("/^J[0-9]/", $pm -> fields['type']))
            {
                $intKey = str_replace("J", "", $pm -> fields['type']);
                $intNumber = $intKey + 39;
                $arrNumber[$i] = '-';
            }
            $arrname[$i] = $arrNames[$intNumber];
            $arramount[$i] = $pm -> fields['amount'];
            $arrcost[$i] = $pm -> fields['cost'];
            $arrseller[$i] = $pm -> fields['seller'];
            $seller = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$pm -> fields['seller']);
            $arruser[$i] = $seller -> fields['user'];
            $seller -> Close();
            if ($player -> id == $pm -> fields['seller']) 
            {
                $arraction[$i] = "<td>- <a href=\"amarket.php?wyc=".$pm -> fields['id']."\">".A_DELETE."</a>";
            } 
                else 
            {
                $arraction[$i] = "<td>- <a href=\"amarket.php?buy=".$pm -> fields['id']."\">".A_BUY."</a>";
                if ($player -> clas == 'Złodziej')
                {
                    $arraction[$i] = $arraction[$i]."<br />- <a href=\"amarket.php?steal=".$pm -> fields['id']."\">".A_STEAL."</a>";
                }
            }
            $arraction[$i] = $arraction[$i]."</td></tr>";
            $pm -> MoveNext();
            $i = $i + 1;
        }
        $pm -> Close();
        $smarty -> assign(array("Name" => $arrname, 
                                "Amount" => $arramount, 
                                "Cost" => $arrcost, 
                                "Seller" => $arrseller, 
                                "Action" => $arraction, 
                                "User" => $arruser,
                                "Number" => $arrNumber,
                                "Tastral" => ASTRAL,
                                "Tamount" => T_AMOUNT,
                                "Tcost" => T_COST,
                                "Tseller" => T_SELLER,
                                "Tnumber" => T_NUMBER,
                                "Toptions" => T_OPTIONS,
                                "Viewinfo" => VIEW_INFO));
        if (!isset($_POST['szukany']))
        {
            $_POST['szukany'] = '';
        }
        if ($_GET['limit'] >= 30) 
        {
            $lim = $_GET['limit'] - 30;
            $smarty -> assign ("Previous", "<form method=\"post\" action=\"amarket.php?view=market&limit=".$lim."&lista=".$_GET['lista']."\"><input type=\"hidden\" name=\"szukany\" value=\"".$_POST['szukany']."\"><input type=\"submit\" value=\"".A_PREVIOUS."\"></form> ");
        }
        $_GET['limit'] = $_GET['limit'] + 30;
        if ($oferty > 30 && $_GET['limit'] < $oferty) 
        {
            $smarty -> assign ("Next", " <form method=\"post\" action=\"amarket.php?view=market&limit=".$_GET['limit']."&lista=".$_GET['lista']."\"><input type=\"hidden\" name=\"szukany\" value=\"".$_POST['szukany']."\"><input type=\"submit\" value=\"".A_NEXT."\"></form>");
        }
    }
}

/**
* Add ofert on market
*/
if (isset ($_GET['view']) && $_GET['view'] == 'add') 
{
    $smarty -> assign(array("Addinfo" => ADD_INFO,
                            "Astral" => ASTRAL,
                            "Herbname" => $arrNames,
                            "Aname" => $arrNames2,
                            "Hamount" => H_AMOUNT,
                            "Hcost" => H_COST,
                            "Aadd" => A_ADD,
                            "Tadd" => T_ADD,
                            "Tadd2" => T_ADD2,
                            "Anumber" => A_NUMBER,
                            "Addofert" => 0));
    if (isset ($_GET['step']) && ($_GET['step'] == 'piece' || $_GET['step'] == 'component')) 
    {
        if (!preg_match("/^[1-9][0-9]*$/", $_POST['amount']) || !preg_match("/^[0-9]*$/", $_POST['name']) || !preg_match("/^[1-9][0-9]*$/", $_POST['number']) || !preg_match("/^[1-9][0-9]*$/", $_POST['cost']))
        {
            error(ERROR);
        }
        if ($_GET['step'] == 'piece')
        {
            if ($_POST['name'] < 7)
            {
                $strName = 'M';
            }
            if ($_POST['name'] > 6 && $_POST['name'] < 12)
            {
                $strName = 'P';
            }
            if ($_POST['name'] > 11 && $_POST['name'] < 17)
            {
                $strName = 'R';
            }
            if ($_POST['name'] > 16)
            {
                $strName = 'Y';
            }
            $strName2 = $arrNames[$_POST['name']];
        }
            else
        {
            if ($_POST['name'] < 7)
            {
                $strName = 'C';
            }
            if ($_POST['name'] > 6 && $_POST['name'] < 12)
            {
                $strName = 'O';
            }
            if ($_POST['name'] > 11 && $_POST['name'] < 17)
            {
                $strName = 'T';
            }
            if ($_POST['name'] > 16)
            {
                $strName = 'J';
            }
            $strName2 = $arrNames2[$_POST['name']];
        }
        $arrNumber = array(0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 0, 1, 2, 3, 4, 0, 1, 2, 3, 4);
        $strPiecename = $strName.$arrNumber[$_POST['name']];
        $intNumber = $_POST['number'] - 1;
        $objAmount = $db -> Execute("SELECT `amount` FROM `astral` WHERE `owner`=".$player -> id." AND `type`='".$strPiecename."' AND `number`=".$intNumber." AND `location`='V'") or die($db -> ErrorMsg());
        if (!$objAmount -> fields['amount'])
        {
            error(NO_AMOUNT.$strName2);
        }
        if ($objAmount -> fields['amount'] < $_POST['amount'])
        {
            error(NO_AMOUNT.$strName2);
        }
        $objTest = $db -> Execute("SELECT `id` FROM `amarket` WHERE `seller`=".$player -> id." AND `type`='".$strPiecename."' AND `number`=".$intNumber);
        if (!$objTest -> fields['id'])
        {
            $db -> Execute("INSERT INTO `amarket` (`seller`, `type`, `number`, `amount`, `cost`) VALUES(".$player -> id.", '".$strPiecename."', ".$intNumber.", ".$_POST['amount'].", ".$_POST['cost'].")") or die($db -> ErrorMsg());
            if ($objAmount -> fields['amount'] == $_POST['amount'])
            {
                $db -> Execute("DELETE FROM `astral` WHERE `owner`=".$player -> id." AND `type`='".$strPiecename."' AND `number`=".$intNumber." AND `location`='V'");
            }
                else
            {
                $db -> Execute("UPDATE `astral` SET `amount`=`amount`-".$_POST['amount']." WHERE `owner`=".$player -> id." AND `type`='".$strPiecename."' AND `number`=".$intNumber." AND `location`='V'");
            }
            $smarty -> assign("Message", YOU_ADD." <a href=\"amarket.php?view=add\">".A_REFRESH."</a>");
        }
            else
        {
            $smarty -> assign(array("Addofert" => $objTest -> fields['id'],
                                    "Youwant" => YOU_WANT,
                                    "Ayes" => YES,
                                    "Herbname" => $_POST['name'],
                                    "Herbamount" => $_POST['amount'],
                                    "Astralnumber" => $_POST['number'],
                                    "Herbcost" => $_POST['cost'],
                                    "Step" => $_GET['step']));
            if (isset($_POST['ofert']))
            {
                if (!preg_match("/^[1-9][0-9]*$/", $_POST['ofert'])) 
                {
                    error(ERROR);
                }
                require_once('includes/marketaddto.php');
                addtoastral($objTest -> fields['id'], $objAmount -> fields['amount'], $player -> id, $strPiecename, $intNumber);
                $smarty -> assign("Message", YOU_ADD." <a href=\"amarket.php?view=add\">".A_REFRESH."</a>");
            }
        }
        $objTest -> Close();
        $objAmount -> Close();
    }
}

/**
* Delete all oferts one player from market
*/
if (isset ($_GET['view']) && $_GET['view'] == 'del') 
{
    require_once('includes/marketdelall.php');
    deleteallastral($player -> id);
    $smarty -> assign("Message", YOU_DELETE." (<a href=\"amarket.php\">".A_BACK."</a>)");
}

/**
* Buy components from market
*/
if (isset($_GET['buy'])) 
{
    if (!preg_match("/^[1-9][0-9]*$/", $_GET['buy'])) 
    {
        error (ERROR);
    }
    $buy = $db -> Execute("SELECT * FROM `amarket` WHERE `id`=".$_GET['buy']) ;
    if (!$buy -> fields['id']) 
    {
        error (NO_OFERTS);
    }
    if ($buy -> fields['seller'] == $player -> id) 
    {
        error (IS_YOUR);
    }
    $seller = $db -> Execute("SELECT `user` FROM `players` WHERE `id`=".$buy -> fields['seller']);
    if (preg_match("/^M[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("M", "", $buy -> fields['type']);
        $intNumber = $intKey;
    }
    if (preg_match("/^P[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("P", "", $buy -> fields['type']);
        $intNumber = $intKey + 7;
    }
    if (preg_match("/^R[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("R", "", $buy -> fields['type']);
        $intNumber = $intKey + 12;
    }
    if (preg_match("/^Y[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("Y", "", $buy -> fields['type']);
        $intNumber = $intKey + 17;
        $buy -> fields['number'] = '-';
    }
    if (preg_match("/^C[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("C", "", $buy -> fields['type']);
        $intNumber = $intKey + 24;
        $buy -> fields['number'] = '-';
    }
    if (preg_match("/^O[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("O", "", $buy -> fields['type']);
        $intNumber = $intKey + 29;
        $buy -> fields['number'] = '-';
    }
    if (preg_match("/^T[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("T", "", $buy -> fields['type']);
        $intNumber = $intKey + 34;
        $buy -> fields['number'] = '-';
    }
     if (preg_match("/^J[0-9]/", $buy -> fields['type']))
    {
        $intKey = str_replace("J", "", $buy -> fields['type']);
        $intNumber = $intKey + 39;
        $buy -> fields['number'] = '-';
    }
    $arrNames = array_merge($arrNames, $arrNames2);
    $strName = $arrNames[$intNumber];
    $intAstralnumber = $buy -> fields['number'] + 1;
    $smarty -> assign(array("Name" => $strName, 
                            "Amount1" => $buy -> fields['amount'], 
                            "Itemid" => $buy -> fields['id'], 
                            "Cost" => $buy -> fields['cost'], 
                            "Seller" => $seller -> fields['user'], 
                            "Sid" => $buy -> fields['seller'],
                            "Anumber" => $intAstralnumber,
                            "Buyinfo" => BUY_INFO,
                            "Bherb" => ASTRAL,
                            "Oamount" => O_AMOUNT,
                            "Hcost" => H_COST,
                            "Hseller" => SELLER,
                            "Bamount" => B_AMOUNT,
                            "Tnumber" => T_NUMBER,
                            "Abuy" => A_BUY));
    $buy -> Close();
    if (isset($_GET['step']) && $_GET['step'] == 'buy') 
    {
        if (!preg_match("/^[1-9][0-9]*$/", $_POST['amount'])) 
        {
            error (ERROR);
        }
        $buy = $db -> Execute("SELECT * FROM `amarket` WHERE `id`=".$_GET['buy']);
        $price = $_POST['amount'] * $buy -> fields['cost'];
        if ($price > $player -> credits) 
        {
            error (NO_MONEY);
        }
        if ($_POST['amount'] > $buy -> fields['amount']) 
        {
            error(NO_AMOUNT.$strName.ON_MARKET);
        }
        $db -> Execute("UPDATE `players` SET `bank`=`bank`+".$price." WHERE `id`=".$buy -> fields['seller']);
        $db -> Execute("UPDATE `players` SET `credits`=`credits`-".$price." WHERE `id`=".$player -> id);
        $objTest = $db -> Execute("SELECT `amount` FROM `astral` WHERE `owner`=".$player -> id." AND `type`='".$buy -> fields['type']."' AND `number`=".$buy -> fields['number']." AND `location`='V'");
        if (!$objTest -> fields['amount'])
        {
            $db -> Execute("INSERT INTO `astral` (`owner`, `type`, `number`, `amount`, `location`) VALUES(".$player -> id.", '".$buy -> fields['type']."', ".$buy -> fields['number'].", ".$_POST['amount'].", 'V')");
        }
            else
        {
            $db -> Execute("UPDATE `astral` SET `amount`=`amount`+".$_POST['amount']." WHERE `owner`=".$player -> id." AND `type`='".$buy -> fields['type']."' AND `number`=".$buy -> fields['number']." AND `location`='V'");
        }
        $objTest -> Close();
        if ($_POST['amount'] == $buy -> fields['amount']) 
        {
            $db -> Execute("DELETE FROM `amarket` WHERE `id`=".$buy -> fields['id']);
        } 
            else 
        {
            $db -> Execute("UPDATE `amarket` SET `amount`=`amount`-".$_POST['amount']." WHERE `id`=".$buy -> fields['id']);
        }
        $strDate = $db -> DBDate($newdate);
        $db -> Execute("INSERT INTO `log` (`owner`, `log`, `czas`) VALUES(".$buy -> fields['seller'].",'<b><a href=view.php?view=".$player -> id.">".$player -> user.L_ACCEPT.$player -> id.L_ACCEPT2.$_POST['amount'].L_AMOUNT.$strName.YOU_GET.$price.TO_BANK."', ".$strDate.")");
        $smarty -> assign("Message", YOU_BUY.$_POST['amount'].I_AMOUNT.$strName.FOR_A.$price.GOLD_COINS);
    }
}

/**
 * Delete one ofert from market
 */
if (isset($_GET['wyc'])) 
{
    if (!preg_match("/^[1-9][0-9]*$/", $_GET['wyc'])) 
    {
        error (ERROR);
    }
    $dwyc = $db -> Execute("SELECT * FROM `amarket` WHERE `id`=".$_GET['wyc']);
    if ($dwyc -> fields['seller'] != $player -> id) 
    {
        error (NOT_YOUR);
    }
    require_once('includes/marketdel.php');
    deleteastral($dwyc, $player -> id);
    $smarty -> assign("Message", YOU_DELETE." (<a href=\"amarket.php\">".A_BACK."</a>)");
}

/**
* List of all ofers on market
*/
if (isset($_GET['view']) && $_GET['view'] == 'all') 
{
    $oferts = $db -> Execute("SELECT `type` FROM `amarket` GROUP BY `type`");
    $arrname = array();
    $arramount = array();
    $i = 0;
    $arrNames = array_merge($arrNames, $arrNames2);
    while (!$oferts -> EOF) 
    {
        if (preg_match("/^M[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("M", "", $oferts -> fields['type']);
            $intNumber = $intKey;
        }
        if (preg_match("/^P[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("P", "", $oferts -> fields['type']);
            $intNumber = $intKey + 7;
        }
        if (preg_match("/^R[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("R", "", $oferts -> fields['type']);
            $intNumber = $intKey + 12;
        }
        if (preg_match("/^Y[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("Y", "", $oferts -> fields['type']);
            $intNumber = $intKey + 17;
        }
        if (preg_match("/^C[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("C", "", $oferts -> fields['type']);
            $intNumber = $intKey + 24;
        }
        if (preg_match("/^O[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("O", "", $oferts -> fields['type']);
            $intNumber = $intKey + 29;
        }
        if (preg_match("/^T[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("T", "", $oferts -> fields['type']);
            $intNumber = $intKey + 34;
        }
        if (preg_match("/^J[0-9]/", $oferts -> fields['type']))
        {
            $intKey = str_replace("J", "", $oferts -> fields['type']);
            $intNumber = $intKey + 39;
        }
        $arrname[$i] = $arrNames[$intNumber];
        $arramount[$i] = 0;
        $query = $db -> Execute("SELECT `id` FROM `amarket` WHERE `type`='".$oferts -> fields['type']."'");
        while (!$query -> EOF) 
        {
            $arramount[$i] = $arramount[$i] + 1;
            $query -> MoveNext();
        }
        $query -> Close();
        $oferts -> MoveNext();
        $i = $i + 1;
    }
    $oferts -> Close();
    $smarty -> assign(array("Name" => $arrname, 
        "Amount" => $arramount, 
        "Message" => "<br />(<a href=\"amarket.php\">".A_BACK."</a>)",
        "Listinfo" => LIST_INFO,
        "Hname" => H_NAME,
        "Hamount" => H_AMOUNT,
        "Haction" => H_ACTION,
        "Ashow" => A_SHOW));
}

/**
 * Steal components from market
 */
if (isset($_GET['steal']))
{
    if (!preg_match("/^[1-9][0-9]*$/", $_GET['steal'])) 
    {
        error(ERROR);
    }
    $objOwner = $db -> Execute("SELECT `seller` FROM `amarket` WHERE `id`=".$_GET['steal']);
    if ($objOwner -> fields['seller'] == $player -> id || $player -> clas != 'Złodziej' || $player -> location == 'Lochy')
    {
        error(ERROR);
    }
    $objCrime = $db -> Execute("SELECT `astralcrime` FROM `players` WHERE `id`=".$player -> id);
    if ($objCrime -> fields['astralcrime'] == 'N') 
    {
        error (NO_CRIME);
    }
    if ($player -> hp <= 0) 
    {
        error (YOU_DEAD);
    }
    require_once('includes/astralsteal.php');
    astralsteal($objOwner -> fields['seller'], 'R', 0, $_GET['steal']);
    $objOwner -> Close();
}

/**
* Initialization of variables
*/
if (!isset($_GET['view'])) 
{
    $_GET['view'] = '';
}
if (!isset($_GET['wyc'])) 
{
    $_GET['wyc'] = '';
}
if (!isset($_GET['buy'])) 
{
    $_GET['buy'] = '';
}

/**
* Assign variables to template and display page
*/
$smarty -> assign(array("View" => $_GET['view'], 
    "Remowe" => $_GET['wyc'], 
    "Buy" => $_GET['buy'],
    "Aback" => A_BACK));
$smarty -> display ('amarket.tpl');

require_once("includes/foot.php");
?>
