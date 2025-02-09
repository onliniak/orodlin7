<?php
/**
 *   File functions:
 *   Explore forest and mountains
 *
 *   @name                 : explore.php                            
 *   @copyright            : (C) 2004,2005,2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @version              : 1.3
 *   @since                : 30.10.2006
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
// $Id: explore.php 796 2006-10-30 14:39:28Z thindil $

$title = "Poszukiwania";
require_once("includes/head.php");
require_once("includes/funkcje.php");
require_once("includes/turnfight.php");
require_once("includes/battle.php");
$teren = $player -> location;

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/explore.php");

if ($player -> location == 'Altara') 
{
    error (ERROR);
}
if ($player -> hp <= 0) 
{
	if ($player -> location == 'Góry')
    	error (YOU_DEAD2, 1, 'gory.php');
    elseif ($player -> location == '')
    	error (YOU_DEAD2, 1, 'las.php');
}


/**
* Assign variables to template
*/
$smarty -> assign(array("Link" => '', 
                        "Menu" => '',
                        "Youwant" => YOU_WANT,
                        "Ayes" => YES,
                        "Ano" => NO));

/**
* Function to fight with monsters
*/
function battle($type,$adress) 
{
	require_once('class/monster_class.php');
	require_once('class/fight_class.php');
    global $player;
    global $smarty;
    global $enemy;
    global $arrehp;
    global $db;
    if ($player -> hp <= 0) 
    {
        error (NO_LIFE);
    }
    if ($type == 'T') 
    {
        if (!isset ($_POST['action'])) 
        {
//prepare session variables for monsters and playerF
		$monster = new Monster($player -> fight,1,0);
		$attacker = new Fighter($player -> id);
		$_SESSION['amount'] = 1;
		for ($k = 0; $k < $_SESSION['amount']; $k++) {
			//each monster identifier
			$strIndex = 'mon'.$k;
			$_SESSION[$strIndex]['id'] = $monster -> id;
			//each monster hit points
			$_SESSION[$strIndex]['hp'] = $monster -> hp;
			//each monster action points
			if ($attacker -> speed > $monster -> attackspeed) {
				$_SESSION[$strIndex]['ap'] = 1;
				}
			else {
				$_SESSION[$strIndex]['ap'] = floor($monster -> attackspeed / $attacker -> speed);
				if ($_SESSION[$strIndex]['ap'] > 5) {
					$_SESSION[$strIndex]['ap'] = 5;
					}
				}
			$tmpActionArr[$k][0] = $monster -> attackspeed;
			$tmpActionArr[$k][1] = $k;
			}
		$tmpActionArr[$k][0] = $attacker -> speed;
		$tmpActionArr[$k][1] = -1;

		/**
		* function to compare elements of actionArr
		*/
		function aacmp($a,$b) {
			if ($a[0] == $b[0]) return 0;
			return ($a[0] > $b[0]) ? -1 : 1;
			}

		usort($tmpActionArr,"aacmp");
		for ($k = 0; $k <= $_SESSION['amount']; $k++) {
			$actionArr[$k] = $tmpActionArr[$k][1];
			}
		$_SESSION['actionArr'] = $actionArr;
		$_SESSION['exhaust']=0;
		if ($attacker -> speed > $monster -> attackspeed) {
			$_SESSION['points'] = floor($attacker -> speed / $monster -> attackspeed);
			if ($_SESSION['points'] > 5) {
				$_SESSION['points'] = 5;
				}
			}
		else {
			$_SESSION['points'] = 1;
			}
		$_SESSION['round']=0;
		}
        turnfight ($adress);
		if (isset($_SESSION['result'])) unset($_SESSION['result']);
    } 
        else 
    {
	$monster = new Monster($player -> fight,1,0);
	$attacker = new Fighter($player -> id);
        pvmfastfight ($attacker,$monster,1,1);
    }
    $fight = $db -> Execute("SELECT `fight`, `hp` FROM `players` WHERE `id`=".$player -> id);
    if ($fight -> fields['fight'] == 0) 
    {
        $player -> energy = $player -> energy - 1;
        if ($player -> energy < 0) 
        {
            $player -> energy = 0;
        }
        $db -> Execute("UPDATE players SET energy=".$player -> energy." WHERE id=".$player -> id);
        if ($player -> location == 'Góry') 
        {
            if ($fight -> fields['hp'] > 0)
            {
                $smarty -> assign ("Link", "<br /><br /><a href=\"explore.php?akcja=gory\">".A_REFRESH."</a><br />");
            }
                else
            {
                $smarty -> assign ("Link", "<br /><br /><a href=\"gory.php\">".A_REFRESH."</a><br />");
            }
        }
        if ($player -> location == 'Las') 
        {
            if ($fight -> fields['hp'] > 0)
            {
                $smarty -> assign ("Link", "<br /><br /><a href=\"explore.php\">".A_REFRESH."</a><br />");
            }
                else
            {
                $smarty -> assign ("Link", "<br /><br /><a href=\"las.php\">".A_REFRESH."</a><br />");
            }
        }
    }
    $fight -> Close();
//     $enemy1 -> Close();
}

