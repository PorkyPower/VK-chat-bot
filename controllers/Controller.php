<?php
require_once './classes/VK_API.php';
require_once './classes/Main.php';
require_once './classes/ObsceneCensorRus.php';

class Controller extends Main {

    public $new_line = "\r\n";
    public $checkmark = "&#10004; &#65039; ";
    public $error_sim = "&#10060; ";
    public $clear_chat = "ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ ᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠᅠ";
    public $max_len_msg = 1000;
    public $path;
    public $vk;
    public $date_last_actv;
    public $cens;

    public $data;
    public $peer_id;
    public $from_id;
    public $mess;
    public $data_new_message;
    public $chat;
    public $check_messages;
    public $from_profile_reply;
    public $money;
    public $rep;
    public $rang;

    public function __construct (Routes $route = null) {

        parent::__construct();
        $this->path = substr($_SERVER['PHP_SELF'], 0, -2);
        $this->date_last_actv = $this->date - (86400 * 21);
        $this->vk = new VK();
        $this->cens = new ObsceneCensorRus();

        if ($route) {

            $this->data = $route->data;
            $this->peer_id = $route->peer_id;
            $this->from_id = $route->from_id;
            $this->mess = $route->mess;
            $this->data_new_message = $route->data_new_message;
            $this->chat = $route->chat;
            $this->check_messages = $route->check_messages;
            $this->from_profile_reply = $route->from_profile_reply;
            $this->money = $route->money;
            $this->rep = $route->rep;
            $this->rang = $route->rang;

        }
        
    }

    public function filstr (string $str): string {
        $str = trim($str);
        $str = htmlspecialchars($str);
        $str = strip_tags($str);
        return $str;
    }

    
    public function get_chat_info (int $peer_id): array {
        
        if ($peer_id) {
            
            $chat_info = $this->db->select(
                    'chats', '*', 
                    'peer_id=:peer_id',
                    ['peer_id' => $peer_id], 1
                );
        
            if (!$chat_info) {

                $this->db->insert(
                    'chats', '(NULL, :peer_id, 0, :date, :date)',
                    [
                        'peer_id' => $peer_id, 
                        'date' => $this->date
                    ]
                );

                $chat_info = $this->get_chat_info($peer_id);

            }

            return $chat_info;
            
        }
        
    }

    public function chat_update_info (object $chat): void {
        $this->db->update(
            'chats',
            'is_active=:is_active, last_active=:last_active',
            'id=:id',
            [
                'is_active'     => $chat->{'0'}['is_active'],
                'last_active'   => $this->date,
                'id'            => $chat->{'0'}['id']
            ]
        );
        
    }

    public function update_last_activity (int $user_id): void {
        $check_last_activity = $this->db->select(
            'last_activity', '*', 
            'user_id=:user_id AND chat_id=:chat_id',
            [
                "user_id" => $user_id, 
                "chat_id" => $this->chat->local_id
            ]
            );
        if ($check_last_activity) {
              
            $this->db->update(
                'last_activity',
                'date_last_acivity=:date_last_acivity',
                'id=:id',
                [
                    "date_last_acivity" => $this->date, 
                    "id" => $check_last_activity['0']['id']
                ]
            );
            
        }else {
            
            $this->db->insert(
                'last_activity',
                '(NULL, :user_id, :chat_id, :date_last_acivity, :date)',
                [
                    "user_id"           => $user_id, 
                    "chat_id"           => $this->chat->local_id,
                    "date_last_acivity" => $this->date,
                    "date"              => $this->date
                ]
            );
            
        }
        
    }
    
