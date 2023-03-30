<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{

    protected $fillable = ['categories', 'exodus', 'status', 'name', 'teams', 'sum', 'date', 'time'];

    /**
    * Добавление события
    *
    * @param array (category id, exodus, status, name, teams (array), sum, date (text), time (text))
    * @return object
    **/

    public static function addAction($data)
    {
        return parent::create($data);
    }

    /**
    * Получение событий по категориям
    *
    * @param array (category id)
    * @return object
    **/

    public static function getActions($data)
    {
        $info = parent::where([
            'status' => 0,
            'categories' => $data['categories']
        ])
        ->limit($data['limit'])
        ->get() ?? false;

        return $info;
    }

    /**
    * Получение события
    *
    * @param array (action_id, full_info - 0 (partially) - 1 (full))
    * @return object
    **/

    public static function getAction($data)
    {
        switch ($data['full_info']) {
            case 0:
                $info = parent::where('id', $data['id'])
                ->select('id', 'exodus', 'sum')
                ->first() ?? false;
                break;
            case 1:
                $info = parent::where('id', $data['id'])
                ->first() ?? false;
                break;
        }

        return $info;
    }

    /**
    * Обновление информации об игре
    *
    * @param array (id, sum)
    * @return num
    **/

    public static function updateAction($data)
    {
        $action = parent::where('id', $data['id'])
        ->select('id', 'sum')
        ->first();
        $action->sum += $data['sum'];
        $action->save();

        return true;
    }

    /**
    * Получение массива всех команд в событии
    *
    * @param array (id)
    * @return array
    **/

    public static function getTeams($data)
    {
        $item = parent::where([
            'id' => $data['id'],
            'status' => 0
        ])
        ->select('teams')
        ->first();

        return unserialize($item->teams);
    }
}
