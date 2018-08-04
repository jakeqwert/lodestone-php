<?php
namespace Lodestone\Modules;

trait Parsers
{    
    private function Achievement()
    {
        preg_match_all(
            Regex::ACHIEVEMENT_DETAILS,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['name'] = htmlspecialchars_decode($character['name']);
            if (empty($character['title'])) {
                $characters[$key]['title'] = false;
            } else {
                $characters[$key]['title'] = htmlspecialchars_decode($character['title']);
            }
            if (empty($character['item'])) {
                $characters[$key]['item'] = false;
            }
            if (!empty($character['itemname'])) {
                $characters[$key]['item'] = [
                    'id'=>$character['itemid'],
                    'name'=>htmlspecialchars_decode($character['itemname']),
                    'icon'=>$character['itemicon'],
                ];
                unset($characters[$key]['itemid'], $characters[$key]['itemname'], $characters[$key]['itemicon']);
            }
            if (empty($character['time'])) {
                $characters[$key]['time'] = NULL;
            }
        }
        $this->result = $characters[0];
        return $this;
    }
    
    private function Achievements()
    {
        preg_match_all(
            Regex::ACHIEVEMENTS_LIST,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['name'] = htmlspecialchars_decode($character['name']);
            if (!empty($character['title'])) {
                $characters[$key]['title'] = true;
            } else {
                $characters[$key]['title'] = false;
            }
            if (!empty($character['item'])) {
                $characters[$key]['item'] = true;
            } else {
                $characters[$key]['item'] = false;
            }
            if (empty($character['time'])) {
                $characters[$key]['time'] = NULL;
            }
        }
        $this->result = $characters;
        return $this;
    }
    
