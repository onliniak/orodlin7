<?php
/**
 *   File functions:
 *   Markets menu
 *
 *   @name                 : market.php
 *   @copyright            : (C) 2004,2005,2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @version              : 1.2
 *   @since                : 28.09.2006
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
// $Id: market.php 640 2006-09-28 13:41:10Z thindil $

$title = "Targowisko";
require_once("includes/head.php");

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/market.php");

if ($player -> location != 'Altara' && $player -> location != 'Ardulith')
{
    error (ERROR);
}

$arrMarkets = array(MARKET1, MARKET2, MARKET3, MARKET4, MARKET5, MARKET6);
$arrFiles = array('pmarket', 'imarket', 'mmarket', 'hmarket', 'amarket', 'rmarket');

/**
 * Main menu
 */
if (!isset($_GET['view']))
{
    $_GET['view'] = '';
    if ($player -> location == 'Altara')
    {
        $arrMarketinfo = array(MARKET_INFO, MARKET_INFO2, MARKET_INFO3);
        $intKey = rand(0,2);
        $smarty -> assign("Marketinfo", $arrMarketinfo[$intKey]);
    }
    $smarty -> assign(array("Ashow" => A_SHOW,
                            "Asearch" => A_SEARCH,
                            "Aadd" => A_ADD,
                            "Adelete" => A_DELETE,
                            "Alist" => A_LIST,
                            "Amyoferts" => A_MYOFERTS,
                            "Location" => $player -> location));
}

/**
 * My oferts menu
 */