/**
* If player not escape - start fight
*/
if (isset($_GET['step']) && $_GET['step'] == 'battle') 
{
    if (!isset ($_GET['type'])) 
    {
        $type = 'T';
    } 
        else 
    {
        $type = $_GET['type'];
    }
    battle($type,'explore.php?step=battle');
}

/**
* If player escape
*/
if (isset($_GET['step']) && $_GET['step'] == 'run') 
{
    $enemy = $db -> Execute("SELECT `level`, `speed`, `name`, `exp1`, `exp2`, `id` FROM `monsters` WHERE `id`=".$player -> fight);
	if ($player -> fight == 0)
		{
		error(NO_MONSTER);
	}
	if (empty($enemy -> fields))
	{
			error(ERROR);
	}
    /**
     * Add bonus from rings
     */
    $arrEquip = $player -> equipment();
    if ($arrEquip[9][2])
    {
        $arrRingtype = explode(" ", $arrEquip[9][1]);
        $intAmount = count($arrRingtype) - 1;
        if ($arrRingtype[$intAmount] == R_SPE4)
        {
            $player -> speed = $player -> speed + $arrEquip[9][2];
        }
    } 
    if ($arrEquip[10][2])
    {
        $arrRingtype = explode(" ", $arrEquip[10][1]);
        $intAmount = count($arrRingtype) - 1;
        if ($arrRingtype[$intAmount] == R_SPE4)
        {
            $player -> speed = $player -> speed + $arrEquip[10][2];
        }
    } 
    $chance = (rand(1, $player -> level * 100) + $player -> speed - $enemy -> fields['speed']);
    $intChance2 = rand(1, 10);
    $smarty -> assign(array("Chance" => $chance,
                            "Chance2" => $intChance2));
    if (($intChance2 < 3)||($chance > 0))
    {
	    $intExpGain = 2;
	    $intExpLvl = ceil($enemy -> fields['level']/10);
	    if ($intExpGain < $intExpLvl)
	    	$intExpGain = $intExpLvl;
        $smarty -> assign(array("Ename" => $enemy -> fields['name'],
                                "Expgain" => $intExpGain,
                                "Escapesucc" => ESCAPE_SUCC,
                                "Escapesucc2" => ESCAPE_SUCC2,
                                "Escapesucc3" => ESCAPE_SUCC3));
        checkexp($player -> exp, $intExpGain, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, '', 0);
        $db -> Execute("UPDATE `players` SET `fight`=0 WHERE `id`=".$player -> id);
    } 
        else 
    {
        $strMessage = ESCAPE_FAIL." ".$enemy -> fields['name']." ".ESCAPE_FAIL2.".<br />";
        $smarty -> assign ("Message", $strMessage);
        $smarty -> display ('error1.tpl');
        battle('T','explore.php?step=battle');
    }
    $hp = $db -> Execute("SELECT `hp` FROM `players` WHERE `id`=".$player -> id);
    $smarty -> assign ("Health", $hp -> fields['hp']);
    if ($player -> location == 'Góry' && $hp -> fields['hp'] > 0) 
    {
        $smarty -> assign (array("Yes" => "explore.php?akcja=gory", 
                                 "No" => "gory.php"));
    }
    if ($player -> location == 'Las' && $hp -> fields['hp'] > 0) 
    {
        $smarty -> assign(array("Yes" => "explore.php", 
                                "No" => "las.php"));
    }
    $hp -> Close();
}

