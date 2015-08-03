<?
$library = new Library;
$library->init();
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class library {
	public $posts;
	public $postsQuantity = 50; //количество постов со стены
	public $offset = 0; //с какого поста наичнать 
	const CLIENT_ID = 5015617; //ID приложение 
	const CLIENT_SECRET = '*****'; //секретный код приложения, в целях безопасности убрал из кода
	const PROFILE_VK_PATH = 'https://vk.com/id'; 
	public $transArr = array(); 
	public $obj_profiles;
	public $templateLinkAttach_img;
	public $templateLinkAttach_withoutimg;
		
	public function transArrPut($var,$val)  // сохраняет шаблоны для замены
	
		{
			$this->transArr[$var]=$val;			
		}
	
	
	
	function __construct() {
		$this -> URL = $this -> request_url();
	}


	public function get_access_token_attr($client_id, $client_secret, $redirect_uri, $code) {//возращает access_token с параметрами
		$access_token_get_params = 'client_id=' . $client_id . '&' . 'client_secret=' . $client_secret . '&' . 'redirect_uri=' . $redirect_uri . '&' . 'code=' . $code;
		$url_get_access_token = 'https://oauth.vk.com/access_token?' . $access_token_get_params;
		//возращает объект со свойствами access_token, expires_in, user_id
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url_get_access_token);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$access_token_attr_json = curl_exec($curl);
		curl_close($curl);
		$access_token_attr = json_decode($access_token_attr_json);
		return $access_token_attr;
	}
	public function jsonGetWallToHtml() {//Возращает все записи со стены в HTML, принимает результаты ответа от метода wall.get в JSON
		$html = '';
		//$parametrs = array('offset' => 0, 'count' => $this -> postsQuantity, 'version' => 5.35,);
		$parametrs = array('offset' => $this->offset, 'count' => $this -> postsQuantity, 'version' => 5.7,'extended' => 1, );
		$json = $this -> getApiReply('wall.get', $parametrs, $_COOKIE['access_token']);
		
		$obj_response = json_decode($json);
		$template = file_get_contents('templates/template.html');
		$this->templateLinkAttach_img = file_get_contents('templates/templateLinkAttach_img.html');
		$this->templateLinkAttach_withoutimg = file_get_contents('templates/templateLinkAttatch_withoutimg.html');
		$this->obj_profiles = $obj_response -> response->profiles;
		//echo '<pre>';var_dump($this->obj_profiles);exit;
		//echo '<pre>';var_dump($obj_response -> response->wall[1]);exit;
		//echo '<pre>';var_dump($obj_response -> response);exit;
		
		for ($i = 1; $i < ($this -> postsQuantity + 1); $i++) {
			$obj_wall = $obj_response -> response->wall[$i];
			$html .= $this -> postObjToHtml($obj_wall,$template);
		}
		return $html;
	}

	public function getAttachHTML($postObj){ //генегирует HTML вложения
		if (isset($postObj->attachment)){
			if ($postObj->attachment->type == 'link')
				{
					$this->transArr['{LinkAttach.URL}'] = $postObj->attachment->link->url;
					$this->transArr['{LinkAttach.title}'] = $postObj->attachment->link->title;
					$this->transArr['{LinkAttach.description}'] = $postObj->attachment->link->description;
					if (isset($postObj->attachment->link->image_src)){
						$this->transArr['{LinkAttach.image_src}'] = $postObj->attachment->link->image_src;
						$HTML = strtr($this->templateLinkAttach_img,$this->transArr);
						}else{				
						$HTML = strtr($this->templateLinkAttach_withoutimg,$this->transArr);
					}
					return $HTML;
				}
		}
	}


	public function postObjToHtml($postObj,$template) {//возращает HTML пост из объекта поста
		//echo '<pre>';var_dump($this->obj_profiles);exit;
		//echo '<pre>';var_dump($this);exit;
		$this->transArr['{FirstName}']=$this->searchUserParams($postObj -> from_id, 'first_name');
		$this->transArr['{LastName}']=$this->searchUserParams($postObj -> from_id, 'last_name');
		$this->transArr['{URL_photo_50*50}']=$this->searchUserParams($postObj -> from_id, 'photo');
		$this->transArr['{userProfileURL}']=self::PROFILE_VK_PATH.$postObj -> from_id;
		$this->transArr['{PostText}']=$postObj -> text;
		$this->transArr['{Attachments}']=$this->getAttachHTML($postObj);
		$HTML = strtr($template,$this->transArr);
		return $HTML;
	}

	public function getApiReply($mhetod_name, $parametrs_array, $access_token = NULL) {//возращает в JSON результат запроса к vk_api
		$url = 'https://api.vk.com/method/' . $mhetod_name . '?';
		$url .= http_build_query($parametrs_array);
		$url .= '&access_token=' . $access_token;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$vk_response = curl_exec($curl);
		//echo '<pre>';var_dump($vk_response);exit;
		return $vk_response;
	}
	public function init() { //посылает авторизоваться, вешает куку с access_token
		if (isset($_COOKIE['access_token'])) {

		} else {
			if (!isset($_GET["code"])) {
				header('location:https://oauth.vk.com/authorize?client_id=' . self::CLIENT_ID . '&redirect_uri=' . $this -> URL . '&display=page&scope=notes&v=5.35');
			} else {
				$token = $this -> get_access_token_attr(self::CLIENT_ID, self::CLIENT_SECRET, $this -> URL, $_GET["code"]);
				setcookie('access_token', $token -> access_token, time() + $token -> expires_in);
				header('location:' . $this -> URL);
			}
		}
	}
	private function searchUserParams($profileID,$propertyName){ //возращает данные профиля пользователя
		foreach ($this->obj_profiles as $key => $value)
			{
				if ($value -> uid == $profileID)
				{
					return $value -> $propertyName;
				}
			}
		}
	private function request_url() { // возращает URL текущей страницы (без GET)
		$result = '';
		$default_port = 80;
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) {
			$result .= 'https://';
			$default_port = 443;
		} else {
			$result .= 'http://';
		}
		$result .= $_SERVER['SERVER_NAME'];
		if ($_SERVER['SERVER_PORT'] != $default_port) {
			$result .= ':' . $_SERVER['SERVER_PORT'];
		}
		$page = explode('?', $_SERVER['REQUEST_URI']);
		$result .= $page[0];
		return $result;
	}
}
?>