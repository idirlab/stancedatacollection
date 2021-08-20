<?php
	include_once("db.php");
	
	$training_sentences = '(129, 1576, 3110, 3429, 4390, 5553, 5562, 5654, 5974, 6002, 6483, 7600, 9017, 9355, 9862, 10060, 10762, 10863, 11025, 11112, 14933, 611, 15445, 15602, 15763, 16014, 16015, 16258, 16828, 17000, 17159, 17420, 17509, 21636, 24352, 26145, 27100, 27828, 27986, 28777)'; /*May 26, 2016*/
	
	$sql = 'select 
    count(*) as TOTAL
from
    Sentence,
    Speaker_File
where
    Sentence.file_id = Speaker_File.file_id
        and Sentence.speaker_id = Speaker_File.speaker_id
        and Speaker_File.role = "Interviewee"
        and screening = -3
        and Sentence.length >= 5;';
	$total_sentences = execute($sql, array(), PDO::FETCH_ASSOC)[0]['TOTAL'];
	echo 'Total Sentences: '.$total_sentences.' (excluding 1032 screening sentences)';
#	$total_labels_required = intval($total_sentences)*2;
#	echo '<br>Labels Required: '.$total_sentences.' X 2 = '.strval($total_labels_required);
	
#	$sql = "select 
#    sum(if(answered = 0, 2, 0))+sum(if(answered = 1, 1, 0)) as REQUIRED, count(*)*2 as TOTAL
#from
#    Sentence,
#    Speaker_File
#where
#    Sentence.file_id = Speaker_File.file_id
#        and Sentence.speaker_id = Speaker_File.speaker_id
#        and Speaker_File.role = 'Interviewee'
#        and Sentence.length >= 5
#		and screening = -3;";
#	$temp = execute($sql, array(), PDO::FETCH_ASSOC)[0];
#	echo '<br>Labels Received: '.strval( intval($temp['TOTAL'])-intval($temp['REQUIRED'])).'<br>';
#	echo '<b>Progress: '. strval(round((intval($temp['TOTAL'])-intval($temp['REQUIRED']))/intval($temp['TOTAL'])*100,2)).'%</b>';

	echo '<br>';
#	$sql = 'select sentence_id, sum(if(response = -1, 1, 0)) as NFS, sum(if(response = 0, 1, 0)) as UFS, sum(if(response = 1, 1, 0)) as CFS from Sentence_User, Sentence where Sentence.id = Sentence_User.sentence_id and screening = -3 group by sentence_id having NFS >= 2 or UFS >= 2 or CFS >= 2;';
#	$all_agreements = count(execute($sql, array(), PDO::FETCH_ASSOC));
	#echo '<br>Two Participants Agreed: '.strval($all_agreements).' <b>['.strval(round((intval($all_agreements))/intval($total_sentences)*100,2)).'%]</b>';
	
	$sql = 'select 
    Sentence_User.username as USERNAME	
from
    Sentence_User,
    Sentence
where
    id = sentence_id and
	username not in ("cmavs2015", "sakiforu", "teaphony") and
	response != -2 and
	sentence_id not in '.$training_sentences.'
group by Sentence_User.username

