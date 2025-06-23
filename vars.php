<?php

/* List of training sentences sql = (select sentence_id from Training) */
$training_sentences = '(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16)'; /*Feb 08, 2023*/

/* Average length of pairs, exclude training and non-active pairs */
$sql = 'SELECT avg(length(claim))+avg(length(tweet)) FROM Sentence WHERE id not in (select sentence_id from Training);';
$avg_len = execute($sql, array(), PDO::FETCH_COLUMN)[0];

/* Find sentences that need annotations. They are not screening or training sentences. */
$sql = 'SELECT	id
		FROM	Sentence
		WHERE 	screening = -3;';
$todo_sentence = execute($sql, array(), PDO::FETCH_COLUMN);
$todo_sentence_string = '("'.implode('","', $todo_sentence).'")';

/* Find $top_participants */
$top_participants_sql = 'SELECT su.username as USERNAME
		FROM		Sentence_User su,
					Sentence s
		WHERE		s.id = su.sentence_id and 
					su.response != -2 and
					s.id not in (select sentence_id from Training)
					and s.is_active=1
		GROUP BY 	su.username
		HAVING		-0.20*	(	sum(if(screening = 0 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 3, 1, 0))+ sum(if(screening = -1 and response = -1, 1, 0)) ) / ( sum(screening != -3 and response != -2))
					+0.50 *	(	sum(if(screening = 0 and response = 3, 1, 0))+ sum(if(screening = 1 and response = 0, 1, 0))+ sum(if(screening = 1 and response = 2, 1, 0))+ sum(if(screening = 2 and response = 1, 1, 0))+ sum(if(screening = 2 and response = 3, 1, 0))+ sum(if(screening = 3 and response = 2, 1, 0))+ sum(if(screening = 3 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 3, 1, 0)) ) / ( sum(screening != -3 and response != -2))
					+0.50*	(	sum(if(screening = 0 and response = 2, 1, 0))+ sum(if(screening = 1 and response = 3, 1, 0))+ sum(if(screening = 2 and response = 0, 1, 0))+ sum(if(screening = 2 and response = -1, 1, 0))+ sum(if(screening = 3 and response = 1, 1, 0))+ sum(if(screening = -1 and response = 2, 1, 0))+ sum(if(screening = 3 and response = 0, 1, 0))+ sum(if(screening = -1 and response = 0, 1, 0)) ) / (sum(screening != -3 and response != -2))
					+1.00*	(	sum(if(screening = 0 and response = 1, 1, 0))+ sum(if(screening = 0 and response = -1, 1, 0)) ) / (sum(screening != -3 and response != -2))
					+2.00*	(	sum(if(screening = 1 and response = -1, 1, 0))+ sum(if(screening = -1 and response = 1, 1, 0)) ) / (sum(screening != -3 and response != -2)) <= 0.0 
					and count(*) >= 10;
';	
$top_participants = execute($top_participants_sql, array(), PDO::FETCH_COLUMN);
$top_participants_string = '("'.implode('","', $top_participants).'")';

/* Find $all_participants */
$sql = 'SELECT 	count(*) from User;';	
$all_participants = execute($sql, array(), PDO::FETCH_COLUMN)[0];
	
/* All labels collected, excluding those on screening and skipped sentences */
$sql = 'SELECT	count(*) 
		FROM	Sentence_User su, 
				Sentence s
		WHERE 	su.sentence_id = s.id and 
				s.screening = - 3 and 
				su.response != - 2;';
$total_labels = execute($sql, array(), PDO::FETCH_COLUMN);

/* All labels collected from top-quality participants, excluding those on screening sentences */
$sql = 'SELECT	count(*) 
		FROM	Sentence_User su, 
				Sentence s
		WHERE 	su.sentence_id = s.id and 
				s.screening = -3 and 
				su.response != - 2 and 
				su.username in '.$top_participants_string.';';
$total_top_labels = execute($sql, array(), PDO::FETCH_COLUMN);