/**
 * Explore moutains - main menu
 */
if ($player -> hp > 0 && !isset($_GET['action']) && $player -> location == 'Góry' && !isset($_GET['step'])) 
{
    if (!empty($player -> fight)) 
    {
        $enemy = $db -> Execute("SELECT `name` FROM `monsters` WHERE `id`=".$player -> fight);
        error (FIGHT1.$enemy -> fields['name'].FIGTH2."<br />
           <a href=\"explore.php?step=battle\">".YES."</a><br />
           <a href=\"explore.php?step=run\">".NO."</a><br />");
        $enemy -> Close();
    }
        else
    {
        $smarty -> assign(array("Minfo" => M_INFO,
                                "Howmuch" => HOW_MUCH,
                                "Tenergy" => T_ENERGY,
                                "Awalk" => T_WALK));
    }
}

/**
* Explore mountains - random encouter
*/
if (isset($_GET['action']) && $_GET['action'] == 'moutains' && $player -> location == 'Góry' && !isset($_GET['step'])) 
{
    if (!isset($_POST['amount']) || !preg_match("/^[0-9][0-9\.]*$/", $_POST['amount'])) 
    {
        error(ERROR);
    }
    if ($_POST['amount'] > $player -> energy)
    {
        error(TIRED2);
    }
    if ($player -> hp <= 0) 
    {
        error(YOU_DEAD2, 1, 'gory.php');
    }
    if (!empty($player -> fight)) 
    {
        $enemy = $db -> Execute("SELECT `name` FROM `monsters` WHERE `id`=".$player -> fight);
        error (FIGHT3.$enemy -> fields['name'].FIGHT2."<br />
               <a href=\"explore.php?step=battle\">".YES."</a><br />
               <a href=\"explore.php?step=run\">".NO."</a><br />");
    }
    $objMaps = $db -> Execute("SELECT `value` FROM `settings` WHERE `setting`='maps'");
    $intAmount2 = $_POST['amount'] * 2;
    $arrGold = array(0, 0);
    $arrHerbs = array(0, 0, 0, 0);
    $intMeteor = 0;
    $intAstral = 0;
    $strEnemy = '';
    $strBridge = '';
    for ($i = 1; $i < $intAmount2; $i++)
    {
        $intRoll = rand(1, 20);
        if ($intRoll == 9) 
        {
            $intAmount = rand(1,1000);
            $arrGold[0] = $arrGold[0] + $intAmount;
        }
        if ($intRoll == 10) 
        {
            $intAmount = rand(1,15);
            $intMeteor = $intMeteor + $intAmount;
        }
        if ($intRoll >= 11 && $intRoll <= 13) 
        {
            $intAmount = rand(1,10);
            $arrHerbs[0] = $arrHerbs[0] + $intAmount;
        }
        if ($intRoll >= 14 && $intRoll <= 15) 
        {
            $intAmount = rand(1,10);
            $arrHerbs[1] = $arrHerbs[1] + $intAmount;
        }
        if ($intRoll == 16) 
        {
            $intAmount = rand(1,10);
            $arrHerbs[2] = $arrHerbs[2] + $intAmount;
        }
        if ($intRoll == 18) 
        {
            $intAmount = rand(1,10);
            $arrHerbs[3] = $arrHerbs[3] + $intAmount;
        }
        if ($intRoll == 19) 
        {
            $intRoll2 = rand(1, 50);
            if ($intRoll2 == 50 && $objMaps -> fields['value'] > 0 && $player -> maps < 20 && $player -> rank != 'Bohater') 
            {
                $objMaps -> fields['value'] --;
                $player -> maps ++;
                $arrGold[1] ++;
            } 
        }
        if ($intRoll == 20)
        {
            require_once('includes/findastral.php');
            $strResult = findastral(2);
            if ($strResult != false)
            {
                $intAstral ++;
            }
        }
        if ($intRoll > 5 && $intRoll < 9) 
        {
            $intRoll2 = rand(1,5);
            if ($intRoll2 < 5 )
            {
            	$arrMonsters = array(65, 67, 69, 71, 73, 74, 75, 77, 82);
                $intRoll3 = rand(0, 8);
            }
            if ($intRoll2 == 5)
            {
            	$arrMonsters = array(88, 92, 99, 114);
                $intRoll3 = rand(0, 3);
            }
	    $enemy = $db -> Execute("SELECT `name`, `id` FROM `monsters` WHERE `location`= '$teren' ORDER BY RAND() LIMIT 1 ");
            //$enemy = $db -> Execute("SELECT `name`, `id` FROM `monsters` WHERE `id`=".$arrMonsters[$intRoll3]);
            $db -> Execute("UPDATE `players` SET `fight`=".$enemy -> fields['id']." WHERE `id`=".$player -> id);
            $strEnemy = YOU_MEET." ".$enemy -> fields['name'].FIGHT2."<br />
               <a href=\"explore.php?step=battle\">".YES."</a><br />
               <a href=\"explore.php?step=run\">".NO."</a><br />";
            $player -> fight = $enemy -> fields['id'];
            $enemy -> Close();
            break;
        }
        if ($intRoll == 17)
        {
            $objBridge = $db -> Execute("SELECT `bridge` FROM `players` WHERE `id`=".$player -> id);
            if ($objBridge -> fields['bridge'] == 'N')
            {
                $strBridge = ACTION8;
                break;
            }
            $objBridge -> Close();
        }
    }

    $intHerbsum = array_sum($arrHerbs);
    $intGoldsum = array_sum($arrGold);
    if ($intHerbsum)
    {
        $objHerbs = $db -> Execute("SELECT `gracz` FROM `herbs` WHERE `gracz`=".$player -> id);
        if ($objHerbs -> fields['gracz'])
        {
            $db -> Execute("UPDATE `herbs` SET `illani`=`illani`+".$arrHerbs[0].", `illanias`=`illanias`+".$arrHerbs[1].", `nutari`=`nutari`+".$arrHerbs[2].", `dynallca`=`dynallca`+".$arrHerbs[3]." WHERE `gracz`=".$player -> id);
        }
            else
        {
            $db -> Execute("INSERT INTO `herbs` (`gracz`, `illani`, `illanias`, `nutari`, `dynallca`) VALUES(".$player -> id.", ".$arrHerbs[0].", ".$arrHerbs[1].", ".$arrHerbs[2].", ".$arrHerbs[3].")");
        }
        $objHerbs -> Close();
    }
    if ($intMeteor)
    {
        $objMinerals = $db -> Execute("SELECT `owner` FROM `minerals` WHERE `owner`=".$player -> id);
        if ($objMinerals -> fields['owner'])
        {
            $db -> Execute("UPDATE `minerals` SET `meteor`=`meteor`+".$intMeteor." WHERE `owner`=".$player -> id);
        }
            else
        {
            $db -> Execute("INSERT INTO `minerals` (`owner`, `meteor`) VALUES(".$player -> id.", ".$intMeteor.")");
        }
        $objMinerals -> Close();
    }
    $fltAmount = $i / 2;
    $strFind = YOU_GO.$fltAmount.T_AMOUNT2;
    if ($intHerbsum || $intGoldsum || $intAstral || $intMeteor)
    {
        $strFind = $strFind.YOU_FIND;
        $arrAmounts = array($intMeteor, $arrHerbs[0], $arrHerbs[1], $arrHerbs[2], $arrHerbs[3], $intAstral, $arrGold[0], $arrGold[1]);
        $arrText = array(T_METEOR, HERB1, HERB2, HERB3, HERB4, T_ASTRALS, T_GOLD, T_MAPS);
        $i = 0;
        foreach ($arrAmounts as $intAmount)
        {
            if ($intAmount)
            {
                $strFind = $strFind.$intAmount.$arrText[$i];
            }
            $i ++;
        }
        $strFind = $strFind."<br />";
    }
    if (!$intHerbsum && !$intGoldsum && !$intAstral && $strEnemy == '' && $strBridge == '')
    {
        $strFind = $strFind.FIND_NOTHING;
    }
    $db -> Execute("UPDATE `players` SET `credits`=`credits`+".$arrGold[0].", `energy`=`energy`-".$fltAmount.", `maps`=".$player -> maps." WHERE `id`=".$player -> id);
    $db -> Execute("UPDATE `settings` SET `value`=".$objMaps -> fields['value']." WHERE `setting`='maps'");
    $objMaps -> Close();
    $smarty -> assign(array("Youfind" => $strFind,
                            "Howmuch" => HOW_MUCH,
                            "Tenergy" => T_ENERGY,
                            "Awalk" => T_WALK,
                            "Enemy" => $strEnemy,
                            "Bridge" => $strBridge));
}

