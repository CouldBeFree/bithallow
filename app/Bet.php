<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Action;

class Bet extends Model
{
    protected $fillable = ['action_id', 'user_id', 'original', 'coef', 'sum', 'leftover', 'move', 'team', 'canceled_bets', 'related_bet'];

    const COEF_TABLE = [
    	"1.01" => 101,
		"1.02" => 51,
		"1.03" => 34.3333,
		"1.04" => 26,
		"1.05" => 21,
		"1.06" => 17.6666,
		"1.07" => 15.2857,
		"1.08" => 13.5,
		"1.09" => 12.1111,
		"1.1" => 11,
		"1.11" => 10,
		"1.12" => 9.3333,
		"1.13" => 8.6923,
		"1.14" => 8.1429,
		"1.15" => 7.6666,
		"1.16" => 7.25,
		"1.17" => 6.8824,
		"1.18" => 6.5555,
		"1.19" => 6.2632,
		"1.2" => 6,
		"1.21" => 5.7619,
		"1.22" => 5.5454,
		"1.23" => 5.3478,
		"1.24" => 5.1666,
		"1.25" => 5,
		"1.26" => 4.8462,
		"1.27" => 4.7037,
		"1.28" => 4.5714,
		"1.29" => 4.4483,
		"1.3" => 4.3333,
		"1.31" => 4.2258,
		"1.32" => 4.125,
		"1.33" => 4.0303,
		"1.34" => 3.9412,
		"1.35" => 3.8571,
		"1.36" => 3.7777,
		"1.37" => 3.7027,
		"1.38" => 3.6316,
		"1.39" => 3.5641,
		"1.4" => 3.5,
		"1.41" => 3.4390,
		"1.42" => 3.3809,
		"1.43" => 3.3256,
		"1.44" => 3.2727,
		"1.45" => 3.2222,
		"1.46" => 3.1739,
		"1.47" => 3.1277,
		"1.48" => 3.0833,
		"1.49" => 3.0408,
		"1.5" => 3,
		"1.51" => 2.9608,
		"1.52" => 2.9231,
		"1.53" => 2.8868,
		"1.54" => 2.8519,
		"1.55" => 2.8182,
		"1.56" => 2.7857,
		"1.57" => 2.7544,
		"1.58" => 2.7241,
		"1.59" => 2.6949,
		"1.6" => 2.6666,
		"1.61" => 2.6393,
		"1.62" => 2.6129,
		"1.63" => 2.5873,
		"1.64" => 2.5625,
		"1.65" => 2.5385,
		"1.66" => 2.5152,
		"1.67" => 2.4925,
		"1.68" => 2.4706,
		"1.69" => 2.4493,
		"1.7" => 2.4286,
		"1.71" => 2.4085,
		"1.72" => 2.3889,
		"1.73" => 2.3699,
		"1.74" => 2.3514,
		"1.75" => 2.3333,
		"1.76" => 2.3158,
		"1.77" => 2.2987,
		"1.78" => 2.2821,
		"1.79" => 2.2658,
		"1.8" => 2.25,
		"1.81" => 2.2346,
		"1.82" => 2.2195,
		"1.83" => 2.2048,
		"1.84" => 2.1905,
		"1.85" => 2.1765,
		"1.86" => 2.1628,
		"1.87" => 2.1494,
		"1.88" => 2.1364,
		"1.89" => 2.1236,
		"1.9" => 2.1111,
		"1.91" => 2.0989,
		"1.92" => 2.0869,
		"1.93" => 2.0753,
		"1.94" => 2.0638,
		"1.95" => 2.0526,
		"1.96" => 2.0412,
		"1.97" => 2.0309,
		"1.98" => 2.0204,
		"1.99" => 2.0101,
		"2" => 2
    ];

    /**
    * Получение ставок для каждого исхода
    *
    * @param array (action_id, exodus)
    * @return array[*]->object
    **/

    public static function getBets($data)
    {
    	$info = [];
    	for ($i = 1; $i <= $data['exodus']; $i++) {
    		for ($j = 1; $j <= 2; $j++) {
                if ($j % 2 == 0) {
                    $sort = 'desc';
                } else {
                    $sort = 'asc';
                }

    			$info[$i.$j] = parent::getBet([
                    'id' => $data['action_id'],
                    'move' => $j,
                    'team' => $i,
                    'sort' => $sort
                ]) ?? false;
    		}
    	}

        return $info;
    }

    /**
    * Получение ставок на определенный исход
    *
    * @param array (action_id, move, team)
    * @return object
    **/
    public function getBet($data)
    {
        $coefs = parent::distinct()
        ->where([
            'action_id' => $data['id'],
            'move' => $data['move'],
            'team' => $data['team'],
            ['leftover', '>', 0]
        ])
        ->limit(3)
        ->orderBy('coef', $data['sort'])
        ->select('coef')
        ->get();

        $bets = [];
        foreach ($coefs as $coef) {
        	$leftover = round(parent::where([
                'action_id' => $data['id'],
                'coef' => $coef['coef'],
                'move' => $data['move'],
                'team' => $data['team'],
                ['sum', '>', 0]
            ])
            ->sum('leftover'), 2, PHP_ROUND_HALF_DOWN);
            $bets[] = [
                'action_id' => $data['id'],
                'coef' => $coef['coef'],
                'leftover' => $leftover
            ];
        }
        
        return $bets;
    }

    /**
    * Получение активных ставок пользователя
    *
    * @param array (action_id, user_id, exodus)
    * @return array[*]->object
    **/

