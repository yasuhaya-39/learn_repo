<?php
	$pdo = new PDO("mysql:dbname=mma_record","root");
?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>ランキング</title>
		<link rel="stylesheet" type="text/css" href="css/fighter_ranking.css">
	</head>
	<body>
			<div>
				<h3>勝利数</h3>
				<table border="1">
					<tr><th>順位</th><th>選手名</th><th>国籍</th><th>勝利数</th><th>最終試合年月日</th></tr>

					<?php
						$sql = "SELECT * FROM fighter_list ORDER BY win_count DESC LIMIT 20";
						$st = $pdo->query($sql);
						$rank = 1;
						while($row = $st->fetch()) {
							echo "<tr>";
							echo "<td>{$rank}</td>";
							echo "<td><a href = 'fighter_details.php?fig_name={$row['name']}&fig_cont={$row['country']}'>{$row['name']}</a></td>";
							echo "<td>{$row['country']}</td>";
							echo "<td>{$row['win_count']}</td>";
							echo "<td>{$row['last_match_date']}</td>";
							echo "</tr>";
							$rank++;
						}
					 ?>
				</table>
			</div>
			<div>
				<h3>敗北数</h3>
				<table border="1">
					<tr><th>順位</th><th>選手名</th><th>国籍</th><th>敗北数</th><th>最終試合年月日</th></tr>
					<?php
						$sql = "SELECT * FROM fighter_list ORDER BY lose_count DESC LIMIT 20";
						$st = $pdo->query($sql);
						$rank = 1;
						while($row = $st->fetch()) {
							echo "<tr>";
							echo "<td>{$rank}</td>";
							echo "<td><a href = 'fighter_details.php?fig_name={$row['name']}&fig_cont={$row['country']}'>{$row['name']}</a></td>";
							echo "<td>{$row['country']}</td>";
							echo "<td>{$row['lose_count']}</td>";
							echo "<td>{$row['last_match_date']}</td>";
							echo "</tr>";
							$rank++;
						}
					 ?>
				</table>
			</div>
			<div>
				<table border="1">
					<h3>試合数</h3>
					<tr><th>順位</th><th>選手名</th><th>国籍</th><th>試合数</th><th>最終試合年月日</th></tr>
					<?php
						$sql = "SELECT * FROM fighter_list ORDER BY match_count DESC LIMIT 20";
						$st = $pdo->query($sql);
						$rank = 1;
						while($row = $st->fetch()) {
							echo "<tr>";
							echo "<td>{$rank}</td>";
							echo "<td><a href = 'fighter_details.php?fig_name={$row['name']}&fig_cont={$row['country']}'>{$row['name']}</a></td>";
							echo "<td>{$row['country']}</td>";
							echo "<td>{$row['match_count']}</td>";
							echo "<td>{$row['last_match_date']}</td>";
							echo "</tr>";
							$rank++;
						}
					 ?>
				</table>
			</div>

			<div>
				<h3>KO/TKO勝数</h3>
				<table border="1">
					<tr><th>順位</th><th>選手名</th><th>国籍</th><th>KO・TKO数</th><th>最終試合年月日</th></tr>

					<?php
						$sql = "SELECT * FROM fighter_list ORDER BY ko_count DESC LIMIT 20";
						$st = $pdo->query($sql);
						$rank = 1;
						while($row = $st->fetch()) {
							echo "<tr>";
							echo "<td>{$rank}</td>";
							echo "<td><a href = 'fighter_details.php?fig_name={$row['name']}&fig_cont={$row['country']}'>{$row['name']}</a></td>";
							echo "<td>{$row['country']}</td>";
							echo "<td>{$row['ko_count']}</td>";
							echo "<td>{$row['last_match_date']}</td>";
							echo "</tr>";
							$rank++;
						}
					 ?>
				</table>
			</div>
			<div>
				<h3>一本勝数</h3>
				<table border="1">
					<tr><th>順位</th><th>選手名</th><th>国籍</th><th>一本数</th><th>最終試合年月日</th></tr>
					<?php
						$sql = "SELECT * FROM fighter_list ORDER BY sub_count DESC LIMIT 20";
						$st = $pdo->query($sql);
						$rank = 1;
						while($row = $st->fetch()) {
							echo "<tr>";
							echo "<td>{$rank}</td>";
							echo "<td><a href = 'fighter_details.php?fig_name={$row['name']}&fig_cont={$row['country']}'>{$row['name']}</a></td>";
							echo "<td>{$row['country']}</td>";
							echo "<td>{$row['sub_count']}</td>";
							echo "<td>{$row['last_match_date']}</td>";
							echo "</tr>";
							$rank++;
						}
					 ?>
				</table>
			</div>
			<div>
				<table border="1">
					<h3>判定勝数</h3>
					<tr><th>順位</th><th>選手名</th><th>国籍</th><th>判定数</th><th>最終試合年月日</th></tr>
					<?php
						$sql = "SELECT * FROM fighter_list ORDER BY dec_count DESC LIMIT 20";
						$st = $pdo->query($sql);
						$rank = 1;
						while($row = $st->fetch()) {
							echo "<tr>";
							echo "<td>{$rank}</td>";
							echo "<td><a href = 'fighter_details.php?fig_name={$row['name']}&fig_cont={$row['country']}'>{$row['name']}</a></td>";
							echo "<td>{$row['country']}</td>";
							echo "<td>{$row['dec_count']}</td>";
							echo "<td>{$row['last_match_date']}</td>";
							echo "</tr>";
							$rank++;
						}
					 ?>
				</table>
			</div>
	</body>
</html>
