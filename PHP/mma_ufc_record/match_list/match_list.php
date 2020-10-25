<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>大会一覧</title>
		<link rel="stylesheet" type="text/css" href="css/match_list.css">
	</head>
	<body>
		<a href="match_list_insert.php">大会情報を入力する</a>
		<a href="../index.html">トップページへもどる</a><br>
		<a href="../analysis/analysis.php">解析ページへ</a>

		<table border="1">
			<tr><th>大会名</th><th>開催国</th><th>開催場所</th><th>日時</th></tr>
			<?php
				$pdo = new PDO("mysql:dbname=mma_record","root");
				$st = $pdo->query("SELECT * FROM ufc_match_data ORDER BY match_date DESC");

				while($row = $st->fetch()) {
					$name 				= htmlspecialchars($row['name']);
					$country 			= htmlspecialchars($row['country']);
					$prace 				= htmlspecialchars($row['prace']);
					$match_date 	= htmlspecialchars($row['match_date']);
					$no 					= htmlspecialchars($row['no']);

					echo "<tr>";
					echo "<td><a href='../match_result/match_result.php?match_no=$no'>$name</a></td>";
					echo "<td>$country</td>";
					echo "<td>$prace</td>";
					echo "<td>$match_date</td>";
					echo "</tr>";
				}
			?>

		</table>
	</body>
</html>
