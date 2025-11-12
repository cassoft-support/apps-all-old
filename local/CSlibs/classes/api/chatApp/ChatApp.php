<?php
namespace CSlibs\Api\ChapApp;
class ChatApp {
    const URL_CHATAPP_TOKEN = 'https://api.chatapp.online/v1/tokens';
    const URL_CHATAPP_API = 'https://api.chatapp.online/v1';

    private $chatAppEmail;
    private $chatAppPassword;
    private $chatAppId;
	
    private $accessToken;
    private $refreshToken;
    private $accessTokenEndTime = 0;
    private $refreshTokenEndTime = 0;
	
    private $configFileTokens;
    private $configFileName;

	public function __construct($chatAppEmail, $chatAppPassword, $chatAppId, $configFileName = 'config-tokens.php') {
        if (!$chatAppEmail)
            throw new Exception('Не указан E-mail для ChatApp.');

        if (!$chatAppPassword)
            throw new Exception('Не указан пароль для ChatApp.');

        if (!$chatAppId)
            throw new Exception('Не указан APP ID для ChatApp.');

        $this->chatAppEmail    = $chatAppEmail;
        $this->chatAppPassword = $chatAppPassword;
        $this->chatAppId       = $chatAppId;
        $this->configFileName       = $configFileName;

		$this->configFileTokens = __DIR__ . '/tools/' . $this->configFileName;

		$this->authoChatApp();
    }

	private function authoChatApp() {
		if (!file_exists($this->configFileTokens)) {
			/**
			 * Если конфигурационного файла с токенами нет, 
			 * отправляем запрос на получение токенов
			 * и записываем их в файл.
			 */
			$this->getTokensChatApp();
		} else {
			/**
			 * Если файл с токенами существует,
			 * устанавливаем токены для дальнейшего использования.
			 */
			$this->setTokens();

			/* Обновляем токены, если время действия истекло */
			$this->updateTokens();
		}
	}

	/**
	 * Подключает конфигурационный файл с токенами, если есть,
	 * и устанавливаем их для дальнейшего использования.
	 */
	private function setTokens() {
		if (file_exists($this->configFileTokens)) {
			require($this->configFileTokens);

			if (!defined('ACCESS_TOKEN')
				or !defined('REFRESH_TOKEN')
				or !defined('ACCESS_TOKEN_END_TIME')
				or !defined('REFRESH_TOKEN_END_TIME')) {

				/* Если константы с токенами отсутствуют, получаем токены. */
				$this->getTokensChatApp();
			} else {
				$this->accessToken  = ACCESS_TOKEN;
				$this->refreshToken = REFRESH_TOKEN;

				$this->accessTokenEndTime  = (int) ACCESS_TOKEN_END_TIME;
				$this->refreshTokenEndTime = (int) REFRESH_TOKEN_END_TIME;
			}
		}
	}

    /**
     * Отправляет запрос к ChatApp на получение Tokens.
     * 
     * @return false/$response - массив с данными ответа от ChatApp
     */
    public function getTokensChatApp() {
        $response = Http::requestPost(
            self::URL_CHATAPP_TOKEN,
            [
				'email' => $this->chatAppEmail,
                'password' => $this->chatAppPassword,
                'appId' => $this->chatAppId
            ]
        );

		if ($this->isSuccess($response)) {
			LogWrited::success('Tokens успешно получены!');

			$this->saveTokensFile($response);
		}
    }

	/**
	 * Проверяет получены ли токены, если получены, сохраняет их в файл.
	 *
	 * @param $response - массив с данными от ChatApp
	 * @return
	 */
	public function saveTokensFile($response) {
		$accessToken  = (isset($response['data']['accessToken'])) ? $response['data']['accessToken'] : '';
		$refreshToken = (isset($response['data']['refreshToken'])) ? $response['data']['refreshToken'] : '';

		$accessTokenEndTime  = (isset($response['data']['accessTokenEndTime'])) ? $response['data']['accessTokenEndTime'] : '';
		$refreshTokenEndTime = (isset($response['data']['refreshTokenEndTime'])) ? $response['data']['refreshTokenEndTime'] : '';

		if ($accessToken and $refreshToken and $accessTokenEndTime and $refreshTokenEndTime) {
			$this->accessToken  = $accessToken;
			$this->refreshToken = $refreshToken;
			
			$this->accessTokenEndTime  = (int) $accessTokenEndTime;
			$this->refreshTokenEndTime = (int) $refreshTokenEndTime;

			/* Сохраняем токены в конфигурационный файл */

			$lineBreak = "\r\n";

			$data = "<?php$lineBreak";
			$data .= "define('ACCESS_TOKEN', '$accessToken');";
			$data .= $lineBreak;
			$data .= "define('REFRESH_TOKEN', '$refreshToken');";
			$data .= $lineBreak;
			$data .= "define('ACCESS_TOKEN_END_TIME', '$accessTokenEndTime');";
			$data .= $lineBreak;
			$data .= "define('REFRESH_TOKEN_END_TIME', '$refreshTokenEndTime');";

			$bytes = file_put_contents($this->configFileTokens, $data, LOCK_EX);
			
			if ($bytes){
				LogWrited::success('Tokens успешно сохранены!');
			}
		}
    }

	/**
	 * Обновляет токены, если их действие истекло.
	 */
	public function updateTokens() {
		$time = time();

		if ($time >= $this->accessTokenEndTime) {
			$response = Http::requestPost(
				self::URL_CHATAPP_TOKEN . '/refresh',
				[],
				['Refresh: ' . $this->refreshToken]
			);

			if ($this->isSuccess($response)) {
				LogWrited::success('Tokens успешно ообновлены!');

				$this->saveTokensFile($response);
			} else {
				$this->getTokensChatApp();
			}
		}
	}

	/**
	 * Получает список всех лицензий
	 */
	public function getLicenses() {
		$response = Http::requestGet(
			self::URL_CHATAPP_API . '/licenses',
			[],
			['Authorization: ' . $this->accessToken]
		);

		if ($this->isSuccess($response)) {
			//print_r($response);
		}
	}

	/**
	 * Отправляет сообщение в WhatsApp.
	 *
	 * @param $phone - номер телефона
	 * @param $message - текст сообщения
	 * @param $licenseId - Id лицензии
	 * @return;
	 */
	public function sendWhatsAppMessage($phone, $message, $licenseId, $messengerType='grWhatsApp') {
        if (!$phone)
            throw new Exception('Не указан номер телефона.');

		if (!$message)
            throw new Exception('Не указан текст сообщения.');

		$response = Http::requestPost(
			self::URL_CHATAPP_API . "/licenses/$licenseId/messengers/$messengerType/chats/$phone/messages/text",
			[
				'text' => $message
			],
			['Authorization: ' . $this->accessToken]
		);
//print_r($response);
		if ($this->isSuccess($response)) {
			LogWrited::success('Сообщение успешно отправлено на номер телефона ' . $phone);
		}
        return $response;
	}

	/**
	 * Проверяет успешный ли запрос к ChatApp.
	 *
	 * @param $response - массив с ответом от ChatApp
	 * @return true/false
	 */
	public function isSuccess($response) {
		if (isset($response['success']) and $response['success'] == 1) {
			return true;
		} else {
			$error = $this->getError($response);
			LogWrited::error($error);
		}

		return false;
	}

	public function getError($response){
		$errorText = '';

		if (isset($response['error']) and $response['error']){
			$errorText .= (isset($response['error']['code'])) ? $response['error']['code'] : '';
			$errorText .= (isset($response['error']['message'])) ? ' | ' . $response['error']['message'] : '';
		}

		return $errorText;
	}
}