<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Log;
use App\Http\Controllers\PagesController;
use Mail;
use App\Http\Requests\RegisterValidate;
use App\Http\Requests\AuthValidate;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditDataValidate;
class AccountController extends Controller
{

	public $email;

    /**
	* Register user
	*
	* @param array (POST)
	* @return redirect
    */

	public function register(RegisterValidate $data)
	{
		/* Проверка авторизации */
		if(!Auth::guest()) return redirect()->route('index');
		/* Генерируем ключ подтвеждения мыла */
		$email_key = Hash::make(mt_rand(10, 15));
		/* Создаем нового пользователя */
		$user = User::create([
			'email' => $data['email'],
			'password' => Hash::make($data['password']),
			'name' => $data['name'], 
			'login' => $data['login'],
			'number' => $data['number'],
			'ref_id' => $data['ref_id'] ?? 0,
			'email_verified' => $email_key,
			'balance' => 0
		]);
		/* Отправляем письмо на почту */
		$this->email = $data['email'];
		$message_email = 'Активируйте свой аккаунт по ссылке - '.route('emailverify').'?'.http_build_query([
			'key' => $email_key
		]);
		Mail::raw($message_email, function($message){
			$message->from('support@bithallow.com', 'Активируйте свой аккаунт - '.env('APP_NAME'));
			$message->to($this->email)->cc($this->email);
		});
		return redirect()->back()->with('message', 'Вы успешно создали аккаунт, подтвердите адрес электронной почты чтобы войти на сайт!');
	}

	/**
	* Auth user
	*
	* @param array (GET)
	* @return redirect
	*/

	public function auth(AuthValidate $data)
	{
		if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']])){
			Log::create([
				'user_id' => Auth::user()->id,
				'ip' => $data->ip()
			]);
			return redirect()->route('index');
		} else{
			return redirect()->back()->with('message_auth', 'Проверьте правильность введенных данных!');
		}
	}

	/**
	* Verify email
	*
	* @param array (GET)
	* @return redirect
    */

	public function emailVerify(Request $data)
	{
		$user = User::where('email_verified', $data['key'])->first() ?? null;
		if(is_null($user)) return redirect(route('index'));
		$user->update([
			'email_verified' => true
		]);
		return redirect(route('register'))->with('message', 'Вы успешно подтвердили адрес электронной почты! Ввойдите на сайт используя свои учетный данные!');
	}

	/**
	* Edit date
	*
	* @return @var
	*/

	public function edit(EditDataValidate $data)
	{
		return;
		$user = Auth::user();
		if($user->email != $data['email']){
			/* Проверяем новый email и сохраняем в случае удачи */
			if(empty(User::where('email', $data['email'])->first())){
				/* Генерируем ключ подтвеждения мыла */
				$email_key = Hash::make(mt_rand(10, 15));
				/* Пишем ключ в базу */
				$user->email = $data['email'];
				$user->email_verified = $email_key;
				$user->save();
				/* Отправляем письмо на почту */
				$this->email = $data['email'];
				$message_email = 'Активируйте свой аккаунт по ссылке - '.route('emailverify').'?'.http_build_query([
					'key' => $email_key
				]);
				Mail::raw($message_email, function($message){
					$message->from('support@bithallow.com', 'Активируйте свой аккаунт - '.env('APP_NAME'));
					$message->to($this->email)->cc($this->email);
				});
			} else{
				return redirect(route('data'))->with('message', 'Данный email уже занят!');
			}
		}
		if($user->number != $data['number']){
			if(empty(User::where('number', $data['number'])->first())){
				/* Пишем номер в базу */
				$user->number = $data['number'];
				$user->save();
			} else{
				return redirect(route('data'))->with('message', 'Данный номер уже занят!');
			}
		}
		return redirect(route('data'))->with('message', 'Данные успешно обновлены!');
	}
}
