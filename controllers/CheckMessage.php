<?php 
class CheckMessage extends Controller {

    public $access;
    public $security;
    public $access_denied;
    

    public function __construct(Routes $route) {

        parent::__construct($route);
        $this->access = (object) [
            'commands'   => true,
            'control'    => false,
            'is_admin'   => false,
            'is_owner'   => false,
            'kick'       => false,
            'rang'       => $this->rang,
            'who_invite' => '',
        ];
        $this->security = false;
        $this->access_denied = false;

    }

    public function run_check (): bool {

        if (isset($this->chat->info->vk)) {

            foreach ($this->chat->members->items as $member_info) {

                if ($member_info->member_id == $this->from_id) {

                    $this->access->who_invite = $member_info->invited_by;
                    if (isset($member_info->is_admin)) {

                        $this->access->is_admin = $member_info->is_admin;
                        $this->access->is_owner = true;
                        $this->access->rang = 6;
                    }

                }

                if (isset($member_info->is_admin) && $member_info->is_admin === true) {

                    if ($member_info->member_id > 0)
                        $this->chat->admins->users[] = $member_info->member_id;
                    else
                        $this->chat->admins->groups[] = $member_info->member_id;

                }

            }

            if (!$this->access->is_admin) {

                $limits_kick = 20;

                $check_admin_db = $this->db->select(
                        'chat_admins', 'COUNT(*)', 
                        'chat_id=:chat_id AND user_id=:user_id', 
                        [
                            "chat_id" => $this->chat->local_id, 
                            "user_id" => $this->from_id
                        ], 1
                    )[0][0];
                if ($check_admin_db) {

                    $this->access->is_admin = true;
                    $this->access->rang = 6;

                    $check_limits_kick = $this->db->select(
                            'kick_logs', 'COUNT(*)', 
                            'chat_id=:chat_id AND admin_id=:admin_id AND date>:date', 
                            [
                                "chat_id" => $this->chat->local_id, 
                                "admin_id" => $this->from_id, 
                                "date" => ($this->date - 86400)
                            ]
                        )[0][0];

                    $this->access->kick = $limits_kick > $check_limits_kick;

                } 

            }else {

                $this->access->kick = true;

            }
            
            if ($this->chat->info->vk->peer->type == "chat") {

                if ($this->chat->security) {
                    $this->access->control = true;
                    $this->security_chat();

                }

            }

        }

        return $this->access->commands;

    }

    function security_chat (): void {
        
        $this->db->update(
            'users',
            'count_mess=count_mess+1',
            'chat_id=:chat_id AND user_id=:user_id',
            [
                "chat_id" => $this->chat->local_id,
                "user_id" => $this->from_id
            ], 1
        );

        
        $check_user_ban_in_chat = $this->db->select(
                'bans', 'COUNT(*)', 
                'user_id=:user_id AND date>:date_end AND chat_id=:chat_id', 
                [
                    "user_id"   => $this->from_id, 
                    "date_end"  => ($this->date - 86400), 
                    "chat_id"   => $this->chat->local_id
                ], 1
            )[0][0];
        
        
        
        if ($check_user_ban_in_chat == 0) {
            
            $check_user_mute_in_chat = $this->db->select(
                'mute', 'COUNT(*)', 
                'user_id=:user_id AND date>:date_end AND chat_id=:chat_id', 
                [
                    "user_id"   => $this->from_id, 
                    "date_end"  => ($this->date), 
                    "chat_id"   => $this->chat->local_id
                ], 1
            )[0][0];

            if ($check_user_mute_in_chat != 0) {
                $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);
                die();
            }
        
            if ($this->mess != ''){
                
                $this->mess = trim(str_replace('[club179296178|@makima_bot] ', '', $this->mess));
                $check_last_drink = $this->db->select(
                    'users', 'COUNT(*)', 
                    'user_id=:user_id AND last_drink>:last_drink AND chat_id=:chat_id', 
                    [
                        "user_id"   => $this->from_id, 
                        "last_drink"  => ($this->date), 
                        "chat_id"   => $this->chat->local_id
                    ], 1
                )[0][0];

                if ($check_last_drink != 0){
                    if (!$this->access->is_admin){
                        $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);
                    }
                        $word = preg_replace('%^.{' . rand(0, mb_strlen($this->mess) - 2) . '}\K(.)(.)%u', '$2$1', $this->mess);
                        $word = preg_replace('%^.{' . rand(0, mb_strlen($word) - 2) . '}\K(.)(.)%u', '$2$1', $word);
                        $word = preg_replace('%^.{' . rand(0, mb_strlen($word) - 2) . '}\K(.)(.)%u', '$2$1', $word);
                        $word = preg_replace('%^.{' . rand(0, mb_strlen($word) - 2) . '}\K(.)(.)%u', '$2$1', $word);
                        $word = preg_replace('%^.{' . rand(0, mb_strlen($word) - 2) . '}\K(.)(.)%u', '$2$1', $word);
                        $word = preg_replace('%^.{' . rand(0, mb_strlen($word) - 2) . '}\K(.)(.)%u', '$2$1', $word);
                        $this->vk->send_message($this->from_profile_reply. ': ' .$word, $this->data_new_message);
                }
            }
            
            
            $len_mess = strlen($this->mess);
            
            

            
            

