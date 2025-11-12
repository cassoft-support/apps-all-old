<?php
namespace Cassoft\Api;
require 'dadata-php-master/vendor/autoload.php';

class ReformaGKH
{
	private $login;
	private $pass;
	public $token;
	private $address;
	private $fias;
	private $gkhId;
    public $dadateKey;

	public function __construct($login, $pass, $dadataKey) {
		$this->login = $login;
		$this->pass = $pass;
        $this->dadataKey = $dadataKey;
	}
    
    public function getBuildProp($address) {
        $this->address = $address;
        $result = $this->dadataToFias($this->address); 
        return $result;
    } 
    public function dadataToFias($address) {
        $dadata = new \Dadata\DadataClient($this->dadataKey, null);
        $dadataAddress = $dadata->suggest('address', $this->address);
        $fias = [
            'city_id' => $dadataAddress['city_fias_id'],
            'street_id' => $dadataAddress[''],
            '' => $dadataAddress[''],
            '' => $dadataAddress[''],
            '' => $dadataAddress[''],
        ]; 
        return $dadataAddress;        
    } 

	public function getInfo() {
		d("Login: {$this->login}");
		d("Password: {$this->pass}");
		d("DadataKey: {$this->dadataKey}");
	}

    public function d($value) {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    }
}
?>