    public static function openBets($data)
    {
        $teams = Action::getTeams([
            'id' => $data['action_id']
        ]);
    	$bets = [];
    	for ($i = 1; $i <= $data['exodus']; $i++) {
    		for ($j = 1; $j <= 2; $j++) {
    			$coefs =  parent::distinct()
                ->where([
    				'action_id' => $data['action_id'],
    				'user_id' => $data['user_id'],
    				'original' => 1,
    				'move' => $j,
    				'team' => $i
    			])
    			->select('coef')
    			->get();
    			if ($coefs) {
    				foreach ($coefs as $coef) {
                        $current_bets = parent::where([
                            'action_id' => $data['action_id'],
                            'user_id' => $data['user_id'],
                            'original' => 1,
                            'coef' => $coef['coef'],
                            'move' => $j,
                            'team' => $i,
                            ['sum', '>', 0]
                        ])
                        ->get();
                        $leftover = $current_bets->sum('leftover');
                        if ($leftover > 0) {
    					    $bets['nopair'][$i.$j][] = [
                                'team' => $teams[$i-1],
                                'move' => $j,
        						'coef' => $coef['coef'],
        						'sum' => $leftover
        					];
                        }
                        $sum = $current_bets->sum('sum');
                        if ($sum - $leftover > 0) {
                            $bets['pair'][$i.$j][] = [
                                'team' => $teams[$i-1],
                                'move' => $j,
                                'coef' => $coef['coef'],
                                'sum' => $sum - $leftover
                            ];
                        }
    				}
    			}
    		}
    	}

    	return $bets;
    }

    /**
    * Получение профита пользователя
    *
    * @param array (action_id, user_id, exodus)
    * @return array
    **/

    public static function getProfit($data)
    {
        $bets = parent::where([
            'action_id' => $data['action_id'],
            'user_id' => $data['user_id'],
            'original' => 1,
            ['sum', '>', 0]
        ])
        ->whereColumn('sum', '!=', 'leftover')
        ->get();

        # profit - профит/проигрыш с учетом победы конкретной команды
        $profit = [];
        $profit['exists'] = false;
        foreach ($bets as $bet) {
        	$profit['exists'] = true;
        	break;
        }
        # Для каждого из исходов
        for ($i = 1; $i <= $data['exodus']; $i++) {
            $profit[$i] = 0;
            foreach ($bets as $bet) {
                # Складываем все ставки за эту команду и против других
                if (($bet->team == $i && $bet->move == 1) || ($bet->team != $i && $bet->move == 2)) {
                    if ($bet->move == 1) {
                        $profit[$i] += round($bet->coef * ($bet->sum - $bet->leftover) - ($bet->sum - $bet->leftover), 2, PHP_ROUND_HALF_DOWN);
                    } else {
                        $profit[$i] += ($bet->sum - $bet->leftover);
                    }
                }
                # Если пользователь ставил против этой команды или за другую, отнимаем ставку из результата
                if (($bet->team == $i && $bet->move == 2) || ($bet->team != $i && $bet->move == 1)) {
                    if ($bet->move == 1) {
                        $profit[$i] -= ($bet->sum - $bet->leftover);
                    } else {
                        $profit[$i] -= round($bet->coef * ($bet->sum - $bet->leftover) - ($bet->sum - $bet->leftover), 2, PHP_ROUND_HALF_DOWN);
                    }
                }
            }
        }

        return $profit;
    }

    /**
    * Проверка баланса (с учетом профита и обязательств)
    *
    * @param array (action_id, user_id, user_balance, sum, move, team)
    * @return array/bool
    **/

    public static function checkBalance($data)
    {
    	# Получаем событие для получения кол-ва исходов
        $action = Action::getAction([
            'id' => $data['action_id'],
            'full_info' => 0
        ]);

    	# Получаем профит
    	$profit = self::getProfit([
    		'action_id' => $data['action_id'],
    		'user_id' => $data['user_id'],
    		'exodus' => $action->exodus
    	]);

	    # Проверяем, хватит ли пользователю денег на ставку (с учетом существующего профита и обязательств)
        if ($data['move'] == 2) {
        	# Получаем профит за эту команду ($data['team'])
        	# limit - лимит того, сколько мы можем поставить, не списывая деньги с баланса
	        $limit = $profit[$data['team']];
	        if ($limit >= 0) {
	        	# Ищем наименьший профит за другую команду
	        	$min_profit = ($data['team'] == 1) ? $profit[2] : $profit[1];
	        	foreach ($profit as $key => $value) {
	        		if (($key == $data['team']) || !is_numeric($value)) continue;
	        		if ($value < $min_profit) $min_profit = $value;
	        	}
	        	# Наш лимит это профит за текущую команду + минимальный отрицательный профит за другую команду по модулю
	        	if ($min_profit < 0) $limit += abs($min_profit);
	        } else {
	        	# Находим минимальный профит
	    		$loss = self::findMin($profit);
	    		# Наш лимит это разница между минимальным профитом и профитом за эту команду (либо 0, если профит за эту команду и есть минимальный)
	    		$limit = ($loss == $limit) ? 0 : (abs($loss) - abs($limit));
	        }
        } else {
        	$limit = 0;
        	# Находим два минимальных профита
	    	list($loss, $loss_larger) = self::findTwoMinLosses($profit);
	    	# Если исхода два, мы можем поставить профит за другую команду
	    	if ($action->exodus == 2) {
	    		switch ($data['team']) {
	    			case 1:
	    				if ($profit[2] > 0) $limit += $profit[2];
	    				break;
	    			case 2:
	    				if ($profit[1] > 0) $limit += $profit[1];
	    				break;
	    		}
	    	}
	    	# Наш лимит это разница между двумя минимальными профитами, при условии, что наименьший профит это профит за эту команду
	    	if ($loss < 0 && $loss == $profit[$data['team']]) $limit += abs($loss) - abs($loss_larger);
        }

        # Если денег на ставку не хватит даже с учетом лимита, возвращаем false
        if (($limit + $data['user_balance']) < $data['sum']) return false;
        # Вычитаем остаток суммы ставки из баланса пользователя (если остаток есть)
        $foo = $data['sum'] - $limit;
        if ($foo > 0) User::editBalance([
            'sum' => $foo,
            'user_id' => $data['user_id'],
            'type' => 0
        ]);

        return true;
    }

