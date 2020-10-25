<?php
	$record_count = 0;
	$file_str = array();
	$LINE_PER_RECORD = 8;
	$err_str = "";

	/* ファイル入力ボタン */
	if(@$_POST['file_input']) {

//		１．テキストファイルを開く
		$fp = fopen("input/match.txt", "r");
		while(($line = fgets($fp)))
		{
			$str_pos1 = mb_strpos ($line,"\t" ,0);
			$str_pos2 = mb_strpos ($line,"\t" ,$str_pos1 + 1);
			$file_str[] = mb_substr ( $line , 0, $str_pos1);
			if($str_pos2 == FALSE)
			{
				$file_str[] = mb_substr ( $line , $str_pos1 + 1);
			}
			else
			{
				$file_str[] = mb_substr ( $line , $str_pos1 + 1, $str_pos2 - ($str_pos1 + 1));
				$file_str[] = mb_substr ( $line , $str_pos2 + 1);
			}
		}

		fclose($fp);

		$record_count = count($file_str) / $LINE_PER_RECORD;
	} else {
		/* 処理なし */
	}
	/* 登録ボタン */
	if(@$_POST['ent_match']) {
		$fighter_name				= htmlspecialchars($_POST['fighter_name']);
		$op_fig_name;
		if ($fighter_name != null) {

			$fp = fopen("input/match.txt", "r");
			while(($line = fgets($fp)))
			{
				$str_pos1 = mb_strpos ($line,"\t" ,0);
				$str_pos2 = mb_strpos ($line,"\t" ,$str_pos1 + 1);
				$file_str[] = mb_substr ( $line , 0, $str_pos1);
				if($str_pos2 == FALSE)
				{
					$file_str[] = mb_substr ( $line , $str_pos1 + 1);
				}
				else
				{
					$file_str[] = mb_substr ( $line , $str_pos1 + 1, $str_pos2 - ($str_pos1 + 1));
					$file_str[] = mb_substr ( $line , $str_pos2 + 1);
				}
			}

			fclose($fp);

			$record_count = count($file_str) / $LINE_PER_RECORD;

			//データベースOpen
			$pdo = new PDO("mysql:dbname=mma_analysis","root");


			for($i = 0 ; $i < $record_count; $i++)
			{
				$offset = $i * $LINE_PER_RECORD;

				$result = $file_str[0 + $offset];
				if( $result == "WIN" ) {
					$win_name = $fighter_name;
					$lose_name = $file_str[1 + $offset];
					$op_fig_name = $lose_name;
				//負けとそれ以外のケース
				} else {
					$win_name = $file_str[1 + $offset];
					$lose_name = $fighter_name;
					$op_fig_name = $win_name;
				}

				$match_name = $file_str[2 + $offset];
				$dec_d = $file_str[4 + $offset];
				//引き分けの場合
				if ($result == "DRAW") {
					//決着内容にDrawの文字が無い
					if ((mb_strpos ($dec_d,"Draw" ,0)) == FALSE) {
						$dec_d = "Draw ".$dec_d;
					}
				}
				$referee = $file_str[5 + $offset];
				$dec_r = $file_str[6 + $offset];
				$dec_t = $file_str[7 + $offset];

				$m_date = $file_str[3 + $offset];;
				$year_chr 	= mb_substr($m_date, 11 ,4);
				$month_chr 	= mb_substr($m_date, 0 ,3);
				$day_chr 		= mb_substr($m_date, 6 ,2);

				switch($month_chr) {
					case "Jan";	//1月
						$cnv_month_chr = "01";
						break;
					case "Feb";	//2月
						$cnv_month_chr = "02";
						break;
					case "Mar";	//3月
						$cnv_month_chr = "03";
						break;
					case "Apr";	//4月
						$cnv_month_chr = "04";
						break;
					case "May";	//5月
						$cnv_month_chr = "05";
						break;
					case "Jun";	//6月
						$cnv_month_chr = "06";
						break;
					case "Jul";	//7月
						$cnv_month_chr = "07";
						break;
					case "Aug";	//8月
						$cnv_month_chr = "08";
						break;
					case "Sep";	//9月
						$cnv_month_chr = "09";
						break;
					case "Oct";	//10月
						$cnv_month_chr = "10";
						break;
					case "Nov";	//11月
						$cnv_month_chr = "11";
						break;
					case "Dec";	//12月
						$cnv_month_chr = "12";
						break;
					default;
						break;
				}

				if( mb_strlen($month_chr) == 1) {
					$month_chr = "0".$month_chr;
				}
				if( mb_strlen($day_chr) == 1) {
					$day_chr = "0".$day_chr;
				}
				$date_chr = $year_chr."-".$cnv_month_chr."-".$day_chr;

				//重複チェック
				//勝者、敗者、大会日時が同一のレコード件数取得
				$sql = "SELECT count(*) FROM match_data WHERE ".
				       "(win_name = '".addslashes($fighter_name)."' OR lose_name = '".addslashes($fighter_name)."') AND ".
							 "(win_name = '".addslashes($op_fig_name)."' OR lose_name = '".addslashes($op_fig_name)."') AND ".
							 "match_date = '$date_chr' AND "."match_name = '".addslashes($match_name)."'";

				$st = $pdo->query($sql);

				if($st->fetchColumn())
				{
					echo "重複あり";
					echo "</br>";

				} else {
					//レコード追加
					$st = $pdo->prepare("INSERT INTO match_data(win_name,lose_name,decision,Referee,decision_round,decision_time,match_name,match_date) VALUES(?,?,?,?,?,?,?,?)");
					$st->execute(array($win_name,$lose_name,$dec_d,$referee,$dec_r,$dec_t,$match_name,$date_chr));
				}
			}
		} else {
			$err_str = "選手名が未入力";
		}

	} else {
		/* 処理なし */
	}
