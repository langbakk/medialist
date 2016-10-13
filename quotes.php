<?php

$quotes = [
				1 => [
					1 => "... my mind is playing tricks on me - I am not as stable as I used to be ...",
					2 => "pain",
					3 => "shut your mouth"
					],
				2 => [
					1 => "... sometimes I'm good. but when I'm bad I'm even better! ...",
					2 => "aerosmith",
					3 => "falling in love"
					],
				3 => [
					1 => "... everybody knows that you've been faithful, ah give or take a night or two ...",
					2 => "leonard cohen",
					3 => "everybody knows"
					],
				4 => [
					1 => "... I walk the thinnest line between the good and bad sides of my mind ...",
					2 => "dead milkmen",
					3 => "the thinnest line"
					],
				5 => [
					1 => "... I'd rather laugh with the sinners than cry with the saints.<br> Sinners are much more fun ...",
					2 => "billy joel",
					3 => "only the good die young"
					],
				6 => [
					1 => "... this could be heaven or this could be hell ...",
					2 => "eagles",
					3 => "hotel california"
					],
				7 => [
					1 => "... they had one thing in common they were good in bed ...",
					2 => "eagles",
					3 => "life in the fast lane"
					],
				8 => [
					1 => "... tap dancing on a landmine ...",
					2 => "aerosmith",
					3 => "rag doll"
					],
				9 => [
					1 => "... I want to love you but I better not touch (Don't touch)<br>I want to hold you but my senses tell me to stop<br>I want to kiss you but I want it too much (Too much)<br>I want to taste you but your lips are venomous poison<br>You're poison running through my veins...",
					2 => "alice cooper",
					3 => "poison"
					],
				10 => [
					1 => "... Your hair is tangled and your lipstick is gone<br>You're stretched out, calling my name<br>With just your high heels on...",
					2 => "alice cooper",
					3 => "crawlin"
					],
				11 => [
					1 => "... Don't fear the reaper<br>We'll be able to fly<br>Don't fear the reaper<br>Baby I'm your man...",
					2 => "blue öyster cult",
					3 => "don't fear the reaper"
					],
				12 => [
					1 => "... I've never met a girl like you before<br>You've made me acknowledge the devil in me...",
					2 => "edwyn collins",
					3 => "girl like you"
					],
				13 => [
					1 => "... I've never met a girl like you before<br>You give me just a taste so I want more and more...",
					2 => "edwyn collins",
					3 => "girl like you"
					]
				];

	srand ((double) microtime() * 1000000);
		$randomquote = rand(1,count($quotes)-1);
		$num = (count($quotes));

	echo '<p class="center"><strong>'.$quotes[$randomquote][1].'</strong> <br>'.ucwords($quotes[$randomquote][2].' - '.$quotes[$randomquote][3]).'</p>';

?>