/* Find sentences for which annotations are considered accomplished.  --> top_quality_sentences */
$sql = 'SELECT		A.sentence_id /*-- , A.screening, A.Label_0, A.Label_1, A.Label_2, A.Label_3, A.Label_4 -- , "TOP_QUALITY_SENTENCES01"*/
		FROM		(select 	su.sentence_id, s.screening,
								sum(if(su.response = -1, 1, 0)) as Label_0, 
								sum(if(su.response = 0, 1, 0)) as Label_1,	
								sum(if(su.response = 1, 1, 0)) as Label_2,	
								sum(if(su.response = 2, 1, 0)) as Label_3,	
								sum(if(su.response = 3, 1, 0)) as Label_4
					from		Sentence_User as su, Sentence s 
					where		s.id = su.sentence_id and
								s.screening = -3 and
								su.sentence_id not in (select sentence_id from Training) and
								su.username in '.$top_participants_string.'
					group by	sentence_id
					having		(Label_0 >= 3 and	Label_0 >= 2+Label_1	and	Label_0 >= 2+Label_2	and	Label_0 >= 2+Label_3	and	Label_0 >= 2+Label_4 	and	Label_0 >= round((Label_1 + Label_2 + Label_3) / 2, 1) ) or
								(Label_1 >= 3 and	Label_1 >= 2+Label_0	and	Label_1 >= 2+Label_2	and	Label_1 >= 2+Label_3	and	Label_1 >= 2+Label_4 	and	Label_1 >= round((Label_0 + Label_2 + Label_3) / 2, 1) ) or
								(Label_2 >= 3 and	Label_2 >= 2+Label_0	and	Label_2 >= 2+Label_1	and	Label_2 >= 2+Label_3	and	Label_2 >= 2+Label_4 	and	Label_2 >= round((Label_0 + Label_1 + Label_3) / 2, 1) ) or
								(Label_3 >= 3 and	Label_3 >= 2+Label_0	and	Label_3 >= 2+Label_1	and	Label_3 >= 2+Label_2	and	Label_3 >= 2+Label_4 	and	Label_3 >= round((Label_0 + Label_1 + Label_2) / 2, 1) ) or
								(Label_4>0) 
					) A';

$top_quality_sentences = execute($sql, array(), PDO::FETCH_COLUMN);
$top_quality_sentences_string = '("'.implode('","', $top_quality_sentences).'")';

$top_finished_labels = '
SELECT	sentence_id, 
	@highest_val:=greatest(Label_0, Label_1, Label_2, Label_3, Label_4) as Final, -- , "TOP_QUALITY_SENTENCES01"*/
	CASE
    WHEN Label_4 > 0 THEN 4
    ELSE
      CASE
		WHEN Label_0 >= Label_1 AND Label_0 >= Label_2 AND Label_0 >= Label_3 THEN 0
        WHEN Label_1 >= Label_2 AND Label_1 >= Label_3 THEN 1
        WHEN Label_2 >= Label_3 THEN 2
        ELSE 3
      END
  	END AS label
FROM	(select 	su.sentence_id, s.screening, s.subset,
			sum(if(su.response = -1, 1, 0)) as Label_0, 
			sum(if(su.response = 0, 1, 0)) as Label_1,	
			sum(if(su.response = 1, 1, 0)) as Label_2,	
			sum(if(su.response = 2, 1, 0)) as Label_3,	
			sum(if(su.response = 3, 1, 0)) as Label_4
	from		Sentence_User as su, Sentence s 
	where		s.id = su.sentence_id
				and s.screening = -3
				and su.sentence_id not in (select sentence_id from Training)
				and su.username in '.$top_participants_string.'
	group by	sentence_id
	having		(Label_0 >= 3 and	Label_0 >= 2+Label_1	and	Label_0 >= 2+Label_2	and	Label_0 >= 2+Label_3	and	Label_0 >= 2+Label_4 	and	Label_0 >= round((Label_1 + Label_2 + Label_3) / 2, 1) ) or
				(Label_1 >= 3 and	Label_1 >= 2+Label_0	and	Label_1 >= 2+Label_2	and	Label_1 >= 2+Label_3	and	Label_1 >= 2+Label_4 	and	Label_1 >= round((Label_0 + Label_2 + Label_3) / 2, 1) ) or
				(Label_2 >= 3 and	Label_2 >= 2+Label_0	and	Label_2 >= 2+Label_1	and	Label_2 >= 2+Label_3	and	Label_2 >= 2+Label_4 	and	Label_2 >= round((Label_0 + Label_1 + Label_3) / 2, 1) ) or
				(Label_3 >= 3 and	Label_3 >= 2+Label_0	and	Label_3 >= 2+Label_1	and	Label_3 >= 2+Label_2	and	Label_3 >= 2+Label_4 	and	Label_3 >= round((Label_0 + Label_1 + Label_2) / 2, 1) ) or
				(Label_4>0) 
	) B';
// $top_finished_labels = execute($top_finished_labels_sql, array(), PDO::FETCH_COLUMN);
// $top_finished_labels = '("'.implode('","', $top_finished_labels).'")';
?>
