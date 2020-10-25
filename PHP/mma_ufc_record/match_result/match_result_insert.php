<?php
	/* セッション開始 */
	if(!isset($_SESSION)){
		session_start();
	}

	require "match_result_insert_class.php";

	$d_match_no = htmlspecialchars($_GET['match_no']);

	$input_init_class = $input_init_win_name = $input_init_lose_name = $input_init_decision =
	$input_init_r_time = $input_init_details = $input_init_win_cont = $input_init_lose_cont = "";

	if(@$_POST['ent_match']) {

		/* inputタグで入力した内容を変数に保存 */
		$order			= htmlspecialchars($_POST['match_order']);
		$d_class 		= htmlspecialchars($_POST['match_class']);
		$d_w_cont 	= htmlspecialchars($_POST['match_win_cont']);
		$d_w_name 	= htmlspecialchars($_POST['match_win_name']);
		$d_l_cont 	= htmlspecialchars($_POST['match_lose_cont']);
		$d_l_name 	= htmlspecialchars($_POST['match_lose_name']);
		$d_decision = htmlspecialchars($_POST['match_decision']);
		$d_time 		= htmlspecialchars($_POST['match_r_time']);
		$d_details 	= htmlspecialchars($_POST['match_details']);
		$category 	= htmlspecialchars($_POST['match_category']);

		$match_data = new match_data_file(true);
		/* セッションに保存している試合結果のデータを変数に格納 */
		if ( $order < $match_data->get_order_num()) {
			$input_init_class 			= $match_data->get_class($order);
			$input_init_win_name 		= $match_data->get_win_name($order);

			if ( $match_data->get_fig_country($input_init_win_name,$t_country)  != false) {
				$input_init_win_cont = $t_country;
			}

			$input_init_lose_name 	= $match_data->get_lose_name($order);

			if ( $match_data->get_fig_country($input_init_lose_name,$t_country)  != false) {
				$input_init_lose_cont = $t_country;
			}

			$input_init_decision 		= $match_data->get_decision($order);
			$input_init_r_time 			= $match_data->get_r_time($order);
			$input_init_details 		= $match_data->get_details($order);
		}

		/* 未入力の項目が無いかチェック */
		if($d_match_no != "" && $d_class != "" && $d_w_cont != "" && $d_w_name != "" && $d_l_cont != ""
		&& $d_l_name != "" && $d_decision != "" && $d_time != "" && $d_details != "" && $order != "" && $category != "") {

			$pdo = new PDO("mysql:dbname=mma_record","root");

			$sql = "SELECT * FROM ufc_match_data WHERE no = {$d_match_no}";
			$st = $pdo->query($sql);
			$row = $st->fetch();

			$match_date = $row['match_date'];

			$st = $pdo->prepare("INSERT INTO ufc_match_result(match_no,m_order,m_class,win_cont,win_name,lose_cont,lose_name,decision,m_r_time,details,category,match_date) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
			$st->execute(array($d_match_no,$order,$d_class,$d_w_cont,$d_w_name,$d_l_cont,$d_l_name,$d_decision,$d_time,$d_details,$category,$match_date));

			/* テーブルに無い国なら登録する */
			$sql = "SELECT COUNT(*) FROM fig_country WHERE country = '{$d_w_cont}'";
			$st = $pdo->query($sql);
			$count = $st->fetchColumn();

			if ($count == 0) {
				$sql = "INSERT INTO fig_country(country) VALUES('{$d_w_cont}')";
				$st = $pdo->query($sql);
			}

			$sql = "SELECT COUNT(*) FROM fig_country WHERE country = '{$d_l_cont}'";
			$st = $pdo->query($sql);
			$count = $st->fetchColumn();

			if ($count == 0) {
				$sql = "INSERT INTO fig_country(country) VALUES('{$d_l_cont}')";
				$st = $pdo->query($sql);
			}

			$sql = "SELECT COUNT(*) FROM fighter_list WHERE name = '{$d_w_name}' AND country = '{$d_w_cont}'";
			$st = $pdo->query($sql);
			$count = $st->fetchColumn();

			if ($count == 0) {
				$sql = "INSERT INTO fighter_list(name,country) VALUES('{$d_w_name}','{$d_w_cont}')";
				$st = $pdo->query($sql);
			}

			$sql = "SELECT COUNT(*) FROM fighter_list WHERE name = '{$d_l_name}' AND country = '{$d_l_cont}'";
			$st = $pdo->query($sql);
			$count = $st->fetchColumn();

			if ($count == 0) {
				$sql = "INSERT INTO fighter_list(name,country) VALUES('{$d_l_name}','{$d_l_cont}')";
				$st = $pdo->query($sql);
			}

			$order++;
		} else {
			echo "<h3>未入力の項目があります</h3>";
		}
	} else {
		$order = 1;
	}

	if($order == 1) {
		/* ファイル読み込み */
		$match_data = new match_data_file(false);
		/* セッションに保存している試合結果のデータを変数に格納 */
		if ( $order < $match_data->get_order_num()) {
			$input_init_class 			= $match_data->get_class(0);
			$input_init_win_name 		= $match_data->get_win_name(0);

			if ( $match_data->get_fig_country($input_init_win_name,$t_country)  != false) {
				$input_init_win_cont = $t_country;
			}

			$input_init_lose_name 	= $match_data->get_lose_name(0);

			if ( $match_data->get_fig_country($input_init_lose_name,$t_country)  != false) {
				$input_init_lose_cont = $t_country;
			}

			$input_init_decision 		= $match_data->get_decision(0);
			$input_init_r_time 			= $match_data->get_r_time(0);
			$input_init_details 		= $match_data->get_details(0);
		}
	}

?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>試合結果入力</title>
	</head>
	<body>
		<form action='match_result_insert.php?match_no=<?php echo $d_match_no ?>' method="post">
			試合順<br>
			<input type="text" name="match_order" value="<?php echo $order ?>"><br>
			階級<br>
			<input type="text" name="match_class" value="<?php echo $input_init_class ?>"><br>
			勝利選手国籍<br>
			<input type="text" name="match_win_cont" value="<?php echo $input_init_win_cont ?>"><br>
			勝利選手名前<br>
			<input type="text" name="match_win_name" value="<?php echo $input_init_win_name ?>"><br>
			敗北選手国籍<br>
			<input type="text" name="match_lose_cont" value="<?php echo $input_init_lose_cont ?>"><br>
			敗北選手名前<br>
			<input type="text" name="match_lose_name" value="<?php echo $input_init_lose_name ?>"><br>
			決着<br>
			<input type="text" name="match_decision" value="<?php echo $input_init_decision ?>"><br>
			時間<br>
			<input type="text" name="match_r_time" value="<?php echo $input_init_r_time ?>"><br>
			詳細<br>
			<input type="text" name="match_details" value="<?php echo $input_init_details ?>"><br>
			カテゴリ<br>
			<input type="radio" name="match_category" value="アーリープレリム" checked>アーリープレリム
			<input type="radio" name="match_category" value="プレリミナリーカード">プレリミナリーカード
			<input type="radio" name="match_category" value="メインカード" >メインカード

			<br>
			<input type="hidden" name="match_no">
			<input type="submit" name="ent_match" value="登録して次の試合へ">
		</form>
		<br>
		<br>
		<a href='match_result.php?match_no=<?php echo $d_match_no ?>'>試合結果一覧へ戻る</a>
	</body>
</html>