            if ($len_mess > $this->max_len_msg && !$this->access->is_admin) {
                
                $this->warn_user(
                    $this->chat->local_id, 
                    $this->from_id, 
                    $this->from_profile_reply . ', Получил варн',
                    'Превышение количества символов в сообщении. Макс. '.$this->max_len_msg
                );
                $this->access->commands = false;

            }elseif ($this->access->control 
                    && isset($this->data->object->message->action) 
                    && ($this->data->object->message->action->type == "chat_invite_user"
                    || $this->data->object->message->action->type == "chat_invite_user_by_link")) {

                if ($this->data->object->message->action->type == "chat_invite_user")
                    $action_usr_id = $this->data->object->message->action->member_id;
                elseif ($this->data->object->message->action->type == "chat_invite_user_by_link")
                    $action_usr_id = $this->from_id;
                
                $check_user_bans = $this->db->select(
                        'bans', 'COUNT(*)', 
                        'user_id=:user_id AND date>:date_end', 
                        [
                            "user_id" => $action_usr_id, 
                            "date_end" => ($this->date - 86400)
                        ], 1
                    )[0][0];

                if ($check_user_bans >= 3) {

                    $this->kick_user($this->chat->local_id, $this->data->object->message->action->member_id, false, $this->messages->bad_active);
                    $this->access->commands = false;

                }elseif ($this->data->object->message->action->type == "chat_invite_user") {

                    $invite_user_id = $this->data->object->message->action->member_id;
                    
                    $access_invite = true;
                    
                    if ($this->chat->security['0']['bots'] == 1 && $invite_user_id < 0 && !$this->access->is_admin) {
                        
                        $this->kick_user($this->chat->local_id, $invite_user_id, true, $this->from_profile_reply . $this->messages->bots_prohibited);
                        $access_invite = false;
                        
                    }
                        

                    if ($access_invite) {
                        
                        $check_user_ban_in_chat = $this->db->select(
                                'bans', 'COUNT(*)', 
                                'user_id=:user_id AND date>:date_end AND chat_id=:chat_id', 
                                [
                                    "user_id"   => $invite_user_id, 
                                    "date_end"  => ($this->date - 86400), 
                                    "chat_id"   => $this->chat->local_id
                                ], 1
                            )[0][0];

                        if ($check_user_ban_in_chat == 1) {

                            if (!$this->access->is_admin) {

                                $this->kick_user($this->chat->local_id, $invite_user_id, false);
                                $this->kick_user($this->chat->local_id, $this->from_id, true, $this->messages->bad_active);
                                $this->access->commands = false;

                            }else {

                                $this->db->delete(
                                    'bans',
                                    'user_id=:user_id AND chat_id=:chat_id',
                                    [
                                        "user_id"   => $invite_user_id, 
                                        "chat_id"   => $this->chat->local_id
                                    ], 1
                                );

                            }

                        }
                        
                    }

                }
                

            }elseif (!$this->access->is_admin) {
                
                $chech_msg = 'check_'.$this->mess;
                $check_all = strpos($chech_msg, '@all');
                $check_online = strpos($chech_msg, '@online');
                if ($check_all>0 || $check_online>0)
                    $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);
                

                $check_fast_spam = $this->db->select(
                    'message_logs', 'COUNT(*)', 
                    'from_id=:from_id AND chat_id=:chat_id AND date>:date_end', 
                    [
                        "from_id" => $this->from_id, 
                        "chat_id" => $this->chat->local_id, 
                        "date_end" => ($this->date - 10)
                    ], 7
                )[0][0]; 

