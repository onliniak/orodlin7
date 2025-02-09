<?php
/**
 *   File functions:
 *   Function to count time to reset
 *
 *   @name                 : counttime.php
 *   @copyright            : (C) 2006 Vallheru Team based on Gamers-Fusion ver 2.5
 *   @author               : thindil <thindil@users.sourceforge.net>
 *   @version              : 1.3
 *   @since                : 16.10.2006
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
// $Id: counttime.php 725 2006-10-16 15:47:57Z thindil $

/**
* Get the localization for game
*/
require_once("languages/".$player -> lang."/counttime.php");

/**
 * Function to count time to reset
 */
function counttime()
{
    global $time;

    $arrHour = explode(":", $time);
    $arrTime2 = array(8, 10, 12, 14, 16, 18, 20, 22, 24);
    foreach ($arrTime2 as $intTime) {
        if ($arrHour[0] < $intTime) {
            $intWait = (($intTime - $arrHour[0]) * 60) - $arrHour[1];
            $intHours = floor($intWait / 60);
            $intMinutes = $intWait % 60;
            break;
        }
    }
    $arrTime = array('', '');
    if ($intHours < 1) {
        $arrTime[0] = '';
    }
    if ($intHours == 1) {
        $arrTime[0] = $intHours.T_HOUR;
    }
    if ($intHours > 1 && $intHours < 5) {
        $arrTime[0] = $intHours.T_HOURS2;
    }
    if ($intHours > 4) {
        $arrTime[0] = $intHours.T_HOURS;
    }
    if ($intMinutes < 1) {
        $arrTime[1] = '';
    }
    if ($intMinutes == 1) {
        $arrTime[1] = $intMinutes.T_MINUTE;
    }
    if (($intMinutes > 1 && $intMinutes < 5) || @preg_match("/^[2-5][2-4]*$/", $intMinutes)) {
        $arrTime[1] = $intMinutes.T_MINUTES2;
    }
    if (($intMinutes > 4 && $intMinutes < 20) || @preg_match("/^[2-5][5-9]*$/", $intMinutes) || @preg_match("/^[2-5][0-1]*$/", $intMinutes)) {
        $arrTime[1] = $intMinutes.T_MINUTES;
    }

    return $arrTime;
}