    private function Character()
    {
        #General
        preg_match_all(
            Regex::CHARACTER_GENERAL,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        $this->html = str_replace($characters[0][0], '', $this->html);
        #Race, grand/free company
        preg_match_all(
            Regex::CHARACTER_GROUPS,
            $this->html,
            $characters2,
            PREG_SET_ORDER
        );
        $this->html = str_replace($characters2[0][0], '', $this->html);
        #Profile text
        preg_match_all(
            Regex::CHARACTER_TEXT,
            $this->html,
            $characters3,
            PREG_SET_ORDER
        );
        $this->html = str_replace($characters3[0][0], '', $this->html);
        #Portrait
        preg_match_all(
            Regex::CHARACTER_PORTRAIT,
            $this->html,
            $characters4,
            PREG_SET_ORDER
        );
        $this->html = str_replace($characters4[0][0], '', $this->html);
        #Jobs
        preg_match_all(
            Regex::CHARACTER_JOBS,
            $this->html,
            $characters5,
            PREG_SET_ORDER
        );
        $this->html = str_replace($characters5[0][0], '', $this->html);
        #Mounts
        preg_match_all(
            Regex::CHARACTER_MOUNTS,
            $this->html,
            $mounts,
            PREG_SET_ORDER
        );
        if (!empty($mounts[0][0])) {
            $this->html = @str_replace($mounts[0][0], '', $this->html);
            $mounts = $mounts[0];
        }
        #Minions
        preg_match_all(
            Regex::CHARACTER_MINIONS,
            $this->html,
            $minions,
            PREG_SET_ORDER
        );
        if (!empty($minions[0][0])) {
            $this->html = @str_replace($minions[0][0], '', $this->html);
            $minions = $minions[0];
        }
        #Attributes
        preg_match_all(
            Regex::CHARACTER_ATTRIBUTES,
            $this->html,
            $characters8,
            PREG_SET_ORDER
        );
        $this->html = str_replace($characters8[0][0], '', $this->html);
        #HP/MP/TP
        preg_match_all(
            Regex::CHARACTER_HPMPTP,
            $this->html,
            $characters9,
            PREG_SET_ORDER
        );
        $this->html = str_replace($characters9[0][0], '', $this->html);
        $characters[0] = array_merge ($characters[0], $characters2[0], $characters3[0], $characters4[0], $characters5[0], $mounts, $minions, $characters8[0], $characters9[0]);
        foreach ($characters as $key=>$character) {
            #Remove non-named groups
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            #Decode html entities
            $characters[$key]['race'] = htmlspecialchars_decode($character['race']);
            $characters[$key]['clan'] = htmlspecialchars_decode($character['clan']);
            if (!empty($character['uppertitle'])) {
                $characters[$key]['title'] = htmlspecialchars_decode($character['uppertitle']);
            } elseif (!empty($character['undertitle'])) {
                $characters[$key]['title'] = htmlspecialchars_decode($character['uppertitle']);
            }
            #Gender to text
            $characters[$key]['gender'] = ($character['gender'] == '♂' ? 'male' : 'female');
            #Guardian
            $characters[$key]['guardian'] = [
                'name'=>htmlspecialchars_decode($character['guardian']),
                'icon'=>$character['guardianicon'],
            ];
            #City
            $characters[$key]['city'] = [
                'name'=>htmlspecialchars_decode($character['city']),
                'icon'=>$character['cityicon'],
            ];
            #Grand Company
            if (!empty($character['gcname'])) {
                $characters[$key]['grandCompany'] = [
                    'name'=>htmlspecialchars_decode($character['gcname']),
                    'rank'=>htmlspecialchars_decode($character['gcrank']),
                    'icon'=>$character['gcicon'],
                ];
            }
            #Free Company
            if (!empty($character['fcid'])) {
                $characters[$key]['freeCompany'] = [
                    'id'=>$character['fcid'],
                    'name'=>htmlspecialchars_decode($character['fcname']),
                ];
                $characters[$key]['freeCompany']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest1']);
                if (!empty($character['crest2'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest2']);
                }
                if (!empty($character['crest3'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest3']);
                }
            }
            #PvP Team
            if (!empty($character['pvpid'])) {
                $characters[$key]['pvp'] = [
                    'id'=>$character['pvpid'],
                    'name'=>htmlspecialchars_decode($character['pvpname']),
                ];
                $characters[$key]['pvp']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['pvpcrest1']);
                if (!empty($character['pvpcrest2'])) {
                    $characters[$key]['pvp']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['pvpcrest2']);
                }
                if (!empty($character['pvpcrest3'])) {
                    $characters[$key]['pvp']['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['pvpcrest3']);
                }
            }
            #Bio
            $character['bio'] = trim($character['bio']);
            if ($character['bio'] == '-') {
                $character['bio'] = '';
            }
            if (!empty($character['bio'])) {
                $characters[$key]['bio'] = strip_tags($character['bio'], '<br>');
            } else {
                unset($characters[$key]['bio']);
            }
            #Jobs
            for ($i = 1; $i <= 26; $i++) {
                $characters[$key]['jobs'][$character['jobname'.$i]] = [
                    'level'=>(is_numeric($character['joblvl'.$i]) ? (int)$character['joblvl'.$i] : 0),
                    'exp'=>(is_numeric($character['jobexpcur'.$i]) ? (int)$character['jobexpcur'.$i] : 0),
                    'expmax'=>(is_numeric($character['jobexpmax'.$i]) ? (int)$character['jobexpmax'.$i] : 0),
                    'icon'=>$character['jobicon'.$i],
                ];
                unset($characters[$key]['jobname'.$i], $characters[$key]['joblvl'.$i], $characters[$key]['jobexpcur'.$i], $characters[$key]['jobexpmax'.$i], $characters[$key]['jobicon'.$i]);
            }
            #Attributes
            for ($i = 1; $i <= 20; $i++) {
                $characters[$key]['attributes'][$character['attrname'.$i]] = $character['attrvalue'.$i];
                unset($characters[$key]['attrname'.$i], $characters[$key]['attrvalue'.$i]);
            }
            #Mounts
            unset($characters[$key]['mounts']);
            if (!empty($character['mounts'])) {
                preg_match_all(
                    Regex::COLLECTIBLE,
                    $character['mounts'],
                    $mounts,
                    PREG_SET_ORDER
                );
                $characters[$key]['mounts'] = [];
                foreach ($mounts as $mount) {
                    $characters[$key]['mounts'][$mount[1]] = $mount[2];
                }
            }
            #Minions
            unset($characters[$key]['minions']);
            if (!empty($character['minions'])) {
                preg_match_all(
                    Regex::COLLECTIBLE,
                    $character['minions'],
                    $minions,
                    PREG_SET_ORDER
                );
                $characters[$key]['minions'] = [];
                foreach ($minions as $minion) {
                    $characters[$key]['minions'][$minion[1]] = $minion[2];
                }
            }
            unset($characters[$key]['crest1'], $characters[$key]['crest2'], $characters[$key]['crest3'], $characters[$key]['cityicon'], $characters[$key]['guardianicon'], $characters[$key]['gcname'], $characters[$key]['gcrank'], $characters[$key]['gcicon'], $characters[$key]['fcid'], $characters[$key]['fcname'], $characters[$key]['uppertitle'], $characters[$key]['undertitle'], $characters[$key]['pvpid'], $characters[$key]['pvpname'], $characters[$key]['pvpcrest1'], $characters[$key]['pvpcrest2'], $characters[$key]['pvpcrest3']);
        }
        #Items
        preg_match_all(
            Regex::CHARACTER_GEAR,
            $this->html,
            $items,
            PREG_SET_ORDER
        );
        #Remove duplicates
        $half = count($items);
        for ($i = count($items)/2; $i <= $half; $i++) {
            unset($items[$i]);
        }
        #Remove non-named groups
        foreach ($items as $key=>$item) {
            foreach ($item as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($items[$key][$key2]);
                }
            }
            $items[$key]['armoireable'] = $this->imageToBool($item['armoireable']);
            if (empty($item['hq'])) {
                $items[$key]['hq'] = false;
            } else {
                $items[$key]['hq'] = true;
            }
            if (empty($item['unique'])) {
                $items[$key]['unique'] = false;
            } else {
                $items[$key]['unique'] = true;
            }
            #Requirements
            $items[$key]['requirements'] = [
                'level'=>$item['level'],
                'classes'=>explode(' ', $item['classes']),
            ];
            #Attributes
            for ($i = 1; $i <= 15; $i++) {
                if (!empty($item['attrname'.$i])) {
                    $items[$key]['attributes'][htmlspecialchars_decode($item['attrname'.$i])] = $item['attrvalue'.$i];
                    unset($items[$key]['attrname'.$i], $items[$key]['attrvalue'.$i]);
                }
            }
            #Materia
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($item['materianame'.$i])) {
                    $items[$key]['materia'][] = [
                        'name'=>htmlspecialchars_decode($item['materianame'.$i]),
                        'attribute'=>$item['materiaattr'.$i],
                        'bonus'=>$item['materiaval'.$i],
                    ];
                    unset($items[$key]['materianame'.$i], $items[$key]['materiaattr'.$i], $items[$key]['materiaval'.$i]);
                }
            }
            #Crafting
            if (!empty($item['repair'])) {
                $items[$key]['crafting']['class'] = $item['repair'];
                $items[$key]['crafting']['materials'] = $item['materials'];
                if (empty($item['desynthesizable'])) {
                    $items[$key]['crafting']['desynth'] = false;
                } else {
                    $items[$key]['crafting']['desynth'] = $item['desynthesizable'];
                }
                if (empty($item['melding'])) {
                    $items[$key]['crafting']['melding'] = false;
                } else {
                    $items[$key]['crafting']['melding'] = $item['melding'];
                    if (empty($item['advancedmelding'])) {
                        $items[$key]['crafting']['advancedmelding'] = true;
                    } else {
                        $items[$key]['crafting']['advancedmelding'] = false;
                    }
                }
                $items[$key]['crafting']['convertible'] = $this->imageToBool($item['convertible']);
            }
            #Trading
            if (empty($item['price'])) {
                $items[$key]['trading']['price'] = NULL;
            } else {
                $items[$key]['trading']['price'] = $item['price'];
            }
            if (empty($item['unsellable'])) {
                $items[$key]['trading']['sellable'] = true;
            } else {
                $items[$key]['trading']['sellable'] = false;
            }
            if (empty($item['marketprohibited'])) {
                $items[$key]['trading']['marketable'] = true;
            } else {
                $items[$key]['trading']['marketable'] = false;
            }
            if (empty($item['untradeable'])) {
                $items[$key]['trading']['tradeable'] = true;
            } else {
                $items[$key]['trading']['tradeable'] = false;
            }
            #Customization
            $items[$key]['customization'] = [
                'crestable'=>$this->imageToBool($item['crestable']),
                'glamourable'=>$this->imageToBool($item['glamourable']),
                'projectable'=>$this->imageToBool($item['projectable']),
                'dyeable'=>$this->imageToBool($item['dyeable']),
            ];
            #Glamour
            if (!empty($item['glamourname'])) {
                $items[$key]['customization']['glamour'] = [
                    'id'=>$item['glamourid'],
                    'name'=>htmlspecialchars_decode($item['glamourname']),
                    'icon'=>$item['glamouricon'],
                ];
            }
            unset($items[$key]['level'], $items[$key]['classes'], $items[$key]['price'], $items[$key]['unsellable'], $items[$key]['marketprohibited'], $items[$key]['repair'], $items[$key]['materials'], $items[$key]['desynthesizable'], $items[$key]['melding'], $items[$key]['advancedmelding'], $items[$key]['convertible'], $items[$key]['glamourname'], $items[$key]['glamourid'], $items[$key]['glamouricon'], $items[$key]['crestable'], $items[$key]['glamourable'], $items[$key]['projectable'], $items[$key]['dyeable'], $items[$key]['untradeable']);
            $characters[0]['gear'][] = $items[$key];
        }
        $this->result = $characters[0];
        return $this;
    }
    
    private function FreeCompany()
    {
        preg_match_all(
            Regex::FREECOMPANY,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest1']);
            if (!empty($character['crest2'])) {
                $characters[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest2']);
            }
            if (!empty($character['crest3'])) {
                $characters[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $character['crest3']);
            }
            //ranking checks for --
            if ($character['weekly_rank'] == '--') {
                unset($characters[$key]['weekly_rank']);
            }
            if ($character['monthly_rank'] == '--') {
                unset($characters[$key]['monthly_rank']);
            }
            #Estates
            if (!empty($character['estate_name'])) {
                $characters[$key]['estate']['name'] = $character['estate_name'];
            }
            if (!empty($character['estate_address'])) {
                $characters[$key]['estate']['address'] = $character['estate_address'];
            }
            if (!empty($character['estate_greeting']) && !in_array($character['estate_greeting'], ['No greeting available.', 'グリーティングメッセージが設定されていません。', 'Il n\'y a aucun message d\'accueil.', 'Keine Begrüßung vorhanden.'])) {
                $characters[$key]['estate']['greeting'] = $character['estate_greeting'];
            }
            #Grand companies reputation
            $characters[$key]['reputation'] = [
                $character['gcname1']=>$character['gcrepu1'],
                $character['gcname2']=>$character['gcrepu2'],
                $character['gcname3']=>$character['gcrepu3'],
            ];
            #Focus
            if (!empty($character['focusname1'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname1'],
                    'enabled'=>($character['focusoff1'] ? 0 : 1),
                    'icon'=>$character['focusicon1'],
                ];
            }
            if (!empty($character['focusname2'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname2'],
                    'enabled'=>($character['focusoff2'] ? 0 : 1),
                    'icon'=>$character['focusicon2'],
                ];
            }
            if (!empty($character['focusname3'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname3'],
                    'enabled'=>($character['focusoff3'] ? 0 : 1),
                    'icon'=>$character['focusicon3'],
                ];
            }
            if (!empty($character['focusname4'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname4'],
                    'enabled'=>($character['focusoff4'] ? 0 : 1),
                    'icon'=>$character['focusicon4'],
                ];
            }
            if (!empty($character['focusname5'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname5'],
                    'enabled'=>($character['focusoff5'] ? 0 : 1),
                    'icon'=>$character['focusicon5'],
                ];
            }
            if (!empty($character['focusname6'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname6'],
                    'enabled'=>($character['focusoff6'] ? 0 : 1),
                    'icon'=>$character['focusicon6'],
                ];
            }
            if (!empty($character['focusname7'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname7'],
                    'enabled'=>($character['focusoff7'] ? 0 : 1),
                    'icon'=>$character['focusicon7'],
                ];
            }
            if (!empty($character['focusname8'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname8'],
                    'enabled'=>($character['focusoff8'] ? 0 : 1),
                    'icon'=>$character['focusicon8'],
                ];
            }
            if (!empty($character['focusname9'])) {
                $characters[$key]['focus'][] = [
                    'name'=>$character['focusname9'],
                    'enabled'=>($character['focusoff9'] ? 0 : 1),
                    'icon'=>$character['focusicon9'],
                ];
            }
            #Seeking
            if (!empty($character['seekingname1'])) {
                $characters[$key]['seeking'][] = [
                    'name'=>$character['seekingname1'],
                    'enabled'=>($character['seekingoff1'] ? 0 : 1),
                    'icon'=>$character['seekingicon1'],
                ];
            }
            if (!empty($character['seekingname2'])) {
                $characters[$key]['seeking'][] = [
                    'name'=>$character['seekingname2'],
                    'enabled'=>($character['seekingoff2'] ? 0 : 1),
                    'icon'=>$character['seekingicon2'],
                ];
            }
            if (!empty($character['seekingname3'])) {
                $characters[$key]['seeking'][] = [
                    'name'=>$character['seekingname3'],
                    'enabled'=>($character['seekingoff3'] ? 0 : 1),
                    'icon'=>$character['seekingicon3'],
                ];
            }
            if (!empty($character['seekingname4'])) {
                $characters[$key]['seeking'][] = [
                    'name'=>$character['seekingname4'],
                    'enabled'=>($character['seekingoff4'] ? 0 : 1),
                    'icon'=>$character['seekingicon4'],
                ];
            }
            if (!empty($character['seekingname5'])) {
                $characters[$key]['seeking'][] = [
                    'name'=>$character['seekingname5'],
                    'enabled'=>($character['seekingoff5'] ? 0 : 1),
                    'icon'=>$character['seekingicon5'],
                ];
            }
            #Trim slogan
            $characters[$key]['slogan'] = trim($character['slogan']);
            unset($characters[$key]['crest1'], $characters[$key]['crest2'], $characters[$key]['crest3'], $characters[$key]['focusname1'], $characters[$key]['focusoff1'], $characters[$key]['focusicon1'], $characters[$key]['focusname2'], $characters[$key]['focusoff2'], $characters[$key]['focusicon2'], $characters[$key]['focusname3'], $characters[$key]['focusoff3'], $characters[$key]['focusicon3'], $characters[$key]['focusname4'], $characters[$key]['focusoff4'], $characters[$key]['focusicon4'], $characters[$key]['focusname5'], $characters[$key]['focusoff5'], $characters[$key]['focusicon5'], $characters[$key]['focusname6'], $characters[$key]['focusoff6'], $characters[$key]['focusicon6'], $characters[$key]['focusname7'], $characters[$key]['focusoff7'], $characters[$key]['focusicon7'], $characters[$key]['focusname8'], $characters[$key]['focusoff8'], $characters[$key]['focusicon8'], $characters[$key]['focusname9'], $characters[$key]['focusoff9'], $characters[$key]['focusicon9'], $characters[$key]['seekingname1'], $characters[$key]['seekingoff1'], $characters[$key]['seekingicon1'], $characters[$key]['seekingname2'], $characters[$key]['seekingoff2'], $characters[$key]['seekingicon2'], $characters[$key]['seekingname3'], $characters[$key]['seekingoff3'], $characters[$key]['seekingicon3'], $characters[$key]['seekingname4'], $characters[$key]['seekingoff4'], $characters[$key]['seekingicon4'], $characters[$key]['seekingname5'], $characters[$key]['seekingoff5'], $characters[$key]['seekingicon5'],
            $characters[$key]['gcname1'], $characters[$key]['gcrepu1'],
            $characters[$key]['gcname2'], $characters[$key]['gcrepu2'],
            $characters[$key]['gcname3'], $characters[$key]['gcrepu3'], $characters[$key]['estate_greeting'],  $characters[$key]['estate_address'],  $characters[$key]['estate_name']);
        }
        $this->result = $characters[0];
        return $this;
    }
    
    private function DeepDungeon()
    {
        preg_match_all(
            Regex::DEEPDUNGEON,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            $characters[$key]['job'] = [
                'name'=>$character['job'],
                'icon'=>$character['jobicon'],
                'form'=>$character['jobform'],
            ];
            unset($characters[$key]['jobicon'], $characters[$key]['jobform']);
        }
        $this->result = $characters;
        return $this;
    }
    
    private function Feast()
    {
        preg_match_all(
            Regex::FEAST,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
        }
        $this->result = $characters;
        return $this;
    }
    
    private function Worlds()
    {
        preg_match_all(
            Regex::WORLDS,
            $this->html,
            $worlds,
            PREG_SET_ORDER
        );
        foreach ($worlds as $key=>$world) {
            foreach ($world as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($worlds[$key][$key2]);
                }
            }
        }
        $this->result = $worlds;
        return $this;
    }
    
    private function Notices()
    {
        #required to skip "special" notices
        preg_match_all(
            Regex::NOTICES,
            $this->html,
            $notices,
            PREG_SET_ORDER
        );
        preg_match_all(
            Regex::NOTICES2,
            $notices[0][0],
            $notices,
            PREG_SET_ORDER
        );
        foreach ($notices as $key=>$notice) {
            foreach ($notice as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($notices[$key][$key2]);
                }
            }
            $notices[$key]['url'] = $this->language.Routes::LODESTONE_URL_BASE.$notice['url'];
        }
        $this->result['notices'] = $notices;
        unset($this->result['total']);
        return $this;
    }
    
    private function News()
    {
        preg_match_all(
            Regex::NEWS,
            $this->html,
            $news,
            PREG_SET_ORDER
        );
        foreach ($news as $key=>$new) {
            foreach ($new as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($news[$key][$key2]);
                }
            }
            $news[$key]['url'] = $this->language.Routes::LODESTONE_URL_BASE.$new['url'];
        }
        if ($this->type == 'Topics') {
            unset($this->result['total']);
            $this->result['topics'] = $news;
        } else {
            $this->result = $news;
        }
        return $this;
    }
    
    private function Banners()
    {
        preg_match(Regex::BANNERS, $this->html, $banners);
        preg_match_all(
            Regex::BANNERS2,
            $banners[0],
            $banners,
            PREG_SET_ORDER
        );
        foreach ($banners as $key=>$banner) {
            foreach ($banner as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($banners[$key][$key2]);
                }
            }
        }
        $this->result = $banners;
        return $this;
    }
    
    private function CharacterList()
    {
        preg_match_all(
            Regex::CHARACTERLIST,
            $this->html,
            $characters,
            PREG_SET_ORDER
        );
        foreach ($characters as $key=>$character) {
            foreach ($character as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($characters[$key][$key2]);
                }
            }
            if (!empty($character['gcname'])) {
                $characters[$key]['grandCompany'] = [
                    'name'=>$character['gcname'],
                    'rank'=>$character['gcrank'],
                    'icon'=>$character['gcrankicon'],
                ];
            }
            if (!empty($character['fcid'])) {
                $characters[$key]['freeCompany'] = [
                    'id'=>$character['fcid'],
                    'name'=>$character['fcname'],
                    'crest'=>[],
                ];
                $characters[$key]['freeCompany']['crest'][] = str_replace('40x40', '128x128', $character['fccrestimg1']);
                if (!empty($character['fccrestimg2'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace('40x40', '128x128', $character['fccrestimg2']);
                }
                if (!empty($character['fccrestimg3'])) {
                    $characters[$key]['freeCompany']['crest'][] = str_replace('40x40', '128x128', $character['fccrestimg3']);
                }
            }
            if (!empty($character['lsrank'])) {
                $characters[$key]['rank'] = $character['lsrank'];
                $characters[$key]['rankicon'] = $character['lsrankicon'];
                if (empty($this->result['server'])) {
                    $this->result['server'] = $character['server'];
                }
                unset($characters[$key]['server']);
            }
            unset($characters[$key]['gcname'], $characters[$key]['gcrank'], $characters[$key]['gcrankicon'], $characters[$key]['fcid'], $characters[$key]['fcname'], $characters[$key]['fccrestimg1'], $characters[$key]['fccrestimg2'], $characters[$key]['fccrestimg3'], $characters[$key]['lsrank'], $characters[$key]['lsrankicon']);
        }
        $this->result['characters'] = $characters;
        return $this;
    }
    
    private function FreeCompaniesList()
    {
        preg_match_all(
            Regex::FREECOMPANYLIST,
            $this->html,
            $freecompanies,
            PREG_SET_ORDER
        );
        foreach ($freecompanies as $key=>$freecompany) {
            foreach ($freecompany as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($freecompanies[$key][$key2]);
                }
            }
            $freecompanies[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $freecompany['fccrestimg1']);
            if (!empty($freecompany['fccrestimg2'])) {
                $freecompanies[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $freecompany['fccrestimg2']);
            }
            if (!empty($freecompany['fccrestimg3'])) {
                $freecompanies[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $freecompany['fccrestimg3']);
            }
            unset($freecompanies[$key]['fccrestimg1'], $freecompanies[$key]['fccrestimg2'], $freecompanies[$key]['fccrestimg3']);
        }
        $this->result['freeCompanies'] = $freecompanies;
        return $this;
    }
    
    private function LinkshellsList()
    {
        preg_match_all(
            Regex::LINKSHELLLIST,
            $this->html,
            $linkshells,
            PREG_SET_ORDER
        );
        foreach ($linkshells as $key=>$linkshell) {
            foreach ($linkshell as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($linkshells[$key][$key2]);
                }
            }
        }
        $this->result['linkshells'] = $linkshells;
        return $this;
    }
    
    private function PvPTeamsList()
    {
        preg_match_all(
            Regex::PVPTEAMLIST,
            $this->html,
            $pvpteams,
            PREG_SET_ORDER
        );
        foreach ($pvpteams as $key=>$pvpteam) {
            foreach ($pvpteam as $key2=>$details) {
                if (is_numeric($key2) || empty($details)) {
                    unset($pvpteams[$key][$key2]);
                }
            }
            $pvpteams[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pvpteam['crest1']);
            if (!empty($pvpteam['crest2'])) {
                $pvpteams[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pvpteam['crest2']);
            }
            if (!empty($pvpteam['crest3'])) {
                $pvpteams[$key]['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pvpteam['crest3']);
            }
            unset($pvpteams[$key]['crest1'], $pvpteams[$key]['crest2'], $pvpteams[$key]['crest3']);
        }
        $this->result['PvPTeams'] = $pvpteams;
        return $this;
    }
    
    private function pageCount()
    {
        preg_match_all(
            Regex::PAGECOUNT,
            $this->html,
            $pages,
            PREG_SET_ORDER
        );
        if (!empty($pages[0]['linkshellname'])) {
            $this->result['name'] = $pages[0]['linkshellname'];
        }
        if (!empty($pages[0]['pageCurrent'])) {
            $this->result['pageCurrent'] = $pages[0]['pageCurrent'];
        }
        if (!empty($pages[0]['pageTotal'])) {
            $this->result['pageTotal'] = $pages[0]['pageTotal'];
        }
        if (!empty($pages[0]['total'])) {
            $this->result['total'] = $pages[0]['total'];
        }
        if (!empty($pages[0]['pvpname'])) {
            $this->result['name'] = $pages[0]['pvpname'];
            if (!empty($pages[0]['server'])) {
                $this->result['dataCenter'] = $pages[0]['server'];
            }
            if (!empty($pages[0]['formed'])) {
                $this->result['formed'] = $pages[0]['formed'];
            }
            $this->result['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pages[0]['pvpcrest1']);
            if (!empty($pages[0]['pvpcrest2'])) {
                $this->result['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pages[0]['pvpcrest2']);
            }
            if (!empty($pages[0]['pvpcrest3'])) {
                $this->result['crest'][] = str_replace(['40x40', '64x64'], '128x128', $pages[0]['pvpcrest3']);
            }
        }
        return $this;
    }
}
?>