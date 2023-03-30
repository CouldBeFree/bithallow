<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\ActionController;
use App\Http\Requests\AddBet;
use App\Bet;
use App\User;
use App\Action;
use App\Answer;
use Centrifugo;

class AddBets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        # Добавление ставки
        $sum = Bet::addBet($this->data);
        # Обновляем данные события
        if($sum > 0) Action::updateAction([
            'id' => $this->data['action_id'],
            'sum' => $sum
        ]);

        $balance = User::getBalance($this->data['user_id']);
        
        $centrifugo = new Centrifugo();
        $centrifugo->publish(
            $this->data['action_id'].'_action', 
            ActionController::actionInfo($this->data['action_id'])
        );
        $centrifugo->publish(
            $this->data['action_id'].'_action_'.$this->data['user_id'].'_user', 
            Answer::generate([
                'status' => 'success',
                'data' => [
                    'move' => $this->data['move'],
                    'team' => $this->data['team'],
                    'balance' => $balance
                ],
                'message' => 'success.bet'
            ])
        );
    }
}
