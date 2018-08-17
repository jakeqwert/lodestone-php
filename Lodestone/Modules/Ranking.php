<?php
namespace Lodestone\Modules;

trait Ranking
{
    public function getFeast(int $season = 1, string $dcgroup = '', string $rank_type = 'all')
    {
        if ($season <= 0) {
            $season = 1;
        }
        $query = $this->queryBuilder([
            'dcgroup' => $dcgroup,
            'rank_type' => $rank_type,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_FEAST, strval($season)).$query;
        $this->type = 'feast';
        $this->typesettings['season'] = $season;
        return $this->parse();
    }
    
    public function getDeepDungeon(int $id = 1, string $dcgroup = '', string $solo_party = 'party', string $subtype = 'PLD')
    {
        if ($id == 1) {
            $id = '';
        }
        if ($subtype) {
            $solo_party = 'solo';
        }
        $query = $this->queryBuilder([
            'dcgroup' => $dcgroup,
            'solo_party' => $solo_party,
            'subtype' => $subtype,
        ]);
        $this->url = sprintf($this->language.Routes::LODESTONE_DEEP_DUNGEON, strval($id)).$query;
        if (empty($id)) {
            $id = 1;
        }
        if (empty($solo_party)) {
            $solo_party = 'party';
        }
        if (empty($subtype)) {
            $subtype = $this->getDeepDungeonClassId('PLD');
        }
        $this->type = 'deepdungeon';
        $this->typesettings['dungeon'] = $id;
        $this->typesettings['solo_party'] = $solo_party;
        $this->typesettings['class'] = $subtype;
        return $this->parse();
    }
    
    public function getFrontline(string $week_month = 'weekly', int $week = 0, string $dcgroup = '', string $worldname = '', int $pvp_rank = 0, int $match = 0, string $gcid = '', string $sort = 'win')
    {
        if (!in_array($week_month, ['weekly','monthly'])) {
            $week_month = 'weekly';
        }
        if (!in_array($sort, ['win', 'rate', 'match'])) {
            $sort = 'win';
        }
        if ($week_month == 'weekly') {
            if (!preg_match('/^[0-9]{4}(0[1-9]|[1-4][0-9]|5[0-3])$/', $week)) {
                $week = 0;
            }
        } else {
            if (!preg_match('/^[0-9]{4}(0[1-9]|1[0-2])$/', $week)) {
                $week = 0;
            }
        }
        $query = $this->queryBuilder([
            'sort' => $sort,
            'dcgroup' => $dcgroup,
            'worldname' => $worldname,
            'pvp_rank' => $pvp_rank,
            'match' => $match,
            'gcid' => $gcid,
        ]);
        $this->url = $this->language.Routes::LODESTONE_FRONTLINE.$week_month.'/'.$week.'/'.$query;
        $this->type = 'frontline';
        $this->typesettings['week'] = $week;
        $this->typesettings['week_month'] = $week_month;
        return $this->parse();
    }
}
?>