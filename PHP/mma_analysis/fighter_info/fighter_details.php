<?php
	require "fighter_class.php";
	$fig_name = $_GET['fig_name'];

	$fig_info = new c_fighter_info($fig_name);
?>

<!DOCTYPE html>
<html lang="ja" dir="ltr">
	<head>
		<meta charset="utf-8">
		<title><?php echo $fig_name ?></title>
		<link rel="stylesheet" type="text/css" href="css/fighter_info.css">
	</head>
	<body>
		<a href="fighter_list.php">選手一覧に戻る</a>
		<div class = "fig_cont">
			<div class = "fig_info_item_1">
				<p><font size = 6><?php echo $fig_name ?></font><font size = 4>  <?php echo $fig_info->get_nickname() ?></font></p>
				<div class="finish_info" >
					<p><font size = 4><?php echo $fig_info->get_vic_str() ?></font></p>
					<div class = "finish_info_div">
						<div style="<?php echo $fig_info->get_finish_rate_htmsytle("KO") ?>; height:20px; margin: 2px 2px 2px 2px"></div>
						<span class = "absolute"><?php echo $fig_info->get_finish_rate_str("KO") ?></span>
					</div>
					<div class = "finish_info_div">
						<div style="<?php echo $fig_info->get_finish_rate_htmsytle("SUB") ?>; height:20px; margin: 2px 2px 2px 2px"></div>
						<span class = "absolute"><?php echo $fig_info->get_finish_rate_str("SUB") ?></span>
					</div>
					<div class = "finish_info_div">
						<div style="<?php echo $fig_info->get_finish_rate_htmsytle("DEC") ?>; height:20px; margin: 2px 2px 2px 2px"></div>
						<span class = "absolute"><?php echo $fig_info->get_finish_rate_str("DEC") ?></span>
					</div>
				</div>

				<table border="1">
					<tr><th>AGE</th><th>CLASS</th><th>ASSOCIATION</th><th>COUNTRY/AREA</th><th>EXP</th>
					<?php
						echo "<tr>";
						echo "<td>{$fig_info->get_age()}</td>";
						echo "<td>{$fig_info->get_class()}</td>";
						echo "<td>{$fig_info->get_association()}</td>";
						echo "<td>{$fig_info->get_from()}</td>";
						echo "<td>{$fig_info->get_fighter_exp($fig_name)}</td>";
						echo "</tr>";
					?>
				</table>
			</div>
			<div class = "fig_into_item_2">
				<table border="1">
					<tr><th>NOTE</th>
					<?php
						echo "<tr><td>{$fig_info->get_note_info_jpn_match()}</td></tr>";
						echo "<tr><td>{$fig_info->get_note_info_mgr_match()}</td></tr>";
						echo "<tr><td>{$fig_info->get_win_info()}</td></tr>";
						echo "<tr><td>{$fig_info->get_notable_win($fig_name)}</td></tr>";
						echo "<tr><td>{$fig_info->get_notable_loss($fig_name)}</td></tr>";
					?>
				</table>
			</div>
		</div>
		<p></p>
		<table border="1">
	 	 <tr><th>RESULT</th><th>FIGHTER</th><th>EVENT</th><th>DATE</th><th>METHOD</th><th>REFEREE</th><th>R</th><th>TIME</th><th>EXP</th>
	 	 <?php
		 for ($i = 0;$i < $fig_info->get_match_num();$i++) {
			 $result = $fig_info->get_match_result($i);
			 /* 試合結果で背景色を変える */
			 //引き分け
			 if($result == "DRAW") {
				$bg_class = "table_nc";
			 //ノーコンテスト
			 } else if($result == "NC") {
			 	$bg_class = "table_nc";
			 //勝利
			 } else if($result == "WIN") {
		 		$bg_class = "table_win";
		  //敗北
			 } else if($result == "LOSS") {
			 $bg_class = "table_lose";
			 //こないはず、エラーとわかるようにする。
			 } else {
		 		 $bg_class = "table_error";
			 }
			 echo "<tr>";
			 echo "<td class = $bg_class>{$result}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_match_op_fig($i)}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_match_name($i)}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_match_date($i)}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_match_decision($i)}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_match_referee($i)}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_match_decision_round($i)}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_match_decision_time($i)}</td>";
			 echo "<td class = $bg_class>{$fig_info->get_op_fig_exp($i)}</td>";
			 echo "</tr>";

			}
	 	 ?>
	  </table>
	</body>
</html>
