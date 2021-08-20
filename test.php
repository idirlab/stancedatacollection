<?php
	include_once("db.php");
	$training_sentences = '(129, 1576, 3110, 3429, 4390, 5553, 5562, 5654, 5974, 6002, 6483, 7600, 9017, 9355, 9862, 10060, 10762, 10863, 11025, 11112, 14933, 611, 15445, 15602, 15763, 16014, 16015, 16258, 16828, 17000, 17159, 17420, 17509, 21636, 24352, 26145, 27100, 27828, 27986, 28777)';

	function randomFloat($min = 0, $max = 1)
	{
		return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}

	for ($i = 1; $i <= 1000000; $i++)
	{
		$class_screening = randomFloat(0,30);
		
		if($class_screening <= 10)
		{
			$class_screening = '-1';
		}
		else if($class_screening <= 20)
		{
			$class_screening = '0';
		}
		else if($class_screening <= 30)
		{
			$class_screening = '1';
		}
	
		$sql = gen_select_query(array('Sentence.id', 'Speaker.shortform as name', 'Sentence.text'), array('Sentence', 'Speaker', 'Speaker_File'), array('Sentence.speaker_id = Speaker.id', 'Speaker.id = Speaker_File.speaker_id', 'Sentence.file_id = Speaker_File.file_id', 'Sentence.length >= 5', 'Speaker_File.role = "Interviewee"', 'screening = '.$class_screening, 'Sentence.id not in '.$training_sentences.' '), array(), array('RAND()'), array('1'));
		$results = execute($sql, array(), PDO::FETCH_ASSOC);
		echo $results[0][id];
		echo "</br>";
	}
?>
