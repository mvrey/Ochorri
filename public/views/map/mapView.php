<style type="text/css">
    <? require ("../../views/map/map.css.php"); ?>
</style>
<?
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// Following script mostly based on the next credentials
// :: HEX.PHP
// ::
// :: Author:
// ::    Tim Holt, tim.m.holt@gmail.com
// :: Description:
// ::    Generates a random hex map from a set of terrain types, then
// ::    outputs HTML to display the map.  Also outputs Javascript
// ::    to handle mouse clicks on the map.  When a mouse click is
// ::    detected, the hex cell clicked is determined, and then the
// ::    cell is highlighted.
// :: Usage Restrictions:
// ::    Available for any use.
// :: Notes:
// ::    Some content (where noted) copied and/or derived from other
// ::    sources.
// ::    Images used in this example are from the game Wesnoth.
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

require_once ('../../config/map.cfg.php');


// ----------------------------------------------------------------------
// --- This is a list of possible terrain types and the
// --- image to use to render the hex.
// ----------------------------------------------------------------------
    $terrain_images = array("water"    => $img_sectors."sector_water.png"
        ,"empty"    => $img_sectors."sector_empty.png"
        ,"own"    => $img_sectors."sector_own.png"
        ,"foreign"    => $img_sectors."sector_foreign.png");


    // ==================================================================

    function generate_map_data() {
        // -------------------------------------------------------------
        // --- Fill the $map array with values identifying the terrain
        // --- type in each hex.  This example simply randomizes the
        // --- contents of each hex.  Your code could actually load the
        // --- values from a file or from a database.
        // -------------------------------------------------------------
        global $MAP_WIDTH, $MAP_HEIGHT;
        global $map, $terrain_images, $visibleSectors, $battles, $owners, $capitol, $sessionPlayer;
        global $originX, $originY;

        $i=0;
        $originSector = $visibleSectors[0];
        $originX = $originSector->getCoordinateX();
        $originY = $originSector->getCoordinateY();

        if (isset($_SESSION['capitolSector']))
            $capitolSector = $_SESSION['capitolSector'];
        else
            $capitolSector = false;

        if ($capitolSector)
            {
            $capitolX = $capitolSector->getCoordinateX();
            $capitolY = $capitolSector->getCoordinateY();
            }
        for ($x=0; $x<$MAP_WIDTH; $x++) {
            for ($y=0; $y<$MAP_HEIGHT; $y++) {
                // --- Choose a terrain type from the terrain
                // --- images array and assign to this coordinate.

                if (isset($visibleSectors[$i]))
                    {
                    $sector = $visibleSectors[$i];

                    if (!$sector->getIsLand())
                        $terrain = "water";
                    else
                        {
                        if ($sector->getOwner()==$sessionPlayer->getId())
                            $terrain= "own";
                        elseif ($sector->getOwner()=="")
                            $terrain= "empty";
                        else $terrain= "foreign";
                        }
                    $map[$x][$y] = $terrain;
                    $battles[$x][$y] = ($sector->getIsBattle()!=false);
                    $owners[$x][$y] = $sector->getOwner();
                    if ($capitolSector)
                        {
                        $capitol[$x][$y] = $sector->getIsCapitol();
                        }
                    }
                $i++;
        }
    }
}

    // ==================================================================

    function render_map_to_html() {
        // -------------------------------------------------------------
        // --- This function renders the map to HTML.  It uses the $map
        // --- array to determine what is in each hex, and the
        // --- $terrain_images array to determine what type of image to
        // --- draw in each cell.
        // -------------------------------------------------------------
        global $MAP_WIDTH, $MAP_HEIGHT;
        global $HEX_HEIGHT, $HEX_SCALED_HEIGHT, $HEX_SIDE;
        global $map, $terrain_images, $battles, $capitol;
        global $img_other, $img_flags, $img_sectors;
        global $allPlayers, $owners;
        global $originX, $originY;
        
        
        // -------------------------------------------------------------
        // --- Draw each hex in the map
        // -------------------------------------------------------------
        for ($x=0; $x<$MAP_WIDTH; $x++) {
            for ($y=0; $y<$MAP_HEIGHT; $y++) {
                // --- Terrain type in this hex
                $terrain = $map[$x][$y];

                // --- Image to draw
                $img = $terrain_images[$terrain];
                $battle_icon = $img_other.'battle_icon.png';

                // --- Coordinates to place hex on the screen
                $tx = $x * $HEX_SIDE * 1.5;
                $ty = $y * $HEX_SCALED_HEIGHT + ($x % 2) * $HEX_SCALED_HEIGHT / 2;

                // --- Style values to position hex image in the right location
                $style = sprintf("left:%dpx;top:%dpx", $tx, $ty);

                // --- Output the image tag for this hex
                print "<div>
                <img id='$x,$y' src='$img' alt='$terrain' title='".($x+$originX).",".($y+$originY)."' class='hex' style='$style' onMouseOver='mouseOverSector($x,$y);' />";
                if ($battles[$x][$y]!=false)
                    print "<img id='battle$x,$y' class='battle_icon' src='$battle_icon' alt='battle' style='$style' onMouseOver='mouseOverSector($x,$y);' />";
                if ($capitol[$x][$y])
                    print "<img id='capitol' class='capitol_icon' src='".$img_sectors."capitol.png' alt='capitol' style='$style' onMouseOver='mouseOverSector($x,$y);' />";

                if (isset($owners[$x][$y]) && $owners[$x][$y])
                    {
                    $owner = $allPlayers[$owners[$x][$y]];
                    if ($owner->getId())
                        {
                        $flag_icon = $img_flags.$owner->getFlag();
                        print "<img id='flag$x,$y' class='flag_icon' src='$flag_icon' alt='flag' style='$style' onMouseOver='mouseOverSector($x,$y);' />";
                        }
                    }

                print "</div>";
            }
        }
    }

    // -----------------------------------------------------------------
    // --- Generate the map data
    // -----------------------------------------------------------------
    generate_map_data();
    render_map_to_html();
    echo "<img id='highlight' class='hex' src='".$img_sectors."hex-highlight.png' style='z-index:1;' alt='sector' />";
    echo "<img id='highlight-green' class='hex' src='".$img_sectors."hex-highlight-green.png' style='z-index:1; display:none' alt='sector' />";
    echo "<img id='highlight-red' class='hex' src='".$img_sectors."hex-highlight-red.png' style='z-index:1; display:none' alt='sector' />";
    ?>
    <script type="text/Javascript">
        <? require ("../../js/map_updated.js"); ?>
    </script>