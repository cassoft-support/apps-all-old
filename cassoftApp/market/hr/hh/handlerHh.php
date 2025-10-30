<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logHandler.txt";
session_start();
p($_GET, "start", $log);
// Проверьте, что параметр 'state' присутствует и совпадает с сохраненным значением
if (isset($_GET['state']) && $_GET['state'] === $_SESSION['oauth2state']) {
// Очистите сохраненное значение state, чтобы предотвратить повторное использование
unset($_SESSION['oauth2state']);

// Проверьте, что параметр 'code' присутствует
if (isset($_GET['code'])) {
$authorizationCode = $_GET['code'];

// Теперь у вас есть код авторизации, и вы можете обменять его на токен доступа
echo "Код авторизации: " . htmlspecialchars($authorizationCode);

// Здесь вы можете продолжить процесс обмена кода на токен доступа
} else {
// Обработка ошибки: параметр 'code' отсутствует
echo "Ошибка: код авторизации отсутствует.";
}
} else {
// Обработка ошибки: параметр 'state' отсутствует или не совпадает
echo "Ошибка: недопустимое состояние авторизации.";
}
?>