/**
 * Bridge of death in moutains
 */
if (isset($_GET['action']) && $_GET['action'] == 'moutains' && $player -> location == 'Góry') 
{
    if (isset($_GET['step']) && $_GET['step'] == 'first') 
    {
        if (!isset($_POST['check']) || isset($_SESSION['bridge'])) 
        {
            error(ERROR);
        }
		$_SESSION['bridge'] = 1;
        $smarty -> assign(array("Fquestion" => F_QUESTION,
                                "Anext" => A_NEXT));
    }
    if (isset ($_GET['step']) && $_GET['step'] == 'second') 
    {
        if (!isset($_SESSION['bridge']) || $_SESSION['bridge'] != 1 || $player -> hp <= 0) 
        {
            error(ERROR);
        }
		$answer = strip_tags($_POST['fanswer']);
        if ($answer == $player -> id) 
        {
			$_SESSION['bridge'] = 2;
            $smarty -> assign (array("Answer" => "true",
                                     "Squestion" => S_QUESTION,
                                     "Anext" => A_NEXT));
        } 
        else 
        {
			unset($_SESSION['bridge']);
			$db -> Execute("UPDATE `players` SET `hp`=0 WHERE `id`=".$player -> id);
            $smarty -> assign (array("Answer" => "false",
                                     "Qfail" => Q_FAIL));
        }
    }
    if (isset ($_GET['step']) && $_GET['step'] == 'third') 
    {
        if (!isset($_SESSION['bridge']) || $_SESSION['bridge'] != 2 || $player -> hp <= 0) 
        {
            error(ERROR);
        }
        if (!isset($_POST['sanswer'])) 
        {
            $_POST['sanswer'] = '';
        }
        $answer = strip_tags($_POST['sanswer']);
        $answer = strtolower($answer);
        $gamename = strtolower($gamename);
        if ($answer == $gamename) 
        {
			$_SESSION['bridge'] = 3;
            $query = $db -> Execute("SELECT `id` FROM `bridge`");
            $amount = $query -> RecordCount();
            $query -> Close();
            $number = rand(1,$amount);
            $test = $db -> Execute("SELECT `temp` FROM `players` WHERE `id`=".$player -> id);
            if ($test -> fields['temp'] != 0) 
            {
                $number = $test -> fields['temp'];
            }
            $test -> Close();
            $question = $db -> Execute("SELECT `question` FROM `bridge` WHERE `id`=".$number);
            $db -> Execute("UPDATE `players` SET `temp`=".$number." WHERE `id`=".$player -> id);
            $smarty -> assign(array("Question" => $question -> fields['question'], 
                                    "Number" => $number, 
                                    "Answer" => "true",
                                    "Tquestion" => T_QUESTION,
                                    "Anext" => A_NEXT));
            $question -> Close();
        } 
            else 
        {
			unset($_SESSION['bridge']);
            $db -> Execute("UPDATE `players` SET `hp`=0 WHERE `id`=".$player -> id);
            $smarty -> assign(array("Answer" => "false",
                                    "Qfail" => Q_FAIL));
        }
    }
    if (isset ($_GET['step']) && $_GET['step'] == 'forth') 
    {
        if (!isset($_SESSION['bridge']) || $_SESSION['bridge'] != 3 || $player -> hp <= 0) 
        {
            error(ERROR);
        }
		unset($_SESSION['bridge']);
        if (!isset($_POST['tanswer'])) 
        {
            $_POST['tanswer'] = '';
        }
        if (!isset($_POST['number'])) 
        {
            $_POST['number'] = 1;
        }
        $answer = $db -> Execute("SELECT `answer` FROM `bridge` WHERE `id`=".$_POST['number']);
        $test = $db -> Execute("SELECT `bridge` FROM `players` WHERE `id`=".$player -> id);
        if ($test -> fields['bridge'] == 'Y') 
        {
            error(ONLY_ONCE);
        }
        $test -> Close();
        $db -> Execute("UPDATE `players` SET `temp`=0 WHERE `id`=".$player -> id);
        $panswer = strip_tags($_POST['tanswer']);
        $panswer = strtolower($panswer);
        $answer -> fields['answer'] = strtolower($answer -> fields['answer']);
        if ($panswer == $answer -> fields['answer']) 
        {
            $query = $db -> Execute("SELECT count(*) FROM `equipment` WHERE `owner`=0 AND `minlev`<=".$player -> level);
            $amount = $query -> fields['count(*)'];
            $query -> Close();
            $roll = rand (0, ($amount-1));
            $arritem = $db -> SelectLimit("SELECT * FROM `equipment` WHERE `owner`=0", 1, $roll);
            $test = $db -> Execute("SELECT `id` FROM `equipment` WHERE `name`='".$arritem -> fields['name']."' AND `wt`=".$arritem -> fields['maxwt']." AND `type`='".$arritem -> fields['type']."' AND `status`='U' AND `owner`=".$player -> id." AND `power`=".$arritem -> fields['power']." AND `zr`=".$arritem -> fields['zr']." AND `szyb`=".$arritem -> fields['szyb']." AND `maxwt`=".$arritem -> fields['maxwt']." AND `cost`=1 AND `poison`=0");
            if (!$test -> fields['id']) 
            {
                $db -> Execute("INSERT INTO `equipment` (`owner`, `name`, `power`, `type`, `cost`, `zr`, `wt`, `minlev`, `maxwt`, `amount`, `szyb`, `twohand`) VALUES(".$player -> id.",'".$arritem -> fields['name']."',".$arritem -> fields['power'].",'".$arritem -> fields['type']."',1,".$arritem -> fields['zr'].",".$arritem -> fields['maxwt'].",".$arritem -> fields['minlev'].",".$arritem -> fields['maxwt'].",1,".$arritem -> fields['szyb'].",'".$arritem -> fields['twohand']."')");
            } 
            else 
            {
                $db -> Execute("UPDATE `equipment` SET `amount`=`amount`+1 WHERE `id`=".$test -> fields['id']);
            }
            $test -> Close();
            $db -> Execute("UPDATE `players` SET `bridge`='Y' WHERE `id`=".$player -> id);
            $smarty -> assign(array("Answer" => "true", 
                                    "Item" => $arritem -> fields['name'],
                                    "Qsucc" => Q_SUCC,
                                    "Qsucc2" => Q_SUCC2,
                                    "Arefresh" => A_REFRESH));
        } 
        else 
        {
            $db -> Execute("UPDATE `players` SET `hp`=0 WHERE `id`=".$player -> id);
            $smarty -> assign(array("Answer" => "false",
                                    "Qfail" => Q_FAIL));
        }
    }
}

