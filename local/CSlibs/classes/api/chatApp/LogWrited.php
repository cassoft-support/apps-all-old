<?php
namespace CSlibs\Api\ChapApp;
class LogWrited {
	private static $error = false;
	private static $errorCount = 0;
	
	/**
	 * Условный метод: были ли ошибки в ходе работы или нет.
	 *
	 * @return (bool) $error - false/true (да/нет)
	 */
	public static function isError(){
		return self::$error;
	}
	
	/**
	 * Возвращает кол-во ошибок
	 *
	 * @return (int) $errorCount - кол-во ошибок
	 */
	public static function getErrorCount(){
		return self::$errorCount;
	}
	
	/**
	  * Возвращает путь до лог-файла.
	  *
	  * @return (string) $fileDir - путь до лог-файла
	*/
	public static function getFileLog(){
		$dirLogs = (defined('DIR_LOGS') and DIR_LOGS) ? DIR_LOGS : dirname( __FILE__ );
		
		$toDate = date('Y-m-d');
		$fileName = $toDate . '-' . 'log';
		$fileDir = $dirLogs . '/logs/' . $fileName . '.txt';

		return $fileDir;
	}

	/**
	 * Записывает данные в указанный файл.
	 *
	 * @param (string) $file - файл, в который записать данные
	 * @param (string) $data - данные, которые записать
	 */
	private static function writedFile($file, $data){
		file_put_contents($file, $data, FILE_APPEND);
	}

	/**
	  * Записывает действия скрипта в лог-файл
	  *
	  * @param string $status - статус действия успешно/ошибка.
	  * @param string $textMessage - описание.
	*/

	public static function writedLog($status, $textMessage){
		if (defined('WRITED_LOG') and WRITED_LOG == true){
			$file = self::getFileLog();

			$data = sprintf(
						"%s | %s: %s \r\n",
						date('Y-m-d-H:i:s'),
						$status,
						$textMessage
					);

			self::writedFile($file, $data);
		}
	}

	/**
	 * Записывает в лог-файл все данные запросов к серверу и ответы от него.
	 *
	 * @param (string/array) $result - данные запроса/ответа
	 * @param (string) $url - URL зпроса, если есть
	 */
	public static function writedDebug($result, $url=''){
		if (defined('DEBUG') and DEBUG == true){
			$file = self::getFileLog();

			if (is_array($result))
				$result = print_r($result, true);

			$data = sprintf(
						"%s | %s: %s \r\n%s\r\n",
						date('Y-m-d-H:i:s'),
						'DEBUG: ',
						$url,
						$result
					);

			self::writedFile($file, $data);
		}
	}

	/** Записывает информацию об успешном действие в лог-файл.
	  *
	  * @param array $textSuccess - текст уведомления.
	 */

	public static function success($textSuccess){
		self::writedLog('Уведомление', $textSuccess);
	}

	/** Записывает ошибку в лог-файл.
	  *
	  * @param array $textError - текст ошибки.
	*/

	public static function error($data){
		if (is_array($data)){
			$errorCode = (isset($data['error'])) ? $data['error'] : '';

			$textError = $errorCode;

			if (isset($data['error_description'])) 
				$textError .= ' - ' . $data['error_description'];
			else
				$textError .= 'В ответе на запрос нет описания ошибки.';
		} else {
			$textError = $data;
		}

		if (!self::$error){
			self::$errorCount++;
			self::$error = true;
		}
			
		self::writedLog('Ошибка', $textError);
	}
}

?>