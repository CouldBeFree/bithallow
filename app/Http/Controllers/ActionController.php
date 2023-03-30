<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Action;
use App\Http\Requests\AddBet;
use App\Bet;
use App\User;
use App\Answer;
use Auth;
use Centrifugo;
use Illuminate\Support\Facades\Redis;
use App\Jobs\AddBets;
use App\QueueStatus;

class ActionController extends Controller
{

    private $centrifugo;

    public function __construct()
    {
        parent::__construct();
        # Получаем токен пользователя centrifugo
        $this->centrifugo = new Centrifugo();
    }

    /**
    * Страница события
    *
    * @param action id,
    * @return view
    **/

    public function index($id)
    {
        $cent_token = $this->centrifugo->generateToken(Auth::id());
        $data['info'] = Action::getAction([
            'id' => $id,
            'full_info' => 1
        ]) ?? false;
        if(!$data['info']) return abort(404);
        $data['bets'] = Bet::getBets([
            'action_id' => $id,
            'exodus' => $data['info']->exodus
        ]) ?? false;
        if (!Auth::guest()) {
            $data['profit'] = Bet::getProfit([
                'action_id' => $id,
                'user_id' => Auth::id(),
                'exodus' => $data['info']->exodus
            ]);
        }
        return view('pages.index.action', compact('data', 'cent_token', 'current_time'));
    }

    /**
    * Добавление ставки на событие
    *
    * @param request (post)
    * @return json
    **/

    public function addBet(AddBet $data)
    {
        /*
        * move - 1 ("за") 2 ("против")
        * team - 1 ("первая команда") 2 ("вторая команда") и т.п.
        */

        # Получаем событие
        $action = Action::getAction([
            'id' => $data['id'],
            'full_info' => 0
        ]);
        # Округляем сумму и коэф
        $data['sum'] = round($data['sum'], 2, PHP_ROUND_HALF_DOWN);
        $data['coef'] = ($action->exodus == 2 && $data['coef'] > 2) ? round($data['coef'], 4, PHP_ROUND_HALF_DOWN) : round($data['coef'], 2, PHP_ROUND_HALF_DOWN);
        # Проверка коэффициента
        $check_coef = Bet::checkCoef([
            'exodus' => $action->exodus,
            'coef' => $data['coef']
        ]);
        if ($check_coef !== true) return Answer::generate([
            'status' => 'error',
            'message' => ($action->exodus == 2) ? 'error_exodus.coef' : 'error.coef',
            'data' => ($action->exodus == 2) ? $check_coef : ''
        ]);

        # Считаем необходимую для ставки сумму
        $sum = ($data['move'] == 1) ? $data['sum'] : round($data['coef'] * $data['sum'] - $data['sum'], 2, PHP_ROUND_HALF_DOWN);

        # Проверка баланса (с учетом профита)
        $check = Bet::checkBalance([
        	'action_id' => $data['id'],
        	'user_id' => Auth::id(),
        	'user_balance' => Auth::user()->balance,
        	'sum' => $sum,
        	'move' => $data['move'],
            'team' => $data['team']
        ]);
        # Если баланса не хватает
    	if ($check === false) return Answer::generate([
            'status' => 'error',
            'message' => 'balance.false'
        ]);

        # Отправляем ставку в очередь
        dispatch((new AddBets([
            'action_id' => $data['id'],
            'user_id' => Auth::id(),
            'original' => 1,
            'coef' => $data['coef'],
            'sum' => $data['sum'],
            'leftover' => $data['sum'],
            'move' => $data['move'],
            'team' => $data['team'],
            'canceled_bets' => serialize($check)
        ]))->onQueue($data['id'].'_action'));

        # Отправляем успешный ответ
        return Answer::generate([
            'status' => 'success',
            'message' => 'load.bet'
        ]);
    }

    public function test($var, Request $data)
    {
        switch ($var) {
            case 'addaction':
                $teams = json_decode(json_encode([
                    'Team 1', 'Team 2'
                ]));
                Action::addAction([
                    'categories' => 1,
                    'exodus' => count($teams),
                    'teams' => serialize($teams),
                    'date' => '20.11.2020',
                    'time' => '10:20',
                    'name' => 'Vlad love or no love',
                    'status' => 0,
                    'sum' => 0
                ]);
                break;
            case 'testbets':
                for ($i=0; $i < 1001; $i++) { 
                    file_get_contents('https://bithallow.com/ajax/addbet?id=4&coef='.mt_rand(1.01,9).'.'.mt_rand(1,9).'&move='.mt_rand(1,2).'&team='.mt_rand(1,3).'&sum='.mt_rand(1,100));
                }
                break;
            case 'queue':
                $test = QueueStatus::queue([
                    'start' => $data['start'],
                    'action_id' => $data['action_id']
                ]);
                break;
        }
        
        return $test ?? '';
    }

    /**
    * Получение данных об игре (ajax)
    * 
    * @param action_id
    * @return json
    **/

    public static function actionInfo($id)
    {
        $data['action'] = Action::getAction([
            'id' => $id,
            'full_info' => 0
        ]) ?? false;
        # Если событие не существует
        if(!$data['action']) return Answer::generate([
            'status' => 'error',
            'message' => 'default.false'
        ]);
        $data['bets'] = Bet::getBets([
            'action_id' => $id,
            'exodus' => $data['action']->exodus
        ]) ?? false;
        return Answer::generate([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function betsInfo(Request $info)
    {
        $action = Action::getAction([
            'id' => $info['id'],
            'full_info' => 0
        ]) ?? false;
        # Если событие не существует
        if(!$action) return Answer::generate([
            'status' => 'error',
            'message' => 'default.false'
        ]);
        $data = Bet::openBets([
            'action_id' => $info['id'],
            'user_id' => Auth::id(),
            'exodus' => $action->exodus
        ]) ?? false;
        return Answer::generate([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function profitInfo(Request $info)
    {
        $action = Action::getAction([
            'id' => $info['id'],
            'full_info' => 0
        ]) ?? false;
        # Если событие не существует
        if(!$action) return Answer::generate([
            'status' => 'error',
            'message' => 'default.false'
        ]);
        $data = Bet::getProfit([
            'action_id' => $info['id'],
            'user_id' => Auth::id(),
            'exodus' => $action->exodus
        ]);
        return Answer::generate([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
