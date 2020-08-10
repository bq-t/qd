<? 
	session_start();

	if($_POST['lo_submit']) {
		session_unset();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>TaskList</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<? if(!$_SESSION['id']): ?>
		<form class="form login" method="POST">
			<input name="login" placeholder="Введите логин.." required>
			<input type="password" name="password" placeholder="Введите пароль.." required>
			<input type="submit" name="submit" value="Войти / Зарегистрироваться">
		</form>
		<? else: ?>
		<form class="form login" method="POST">
			<div>Вы уже авторизованы, хотите выйти?</div>
			<input type="submit" name="lo_submit" value="Выйти">
		</form>
		<? 
			endif;

			if($_POST['submit']) {
				include_once 'php/connect.php';

				$login = $_POST['login'];

				$query = $mysql->prepare("SELECT * FROM `users` WHERE `login` = ?");
				$query->bind_param('s', $login);
				$query->execute();
				$result = $query->get_result();


				if(!$result->num_rows) {

					$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

					$reg = $mysql->prepare("INSERT INTO `users` (`login`, `password`) VALUES (? , ?);");
					$reg->bind_param('ss', $login, $password);
					$reg->execute();


					$id = $mysql->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
					$id->bind_param('s', $login);
					$id->execute();
					$result = $id->get_result();

					$fetch = $result->fetch_array();

					$_SESSION['id'] = $fetch['id'];

					echo
						'<script>location.href= "tasklist.php";</script>';
				}
				else {
					$query = $mysql->prepare("SELECT `id`, `password` FROM `users` WHERE `login` = ?");
					$query->bind_param('s', $login);
					$query->execute();
					$result = $query->get_result();

					$fetch = $result->fetch_array();

					if(password_verify($_POST['password'], $fetch['password'])) {
						$_SESSION['id'] = $fetch['id'];

						echo
							'<script>location.href= "tasklist.php";</script>';
					}
					else 
						echo "<div class='bad-pass'>Вы ввели неверный пароль!</div>";
				}
			}
		?>
	</body>
</html>