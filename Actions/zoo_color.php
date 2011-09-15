<?php
/**
 * Display colors table
 *
 * @author Anakeen 2008
 * @version $Id: zoo_color.php,v 1.3 2010-04-30 13:44:07 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 * 
 *
 */

/**
 * Display colors table
 * @global red Http var : 0-255 red intensity
 */
function zoo_color(Action &$action)
{
    
    $usage = new ActionUsage($action);
    $red=$usage->addOption("red", "red level", array(), 128);
    $quality=$usage->addOption("quality", "quality",  array(), 20);
    $usage->strict();
    $usage->verify();
    
    if (!is_numeric($red)) {
        $usage->exitError('red must be a integer');
    }
    if (!is_numeric($quality)) {
        $usage->exitError('quality must be a integer');
    }
    if ($quality > 255) $quality = 255;
    if ($red > 255) $red = 255;
    
    for($i = 0; $i < $quality; $i++) {
        $green = $i * (255 / $quality);
        $tcolor[] = array(
            "cells" => "green$i"
        );
        $tcells = array();
        for($j = 0; $j < $quality; $j++) {
            $blue = $j * (255 / $quality);
            $tcells[] = array(
                "color" => sprintf("#%02X%02X%02X", $red, $green, $blue)
            );
        }
        $action->lay->setBlockData("green$i", $tcells);
    }
    
    $action->lay->set("red", $red);
    $action->lay->set("quality", $quality);
    $action->lay->setBlockData("ROW", $tcolor);
}