having 

	-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
	+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)) <= 0.0 and count(*) >= 50';	
	$top_participants = execute($sql, array(), PDO::FETCH_COLUMN);
	$top_participants_string = '("'.implode('","', $top_participants).'")';
	echo '<br><br>Number of Top-quality Participants: '.count($top_participants).' [RANK_W <= 0.0]';
	
	echo '<br><br>/*A sentence is NOT selected for further questions if the following condition is true: there exists a category X in {NFS, CFS, UFS} such that X>=2 and X/(N+U+C) > Y/(N+U+C) for any Y <> X and Y in {NFS, CFS, UFS}.*/<br>';
	
	echo '<br><br>/*A sentence is NOT selected for further questions if the following condition is true: there exists a category X in {NFS, CFS, UFS} such that X>=2 and X > (N+U+C)/2.*/<br>';
	
	echo '<br><br>A sentence is NOT selected for further questions if the following condition is true: there exists a category X in {NFS, CFS, UFS} such that X>=3 and X >= 2+Y and X >= 2+Z.<br>';

	$sql = 'select sentence_id, 
			sum(if(response = -1, 1, 0)) as nfs, 
			sum(if(response = 0, 1, 0)) as ufs, 
			sum(if(response = 1, 1, 0)) as cfs
			from Sentence_User, Sentence 
			where Sentence.id = Sentence_User.sentence_id 
			and screening = -3
			and username in '.$top_participants_string.'group by sentence_id
			having (nfs >= 3 and nfs >= 2+ufs and nfs >= 2+cfs)
			or (ufs >= 3 and ufs >= 2+nfs and ufs >= 2+cfs)
			or (cfs >= 3 and cfs >= 2+ufs and cfs >= 2+nfs)';
	
	echo 'Number of Sentences for which the above condition is true: ';	
	$top_agreements = count(execute($sql, array(), PDO::FETCH_ASSOC));
	echo ''.strval($top_agreements).' <b>['.strval(round((intval($top_agreements))/intval($total_sentences)*100,2)).'%]</b>';

	/*$sql = 'select sentence_id, sum(if(response = -1, 1, 0)) as NFS, sum(if(response = 0, 1, 0)) as UFS, sum(if(response = 1, 1, 0)) as CFS from Sentence_User, Sentence where Sentence.id = Sentence_User.sentence_id and screening = -3 and username in '.$top_participants_string.' group by sentence_id having NFS >= 2 or UFS >= 2 or CFS >= 2;';
	$top_agreements = count(execute($sql, array(), PDO::FETCH_ASSOC));
	echo '<br>Two Top-Quality Participants Agreed: '.strval($top_agreements).' <b>['.strval(round((intval($top_agreements))/intval($total_sentences)*100,2)).'%]</b>';*/
	
	$sql = 'select count(*) from Sentence_User, Sentence where Sentence_User.sentence_id = Sentence.id and screening = - 3 and response != - 2;';
	$total_labels = execute($sql, array(), PDO::FETCH_COLUMN);
	$sql = 'select count(*) from Sentence_User, Sentence where Sentence_User.sentence_id = Sentence.id and screening = - 3 and response != - 2 and username in '.$top_participants_string.';';
	$total_top_labels = execute($sql, array(), PDO::FETCH_COLUMN);
	echo '<br><br>Total Labels: '.$total_labels[0].'<br>Top-Quality Labels: '.$total_top_labels[0].'<br>';
	
	
	echo '<br><br>Rank_E: <a href="http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.230.7064&rep=rep1&type=pdf">http://citeseerx.ist.psu.edu/viewdoc/download?doi=10.1.1.230.7064&rep=rep1&type=pdf</a> [equation 8]';
	echo '<br>Rank_W: NFS_YES/YES_NFS mistake cost 2.5; NFS_NO/NO_NFS/YES_NO/NO_YES mistake cost 0.7; correct answer cost -0.2';
	echo '<br>Rank_L: LEN / L * ANSWERED*sign(0.3 - RANK_W)*pow((0.3 - RANK_W), 2)';
	echo '<br>Quality: if RANK_W <= 0 then 3-7*RANK_W/0.2 elif RANK_W <= 0.3 then pow((0.3-RANK_W)/0.3, 2.5)*3 else 0';	
	echo '<br>SKIP = NO_OF_SKIPPED  / ANSWERED';
	echo '<br>PAYRATE: (LEN / L)^1.5 * Quality * (0.6^SKIP)';
	echo '<br>PAYMENT = PAYRATE  * ANSWERED / 100';
	echo '<br>LEN is the average length of the sentences labelled by the participant';
	echo '<br>L: average length = 18.4217';
	
	echo '<br><br>Screening Question Selection Process: There are 1032 (NFS 731, UFS 63, CFS 238) sentences for which we three agreed upon. These are used as screening questions. For every ten regular questions a participant faces, the system makes sure that there is one screening question. A random number X [1:3] decides the type of the screening question. If X = 1, the type is NFS, if X = 2, the type is UFS, if X = 3, the type is CFS. Once the type is selected, the screening question is randomly picked from the pool of screening questions of that particular type.<br>Note that, as the set of screening sentence is not equally distributed and as we may repeat screening questions, the distribution of NFS/NO/YES screening questions is skewed.';
	
	echo '<br><br><a href="https://docs.google.com/spreadsheets/d/1PN9k-gXR14y5mtV7ThTBw9RKPN0pp1py73SmtPWkCFA/edit#gid=0">Change Log: http://bit.ly/1CsOhQY</a>. This is link of the google sheet where we keep records of the changes gradually made in the data collection system.';
	
	$sql = 'select A.username, count(*) as count from (select * from Sentence_User where response != -2 and time >= "2016-05-08 00:00:00" order by time desc limit 100) A group by A.username';
	$results = $results = execute($sql, array(), PDO::FETCH_ASSOC);
	echo '<br><br>Latest (100) sentence labelers: ';
	foreach($results as $key=>$v)
	{
		echo $v['username'].' ('.$v['count'].'), ';
	}
	
	$sql = 'select distinct(username) from Sentence_User where minute(timediff(now(), time)) <= 3 and hour(timediff(now(), time)) = 0';
	$results = $results = execute($sql, array(), PDO::FETCH_ASSOC);
	echo '<br>Active Participants in last 3 minutes are marked below.';
	$active_users = array();
	foreach($results as $key=>$v)
	{
		array_push($active_users, $v['username']);
	}
	
	
	echo '<br><br>Workshop Statistics [After 2017-01-11 18:00:00]';

