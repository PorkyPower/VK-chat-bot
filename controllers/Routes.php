<?php
require_once 'Controller.php';
require_once 'CheckMessage.php';
require_once 'Manager.php';

class Routes extends Controller {

    public function __construct ($data) {

        parent::__construct();
        $this->data = $data;
        if ($this->data->type == 'message_event'){
            $this->peer_id = (int) $this->filstr((string) $data->object->peer_id);
            $this->event_id = (string) $this->filstr((string) $data->object->event_id);
            $this->from_id = (int) $this->filstr((string) $data->object->user_id);
            $this->payload = $data->object->payload;
        } else {
            $this->peer_id = (int) $this->filstr((string) $data->object->message->peer_id);
            $this->from_id = (int) $this->filstr((string) $data->object->message->from_id);
            $this->mess = $this->filstr((string) $data->object->message->text);
        }
        
        $this->data_new_message = ['chat_id' => $this->peer_id];
        $this->chat = (object) [
            'admins'    => (object) ["groups" => [], "users" => []],
            'info'      => (object) [
                'db' => (object) $this->get_chat_info($this->peer_id), 
                'vk' => $this->vk->get_conversation_by_id($this->peer_id)->items[0]
            ],
            'members'   => $this->vk->get_conversation_members($this->peer_id)
        ];
        $this->chat->local_id = $this->chat->info->vk->peer->local_id;
        $this->chat->security = $this->db->select(
            'control_chats', '*', 
            'local_chat_id=:local_chat_id', 
            [
                "local_chat_id" => $this->chat->local_id
            ]
        );
        if (empty($this->chat->security)){
            $this->chat->security = false;
        }
    
        
        if ($this->from_id > 0) {

            $from_user_info = $this->get_info_user($this->from_id);
            $this->from_profile_reply = "@id$this->from_id ($from_user_info->first_name $from_user_info->last_name)";
            
            
            $get_profile = $this->db->select(
                            'users', 'rang,money,rep', 
                            'chat_id=:chat_id AND user_id=:user_id', 
                            [
                                "chat_id" => $this->chat->local_id, 
                                "user_id" => $this->from_id
                            ]
            );
            if (isset($get_profile['0'])){
                $this->money = $get_profile['0']['money'];
                $this->rang = $get_profile['0']['rang'];
                $this->rep = $get_profile['0']['rep'];
            } else {
                $this->money = 50;
                $this->rang = 1;
                $this->rep = 0;
                
               $this->db->insert(
                    'users',
                    '(NULL, :chat_id, :user_id, :money, :rep, :rang, :count_mess, :last_rep, :last_bonus, :last_drink, :last_gift)',
                    [
                        "chat_id"       => $this->chat->local_id, 
                        "user_id"       => $this->from_id, 
                        "money"         => $this->money, 
                        "rep"           => $this->rep,
                        "rang"          => $this->rang,
                        "count_mess"    => 0,
                        "last_rep"      => 0,
                        'last_bonus'    => 0,
                        'last_drink'    => 0,
                        'last_gift'     => 0
                    ]
                );
                
            }

            

        }else {
            $this->from_profile_reply = "";
        }

    }