    // public static function checkBalance($data)
    // {
    // 	# Сюда мы будем записывать сколько мы взяли денег и из какой ставки
    // 	$takenFrom = [];

    // 	# Получаем событие для получения кол-ва исходов
    //     $action = Action::getAction([
    //         'id' => $data['action_id'],
    //         'full_info' => 0
    //     ]);

    // 	# Получаем профит
    // 	$profit = self::getProfit([
    // 		'action_id' => $data['action_id'],
    // 		'user_id' => $data['user_id'],
    // 		'exodus' => $action->exodus
    // 	]);

	   //  # Проверяем, хватит ли пользователю денег на ставку (с учетом существующего профита и обязательств)
    //     if ($data['move'] == 2) {
    //     	# Получаем профит за эту команду ($data['team'])
    //     	# limit - лимит того, сколько мы можем поставить, не списывая деньги с баланса
	   //      $limit = $profit[$data['team']];
	   //      if ($limit >= 0) {
	   //      	# Ищем наименьший профит за другую команду
	   //      	$min_profit = ($data['team'] == 1) ? $profit[2] : $profit[1];
	   //      	foreach ($profit as $key => $value) {
	   //      		if (($key == $data['team']) || !is_numeric($value)) continue;
	   //      		if ($value < $min_profit) $min_profit = $value;
	   //      	}
	   //      	# Наш лимит это профит за текущую команду + минимальный отрицательный профит за другую команду по модулю
	   //      	if ($min_profit < 0) $limit += abs($min_profit);

	   //      	# Если денег на ставку не хватит даже с учетом лимита, возвращаем false
    //     		if (($limit + $data['user_balance']) < $data['sum']) return false;

    //     		# Вычитаем остаток суммы ставки из баланса пользователя (если остаток есть)
		  //       $foo = $data['sum'] - $limit;
		  //       if ($foo > 0) User::editBalance([
		  //           'sum' => $foo,
		  //           'user_id' => $data['user_id'],
		  //           'type' => 0
		  //       ]);

    //     		# Ставки за эту команду
    //     		if ($limit > 0) $bets_back = true;
	   //      } else {
	   //      	# Находим минимальный профит
	   //  		$loss = self::findMin($profit);
	   //  		# Наш лимит это разница между минимальным профитом и профитом за эту команду (либо 0, если профит за эту команду и есть минимальный)
	   //  		$limit = ($loss == $limit) ? 0 : (abs($loss) - abs($limit));
	   //  		# Если денег на ставку не хватит даже с учетом лимита, возвращаем false
    //     		if (($limit + $data['user_balance']) < $data['sum']) return false;
    //     		# Вычитаем остаток суммы ставки из баланса пользователя (если остаток есть)
		  //       $foo = $data['sum'] - $limit;
		  //       if ($foo > 0) User::editBalance([
		  //           'sum' => $foo,
		  //           'user_id' => $data['user_id'],
		  //           'type' => 0
		  //       ]);
	   //      }
	   //      if ($limit > 0) {
	   //      	#														#
    //     		#	Берем деньги из противоположных принятых ставок		#
    //     		#														#

