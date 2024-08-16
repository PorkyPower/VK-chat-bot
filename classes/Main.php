<?php
require_once './classes/DB.php';
require_once './config.php';
require_once './messages.php';
require_once './commands.php';
require_once './items.php';

class Main {

    public $db;
    public $date;
    public $config;
    public $messages;
    public $commands;
    public $ru_commands;
    public $ru_commands_desc;
    public $rp_commands;
    public $admin_commands;
    public $admin_commands_desc;
    public $bar;
    public $autos;
    public $butik;
    public $larek;

    public function __construct() {
        $this->config = $GLOBALS['config'];
        $this->messages = $GLOBALS['messages'];
        $this->commands = $GLOBALS['commands'];
        $this->ru_commands = $GLOBALS['ru_cmds'];
        $this->ru_commands_desc = $GLOBALS['ru_desc'];
        $this->rp_commands = $GLOBALS['rp_cmds'];
        $this->admin_commands = $GLOBALS['admin_cmds_ru'];
        $this->admin_commands_desc = $GLOBALS['admin_cmds_ru_desc'];
        $this->bar = $GLOBALS['bar'];
        $this->autos = $GLOBALS['autos'];
        $this->butik = $GLOBALS['butik'];
        $this->larek = $GLOBALS['larek'];
        $this->db = new DB($this->config->DB->host, $this->config->DB->username, $this->config->DB->password, $this->config->DB->dbname);
        $this->date = time();
    }

    public function curl_send (string $url, bool $ignore = true, string $proxy = ""): string {
        
        $myCurl = curl_init();
        
        curl_setopt_array($myCurl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query(array())
        ));
        
        $response = curl_exec($myCurl);
        
        curl_close($myCurl);
        return $response;
        
    }

    

}
?>