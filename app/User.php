<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'email_verified', 'ref_id', 'name', 'login', 'number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
    * Редактирование (+/-) баланса пользователя
    *
    * @param array (sum, user_id, type - minus 0/plus 1)
    * @return boolean 
    */

    public static function editBalance($data)
    {
        if ($data['sum'] <= 0) return false;
        # Получаем пользователя
        $user = parent::where('id', $data['user_id'])
        ->select('id', 'balance')
        ->first() ?? false;
        if (!$user) return false;
        # Редактируем баланс
        switch($data['type']) {
            case 0:
                # Проверяем баланс
                if ($user->balance < $data['sum']) return false;
                $user->balance -= $data['sum'];
                break;
            case 1:
                $user->balance += $data['sum'];
                break;
        }
        # Сохраняем пользователя
        $user->save();
        
        return true;
    }

    /**
    * Получение баланса пользователя
    *
    * @param user_id
    * @return num 
    */

    public static function getBalance($user_id)
    {
        # Получаем пользователя
        $user = parent::where('id', $user_id)
        ->select('id', 'balance')
        ->first() ?? false;
        if (!$user) return 0;

        return $user->balance;
    }
}