		  //       # Получаем ставки за эту команду и против всех других команд или просто против всех других команд
		  //       $bets = isset($bets_back) ? parent::whereColumn('sum', '!=', 'leftover')
		  //       ->where([
				// 	'action_id' => $data['action_id'],
		  //           'user_id' => $data['user_id'],
		  //           'original' => 1,
		  //           'move' => 1,
    //             	'team' => $data['team'],
    //                 ['sum', '>', 0]
		  //       ])
		  //       ->orWhere([
				// 	'action_id' => $data['action_id'],
		  //           'user_id' => $data['user_id'],
		  //           'original' => 1,
		  //           'move' => 2,
    //             	['team', '!=', $data['team']],
    //                 ['sum', '>', 0]
		  //       ])
    // 			->get() : parent::where([
				// 	'action_id' => $data['action_id'],
		  //           'user_id' => $data['user_id'],
		  //           'original' => 1,
		  //           'move' => 2,
    //             	['team', '!=', $data['team']],
    //                 ['sum', '>', 0]
		  //       ])
		  //       ->whereColumn('sum', '!=', 'leftover')
		  //       ->get();
		  //       foreach ($bets as $bet) {
		  //       	# Если мы уже взяли необходимое нам кол-во денег, выходим из цикла
		  //       	if ($limit == 0) break;
		  //       	# Считаем доступный нам профит с этой ставки
		  //       	$available_profit = ($bet->move == 1) ? round($bet->coef * ($bet->sum - $bet->leftover) - ($bet->sum - $bet->leftover), 2, PHP_ROUND_HALF_DOWN) : ($bet->sum - $bet->leftover);
		  //       	if ($limit >= $available_profit) {
		  //       		# Вычитаем взятую сумму из нашей переменной
		  //       		$limit -= $available_profit;
		  //       		# Записываем информацию о взятии денег в наш массив
		  //       		$takenFrom[] = [
		  //       			'id' => $bet->id,
		  //       			'sum' => $available_profit
		  //       		];
		  //       		# Обновляем сумму принятой ставки
		  //       		$bet->sum = ($bet->move == 1) ? round($bet->sum - (($bet->sum - $bet->leftover) / ($bet->coef - 1)), 2, PHP_ROUND_HALF_DOWN) : ($bet->sum - ($bet->sum - $bet->leftover));
		  //       		$bet->save();
		  //       		# Обновляем сумму принятой связанной ставки
		  //       		if (!is_null($bet->related_bet)) {
		  //       			$related_bet = parent::where('id', $bet->related_bet)->first();
		  //       			$related_bet->sum = round($bet->coef * $bet->sum - $bet->sum, 2, PHP_ROUND_HALF_DOWN);
		  //       			$related_bet->save();
		  //       		}
		  //       	} else {
		  //       		# Записываем информацию о взятии денег в наш массив
		  //       		$takenFrom[] = [
		  //       			'id' => $bet->id,
		  //       			'sum' => $limit
		  //       		];
		  //       		# Обновляем сумму принятой ставки
		  //       		$bet->sum = ($bet->move == 1) ? ($bet->sum - round($bet->sum - ($limit / ($bet->coef - 1)), 2, PHP_ROUND_HALF_DOWN)) : ($bet->sum - $limit);
		  //       		$bet->save();
		  //       		# Обновляем сумму принятой связанной ставки
		  //       		if (!is_null($bet->related_bet)) {
		  //       			$related_bet = parent::where('id', $bet->related_bet)->first();
		  //       			$related_bet->sum = round($bet->coef * $bet->sum - $bet->sum, 2, PHP_ROUND_HALF_DOWN);
		  //       			$related_bet->save();
		  //       		}
		  //       		# limit становится равным 0
		  //       		$limit = 0;
		  //       	}
		  //       }
	   //      }
    //     } else {
    //     	$limit = 0;
    //     	# Находим два минимальных профита
	   //  	list($loss, $loss_larger) = self::findTwoMinLosses($profit);
	   //  	# Если исхода два, мы можем поставить профит за другую команду
	   //  	if ($action->exodus == 2) {
	   //  		switch ($data['team']) {
	   //  			case 1:
	   //  				if ($profit[2] > 0) $limit += $profit[2];
	   //  				break;
	   //  			case 2:
	   //  				if ($profit[1] > 0) $limit += $profit[1];
	   //  				break;
	   //  		}
	   //  	}
	   //  	# Наш лимит это разница между двумя минимальными профитами, при условии, что наименьший профит это профит за эту команду
	   //  	if ($loss < 0 && $loss == $profit[$data['team']]) $limit += abs($loss) - abs($loss_larger);
	   //  	# Если денег на ставку не хватит даже с учетом лимита, возвращаем false
    // 		if (($limit + $data['user_balance']) < $data['sum']) return false;
    // 		# Вычитаем остаток суммы ставки из баланса пользователя (если остаток есть)
	   //      $foo = $data['sum'] - $limit;
	   //      if ($foo > 0) User::editBalance([
	   //          'sum' => $foo,
	   //          'user_id' => $data['user_id'],
	   //          'type' => 0
	   //      ]);
    // 		if ($limit > 0) {
    // 			#														#
    //     		#	Берем деньги из противоположных принятых ставок		#
    //     		#														#

		  //       # Получаем ставки против этой команды и за все другие команды
		  //       $bets = parent::whereColumn('sum', '!=', 'leftover')
		  //       ->where([
				// 	'action_id' => $data['action_id'],
		  //           'user_id' => $data['user_id'],
		  //           'original' => 1,
		  //           'move' => 2,
    //             	'team' => $data['team'],
    //                 ['sum', '>', 0]
		  //       ])
		  //       ->orWhere([
				// 	'action_id' => $data['action_id'],
		  //           'user_id' => $data['user_id'],
		  //           'original' => 1,
		  //           'move' => 1,
    //             	['team', '!=', $data['team']],
    //                 ['sum', '>', 0]
		  //       ])
    // 			->get();
		  //       foreach ($bets as $bet) {
		  //       	# Если мы уже взяли необходимое нам кол-во денег, выходим из цикла
		  //       	if ($limit == 0) break;
		  //       	# Считаем доступный нам профит с этой ставки
		  //       	$available_profit = ($bet->move == 1) ? round($bet->coef * ($bet->sum - $bet->leftover) - ($bet->sum - $bet->leftover), 2, PHP_ROUND_HALF_DOWN) : ($bet->sum - $bet->leftover);
		  //       	if ($limit >= $available_profit) {
		  //       		# Вычитаем взятую сумму из нашей переменной
		  //       		$limit -= $available_profit;
		  //       		# Записываем информацию о взятии денег в наш массив
		  //       		$takenFrom[] = [
		  //       			'id' => $bet->id,
		  //       			'sum' => $available_profit
		  //       		];
		  //       		# Обновляем сумму принятой ставки
		  //       		$bet->sum = ($bet->move == 1) ? round($bet->sum - (($bet->sum - $bet->leftover) / ($bet->coef - 1)), 2, PHP_ROUND_HALF_DOWN) : ($bet->sum - ($bet->sum - $bet->leftover));
		  //       		$bet->save();
		  //       		# Обновляем сумму принятой связанной ставки
		  //       		if (!is_null($bet->related_bet)) {
		  //       			$related_bet = parent::where('id', $bet->related_bet)->first();
		  //       			$related_bet->sum = round($bet->coef * $bet->sum - $bet->sum, 2, PHP_ROUND_HALF_DOWN);
		  //       			$related_bet->save();
		  //       		}
		  //       	} else {
		  //       		# Записываем информацию о взятии денег в наш массив
		  //       		$takenFrom[] = [
		  //       			'id' => $bet->id,
		  //       			'sum' => $limit
		  //       		];
		  //       		# Обновляем сумму принятой ставки
		  //       		$bet->sum = ($bet->move == 1) ? round($bet->sum - ($limit / ($bet->coef - 1)), 2, PHP_ROUND_HALF_DOWN) : ($bet->sum - $limit);
		  //       		$bet->save();
		  //       		# Обновляем сумму принятой связанной ставки
		  //       		if (!is_null($bet->related_bet)) {
		  //       			$related_bet = parent::where('id', $bet->related_bet)->first();
		  //       			$related_bet->sum = round($bet->coef * $bet->sum - $bet->sum, 2, PHP_ROUND_HALF_DOWN);
		  //       			$related_bet->save();
		  //       		}
		  //       		# sum становится равным 0
		  //       		$limit = 0;
		  //       	}
		  //       }
    // 		}
    //     }