    public function route (): void {
        if ($this->chat->info && $this->chat->info->db->{'0'}['is_active']) {
            
            $this->chat->info->db->is_active = 1;
        
            $this->chat_update_info($this->chat->info->db);
            
            $this->check_messages = new CheckMessage($this);

            if ($this->check_messages->run_check()) {
                $this->rang = $this->check_messages->access->rang;

                $manager = new Manager($this);

                $this->mess = trim($this->mess);
                $lower_command = mb_strtolower($this->mess);
                
                if ($this->data->type == 'message_event'){
                    if (isset($this->payload->type) && $this->payload->type == 'buy'){
                        $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, $manager->buyitem($this->from_id, $this->payload->id, $this->payload->typebuy));
                    }
                    if (isset($this->payload->type) && $this->payload->type == 'moneta'){
                        if ($this->payload->who == $this->from_id){
                            $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, $manager->moneta_take($this->payload->count,$this->payload->side,$this->payload->from));  
                        } else {
                            $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Вызов не Вам!'); 
                        }
                    }
                    if (isset($this->payload->type) && $this->payload->type == 'drink'){
                        $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, $manager->bar_drink($this->from_id, $this->payload->id));
                    }
                    if (isset($this->payload->type) && $this->payload->type == 'upgrade'){
                        switch($this->rang){
                            case 1:
                                if ($this->money >= 1000){
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Ранг повышен до '.$manager->updaterang($this->from_id, $this->rang, 1000));
                                    
                                } else {
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Недостаточно Рё');
                                }
                                break;
                            case 2:
                                if ($this->money >= 5000){
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Ранг повышен до '.$manager->updaterang($this->from_id, $this->rang, 5000));
                                    
                                } else {
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Недостаточно Рё');
                                }
                                break;
                            case 3:
                                if ($this->money >= 20000){
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Ранг повышен до '.$manager->updaterang($this->from_id, $this->rang, 20000));
                                    
                                } else {
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Недостаточно Рё');
                                }
                                break;
                            case 4:
                                if ($this->money >= 50000){
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Ранг повышен до '.$manager->updaterang($this->from_id, $this->rang, 50000));
                                    
                                } else {
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Недостаточно Рё');
                                }
                                break;
                            case 5:
                                if ($this->money >= 500000){
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Ранг повышен до '.$manager->updaterang($this->from_id, $this->rang, 500000));
                                    
                                } else {
                                    $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Недостаточно Рё');
                                }
                                break;
                            case 6:
                                $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Максимальный ранг');
                                break;
                        }
                    } else {
                        $this->vk->sendMessageEventAnswer($this->event_id, $this->from_id, $this->peer_id, 'Не нажимай, блт');
                    }
                    die();       
                }

                 
                
                
                if ($this->findCommand($this->commands->help)
                    || ($this->data->object->message->conversation_message_id == 1 && $this->messages->welcome_invite)) {

                    $manager->help();

                }elseif ($this->findCommand($this->commands->rules)
                    || ($this->data->object->message->conversation_message_id == 1 && $this->messages->welcome_invite)) {

                    $manager->rules();

                }elseif ($this->findCommand($this->commands->update)){

                    $manager->update();

                }elseif ($lower_command == $this->commands->clear) {

                    if ($this->check_messages->access->is_admin)
                        $this->clear_chat($this->peer_id);
                    else 
                        $this->check_messages->access_denied = true;

                }elseif ($lower_command == $this->commands->admin_help) {

                    if ($this->check_messages->access->is_admin)
                        $this->admin_help($this->peer_id);
                    else 
                        $this->check_messages->access_denied = true;

                }elseif ($this->findCommand($this->commands->all)) {

                    if ($this->check_messages->access->is_admin)
                        $manager->call_everyone($this->mess);
                    else
                        $this->check_messages->access_denied = true;

                }elseif ($this->mess == "+" && $this->from_id > 0) {

                    $manager->vote_poll();

                }elseif ($this->findRPcommand(mb_strtolower($this->mess)) && $this->from_id > 0) {
                    if ($this->rang >= 2) {
                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->role_play($this->mess, $this->data->object->message->reply_message->from_id);
                        else
                            $manager->role_play($this->mess);
                    } else {
                        $this->vk->send_message($this->from_profile_reply.', РП команды доступны со 2го ранга', $this->data_new_message);
                    }
                    

                }elseif ($this->findCommand($this->commands->votekick) && $this->from_id > 0) {

                    if (isset($this->data->object->message->reply_message->from_id))
                        $manager->create_voting($this->data->object->message->reply_message->from_id);
                    else
                        $manager->create_voting($this->mess);

                }elseif ($this->findCommand($this->commands->plus_rep) && $this->from_id > 0) {

                    if (isset($this->data->object->message->reply_message->from_id))
                        $manager->plus_rep($this->data->object->message->reply_message->from_id);
                    else
                        $manager->plus_rep($this->mess);

                }elseif ($this->findCommand($this->commands->minus_rep) && $this->from_id > 0) {

                    if (isset($this->data->object->message->reply_message->from_id))
                        $manager->minus_rep($this->data->object->message->reply_message->from_id);
                    else
                        $manager->minus_rep($this->mess);

                }elseif ($this->findCommand($this->commands->kick)) {

                    if ($this->check_messages->access->kick) {

                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->kick($this->data->object->message->reply_message->from_id);
                        else
                            $manager->kick_by_screen_name($this->mess);

                    }else {

                        $this->check_messages->access_denied = true;

                    }

                }elseif ($this->findCommand($this->commands->warn_remove)) {

                    if ($this->check_messages->access->is_admin) {
                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->warn_remove($this->data->object->message->reply_message->from_id);
                        else
                            $manager->warn_remove($this->mess);

                    }else {
                        $this->check_messages->access_denied = true;
                    }

                }elseif ($this->findCommand($this->commands->mute_remove)) {

                    if ($this->check_messages->access->is_admin) {
                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->mute_remove($this->data->object->message->reply_message->from_id);
                        else
                            $manager->mute_remove($this->mess);

                    }else {
                        $this->check_messages->access_denied = true;
                    }

                }elseif ($this->findCommand($this->commands->delmess)) {

                    if ($this->check_messages->access->is_admin) {
                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->delmess($this->data->object->message->conversation_message_id,$this->data->object->message->reply_message->conversation_message_id);
                    }else {
                        $this->check_messages->access_denied = true;
                    }

                }elseif ($this->findCommand($this->commands->warn)) {

                    if ($this->check_messages->access->kick) {

                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->warn($this->mess, $this->data->object->message->reply_message->from_id);
                        else
                            $manager->warn($this->mess);

                    }else {

                        $this->check_messages->access_denied = true;

                    }

                }elseif ($this->findCommand($this->commands->mute)) {

                    if ($this->check_messages->access->kick) {

                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->mute($this->mess, $this->data->object->message->reply_message->from_id);
                        else
                            $manager->mute($this->mess);

                    }else {

                        $this->check_messages->access_denied = true;

                    }

                }elseif ($this->findCommand($this->commands->admin_add)) {

                    if ($this->check_messages->access->is_owner) {

                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->add_admin($this->data->object->message->reply_message->from_id);
                        else
                            $manager->add_admin($this->mess);

                    }else {

                        $this->check_messages->access_denied = true;

                    }

                }elseif ($this->findCommand($this->commands->admin_remove)) {

                    if ($this->check_messages->access->is_owner) {

                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->remove_admin($this->data->object->message->reply_message->from_id);
                        else
                            $manager->remove_admin($this->mess);

                    }else {

                        $this->check_messages->access_denied = true;

                    }

                }elseif ($this->findCommand($this->commands->who_invite)) {

                    if ($this->check_messages->access->is_admin) {

                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->who_invite($this->data->object->message->reply_message->from_id);
                        else
                            $manager->who_invite($this->mess);

                    }else {

                        $this->check_messages->access_denied = true;

                    }

                }elseif ($this->findCommand($this->commands->changerang)) {

                    if ($this->check_messages->access->is_admin) {

                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->changerang($this->data->object->message->reply_message->from_id, $this->mess, true);
                        else
                            $manager->changerang(0, $this->mess, false);

                    }else {

                        $this->check_messages->access_denied = true;

                    }

                }elseif ($this->findCommand($this->commands->admin_list)) {

                    $manager->show_admins();

                }elseif ($this->findCommand($this->commands->show_secure)) {
                    
                    if ($this->check_messages->access->is_admin)
                        $manager->show_status_security();
                    else
                        $this->check_messages->access_denied = true;

                }elseif ($this->findCommand($this->commands->show_active)) {

                    $manager->show_last_active();

                }elseif ($this->findCommand($this->commands->show_unactive)) {

                    if ($this->check_messages->access->is_admin)
                        $manager->show_not_active();
                    else
                        $this->check_messages->access_denied = true;

                }elseif ($this->findCommand($this->commands->nick_add)) {

                    if ($this->check_messages->access->is_admin) {

                        if (isset($this->data->object->message->reply_message->from_id)) {

                            $manager->nick_add($this->data->object->message->reply_message->from_id, $this->mess);

                        }else {
                            $arr_parms = explode(' ',$this->mess);
                            if (isset($arr_parms[1])){
                                $manager->nick_add($arr_parms[0], $arr_parms[1]);
                            }

                        }
                            

                    }else {
                        $this->check_messages->access_denied = true;
                    }

                }elseif ($this->findCommand($this->commands->nick_remove)) {

                    if ($this->check_messages->access->is_admin) {
                        if (isset($this->data->object->message->reply_message->from_id))
                            $manager->nick_remove($this->data->object->message->reply_message->from_id);
                        else
                            $manager->nick_remove($this->mess);

                    }else {
                        $this->check_messages->access_denied = true;
                    }

                }elseif ($this->findCommand($this->commands->nick_list)) {

                    $manager->nick_list();

                }elseif ($this->findCommand($this->commands->profile)) {

                    $manager->profile();

                }elseif ($this->findCommand($this->commands->shop)) {

                    $manager->shop('shop');

                }elseif ($this->findCommand($this->commands->autos)) {

                    $manager->shop('autos');

                }elseif ($this->findCommand($this->commands->butik)) {

                    $manager->shop('butik');

                }elseif ($this->findCommand($this->commands->larek)) {

                    $manager->shop('larek');

                }elseif ($this->findCommand($this->commands->bar)) {

                    $manager->bar();

                }elseif ($this->findCommand($this->commands->top_mess)) {

                    $manager->top_mess();

                }elseif ($this->findCommand($this->commands->test)) {

                    $manager->test();
                }elseif ($this->findCommand($this->commands->moneta) && $this->from_id > 0) {
                    if (isset($this->data->object->message->reply_message->from_id))
                        $manager->moneta($this->mess, $this->data->object->message->reply_message->from_id);
                    else
                        $manager->moneta($this->mess);

                }elseif ($this->findCommand($this->commands->money_get_user) && $this->from_id > 0) {
                         if (isset($this->data->object->message->reply_message->from_id))
                            $manager->money_get_user($this->mess, $this->data->object->message->reply_message->from_id);
                        else
                            $manager->money_get_user($this->mess);
                }elseif ($this->findCommand($this->commands->money_get) && $this->from_id > 0) {
                    if ($this->check_messages->access->is_admin) {
                         if (isset($this->data->object->message->reply_message->from_id))
                            $manager->money_get($this->mess, $this->data->object->message->reply_message->from_id);
                        else
                            $manager->money_get($this->mess);
                    }else {
                        $this->check_messages->access_denied = true;
                    }
                }elseif ($this->findCommand($this->commands->money_take) && $this->from_id > 0) {
                    if ($this->check_messages->access->is_admin) {
                         if (isset($this->data->object->message->reply_message->from_id))
                            $manager->money_take($this->mess, $this->data->object->message->reply_message->from_id);
                        else
                            $manager->money_take($this->mess);
                    }else {
                        $this->check_messages->access_denied = true;
                    }
                }elseif ($this->findCommand($this->commands->casino)) {
                    $manager->casino($this->mess);

                }elseif ($this->findCommand($this->commands->bandit)) {
                    $manager->bandit($this->mess);

                }elseif ($this->findCommand($this->commands->gift)) {
                    $manager->gift();
                    
                }elseif ($this->findCommand($this->commands->bonus)) {
                    $manager->bonus();
                    
                }elseif ($this->findCommand($this->commands->roulette)) {

                    $manager->russian_roulette();
                }
                    else {

                    $secure_module = false;

                    if ($this->findCommand($this->commands->secure_links))
                        $secure_module = "links";
                    elseif ($this->findCommand($this->commands->secure_invites))
                        $secure_module = "invites";
                    elseif ($this->findCommand($this->commands->secure_bots))
                        $secure_module = "bots";
                    elseif ($this->findCommand($this->commands->secure_nude))
                        $secure_module = "nude";
                    elseif ($this->findCommand($this->commands->secure_censor))
                        $secure_module = "censor";
                    elseif ($this->findCommand($this->commands->secure_repost))
                        $secure_module = "repost";
                    elseif ($this->findCommand($this->commands->secure))
                        $secure_module = "security";

                    if ($secure_module) {

                        if ($this->check_messages->access->is_admin)
                            if ($this->findCommand($this->commands->enable))
                                $manager->$secure_module(1);
                            elseif ($this->findCommand($this->commands->disable))
                                $manager->$secure_module(0);
                        else
                            $this->check_messages->access_denied = true;

                    }

                }

                

