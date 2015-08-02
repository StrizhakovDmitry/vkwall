
/*	public function bootstrap() {
		if (isset($_COOKIE['access_token'])) {
			$token = $_COOKIE['access_token'];
			$vk_response = $this -> get_api_reply($vk_api_method, $parametrs, $token);
			$this -> posts = $this -> jsonGetWallToHtml($vk_response);
		} else {

			if (isset($_GET["code"])) {
				$token = $this -> get_access_token_attr(5015617, 'EqMZZhD4CntAte8jlTGO', 'http://merk46.ru/vknews/index.html', $_GET["code"]);

				$vk_api_method = 'wall.get';
				$parametrs = array('offset' => 0, 'count' => $this -> postsQuantity, 'version' => 5.35, );
				$vk_response = $this -> get_api_reply($vk_api_method, $parametrs, $token -> access_token);
				$this -> posts = $this -> jsonGetWallToHtml($vk_response);
			} else {
				header("location:https://oauth.vk.com/authorize?client_id=5015617&redirect_uri=http://merk46.ru/vknews/index.html&display=page&scope=notes&v=5.35");
			}
		}

	}*/

/*	public function getUserInfo($vk_uid) {//возращает имя пользователя по его id_vk
		$json_user_info = $this -> get_api_reply('users.get', array('user_ids' => $vk_uid, 'fields' => 'photo_50'));
		$objUserInfo = json_decode($json_user_info);
		$objUserInfo = $objUserInfo -> response[0];
		$userInfo = new UserInfo;
		$userInfo -> first_name = $objUserInfo -> first_name;
		$userInfo -> last_name = $objUserInfo -> last_name;
		$userInfo -> photo_50 = $objUserInfo -> photo_50;
		return $userInfo;
	}*/


