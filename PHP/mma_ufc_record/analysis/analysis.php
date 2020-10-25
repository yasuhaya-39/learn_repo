<?php
	if(@$_POST['ent_menu']) {

		$menu = $_POST['select_menu'];

		$pdo = new PDO("mysql:dbname=mma_record","root");

		if( $menu == "victory" ) {
			$array_country = array();
			$array_w_count = array();
			$array_l_count = array();
			$array_w_rate	 = array();

			$sql = "SELECT * FROM fig_country";
			$st_country = $pdo->query($sql);
			while($row = $st_country->fetch()) {
				$country = htmlspecialchars($row['country']);
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE win_cont='{$country}' AND decision !='引き分け'";
				$st = $pdo->query($sql);
				$w_count = $st->fetchColumn();

				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE lose_cont='{$country}' AND decision !='引き分け'";
				$st = $pdo->query($sql);
				$l_count = $st->fetchColumn();

				$array_country[] = $country;
				$array_w_count[] = $w_count;
				$array_l_count[] = $l_count;
				$array_w_rate[]  = floor(($w_count / ($w_count + $l_count)) * 100 );
			}

		} else if($menu == "player") {

		}
	} else {
		$menu = "";
	}

?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>分析ページ</title>
	</head>
	<body>
		<a href="../match_list/match_list.php?match=UFC">大会一覧へ戻る</a>
		<form action="analysis.php" method="post">
			<p>解析項目の選択：<br>
			<select name="select_menu">
			<option value="victory">国ごとの勝率</option>
			<option value="player">国ごとの選手数と割合</option>
			</select></p>
			<input type="submit" name="ent_menu" value="決定">
		</form>

		<?php
			echo "<table border=\"1\">";
			if($menu == "victory") {
				echo "<tr><th>国</th><th>勝</th><th>負</th><th>勝率</th></tr>";

				for($i = 0;$i < count($array_country);$i++) {
					echo "<tr>";
					echo "<td>$array_country[$i]</td>";
					echo "<td>$array_w_count[$i]</td>";
					echo "<td>$array_l_count[$i]</td>";
					echo "<td>$array_w_rate[$i]</td>";
					echo "</tr>";
				}
			}
			echo "</table>";
		?>
	</body>
</html>
