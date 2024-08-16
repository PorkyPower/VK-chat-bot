<?php
class Manager extends Controller {

    public function __construct(Routes $route) {
        parent::__construct($route);
    }

    public function kick (int $user_id): void {

        if (!in_array($user_id, $this->chat->admins->users) 
                && !in_array($user_id, $this->chat->admins->groups)) {

            if ($user_id > 0) {
                
                $user_info = $this->get_info_user($user_id);
                $profile_reply = "@id$user_id ($user_info->first_name $user_info->last_name)";
            }

            $this->kick_user(
                $this->chat->local_id, 
                $user_id, 
                true,
                ($user_id > 0) ? $profile_reply . $this->messages->kick : ''
            );

        }else {

            $this->vk->send_message($this->from_profile_reply . $this->messages->cant_kick_admin, $this->data_new_message);
            
        }

    }

    public function mute (string $message,int $user_id=0): void {
        $message = ($user_id == 0) ? $message : $user_id.' '.$message;
        $action_mess = explode(' ', $message);

        if (isset($action_mess[0])){
            $action_to = $action_mess[0];
            $to_user_id = $this->screen_name_parse($action_to);
            $to_user_id = str_replace("id", "", $to_user_id);
            $to_user_id = str_replace("-", "", $to_user_id);
            $user_id = $this->search_user_in_chat($to_user_id);
            
            if ($user_id <=0){
                $this->vk->send_message($this->from_profile_reply . $this->messages->cannot_find_user, $this->data_new_message);
                die();
            }
            
            if (isset($action_mess[1]) && $action_mess[1] != ''){
                $time_mute = $action_mess[1];
            } else {
                $time_mute = 5;
            }
            $time_mute = $time_mute*60;
            
            $action_name = '';

            for ($i=2;$i<count($action_mess);    $i++){
                    $action_name .= $action_mess[$i] . " ";
            }
            $action_name = rtrim($action_name, ' ');
            
            if ($action_name==''){
                $action_name = 'Без причины';
            }
            
            if (!in_array($user_id, $this->chat->admins->users) 
                    && !in_array($user_id, $this->chat->admins->groups)) {

                if ($user_id > 0) {

                    $user_info = $this->get_info_user($user_id);
                    $profile_reply = "@id$user_id ($user_info->first_name $user_info->last_name)";
                }
                
                $this->mute_user(
                    $this->chat->local_id, 
                    $user_id, 
                    $profile_reply.', Получил мут' , 
                    $time_mute, 
                    $action_name
                );

            }else {

                $this->vk->send_message($this->from_profile_reply . $this->messages->cant_mute_admin, $this->data_new_message);

            }
        } else {
                $this->vk->send_message($this->from_profile_reply . $this->messages->cannot_find_user, $this->data_new_message);
            
        }

    }

    public function warn (string $message,int $user_id=0): void {
        $message = ($user_id == 0) ? $message : $user_id.' '.$message;
        $action_mess = explode(' ', $message);

        if (isset($action_mess[0])){
            $action_to = $action_mess[0];
            $to_user_id = $this->screen_name_parse($action_to);
            $to_user_id = str_replace("id", "", $to_user_id);
            $to_user_id = str_replace("-", "", $to_user_id);
            $user_id = $this->search_user_in_chat($to_user_id);

            $action_name = '';

            for ($i=1;$i<count($action_mess);    $i++){
                    $action_name .= $action_mess[$i] . " ";
            }
            $action_name = rtrim($action_name, ' ');

            if (!in_array($user_id, $this->chat->admins->users) 
                    && !in_array($user_id, $this->chat->admins->groups)) {

                if ($user_id > 0) {

                    $user_info = $this->get_info_user($user_id);
                    $profile_reply = "@id$user_id ($user_info->first_name $user_info->last_name)";
                }

                $this->warn_user(
                    $this->chat->local_id, 
                    $user_id, 
                    ($user_id > 0) ? $profile_reply . $this->messages->warn : '',
                    $action_name
                );

            }else {

                $this->vk->send_message($this->from_profile_reply . $this->messages->cant_warn_admin, $this->data_new_message);

            }
        } else {
                $this->vk->send_message($this->from_profile_reply . $this->messages->cannot_find_user, $this->data_new_message);
            
        }

    }
    public function test(){
        $this->vk->send_message("Не клацай, блт", $this->data_new_message);
    }
    
