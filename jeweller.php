<?php
/**
 *   File functions:
 *   Jeweller - make rings
 *
 *   @name                 : jeweller.php
 *   @copyright            : (C) 2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @version              : 1.3
 *   @since                : 16.11.2006
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

$title = 'Warsztat jubilerski';
require_once('includes/head.php');
require_once('includes/checkexp.php');
require_once('includes/artisan.php');

/**
* Get the localization for game
*/
require_once('languages/'.$player -> lang.'/jeweller.php');

if ($player -> location != 'Ardulith')
{
    error(ERROR);
}

/**
 * Main menu
 */
if (!isset($_GET['step']))
{
    $_GET['step'] = '';
    $smarty -> assign(array('Jewellerinfo' => JEWELLER_INFO,
                            'Aplans' => A_PLANS,
                            'Aring' => A_RING,
                            'Amakering' => A_MAKE_RING,
                            'Amakering2' => A_MAKE_RING2,
                            'Playerclass' => $player -> clas,
                            'Aastral' => A_ASTRAL));
}
    else
{
    $smarty -> assign(array('Aback' => A_BACK,
                            'Message' => ''));
}

/**
 * Buy plans
 */
if (isset ($_GET['step']) && $_GET['step'] == 'plans')
{
    if (!isset($_GET['buy']))
    {
        showplans ('jeweller', 0, $player -> clas);
    }
    else
    {
        buyplan('jeweller', $_GET['buy'], $player -> id, $player -> credits, $player -> clas);
    }
}

/**
 * Make simple rings
 */
if (isset($_GET['step']) && $_GET['step'] == 'make')
{
    $objPlan = $db -> Execute('SELECT `id` FROM `jeweller` WHERE `name`=\''.RING.'\' AND `owner`='.$player -> id);
    if (!$objPlan -> fields['id'])
    {
        error(NO_PLAN);
    }
    $objPlan -> Close();
    $smarty -> assign(array('Ringinfo' => RING_INFO,
                            'Amake' => A_MAKE,
                            'Ramount' => R_AMOUNT));
    if (isset($_GET['make']) && $_GET['make'] == 'Y')
    {
        if (!preg_match("/^[1-9][0-9]*$/", $_POST['amount']))
        {
            error(ERROR);
        }
        if ($player -> hp == 0)
        {
            error(YOU_DEAD);
        }
        $objAdamantium = $db -> Execute('SELECT `adamantium` FROM `minerals` WHERE `owner`='.$player -> id);
        if ($objAdamantium -> fields['adamantium'] < $_POST['amount'])
        {
            error(NO_ADAMANTIUM);
        }
        $objAdamantium -> Close();
        if ($player -> energy < $_POST['amount'])
        {
            error(NO_ENERGY);
        }

        /**
         * Add bonuses to ability
         */
        require_once('includes/abilitybonus.php');
        $intJeweller = abilitybonus('jeweller');

        $intChance = $intJeweller * 100;
        if ($intChance > 95)
        {
            $intChance = 95;
        }
        $intAmount = 0;
        for ($i = 0; $i < $_POST['amount']; $i++)
        {
            $intRoll = rand(1, 100);
            if ($intRoll < $intChance)
            {
                $intAmount ++;
            }
        }
        $intAbility = ($intAmount + $_POST['amount']) * 0.005 ;
        $intGainexp = $intAmount;
        if ($player -> clas == ARTISAN)
        {
            $intAbility = $intAbility * 2;
            $intGainexp = $intGainexp * 2;
        }
        $intAbility = round($intAbility*100, 0)/100;
        $intAbility = $intAbility ? $intAbility : 0.01; 
        checkexp($player -> exp, $intGainexp, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'jeweller', $intAbility);
        if ($intAmount)
        {
            $objTest = $db -> Execute('SELECT `id` FROM `equipment` WHERE `owner`='.$player -> id.' AND `name`=\''.RING.'\' AND `status`=\'U\'');
            if (!$objTest -> fields['id'])
            {
                $db -> Execute('INSERT INTO `equipment` (`owner`, `name`, `power`, `type`, `cost`, `minlev`, `amount`) VALUES('.$player -> id.', \''.RING.'\', 0, \'I\', 3, 1, '.$intAmount.')');
            }
                else
            {
                $db -> Execute('UPDATE `equipment` SET `amount`=`amount`+'.$intAmount.' WHERE `id`='.$objTest -> fields['id']);
            }
            $objTest -> Close();
        }
        $db -> Execute('UPDATE `players` SET `energy`=`energy`-'.$_POST['amount'].' WHERE `id`='.$player -> id);
        $db -> Execute('UPDATE `minerals` SET `adamantium`=`adamantium`-'.$_POST['amount'].' WHERE `owner`='.$player -> id);
        $smarty -> assign('Message', YOU_MAKE.$intAmount.'</b> '.R_AMOUNT.YOU_GAIN3.$intGainexp.T_PD.$intAbility.T_ABILITY);
    }
}

/**
 * Make rings with bonus to stats
 */
