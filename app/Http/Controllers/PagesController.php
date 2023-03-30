<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Log;
use App\Action;
use App\Categorie;
use App\Bet;
use App\Ceff;
use Cache;

class PagesController extends Controller
{
	/* */
    public function index()
    {   
        
        # Проверяем данные на наличие в кеше
        if(Cache::has('data') and Cache::has('bets')){
            $data = Cache::get('data');
            $bets = Cache::get('bets');
        } else {
            # Категории котрые показываем на главной странице (id)
            $categories = [1, 2];
            # Количеество выводимых собитий по каждой категории
            $limit = 7;
            # Масив с категориями
            $data = [];
            # Масив со ставками
            $bets = [];
            # Получаем выбраные категории из базы
            foreach ($categories as $id) {
                # Добавляем в масив
                $data[] = [
                    'name_category' => Categorie::where('id', $id)->first()->name ?? false,
                    'info' => [
                        'actions' => Action::getActions([
                            'categories' => $id,
                            'limit' => $limit
                        ])
                    ]
                ];
            }
            # Получаем ставки по каждой категории
            foreach ($data as $item) {
                foreach ($item['info']['actions'] as $key => $value) {
                    $bets[$key] = Bet::getBets([
                        'action_id' => $value->id,
                        'exodus' =>  $value->exodus
                    ]) ?? false;
                }
            }
            # Добавляем данные в кеш
            Cache::add('data', $data, env('CACHE_INDEX_TIME'));
            Cache::add('bets', $bets, env('CACHE_INDEX_TIME'));
        }
        return view('pages.index.index', compact('data', 'bets'));
    }

    public function categories(int $id)
    {
        $data[] = [
            'name_category' => Categorie::where('id', $id)->first()->name ?? 'None',
            'info' => [
                'actions' => Action::getActions([
                    'categories' => $id,
                    'limit' => 10
                ])
            ]
        ];
        return view('pages.index.index', compact('data'));
    }

    public function register()
    {
    	return view('pages.index.register');
    }

    /* My account */
    public function balance()
    {
    	return view('pages.my.balance');
    }

    public function data()
    {
    	return view('pages.my.data');
    }

    public function historyBets()
    {
    	return view('pages.my.history_bets');
    }

    public function security()
    {
    	$data = Log::where([
    		'user_id' => Auth::id()
    	])->limit(10)->orderBy('id', 'desc')->get();
    	return view('pages.my.security', compact('data'));
    }
}