?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>大会入力</title>
		<link rel="stylesheet" type="text/css" href="css/match_list.css">
	</head>
	<body>
		<form action='match_list_insert.php' method="post">
			選手名<input type="text" name="fighter_name" value=""><?php echo "$err_str" ?><br>
			<!-- 登録ボタン -->
			<input type="submit" name="ent_match" value="登録">
		</form>
		<form action="match_list_insert.php" method="post">
			<input type="submit" name="file_input" value="ファイル読み込み">
		</form>
		<a href="../index.html">トップページへもどる</a><br>

		<table border="1">
	 	 <tr><th>RESULT</th><th>FIGHTER</th><th>EVENT</th><th>DATE</th><th>METHOD</th><th>REFEREE</th><th>R</th><th>TIME</th>
	 	 <?php

		 	$record_count = count($file_str) / $LINE_PER_RECORD;

	 		for($i = 0 ; $i < $record_count; $i++)
	 		{
				$offset = $i * $LINE_PER_RECORD;

				/* 試合結果で背景色を変える */
				$result = $file_str[0 + $offset];
				if( $result == "WIN" ) {
					$bg_class = "table_win";
				} else if( $result == "LOSS" ) {
					$bg_class = "table_lose";
				} else if( $result == "NC" ) {
					$bg_class = "table_nc";
				} else {
					/* 引き分けとそれ以外のケース */
					$bg_class = "table_nc";
				}
				echo "<tr>";
				echo "<td class = $bg_class>{$result}</td>";
				echo "<td class = $bg_class>{$file_str[1 + $offset]}</td>";
				echo "<td class = $bg_class>{$file_str[2 + $offset]}</td>";
				echo "<td class = $bg_class>{$file_str[3 + $offset]}</td>";
				echo "<td class = $bg_class>{$file_str[4 + $offset]}</td>";
				echo "<td class = $bg_class>{$file_str[5 + $offset]}</td>";
				echo "<td class = $bg_class>{$file_str[6 + $offset]}</td>";
				echo "<td class = $bg_class>{$file_str[7 + $offset]}</td>";

				echo "</tr>";
			}
	 	 ?>
	  </table>
	</body>
</html>