    public function check_image_nude (string $url_image): bool {
        
        $detector = new NudeDetector(null, 'YCbCr');  # 'HSV' for alternate skin-color-detection
        $detector->set_file_name($url_image);
        
        if ($detector->is_nude()) {
            
            $data_string = json_encode($url_image);

            $curl = curl_init('https://api.algorithmia.com/v1/algo/sfw/NudityDetectioni2v/0.2.12');

            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Simple ' . $this->config->algorithmia)
            );
            $result = curl_exec($curl);
            curl_close($curl);
            
            if($result !== false) {
                $result = json_decode($result);
                return $result->result->nude;
            } else {
                return false;
            }
            
        }else {
            return false;
        }
        
    }

    public function search_user_in_chat (string $screen_name): int {
        $screen_name = mb_strtolower($screen_name);
        $user_id = 0;
        
        if ($screen_name) {
            foreach ($this->chat->members->profiles as $member) {

                if ($member->id == $screen_name) {

                    $user_id = $member->id;
                    break;

                }

            }

            if ($user_id == 0) {
                foreach ($this->chat->members->groups as $group) {
                    
                    if ($group->id == $screen_name) {

                        $user_id = "-" . $group->id;
                        break;

                    }

                }

            }
            
        }
        
        return (int) $user_id;
        
    }
    
    public function warn_user(int $chat_local_id, int $user_id, string $message = 'Получил варн', $reason = 'Без причины'){
        $text_reason = ' (' . $reason . ')';
        
        
        $this->db->insert(
            'warn',
            '(NULL, :user_id, :chat_id, :date)',
            [
                "user_id" => $user_id, 
                "chat_id" => $chat_local_id, 
                "date"    => $this->date
            ]
        );
        
        
        $check_user_warn_in_chat = $this->db->select(
                        'warn', 'COUNT(*)',
                        'user_id=:user_id AND chat_id=:chat_id',
                        [
                            "user_id"   => $user_id,
                            "chat_id"   => $chat_local_id
                        ],1
                    )[0][0];
        
        
        if ($check_user_warn_in_chat > 2) {           
            
            $user_info = $this->get_info_user($user_id);
            $profile_reply = "@id$user_id ($user_info->first_name $user_info->last_name)";
            
            //$this->vk->send_message($profile_reply . ' ушел в мут за превышение количества предупреждений!', $this->data_new_message);
            $this->mute_user($chat_local_id, $user_id, $profile_reply.', Получил мут', $time_mute = 900, $reason = 'За превышение количества предупреждений');
            //$this->vk->removeChatUser($chat_local_id, $user_id);
            
//            
//            $this->db->insert(
//                'bans',
//                '(NULL, :user_id, :chat_id, :date)',
//                [
//                    "user_id" => $user_id, 
//                    "chat_id" => $chat_local_id, 
//                    "date"    => $this->date
//                ]
//            );
            
            $this->db->delete(
                'warn',
                'user_id=:user_id AND chat_id=:chat_id',
                [
                    "user_id" => $user_id, 
                    "chat_id" => $chat_local_id
                ]
            );
        } else {
        if ($message)
            $this->vk->send_message($message . ' ' . $check_user_warn_in_chat . '/3' . $text_reason, $this->data_new_message);
            
        }

    }
    
    public function mute_user(int $chat_local_id, int $user_id, string $message = '', $time_mute = 300, $reason = 'Без причины'){
        $text_reason = ' на '. $time_mute/60 . ' мин. (' . $reason . ')';
        
        if ($message)
            $this->vk->send_message($message . $text_reason, $this->data_new_message);
        
        $check_user_mute_in_chat = $this->db->select(
                        'mute', 'COUNT(*)',
                        'user_id=:user_id AND date>:date_end AND chat_id=:chat_id',
                        [
                            "user_id"   => $user_id, 
                            "date_end"  => ($this->date), 
                            "chat_id"   => $chat_local_id
                        ],1
                    )[0][0];
        if ($check_user_mute_in_chat == 0) {
            $this->db->insert(
                'mute',
                '(NULL, :user_id, :chat_id, :date)',
                [
                    "user_id" => $user_id, 
                    "chat_id" => $chat_local_id, 
                    "date"    => $this->date + $time_mute
                ]
            );
        }

    }

    public function kick_user (int $chat_local_id, int $user_id, bool $log = true, string $message = '') {

        if ($message)
            $this->vk->send_message($message, $this->data_new_message);

        $result = $this->vk->removeChatUser($chat_local_id, $user_id);

        if (isset($result->response) && $result->response == 1) {

            if ($log) {
                
                $check_user_ban_in_chat = $this->db->select(
                        'bans', 'COUNT(*)',
                        'user_id=:user_id AND date>:date_end AND chat_id=:chat_id',
                        [
                            "user_id"   => $user_id, 
                            "date_end"  => ($this->date - 86400), 
                            "chat_id"   => $chat_local_id
                        ], 1
                    )[0][0];

                if ($check_user_ban_in_chat == 0) {
                    $this->db->insert(
                        'bans',
                        '(NULL, :user_id, :chat_id, :date)',
                        [
                            "user_id" => $user_id, 
                            "chat_id" => $chat_local_id, 
                            "date"    => $this->date
                        ]
                    );
                }
                
            }
            
            if ($this->check_messages->access->kick) {
                
                $this->db->insert(
                    'kick_logs',
                    '(NULL, :user_id, :admin_id, :chat_id, :date)',
                    [
                        "user_id"   => $user_id,
                        "admin_id"  => $this->from_id,
                        "chat_id"   => $this->chat->local_id, 
                        "date"      => $this->date
                    ]  
                );
                
            }
            
        }

        return $result;

    }

    public function clear_chat (int $peer_id) {
        
        $mess = "Чистка чата!" . $this->new_line . $this->clear_chat;
        
        $this->vk->send_message($mess, $this->data_new_message);
        
    }
    public function admin_help (int $peer_id) {
        
        
        $cmds_desc = array_merge_recursive((array)$this->admin_commands, (array)$this->admin_commands_desc);
        
        $text_admin_help = 'Список админских команд:';
        $help_admin_cmd = $text_admin_help . $this->new_line;
        foreach($cmds_desc as $key => $value){
            $help_admin_cmd .= $value['0'] . " - " . $value['1'] . $this->new_line;    
        }
        
        $this->vk->send_message($help_admin_cmd, $this->data_new_message);
        
    }


    public function get_info_user (int $usr_id, bool $from_api = false, string $name_case = 'nom'): object {
        
        if ($usr_id > 0) {
        
            $user_info = [];
            foreach ($this->chat->members->profiles as $profile) {
                
                if ($profile->id == $usr_id) {

                    $user_info = $profile;
                    break;

                }

            }


            if (empty($user_info) || ($from_api || (isset($this->check_messages->access) && !$this->check_messages->access->control)))
                $user_info = $this->vk->users_get($usr_id, $name_case)[0];
            
            
        } else {        
        
                $user_info = [];
                foreach ($this->chat->members->groups as $group) {

                    if ($group->id == $usr_id*(-1)) {

                        $user_info = $group;
                        break;

                    }

                }

        }
        return $user_info;
        
    }


    public function declime_unactive_users (int $count): string {
    
        $title = "неактивных пользователей";
    
        if ($count < 10 || $count > 20) {
    
            $last_num = substr($count, (strlen($count) - 1));
    
            if ($last_num == 1)
                $title = "неактивный пользователь";
            elseif ($last_num > 1 && $last_num < 5)
                $title = "неактивных пользователя";
    
        }
        
        $text = $count . " " . $title;
        
        return $text;
        
    }
    
    public function declime_vote (int $num_vote): string {
        
        $last_num_vote = substr($num_vote, (strlen($num_vote) - 1));
        $word = "голосов";
        
        if ($num_vote < 10 || $num_vote > 20) {
            
            if ($last_num_vote == 1)
                $word = "голос";
            elseif ($last_num_vote > 1 && $last_num_vote < 5)
                $word = "голоса";
            
        }
        
        return $num_vote . " " . $word;
        
        
    }

    public function parse_timer (int $seconds): string {
    
        $last_seconds = $seconds;
    
        $minutes = explode(".", $last_seconds / 60)[0];
        $minutes_2 = $minutes;
        $hours = explode(".", $minutes / 60)[0];
        $hours_2 = $hours;
        
        $days = explode(".", $hours / 24)[0];
        
        $hours -= 24 * $days;
        
        $minutes -= 60 * $hours_2;
        $seconds = $last_seconds - (60 * $minutes_2);
        $text_timer = "";
        
        if ($days > 0)
            $text_timer .= $this->declOfNum($days, ['день', 'дня', 'дней']) . " ";
    
        if ($hours > 0)
            $text_timer .= $this->declOfNum($hours, ['час', 'часа', 'часов']) . " ";
        
        if ($minutes > 0)
            $text_timer .= $this->declOfNum($minutes, ['минута', 'минуты', 'минут']) . " ";
    
        $text_timer .= $this->declOfNum($seconds, ['секунда', 'секунды', 'секунд']) . " ";
        
        return $text_timer;
        
    }

    public function declOfNum($num, $titles) {

        $cases = array(2, 0, 1, 1, 1, 2);
        return $num . " " . $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]];
    
    }

}
?>