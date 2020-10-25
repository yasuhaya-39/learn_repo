<?php
function calc_fighter_exp($fig_name) {

	$fig_exp = 0;
	$pdo = new PDO("mysql:dbname=mma_analysis","root");

	//データベースから選手名と勝者、敗者が一致するレコードを出す
	$sql = "SELECT * FROM match_data WHERE win_name = '$fig_name' OR lose_name = '$fig_name' ORDER BY match_date DESC";
	$st_match_data = $pdo->query($sql);

	$cmp_name = mb_strtoupper($fig_name);

	while($row_match = $st_match_data->fetch()) {

		$sql = "SELECT * FROM match_score";
		$st_match_score = $pdo->query($sql);

		$plus_point = 1;

		while($row_score = $st_match_score->fetch()) {
			//一致する大会がある
			if(mb_strpos ($row_match['match_name'],$row_score['name']) !== FALSE) {
				//引き分け
				if(mb_strpos ($row_match['decision'],"Draw") !== FALSE) {
					$plus_point =  $row_score['point'];
					break;
				}
				//勝利
				else if ($cmp_name == mb_strtoupper($row_match['win_name'])) {
					$plus_point = floor(($row_score['point'] * 2));
					break;
				}
				//敗北
				else if ($cmp_name == mb_strtoupper($row_match['lose_name'])) {
					$plus_point = $row_score['point'];
					break;
				}
				//それ以外
				else {
					$plus_point = $row_score['point'];
					break;
				}
			}
		}
		$fig_exp = $fig_exp + $plus_point;
	}

	return $fig_exp;
}


function activity_group($fig_name)
{
	$pdo = new PDO("mysql:dbname=mma_analysis","root");

	//データベースから選手名と勝者、敗者が一致するレコードを出す
	$sql = "SELECT * FROM match_data WHERE win_name = '$fig_name' OR lose_name = '$fig_name' ORDER BY match_date DESC LIMIT 1";
	$st_match_data = $pdo->query($sql);

	$cmp_name = mb_strtoupper($fig_name);

	$act_group = "etc";

	while($row_match = $st_match_data->fetch()) {
		$sql = "SELECT * FROM match_score";
		$st_match_score = $pdo->query($sql);

		$plus_point = 1;

		while($row_score = $st_match_score->fetch()) {
			//一致する大会がある
			if(mb_strpos ($row_match['match_name'],$row_score['name']) !== FALSE) {
				$act_group = $row_score['name'];
			}

		}
	}

	return $act_group;
}

function disp_figther_list($w_class)
{

	echo "<div>";
	echo "<p>".$w_class."</p>";
	echo "<table border='1'>";
	echo "<tr><th>Name</th><th>Country</th><th>Record</th><th>EXP</th><th>Group</th></tr>";
	$pdo = new PDO("mysql:dbname=mma_analysis","root");

	//DBから選手一覧
	$sql = "SELECT * FROM fighter_info WHERE w_class = '$w_class' LIMIT 70";
	$st_fig_info = $pdo->query($sql);

	while($row = $st_fig_info->fetch()) {

		$name 		= htmlspecialchars($row['name']);

		//引き分けとNC以外の勝利者と一致するレコード
		$sql = "SELECT count(*) FROM match_data WHERE win_name = '$name' AND decision NOT LIKE 'Draw%' AND decision NOT LIKE 'NC%'";

		$st_match_data = $pdo->query($sql);
		$win_count = $st_match_data->fetchColumn();

		//引き分けとNC以外の勝利者と一致するレコード
		$sql = "SELECT count(*) FROM match_data WHERE lose_name = '$name' AND decision NOT LIKE 'Draw%' AND decision NOT LIKE 'NC%'";
		$st_match_data = $pdo->query($sql);
		$lose_count = $st_match_data->fetchColumn();

		$disp_name = mb_strtoupper($name);

		/* 勝敗 */
		$record_str =  $win_count."勝".$lose_count."敗";

		echo "<tr>";
		echo "<td><a href = 'fighter_details.php?fig_name={$row['name']}'>$disp_name</a></td>";
		echo "<td>{$row['country']}</td>";
		echo "<td>$record_str</td>";
		echo "<td>".calc_fighter_exp($name)."</td>";
		echo "<td>".activity_group($name)."</td>";
		echo "</tr>";

	}
	echo "</table>";
	echo "</div>";
}
?>
