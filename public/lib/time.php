<?
function translate_time ($seconds) {
	$times = array('semanas'=>604800, 'días'=>86400, 'horas'=>3600, 'minutos'=>60);
	$timeStr = "Hace";
	$broke = false;
	foreach ($times as $name=>$time)
		{
		$aux = floor($seconds/$time);
		if ($aux) {
			$timeStr .= " ".$aux." ".$name;
                        $broke = true; break;
                        }
		if ($seconds%$time==0)
			{
			$broke = true;
			break;
			}
		else
			$seconds = $seconds%$time;
		}
	if (!$broke)
		$timeStr .= " ".$seconds." segundos";

	return ($timeStr);
}
?>