    //     return $takenFrom;
    // }

    /**
    * Возвращаем часть денег, если пользователь потратил больше, чем его общие обязательства по игре
    *
    * @param array (action_id, user_id, spend, old_loss, new_loss)
    * @return bool
    **/

    public static function checkAndRefund($data)
    {
    	# Возвращаем пользователю все деньги
		if ($data['new_loss'] >= 0) User::editBalance([
            'sum' => $data['spend'] + abs($data['old_loss']),
            'user_id' => $data['user_id'],
            'type' => 1
        ]);
		# Возвращаем пользователю потраченные на принятую ставку деньги + старые обязательства - новые обязательства
		if ($data['new_loss'] < 0) User::editBalance([
            'sum' => $data['new_loss'] + $data['spend'] + abs($data['old_loss']),
            'user_id' => $data['user_id'],
            'type' => 1
        ]);

		return true;
    }

    /**
    * Удаление ставки
    *
    * @param id
    * @return bool
    **/

    public static function cancelBet($id)
    {
    	# Получаем ставку
    	$target = parent::where('id', $id)->first();
    	# Сохраняем leftover
    	$leftover = $target->leftover;
    	if ($leftover > 0) {
	    	# Обновляем sum и leftover ставки
	    	$target->sum = $target->sum - $leftover;
	    	$target->leftover = 0;
	    	$target->save();
	    	# Получаем инфу о том, сколько мы денег взяли и из каких ставок
	    	$takenFrom = unserialize($target->canceled_bets);
	    	# Возвращаем деньги в те ставки, из которых мы изначально их брали
	    	foreach ($takenFrom as $bet) {
	    		if ($leftover <= 0) break;
	    		# Вычитаем сумму из нашей переменной
	    		$leftover -= $bet->sum;
	    		# Возвращаем деньги, взятые из ставки
	    		$current = parent::where('id', $bet->id)->first();
	    		$current->sum = ($current->move == 1) ? round($current->sum + ($bet->sum / ($current->coef - 1)), 2, PHP_ROUND_HALF_DOWN) : ($current->sum + $bet->sum);
	    		$current->save();
        		if (!is_null($current->related_bet)) {
        			$related_current = parent::where('id', $current->related_bet)->first();
        			$related_current->sum = round($current->coef * $current->sum - $current->sum, 2, PHP_ROUND_HALF_DOWN);
        			$related_current->save();
        		}
	    	}
	    	# Возмещаем пользователю остаток денег
	    	if ($leftover > 0) User::editBalance([
                'sum' => ($target->move == 1) ? $target->leftover : round($target->coef * $target->leftover - $target->leftover, 2, PHP_ROUND_HALF_DOWN),
                'user_id' => $target->user_id,
                'type' => 1
            ]);
	    	# Если ставка так и не была принята, удаляем её из базы
	    	if ($target->sum == 0) $target->delete();

	    	return true;
	    }

	    return false;
    }

    /**
    * Проверка коэффициента на валидность
    *
    * @param array (exodus, coef)
    * @return bool/array
    **/

