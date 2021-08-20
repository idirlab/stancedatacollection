<?php

	function randomFloat($min = 0, $max = 1)
	{
    	return $min + mt_rand() / mt_getrandmax() * ($max - $min);
	}	
	
	echo 'TOTAL/ REG/ NFS/ UFS/ CFS<br>';
	$val = array(500, 1000, 10000, 100000, 1000000, 10000000);	
	
	foreach($val as $j )
	{
		$i = 0;
		$answered = 0;
		$screening_questioned = 0;
	
		$NFS = 0;
		$UFS = 0;
		$CFS = 0;
		$REG = 0;
		for($i = 0; $i < $j; $i++)
		{
			$is_screening = (randomFloat() <= 0.10) ? 1 : 0;// 1 is screening, 0 is regular
			$answered = $answered + 1;
			if($answered%10 == 0)
			{
				if($screening_questioned == 0)//This is the case where $is_screening was 0 in last 10 round
				{
					$is_screening = 1; // So we are making sure that there is at least one screening witin 10 sentences
				}
				$answered = 0;
				$screening_questioned = 0; //initializing for next round of 10
			}
			else if($answered%10 != 0)
			{
				if($is_screening == 1)
				{
					$screening_questioned = 1;
				}			
			}
	
			if($is_screening == 1) #screening question
			{
		
				$class_screening = randomFloat(0,30);
				if($class_screening <= 10)
				{
					$class_screening = '-1';
					$NFS += 1;
				}
				else if($class_screening <= 20)
				{
					$class_screening = '0';
					$UFS += 1;
				}
				else if($class_screening <= 30)
				{
					$class_screening = '1';
					$CFS += 1;
				}
			}
			else #regular question
			{
				$REG += 1;
			}
		}	
		echo $i.'/ '.round($REG/$i, 3).'/ '.round($NFS/$i, 3).'/ '.round($UFS/$i, 3).'/ '.round($CFS/$i, 3).'<br>';
	}
?>