$sql = 'select USERNAME,PROFESSION,RANK_E,RANK_W,ANSWERED as "#", A.SKIPPED/A.ANSWERED as SKIP, A.LEN,round(A.LEN/18.4217*A.ANSWERED*sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as Quality,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYRATE,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYMENT,
	NFS,NO,YES,NFS_NFS,NFS_NO,NFS_YES,NO_NFS,NO_NO,NO_YES,YES_NFS,YES_NO,YES_YES from (select 
    Sentence_User.username as USERNAME, profession as PROFESSION, 
	
	round((pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2))/(3*2), 3) as RANK_E,

	round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
	+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
	
	sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
	sum(if(Sentence_User.response = -2, 1, 0)) as SKIPPED,
	avg(if(Sentence_User.response != -2, length, null)) as LEN,
	
	sum(if(response = -1,1,0)) as NFS,
	sum(if(response = 0,1,0)) as NO,
	sum(if(response = 1,1,0)) as YES,

    sum(if(screening = -1 and response = -1, 1, 0)) as NFS_NFS,
	sum(if(screening = -1 and response = 0, 1, 0)) as NFS_NO,
	sum(if(screening = -1 and response = 1, 1, 0)) as NFS_YES,
	sum(if(screening = 0 and response = -1, 1, 0)) as NO_NFS,
	sum(if(screening = 0 and response = 0, 1, 0)) as NO_NO,
	sum(if(screening = 0 and response = 1, 1, 0)) as NO_YES,
	sum(if(screening = 1 and response = -1, 1, 0)) as YES_NFS,
	sum(if(screening = 1 and response = 0, 1, 0)) as YES_NO,
	sum(if(screening = 1 and response = 1, 1, 0)) as YES_YES
from
    Sentence_User,
    Sentence,
	User
where
    id = sentence_id and
	Sentence_User.username = User.username and
	Sentence_User.username != "factchecker" and	
	Sentence_User.time >= "2017-01-11 18:00:00" and
	sentence_id not in '.$training_sentences.'
group by Sentence_User.username) A order by PAYMENT desc;';
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
#	var_dump($results);

	$ID = 1;
	echo '<br><br>';
	echo '<table border = "1">';
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	
	foreach($results as $key=>$v)
	{
		if(in_array($v['USERNAME'], $active_users))
		{
			echo '<tr bgcolor="#8DDF00">';	
		}
		else echo '<tr>';
		echo '<td>'.$ID.'</td>';
		foreach($v as $k=>$value) {
			echo '<td>'.$value.'</td>';
		}
		echo '</tr>';
		$ID = $ID + 1;
	}
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	echo '</table>';
	
	
	
	
	echo '<br><br>Fourth Phase Statistics [After 2016-05-08 00:00:00]';

$sql = 'select USERNAME,PROFESSION,RANK_E,RANK_W,ANSWERED as "#", A.SKIPPED/A.ANSWERED as SKIP, A.LEN,round(A.LEN/18.4217*A.ANSWERED*sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as Quality,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYRATE,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYMENT,
	NFS,NO,YES,NFS_NFS,NFS_NO,NFS_YES,NO_NFS,NO_NO,NO_YES,YES_NFS,YES_NO,YES_YES from (select 
    Sentence_User.username as USERNAME, profession as PROFESSION, 
	
	round((pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2))/(3*2), 3) as RANK_E,

	round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
	+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
	
	sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
	sum(if(Sentence_User.response = -2, 1, 0)) as SKIPPED,
	avg(if(Sentence_User.response != -2, length, null)) as LEN,
	
	sum(if(response = -1,1,0)) as NFS,
	sum(if(response = 0,1,0)) as NO,
	sum(if(response = 1,1,0)) as YES,

    sum(if(screening = -1 and response = -1, 1, 0)) as NFS_NFS,
	sum(if(screening = -1 and response = 0, 1, 0)) as NFS_NO,
	sum(if(screening = -1 and response = 1, 1, 0)) as NFS_YES,
	sum(if(screening = 0 and response = -1, 1, 0)) as NO_NFS,
	sum(if(screening = 0 and response = 0, 1, 0)) as NO_NO,
	sum(if(screening = 0 and response = 1, 1, 0)) as NO_YES,
	sum(if(screening = 1 and response = -1, 1, 0)) as YES_NFS,
	sum(if(screening = 1 and response = 0, 1, 0)) as YES_NO,
	sum(if(screening = 1 and response = 1, 1, 0)) as YES_YES