/**
 * Explore forest - main menu
 */
if ($player -> hp > 0 && !isset ($_GET['action']) && $player -> location == 'Las' && !isset($_GET['step'])) 
{
    if (!empty($player -> fight)) 
    {
        $enemy = $db -> Execute("SELECT `name` FROM `monsters` WHERE `id`=".$player -> fight);
        error (FIGHT3.$enemy -> fields['name'].FIGHT2."<br />
           <a href=explore.php?step=battle&type=T>".Y_TURN_F."</a><br />
           <a href=explore.php?step=battle&type=N>".Y_NORM_F."</a><br />
           <a href=explore.php?step=run>".NO."</a><br />");
    }
        else
    {
        $smarty -> assign(array("Finfo" => F_INFO,
                                "Howmuch" => HOW_MUCH,
                                "Tenergy" => T_ENERGY,
                                "Awalk" => T_WALK));
    }
}

/**
* Explore forest - random encouter
*/
if (isset($_GET['action']) && $_GET['action'] == 'forest' && $player -> location == 'Las')
{
    if (!isset($_POST['amount']) || !preg_match("/^[0-9][0-9\.]*$/", $_POST['amount'])) 
    {
        error(ERROR);
    }
    if ($_POST['amount'] > $player -> energy)
    {
        error(TIRED2);
    }
    if ($player -> hp <= 0) 
    {
        error(YOU_DEAD2,1,'las.php');
    }
    if (!empty($player -> fight)) 
    {
        $enemy = $db -> Execute("SELECT `name` FROM `monsters` WHERE `id`=".$player -> fight);
        error (FIGHT3.$enemy -> fields['name'].FIGHT2."<br /><br />
               &raquo; <a href=\"explore.php?step=battle\">".YES."</a><br />
               &raquo; <a href=\"explore.php?step=run\">".NO."</a><br />");
    }
    $objMaps = $db -> Execute("SELECT `value` FROM `settings` WHERE `setting`='maps'");
    $intAmount = $_POST['amount'] * 2;
    $arrGold = array(0, 0, 0);
    $arrHerbs = array(0, 0, 0, 0);
    $intAstral = 0;
    $strEnemy = '';
    for ($i = 1; $i < $intAmount; $i++)
    {
        $intRoll = rand(1, 19);
        if ($intRoll == 9) 
        {
            $intGold = rand(1,1000);
            $arrGold[0] = $arrGold[0] + $intGold;
        }
        if ($intRoll == 10) 
        {
            $intEnergy = rand(1, 2);
            $arrGold[1] = $arrGold[1] + $intEnergy;
        }
        if ($intRoll >= 11 && $intRoll <= 13) 
        {
            $intHerb = rand(1,10);
            $arrHerbs[0] = $arrHerbs[0] + $intHerb;
        }
        if ($intRoll >= 14 && $intRoll <= 15) 
        {
            $intHerb = rand(1,10);
            $arrHerbs[1] = $arrHerbs[1] + $intHerb;
        }
        if ($intRoll == 16) 
        {
            $intHerb = rand(1,10);
            $arrHerbs[2] = $arrHerbs[2] + $intHerb;
        }
        if ($intRoll == 17) 
        {
            $intHerb = rand(1,10);
            $arrHerbs[3] = $arrHerbs[3] + $intHerb;
        }
        if ($intRoll == 18) 
        {
            $intRoll2 = rand(1, 50);
            if ($intRoll2 == 50 && $objMaps -> fields['value'] > 0 && $player -> maps < 20 && $player -> rank != 'Bohater') 
            {
                $objMaps -> fields['value'] --;
                $player -> maps ++;
                $arrGold[2] ++;
            } 
        }
        if ($intRoll == 19)
        {
            require_once('includes/findastral.php');
            $strResult = findastral(2);
            if ($strResult != false)
            {
                $intAstral ++;
            }
        }
        if ($intRoll > 5 && $intRoll < 9) 
        {
            $intRoll2 = rand(1,5);
            if ($intRoll2 < 5 )
            {
            	$arrMonsters = array(2, 4, 8, 10, 15, 19, 22, 25);
                $intRoll3 = rand(0, 7);
            }
            if ($intRoll2 == 5)
            {
            	$arrMonsters = array(35, 40, 46, 55);
                $intRoll3 = rand(0, 3);
            }
	    $enemy = $db -> Execute("SELECT `name`, `id` FROM `monsters` WHERE `location`= '$teren' ORDER BY RAND() LIMIT 1 ");
            //$enemy = $db -> Execute("SELECT `name`, `id` FROM `monsters` WHERE `id`=".$arrMonsters[$intRoll3]);
            $db -> Execute("UPDATE `players` SET `fight`=".$enemy -> fields['id']." WHERE `id`=".$player -> id);
            $strEnemy = YOU_MEET." ".$enemy -> fields['name'].FIGHT2."<br /><br />
               &raquo; <a href=\"explore.php?step=battle\">".YES."</a><br />
               &raquo; <a href=\"explore.php?step=run\">".NO."</a><br />";
            $player -> fight = $enemy -> fields['id'];
            $enemy -> Close();
            break;
        }
    }

    $intHerbsum = array_sum($arrHerbs);
    $intGoldsum = array_sum($arrGold);
    if ($intHerbsum)
    {
        $objHerbs = $db -> Execute("SELECT `gracz` FROM `herbs` WHERE `gracz`=".$player -> id);
        if ($objHerbs -> fields['gracz'])
        {
            $db -> Execute("UPDATE `herbs` SET `illani`=`illani`+".$arrHerbs[0].", `illanias`=`illanias`+".$arrHerbs[1].", `nutari`=`nutari`+".$arrHerbs[2].", `dynallca`=`dynallca`+".$arrHerbs[3]." WHERE `gracz`=".$player -> id) or die("Błąd");
        }
            else
        {
            $db -> Execute("INSERT INTO `herbs` (`gracz`, `illani`, `illanias`, `nutari`, `dynallca`) VALUES(".$player -> id.", ".$arrHerbs[0].", ".$arrHerbs[1].", ".$arrHerbs[2].", ".$arrHerbs[3].")");
        }
        $objHerbs -> Close();
    }
    $fltAmount = $i / 2;
    $strFind = YOU_GO.$fltAmount.T_AMOUNT2;
    if ($intHerbsum || $intGoldsum || $intAstral)
    {
        $strFind = $strFind.YOU_FIND;
        $arrAmounts = array($arrHerbs[0], $arrHerbs[1], $arrHerbs[2], $arrHerbs[3], $intAstral, $arrGold[0], $arrGold[1], $arrGold[2]);
        $arrText = array(HERB1, HERB2, HERB3, HERB4, T_ASTRALS, T_GOLD, T_ENERGY2, T_MAPS);
        $i = 0;
        foreach ($arrAmounts as $intAmount)
        {
            if ($intAmount)
            {
                $strFind = $strFind.$intAmount.$arrText[$i];
            }
            $i ++;
        }
        $strFind = $strFind."<br />";
    }
    if (!$intHerbsum && !$intGoldsum && !$intAstral && $strEnemy == '')
    {
        $strFind = $strFind.FIND_NOTHING;
    }
    $fltEnergy = $fltAmount - $arrGold[1];
    $db -> Execute("UPDATE `players` SET `credits`=`credits`+".$arrGold[0].", `energy`=`energy`-".$fltEnergy.", `maps`=".$player -> maps." WHERE `id`=".$player -> id);
    $db -> Execute("UPDATE `settings` SET `value`=".$objMaps -> fields['value']." WHERE `setting`='maps'");
    $objMaps -> Close();
    $smarty -> assign(array("Youfind" => $strFind,
                            "Howmuch" => HOW_MUCH,
                            "Tenergy" => T_ENERGY,
                            "Awalk" => T_WALK,
                            "Enemy" => $strEnemy));
}

/**
* Initialization of variables
*/
if (!isset($_GET['step'])) 
{
    $_GET['step'] = '';
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
if (!isset($rzut)) 
{
    $rzut = '';
}

$smarty -> assign(array("Step" => $_GET['step'], 
                        "Fight" => $player -> fight, 
                        "Action" => $_GET['action'],
                        "Location" => $player -> location, 
                        "Roll" => $rzut,
                        "Health" => $player -> hp));
$smarty -> display('explore.tpl');

require_once("includes/foot.php");
?>
