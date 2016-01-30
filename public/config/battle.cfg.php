<?php
$round_time=30000; //in miliseconds

/*This saves impact probability percent on battle like this:
 * root=>(attacker=>(target=>probability))
 * UnitClass:
 * 0-> MISS
 * 1-> Melee Infantry
 * 2-> Ranged Infantry
 * 3-> Artillery
 * 4-> Building
 */
$impactProbability = array(
    1=> array(
        0=> 10,
        1=> 25,
        2=> 40,
        3=> 15,
        4=> 10,
    ),
    2=> array(
        0=> 10,
        1=> 30,
        2=> 15,
        3=> 30,
        4=> 15,
    ),
    3=> array(
        0=> 10,
        1=> 5,
        2=> 10,
        3=> 25,
        4=> 50,
    )
);
?>
