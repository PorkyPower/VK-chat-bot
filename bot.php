<?php
require './controllers/Routes.php';


$data = json_decode(file_get_contents("php://input"));    

if ($config->secret != $data->secret)
    die();

switch ($data->type) {
    case "confirmation":
        echo $config->confirm_token;
        break;
    case "message_new":
        echo 'ok';
        
        $routes = new Routes($data);
        $routes->route();
        break;
    case "message_event":
        echo 'ok';
        
        $routes = new Routes($data);
        $routes->route();
        break;
    case "group_change_settings":
        echo 'ok';
        break;
    case "group_join":
        echo 'ok';
        break;
    case "group_officers_edit":
        echo 'ok';
        break;
    case "message_typing_state":
        echo 'ok';
        break;
    case "message_reply":
        echo 'ok';
        break;
    case "message_allow":
        echo 'ok';
        break;
}
?>