<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class QueueStatus extends Model
{
	/**
	*
	* Запуск\остановка воркера события
	*
	* @param action_id, start (1/0)
	*
	* @return json
	**/

    public static function queue($data)
    {
    	$response = Http::get('http://127.0.0.1/queue.php', [
    		'start' => $data['start'],
    		'action_id' => $data['action_id']
    	]);
    	return $response->json();
    }
}