                if ($this->check_messages->access_denied) {

                    $message_rule_kick = $this->messages->access_denied;

                    if (!$this->check_messages->access->kick && $this->check_messages->access->is_admin)
                        $message_rule_kick = $this->messages->limit_denied;

                    $this->vk->send_message(($this->check_messages->from_profile_reply . $message_rule_kick), $this->data_new_message);

                }

                $this->update_last_activity($this->from_id);

            }
            
            
            $this->chat->info->db->is_active = 0;
        
            $this->chat_update_info($this->chat->info->db);
            
        } else {
            if ($this->peer_id > 2000000000)
                $this->vk->send_message('Бот является платным, для покупки пишите в ЛС', $this->data_new_message);
        }

    }

    public function findCommand ($command): bool {
        $this->mess = trim(str_replace('[club179296178|@makima_bot] ', '', $this->mess));
        $this->mess = mb_strtolower($this->mess);
        $result = '';     
        if (gettype($command) == "array"){
            foreach($command as $cmq){

                $result .= mb_stristr($this->mess, $cmq) 
                       && mb_stripos($this->mess, $cmq) === 0;
                if ($result)
                    $this->mess = trim(str_replace($cmq, '', $this->mess));

            }
            
        } else {
            $result = mb_stristr($this->mess, $command) 
                   && mb_stripos($this->mess, $command) === 0;

            if ($result)
                $this->mess = trim(str_replace($command, '', $this->mess));

        }
        return $result;

    }
    
    public function findRPcommand($command) : bool{
        $result = 0;
            foreach($this->rp_commands as $coms){
                $result .= mb_stristr($this->mess, $coms) 
                   && mb_stripos($this->mess, $coms) === 0;
            }
        if ($result > 0){
            $result = true;
        }
        
        return $result;
    }

}