    public function bandit($bet){
        $user_id = $this->from_id;
        if (count(explode(" ", $bet)) > 1){
            $this->vk->send_message($this->from_profile_reply . ", неверная ставка", $this->data_new_message);
            die();
        }
        if ($bet == '') {
            $this->vk->send_message($this->from_profile_reply . ", неверная ставка", $this->data_new_message);
            die();
        }
        
        if ($bet == 'всё' || $bet == 'все'){
            if ($this->money > 0){
                $bet = $this->money;
            } else {
                $this->vk->send_message($this->from_profile_reply . ", недостаточно Рё", $this->data_new_message);
            }
        }
        
        if (gettype((int)$bet) == 'integer'){
            if ($this->money >= $bet){
                
                $bandit = '&#127920;';
                
                $seven = '7&#8419;';
                $brill = '&#128142;';
                $vish = '&#127826;';
                $arb = '&#127817;';
                $lim = '&#127819;';
                $banan = '&#127820;';
                $serd = '&#10084;';
                $vinogr = '&#127815;';
                
                $win_mass = [
                    '1' => 200,
                    '2' => 100,
                    '3' => 70,
                    '4' => 50,
                    '5' => 40,
                    '6' => 30,
                    '7' => 20,
                    '8' => 10,
                ];
                
                $mass = [
                    '1' => $seven,
                    '2' => $brill,
                    '3' => $serd,
                    '4' => $arb,
                    '5' => $lim,
                    '6' => $banan,
                    '7' => $vish,
                    '8' => $vinogr,
                ];
                
                $one = rand(1,8);
                $two = rand(1,8);
                $tree = rand(1,8);

                
                $co = '0';
                $how_x = '';
                
                
                if ($one == $two && $two == $tree){
                    $co = $bet*$win_mass[$one];
                    $how_x = 'x'.$win_mass[$one];
                } else {
                    if ($one == 1 && $two == 1 || $tree == 1 && $two == 1 || $tree == 1 && $one == 1 ){
                        $co = $bet*3;
                        $how_x = 'x3';
                    } elseif ($one == 1 || $two == 1 || $tree == 1){
                        $co = $bet*2;
                        $how_x = 'x2';
                    }
                }
                
                $res = $this->from_profile_reply . ', дергает рычаг '
                    .$bandit.$this->new_line.'Выпало '
                    .$mass[$one].$mass[$two].$mass[$tree]. ' '.$how_x. $this->new_line
                    .'Выигрыш '.$co.' Рё';
                
                $mess = 'Таблица выигрышей!'. $this->new_line
                    .$mass[1].$mass[1].$mass[1]. ' - x200' . $this->new_line
                    .$mass[2].$mass[2].$mass[2]. ' - x100' . $this->new_line
                    .$mass[3].$mass[3].$mass[3]. ' - x70' . $this->new_line
                    .$mass[4].$mass[4].$mass[4]. ' - x50' . $this->new_line
                    .$mass[5].$mass[5].$mass[5]. ' - x40' . $this->new_line
                    .$mass[6].$mass[6].$mass[6]. ' - x30' . $this->new_line
                    .$mass[7].$mass[7].$mass[7]. ' - x20' . $this->new_line
                    .$mass[8].$mass[8].$mass[8]. ' - x10' . $this->new_line.$this->new_line;
                
                $this->vk->send_message($mess.$res, $this->data_new_message);
                
                    $money_res = $co - $bet;
                
                
                $this->db->update(
                    'users',
                    'money=money+:money_res',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "money_res" => $money_res, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $this->from_id
                    ], 1
                );
                
                
            } else {
                $this->vk->send_message($this->from_profile_reply . ", недостаточно Рё", $this->data_new_message);
            }
        } else {
            $this->vk->send_message($this->from_profile_reply . ", неверная ставка", $this->data_new_message);
        }
        
    }
    public function casino($bet){
        $user_id = $this->from_id;
        if (count(explode(" ", $bet)) > 1){
            $this->vk->send_message($this->from_profile_reply . ", неверная ставка", $this->data_new_message);
            die();
        }
        if ($bet == '') {
            $this->vk->send_message($this->from_profile_reply . ", неверная ставка", $this->data_new_message);
            die();
        }
        
        if ($bet == 'всё' || $bet == 'все'){
            if ($this->money > 0){
                $bet = $this->money;
            } else {
                $this->vk->send_message($this->from_profile_reply . ", недостаточно Рё", $this->data_new_message);
            }
        }
        
        if (gettype((int)$bet) == 'integer'){
            if ($this->money >= $bet){
                $rand = rand(0,1);
                if ($rand == '0'){
                    $this->vk->send_message($this->from_profile_reply . ', ставка не зашла. Проиграл '.$bet.' Рё', $this->data_new_message);            
                    $this->db->update(
                        'users',
                        'money=money-:bet',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "bet" => $bet, 
                            "chat_id" => $this->chat->local_id,
                            "user_id" => $this->from_id
                        ], 1
                    );
                } else {
                    $this->vk->send_message($this->from_profile_reply . ', ставка зашла. Выиграл '.$bet.' Рё', $this->data_new_message);                   
                    $this->db->update(
                        'users',
                        'money=money+:bet',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "bet" => $bet, 
                            "chat_id" => $this->chat->local_id,
                            "user_id" => $this->from_id
                        ], 1
                    );
                }
            } else {
                $this->vk->send_message($this->from_profile_reply . ", недостаточно Рё", $this->data_new_message);
            }
        } else {
            $this->vk->send_message($this->from_profile_reply . ", неверная ставка", $this->data_new_message);
        }
        
    }
    
    public function bonus(){
        $user_id=$this->from_id;
        $bonus_count = '75';
        
        if ($user_id <= 0){
            die();
        }

         $check_last_bonus = $this->db->select(
            'users', 'COUNT(*)', 
            'user_id=:user_id AND last_bonus>:last_bonus AND chat_id=:chat_id', 
            [
                "user_id"   => $this->from_id, 
                "last_bonus"  => ($this->date), 
                "chat_id"   => $this->chat->local_id
            ], 1
        )[0][0];

        if ($check_last_bonus != 0){
            $this->vk->send_message($this->from_profile_reply.', бонус можно взять раз в 24 часа!', $this->data_new_message);
            die();
        } else {
            $this->db->update(
                'users',
                'money=money+:bonus_count,last_bonus=:last_bonus',
                'chat_id=:chat_id AND user_id=:user_id',
                [
                    "bonus_count" => $bonus_count, 
                    "last_bonus" => ($this->date + 86400), 
                    "chat_id" => $this->chat->local_id,
                    "user_id" => $this->from_id
                ], 1
            );
            $this->vk->send_message($this->from_profile_reply.' получил бонус '.$bonus_count.' Рё&#128180;', $this->data_new_message);
        }
    }
        
    public function gift(){
        
        
        $this->vk->send_message($this->from_profile_reply.', раздача окончена! Ожидаем следующего события!', $this->data_new_message);
        return;
        
        $user_id=$this->from_id;
        $bonus_count = '250';
        $gifts = ['item','money','rep'];
        $rrand = rand(0,2);
        if ($rrand == 0){
            $rand_gift = $gifts[rand(1,count($gifts)-1)];
        } else {
            $rand_gift = $gifts[0];
        }
        
        if ($user_id <= 0){
            die();
        }

         $check_last_gift = $this->db->select(
            'users', 'COUNT(*)', 
            'user_id=:user_id AND last_gift>:last_gift AND chat_id=:chat_id', 
            [
                "user_id"   => $this->from_id, 
                "last_gift"  => ($this->date), 
                "chat_id"   => $this->chat->local_id
            ], 1
        )[0][0];

        if ($check_last_gift != 0){
            $this->vk->send_message($this->from_profile_reply.', подарок можно взять раз в 8 часов!', $this->data_new_message);
            die();
        } else {
            $attch =  [
                'type'       => 'photo',
                'owner_id'   => '-222251441',
                'media_id'   => '457239092'
            ];
            if ($rand_gift == 'money'){
                $attch =  [
                    'type'       => 'photo',
                    'owner_id'   => '-222251441',
                    'media_id'   => '457239095'
                ];
                $this->db->update(
                    'users',
                    'money=money+:bonus_count,last_gift=:last_gift',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "bonus_count" => $bonus_count, 
                        "last_gift" => ($this->date + 28800), 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $this->from_id
                    ], 1
                );
                $this->vk->send_message($this->from_profile_reply.' получил подарок '.$bonus_count.' Рё&#128180;', $this->data_new_message, [$attch]);
            }
            if ($rand_gift == 'rep'){
                $attch =  [
                    'type'       => 'photo',
                    'owner_id'   => '-222251441',
                    'media_id'   => '457239099'
                ];
                $this->db->update(
                    'users',
                    'rep=rep+1, last_gift=:last_gift',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "last_gift" => ($this->date + 28800), 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $this->from_id
                    ], 1
                );
                $this->vk->send_message($this->from_profile_reply.' получил +1 к репутации от @makima_bot(Makima)!', $this->data_new_message, [$attch]);
            }
            if ($rand_gift == 'item'){
                $gift_items = [
                    [
                        'id'        => '12',
                        'name'      => 'Снегурочка',
                        'count'     => '0',
                        'preview'   => '457239083'
                    ],
                    [
                        'id'        => '13',
                        'name'      => 'Снеговик',
                        'count'     => '0',
                        'preview'   => '457239088'
                    ],
                    [
                        'id'        => '14',
                        'name'      => 'Новогодняя ёлка',
                        'count'     => '0',
                        'preview'   => '457239089'
                    ],
                    [
                        'id'        => '15',
                        'name'      => 'Свитер акацуки',
                        'count'     => '0',
                        'preview'   => '457239090'
                    ],
                    [
                        'id'        => '16',
                        'name'      => 'Олень',
                        'count'     => '0',
                        'preview'   => '457239091'
                    ],
                    [
                        'id'        => '17',
                        'name'      => 'Магический шар',
                        'count'     => '0',
                        'preview'   => '457239093'
                    ],
                    [
                        'id'        => '18',
                        'name'      => 'Посох',
                        'count'     => '0',
                        'preview'   => '457239094'
                    ],
                    [
                        'id'        => '19',
                        'name'      => 'Катана Мурамаса',
                        'count'     => '0',
                        'preview'   => '457239097'
                    ],

                ];
                $rand_gift_items = $gift_items[rand(0,count($gift_items)-1)];
                $prev = $rand_gift_items["preview"];
                $id_gift = $rand_gift_items["id"];
                $name_gift = $rand_gift_items["name"];
                $count_gift = $rand_gift_items["count"];
                
                $attch =  [
                    'type'       => 'photo',
                    'owner_id'   => '-222251441',
                    'media_id'   => $prev
                ];
                $this->db->update(
                    'users',
                    'last_gift=:last_gift',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "last_gift" => ($this->date + 28800), 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $this->from_id
                    ], 1
                );
                $this->db->insert(
                        'items',
                        '(NULL, :chat_id, :user_id, :item_id, :name, :count, :preview)',
                        [
                            "chat_id" => $this->chat->local_id, 
                            "user_id"      => $this->from_id, 
                            "item_id"  => $id_gift,
                            "name"  => $name_gift,
                            "count"  => $count_gift,
                            "preview"  => $prev,
                        ]
                );
                $this->vk->send_message($this->from_profile_reply.' получил предмет '.$rand_gift_items["name"].'!', $this->data_new_message, [$attch]);
            }
        }
    }
    
    public function updaterang($user_id, $user_rang, $user_money_rang){
            $user_money = $this->money - $user_money_rang;
            $user_rang = $user_rang + 1;       
        
            switch($user_rang){
                case 1:
                    $namerang = 'Ученик Академии&#10084;';
                    break;
                case 2:
                    $namerang = 'Генин&#128153;';
                    break;
                case 3:
                    $namerang = 'Чунин&#128154;';
                    break;
                case 4:
                    $namerang = 'Токубетсу Джонин&#128155;';
                    break;
                case 5:
                    $namerang = 'Джонин&#128156;';
                    break;
                case 6:
                    $namerang = 'Каге&#128420;';
                    break;
            }
        
            $this->db->update(
                'users',
                'rang=:user_rang, money=:user_money',
                'chat_id=:chat_id AND user_id=:user_id',
                [
                    "user_rang" => $user_rang, 
                    "user_money" => $user_money, 
                    "chat_id" => $this->chat->local_id,
                    "user_id" => $user_id
                ], 1
            );
        
            return $namerang;
    }
    
    
    public function buyitem($user_id, $item_id, $type){
        $mess = '';
        $finditem = [];
        
        for ($i=0;$i<count($this->$type);$i++){
            if ($this->$type[$i]['id'] == $item_id){
                $finditem ['id'] = $this->$type[$i]['id'];
                $finditem ['name'] = $this->$type[$i]['name'];
                $finditem ['count'] = $this->$type[$i]['count'];
                $finditem ['preview'] = $this->$type[$i]['preview'];
            }
        }
        
        if ($this->money >= $finditem['count']){
            $user_money = $this->money - $finditem['count'];
            
            $this->db->update(
                'users',
                'money=:user_money',
                'chat_id=:chat_id AND user_id=:user_id',
                [
                    "user_money" => $user_money, 
                    "chat_id" => $this->chat->local_id,
                    "user_id" => $user_id
                ], 1
            );
            
            $this->db->insert(
                    'items',
                    '(NULL, :chat_id, :user_id, :item_id, :name, :count, :preview)',
                    [
                        "chat_id" => $this->chat->local_id, 
                        "user_id"      => $user_id, 
                        "item_id"  => $item_id,
                        "name"  => $finditem['name'],
                        "count"  => $finditem['count'],
                        "preview"  => $finditem['preview'],
                    ]
            );
        $mess = 'Купил '. $finditem['name'];
            
        } else {
            $mess = 'Недостаточно Рё';
        }
        
        
        
        return $mess;
    
    }public function bar_drink($user_id, $item_id){
        $mess = '';
        $finditem = [];
        
        for ($i=0;$i<count($this->bar);$i++){
            if ($this->bar[$i]['id'] == $item_id){
                $finditem ['id'] = $this->bar[$i]['id'];
                $finditem ['name'] = $this->bar[$i]['name'];
                $finditem ['count'] = $this->bar[$i]['count'];
                $finditem ['preview'] = $this->bar[$i]['preview'];
            }
        }
        
        if ($this->money >= $finditem['count']){
            $user_money = $this->money - $finditem['count'];
            
            $this->db->update(
                'users',
                'money=:user_money, last_drink=:last_drink',
                'chat_id=:chat_id AND user_id=:user_id',
                [
                    "user_money" => $user_money, 
                    "last_drink" => ($this->date + 300), 
                    "chat_id" => $this->chat->local_id,
                    "user_id" => $user_id
                ], 1
            );
        $mess = 'Выпил ' . $finditem['name'];
        $this->vk->send_message( $this->from_profile_reply.' выпил '.$finditem['name']. ' и опьянел на 5 минут', $this->data_new_message);
            
        } else {
            $mess = 'Недостаточно Рё';
        }
        
        
        
        return $mess;
    
    }
    
    public function top_mess(){
        $top_mess = $this->db->select(
            'users', 'user_id, count_mess', 
            'chat_id=:chat_id', 
            ["chat_id" => $this->chat->local_id], 10, 0, 
            'count_mess DESC'
        );
        
        $count_mess = 0;
        $text_mess = '';
        foreach ($top_mess as $top_mess_user) {
            
            $user_info = $this->get_info_user($top_mess_user['user_id']);
            
            if (isset($user_info)) {
                $profile_reply = "$user_info->first_name $user_info->last_name";
                $text_mess .= $profile_reply. ' - ' . $top_mess_user['count_mess'] . $this->new_line;
                
            }
        }
        $this->vk->send_message( 'Топ сообщений:'.$this->new_line.$text_mess, $this->data_new_message);
    }
    
    public function bar(){
        $mess = '';
        $photo = [];
        foreach ($this->bar as $item){
            foreach($item as $key=>$value){
                if ($key == 'name'){
                    $mess .= $value; 
                    $mess .= ' - ';
                } 
                if ($key == 'count'){
                    $mess .= $value;
                    $mess .= ' Рё';
                }
                if ($key == 'preview'){
                    $photo[] = $value;
                }
                
            }
            $mess .= $this->new_line;
        }
        $attch = [];
        for ($i=0;$i<count($photo);$i++){
            $attch[] =  [
                'type'       => 'photo',
                'owner_id'   => '-222251441',
                'media_id'   => $photo[$i]
            ];
        }
        
        
        
            $sort_btns = [];
            $bts = [];        
            $cur_num_arr = 0;
            $i_num_arr = 1;
            
            foreach($this->bar as $value){
                
                if ($cur_num_arr<0){
                    $btns = [
                        'action'    => [
                            'type'      => 'callback',
                            'payload'   => ['type' => 'drink',
                                            'id' => $value['id']],
                            'label'     => "Выпить ". $value['name']. ' - '.$value['count']. ' Рё'
                        ],
                        'color'     => 'primary'               
                    ];
                    array_push($bts, $btns);
                    
                    if (count((array)$this->bar) == $i_num_arr){
                        array_push($sort_btns, $bts);
                    }
                } else {
                    $btns = [
                        'action'    => [
                            'type'      => 'callback',
                            'payload'   => ['type' => 'drink',
                                            'id' => $value['id']],
                            'label'     => "Выпить ". $value['name']. ' - '.$value['count']. ' Рё'
                        ],
                        'color'     => 'primary'               
                    ];
                    array_push($bts, $btns);
                    
                    array_push($sort_btns, $bts);
                    $bts = [];
                    
                    $cur_num_arr = 0;   
                }
                
                $cur_num_arr ++;
                $i_num_arr ++;

            }

            $keyb = [
                'inline'    => true,
                'buttons'   => (array)$sort_btns
            ];
        
        
        $this->vk->send_message( $mess, $this->data_new_message, $attch, $keyb);
    }
    
    public function shop($type){
        if ($type=='shop'){
            $larek = [
                    'action'    => [
                        'type'      => 'text',
                        'payload'   => ['type' => "shop"],
                        'label'     => "!ларек"
                    ],
                    'color'     => 'primary'               
            ];             
            $auto = [
                    'action'    => [
                        'type'      => 'text',
                        'payload'   => ['type' => "shop"],
                        'label'     => "!автосалон"
                    ],
                    'color'     => 'primary'               
            ];              
            $butik = [
                    'action'    => [
                        'type'      => 'text',
                        'payload'   => ['type' => "shop"],
                        'label'     => "!бутик"
                    ],
                    'color'     => 'primary'               
            ];              
            $bar = [
                    'action'    => [
                        'type'      => 'text',
                        'payload'   => ['type' => "shop"],
                        'label'     => "!бар"
                    ],
                    'color'     => 'primary'               
            ];   
            $keyb = [
                'inline'    => true,
                'buttons'   => [(array($larek,$auto,$butik,$bar))]
            ];
            $this->vk->send_message( 'Торговый центр', $this->data_new_message, [], $keyb);
            die();
        }
        $mess = '';
        $photo = [];
        foreach ($this->$type as $item){
            foreach($item as $key=>$value){
                if ($key == 'name'){
                    $mess .= $value; 
                    $mess .= ' - ';
                } 
                if ($key == 'count'){
                    $mess .= $value;
                    $mess .= ' Рё';
                }
                if ($key == 'preview'){
                    $photo[] = $value;
                }
                
            }
            $mess .= $this->new_line;
        }
        $attch = [];
        for ($i=0;$i<count($photo);$i++){
            $attch[] =  [
                'type'       => 'photo',
                'owner_id'   => '-222251441',
                'media_id'   => $photo[$i]
            ];
        }
        
        
        
            $sort_btns = [];
            $bts = [];        
            $cur_num_arr = 0;
            $i_num_arr = 1;
            
            foreach($this->$type as $value){
                
                if ($cur_num_arr<0){
                    $btns = [
                        'action'    => [
                            'type'      => 'callback',
                            'payload'   => ['type' => 'buy',
                                            'id' => $value['id'],
                                            'typebuy' => $type],
                            'label'     => "Купить ". $value['name']. ' - '.$value['count']. ' Рё'
                        ],
                        'color'     => 'primary'               
                    ];
                    array_push($bts, $btns);
                    
                    if (count((array)$this->$type) == $i_num_arr){
                        array_push($sort_btns, $bts);
                    }
                } else {
                    $btns = [
                        'action'    => [
                            'type'      => 'callback',
                            'payload'   => ['type' => 'buy',
                                            'id' => $value['id'],
                                            'typebuy' => $type],
                            'label'     => "Купить ". $value['name']. ' - '.$value['count']. ' Рё'
                        ],
                        'color'     => 'primary'               
                    ];
                    array_push($bts, $btns);
                    
                    array_push($sort_btns, $bts);
                    $bts = [];
                    
                    $cur_num_arr = 0;   
                }
                
                $cur_num_arr ++;
                $i_num_arr ++;

            }

            $keyb = [
                'inline'    => true,
                'buttons'   => (array)$sort_btns
            ];
        
        
        $this->vk->send_message( $mess, $this->data_new_message, $attch, $keyb);
    }
    
    public function profile(){
        $rang = '1';
        $namerang = 'Ученик Академии&#10084;';
        $upgradecount = '100';
        switch($this->check_messages->access->rang){
            case 1:
                $rang = 1;
                $namerang = 'Ученик Академии&#10084;';
                $upgradecount = '1000';
                break;
            case 2:
                $rang = 2;
                $namerang = 'Генин&#128153;';
                $upgradecount = '5000';
                break;
            case 3:
                $rang = 3;
                $namerang = 'Чунин&#128154;';
                $upgradecount = '20000';
                break;
            case 4:
                $rang = 4;
                $namerang = 'Токубетсу Джонин&#128155;';
                $upgradecount = '50000';
                break;
            case 5:
                $rang = 5;
                $namerang = 'Джонин&#128156;';
                $upgradecount = '500000';
                break;
            case 6:
                $rang = 6;
                $namerang = 'Каге&#128420;';
                $upgradecount = '0';
                break;
        }
        $btns = [
                'action'    => [
                    'type'      => 'callback',
                    'payload'   => ['type' => "upgrade",
                                   'count' => $upgradecount],
                    'label'     => "Повысить ранг за $upgradecount Рё?"
                ],
                'color'     => 'primary'               
        ];
        $keyb = [
            'inline'    => true,
            'buttons'   => [(array($btns))]
        ];
        if ($upgradecount == 0){
            $keyb = [];
        }
        
        $get_items = $this->db->select(
                        'items', 'item_id,name,count,preview', 
                        'chat_id=:chat_id AND user_id=:user_id', 
                        [
                            "chat_id" => $this->chat->local_id, 
                            "user_id" => $this->from_id
                        ]
        );
        
        $user_items = '';
        if (isset($get_items)){
            foreach($get_items as $get_item){
                $user_items .= $get_item['name'] . ', ';
            }
            $user_items = rtrim($user_items, ", ");
        } 
        
        
        $mess = $this->from_profile_reply. $this->new_line.'Твой ранг ' . $rang . ' - ' . $namerang . $this->new_line
            . 'Баланс - ' . $this->money . ' Рё&#128180;' . $this->new_line
            . 'Репутация - ' . $this->rep. '&#10024;' . $this->new_line
            . 'Предметы - ' . $user_items;
        $this->vk->send_message( $mess, $this->data_new_message, [], $keyb);
    }
    
    public function russian_roulette(){
        $rand_rr = mt_rand(0, 2);
        $lucky_mess = "&#128560;&#128299; На этот раз тебе повезло!";
        $unlucky_mess = "&#128565;&#128299; Не повезло!";
        
        $btns = [
                        'action'    => [
                            'type'      => 'text',
                            'payload'   => ['srv' => "roulette"],
                            'label'     => "!рулетка"
                        ],
                        'color'     => 'primary'               
                    ];
        $keyb = [
            'inline'    => true,
            'buttons'   => [(array($btns))]
        ];
        
        if ($rand_rr != 0){
            $this->vk->send_message($lucky_mess . $this->new_line . "Еще раз?", $this->data_new_message, [], $keyb);
        } else {
            $this->vk->send_message($unlucky_mess, $this->data_new_message);
            if (!$this->check_messages->access->is_admin){
                $this->kick_user(
                    $this->chat->local_id, 
                    $this->from_id, 
                    true
                );
            }

        }
    }
    
    public function role_play ($mess_do, $reply = '0') {
        $mess_do = ($reply == 0) ? $mess_do : $mess_do.' '.$reply;
        $action_mess = explode(' ', $mess_do);
    
        $action_to = $action_mess[count($action_mess)-1];
        $to_user_id = $this->screen_name_parse($action_to);
        $to_user_id = str_replace("id", "", $to_user_id);
        $to_user_id = str_replace("-", "", $to_user_id);
        $to_user_id = $this->search_user_in_chat($to_user_id);
        
        $action_name = '';

        for ($i=0;$i<count($action_mess) - (($to_user_id == 0) ? 0 : 1);    $i++){
                $action_name .= $action_mess[$i] . " ";
        }
        $action_name = rtrim($action_name, ' ');
        
        $from_id = $this->get_info_user($this->from_id);
        $profile_reply = "@id$from_id->id ($from_id->first_name $from_id->last_name)";
        if ($from_id->sex == 1) { 
            $sex = 'а'; 
        }else { 
            $sex = '';
        }   
        $this_act = explode(' ', $action_name);
        $act_name = $this_act[0];
        if (isset($this_act[1])){
            $act_name_two = $this_act[0].' '.$this_act[1];
        } else {
            $act_name_two = $this_act[0];
        }
        
        
        if ($act_name == 'обнять'){
            $action_name = str_replace('обнять', 'обнял'.$sex.'&#129303;', mb_strtolower($action_name));
            $photo = ['457239019','457239020','457239018'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        if ($act_name == 'ударить'){
            $action_name = str_replace('ударить', 'ударил'.$sex.'&#128074;', mb_strtolower($action_name));
            $photo = ['457239025','457239026','457239027','457239030'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        if ($act_name == 'поцеловать'){
            $action_name = str_replace('поцеловать', 'поцеловал'.$sex.'&#128538;', mb_strtolower($action_name));
            $photo = ['457239031','457239032','457239033'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        if ($act_name_two == 'пожать руку'){
            $action_name = str_replace('пожать руку', 'пожал'.$sex.' руку&#129309;', mb_strtolower($action_name));
            $photo = ['457239034','457239035','457239036','457239037'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        if ($act_name == 'кусь'){
            $action_name = str_replace('кусь', 'укусил'.$sex, mb_strtolower($action_name));
            $photo = ['457239038','457239039'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        if ($act_name == 'пнуть'){
            $action_name = str_replace('пнуть', 'пнул'.$sex, mb_strtolower($action_name));
            $photo = ['457239040','457239041','457239042'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        if ($act_name == 'шлепнуть'){
            $action_name = str_replace('шлепнуть', 'шлепнул'.$sex, mb_strtolower($action_name));
            $photo = ['457239070','457239071','457239072'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        if ($act_name_two == 'дать пять'){
            $action_name = str_replace('дать пять', 'дал'.$sex.' пять', mb_strtolower($action_name));
            $photo = ['457239073','457239074'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
        }
        $make_rp = $action_name;
        
        
        $attch = [
            'type'       => 'photo',
            'owner_id'   => '-222251441',
            'media_id'   => $media_id
        ];
        

        if (mb_strtolower($action_to) == "всех" || mb_strtolower($action_to) == "всем"){        
            $this->vk->send_message($profile_reply . ' ' . $make_rp, $this->data_new_message);
        } else {
            if ($to_user_id != 0){
                if ($to_user_id > 0){
                    $to_id = $this->get_info_user($to_user_id, false, "gen");
                    $profile_reply_to = "@id$to_id->id ($to_id->first_name_gen $to_id->last_name_gen)";
                } else {
                    $to_id = $this->get_info_user($to_user_id);
                    $profile_reply_to = "@club$to_id->id ($to_id->name)";
                }

                $this->vk->send_message($profile_reply . ' ' . $make_rp . " " . $profile_reply_to, $this->data_new_message, [$attch]); 
            } else {
                $this->vk->send_message($profile_reply . ' ' . $make_rp . " ", $this->data_new_message, [$attch]); 
            }
        }
    }

    public function update (): void {
        $update_text = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/vkbotchat/update.txt', true);
        if ($update_text) {
            $this->vk->send_message($update_text , $this->data_new_message);
            
        }
        
    }

    public function rules (): void {
        $rules_text = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/vkbotchat/rules.txt', true);
        if ($rules_text) {
            $message = '';
            if (isset($this->data->object->message->action->type)){
                if ($this->data->object->message->action->type == "chat_invite_user")
                    $user_info = $this->get_info_user($this->data->object->message->action->member_id);
            }
            else {
                $user_info = $this->get_info_user($this->from_id);
            }

            $profile_reply = "@id" . $this->from_id . " (" . $user_info->first_name . " " . $user_info->last_name . ")";
            $message .= $profile_reply . ", " . $this->new_line;

            
            if (isset($this->data->object->message->action->type)){
                if ($this->data->object->message->conversation_message_id == 1 || $this->data->object->message->action->type == "chat_invite_user")
                    $message .= $this->messages->welcome_invite . $this->new_line;
            }

            $this->vk->send_message($message . $rules_text , $this->data_new_message);
            
        }
        
    }

    public function help (): void {
        $help_text = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/vkbotchat/help.txt', true);
        if ($help_text) {
            
            $sort_btns = [];
            $bts = [];        
            $cur_num_arr = 0;
            $i_num_arr = 1;
            
            foreach($this->ru_commands as $key => $value){
                
                if ($cur_num_arr<3){
                    $btns = [
                        'action'    => [
                            'type'      => 'text',
                            'payload'   => ['srv' => $value],
                            'label'     => $value
                        ],
                        'color'     => 'primary'               
                    ];
                    array_push($bts, $btns);
                    
                    if (count((array)$this->ru_commands) == $i_num_arr){
                        array_push($sort_btns, $bts);
                    }
                } else {
                    $btns = [
                        'action'    => [
                            'type'      => 'text',
                            'payload'   => ['srv' => $value],
                            'label'     => $value
                        ],
                        'color'     => 'primary'               
                    ];
                    array_push($bts, $btns);
                    
                    array_push($sort_btns, $bts);
                    $bts = [];
                    
                    $cur_num_arr = 0;   
                }
                
                $cur_num_arr ++;
                $i_num_arr ++;

            }

            $keyb = [
                'inline'    => true,
                'buttons'   => (array)$sort_btns
            ];
            
            $cmds_desc = array_merge_recursive((array)$this->ru_commands, (array)$this->ru_commands_desc);
            
            $help_cmd = $this->new_line;
            foreach($cmds_desc as $key => $value){
                $help_cmd .= $value['0'] . " - " . $value['1'] . $this->new_line;    
            }
            


            $message = "";
            $txtchat = 'Список актуальных команд!'. $help_cmd;

            if (isset($this->data->object->message->action->type)){
                if ($this->data->object->message->action->type == "chat_invite_user")
                    $user_info = $this->get_info_user($this->data->object->message->action->member_id);
            }
            else {
                $user_info = $this->get_info_user($this->from_id);
            }

            $profile_reply = "@id" . $this->from_id . " (" . $user_info->first_name . " " . $user_info->last_name . ")";
            $message .= $profile_reply . ", " . $this->new_line;

            
            if (isset($this->data->object->message->action->type)){
                if ($this->data->object->message->conversation_message_id == 1 || $this->data->object->message->action->type == "chat_invite_user")
                    $message .= $this->messages->welcome_invite . $this->new_line;
            }

            $this->vk->send_message($message . $txtchat , $this->data_new_message);
            //$this->vk->send_message($message . $txtchat , $this->data_new_message, [], $keyb);

        }

    }

    public function call_everyone (string $text = ""): void {

        $users = [];

        for ($i = 0; $i < 20; $i++) {
            
            $count_users_chat = count($this->chat->members->profiles);
            
            if ($count_users_chat > 0) {
            
                $rand_usr = mt_rand(0, ($count_users_chat - 1));

                $users[] = $this->chat->members->profiles[$rand_usr];

                unset($this->chat->members->profiles[$rand_usr]);
                sort($this->chat->members->profiles);
                
            }else {
                
                break;
                
            }
            
        }

        $message_text = "";

        foreach ($users as $user)
            $message_text .= "@id" . $user->id . " (" . $user->first_name . "), ";

        $message_text .= (!empty($text)) ? $text : $this->messages->call_everyone;

        $this->vk->send_message($message_text, $this->data_new_message);

    }

    public function bots (int $status): void {

        if ($this->check_security()) {

            $this->db->update(
                'control_chats',
                'bots=:bots',
                'local_chat_id=:local_chat_id',
                [
                    "bots" => $status, 
                    "local_chat_id" => $this->chat->local_id
                ], 1
            );
            
            $message = ($status) ? $this->messages->bots_enabled : $this->messages->bots_disabled;
            $this->vk->send_message($message, $this->data_new_message);

        }

    }

    public function invites (int $invites): void {

        if ($this->check_security()) {
            
            $this->db->update(
                'control_chats',
                'invites=:invites',
                'local_chat_id=:local_chat_id',
                [
                    "invites" => $invites, 
                    "local_chat_id" => $this->chat->local_id
                ], 1
            );
            
            $message = ($invites) ? $this->messages->invites_enabled : $this->messages->invites_disabled;

            $this->vk->send_message($message, $this->data_new_message);

        }

    }

    public function links (int $links): void {

        if ($this->check_security()) {
            
            $this->db->update(
                'control_chats',
                'links=:links',
                'local_chat_id=:local_chat_id',
                [
                    "links" => $links, 
                    "local_chat_id" => $this->chat->local_id
                ], 1
            );
            
            $message = ($links) ? $this->messages->links_enabled : $this->messages->links_disabled;
            $this->vk->send_message($message, $this->data_new_message);

        }

    }
    public function censor (int $censor): void {

        if ($this->check_security()) {
            
            $this->db->update(
                'control_chats',
                'censor=:censor',
                'local_chat_id=:local_chat_id',
                [
                    "censor" => $censor, 
                    "local_chat_id" => $this->chat->local_id
                ], 1
            );
            
            $message = ($censor) ? $this->messages->censor_enabled : $this->messages->censor_disabled;
            $this->vk->send_message($message, $this->data_new_message);

        }

    }
    public function repost (int $repost): void {

        if ($this->check_security()) {
            
            $this->db->update(
                'control_chats',
                'repost=:repost',
                'local_chat_id=:local_chat_id',
                [
                    "repost" => $repost, 
                    "local_chat_id" => $this->chat->local_id
                ], 1
            );
            
            $message = ($repost) ? $this->messages->repost_enabled : $this->messages->repost_disabled;
            $this->vk->send_message($message, $this->data_new_message);

        }

    }

    public function nude (int $nude): void {

        if ($this->check_security()) {

            $message = $this->messages->error_nude_enable;

            if ($this->config->algorithmia) {

                $this->db->update(
                    'control_chats',
                    'nude_security=:nude_security',
                    'local_chat_id=:local_chat_id',
                    [
                        "nude_security" => $nude, 
                        "local_chat_id" => $this->chat->local_id
                    ], 1
                );
                
                $message = ($nude) ? $this->messages->nude_enabled : $this->messages->nude_disabled;
    
            }

            $this->vk->send_message($message, $this->data_new_message);

        }

    }

    public function security (int $security): void {
        if (isset($this->chat->local_id) && $this->check_messages->access->is_admin) {
            if (!$this->check_messages->access->control) {
                
    
                $this->db->insert(
                    'control_chats',
                    '(NULL, :local_chat_id, :security, 0, 0, 0, 0, 0, 0, :added_usr_id, :date_add)',
                    [
                        "local_chat_id" => $this->chat->local_id, 
                        "security"      => $security, 
                        "added_usr_id"  => $this->from_id, 
                        "date_add"      => $this->date
                    ]
                );
            }else {
    
                $this->db->update(
                    'control_chats',
                    'security=:security',
                    'local_chat_id=:local_chat_id',
                    [
                        "security"      => $security, 
                        "local_chat_id" => $this->chat->local_id
                    ], 1
                );
    
            }
    
    
            $message = ($security) ? $this->messages->security_enabled : $this->messages->security_disabled;
        
        }elseif (!$this->check_messages->access->is_admin) {
        
            $message = $this->messages->manage_secure_can_admin;
        
        }else {
            
            $message = $this->messages->bot_not_admin;
            
        }
        
        $this->vk->send_message($message, $this->data_new_message);

    }

    public function show_status_security () {
        if (!$this->chat->security){
            $sim_spam_security = $this->error_sim;
            $sim_link_security = $this->error_sim;
            $sim_invite_security = $this->error_sim;
            $sim_invite_bots = $this->error_sim;
            $sim_nude_security = $this->error_sim;
            $sim_censor = $this->error_sim;
            $sim_repost = $this->error_sim;
        } else {
            $sim_spam_security = ($this->chat->security['0']['security']) ? $this->checkmark : $this->error_sim;
            $sim_link_security = ($this->chat->security['0']['links']) ? $this->checkmark : $this->error_sim;
            $sim_invite_security = ($this->chat->security['0']['invites']) ? $this->checkmark : $this->error_sim;
            $sim_invite_bots = ($this->chat->security['0']['bots']) ? $this->checkmark : $this->error_sim;
            $sim_nude_security = ($this->chat->security['0']['nude_security']) ? $this->checkmark : $this->error_sim;
            $sim_censor = ($this->chat->security['0']['censor']) ? $this->checkmark : $this->error_sim;
            $sim_repost = ($this->chat->security['0']['repost']) ? $this->checkmark : $this->error_sim;
        }

        $message = "-----Статус защиты-----" . $this->new_line 
                . "Основная спам-защита: " . $sim_spam_security . " " . $this->new_line 
                . "Защита от ссылок: " . $sim_link_security . " " . $this->new_line 
                . "Защита от ивайт-ссылок в другие беседы: " . $sim_invite_security . " " . $this->new_line 
                . "Защита от ботов: " . $sim_invite_bots . " " . $this->new_line 
                . "Защиты от контента для взрослых: " . $sim_nude_security . " " . $this->new_line 
                . "Защиты от мата: " . $sim_censor . " " . $this->new_line 
                . "Защиты от репостов: " . $sim_repost;

        $this->vk->send_message($message, $this->data_new_message);

    }

    public function show_not_active () {

        $unactive_users = 0;

        foreach ($this->chat->members->profiles as $member_info) {

            $is_kick = false;

            $get_last_activity = $this->db->select(
                    'last_activity', '*',
                    'user_id=:user_id AND chat_id=:chat_id',
                    [
                        "user_id" => $member_info->id, 
                        "chat_id" => $this->chat->local_id
                    ]
                );

            if (isset($get_last_activity)) {
                if ($this->date_last_actv > $get_last_activity['0']['date_last_acivity'])
                    $is_kick = true;

            }else {

                $is_kick = true;

            }

            if ($is_kick) {

                $unactive_users++;

            }

        }

        $message = $this->from_profile_reply . ", " . $this->declime_unactive_users($unactive_users);

        $this->vk->send_message($message, $this->data_new_message);

    }

    public function show_last_active () {

//        $users_last_activity = $this->db->select(
//            'last_activity', '*', 
//            'chat_id=:chat_id', 
//            ["chat_id" => $this->chat->local_id], 15, 0, 
//            'date_last_acivity DESC'
//        );
//
//        $message_text = "";
//
//        foreach ($users_last_activity as $user_last_activity) {
//            
//            $user_info = $this->get_info_user($user_last_activity['user_id']);
//            
//            if (isset($user_info)) {
//                
//                $time = $this->date - $user_last_activity['date_last_acivity'];
//
//                if ($time > 60)
//                    $time = $this->parse_timer($time);
//                else
//                    $time = "только что";
//
//                $message_text .= $user_info->first_name . " - " . $time . " " . $this->new_line;
//                
//            }
//            
//        }
//
//        $this->vk->send_message($message_text, $this->data_new_message);

    }

    public function remove_admin ($user_id) {
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);

        if (isset($user_id)) {
            
            $user_id = $this->search_user_in_chat($user_id);

            if ($user_id != 0) {
                
                $check_user_in_admin = $this->db->select(
                    'chat_admins', '*', 
                    'chat_id=:chat_id AND user_id=:user_id', 
                    [
                        "chat_id" => $this->chat->local_id, 
                        "user_id" => $user_id
                    ], 1
                )[0];
                
                if (isset($check_user_in_admin)) {
                    
                    $this->db->delete(
                        'chat_admins',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "chat_id" => $this->chat->local_id, 
                            "user_id" => $user_id
                        ], 1
                    );
                    
                    $message_text = $this->from_profile_reply . $this->messages->admin_removed;
                    
                }else {
                    
                    $message_text = $this->from_profile_reply . $this->messages->user_not_admin;
                    
                }
                
            }else {

                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;

            }
            
            $this->vk->send_message($message_text, $this->data_new_message);
            
        }

    }

    public function moneta_take ($count, $side, $from=0) {
        $moneta_side = ($side == 1) ? '&#9899;Орёл' : '&#9898;Решка';
        $res = '';
        if ($side == 'zero'){
            $to_id = $this->get_info_user($from);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
            $this->vk->send_message('&#128293;Дуэль между '.$this->from_profile_reply.' и '.$profile_reply_to. $this->new_line.$this->from_profile_reply.', отказался от дуэли!', $this->data_new_message);
            $this->vk->delete_message($this->data->object->peer_id, $this->data->object->conversation_message_id);
            return $res = 'Отказался';
        }
        if ($this->money >= $count){
            $rand = rand(1,2);
            $moneta_side_bot = ($rand == 1) ? '&#9899;Орёл' : '&#9898;Решка';
            if ($rand == $side){
                
            $to_id = $this->get_info_user($from);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
            
                $mess = '&#128293;Дуэль между '.$this->from_profile_reply.' и '.$profile_reply_to. $this->new_line
                    .'&#128171;'.$this->from_profile_reply.' выбирает '. $moneta_side . $this->new_line
                    .'Выпал '.$moneta_side_bot. $this->new_line
                    .'&#128180;'.$this->from_profile_reply.' забирает '.$count. ' Рё';
                $this->vk->send_message($mess, $this->data_new_message);
                $res = 'Вы выиграли '.$count. ' Рё';
                $this->db->update(
                    'users',
                    'money=money+:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $count, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $this->from_id
                    ], 1
                );
                $this->db->update(
                    'users',
                    'money=money-:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $count, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $from
                    ], 1
                );
            } else {
                $to_id = $this->get_info_user($from);
                $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
                
                
                $mess = '&#128293;Дуэль между '.$this->from_profile_reply.' и '.$profile_reply_to. $this->new_line
                    .'&#128171;'.$this->from_profile_reply.' выбирает '. $moneta_side . $this->new_line
                    .'Выпала '.$moneta_side_bot. $this->new_line
                    .'&#128180;'.$profile_reply_to.' забирает '.$count. ' Рё';
                $this->vk->send_message($mess, $this->data_new_message);
                $res = 'Вы проиграли '.$count. ' Рё';
                $this->db->update(
                    'users',
                    'money=money-:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $count, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $this->from_id
                    ], 1
                );
                $this->db->update(
                    'users',
                    'money=money+:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $count, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $from
                    ], 1
                );
            }
        } else {
            $res = 'Недостаточно Рё';
        }
        $this->vk->delete_message($this->data->object->peer_id, $this->data->object->conversation_message_id);
        return $res;
    }
    public function money_take ($mess, $user_id=0) {
        if (isset($mess)){
            $money_get = explode(' ', $mess);
            if ($user_id==0){
                $user_id = $money_get[0];
                if (isset($money_get[1])){
                    $mess = $money_get[1];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
                    die();
                }
            } else {
                if (isset($money_get[0])){
                    $mess = $money_get[0];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
                    die();
                }
            }
            
        }
        
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
        $user_id = $this->search_user_in_chat($user_id);
    
        if ($user_id<0){
            $this->vk->send_message($this->from_profile_reply .', нельзя забрать деньги у бота!', $this->data_new_message);
            die();
        }
        
        if ($mess > 0) {                
            $this->db->update(
                    'users',
                    'money=money-:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $mess, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $user_id
                    ], 1
            );
            $to_id = $this->get_info_user($user_id);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
            $this->vk->send_message($this->from_profile_reply .', забрал '.$mess. ' Рё у пользователя '. $profile_reply_to, $this->data_new_message);
        } else {
            $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
        }
        
    }
    public function money_get_user ($mess, $user_id=0) {
        if (isset($mess)){
            $money_get = explode(' ', $mess);
            if ($user_id==0){
                $user_id = $money_get[0];
                if (isset($money_get[1])){
                    $mess = $money_get[1];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
                    die();
                }
            } else {
                if (isset($money_get[0])){
                    $mess = $money_get[0];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
                    die();
                }
            }
            
        }
        
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
        $user_id = $this->search_user_in_chat($user_id);
    
        if ($user_id<0){
            $this->vk->send_message($this->from_profile_reply .', нельзя дать денег боту!', $this->data_new_message);
            die();
        }
        
        if ($mess > 0 && $this->money >= $mess) {                
            $this->db->update(
                    'users',
                    'money=money+:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $mess, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $user_id
                    ], 1
            );              
            $this->db->update(
                    'users',
                    'money=money-:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $mess, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $this->from_id
                    ], 1
            );
            $to_id = $this->get_info_user($user_id);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
            $this->vk->send_message($this->from_profile_reply .', дал '.$mess. ' Рё пользователю '. $profile_reply_to, $this->data_new_message);
        } else {
            $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
        }
        
    }
    public function money_get ($mess, $user_id=0) {
        if (isset($mess)){
            $money_get = explode(' ', $mess);
            if ($user_id==0){
                $user_id = $money_get[0];
                if (isset($money_get[1])){
                    $mess = $money_get[1];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
                    die();
                }
            } else {
                if (isset($money_get[0])){
                    $mess = $money_get[0];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
                    die();
                }
            }
            
        }
        
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
        $user_id = $this->search_user_in_chat($user_id);
    
        if ($user_id<0){
            $this->vk->send_message($this->from_profile_reply .', нельзя выдать деньги боту!', $this->data_new_message);
            die();
        }
        
        if ($mess > 0) {                
            $this->db->update(
                    'users',
                    'money=money+:user_money',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "user_money" => $mess, 
                        "chat_id" => $this->chat->local_id,
                        "user_id" => $user_id
                    ], 1
            );
            $to_id = $this->get_info_user($user_id);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
            $this->vk->send_message($this->from_profile_reply .', выдал '.$mess. ' Рё пользователю '. $profile_reply_to, $this->data_new_message);
        } else {
            $this->vk->send_message($this->from_profile_reply .', неверная сумма', $this->data_new_message);
        }
        
    }
    
    
    public function moneta ($mess, $user_id=0) {
        if (isset($mess)){
            $monetka = explode(' ', $mess);
            if ($user_id==0){
                $user_id = $monetka[0];
                if (isset($monetka[1])){
                    $mess = $monetka[1];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная ставка', $this->data_new_message);
                    die();
                }
            } else {
                if (isset($monetka[0])){
                    $mess = $monetka[0];
                } else {
                    $this->vk->send_message($this->from_profile_reply .', неверная ставка', $this->data_new_message);
                    die();
                }
            }
            
        }
        
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
        $user_id = $this->search_user_in_chat($user_id);
        
        if ($user_id == $this->from_id){
            $this->vk->send_message($this->from_profile_reply.', нельзя предложить дуэль себе', $this->data_new_message);
            die();
        }
        
        if ($user_id<0){
            $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;
            $this->vk->send_message($message_text, $this->data_new_message);
            die();
        }
        $attch = [];
        $keyb = [];
        if ($this->money > $mess){
            $photo = ['457239061','457239062','457239063','457239064'];
            $media_id = $photo[mt_rand(0,count($photo)-1)];
            $attch[] =  [
                'type'       => 'photo',
                'owner_id'   => '-222251441',
                'media_id'   => $media_id
            ];  
            
            
            $btns = [
                    'action'    => [
                        'type'      => 'callback',
                        'payload'   => ['type' => "moneta",
                                       'count' => $mess,
                                       'side' => 1,
                                       'who' => $user_id,
                                       'from' => $this->from_id],
                        'label'     => "&#9899;Орёл"
                    ],
                    'color'     => 'positive'               
            ];            
            $btn2 = [
                    'action'    => [
                        'type'      => 'callback',
                        'payload'   => ['type' => "moneta",
                                       'count' => $mess,
                                       'side' => 2,
                                       'who' => $user_id,
                                       'from' => $this->from_id],
                        'label'     => "&#9898;Решка"
                    ],
                    'color'     => 'positive'               
            ];        
            $cancel = [
                    'action'    => [
                        'type'      => 'callback',
                        'payload'   => ['type' => "moneta",
                                       'count' => $mess,
                                       'side' => 'zero',
                                       'who' => $user_id,
                                       'from' => $this->from_id],
                        'label'     => "&#9940;Отказаться"
                    ],
                    'color'     => 'negative'               
            ];
            $keyb = [
                'inline'    => true,
                'buttons'   => [(array($btns,$btn2)),(array($cancel))]
            ];
            
            $to_id = $this->get_info_user($user_id);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
            
            $message_text = '&#128293;'.$this->from_profile_reply.' бросил вызов '.$profile_reply_to. $this->new_line
                . '&#128180;Ставка - '.$mess.' Рё' . $this->new_line
                . '&#128171;Первый бросает '. $profile_reply_to;
        } else {
            $message_text = 'Недостаточно Рё';
        }
        
        $this->vk->send_message($message_text, $this->data_new_message, $attch, $keyb);
        
    }

    public function changerang ($user_id, $mess, $repl) {
        if ($user_id == 0){
            $rang_change = explode(' ', $mess);
            $user_id = $rang_change[0];
            $user_id = $this->screen_name_parse($user_id);
            $user_id = str_replace("id", "", $user_id);
            $user_id = str_replace("-", "", $user_id);
            if (isset($rang_change[1])){
                $mess = $rang_change[1];
            } else {
                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;
                $this->vk->send_message($message_text, $this->data_new_message);
                die();
            }
        } 
        if ($user_id<0){
            $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;
            $this->vk->send_message($message_text, $this->data_new_message);
            die();
        }
        
        if ($mess > 0 AND $mess < 7){
            $this->db->update(
                'users',
                'rang=:user_rang',
                'chat_id=:chat_id AND user_id=:user_id',
                [
                    "user_rang" => $mess, 
                    "chat_id" => $this->chat->local_id,
                    "user_id" => $user_id
                ], 1
            );
            $get_rang = $this->get_name_rang($mess);

            $to_id = $this->get_info_user($user_id);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
            $message_text = 'Ранг пользователя '.$profile_reply_to.' изменен на '. $mess . ' - '.$get_rang;
        } else {
            $message_text = 'Неверный ранг. Ранг должен быть от 1 до 6.';
        }
        

        
        
        $this->vk->send_message($message_text, $this->data_new_message);
        
    }

    public function who_invite ($user_id) {
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
        $user_id = $this->search_user_in_chat($user_id);
        
        if ($user_id == 0){
            $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;
            $this->vk->send_message($message_text, $this->data_new_message);
            die();
        }
        
        if ($user_id > 0){
            $who_id = $this->get_info_user($user_id, false, "gen");
            $profile_reply_who = "@id$who_id->id ($who_id->first_name_gen $who_id->last_name_gen)";
        } else {
            $who_id = $this->get_info_user($user_id);
            $profile_reply_who = "@club$who_id->id ($who_id->name)";
        }
        
        
            foreach ($this->chat->members->items as $member_info_who) {

                if ($member_info_who->member_id == $user_id) {

                    $who_invite = $member_info_who->invited_by;
                }
            }
        
        
        if ($who_invite > 0){
            $to_id = $this->get_info_user($who_invite);
            $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
        } else {
            $to_id = $this->get_info_user($who_invite);
            $profile_reply_to = "@club$to_id->id ($to_id->name)";
        }
        
        $this->vk->send_message('Пользователя '.$profile_reply_who.' добавил '.$profile_reply_to, $this->data_new_message);
        
    }
    
    
    public function add_admin ($user_id) {
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
        if (isset($user_id)) {
            
            $user_id = $this->search_user_in_chat($user_id);
            if ($user_id != 0) {
                
                $check_user_admin = $this->db->select(
                    'chat_admins', '*',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "chat_id" => $this->chat->local_id, 
                        "user_id" => $user_id
                    ], 1
                );
                
                if (empty($check_user_admin)) {

                    $this->db->insert(
                        'chat_admins',
                        '(NULL, :user_id, :chat_id, :added_id, :date)',
                        [
                            "user_id"   => $user_id,
                            "chat_id"   => $this->chat->local_id,
                            "added_id"  => $this->from_id,
                            "date"      => $this->date
                        ]
                    );
                    
                    $message_text = $this->from_profile_reply . $this->messages->added_admin;
                    
                }else {
                    
                    $message_text = $this->from_profile_reply . $this->messages->already_admin;
                    
                }
                
            }else {

                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;

            }
            
            $this->vk->send_message($message_text, $this->data_new_message);
            
        }

    }

    public function nick_remove ($user_id) {

        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);

        if (isset($user_id)) {
            
            $user_id = $this->search_user_in_chat($user_id);

            if ($user_id != 0) {
                
                $this->db->delete(
                    'users_nick',
                    'user_id=:user_id AND chat_id=:chat_id',
                    [
                        "user_id" => $user_id, 
                        "chat_id" => $this->chat->local_id
                    ], 1
                );
                $profile_reply = "@id" . $this->from_id . " (" . $user_info->first_name . " " . $user_info->last_name . ")";
                $message .= $profile_reply . ", " . $this->new_line;
                
                $message_text = $this->from_profile_reply . $this->messages->removed_nick;
                
            }else {

                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;

            }
            
            $this->vk->send_message($message_text, $this->data_new_message);
            
        }

    }

    public function warn_remove ($user_id) {

        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);

        if (isset($user_id)) {
            
            $user_id = $this->search_user_in_chat($user_id);

            if ($user_id != 0) {
                
                $this->db->delete(
                    'warn',
                    'user_id=:user_id AND chat_id=:chat_id',
                    [
                        "user_id" => $user_id, 
                        "chat_id" => $this->chat->local_id
                    ], 1
                );
                
                $to_id = $this->get_info_user($user_id, false, "gen");
                $profile_reply_to = "@id$to_id->id ($to_id->first_name_gen $to_id->last_name_gen)";

                $message_text = $this->from_profile_reply . ', снят варн с '. $profile_reply_to;
                
            }else {

                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;

            }
            
            $this->vk->send_message($message_text, $this->data_new_message);
            
        }

    }

    public function delmess ($id_mess, $mess_id) {
        $this->vk->delete_message($this->data->object->message->peer_id, $mess_id);
        $this->vk->delete_message($this->data->object->message->peer_id, $id_mess);
    }

    public function mute_remove ($user_id) {

        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);

        if (isset($user_id)) {
            
            $user_id = $this->search_user_in_chat($user_id);

            if ($user_id != 0) {
                
                $this->db->delete(
                    'mute',
                    'user_id=:user_id AND chat_id=:chat_id',
                    [
                        "user_id" => $user_id, 
                        "chat_id" => $this->chat->local_id
                    ], 1
                );
                $to_id = $this->get_info_user($user_id, false, "gen");
                $profile_reply_to = "@id$to_id->id ($to_id->first_name_gen $to_id->last_name_gen)";

                $message_text = $this->from_profile_reply . ', снят мут с '. $profile_reply_to;
                
            }else {

                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;

            }
            
            $this->vk->send_message($message_text, $this->data_new_message);
            
        }

    }

    public function nick_add ($user_id, $nick) {
        
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);

        if (isset($user_id)) {
            
            $user_id = $this->search_user_in_chat($user_id);

            if ($user_id != 0) {
                
                $check_nick = $this->db->select(
                    'users_nick', 'COUNT(*)',
                    'chat_id=:chat_id AND user_id=:user_id',
                    [
                        "chat_id" => $this->chat->local_id, 
                        "user_id" => $user_id
                    ], 1
                )[0][0];

                if ($check_nick) {

                    $this->db->update(
                        'users_nick',
                        'nick=:nick, date=:date',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "nick"    => $nick,
                            "chat_id" => $this->chat->local_id, 
                            "user_id" => $user_id,
                            "date"    => $this->date
                        ], 1
                    );

                }else {

                    $this->db->insert(
                        'users_nick',
                        '(NULL, :chat_id, :user_id, :nick, :date)',
                        [
                            "chat_id" => $this->chat->local_id,
                            "user_id" => $user_id, 
                            "nick"    => $nick,
                            "date"    => $this->date
                        ]
                    );

                }

                $message_text = $this->from_profile_reply . $this->messages->added_nick;
                
            }else {

                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;

            }
            
            $this->vk->send_message($message_text, $this->data_new_message);
            
        }

    }

    public function nick_list () {

        $nicks = $this->db->select(
            'users_nick', '*',
            'chat_id=:chat_id',
            [
                "chat_id" => $this->chat->local_id
            ], 0, 0, 'date DESC'
        );

        $message_text = $this->messages->show_nicks . $this->new_line;

        if ($nicks) {

            for ($i = 0; $i < count($nicks); $i++)
                $message_text .= ($i+1) . " " . $this->add_adm_in_status($nicks[$i]['user_id'], " ") . "- '" . $nicks[$i]['nick'] . "'";

        }else {

            $message_text .= $this->messages->empty;

        }

        $this->vk->send_message($message_text, $this->data_new_message);

    }

    public function show_admins () {

        $message = $this->from_profile_reply . $this->messages->list_admins . $this->new_line;

        foreach ($this->chat->admins->users as $admin)
            $message .= $this->add_adm_in_status($admin);

        $admins_db = $this->db->select(
            'chat_admins', '*', 
            'chat_id=:chat_id',
            ["chat_id" => $this->chat->local_id]
        );

        foreach ($admins_db as $admin)
            $message .= $this->add_adm_in_status($admin['user_id']);

        $this->vk->send_message($message, $this->data_new_message);

    }

    public function screen_name_parse (string $text): string {
            preg_match("/\[(.*?)\|/", $text, $matches);
            if (isset($matches[1])){
                $screenname = $matches[1];
                $screenname = str_replace("club", "", $screenname);
                $screenname = str_replace("public", "", $screenname);
            } else {
                $screenname = $text;
            }

        if (empty($screenname))
            $screenname = $text;
            

        return $screenname;
    }
    

    public function kick_by_screen_name ($user_id) {
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);

        if (isset($user_id)) {

            if (mb_stristr($user_id, "неактив") 
                    && mb_stripos($user_id, "неактив") === 0) {
                
                $date_check_start = $this->date - (86400 * 5);
                
                if ($this->date_added_chat > 0 && $date_check_start > $this->date_added_chat) {
                
                    $limited = 5;
                    $count_kicked = 0;

                    $count_by_user = explode(" ", $user_id, 2)[1];

                    if (isset($count_by_user)) {

                        if (intval($count_by_user) > 0)
                            $limited = $count_by_user;

                    }

                    if ($limited > 10)
                        $limited = 10;
                    
                    foreach ($this->chat->members->profiles as $member_info) {

                        if ($limited > 0) {
                            
                            $is_kick = false;
                            
                            $get_last_activity = $this->db->select(
                                'last_activity', '*',
                                'user_id=:user_id AND chat_id=:chat_id',
                                [
                                    "user_id" => $member_info->id, 
                                    "chat_id" => $this->chat->local_id
                                ], 1
                            )[0];

                            $is_kick = (isset($get_last_activity) && $this->date_last_actv > $get_last_activity['date_last_acivity']) || true;
                            
                            if ($is_kick) {
                                
                                $result = $this->kick_user($this->chat->local_id, $member_info->id, true, true);

                                if ($result->response == 1) {

                                    $count_kicked++;

                                    $limited--;
                                    
                                    $this->db->delete(
                                        'last_activity',
                                        'user_id=:user_id AND chat_id=:chat_id',
                                        [
                                            "user_id" => $member_info->id, 
                                            "chat_id" => $this->chat->local_id
                                        ], 1
                                    );

                                }
                                
                            }

                        }else {

                            break;

                        }

                    }

                    $message_text = $this->from_profile_reply . ", Было кикнуто: " . $this->declime_unactive_users($count_kicked);
                    $this->vk->send_message($message_text, $this->data_new_message);
                    
                }else {
                    
                    $message_text = $this->error_sim . " " . $this->from_profile_reply . ", Вы можете воспользоваться данной функцией через: " . $this->parse_timer(($this->date_added_chat - $date_check_start));
                    $this->vk->send_message($message_text, $this->data_new_message);
                    
                }
                
            }else {
                
                $id_usr_kick = $this->search_user_in_chat($user_id);

                if ($id_usr_kick != 0) {

                    if (!in_array($id_usr_kick, $this->chat->admins->users) 
                            && !in_array($id_usr_kick,  $this->chat->admins->groups)) {

                        if ($id_usr_kick > 0) {

                            $user_info = $this->get_info_user($id_usr_kick);

                            $profile_reply = "@id" . $id_usr_kick . " (" . $user_info->last_name . ")";
                            $message_text = $profile_reply . $this->messages->kick;
                            $this->vk->send_message($message_text, $this->data_new_message);

                        }

                        $this->kick_user($this->chat->local_id, $id_usr_kick, true);

                    }else {

                        $message_text = $this->from_profile_reply . $this->messages->cannot_kick_admin;
                        $this->vk->send_message($message_text, $this->data_new_message);
                        
                    }

                }else {

                    $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;
                    $this->vk->send_message($message_text, $this->data_new_message);
                    
                }
                
            }
                
        }

    }

    public function vote_poll (): void {

        if (isset($this->db->select(
            'kick_polls', '*',
            'chat_id=:chat_id AND date_create>:date_create AND reresolved=0',
            [
                "chat_id" => $this->chat->local_id, 
                "date_create" => ($this->date - 600)
            ], 1
        )[0])) {
        
            $check_current_polls = $this->db->select(
                'kick_polls', '*',
                'chat_id=:chat_id AND date_create>:date_create AND reresolved=0',
                [
                    "chat_id" => $this->chat->local_id, 
                    "date_create" => ($this->date - 600)
                ], 1
            )[0];


            $poll_id = $check_current_polls['id'];
            $current_votes = $check_current_polls['current_votes'];
            $needed_votes = $check_current_polls['needed_votes'];
            $id_usr_kick = $check_current_polls['kick_usr_id'];
            $kick = false;

            $check_vote = $this->db->select(
                'kick_poll_votes', 'COUNT(*)',
                'poll_id=:poll_id AND author_id=:author_id',
                [
                    "poll_id" => $poll_id, 
                    "author_id" => $this->from_id
                ], 1
            )[0][0];

            if ($check_vote == 0) {

                $this->db->insert(
                    'kick_poll_votes',
                    '(NULL, :poll_id, :author_id, :date)',
                    [
                        "poll_id" => $poll_id, 
                        "author_id" => $this->from_id, 
                        "date" => $this->date
                    ]  
                );

                $current_votes++;

                $this->db->update(
                    'kick_polls',
                    'current_votes=:current_votes',
                    'id=:id',
                    [
                        "current_votes" => ($current_votes), 
                        "id" => $poll_id
                    ], 1
                );

                if ($current_votes == $needed_votes) {

                    if ($id_usr_kick > 0) {
                        
                        $user_info = $this->get_info_user($id_usr_kick);

                        $profile_reply = "@id$id_usr_kick ($user_info->first_name $user_info->last_name)";
                        $message_text = $profile_reply . $this->messages->vote_kicked;
                        
                    }

                    $kick = true;
                    
                    $this->db->update(
                        'kick_polls',
                        'reresolved=1',
                        'id=:id',
                        ['id' => $poll_id], 1
                    );
                    
                    $this->db->delete(
                        'kick_poll_votes',
                        'poll_id=:poll_id',
                        ["poll_id" => $poll_id]
                    );

                }else {

                    $message_text = $this->from_profile_reply . $this->messages->success_voted
                     . $this->declime_vote(($needed_votes - $current_votes));

                }


            }else {

                $message_text = $this->from_profile_reply . $this->messages->already_voted;

            }

            $this->vk->send_message($message_text, $this->data_new_message);

            if ($kick)
                $this->kick_user($this->chat->local_id, $id_usr_kick, true);

        }

    }

    public function minus_rep (string $user_id): void {
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
                            
        if (isset($user_id)) {
            if ($user_id == $this->from_id){
                $this->vk->send_message($this->from_profile_reply. ', Нельзя понизить репутацию самому себе', $this->data_new_message);
                die();
            }
            
            if ($user_id <= 0){
                die();
            }
            
             $check_last_rep = $this->db->select(
                'users', 'COUNT(*)', 
                'user_id=:user_id AND last_rep>:last_rep AND chat_id=:chat_id', 
                [
                    "user_id"   => $this->from_id, 
                    "last_rep"  => ($this->date), 
                    "chat_id"   => $this->chat->local_id
                ], 1
            )[0][0];
            
            if ($check_last_rep != 0){
                $this->vk->send_message('Понижать репутацию можно раз в 12 часов', $this->data_new_message);
                die();
            }
            
            $get_profile = $this->db->select(
                'users', 'rep', 
                'chat_id=:chat_id AND user_id=:user_id', 
                [
                    "chat_id" => $this->chat->local_id, 
                    "user_id" => $user_id
                ]
            );
            if (isset($get_profile['0'])){           
                    $this->db->update(
                        'users',
                        'rep=:user_rep',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "user_rep" => $get_profile['0']['rep']-1, 
                            "chat_id" => $this->chat->local_id,
                            "user_id" => $user_id
                        ], 1
                    );
                    $this->db->update(
                        'users',
                        'last_rep=:last_rep',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "last_rep" => ($this->date + 43200), 
                            "chat_id" => $this->chat->local_id,
                            "user_id" => $this->from_id
                        ], 1
                    );
                    $to_id = $this->get_info_user($user_id);
                    $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
                    $this->vk->send_message($this->from_profile_reply. ' понизил репутацию '. $profile_reply_to, $this->data_new_message);
                
            }
        }
    }

    public function plus_rep (string $user_id): void {
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
                            
        if (isset($user_id)) {
            if ($user_id == $this->from_id){
                die();
            }
            
            if ($user_id <= 0){
                $this->vk->send_message($this->from_profile_reply. ', Нельзя повысить репутацию боту', $this->data_new_message);
                die();
            }
            
             $check_last_rep = $this->db->select(
                'users', 'COUNT(*)', 
                'user_id=:user_id AND last_rep>:last_rep AND chat_id=:chat_id', 
                [
                    "user_id"   => $this->from_id, 
                    "last_rep"  => ($this->date), 
                    "chat_id"   => $this->chat->local_id
                ], 1
            )[0][0];
            
            if ($check_last_rep != 0){
                $this->vk->send_message('Повышать репутацию можно раз в 12 часов', $this->data_new_message);
                die();
            }
            
            $get_profile = $this->db->select(
                'users', 'rep', 
                'chat_id=:chat_id AND user_id=:user_id', 
                [
                    "chat_id" => $this->chat->local_id, 
                    "user_id" => $user_id
                ]
            );
            if (isset($get_profile['0'])){           
                    $this->db->update(
                        'users',
                        'rep=:user_rep',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "user_rep" => $get_profile['0']['rep']+1, 
                            "chat_id" => $this->chat->local_id,
                            "user_id" => $user_id
                        ], 1
                    );
                    $this->db->update(
                        'users',
                        'last_rep=:last_rep',
                        'chat_id=:chat_id AND user_id=:user_id',
                        [
                            "last_rep" => ($this->date + 43200), 
                            "chat_id" => $this->chat->local_id,
                            "user_id" => $this->from_id
                        ], 1
                    );
                    $to_id = $this->get_info_user($user_id);
                    $profile_reply_to = "@id$to_id->id ($to_id->first_name $to_id->last_name)";
                    $this->vk->send_message($this->from_profile_reply. ' повысил репутацию '. $profile_reply_to, $this->data_new_message);
                
            }
        }
    }
    public function create_voting (string $user_id): void {
        if ($user_id == ""){
            die();
        }
        $user_id = $this->screen_name_parse($user_id);
        $user_id = str_replace("id", "", $user_id);
        $user_id = str_replace("-", "", $user_id);
                            
        if (isset($user_id)) {
            
            $date_create = 0;

            $id_usr_kick = $this->search_user_in_chat($user_id);

            if ($id_usr_kick) {
                
                if (!in_array($id_usr_kick, $this->chat->admins->users) 
                        && !in_array($id_usr_kick,  $this->chat->admins->groups)) {
                
                    $access_polls = 0;
                    
                    $check_current_polls = $this->db->select(
                        'kick_polls', 'COUNT(*)',
                        'chat_id=:chat_id AND date_create>:date_create AND reresolved=0',
                        [
                            "chat_id" => $this->chat->local_id, 
                            "date_create" => ($this->date - 600)
                        ], 1
                    )[0][0];
                    
                    if ($check_current_polls > 0) {
                        
                        $access_polls = 1;
                        
                    }else {
                        
                        if (isset($this->db->select(
                            'kick_polls', '*',
                            'chat_id=:chat_id AND date_create>:date_create', 
                            [
                                "chat_id" => $this->chat->local_id, 
                                "date_create" => ($this->date - 3600)
                            ], 
                            1, 0, 'id DESC'
                        )[0])){
                           $check_last_polls = $this->db->select(
                                'kick_polls', '*',
                                'chat_id=:chat_id AND date_create>:date_create', 
                                [
                                    "chat_id" => $this->chat->local_id, 
                                    "date_create" => ($this->date - 3600)
                                ], 
                                1, 0, 'id DESC'
                            )[0];
                            $date_create = $check_last_polls['date_create'];
                            $access_polls = 2;
                            
                        }
                        
                    }
                    
                    

                    if ($access_polls === 0) {

                        $conversation_get_active = $this->vk->call('messages.getConversationsById', 
                                                            [
                                                                'peer_ids'  => $this->peer_id,
                                                                'group_id'  => $this->config->group_id,
                                                                'long'      => 'ru'
                                                            ]);

                        if (isset($conversation_get_active->response)) {

                            $needed_votes = count($conversation_get_active->response->items[0]->chat_settings->active_ids);

                            $this->db->insert(
                                'kick_polls',
                                '(NULL, :chat_id, :author_id, :kick_usr_id, :needed_votes, 0, 0, :date_create)',
                                [
                                    "chat_id" => $this->chat->local_id,
                                    "author_id" => $this->from_id,
                                    "kick_usr_id" => $id_usr_kick, 
                                    "needed_votes" => $needed_votes,
                                    "date_create" => $this->date
                                ]
                            );

                            $msg_screen_name_kick_usr = ($id_usr_kick > 0) ? 
                                ("id" . $id_usr_kick) : ("club" . str_replace("-", "", $id_usr_kick));

                            $message_text = $this->checkmark . $this->from_profile_reply . ", Голосование за исключение @$msg_screen_name_kick_usr (пользователя) активировано!" . $this->new_line
                                                                        . "Чтобы проголосовать за исключение, отправьте '+' в чат." . $this->new_line
                                                                        . "Голосование будет активно 10 минут!" . $this->new_line 
                                                                        . "Голосов до исключения: " . $this->declime_vote($needed_votes);

                        }

                    }elseif ($access_polls === 1) {

                        $message_text = $this->error_sim . $this->from_profile_reply . ", В беседе еще активно другое голосование!";

                    }elseif ($access_polls === 2) {
                        
                        if ($date_create > 0) {
                            
                            $last_time = $date_create - ($this->date - 3600);
                            
                            $message_text = $this->error_sim . $this->from_profile_reply . ", Следующее голосование будет доступно через: " . $this->parse_timer($last_time);
                            
                        }
                        
                    }
                    
                }else {

                    $message_text = $this->from_profile_reply . $this->messages->cannot_kick_admin;

                }
                
            }else {
                
                $message_text = $this->from_profile_reply . $this->messages->cannot_find_user;
                
            }
            
            $this->vk->send_message($message_text, $this->data_new_message);

        }

    }
    
    private function get_name_rang ($user_rang): string{
        $namerang = '';
        switch($user_rang){
            case 1:
                $namerang = 'Ученик Академии&#10084;';
                break;
            case 2:
                $namerang = 'Генин&#128153;';
                break;
            case 3:
                $namerang = 'Чунин&#128154;';
                break;
            case 4:
                $namerang = 'Токубетсу Джонин&#128155;';
                break;
            case 5:
                $namerang = 'Джонин&#128156;';
                break;
            case 6:
                $namerang = 'Каге&#128420;';
                break;
        }
        return $namerang;
    }

    private function add_adm_in_status (int $user_id, string $end_line = ""): string {
    
        $end_line = ($end_line) ? $end_line : $this->new_line;
        $info_admin = $this->get_info_user($user_id);
        return " - @id" . $user_id . " (" . $info_admin->first_name . " " . $info_admin->last_name . ") " . $end_line;
        
    }

    public function check_security (): bool {
        if (isset($this->chat->security['0']['security']) && $this->chat->security['0']['security'])
            return true;

        $this->vk->send_message($this->messages->security_not_enabled, $this->data_new_message);
        return false;
    
    }

}
?>