<?php

	require "fighter_class.php";

	$fig_name 		= $_GET['fig_name'];
	$fig_country 	= $_GET['fig_cont'];

	$fig_data = new fighter_match_data($fig_name);

	$fig_record = $fig_data->get_win_count()."勝".$fig_data->get_lose_count()."敗";

	$d_count = $fig_data->get_drow_count();
	if($d_count != 0) {
		$fig_record = $fig_record.$d_count."分";
	}

	$nc_count = $fig_data->get_nc_count();
	if($nc_count != 0) {
		$fig_record = $fig_record.$nc_count."NC";
	}

	$fig_finish = "";
	$ko_count = $fig_data->get_ko_count();
	if($ko_count != 0) {
		$fig_finish = $ko_count."KO/TKO ";
	}

	$sub_count = $fig_data->get_sub_count();
	if($sub_count != 0) {
		$fig_finish = $fig_finish.$sub_count."Sub ";
	}
	$dec_count = $fig_data->get_dec_count();
	if($dec_count != 0) {
		$fig_finish = $fig_finish.$dec_count."dec";
	}

	$fig_finish_rate = "";

	if( $fig_finish != "") {
		$fig_finish_rate = "(".$fig_finish."：試合決定率".$fig_data->get_finish_rate()."%";
		$fig_finish_rate = $fig_finish_rate.")";
	}

	$opp_rate_ave = "対戦相手の平均勝率".$fig_data->get_opp_rate_ave()."%";
 ?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
	<head>
		<meta charset="utf-8">
		<title><?php echo $fig_name ?></title>
		<link rel="stylesheet" type="text/css" href="css/fighter_details.css">
	</head>
	<body>
		<a href="fighter_list.php">選手一覧に戻る</a>
		<h3><?php echo $fig_name."：".$fig_country ?></h3>
		<h3> <?php echo $fig_record.$fig_finish_rate ?></h3>
		<h3> <?php echo $opp_rate_ave ?></h3>
		<table border="1">
	 	 <tr><th>試合名</th><th>試合カテゴリ</th><th>対戦相手</th><th>対戦相手国籍</th><th>結果</th><th>決着</th><th>時間</th><th>詳細</th><th>大会名</th><th>日時</th>
		 <th>対戦相手の戦績</th><th>対戦相手の勝率</th></tr>
	 	 <?php
		 	for ($i = 0;$i < $fig_data->get_d_match_num();$i++) {

				/* 試合結果で背景色を変える */
				if( $fig_data->get_d_match_result($i) == "〇" ) {
					$bg_class = "table_win";
				} else if( $fig_data->get_d_match_result($i) == "×" ) {
					$bg_class = "table_lose";
				} else if( $fig_data->get_d_match_result($i) == "NC" ) {
					$bg_class = "table_nc";
				} else {
					/* 引き分けとそれ以外のケース */
					$bg_class = "";
				}

				echo "<tr>";
				echo "<td class = $bg_class>{$fig_data->get_d_match_class($i)}</td>";
				echo "<td class = $bg_class>{$fig_data->get_d_match_category($i)}</td>";
				echo "<td class = $bg_class><a href = 'fighter_details.php?fig_name={$fig_data->get_d_opp_name($i)}&fig_cont={$fig_data->get_d_opp_country($i)}'>{$fig_data->get_d_opp_name($i)}</a></td>";
				echo "<td class = $bg_class>{$fig_data->get_d_opp_country($i)}</td>";
				echo "<td class = 'table_center {$bg_class}'>{$fig_data->get_d_match_result($i)}</td>";
				echo "<td class = $bg_class>{$fig_data->get_d_match_decision($i)}</td>";
				echo "<td class = $bg_class>{$fig_data->get_d_match_r_time($i)}</td>";
				echo "<td class = $bg_class>{$fig_data->get_d_match_result_details($i)}</td>";
				echo "<td class = $bg_class><a href = '../match_result/match_result.php?match_no={$fig_data->get_d_match_no($i)}'>{$fig_data->get_d_match_name($i)}</a></td>";
				echo "<td class = $bg_class>{$fig_data->get_d_match_date($i)}</td>";
				echo "<td class = $bg_class>{$fig_data->get_d_opp_record($i)}</td>";
				echo "<td class = $bg_class>{$fig_data->get_d_opp_rate($i)}</td>";

				echo "</tr>";
			}
	 	 ?>
	  </table>
	</body>
</html>
