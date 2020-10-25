<?php

	if(@$_POST['ent_menu']) {
		$select_menu = $_POST['select_menu'];

		$pdo = new PDO("mysql:dbname=mma_record","root");

		if( $select_menu == "ins_fig_table") {

			//試合結果一覧から選手のテーブルを更新する
			$sql = "SELECT DISTINCT win_name,win_cont FROM ufc_match_result";
			$st = $pdo->query($sql);

			while($row = $st->fetch()) {
				$sql = "SELECT COUNT(*) FROM fighter_list WHERE name= '{$row['win_name']}'";
				$st_fig = $pdo->prepare($sql);
				$st_fig->execute();
				$count = $st_fig->fetchColumn();
				if ($count == 0) {
					echo "pass";
					$st_ins = $pdo->prepare("INSERT INTO fighter_list(name,country) VALUES(?,?)");
					$st_ins->execute(array($row['win_name'],$row['win_cont']));
				}
			}

			$sql = "SELECT DISTINCT lose_name,lose_cont FROM ufc_match_result";
			$st = $pdo->query($sql);

			while($row = $st->fetch()) {
				$sql = "SELECT COUNT(*) FROM fighter_list WHERE name= '{$row['lose_name']}'";
				$st_fig = $pdo->prepare($sql);
				$st_fig->execute();
				$count = $st_fig->fetchColumn();
				if ($count == 0) {
					$st_ins = $pdo->prepare("INSERT INTO fighter_list(name,country) VALUES(?,?)");
					$st_ins->execute(array($row['lose_name'],$row['lose_cont']));
				}
			}
		} else if($select_menu == "update_fig_table") {
			/* 選手テーブルの全レコードをセレクト */
			$sql = "SELECT * FROM fighter_list";
			$st = $pdo->query($sql);

			while($row = $st->fetch()) {
				/* 勝利数 */
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE win_name= '{$row['name']}' AND decision != '引き分け' AND decision != 'ノーコンテスト'";
				$st_fig = $pdo->query($sql);
				$w_count = $st_fig->fetchColumn();

				/* 敗北数 */
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE lose_name= '{$row['name']}' AND decision != '引き分け' AND decision != 'ノーコンテスト'";
				$st_fig = $pdo->query($sql);
				$l_count = $st_fig->fetchColumn();

				/* 引き分け数 */
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE (win_name= '{$row['name']}' OR  lose_name= '{$row['name']}')  AND decision = '引き分け'";
				$st_fig = $pdo->query($sql);
				$d_count = $st_fig->fetchColumn();

				/* ノーコンテスト数 */
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE (win_name= '{$row['name']}' OR  lose_name= '{$row['name']}')  AND decision = 'ノーコンテスト'";
				$st_fig = $pdo->query($sql);
				$nc_count = $st_fig->fetchColumn();

				/* 試合数 */
				$matcu_sum = $w_count + $l_count + $nc_count + $d_count;

				/* KO数 */
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE win_name= '{$row['name']}' AND (decision = 'KO' OR  decision = 'TKO')";
				$st_fig = $pdo->query($sql);
				$ko_count = $st_fig->fetchColumn();

				/* Sub数 */
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE win_name= '{$row['name']}' AND decision = '一本'";
				$st_fig = $pdo->query($sql);
				$sub_count = $st_fig->fetchColumn();

				/* Sub数 */
				$sql = "SELECT COUNT(*) FROM ufc_match_result WHERE win_name= '{$row['name']}' AND decision = '判定'";
				$st_fig = $pdo->query($sql);
				$dec_count = $st_fig->fetchColumn();

				/* 最終試合日 */
				$sql = "SELECT * FROM ufc_match_result WHERE win_name= '{$row['name']}' OR  lose_name= '{$row['name']}' ORDER BY match_date DESC";
				$st_fig = $pdo->query($sql);
				$row_2 = $st_fig->fetch();

				$sql = "UPDATE fighter_list
				SET win_count={$w_count}, lose_count={$l_count}, drow_count={$d_count},nc_count={$nc_count},last_match_date = '{$row_2['match_date']}' ,
						ko_count={$ko_count},sub_count={$sub_count},dec_count={$dec_count},match_count={$matcu_sum}
				WHERE name = '{$row['name']}'";
				$st_fig = $pdo->query($sql);
			}
		}
	}
 ?>

 <!DOCTYPE html>
 <html lang="ja">
 	<head>
 		<meta charset="utf-8">
 		<title>メンテナンス</title>
 	</head>
 	<body>
 		<a href="../index.html">トップページへ</a>
 		<form action="mainte.php" method="post">
 			<p>解析項目の選択：<br>
 			<select name="select_menu">
 			<option value="ins_fig_table">試合結果から選手テーブルを追加</option>
			<option value="update_fig_table">試合結果から選手テーブルの戦績を更新</option>
 			</select></p>
 			<input type="submit" name="ent_menu" value="決定">
 		</form>
 	</body>
 </html>