from
    Sentence_User,
    Sentence,
	User
where
    id = sentence_id and
	Sentence_User.username = User.username and
	Sentence_User.username != "factchecker" and	
	Sentence_User.time >= "2016-05-08 00:00:00" and
	sentence_id not in '.$training_sentences.'
group by Sentence_User.username) A order by PAYMENT desc;';
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
#	var_dump($results);

	$ID = 1;
	echo '<br><br>';
	echo '<table border = "1">';
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	
	foreach($results as $key=>$v)
	{
		if(in_array($v['USERNAME'], $active_users))
		{
			echo '<tr bgcolor="#8DDF00">';	
		}
		else echo '<tr>';
		echo '<td>'.$ID.'</td>';
		foreach($v as $k=>$value) {
			echo '<td>'.$value.'</td>';
		}
		echo '</tr>';
		$ID = $ID + 1;
	}
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	echo '</table>';
	
	echo '<br><br>Training Statistics [After 2016-05-08 00:00:00]';

$sql = 'select USERNAME,PROFESSION,RANK_E,RANK_W,ANSWERED as "#", A.SKIPPED/A.ANSWERED as SKIP, A.LEN,round(A.LEN/18.4217*A.ANSWERED*sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as Quality,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYRATE,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYMENT,
	NFS,NO,YES,NFS_NFS,NFS_NO,NFS_YES,NO_NFS,NO_NO,NO_YES,YES_NFS,YES_NO,YES_YES from (select 
    Sentence_User.username as USERNAME, profession as PROFESSION, 
	
	round((pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2))/(3*2), 3) as RANK_E,

	round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
	+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
	
	sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
	sum(if(Sentence_User.response = -2, 1, 0)) as SKIPPED,
	avg(if(Sentence_User.response != -2, length, null)) as LEN,
	
	sum(if(response = -1,1,0)) as NFS,
	sum(if(response = 0,1,0)) as NO,
	sum(if(response = 1,1,0)) as YES,

    sum(if(screening = -1 and response = -1, 1, 0)) as NFS_NFS,
	sum(if(screening = -1 and response = 0, 1, 0)) as NFS_NO,
	sum(if(screening = -1 and response = 1, 1, 0)) as NFS_YES,
	sum(if(screening = 0 and response = -1, 1, 0)) as NO_NFS,
	sum(if(screening = 0 and response = 0, 1, 0)) as NO_NO,
	sum(if(screening = 0 and response = 1, 1, 0)) as NO_YES,
	sum(if(screening = 1 and response = -1, 1, 0)) as YES_NFS,
	sum(if(screening = 1 and response = 0, 1, 0)) as YES_NO,
	sum(if(screening = 1 and response = 1, 1, 0)) as YES_YES
from
    Sentence_User,
    Sentence,
	User
where
    id = sentence_id and
	Sentence_User.username = User.username and
	Sentence_User.username != "factchecker" and	
	Sentence_User.time >= "2016-05-08 00:00:00" and
	sentence_id in '.$training_sentences.'
group by Sentence_User.username) A order by PAYMENT desc;';
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
#	var_dump($results);

	$ID = 1;
	echo '<br><br>';
	echo '<table border = "1">';
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	
	foreach($results as $key=>$v)
	{
		if(in_array($v['USERNAME'], $active_users))
		{
			echo '<tr bgcolor="#8DDF00">';	
		}
		else echo '<tr>';
		echo '<td>'.$ID.'</td>';
		foreach($v as $k=>$value) {
			echo '<td>'.$value.'</td>';
		}
		echo '</tr>';
		$ID = $ID + 1;
	}
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	echo '</table>';


	echo '<br><br>Overall Statistics';