    public static function checkCoef($data)
    {
    	if ($data['exodus'] == 2) {
    		$check = true;
			if ($data['coef'] > 2) {
				if (array_search($data['coef'], self::COEF_TABLE) === false) $check = false;
			} else {
				if (!isset(self::COEF_TABLE[(string)$data['coef']])) $check = false;
			}
			if (!$check) {
				# Ищем ближайший допустимый коэффициент в таблице и возвращаем его пользователю
				$closest = self::COEF_TABLE['1.01'];
				foreach (self::COEF_TABLE as $value) {
					if ($value >= $data['coef']) {
						$closest = $value;
					} else {
						if ($closest - $data['coef'] > $data['coef'] - $value) return [
							'original' => $data['coef'],
							'new' => $value
						];

						return [
							'original' => $data['coef'],
							'new' => $closest
						];
					}
				}
			}
		} else {
			if ($data['coef'] <= 2) return true;
			# Если у коэффициента пользователя неправильный шаг, возвращаем false
			if ($data['coef'] < 3 && ($data['coef'] % 0.02) != 0) return false;
			if ($data['coef'] < 4 && ($data['coef'] % 0.05) != 0) return false;
			if ($data['coef'] < 6 && ($data['coef'] % 0.1) != 0) return false;
			if ($data['coef'] < 10 && ($data['coef'] % 0.2) != 0) return false;
			if ($data['coef'] < 20 && ($data['coef'] % 0.5) != 0) return false;
			if ($data['coef'] < 30 && ($data['coef'] % 1) != 0) return false;
			if ($data['coef'] < 50 && ($data['coef'] % 2) != 0) return false;
			if ($data['coef'] < 100 && ($data['coef'] % 10) != 0) return false;
			if ($data['coef'] > 100 && ($data['coef'] % 101) != 0) return false;
		}

    	return true;
    }

    /**
    * Получение противоположного коэффициента
    *
    * @param array (exodus, coef)
    * @return num
    **/

    public static function getReverseCoef($data)
    {
    	if ($data['exodus'] == 2) {
    		if ($data['coef'] > 2) return array_search($data['coef'], self::COEF_TABLE);

    		return self::COEF_TABLE[(string)$data['coef']];
    	}

    	return round($data['coef'] / ($data['coef'] - 1), 2, PHP_ROUND_HALF_DOWN);
    }

    /**
    * Добавление новой ставки
    *
    * @param array (action_id, user_id, original, coef, sum, leftover, move, team)
    * @return num
    **/

