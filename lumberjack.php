<?php
/**
 *   File functions:
 *   Chop trees
 *
 *   @name                 : lumberjack.php
 *   @copyright            : (C) 2004,2005,2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @version              : 1.3
 *   @since                : 25.10.2006
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
// $Id: lumberjack.php 774 2006-10-25 19:16:49Z thindil $

$title = "Wyrąb";
require_once("includes/head.php");
require_once("includes/checkexp.php");

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/lumberjack.php");

if ($player -> location != 'Las')
{
    error (ERROR, RET_LOC);
}
if ($player -> hp < 1)
{
    error(YOU_DEAD);
}
$oldFetchMode = $db -> SetFetchMode(ADODB_FETCH_NUM);
$arrLevel = $db -> GetRow('SELECT `level` FROM `lumberjack` WHERE `owner`='.$player -> id);
$db -> SetFetchMode($oldFetchMode);
if (empty($arrLevel))
{
    error(NO_LICENSE);
}
$smarty -> assignByRef ('LumberKinds', $arrLumberKinds);
$smarty -> assignByRef ('Limit', $arrLevel[0]);
/**
 * Chop down trees
 */
if (isset ($_GET['action']) && $_GET['action'] == 'chop')
{
    if (!isset($_POST['amount']) || (!preg_match("/^[1-9][0-9]*$/", $_POST['amount'])))
    {
        error(ERROR);
    }
    if ($player-> energy < $_POST['amount'])
    {
        error(NO_ENERGY);
    }
    if ($_POST['kind'] > $arrLevel[0])
    {
        error(ERROR);
    }
    /**
     * Count bonus to ability
     */
    require_once('includes/abilitybonus.php');
    $fltAbility = abilitybonus('lumberjack');

    $intAmountGold = 0;
    $arrKey = array(1, 2, 3, 4);
    $arrAmount = array(0, 0, 0, 0);
    $intLostHP = 0;
    $intLostHPSum = 0;
    $strMessage = '';
    $fltAmountAbility = 0;
    $strInfo = '';
    $intGainExp = 0;
    $intKeyLimit = min($_POST['kind'], $arrLevel[0]);
    for ($i = 0; $i < $_POST['amount']; $i++)
    {
        $intRoll = rand(1, 8);
        if ($intRoll == 5 || $intRoll == 6)
        {
            $intKey = rand(0, $intKeyLimit);
            $intAmount = max(1, ceil(((rand(1, 20) * 1 / $arrKey[$intKey]) * (1 + ($fltAbility + $fltAmountAbility) / 20)) - $arrKey[$intKey]));
            $arrAmount[$intKey] += $intAmount;
            $fltAmountAbility += 0.1;
            $intGainExp += $player -> level;
        }
        if ($intRoll == 7)
        {
            $intAmount = rand(1,100 + round($fltAbility));
            $intAmountGold += $intAmount;
        }
        if ($intRoll == 8)
        {
            $intLostHP = rand(1,100);
            if ($intLostHP < 51)
            {
                $intLostHPSum += $intLostHP;
            }
            if ($intLostHPSum > $player -> hp - 1)
            {
                $intLostHPSum = min($intLostHPSum, $player -> hp);
                $strInfo = '<br />'.DEAD_MAN;
                break;
            }
        }
        if ($intLostHPSum > 0)
        {
            $strInfo = TREE_STOMP.YOU_UNLUCK.$intLostHPSum.T_HITS;
        }
    }
	$oldFetchMode = $db -> SetFetchMode(ADODB_FETCH_NUM);
	$arrBless = $db -> GetRow('SELECT `bless`, `blessval` FROM `players` WHERE `id`='.$player -> id);
	if (isset($arrBless) && $arrBless[0] == 'hp' && $intLostHPSum < $arrBless[1])
	{
		$intLostHPSum = $arrBless[1];
	}
    $strMessage = YOU_GO.$i.T_ENERGY2;
    $fltTest = $intAmountGold + $fltAmountAbility;
    if (!$fltTest)
    {
        $strMessage = $strMessage.NOTHING;
    }
        else
    {
        $strMessage = $strMessage.YOU_FIND;
    }
    if ($arrAmount[0])
    {
        $strMessage .= $arrAmount[0].T_PINE;
    }
    if ($arrAmount[1])
    {
        $strMessage .= $arrAmount[1].T_HAZEL;
    }
    if ($arrAmount[2])
    {
        $strMessage .= $arrAmount[2].T_YEW;
    }
    if ($arrAmount[3])
    {
        $strMessage .= $arrAmount[3].T_ELM;
    }
    if ($intAmountGold)
    {
        $strMessage .= $intAmountGold.T_GOLD;
    }
    if ($fltAmountAbility)
    {
        $strMessage .= $fltAmountAbility.T_ABILITY.$intGainExp.T_GAIN_EXP;
    }
    $strMessage .= $strInfo;
    $smarty -> assignByRef ('Message', $strMessage);
	$strBless = isset($arrBless) && $arrBless[0] == 'hp' ? ', `bless`=\'\', `blessval`=0' : '';
	$db -> Execute('UPDATE `players` SET `energy`=`energy`-'.$i.', `exp`=`exp`+'.$intGainExp.', `credits`=`credits`+'.$intAmountGold.', `hp`=`hp`-'.$intLostHPSum.$strBless.' WHERE `id`='.$player -> id);
    checkexp ($player -> exp, $intGainExp, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'lumberjack', $fltAmountAbility);
    $arrLumber = $db -> GetRow('SELECT `owner` FROM `minerals` WHERE `owner`='.$player -> id);
    $db -> SetFetchMode($oldFetchMode);
    if (empty($arrLumber))
    {
        $db -> Execute('INSERT INTO `minerals` (`owner`, `pine`, `hazel`, `yew`, `elm`) VALUES('.$player -> id.', '.$arrAmount[0].', '.$arrAmount[1].', '.$arrAmount[2].', '.$arrAmount[3].')') or die($db -> ErrorMsg());
    }
        else
    {
        $db -> Execute('UPDATE `minerals` SET `pine`=`pine`+'.$arrAmount[0].', `hazel`=`hazel`+'.$arrAmount[1].', `yew`=`yew`+'.$arrAmount[2].', `elm`=`elm`+'.$arrAmount[3].' WHERE `owner`='.$player -> id);
    }
}

/**
* Initialization of variable
*/
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}

/**
* Assign variables to template and display page
*/
$smarty -> assign ('Health', isset($intLostHPSum) ? $player -> hp - $intLostHPSum : $player -> hp);
$smarty -> display ('lumberjack.tpl');
require_once("includes/foot.php");
?>