if (isset($_GET['view']) && $_GET['view'] == 'myoferts')
{
    /**
     * Show amount of oferts
     */
    if (!isset($_GET['type']))
    {
        /**
         * Delete all oferts from all markets
         */
        if (isset($_GET['deleteall']))
        {
            if ($_GET['deleteall'] == 'no')
            {
                $smarty -> assign(array("Youwant2" => YOU_WANT2,
                                        "Ayes" => YES,
                                        "Actiondelete" => 'no'));
            }
            if ($_GET['deleteall'] == 'yes')
            {
                require_once('includes/marketdelall.php');
                $arrNames = array(MIN1, MIN15, MIN16, MIN17, MIN18, MIN2, MIN9, MIN10, MIN3, MIN11, MIN4, MIN5, MIN6, MIN7, MIN8, MIN12, MIN13, MIN14);
                deleteallmin($player -> id, $arrNames);
                $objArm = $db -> Execute("SELECT * FROM `equipment` WHERE `owner`=".$player -> id." AND `status`='R'");
                while (!$objArm -> EOF)
                {
                    $intTest = $db -> Execute("SELECT `id` FROM `equipment` WHERE `name`='".$objArm -> fields['name']."' AND `wt`=".$objArm -> fields['wt']." AND `type`='".$objArm -> fields['type']."' AND `status`='U' AND `owner`=".$player -> id." AND `power`=".$objArm -> fields['power']." AND `zr`=".$objArm -> fields['zr']." AND `szyb`=".$objArm -> fields['szyb']." AND `maxwt`=".$objArm -> fields['maxwt']." AND `poison`=".$objArm -> fields['poison']." AND `cost`=1 AND `ptype`='".$objArm -> fields['ptype']."' AND `twohand`='".$objArm -> fields['twohand']."'");
                    if (!$intTest -> fields['id'])
                    {
                        $db -> Execute("UPDATE `equipment` SET `status`='U', `cost`=1 WHERE id=".$objArm -> fields['id']);
                    }
                        else
                    {
                        if ($objArm -> fields['type'] != 'R')
                        {
                            $db -> Execute("UPDATE equipment SET amount=amount+".$objArm -> fields['amount']." WHERE id=".$intTest -> fields['id']);
                        }
                            else
                        {
                            $db -> Execute("UPDATE `equipment` SET `wt`=`wt`+".$objArm -> fields['wt']." WHERE `id`=".$intTest -> fields['id']);
                        }
                        $db -> Execute("DELETE FROM `equipment` WHERE `status`='R' AND `owner`id`=".$intTest -> fields['id']);
                    }
                    $intTest -> Close();
                    $objArm -> MoveNext();
                }
                $arrName = array(N_HERB1, N_HERB2, N_HERB3, N_HERB4, N_HERB5, N_HERB6, N_HERB7, N_HERB8);
                deleteallherb($player -> id, $arrName);
                deleteallpotion($player -> id);
                deleteallastral($player -> id);
                $smarty -> assign(array("Actiondelete" => 'yes',
                                        "Message" => DELETED_ALL));
            }
        }
            else
        {
            $_GET['deleteall'] = '';
        }

        $arrAmount = array(0, 0, 0, 0, 0, 0);
        $arrQueries = array("SELECT count(*) FROM `pmarket` WHERE `seller`=".$player -> id,
                            "SELECT count(*) FROM `equipment` WHERE `status`='R' AND `type`!='I' AND `owner`=".$player -> id,
                            "SELECT count(*) FROM `potions` WHERE `status`='R' AND `owner`=".$player -> id,
                            "SELECT count(*) FROM `hmarket` WHERE `seller`=".$player -> id,
                            "SELECT count(*) FROM `amarket` WHERE `seller`=".$player -> id,
                            "SELECT count(*) FROM `equipment` WHERE `status`='R' AND `type`='I' AND `owner`=".$player -> id);
        for ($i = 0; $i < 6; $i ++)
        {
            $objAmount = $db -> Execute($arrQueries[$i]);
            $arrAmount[$i] = $objAmount -> fields['count(*)'];
            $objAmount -> Close();
        }
        $_GET['type'] = '';
        $smarty -> assign(array("Oamount" => $arrAmount,
                                "Deleteall" => DELETE_ALL,
                                "Actiondelete" => $_GET['deleteall']));
    }
    /**
     * Show oferts on selected market
     */
        else
    {
        $intKey = array_search($_GET['type'], $arrFiles);
        if ($intKey === false)
        {
            error(ERROR);
        }
        /**
         * Show oferts list
         */
        if (!isset($_GET['delete']) && !isset($_GET['add']) && !isset($_GET['change']))
        {
            $arrQueries = array("SELECT `id`, `ilosc`, `cost`, `nazwa` FROM `pmarket` WHERE `seller`=".$player -> id,
                                "SELECT `id`, `name`, `power`, `cost`, `minlev`, `zr`, `wt`, `szyb`, `maxwt`, `amount` FROM `equipment` WHERE `status`='R' AND `type`!='I' AND `owner`=".$player -> id,
                                "SELECT `id`, `name`, `efect`, `power`, `amount`, `cost` FROM `potions` WHERE `status`='R' AND `owner`=".$player -> id,
                                "SELECT `id`, `ilosc`, `cost`, `nazwa` FROM `hmarket` WHERE `seller`=".$player -> id,
                                "SELECT `id`, `type`, `number`, `amount`, `cost` FROM `amarket` WHERE `seller`=".$player -> id,
                                "SELECT `id`, `name`, `power`, `cost`, `amount` FROM `equipment` WHERE `status`='R' AND `type`='I' AND `owner`=".$player -> id);
            $objOferts = $db -> Execute($arrQueries[$intKey]);
            $intAmount = $objOferts -> RecordCount();
            if (!$intAmount)
            {
                error(NO_OFERTS);
            }
            $i = 0;
            $arrValues = array();
            $arrId = array();
            /**
             * Minerals or herbs market
             */
            if ($intKey == 0 || $intKey == 3)
            {
                $arrTable = array(T_NAME, T_AMOUNT, T_COST);
                while (!$objOferts -> EOF)
                {
                    $arrValues[$i][0] = $objOferts -> fields['nazwa'];
                    $arrValues[$i][1] = $objOferts -> fields['ilosc'];
                    $arrValues[$i][2] = $objOferts -> fields['cost'];
                    $arrId[$i] = $objOferts -> fields['id'];
                    $i ++;
                    $objOferts -> MoveNext();
                }
            }
            /**
             * Items market
             */
            if ($intKey == 1)
            {
                $arrTable = array(T_NAME, T_POWER, T_DUR, T_SPEED, T_AGI, T_LVL, T_AMOUNT, T_COST);
                while (!$objOferts -> EOF)
                {
                    $arrValues[$i][0] = $objOferts -> fields['name'];
                    $arrValues[$i][1] = $objOferts -> fields['power'];
                    $arrValues[$i][2] = $objOferts -> fields['wt']."/".$objOferts -> fields['maxwt'];
                    if ($objOferts -> fields['szyb'] > 0)
                    {
                        $strSpeed = "+".$objOferts -> fields['szyb'];
                    }
                        else
                    {
                        $strSpeed = 0;
                    }
                    $arrValues[$i][3] = $strSpeed;
                    if ($objOferts -> fields['zr'] <= 0)
                    {
                        $objOferts -> fields['zr'] = str_replace("-","",$objOferts -> fields['zr']);
                        $strAgility = "+".$objOferts -> fields['zr'];
                    }
                        elseif ($objOferts -> fields['zr'] > 0)
                    {
                        $strAgility = "-".$objOferts -> fields['zr'];
                    }
                        $arrValues[$i][4] = $strAgility;
                        $arrValues[$i][5] = $objOferts -> fields['minlev'];
                        $arrValues[$i][6] = $objOferts -> fields['amount'];
                        $arrValues[$i][7] = $objOferts -> fields['cost'];
                        $arrId[$i] = $objOferts -> fields['id'];
                        $i ++;
                        $objOferts -> MoveNext();
                }
            }
            /**
             * Potions market
             */
            if ($intKey == 2)
            {
                $arrTable = array(T_NAME, T_EFECT, T_AMOUNT, T_COST);
                while (!$objOferts -> EOF)
                {
                    $arrValues[$i][0] = $objOferts -> fields['name'];
                    $arrValues[$i][1] = $objOferts -> fields['efect'];
                    $arrValues[$i][2] = $objOferts -> fields['amount'];
                    $arrValues[$i][3] = $objOferts -> fields['cost'];
                    $arrId[$i] = $objOferts -> fields['id'];
                    $i ++;
                    $objOferts -> MoveNext();
                }
            }
            /**
             * Astral market
             */
            if ($intKey == 4)
            {
                $arrNames = array(MAP1, MAP2, MAP3, MAP4, MAP5, MAP6, MAP7, PLAN1, PLAN2, PLAN3, PLAN4, PLAN5, RECIPE1, RECIPE2, RECIPE3, RECIPE4, RECIPE5, FORMULA1, FORMULA2, FORMULA3, FORMULA4, FORMULA5, COMP1, COMP2, COMP3, COMP4, COMP5, COMP6, COMP7, CONST1, CONST2, CONST3, CONST4, CONST5, POTION1, POTION2, POTION3, POTION4, POTION5, JEWELLERY1, JEWELLERY2, JEWELLERY3, JEWELLERY4, JEWELLERY5);
                $arrTable = array(T_NAME, T_NUMBER, T_AMOUNT, T_COST);
                while (!$objOferts -> EOF)
                {
                    if (preg_match("/^M[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("M", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2;
                        $arrValues[$i][1] = $objOferts -> fields['number'] + 1;
                    }
                    if (preg_match("/^P[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("P", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2 + 7;
                        $arrValues[$i][1] = $objOferts -> fields['number'] + 1;
                    }
                    if (preg_match("/^R[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("R", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2 + 12;
                        $arrValues[$i][1] = $objOferts -> fields['number'] + 1;
                    }
                    if (preg_match("/^Y[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("Y", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2 + 17;
                        $arrValues[$i][1] = '-';
                    }
                    if (preg_match("/^C[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("C", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2 + 24;
                        $arrValues[$i][1] = '-';
                    }
                    if (preg_match("/^O[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("O", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2 + 29;
                        $arrValues[$i][1] = '-';
                    }
                    if (preg_match("/^T[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("T", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2 + 34;
                        $arrValues[$i][1] = '-';
                    }
                    if (preg_match("/^J[0-9]/", $objOferts -> fields['type']))
                    {
                        $intKey2 = str_replace("J", "", $objOferts -> fields['type']);
                        $intNumber = $intKey2 + 39;
                        $arrValues[$i][1] = '-';
                    }
                    $arrValues[$i][0] = $arrNames[$intNumber];
                    $arrValues[$i][2] = $objOferts -> fields['amount'];
                    $arrValues[$i][3] = $objOferts -> fields['cost'];
                    $arrId[$i] = $objOferts -> fields['id'];
                    $i ++;
                    $objOferts -> MoveNext();
                }
            }
            /**
             * Jeweller market
             */
            if ($intKey == 5)
            {
                $arrTable = array(T_NAME, T_BONUS, T_AMOUNT, T_COST);
                while (!$objOferts -> EOF)
                {
                    $arrValues[$i][0] = $objOferts -> fields['name'];
                    $arrValues[$i][1] = $objOferts -> fields['power'];
                    $arrValues[$i][2] = $objOferts -> fields['amount'];
                    $arrValues[$i][3] = $objOferts -> fields['cost'];
                    $arrId[$i] = $objOferts -> fields['id'];
                    $i ++;
                    $objOferts -> MoveNext();
                }
            }
            $objOferts -> Close();
            $arrTable[] = T_OPTIONS;
            $smarty -> assign(array("Ttable" => $arrTable,
                                    "Tvalues" => $arrValues,
                                    "Tid" => $arrId,
                                    "Aadd" => A_ADD,
                                    "Achange" => A_CHANGE,
                                    "Adelete" => A_DELETE,
                                    "Change" => '',
                                    "Add" => '',
                                    "Delete" => ''));
        }
        /**
         * Edit selected ofert
         */
        if (isset($_GET['delete']) || isset($_GET['change']) || isset($_GET['add']))
        {
            if (isset($_GET['delete']))
            {
                $intId = $_GET['delete'];
            }
                else
            {
                $_GET['delete'] = '';
            }
            if (isset($_GET['change']))
            {
                $intId = $_GET['change'];
            }
                else
            {
                $_GET['change'] = '';
            }
            if (isset($_GET['add']))
            {
                $intId = $_GET['add'];
            }
                else
            {
                $_GET['add'] = '';
            }
            if (!preg_match("/^[1-9][0-9]*$/", $intId))
            {
                error(ERROR);
            }
            $strMessage = '';
            $arrQueries = array("SELECT `id`, `ilosc`, `cost`, `nazwa` FROM `pmarket` WHERE `id`=".$intId." AND `seller`=".$player -> id,
                                "SELECT * FROM `equipment` WHERE `id`=".$intId." AND `status`='R' AND `type`!='I' AND `owner`=".$player -> id,
                                "SELECT * FROM `potions` WHERE `id`=".$intId." AND `status`='R' AND `owner`=".$player -> id,
                                "SELECT `id`, `ilosc`, `cost`, `nazwa` FROM `hmarket` WHERE `id`=".$intId." AND `seller`=".$player -> id,
                                "SELECT `id`, `type`, `number`, `amount`, `cost` FROM `amarket` WHERE `id`=".$intId." AND `seller`=".$player -> id,
                                "SELECT * FROM `equipment` WHERE `id`=".$intId." AND `status`='R' AND `type`='I' AND `owner`=".$player -> id);
            $objOfert = $db -> Execute($arrQueries[$intKey]);
            $intTest = $objOfert -> RecordCount();
            if (!$intTest)
            {
                error(NO_OFERT);
            }
            /**
             * Assign ofert name
             */
            if ($intKey == 0 || $intKey == 3)
            {
                if ($intKey == 0)
                {
                    $arrNames = array(MIN1, MIN15, MIN16, MIN17, MIN18, MIN2, MIN9, MIN10, MIN3, MIN11, MIN4, MIN5, MIN6, MIN7, MIN8, MIN12, MIN13, MIN14);
                }
                else
                {
                    $arrNames = array(N_HERB1, N_HERB2, N_HERB3, N_HERB4, N_HERB5, N_HERB6, N_HERB7, N_HERB8);
                }
                $strName = $objOfert -> fields['nazwa'];
            }
            if ($intKey == 1 || $intKey == 2 || $intKey == 5)
            {
                $strName = $objOfert -> fields['name'];
            }
            if ($intKey == 4)
            {
                $arrNames = array(MAP1, MAP2, MAP3, MAP4, MAP5, MAP6, MAP7, PLAN1, PLAN2, PLAN3, PLAN4, PLAN5, RECIPE1, RECIPE2, RECIPE3, RECIPE4, RECIPE5, FORMULA1, FORMULA2, FORMULA3, FORMULA4, FORMULA5, COMP1, COMP2, COMP3, COMP4, COMP5, COMP6, COMP7, CONST1, CONST2, CONST3, CONST4, CONST5, POTION1, POTION2, POTION3, POTION4, POTION5, JEWELLERY1, JEWELLERY2, JEWELLERY3, JEWELLERY4, JEWELLERY5);
                if (preg_match("/^M[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("M", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2;
                    $strName2 = $objOfert -> fields['number'] + 1;
                }
                if (preg_match("/^P[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("P", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2 + 7;
                    $strName2 = $objOfert -> fields['number'] + 1;
                }
                if (preg_match("/^R[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("R", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2 + 12;
                    $strName2 = $objOfert -> fields['number'] + 1;
                }
                if (preg_match("/^Y[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("Y", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2 + 17;
                    $strName2 = '-';
                }
                if (preg_match("/^C[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("C", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2 + 24;
                    $strName2 = '-';
                }
                if (preg_match("/^O[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("O", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2 + 29;
                    $strName2 = '-';
                }
                if (preg_match("/^T[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("T", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2 + 34;
                    $strName2 = '-';
                }
                if (preg_match("/^J[0-9]/", $objOfert -> fields['type']))
                {
                    $intKey2 = str_replace("J", "", $objOfert -> fields['type']);
                    $intNumber = $intKey2 + 39;
                    $strName2 = '-';
                }
                $strName = $arrNames[$intNumber].O_NUMBER.$strName2;
            }
            /**
             * Delete ofert
             */
            if (isset($_GET['delete']) && !empty($_GET['delete']))
            {
                $smarty -> assign(array("Youwant" => YOU_WANT,
                                        "Ayes" => YES,
                                        "Oname" => $strName));
                if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes')
                {
                    require_once('includes/marketdel.php');
                    if ($intKey == 0)
                    {
                        deletemin($_GET['delete'], $objOfert -> fields['nazwa'], $objOfert -> fields['ilosc'], $player -> id, $arrNames);
                    }
                    if ($intKey == 1 || $intKey == 5)
                    {
                        deleteitem($objOfert, $player -> id);
                    }
                    if ($intKey == 2)
                    {
                        deletepotion($objOfert, $player -> id);
                    }
                    if ($intKey == 3)
                    {
                        deleteherb($_GET['delete'], $objOfert -> fields['nazwa'], $objOfert -> fields['ilosc'], $player -> id, $arrNames);
                    }
                    if ($intKey == 4)
                    {
                        deleteastral($objOfert, $player -> id);
                    }
                    $strMessage = YOU_DELETE;
                }
            }
            /**
             * Add to ofert
             */
            if (isset($_GET['add']) && !empty($_GET['add']))
            {
                $smarty -> assign(array("Toofert" => TO_OFERT,
                                        "Aadd" => A_ADD,
                                        "Tamount2" => T_AMOUNT2,
                                        "Oname" => $strName));
                if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes')
                {
                    if (!isset($_POST['amount']) || !preg_match("/^[1-9][0-9]*$/", $_POST['amount']))
                    {
                        error(ERROR);
                    }
                    if ($intKey == 0)
                    {
                        $arrSqlname = array('', 'copperore', 'zincore', 'tinore', 'ironore', 'copper', 'bronze', 'brass', 'iron', 'steel', 'coal', 'adamantium', 'meteor', 'crystal', 'pine', 'hazel', 'yew', 'elm');
                        $intSqlkey = array_search($strName, $arrNames);
                        $strSqlname = $arrSqlname[$intSqlkey];
                        if (!$intSqlkey)
                        {
                            $intAmount = $player -> platinum;
                        }
                            else
                        {
                            $objAmount = $db -> Execute("SELECT `".$strSqlname."` FROM `minerals` WHERE `owner`=".$player -> id);
                            $intAmount = $objAmount -> fields[$strSqlname];
                            $objAmount -> Close();
                        }
                    }
                    if ($intKey == 1 || $intKey == 5)
                    {
                         $objAmount = $db -> Execute("SELECT `id`, `wt`, `amount` FROM `equipment` WHERE `name`='".$objOfert -> fields['name']."' AND `wt`=".$objOfert -> fields['wt']." AND `type`='".$objOfert -> fields['type']."' AND `status`='U' AND `owner`=".$player -> id." AND `power`=".$objOfert -> fields['power']." AND `zr`=".$objOfert -> fields['zr']." AND `szyb`=".$objOfert -> fields['szyb']." AND `maxwt`=".$objOfert -> fields['maxwt']." AND `poison`=".$objOfert -> fields['poison']." AND `ptype`='".$objOfert -> fields['ptype']."' AND `twohand`='".$objOfert -> fields['twohand']."'");
                         $intAmount = $objAmount -> fields['amount'];
                    }
                    if ($intKey == 2)
                    {
                        $objAmount = $db -> Execute("SELECT `id`, `amount` FROM `potions` WHERE `owner`=".$player -> id." AND `name`='".$objOfert -> fields['name']."' AND `efect`='".$objOfert -> fields['efect']."' AND `status`='K' AND `type`='".$objOfert -> fields['type']."'");
                        $intAmount = $objAmount -> fields['amount'];
                    }
                    if ($intKey == 3)
                    {
                        $arrSqlname = array('illani', 'illanias', 'nutari', 'dynallca', 'illani_seeds', 'illanias_seeds', 'nutari_seeds', 'dynallca_seeds');
                        $intSqlkey = array_search($strName, $arrNames);
                        $strSqlname = $arrSqlname[$intSqlkey];
                        $objAmount = $db -> Execute("SELECT `".$strSqlname."` FROM `herbs` WHERE `gracz`=".$player -> id);
                        $intAmount = $objAmount -> fields[$strSqlname];
                        $objAmount -> Close();
                    }
                    if ($intKey == 4)
                    {
                        $objAmount = $db -> Execute("SELECT `amount` FROM `astral` WHERE `owner`=".$player -> id." AND `type`='".$objOfert -> fields['type']."' AND `number`=".$objOfert -> fields['number']);
                        $intAmount = $objAmount -> fields['amount'];
                        $objAmount -> Close();
                    }
                    if ($_POST['amount'] > $intAmount)
                    {
                        error(NO_AMOUNT.$strName);
                    }
                    require_once('includes/marketaddto.php');
                    if ($intKey == 0)
                    {
                        addtomin($_GET['add'], $strSqlname, $intSqlkey, $player -> id);
                    }
                    if ($intKey == 1 || $intKey == 5)
                    {
                        $intAmount = $objAmount -> fields['amount'] - $_POST['amount'];
                        if ($intAmount > 0)
                        {
                            $db -> Execute("UPDATE `equipment` SET `amount`=".$intAmount." WHERE `id`=".$objAmount -> fields['id']);
                        }
                            else
                        {
                            $db -> Execute("DELETE FROM `equipment` WHERE `id`=".$objAmount -> fields['id']);
                        }
                        addtoitem($objOfert -> fields['type'], $_GET['add'], $objAmount -> fields['wt']);
                        $objAmount -> Close();
                    }
                    if ($intKey == 2)
                    {
                        $intAmount = $objAmount -> fields['amount'] - $_POST['amount'];
                        if ($intAmount > 0)
                        {
                            $db -> Execute("UPDATE `potions` SET `amount`=".$intAmount." WHERE `id`=".$objAmount -> fields['id']);
                        }
                            else
                        {
                            $db -> Execute("DELETE FROM `potions` WHERE `id`=".$objAmount -> fields['id']);
                        }
                        $db -> Execute("UPDATE `potions` SET `amount`=`amount`+".$_POST['amount']." WHERE `id`=".$_GET['add']);
                        $objAmount -> Close();
                    }
                    if ($intKey == 3)
                    {
                        addtoherb($_GET['add'], $strSqlname, $player -> id, $_POST['amount']);
                    }
                    if ($intKey == 4)
                    {
                        addtoastral($_GET['add'], $intAmount, $player -> id, $objOfert -> fields['type'], $objOfert -> fields['number']);
                    }
                    $strMessage = YOU_ADD;
                }
            }
            /**
             * Change price
             */
            if (isset($_GET['change']) && !empty($_GET['change']))
            {
                $smarty -> assign(array("Tofert" => T_OFERT,
                                        "Achange" => A_CHANGE,
                                        "Ona" => ON_A,
                                        "Goldcoins" => GOLD_COINS,
                                        "Oname" => $strName));
                if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes')
                {
                    if (!isset($_POST['amount']) || !preg_match("/^[1-9][0-9]*$/", $_POST['amount']))
                    {
                        error(ERROR);
                    }
                    $arrTables = array('pmarket', 'equipment', 'potions', 'hmarket', 'amarket', 'equipment');
                    $db -> Execute("UPDATE `".$arrTables[$intKey]."` SET `cost`=".$_POST['amount']." WHERE `id`=".$_GET['change']);
                    $strMessage = YOU_CHANGE;
                }
            }
            $smarty -> assign(array("Delete" => $_GET['delete'],
                                    "Add" => $_GET['add'],
                                    "Change" => $_GET['change'],
                                    "Message" => $strMessage));
        }
    }
    $smarty -> assign(array("Type" => $_GET['type'],
                            "Aback" => A_BACK));
}

/**
* Assign variables to template and display page
*/
$smarty -> assign(array("View" => $_GET['view'],
                        "Markets" => $arrMarkets,
                        "Filesname" => $arrFiles));
$smarty -> display ('market.tpl');

require_once("includes/foot.php");
?>