    public static function addBet($data)
    {
        # Сумма сыгравших ставок, возвращаемое значение функции
        $update = 0;

        # Добавляем ставку в БД
        $bet = parent::create($data);

        # Получаем событие для получения кол-ва исходов
        $action = Action::getAction([
            'id' => $data['action_id'],
            'full_info' => 0
        ]);

        # Считаем, сколько пользователь потратил на все парные ставки до принятия текущей
        $old_bet_loss = self::findMin(self::getProfit([
            'action_id' => $data['action_id'],
            'user_id' => $bet->user_id,
            'exodus' => $action->exodus
        ]));
        if ($old_bet_loss > 0) $old_bet_loss = 0;

        # Если исхода два, создаем связанную ставку
        $related_bet = false;
        if ($action->exodus == 2) {
            #                                       	#
            #   Теперь надо создать связанную ставку	#
            #   по противоположному коэффициенту    	#
            #                                       	#

            # Вычисляем сумму
            $reverse_sum = round(($bet->coef * $bet->sum) - $bet->sum, 2, PHP_ROUND_HALF_DOWN);
            # Создаем связанную ставку
            $related_bet = parent::create([
                'action_id' => $bet->action_id,
                'user_id' => $bet->user_id,
                'original' => 0,
                'coef' => self::getReverseCoef([
                	'exodus' => $action->exodus,
                	'coef' => $bet->coef
                ]),
                'sum' => $reverse_sum,
                'leftover' => $reverse_sum,
                'move' => ($bet->move == 1) ? 2 : 1,
                'team' => ($bet->team == 1) ? 2 : 1,
                'related_bet' => $bet->id
            ]);
            # Связываем ставки
            $bet->related_bet = $related_bet->id;
            $bet->save();
        }

        # Отменяем все ставки этого же пользователя с менее выгодным коэффициентом
        if ($bet->move == 1) {
        	$bets_to_remove = parent::where([
                'action_id' => $bet->action_id,
                'user_id' => $bet->user_id,
                'move' => ($bet->move == 1) ? 2 : 1,
                'team' => $bet->team,
                ['coef', '>=', $bet->coef],
                ['sum', '>', 0],
                ['leftover', '>', 0]
            ])
            ->get();
        } else {
        	$bets_to_remove = parent::where([
                'action_id' => $bet->action_id,
                'user_id' => $bet->user_id,
                'move' => ($bet->move == 1) ? 2 : 1,
                'team' => $bet->team,
                ['coef', '<=', $bet->coef],
                ['sum', '>', 0],
                ['leftover', '>', 0]
            ])
            ->get();
        }
        foreach ($bets_to_remove as $bet_to_remove) cancelBet($bet_to_remove->id);

        #                                               #
        #   Теперь надо найти уже существующие ставки,  #
        #   с которыми наша новая может сыграть         #
        #                                               #

        # Бесконечный цикл для поиска ставок, который прерывается если:
        # 1) Наша ставка сыграла
        # 2) По выбранному исходу не найдено подходящих чужих ставок
        while (true) {
            # leftover - остаток банка ставки
            if ($bet->leftover == 0) break;

            # Получаем наиболее выгодный коэффициент
            if ($bet->move == 1) {
                $best_coef = parent::where([
                    'action_id' => $bet->action_id,
                    'move' => ($bet->move == 1) ? 2 : 1,
                    'team' => $bet->team,
                    ['coef', '>', $bet->coef],
                    ['sum', '>', 0],
                    ['leftover', '>', 0]
                ])
                ->max('coef');
            } else {
                $best_coef = parent::where([
                    'action_id' => $bet->action_id,
                    'move' => ($bet->move == 1) ? 2 : 1,
                    'team' => $bet->team,
                    ['coef', '<', $bet->coef],
                    ['sum', '>', 0],
                    ['leftover', '>', 0]
                ])
                ->min('coef');
            }
            
            # Если мы нашли коэффициент выгоднее нашего
            if ($best_coef) {
                # Получаем наиболее выгодную ставку
                $active_bet = parent::where([
                    'action_id' => $bet->action_id,
                    'coef' => $best_coef,
                    'move' => ($bet->move == 1) ? 2 : 1,
                    'team' => $bet->team,
                    ['sum', '>', 0],
                    ['leftover', '>', 0]
                ])
                ->first();

                # Получаем связанную ставку
                $related_active_bet = false;
                if ($related_bet) $related_active_bet = parent::where('related_bet', $active_bet->id)->first();

                # Если банк найденной ставки строго меньше нашего
                if ($active_bet->leftover < $bet->leftover) {
                    # Возвращаем пользователю часть баланса
                    if ($bet->move == 2) {
                        $overflow = round(($active_bet->coef * $active_bet->leftover - $active_bet->leftover) / ($active_bet->coef - 1), 2, PHP_ROUND_HALF_DOWN) - $active_bet->leftover;
                        $sum = round($active_bet->coef * $overflow - $overflow, 2, PHP_ROUND_HALF_DOWN);
                        if ($sum > 0) User::editBalance([
                            'sum' => $sum,
                            'user_id' => $bet->user_id,
                            'type' => 1
                        ]);
                    }

                    # Считаем, сколько владелец найденной ставки потратил на все парные ставки до принятия текущей
			        $old_active_loss = self::findMin(self::getProfit([
			            'action_id' => $data['action_id'],
			            'user_id' => $active_bet->user_id,
			            'exodus' => $action->exodus
			        ]));
			        if ($old_active_loss > 0) $old_active_loss = 0;

                    # Обновляем сумму сыгравших ставок
                    $update += $active_bet->leftover + $active_bet->leftover;
                    
                    # Создаем ставку по новому коэффициенту
                    parent::create([
                        'action_id' => $bet->action_id,
                        'user_id' => $bet->user_id,
                        'original' => 1,
                        'coef' => $active_bet->coef,
                        'sum' => $active_bet->leftover,
                        'leftover' => 0,
                        'move' => $bet->move,
                        'team' => $bet->team
                    ]);

                    # active_spend - сколько потратил пользователь найденной ставки при принятии текущей
                    $active_spend = ($active_bet->move == 1) ? $active_bet->leftover : round(($active_bet->coef * $active_bet->leftover) - $active_bet->leftover, 2, PHP_ROUND_HALF_DOWN);

                    # Вычитаем сумму из банка основной ставки
                    $bet->leftover -= $active_bet->leftover;
                    $bet->save();
                    if ($related_bet) {
                        # Обновляем банк связанной ставки
                        $related_bet->leftover = round(($bet->coef * $bet->leftover) - $bet->leftover, 2, PHP_ROUND_HALF_DOWN);
                        $related_bet->save();
                    }
                    
                    # Банк найденной ставки становится равным 0
                    $active_bet->leftover = 0;
                    $active_bet->save();
                    if ($related_active_bet) {
                        $related_active_bet->leftover = 0;
                        $related_active_bet->save();
                    }

                    # Считаем наименьший профит владельца найденной ставки после принятия текущей
			        $new_active_loss = self::findMin(self::getProfit([
			            'action_id' => $data['action_id'],
			            'user_id' => $active_bet->user_id,
			            'exodus' => $action->exodus
			        ]));
			        # Возмещаем деньги
					self::checkAndRefund([
						'action_id' => $data['action_id'],
			            'user_id' => $active_bet->user_id,
			            'spend' => $active_spend,
			            'old_loss' => $old_active_loss,
			            'new_loss' => $new_active_loss
					]);

                    continue;
                } else {
                    # Если банк найденной ставки больше либо равен нашему
                    # Возвращаем пользователю часть баланса
                    if ($bet->move == 2) {
                        $overflow = round(($bet->coef * $bet->leftover - $bet->leftover) / ($active_bet->coef - 1), 2, PHP_ROUND_HALF_DOWN) - $bet->leftover;
                        $sum = round($active_bet->coef * $overflow - $overflow, 2, PHP_ROUND_HALF_DOWN);
                        if ($sum > 0) User::editBalance([
                            'sum' => $sum,
                            'user_id' => $bet->user_id,
                            'type' => 1
                        ]);
                    }

                    # Меняем коэффициент
                    $bet->coef = $active_bet->coef;
                    # Сохраняем ставку
                    $bet->save();
                    if ($related_bet) {
                        $related_bet->coef = $related_active_bet->coef;
                        # Пересчитываем банк связанной ставки по новому коэффициенту
                        $sum = round(($active_bet->coef * $bet->leftover) - $bet->leftover, 2, PHP_ROUND_HALF_DOWN);
                        $related_bet->leftover = $sum;
                        # Обновляем общую сумму связанной ставки
                        $new_related_sum = $sum + ($sum - $related_bet->leftover);
                        $related_bet->sum = $new_related_sum;
                        $related_bet->save();
                    }
                }
            } else {
                # Если выгоднее коэффициента нет, берем ставку с текущим коэффициентом
                $active_bet = parent::where([
                    'action_id' => $bet->action_id,
                    'coef' => $bet->coef,
                    'move' => ($bet->move == 1) ? 2 : 1,
                    'team' => $bet->team,
                    ['sum', '>', 0],
                    ['leftover', '>', 0]
                ])
                ->first();

                # Если ставок нет, выходим из цикла
                if (!$active_bet) break;

                # Ищем связанную ставку
                $related_active_bet = false;
                if ($related_bet) $related_active_bet = parent::where('related_bet', $active_bet->id)->first();
            }

            # Считаем, сколько владелец найденной ставки потратил на все парные ставки до принятия текущей
	        $old_active_loss = self::findMin(self::getProfit([
	            'action_id' => $data['action_id'],
	            'user_id' => $active_bet->user_id,
	            'exodus' => $action->exodus
	        ]));
	        if ($old_active_loss > 0) $old_active_loss = 0;

            # Если банк найденной ставки больше или равен нашему
            if ($active_bet->leftover >= $bet->leftover) {
                # Обновляем сумму сыгравших ставок
                $update += $bet->leftover + $bet->leftover;

                # active_spend - сколько потратил пользователь найденной ставки при принятии текущей
                $active_spend = ($bet->move == 2) ? $bet->leftover : round(($bet->coef * $bet->leftover) - $bet->leftover, 2, PHP_ROUND_HALF_DOWN);

                # Вычитаем из банка найденной ставки наш банк
                $active_bet->leftover -= $bet->leftover;
                $active_bet->save();
                if ($related_active_bet) {
                    # Считаем банк связанной найденной ставки
                    $related_active_bet->leftover = round(($active_bet->coef * $active_bet->leftover) - $active_bet->leftover, 2, PHP_ROUND_HALF_DOWN);
                    $related_active_bet->save();
                }

                # Банк нашей ставки становится равным 0
                $bet->leftover = 0;
                $bet->save();
                if ($related_bet) {
                    $related_bet->leftover = 0;
                    $related_bet->save();
                }
            } else {
                # Если банк найденной ставки строго меньше нашего
                # Обновляем сумму сыгравших ставок
                $update += $active_bet->leftover + $active_bet->leftover;

                # active_spend - сколько потратил пользователь найденной ставки при принятии текущей
                $active_spend = ($active_bet->move == 1) ? $active_bet->leftover : round(($active_bet->coef * $active_bet->leftover) - $active_bet->leftover, 2, PHP_ROUND_HALF_DOWN);

                # Из банка основной ставки вычитаем банк найденной
                $bet->leftover -= $active_bet->leftover;
                $bet->save();
                if ($related_bet) {
                    # Считаем банк связанной ставки
                    $related_bet->leftover = round(($bet->coef * $bet->leftover) - $bet->leftover, 2, PHP_ROUND_HALF_DOWN);
                    $related_bet->save();
                }

                # Банк найденной ставки становится равным 0
                $active_bet->leftover = 0;
                $active_bet->save();
                if ($related_active_bet) {
                    $related_active_bet->leftover = 0;
                    $related_active_bet->save();
                }
            }

            # Считаем наименьший профит владельца найденной ставки после принятия текущей
	        $new_active_loss = self::findMin(self::getProfit([
	            'action_id' => $data['action_id'],
	            'user_id' => $active_bet->user_id,
	            'exodus' => $action->exodus
	        ]));
			# Возмещаем деньги
			self::checkAndRefund([
				'action_id' => $data['action_id'],
	            'user_id' => $active_bet->user_id,
	            'spend' => $active_spend,
	            'old_loss' => $old_active_loss,
	            'new_loss' => $new_active_loss
			]);
        }

        # Считаем, сколько пользователь потратил на все парные ставки после принятия текущей
        $new_bet_loss = self::findMin(self::getProfit([
            'action_id' => $data['action_id'],
            'user_id' => $bet->user_id,
            'exodus' => $action->exodus
        ]));
        if ($new_bet_loss > 0) $new_bet_loss = 0;

        # Возмещаем деньги
        if ($old_bet_loss < $new_bet_loss) User::editBalance([
			'sum' => abs($old_bet_loss) - abs($new_bet_loss),
			'user_id' => $bet->user_id,
			'type' => 1
		]);
        
        return $update;
    }

