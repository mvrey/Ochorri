var map_width = <?php echo $MAP_WIDTH; ?>;
var map_height = <?php echo $MAP_HEIGHT; ?>;

// ---This function moves the highlight image to
// ---be over the sector clicked on the hex grid.
function placeHighlight (map_x,map_y,mode,correction) {

if (correction == 'undefined')
    correction = false;
/*
    var correction = 0;
    var even_row = (map_x % 2 == 0);
    if (even_row)
        correction = 3;
    else
        correction = 3;*/
if (correction)
    {
    tx = (map_x-originX) * <?php echo $HEX_SIDE ?> * 1.5;
    ty = (map_y-originY) * <?php echo $HEX_SCALED_HEIGHT ?> + (map_x % 2) * (<?php echo $HEX_SCALED_HEIGHT / 2?>);
    }
else
    {
    tx = map_x * <?php echo $HEX_SIDE ?> * 1.5;
    ty = map_y * <?php echo $HEX_SCALED_HEIGHT ?> + (map_x % 2) * (<?php echo $HEX_SCALED_HEIGHT ?> / 2);
    }

    // ----------------------------------------------------------------------
    // --- Get the highlight image by ID
    // ----------------------------------------------------------------------
    switch (mode)
        {
        case 0: var highlight = document.getElementById('highlight'); break;
        case 1: hideHighlight (2);
                var highlight = document.getElementById('highlight-green');
                lastDestinySector = map_x+","+map_y;
                lastDestinyMode = 1;
                break;
        case 2: hideHighlight (1);
                var highlight = document.getElementById('highlight-red');
                lastDestinySector = map_x+","+map_y;
                lastDestinyMode = 2;
                break;
        }

    // ----------------------------------------------------------------------
    // --- Set position to be over the clicked on hex
    // ----------------------------------------------------------------------
    highlight.style.display = 'block';
    highlight.style.left = tx + 'px';
    highlight.style.top = ty + 'px';
}

function show_sectorMenu(map_x,map_y,terrain_type) {

   map = document.getElementById('hexmap');
   posx = map.offsetLeft;
   posy = map.offsetTop;

   sectorMenu_id = "sectorMenu_"+terrain_type;
   sectorMenu = $("#"+sectorMenu_id);

   tx = map_x * <?php echo $HEX_SIDE ?> * 1.5+posx+52;
   ty = map_y * <?php echo $HEX_SCALED_HEIGHT ?> + (map_x % 2) * (<?php echo $HEX_SCALED_HEIGHT ?> / 2)+posy+17;

   sectorMenu.css("left",tx+"px");
   sectorMenu.css("top",ty+"px");

   sectorMenu.fadeIn('slow');
}

// --- This function gets a mouse click on the map and
// --- converts the click to hex map coordinates
function getClickedSector (event) {
    var posx = 0;
    var posy = 0;
    if (event.pageX || event.pageY) {
        posx = event.pageX;
        posy = event.pageY;
    } else if (event.clientX || e.clientY) {
        posx = event.clientX + document.body.scrollLeft
            + document.documentElement.scrollLeft;
        posy = event.clientY + document.body.scrollTop
            + document.documentElement.scrollTop;
    }
    // --- Apply offset for the map div
    var map = document.getElementById('hexmap');
    posx = posx - map.offsetLeft;
    posy = posy - map.offsetTop;
    // ----------------------------------------------------------------------
    // --- Convert mouse click to hex grid coordinate
    // --- Code is from http://www-cs-students.stanford.edu/~amitp/Articles/GridToHex.html
    // ----------------------------------------------------------------------
    var hex_height = <?php echo $HEX_SCALED_HEIGHT; ?>;
    x = (posx - (hex_height/2)) / (hex_height * 0.75);
    y = (posy - (hex_height/2)) / hex_height;
    z = -0.5 * x - y;
    y = -0.5 * x + y;

    ix = Math.floor(x+0.5);
    iy = Math.floor(y+0.5);
    iz = Math.floor(z+0.5);
    s = ix + iy + iz;
    if (s) {
        abs_dx = Math.abs(ix-x);
        abs_dy = Math.abs(iy-y);
        abs_dz = Math.abs(iz-z);
        if (abs_dx >= abs_dy && abs_dx >= abs_dz) {
            ix -= s;
        } else if (abs_dy >= abs_dx && abs_dy >= abs_dz) {
            iy -= s;
        } else {
            iz -= s;
        }
    }

    // ----------------------------------------------------------------------
    // --- map_x and map_y are the map coordinates of the click
    // ----------------------------------------------------------------------
    map_x = ix;
    map_y = (iy - iz + (1 - ix %2 ) ) / 2 - 0.5;
    coordinates = map_x+","+map_y;

    //THIS IS FOR DEBUGGING PURPOSES
    //console.log(coordinates);

    return (coordinates);
}