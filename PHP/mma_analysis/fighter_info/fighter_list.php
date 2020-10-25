<?php
	require "fighter_api.php";
?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>選手一覧</title>
		<link rel="stylesheet" type="text/css" href="css/fighter_info.css">
	</head>
	<body>
		<a href="../index.html">トップページへもどる</a>
		<form action="fighter_list.php" method="post">
			<div class="search_div">
				<div class="search_menu">
					<h3>選手名で検索</h3>
					<p>選手名<br>
					<input type="text" name="search_fig_name" value="">
					<input type="submit" name="search_fig_ent" value="検索">
				</div>
			</div>
			<div class="class_area">
					<?php
						disp_figther_list("BANTAMWEIGHT");
						disp_figther_list("FLYWEIGHT");
						disp_figther_list("FEATHERWEIGHT");
						disp_figther_list("LIGHTWEIGHT");
					?>
			</div>
		</form>
	</body>
</html>