                if (7 > $check_fast_spam) {
                    
                    $check_text = true;

                    if ($this->chat->security['0']['nude_security']) {
                        
                        foreach ($this->data->object->message->attachments as $attachment) {

                            if ($attachment->type == "photo" 
                                || $attachment->type == "video"
                                || $attachment->type == "doc") {

                                if ($attachment->type == "video") {
                                
                                    $photos = [];

                                    foreach ($attachment->video as $key=>$val) {

                                        if (mb_stristr($key, "photo_")) {

                                            $photos[] = $val;

                                        }

                                    }
                                    
                                    $count_sizes = count($photos);
                                    $url_image = $photos[$count_sizes - 1];
                                    
                                }elseif ($attachment->type == "doc") {
                                    
                                    $count_sizes = count($attachment->doc->preview->photo->sizes);
                                    $image = $attachment->doc->preview->photo->sizes[$count_sizes - 1];

                                    $url_image = $image->src;
                                    
                                }else {
                                    
                                    $count_sizes = count($attachment->photo->sizes);
                                    $image = $attachment->photo->sizes[$count_sizes - 1];

                                    $url_image = $image->url;
                                    
                                }
                                

                                if ($this->check_image_nude($url_image) === true) {

                                    $this->kick_user(
                                        $this->chat->local_id, 
                                        $this->from_id, 
                                        true, 
                                        ($this->from_id > 0) ? $this->from_profile_reply . $this->messages->nude_photo : ''
                                    );
                                    $check_text = false;

                                }

                            }

                        }
                        
                    }
                    
                    if ($check_text) {
                        
                        
                        
                        $limits_messages = 30;
                        $date_limit = 1800;

                        if ($len_mess > 40) {
                            
                            $limits_messages = ($this->chat->local_id == 2 || $this->chat->local_id == 37) ? 16 : 2;                            
                            $date_limit = 43200;
                            
                        }
                        if (isset($this->data->object->message->attachments[0]->type) && $this->data->object->message->attachments[0]->type == "sticker")
                            $this->mess = "sticker=" . $this->data->object->message->attachments[0]->sticker->sticker_id;
                        if (isset($this->data->object->message->attachments[0]->type) && $this->data->object->message->attachments[0]->type == "link")
                            $this->mess = "link=" . $this->data->object->message->attachments[0]->link->url;
                        if (isset($this->data->object->message->attachments[0]->type) && $this->data->object->message->attachments[0]->type == "wall")
                            $this->mess = "wall=" . $this->data->object->message->attachments[0]->wall->from_id;
                        if (isset($this->data->object->message->attachments[0]->type) && $this->data->object->message->attachments[0]->type == "wall_reply")
                            $this->mess = "wall_reply=" . $this->data->object->message->attachments[0]->wall_reply->from_id;

                        $this->mess = trim($this->mess);

                        if (!empty($this->mess)) {

                            $coding_text = urlencode($this->mess);

                            $check_spam_messages = $this->db->select(
                                'message_logs', 'COUNT(*)', 
                                'from_id=:from_id AND chat_id=:chat_id AND text=:text AND date>:date_end',
                                [
                                    "from_id" => $this->from_id, 
                                    "chat_id" => $this->chat->local_id, 
                                    "text" => $coding_text,
                                    "date_end" => ($this->date - $date_limit)
                                ], $limits_messages
                                )[0][0];
                            
                            if ($limits_messages > $check_spam_messages) {

                                $this->db->insert(
                                    'message_logs',
                                    '(NULL, :from_id, :chat_id, :text, :len_text, :date)',
                                    [
                                        "from_id" => $this->from_id, 
                                        "chat_id" => $this->chat->local_id, 
                                        "text" => $coding_text, 
                                        "len_text" => $len_mess,
                                        "date" => $this->date
                                    ]
                                );

                                if ($this->chat->security['0']['links']) {
                                    $find_link = false;
                                    
                                    $this->mess = str_replace("https://", "http://", $this->mess);

                                    $findme = 'http://';
                                    $pos = strpos(strtolower($this->mess), $findme);
                                    
                                    $zones = ['.ru','.com','.org','.net','.cc','.info','.kz','.ua','.xyz','.online','.site','.biz','.de','.in','.io','.co', '.me'];
                                    for ($i=0; $i<count($zones); $i++){
                                        $pos_zone = strpos(strtolower($this->mess), $zones[$i]);
                                        if ($pos_zone !== false)
                                            $find_link = true;
                                    }
                                    
                                    
                                    if (isset($this->data->object->message->attachments['0'])){
                                        if ($this->data->object->message->attachments['0']->type == "link"){
                                            $find_link = true;
                                        }
                                    }
                                    

                                    if ($pos !== false)
                                        $find_link = true;
                                    
                                    if ($find_link) {
                                        
                                        $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);
                                        $this->warn_user(
                                            $this->chat->local_id, 
                                            $this->from_id, 
                                            $this->from_profile_reply . ', Получил варн',
                                            $this->messages->links_prohibited
                                        );
                                        
                                        $this->access->commands = false;

                                    }

                                }elseif ($this->chat->security['0']['invites']) {

                                    $this->mess = str_replace("https://", "http://", $this->mess);

                                    $findme = 'http://vk.me/join/';
                                    $pos = strpos(strtolower($this->mess), $findme);

                                    if ($pos !== false) {
                                    $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);

                                        $this->warn_user(
                                            $this->chat->local_id, 
                                            $this->from_id, 
                                            $this->from_profile_reply . ', Получил варн',
                                            $this->messages->invites_prohibited
                                        );
                                        
                                        $this->access->commands = false;

                                    }


                                } 
                                if ($this->chat->security['0']['censor']){
                                    if (!$this->cens->isAllowed($this->mess)){
                                        $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);

                                        $this->warn_user(
                                            $this->chat->local_id, 
                                            $this->from_id, 
                                            $this->from_profile_reply . ', Получил варн',
                                            'Мат запрещен'
                                        );
                                    }
                                    
                                }
                                
                                if ($this->chat->security['0']['repost']){
                                    if (isset($this->data->object->message->attachments[0]->type) && $this->data->object->message->attachments[0]->type == "wall")
                                        if ($this->data->object->message->attachments[0]->wall->from_id != '-221543698'){
                                            
                                            $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);
                                            
                                            $this->warn_user(
                                                $this->chat->local_id, 
                                                $this->from_id, 
                                                $this->from_profile_reply . ', Получил варн',
                                                $this->messages->repost_prohibited
                                            );
                                        }
                                    if (isset($this->data->object->message->attachments[0]->type) && $this->data->object->message->attachments[0]->type == "wall_reply")
                                        if ($this->data->object->message->attachments[0]->wall_reply->owner_id != '-221543698'){
                                            $this->vk->delete_message($this->data->object->message->peer_id, $this->data->object->message->conversation_message_id);                                            
                                            
                                            $this->warn_user(
                                                $this->chat->local_id, 
                                                $this->from_id, 
                                                $this->from_profile_reply . ', Получил варн',
                                                $this->messages->repost_prohibited
                                            );
                                        }
                                    
                                    
                                    
                                    
                                }
                                

