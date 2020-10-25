<?php
	$match_no = $_GET['match_no'];
?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>試合結果</title>
		<link rel="stylesheet" type="text/css" href="css/match_result.css">
	</head>
	<body>
		<a href='match_result_insert.php?match_no=<?php echo $match_no ?>'>試合結果を入力</a>
		<a href='../match_list/match_list.php'>大会一覧へ戻る</a>
		<table border="1">
			<tr><th>試合順</th><th>階級</th><th>勝利選手国籍</th><th>勝利選手名</th><th>敗北選手国籍</th><th>敗北選手名</th><th>決着</th><th>時間</th><th>決着詳細</th><th>カテゴリ</th><th>日時</th></tr>
			<?php

				$pdo = new PDO("mysql:dbname=mma_record","root");

				$sql = "SELECT * FROM ufc_match_result WHERE match_no={$match_no} ORDER BY m_order";
				$st = $pdo->query($sql);
				$count = 0;

				while($row = $st->fetch()) {
					$match_no 		= htmlspecialchars($row['match_no']);
					$order 				= htmlspecialchars($row['m_order']);
					$class 				= htmlspecialchars($row['m_class']);
					$win_cont 		= htmlspecialchars($row['win_cont']);
					$win_name 		= htmlspecialchars($row['win_name']);
					$lose_cont 		= htmlspecialchars($row['lose_cont']);
					$lose_name 		= htmlspecialchars($row['lose_name']);
					$decision 		= htmlspecialchars($row['decision']);
					$m_r_time 		= htmlspecialchars($row['m_r_time']);
					$details 			= htmlspecialchars($row['details']);
					$category			= htmlspecialchars($row['category']);
					$match_date		= htmlspecialchars($row['match_date']);

					echo "<tr>";
					echo "<td>第{$order}試合</td>";
					echo "<td>$class</td>";
					echo "<td>$win_cont</td>";
					echo "<td><a href = '../figter_list/fighter_details.php?fig_name={$win_name}&fig_cont={$win_cont}'>$win_name</td>";
					echo "<td>$lose_cont</td>";
					echo "<td><a href = '../figter_list/fighter_details.php?fig_name={$lose_name}&fig_cont={$win_cont}'>$lose_name</td>";
					echo "<td>$decision</td>";
					echo "<td>$m_r_time</td>";
					echo "<td>$details</td>";
					echo "<td>$category</td>";
					echo "<td>$match_date</td>";
					echo "</tr>";

					$count++;
				}

				/* 登録試合無し */
				if($count == 0) {
					echo "<h2>登録されている試合結果はありません</h2>";
				}
			?>
		</table>
	</body>
</html>
