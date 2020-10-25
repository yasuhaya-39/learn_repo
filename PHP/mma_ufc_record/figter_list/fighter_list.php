<?php
	$pdo = new PDO("mysql:dbname=mma_record","root");
?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>選手データ</title>
		<link rel="stylesheet" type="text/css" href="css/fighter_list.css">
	</head>
	<body>
		<a href="fighter_ranking.php">ランキングへ</a>
		<a href="../index.html">トップページへもどる</a>
		<form action="fighter_list.php" method="post">
			<div class="search_div">
				<div class="search_menu">
					<h3>絞り込み検索</h3>
					<p>選手国籍でフィルタ<br>
					<select name="select_country">
					<?php
						echo "<option value='ALL'>ALL</option>";
						$sql = "SELECT * FROM fig_country";
						$st = $pdo->query($sql);
						while($row = $st->fetch()) {
							echo "<option value={$row['country']}>{$row['country']}</option>";
						}
					?>
					</select></p>

					<p>試合数でフィルタ<br>
					<input type="radio" name="match_num" value="ALL" checked>ALL<br>
					<input type="radio" name="match_num" value=5>5戦以上<br>
					<input type="radio" name="match_num" value=10>10戦以上<br>
					<input type="radio" name="match_num" value=15>15戦以上<br>
					<input type="radio" name="match_num" value=20>20戦以上<br>
					</p>
					<p>アクティブ選手でフィルタ<br>
					 （最終試合からの経過時間が3年以内）<br>
					<input type="radio" name="fig_active" value="ALL" checked>ALL<br>
					<input type="radio" name="fig_active" value="active">Active<br>
					</p>
					<input type="submit" name="ent_country" value="検索">
				</div>
				<div class="search_menu">
					<h3>選手名で検索</h3>
					<p>選手名<br>
					<input type="text" name="search_fig_name" value="">
					<input type="submit" name="search_fig_ent" value="検索">
				</div>

			</div>
				<?php
					if(@$_POST['ent_country']) {
						$select_count 			= $_POST['select_country'];
						$select_match_num 	= $_POST['match_num'];
						$select_fig_avtive 	=  $_POST['fig_active'];

						echo "<table border='1'>";
						echo "<tr><th>選手名</th><th>国籍</th><th>戦績</th><th>最終試合年月日</th></tr>";

						if( $select_count != 'ALL' ) {
							$sql = "SELECT * FROM fighter_list WHERE country = '$select_count' ORDER BY name";
						} else {
							$sql = "SELECT * FROM fighter_list ORDER BY country";
						}
						$st = $pdo->query($sql);

						while($row = $st->fetch()) {
							$name 		= htmlspecialchars($row['name']);
							$country 	= htmlspecialchars($row['country']);
							/* 勝数 */
							$record_str =  $row['win_count']."勝";
							/* 負数 */
							$record_str =  $record_str.$row['lose_count']."敗";
							/* 引き分け */
							if($row['drow_count'] != 0) {
								$record_str =  $record_str.$row['drow_count']."分";
							}
							/* ノーコンテスト */
							if($row['nc_count'] != 0) {
								$record_str =  $record_str.$row['nc_count']."NC";
							}
							/* 試合数を判断 */
							if($select_match_num <= ($row['win_count'] + $row['lose_count'] + $row['drow_count'] + $row['nc_count'])) {
								/* アクティブ状態の選手を表示 */
								$disp_flg = false;
								if($select_fig_avtive == "active" ) {
									if ( strtotime($row['last_match_date']) >= strtotime("-3 year") ) {
										$disp_flg = true;
									}
								} else {
									$disp_flg = true;
								}

								if ($disp_flg == true) {
									echo "<tr>";
									echo "<td>$country</td>";
									echo "<td><a href = 'fighter_details.php?fig_name={$name}&fig_cont={$country}'>$name</a></td>";
									echo "<td>$record_str</td>";
									echo "<td>{$row['last_match_date']}</td>";
									echo "</tr>";
								}
							}
						}
						echo "</table>";
					} else if (@$_POST['search_fig_ent']) {
						$search_name = $_POST['search_fig_name'];
						$sql = "SELECT * FROM fighter_list WHERE name LIKE '%{$search_name}%'";
						$st = $pdo->query($sql);

						echo "<table border='1'>";
						echo "<tr><th>選手名</th><th>国籍</th><th>戦績</th><th>最終試合年月日</th></tr>";

						while($row = $st->fetch()) {
							$name 		= htmlspecialchars($row['name']);
							$country 	= htmlspecialchars($row['country']);
							/* 勝数 */
							$record_str =  $row['win_count']."勝";
							/* 負数 */
							$record_str =  $record_str.$row['lose_count']."敗";
							/* 引き分け */
							if($row['drow_count'] != 0) {
								$record_str =  $record_str.$row['drow_count']."分";
							}
							/* ノーコンテスト */
							if($row['nc_count'] != 0) {
								$record_str =  $record_str.$row['nc_count']."NC";
							}
							echo "<tr>";
							echo "<td>$country</td>";
							echo "<td><a href = 'fighter_details.php?fig_name={$name}&fig_cont={$country}'>$name</a></td>";
							echo "<td>$record_str</td>";
							echo "<td>{$row['last_match_date']}</td>";
							echo "</tr>";
						}
						echo "</table>";
					}
				?>
		</form>
	</body>
</html>