                                $last_messages = $this->db->select(
                                    'message_logs', 'len_text', 
                                    'from_id=:from_id AND chat_id=:chat_id AND date>:date', 
                                    [
                                        "from_id" => $this->from_id, 
                                        "chat_id" => $this->chat->local_id, 
                                        "date" => ($this->date - 600)
                                    ]
                                );
                                
                                $len_last_messages = 0;
                                
                                foreach ($last_messages as $mess_len)
                                    $len_last_messages += $mess_len['len_text'];
                                
                                if ($len_last_messages > ($this->max_len_msg * 3)) {
                                    
                                    $this->warn_user(
                                        $this->chat->local_id, 
                                        $this->from_id, 
                                        $this->from_profile_reply . ', Получил варн', 
                                        $this->messages->too_many_long_messages
                                    );
                                    $this->access->commands = false;
                                    
                                }
                                
                            }else {

                                if ($this->from_id > 0) {
                                    
                                    $this->warn_user($this->chat->local_id, $this->from_id, $this->from_profile_reply . ', Получил варн', $this->messages->many_similar_messages);

                                }

                                $this->access->commands = false;

                            }

                        }
                    
                    }
                
                }else {

                    $this->warn_user($this->chat->local_id, $this->from_id, $this->from_profile_reply . ', Получил варн', $this->messages->many_active);
                    $this->access->commands = false;

                }

            }
            
        }else {
            
            $this->warn_user(
                $this->chat->local_id, 
                $this->from_id, 
                $this->from_profile_reply . ', Получил варн',
                ''
            );
                                        
            
            if (isset($this->data->object->message->action->type) && $this->data->object->message->action->type == "chat_invite_user") {
                $action_usr_id = $this->data->object->message->action->member_id;
                  $this->warn_user(
                                $this->chat->local_id, 
                                $this->from_id, 
                                $this->from_profile_reply . ', Получил варн',
                                ''
                );
            }
            
        }

    }
    

}

?>