$sql = 'select USERNAME,PROFESSION,RANK_E,RANK_W,ANSWERED as "#", A.SKIPPED/A.ANSWERED as SKIP, A.LEN,round(A.LEN/18.4217*A.ANSWERED*sign(0.3 - A.RANK_W)*pow((0.3 - A.RANK_W),2),2) AS RANK_L,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) ),2), 0) as Quality,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYRATE,
	if(ANSWERED >= 50, round(if( RANK_W <= 0, 3-7*RANK_W/0.2, if(RANK_W<=0.3, pow((0.3-RANK_W)/0.3, 2.5)*3, 0) )*pow((A.LEN/18.4217), 1.5)*ANSWERED/100*pow(0.6, A.SKIPPED/A.ANSWERED),2), 0) as PAYMENT,
	NFS,NO,YES,NFS_NFS,NFS_NO,NFS_YES,NO_NFS,NO_NO,NO_YES,YES_NFS,YES_NO,YES_YES from (select 
    Sentence_User.username as USERNAME, profession as PROFESSION, 
	
	round((pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = -1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 0, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = -1 and response = 1, 1, 0))/sum(if(screening = -1 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = -1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = -1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 0, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 0, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2)+
	pow(sum(if(screening = 0 and response = 1, 1, 0))/sum(if(screening = 0 and response != -2, 1, 0))-sum(if(screening = 1 and response = 1, 1, 0))/sum(if(screening = 1 and response != -2, 1, 0)),2))/(3*2), 3) as RANK_E,

	round(-0.2*(sum(if(screening = -1 and response = -1, 1, 0))+sum(if(screening = 0 and response = 0, 1, 0))+sum(if(screening = 1 and response = 1, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = 0 and response = 1, 1, 0))+sum(if(screening = 1 and response = 0, 1, 0)))/(sum(screening != -3 and response != -2))
	+0.7*(sum(if(screening = -1 and response = 0, 1, 0))+sum(if(screening = 0 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2))
	+2.5*(sum(if(screening = -1 and response = 1, 1, 0))+sum(if(screening = 1 and response = -1, 1, 0)))/(sum(screening != -3 and response != -2)), 3) as RANK_W,
	
	sum(if(Sentence_User.response != -2, 1, 0)) as ANSWERED,
	sum(if(Sentence_User.response = -2, 1, 0)) as SKIPPED,
	avg(if(Sentence_User.response != -2, length, null)) as LEN,
	
	sum(if(response = -1,1,0)) as NFS,
	sum(if(response = 0,1,0)) as NO,
	sum(if(response = 1,1,0)) as YES,

    sum(if(screening = -1 and response = -1, 1, 0)) as NFS_NFS,
	sum(if(screening = -1 and response = 0, 1, 0)) as NFS_NO,
	sum(if(screening = -1 and response = 1, 1, 0)) as NFS_YES,
	sum(if(screening = 0 and response = -1, 1, 0)) as NO_NFS,
	sum(if(screening = 0 and response = 0, 1, 0)) as NO_NO,
	sum(if(screening = 0 and response = 1, 1, 0)) as NO_YES,
	sum(if(screening = 1 and response = -1, 1, 0)) as YES_NFS,
	sum(if(screening = 1 and response = 0, 1, 0)) as YES_NO,
	sum(if(screening = 1 and response = 1, 1, 0)) as YES_YES
from
    Sentence_User,
    Sentence,
	User
where
    id = sentence_id and
	Sentence_User.username = User.username and
	Sentence_User.username != "factchecker" and
	sentence_id not in '.$training_sentences.'
group by Sentence_User.username) A order by PAYMENT desc;';
	$results = execute($sql, array(), PDO::FETCH_ASSOC);
#	var_dump($results);

	$ID = 1;
	echo '<br><br>';
	echo '<table border = "1">';
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	
	foreach($results as $key=>$v)
	{
		if(in_array($v['USERNAME'], $active_users))
		{
			echo '<tr bgcolor="#8DDF00">';	
		}
		else echo '<tr>';
		echo '<td>'.$ID.'</td>';
		foreach($v as $k=>$value) {
			echo '<td>'.$value.'</td>';
		}
		echo '</tr>';
		$ID = $ID + 1;
	}
	echo '<tr>';
	echo '<th>'.'ID'.'</th>';
	foreach($results[0] as $key=>$value)
	{
		if(strcmp($key, 'PAYRATE') == 0)echo '<th>'.'PAYRATE_(&cent;)'.'</th>';
		else if(strcmp($key, 'PAYMENT') == 0)echo '<th>'.'PAYMENT_($)'.'</th>';
		else echo '<th>'.$key.'</th>';
	}
	echo '</tr>';
	echo '</table>';
?>