if (isset($_GET['step']) && $_GET['step'] == 'make2')
{
    if ($player -> clas != ARTISAN)
    {
        error(WRONG_CLASS);
    }

    /**
     * Make rings menu
     */
    if (!isset($_GET['action']))
    {
        $objMaked = $db -> Execute('SELECT `id`, `name`, `n_energy`, `u_energy` FROM `jeweller_work` WHERE `owner`='.$player -> id.' AND `type`=\'N\'');
        if (!$objMaked -> fields['id'])
        {
            $objPlans = $db -> Execute('SELECT `id`, `name`, `level`, `bonus` FROM `jeweller` WHERE `owner`='.$player -> id.' AND `name`!=\''.RING.'\' ORDER BY `level`');
            if (!$objPlans -> fields['name'])
            {
                error(NO_PLANS);
            }
            $arrId = array();
            $arrName = array();
            $arrLevel = array();
            $arrAdam = array();
            $arrCryst = array();
            $arrMeteor = array();
            $arrEnergy = array();
            $arrChange = array();
            $arrBonus = array();
            $i = 0;
            while (!$objPlans -> EOF)
            {
                $arrId[$i] = $objPlans -> fields['id'];
                $arrName[$i] = $objPlans -> fields['name'];
                $arrLevel[$i] = $objPlans -> fields['level'];
                $arrAdam[$i] = $objPlans -> fields['level'];
                $arrCryst[$i] = ceil($objPlans -> fields['level'] / 2);
                $arrMeteor[$i] = ceil($objPlans -> fields['level'] / 4);
                $arrBonus[$i] = $objPlans -> fields['bonus'];
                $arrEnergy[$i] = $objPlans -> fields['level'] * 2;
                $intChange = $objPlans -> fields['level'] * 4;
                if ($player -> jeweller >= $intChange)
                {
                    $arrChange[$i] = YES;
                }
                    else
                {
                    $arrChange[$i] = NO;
                }
                $i++;
                $objPlans -> MoveNext();
            }
            $objPlans -> Close();
            $smarty -> assign(array('Ringinfo' => RING_INFO,
                                    'Tname' => T_NAME,
                                    'Tlevel' => T_LEVEL,
                                    'Tadam' => T_ADAM,
                                    'Tcryst' => T_CRYST,
                                    'Tmeteor' => T_METEOR,
                                    'Tenergy' => T_ENERGY,
                                    'Tbonus' => T_BONUS,
                                    'Tchange' => T_CHANGE,
                                    'Rname' => $arrName,
                                    'Rlevel' => $arrLevel,
                                    'Radam' => $arrAdam,
                                    'Rcryst' => $arrCryst,
                                    'Rmeteor' => $arrMeteor,
                                    'Renergy' => $arrEnergy,
                                    'Rbonus' => $arrBonus,
                                    'Rid' => $arrId,
                                    'Rchange' => $arrChange,
                                    'Action' => '',
                                    'Make' => '',
                                    'Maked' => ''));

            /**
             * Select make ring
             */
            if (isset($_GET['make']))
            {
                if (!preg_match("/^[1-9][0-9]*$/", $_GET['make']))
                {
                    error(ERROR);
                }
                if ($player -> hp == 0)
                {
                  error(YOU_DEAD);
                }
                if ($objMaked -> fields['id'])
                {
                    error(ERROR);
                }
                $intKey = array_search($_GET['make'], $arrId);
                $arrBonus = array(R_AGI, R_STR, R_INT, R_WIS, R_SPE, R_CON);
                if ($arrChange[$intKey] == YES)
                {
                    $strChange = 'Y';
                }
                    else
                {
                    $strChange = 'N';
                }
                $smarty -> assign(array('Make' => $_GET['make'],
                                        'Rname2' => $arrName[$intKey],
                                        'Rbonus2' => $arrBonus,
                                        'Pjeweller' => $player -> jeweller,
                                        'Change' => $strChange,
                                        'Youmake' => YOU_MAKE,
                                        'Ramount' => R_AMOUNT2,
                                        'Withbon' => WITH_BON,
                                        'Tenergy3' => R_ENERGY));
            }
        }
            else
        {
            $smarty -> assign(array('Ringinfo' => RING_INFO,
                                    'Tname' => T_NAME,
                                    'Tenergy' => T_ENERGY,
                                    'Tenergy2' => T_ENERGY2,
                                    'Youcontinue' => YOU_CONTINUE,
                                    'Tenergy3' => R_ENERGY,
                                    'Ramount' => R_AMOUNT2,
                                    'Rname' => $objMaked -> fields['name'],
                                    'Renergy' => $objMaked -> fields['n_energy'],
                                    'Renergy2' => $objMaked -> fields['u_energy'],
                                    'Action' => '',
                                    'Make' => '',
                                    'Maked' => $objMaked -> fields['id']));
        }
        $objMaked -> Close();
    }

    /**
     * Continue create rings
     */
    if (isset($_GET['action']) && $_GET['action'] == 'continue')
    {
        if (!preg_match("/^[1-9][0-9]*$/", $_POST['make']) || !preg_match("/^[1-9][0-9]*$/", $_POST['amount']))
        {
            error(ERROR);
        }
        if ($player -> hp == 0)
        {
            error(YOU_DEAD);
        }
        $objRing = $db -> Execute('SELECT `owner`, `name`, `n_energy`, `u_energy`, `bonus` FROM `jeweller_work` WHERE `id`='.$_POST['make']);
        if (!$objRing -> fields['owner'] || $objRing -> fields['owner'] != $player -> id)
        {
            error(NO_WORK);
        }
        $intEnergy = $_POST['amount'] + $objRing -> fields['u_energy'];
        if ($intEnergy > $objRing -> fields['n_energy'])
        {
            error(TOO_MUCH);
        }

        /**
         * When player assign need energy
         */
        if ($intEnergy == $objRing -> fields['n_energy'])
        {
            /**
             * Add bonuses to ability
             */
            require_once('includes/abilitybonus.php');
            $intJeweller = abilitybonus('jeweller');

            $objRing2 = $db -> Execute('SELECT `level`, `bonus`, `cost`, `type` FROM `jeweller` WHERE `owner`='.$player -> id.' AND `name`=\''.$objRing -> fields['name'].'\'');

            $intChance = ($intJeweller / $objRing2 -> fields['level']) * 50;
            if ($intChance > 95)
            {
                $intChance = 95;
            }

            $intGainexp = 0;

            $arrStats = array(R_AGI, R_STR, R_INT, R_WIS, R_SPE, R_CON);
            $arrStats2 = array('agility', 'strength', 'inteli', 'wisdom', 'speed', 'cond');
            $intKey = array_search($objRing -> fields['bonus'], $arrStats);
            $intStat = $player -> $arrStats2[$intKey];

            $intRoll = rand(1, 100);

            /**
             * Success
             */
            if ($intRoll <= $intChance)
            {
	            echo (floor($intJeweller/2))." - ".$intJeweller." ";
                echo $intBonus = rand(floor($intJeweller/2), $intJeweller);
                if ($intBonus < 1)
                {
                    $intBonus = 1;
                }
                if ($intBonus > $objRing2 -> fields['bonus'])
                {
                    $intBonus = $objRing2 -> fields['bonus'];
                }
                $intBonus += floor($intStat / 50);
                $strName = $objRing -> fields['name']." ".$objRing -> fields['bonus'];
                $intCost = ceil($objRing2 -> fields['cost'] / 200);
                $objTest = $db -> Execute('SELECT `id` FROM `equipment` WHERE `owner`='.$player -> id.' AND status=\'U\' AND `name`=\''.$strName.'\' AND `power`='.$intBonus.' AND `cost`='.$intCost);
                if (!$objTest -> fields['id'])
                {
                    $db -> Execute('INSERT INTO `equipment` (`owner`, `name`, `power`, `status`, `type`, `cost`, `minlev`, `amount`) VALUES('.$player -> id.', \''.$strName.'\', '.$intBonus.', \'U\', \''.$objRing2 -> fields['type'].'\', '.$intCost.', '.$objRing2 -> fields['level'].', 1)');
                }
                    else
                {
                    $db -> Execute('UPDATE `equipment` SET `amount`=`amount`+1 WHERE `id`='.$objTest -> fields['id']);
                }
                $objTest -> Close();
                $intGainexp = $objRing2 -> fields['level'] * 200;
                $intAbility = ($objRing -> fields['n_energy'] * 0.02);
                $smarty -> assign('Message', YOU_MAKE.$strName.YOU_GAIN3.$intGainexp.AND_EXP2.$intAbility.IN_JEWELLER);
            }
                else
            {
                $intAbility = ($objRing -> fields['n_energy'] * 0.01);
                $intGainexp = 0;
                $smarty -> assign('Message', YOU_TRY.$objRing -> fields['name'].BUT_FAIL.$intAbility.IN_JEWELLER);
            }
            $objRing2 -> Close();
            $db -> Execute('DELETE FROM `jeweller_work` WHERE `id`='.$_POST['make']);
            checkexp($player -> exp, $intGainexp, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'jeweller', $intAbility);
        }
            else
        {
            $db -> Execute('UPDATE `jeweller_work` SET `u_energy`='.$intEnergy.' WHERE `id`='.$_POST['make']);
            $smarty -> assign('Message', YOU_TRY.$objRing -> fields['name'].YOU_GAIN4);
        }
        $objRing -> Close();
        $db -> Execute('UPDATE `players` SET `energy`=`energy`-'.$_POST['amount'].' WHERE `id`='.$player -> id);
        $smarty -> assign('Action', $_GET['action']);
    }

    /**
     * Start create rings
     */
    if (isset($_GET['action']) && $_GET['action'] == 'create')
    {
        if (!preg_match("/^[1-9][0-9]*$/", $_POST['make']) || !preg_match("/^[1-9][0-9]*$/", $_POST['amount']))
        {
            error(ERROR);
        }
        $objRing = $db -> Execute('SELECT `owner`, `name`, `level`, `bonus`, `cost`, `type` FROM `jeweller` WHERE `id`='.$_POST['make']);
        if (!$objRing -> fields['owner'] || $objRing -> fields['owner'] != $player -> id)
        {
            error(NO_PLAN);
        }
        $intMake = floor($_POST['amount'] / ($objRing -> fields['level'] * 2));

        $intNeed = ($intMake < 1) ? 1 : $intMake;
        $intAdam = $objRing -> fields['level'] * $intNeed;
        $intCryst = ceil($objRing -> fields['level'] / 2) * $intNeed;
        $intMeteor = ceil($objRing -> fields['level'] / 4) * $intNeed;
        $objMinerals = $db -> Execute('SELECT `adamantium`, `crystal`, `meteor` FROM `minerals` WHERE `owner`='.$player -> id);
        if ($objMinerals -> fields['adamantium'] < $intAdam || $objMinerals -> fields['crystal'] < $intCryst || $objMinerals -> fields['meteor'] < $intMeteor)
        {
            error(NO_MINERALS);
        }
        $objMinerals -> Close();
        if ($player -> energy < $_POST['amount'])
        {
            error(NO_ENERGY);
        }
        $objRings = $db -> Execute('SELECT `id`, `amount` FROM `equipment` WHERE `owner`='.$player -> id.' AND `name`=\''.RING.'\' AND `status`=\'U\'');
        if ($objRings -> fields['amount'] < $intMake)
        {
            error(NO_RINGS);
        }

        /**
         * Add bonuses to ability
         */
        require_once('includes/abilitybonus.php');
        $intJeweller = abilitybonus('jeweller');

        $intChance = ($intJeweller / $objRing -> fields['level']) * 50;
        if ($intChance > 95)
        {
            $intChance = 95;
        }

        /**
         * Which stats have bonus
         */
        $intChange = $objRing -> fields['level'] * 4;
        $arrStats = array(R_AGI, R_STR, R_INT, R_WIS, R_SPE, R_CON);
        $arrStats2 = array('agility', 'strength', 'inteli', 'wisdom', 'speed', 'cond');
        if (isset($_POST['bonus']) && $player -> jeweller >= $intChange)
        {
            $strStat = $_POST['bonus'];
            $intKey = array_search($_POST['bonus'], $arrStats);
            $intStat = $player -> $arrStats2[$intKey];
        }
            else
        {
            $intRoll = rand(0, 5);
            $strStat = $arrStats[$intRoll];
            $intStat = $player -> $arrStats2[$intRoll];
        }

        /**
         * Start create items
         */
        $arrBonus = array();
        $arrAmount = array();
        $intAmount2 = 0;
        for ($i = 0; $i < $intMake; $i++)
        {
            $intRoll = rand(1, 100);
            if ($intRoll <= $intChance)
            {
                $intAmount2++;
                echo (floor($intJeweller/2))." - ".$intJeweller." ";
                echo $intBonus = rand(floor($intJeweller/2), $intJeweller);
                if ($intBonus < 1)
                {
                    $intBonus = 1;
                }
                if ($intBonus > $objRing -> fields['bonus'])
                {
                    $intBonus = $objRing -> fields['bonus'];
                }
                $intBonus += floor($intStat / 50);
                $intKey = array_search($intBonus, $arrBonus);
                if (!empty($intKey) || $intKey === 0)
                {
                    $arrAmount[$intKey]++;
                }
                    else
                {
                    $arrBonus[] = $intBonus;
                    $arrAmount[] = 1;
                }
            }
        }

        /**
         * Write to database and show info
         */
        $intAbility = 0;
        if ($intMake > 0)
        {
            $i = 0;
            $strName = $objRing -> fields['name']." ".$strStat;
            $intCost = ceil($objRing -> fields['cost'] / 200);
            foreach ($arrAmount as $intAmount)
            {
                $objTest = $db -> Execute('SELECT `id` FROM `equipment` WHERE `owner`='.$player -> id.' AND status=\'U\' AND `name`=\''.$strName.'\' AND `power`='.$arrBonus[$i].' AND `cost`='.$intCost);
                if (!$objTest -> fields['id'])
                {
                    $db -> Execute('INSERT INTO `equipment` (`owner`, `name`, `power`, `status`, `type`, `cost`, `minlev`, `amount`) VALUES('.$player -> id.', \''.$strName.'\', '.$arrBonus[$i].', \'U\', \''.$objRing -> fields['type'].'\', '.$intCost.', '.$objRing -> fields['level'].', '.$intAmount.')');
                }
                    else
                {
                    $db -> Execute('UPDATE `equipment` SET `amount`=`amount`+'.$intAmount.' WHERE `id`='.$objTest -> fields['id']);
                }
                $objTest -> Close();
                $i ++;
            }
            $intGainexp = $objRing -> fields['level'] * 200;
            $intGainexp = floor($intGainexp * $intAmount2);
            $intAbility = 0.02 * $objRing -> fields['level'] * ($intAmount2 + $intMake);
            $smarty -> assign('Message', YOU_MAKE.$intAmount2.'</b> '.R_AMOUNT.$strName.YOU_GAIN3.$intGainexp.AND_EXP2.$intAbility.IN_JEWELLER);
        }
            else
        {
            $intAdam = $objRing -> fields['level'];
            $intCryst = ceil($objRing -> fields['level'] / 2);
            $intMeteor = ceil($objRing -> fields['level'] / 4);
            $intGainexp = 0;
            $intEnergy2 = ($objRing -> fields['level'] * 2);
            $intAbility = 0;
            $intEnergy = $_POST['amount'];
            $intMake = 1;
            $db -> Execute('INSERT INTO `jeweller_work` (`owner`, `name`, `n_energy`, `u_energy`, `bonus`, `type`) VALUES('.$player -> id.', \''.$objRing -> fields['name'].'\', '.$intEnergy2.', '.$_POST['amount'].', \''.$strStat.'\', \'N\')');
            $smarty -> assign('Message', YOU_TRY.$objRing -> fields['name'].YOU_GAIN4);
        }
        checkexp($player -> exp, $intGainexp, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'jeweller', $intAbility);
        $db -> Execute('UPDATE `minerals` SET `adamantium`=`adamantium`-'.$intAdam.', `crystal`=`crystal`-'.$intCryst.', `meteor`=`meteor`-'.$intMeteor.' WHERE `owner`='.$player -> id);
        $db -> Execute('UPDATE `players` SET `energy`=`energy`-'.$_POST['amount'].' WHERE `id`='.$player -> id);
        if ($objRings -> fields['amount'] == $intMake)
        {
            $db -> Execute('DELETE FROM `equipment` WHERE `id`='.$objRings -> fields['id']);
        }
            else
        {
            $db -> Execute('UPDATE `equipment` SET `amount`=`amount`-'.$intMake.' WHERE `id`='.$objRings -> fields['id']);
        }
        $objRings -> Close();
        $smarty -> assign('Action', $_GET['action']);
    }
}

