<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Answer extends Model
{
 	# Статус ответа
    static $status = [
    	'error' => 0,
    	'success' => 1
    ];

    static $messages = [
    	'ceff.false' => 'Вы не можете поставить на данный коэфициент!',
    	'balance.false' => 'Недостаточно средств на балансе!',
    	'default.false' => 'Неизвестная ошибка :(',
    	'success.bet' => 'Ваша ставка размещена!',
        'load.bet' => 'Ваша ставка принята в обработку!',
        'error.coef' => 'Невалидный коэффициент!',
        'error_exodus.coef' => 'На двух исходах ближайший доступный коэффициент: {ph1}. Ваши коэффициенты были обновлены.'
    ];
 
	/**
	* Генерация ответа сервера
    *
    * @param array (status, data, message)
    * @return json
    **/

    public static function generate($data)
    {
    	return response()->json([
    		'success' => self::$status[
    			$data['status']
    		],
            'data' => isset($data['data']) ? json_encode($data['data']) : '',
            'text' => isset($data['message']) ? self::$messages[
                $data['message']
            ] : '',
    		'time' => Carbon::now()
    	]);
    }
}