    /**
    * Вычисление минимального значения в массиве (без учета строк)
    *
    * @param array
    * @return num
    **/

    public static function findMin($array)
    {
    	$min = $array[1] ?? $array[2];
    	foreach ($array as $value) {
    		if (!is_numeric($value)) continue;
    		if ($value < $min) $min = $value;
    	}

    	return $min;
    }

    /**
    * Вычисление двух минимальных затрат по командам
    *
    * @param array
    * @return num
    **/

    public static function findTwoMinLosses($profit)
    {
    	# Находим два минимальных профита (наименьший из них это наши затраты по всем принятым ставкам, сразу обнуляем их, если они больше 0)
    	$loss = false;
    	$loss_larger = false;
    	foreach ($profit as $value) {
	        if (!is_numeric($value)) continue;
	        if ($loss === false) {
	            $loss = $value;
	            continue;
	        }
	        if ($loss_larger === false) {
	            $loss_larger = $value;
	            if ($loss_larger < $loss) {
	                $foo = $loss;
	                $loss = $loss_larger;
	                $loss_larger = $foo;
	            }
	            continue;
	        }
	        if ($value <= $loss_larger) {
	            if ($value < $loss) {
	    			$loss_larger = $loss;
	    			$loss = $value;
	            } else {
	                $loss_larger = $value;
	            }
	        }
	    }
	    if ($loss > 0) $loss = 0;
	    if ($loss_larger > 0) $loss_larger = 0;

	    return [$loss, $loss_larger];
    }
}