/**
 * Make special rings with bonus to stats
 */
if (isset($_GET['step']) && $_GET['step'] == 'make3')
{
    if ($player -> clas != ARTISAN)
    {
        error(WRONG_CLASS);
    }

    /**
     * Main menu
     */
    $objMaked = $db -> Execute('SELECT `id`, `name`, `n_energy`, `u_energy` FROM `jeweller_work` WHERE `owner`='.$player -> id.' AND `type`=\'S\'');
    if (!$objMaked -> fields['id'])
    {
        $objPlans = $db -> Execute('SELECT `name`, `level` FROM `jeweller` WHERE `owner`='.$player -> id.' AND `name`!=\''.RING.'\' ORDER BY `level`');
        if (!$objPlans -> fields['name'])
        {
            error(NO_PLANS);
        }
        $arrName = array();
        $arrLevel = array();
        $arrMeteor = array();
        $arrEnergy = array();
        $i = 0;
        while (!$objPlans -> EOF)
        {
            $arrName[$i] = $objPlans -> fields['name'];
            $arrLevel[$i] = $objPlans -> fields['level'];
            $arrMeteor[$i] = ceil($objPlans -> fields['level'] / 2);
            $arrEnergy[$i] = $objPlans -> fields['level'] * 1.5;
            $i++;
            $objPlans -> MoveNext();
        }
        $objPlans -> Close();
        $objRings = $db -> Execute('SELECT `id`, `name`, `amount`, `minlev`, `power`, `cost` FROM `equipment` WHERE `owner`='.$player -> id.' AND `status`=\'U\' AND `type`=\'I\' AND `name`!=\''.RING.'\' AND `name` NOT LIKE \''.R_GNOME.'%\' AND `name` NOT LIKE \''.R_ELF.'%\' AND `name` NOT LIKE \''.R_DWARF.'%\' AND `name` NOT LIKE \''.R_GOD.'%\'');
        $arrRid = array();
        $arrRname = array();
        $arrRamount = array();
        $arrRpower = array();
        $arrRlevel = array();
        $arrRcost = array();
        $i = 0;
        while (!$objRings -> EOF)
        {
            $arrRid[$i] = $objRings -> fields['id'];
            $arrRname[$i] = $objRings -> fields['name'];
            $arrRamount[$i] = $objRings -> fields['amount'];
            $arrRpower[$i] = $objRings -> fields['power'];
            $arrRlevel[$i] = $objRings -> fields['minlev'];
            $arrRcost[$i] = $objRings -> fields['cost'];
            $i++;
            $objRings -> MoveNext();
        }
        $objRings -> Close();

        $smarty -> assign(array('Ringinfo' => RING_INFO,
                                'Tname' => T_NAME,
                                'Tlevel' => T_LEVEL,
                                'Tmeteor' => T_METEOR,
                                'Tenergy' => T_ENERGY,
                                'Amake' => A_MAKE,
                                'Ramount' => R_AMOUNT,
                                'Onspecial' => ON_SPECIAL,
                                'Ramount3' => R_AMOUNT2,
                                'Ramount4' => R_AMOUNT4,
                                'Renergy2' => R_ENERGY,
                                'Maked' => '',
                                'Rname' => $arrName,
                                'Rlevel' => $arrLevel,
                                'Rmeteor' => $arrMeteor,
                                'Renergy' => $arrEnergy,
                                'Rid2' => $arrRid,
                                'Rname2' => $arrRname,
                                'Ramount2' => $arrRamount,
                                'Rpower' => $arrRpower));
    }
        else
    {
        $smarty -> assign(array('Ringinfo' => RING_INFO,
                                'Tname' => T_NAME,
                                'Tenergy' => T_ENERGY,
                                'Tenergy2' => T_ENERGY2,
                                'Youcontinue' => YOU_CONTINUE,
                                'Tenergy3' => R_ENERGY,
                                'Ramount' => R_AMOUNT2,
                                'Rname' => $objMaked -> fields['name'],
                                'Renergy' => $objMaked -> fields['n_energy'],
                                'Renergy2' => $objMaked -> fields['u_energy'],
                                'Action' => '',
                                'Make' => '',
                                'Maked' => $objMaked -> fields['id']));
    }

    /**
     * Continue create special rings
     */
    if (isset($_GET['action']) && $_GET['action'] == 'continue')
    {
        if (!preg_match("/^[1-9][0-9]*$/", $_POST['make']) || !preg_match("/^[0-9][0-9\.]*$/", $_POST['amount']))
        {
            error(ERROR);
        }
        if ($player -> hp == 0)
        {
            error(YOU_DEAD);
        }
        $objRing = $db -> Execute('SELECT `owner`, `name`, `n_energy`, `u_energy`, `bonus` FROM `jeweller_work` WHERE `id`='.$_POST['make']);
        if (!$objRing -> fields['owner'] || $objRing -> fields['owner'] != $player -> id)
        {
            error(NO_WORK);
        }
        $intEnergy = $_POST['amount'] + $objRing -> fields['u_energy'];
        if ($intEnergy > $objRing -> fields['n_energy'])
        {
            error(TOO_MUCH);
        }

        /**
         * When player assign need energy
         */
        if ($intEnergy == $objRing -> fields['n_energy'])
        {
            /**
             * Add bonuses to ability
             */
            require_once('includes/abilitybonus.php');
            $intJeweller = abilitybonus('jeweller');

            /**
             * Select ring name
             */
            $arrRings = array(R_AGI, R_SPE, R_STR, R_CON, R_INT, R_WIS);
            $arrPrefix = array(R_ELF, R_DWARF, R_GNOME);
            $strName = $objRing -> fields['name'];
            $arrRingtype = explode(" ", $strName);
            $intAmount2 = count($arrRingtype) - 1;
            $intKey2 = array_search($arrRingtype[$intAmount2], $arrRings);
            $intName = $intAmount2 - ( ($intKey2 < 5) ? 1 : 2 );
            $strName2 = '';
            for ($i = 0; $i <= $intName; $i++)
            {
                if (!empty($strName2))
                {
                  $strName2 = $strName2." ";
                }
                $strName2 = $strName2.$arrRingtype[$i];
            }
            $intKey2 = floor($intKey2 / 2);
            $strPrefix = $arrPrefix[$intKey2];

            $objRing2 = $db -> Execute('SELECT `level`, `cost`, `type` FROM `jeweller` WHERE `owner`='.$player -> id.' AND `name`=\''.$strName2.'\'');

            $intChance = floor(($intJeweller / 50) * 0.5) + 5;
            if ($intChance > 15)
            {
                $intChance = 15;
            }

            $intGainexp = 0;

            $intRoll = rand(1, 100);

            /**
             * Success
             */
            if ($intRoll <= $intChance)
            {
                $blnGod = false;
                $intRoll = rand(1, 100);
                if ($intRoll == 1)
                {
                    $intRoll2 = rand(1, 2);
                    if ($intRoll2 == 1)
                    {
                        $intGainexp =  $objRing -> fields['n_energy'] * 500;
                        $intAbility =  $objRing -> fields['n_energy'] * 0.28;
                        $blnGod = true;
                    }
                }
                if ($intRoll <= $intChance && !$blnGod)
                {
                    $intGainexp = $objRing -> fields['n_energy'] * 250;
                    $intAbility = $objRing -> fields['n_energy'] * 0.14;
                }

                $intPower = (int)$objRing -> fields['bonus'];
                $intCost = ceil($objRing2 -> fields['cost'] / 10);
                $intAbility = $objRing -> fields['n_energy'] * 0.02;
                if ($blnGod)
                {
                    $strName2 = R_GOD.$strName;
                    $intPower = $intPower * 4;
                    $objTest = $db -> Execute('SELECT `id` FROM `equipment` WHERE `owner`='.$player -> id.' AND `name`=\''.$strName2.'\' AND `status`=\'U\' AND `cost`='.$intCost.' AND `power`='.$intPower);
                    if (!$objTest -> fields['id'])
                    {
                        $db -> Execute('INSERT INTO `equipment` (`owner`, `name`, `power`, `status`, `type`, `cost`, `minlev`, `amount`) VALUES('.$player -> id.', \''.$strName2.'\', \''.$intPower.'\', \'U\', \'I\', '.$intCost.', '.$intLevel.', '.$intGod.')');
                    }
                        else
                    {
                        $db -> Execute('UPDATE `equipment` SET `amount`=`amount`+'.$intGod.' WHERE `id`='.$player -> id);
                    }
                    $objTest -> Close();
                }
                    else
                {
                    $strName2 = $strPrefix.$strName;
                    $intPower = $intPower * 2;
                    $objTest = $db -> Execute('SELECT `id` FROM `equipment` WHERE `owner`='.$player -> id.' AND `name`=\''.$strName2.'\' AND `status`=\'U\' AND `cost`='.$intCost.' AND `power`='.$intPower);
                    if (!$objTest -> fields['id'])
                    {
                        $db -> Execute('INSERT INTO `equipment` (`owner`, `name`, `power`, `status`, `type`, `cost`, `minlev`, `amount`) VALUES('.$player -> id.', \''.$strName2.'\', '.$intPower.', \'U\', \'I\', \''.$intCost.', '.$intLevel.', 1)');
                    }
                    else
                    {
                        $db -> Execute('UPDATE `equipment` SET `amount`=`amount`+'.$intSpecial.' WHERE `id`='.$player -> id);
                    }
                    $objTest -> Close();
                }
                $smarty -> assign('Message', YOU_MAKE.$strName.YOU_GAIN3.$intGainexp.AND_EXP2.$intAbility.IN_JEWELLER);
            }
                else
            {
                $intAbility = $objRing -> fields['n_energy'] * 0.01;
                $smarty -> assign('Message', YOU_TRY.$strName.BUT_FAIL.$intAbility.IN_JEWELLER);
            }
            checkexp($player -> exp, $intGainexp, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'jeweller', $intAbility);
            $objRing2 -> Close();
            $db -> Execute('DELETE FROM `jeweller_work` WHERE `id`='.$_POST['make']);
        }
            else
        {
            $db -> Execute('UPDATE `jeweller_work` SET `u_energy`='.$intEnergy.' WHERE `id`='.$_POST['make']);
            $smarty -> assign('Message', YOU_TRY.$objRing -> fields['name'].YOU_GAIN4);
        }
        $objRing -> Close();
        $db -> Execute('UPDATE `players` SET `energy`=`energy`-'.$_POST['amount'].' WHERE `id`='.$player -> id);
    }

    /**
     * Start create special rings
     */
    if (isset($_GET['action']) && $_GET['action'] == 'create')
    {
        if (!isset($_POST['rings']) || !preg_match("/^[1-9][0-9]*$/", $_POST['rings']) || !preg_match("/^[0-9][0-9\.]*$/", $_POST['amount']))
        {
            error(ERROR);
        }
        if ($objMaked -> fields['id'])
        {
            error(ERROR);
        }
        if ($player -> hp == 0)
        {
            error(YOU_DEAD);
        }
        $intKey = array_search($_POST['rings'], $arrRid);
        $intLevel = $arrRlevel[$intKey];
        $intMake = floor($_POST['amount'] / ($intLevel * 1.5));
        if (!$intMake)
        {
            $intMake = 1;
        }
        $intMeteor = ceil($intLevel / 2) * $intMake;
        $objMeteor = $db -> Execute('SELECT `meteor` FROM `minerals` WHERE `owner`='.$player -> id);
        if ($objMeteor -> fields['meteor'] < $intMeteor)
        {
            error(NO_METEOR);
        }
        $objMeteor -> Close();
        $intEnergys = $intLevel * 1.5;
        $intEnergy = $intEnergys * $intMake;
        if ($player -> energy < $intEnergy)
        {
            error(NO_ENERGY);
        }
        $intRamount = $arrRamount[$intKey];
        if ($intRamount < $intMake)
        {
            error(NO_RINGS);
        }

        /**
         * Select ring name
         */
        $arrRings = array(R_AGI, R_SPE, R_STR, R_CON, R_INT, R_WIS);
        $arrPrefix = array(R_ELF, R_DWARF, R_GNOME);
        $strName = $arrRname[$intKey];
        $arrRingtype = explode(" ", $strName);
        $intAmount2 = count($arrRingtype) - 1;
        $intKey2 = array_search($arrRingtype[$intAmount2], $arrRings);
        $intKey2 = floor($intKey2 / 2);
        $strPrefix = $arrPrefix[$intKey2];

        /**
         * Add bonuses to ability
         */
        require_once('includes/abilitybonus.php');
        $intJeweller = abilitybonus('jeweller');

        $intChance = floor(($intJeweller / 50) * 0.5) + 5;
        if ($intChance > 15)
        {
            $intChance = 15;
        }

        /**
         * Start create item
         */
        $intGod = 0;
        $intSpecial = 0;
        $intGainexp = 0;
        $intAbility = 0;
        for ($i = 0; $i < $intMake; $i++)
        {
            $intRoll = rand(1, 100);
            if ($intRoll == 1)
            {
                $intRoll2 = rand(1, 2);
                if ($intRoll2 == 1)
                {
                    $intGod ++;
                    $intGainexp = $intGainexp + ($intEnergys * 500);
                    $intAbility = $intAbility + ($intEnergys * 0.28);
                    continue;
                }
            }
            if ($intRoll <= $intChance)
            {
                $intSpecial ++;
                $intGainexp = $intGainexp + ($intEnergys * 250);
                $intAbility = $intAbility + ($intEnergys * 0.14);
            }
                else
            {
                $intAbility = $intAbility + 0.01 * $intEnergys;
            }
        }
		$intAbility = round($intAbility, 2);
        /**
         * Write to database and show info
         */
        if ($intMake)
        {
            $intCost = ceil($arrRcost[$intKey] / 10);
            $intPower = $arrRpower[$intKey];
            if ($intGod)
            {
                $strName2 = R_GOD.$strName;
                $intPower = $intPower * 4;
                $objTest = $db -> Execute('SELECT `id` FROM `equipment` WHERE `owner`='.$player -> id.' AND `name`=\''.$strName2.'\' AND `status`=\'U\' AND `cost`='.$intCost.' AND `power`='.$intPower);
                if (!$objTest -> fields['id'])
                {
                    $db -> Execute('INSERT INTO `equipment` (`owner`, `name`, `power`, `status`, `type`, `cost`, `minlev`, `amount`) VALUES('.$player -> id.', \''.$strName2.'\', '.$intPower.', \'U\', \'I\', '.$intCost.', '.$intLevel.', '.$intGod.')');
                }
                    else
                {
                    $db -> Execute('UPDATE `equipment` SET `amount`=`amount`+'.$intGod.' WHERE `id`='.$player -> id);
                }
                $objTest -> Close();
            }
            $intPower = $arrRpower[$intKey];
            if ($intSpecial)
            {
                $strName2 = $strPrefix.$strName;
                $intPower *= 2;
                $objTest = $db -> Execute("SELECT `id` FROM `equipment` WHERE `owner`=".$player -> id." AND `name`='".$strName2."' AND `status`='U' AND `cost`=".$intCost." AND `power`=".$intPower);
                if (!$objTest -> fields['id'])
                {
                    $db -> Execute("INSERT INTO `equipment` (`owner`, `name`, `power`, `status`, `type`, `cost`, `minlev`, `amount`) VALUES(".$player -> id.", '".$strName2."', '".$intPower."', 'U', 'I', '".$intCost."', '".$intLevel."', '".$intSpecial."')");
                }
                    else
                {
                  $db -> Execute("UPDATE `equipment` SET `amount`=`amount`+".$intSpecial." WHERE `id`=".$player -> id);
                }
                $objTest -> Close();
            }
            $intRings = $intGod + $intSpecial;
            $smarty -> assign('Message', '<br /><br />'.YOU_MAKE.$intRings.'</b> '.R_AMOUNT3.$strName.YOU_GAIN3.$intGainexp.AND_EXP2.$intAbility.IN_JEWELLER);
        }
           else
        {
            $intMeteor = ceil($intLevel / 2);
            $intGainexp = 0;
            $intEnergy2 = ($intLevel * 1.5);
            $intAbility = 0;
            $intEnergy = $_POST['amount'];
            $intMake = 1;
            $intPower = $arrRpower[$intKey];
            $db -> Execute('INSERT INTO `jeweller_work` (`owner`, `name`, `n_energy`, `u_energy`, `bonus`, `type`) VALUES('.$player -> id.', \''.$strName.'\', '.$intEnergy2.', '.$_POST['amount'].', '.$intPower.', \'S\')');
            $smarty -> assign('Message', YOU_TRY.$strName.YOU_GAIN4);
        }
        $db -> Execute('UPDATE `minerals` SET `meteor`=`meteor`-'.$intMeteor.' WHERE `owner`='.$player -> id);
        $db -> Execute('UPDATE `players` SET `energy`=`energy`-'.$intEnergy.' WHERE `id`='.$player -> id);
        checkexp($player -> exp, $intGainexp, $player -> level, $player -> race, $player -> user, $player -> id, 0, 0, $player -> id, 'jeweller', $intAbility);
        if ($intRamount == $intMake)
        {
            $db -> Execute('DELETE FROM `equipment` WHERE `id`='.$_POST['rings']);
        }
            else
        {
            $db -> Execute('UPDATE `equipment` SET `amount`=`amount`-'.$intMake.' WHERE `id`='.$_POST['rings']);
        }
    }
}

if (!isset($_GET['buy']))
{
    $_GET['buy'] = '';
}

/**
 * Make astral constructions
 */
if (isset($_GET['step']) && $_GET['step'] == 'astral')
{
    if( !isset($_GET['component'] ))
    {
        makeastral3();
    }
    else
    {
        makeastral3($_GET['component']);
    }
}

/**
* Assign variables to template and display page
*/
$smarty -> assign(array('Step' => $_GET['step'], 'Buy' => $_GET['buy']));
$smarty -> display('jeweller.tpl');

require_once('includes/foot.php');
?>
