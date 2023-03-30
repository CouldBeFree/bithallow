@extends('layouts.my')
@section('title', 'Безопасность')
@section('content')
		<div class="content-content">
			<div class="personal-date full-bl">
				<div class="block">
					<div class="tr">
						<div class="title">
							<span>Двухступенчатая система безопасности</span>
							<span class="edit">Изменить</span>
						</div>
					</div>
					<div class="info-block">
						<div class="line">
							<div class="text">Повысьте уровень защищенности Вашей учетной записи, используя при входе не только пароль, но и телефон.
							После того как Вы укажете пароль, Вам потребуется ввести код, который Вы получите через мобильное приложение.</div>
						</div>
						<div class="line">
							<div class="text">
								<b>Статус: </b>Выкл
							</div>
						</div>
					</div>
				</div>
				<div class="block">
					<div class="tr">
						<div class="title">
							<span>Последние 10 авторизаций</span>
						</div>
					</div>
					<div class="info-block">
						@forelse($data as $item)
							<div class="line">
								<div class="text login-history">
									<span>{{ $item->created_at }}</span>
									<span><b>Успешный вход в систему</b></span>
									<span>IP: {{ $item->ip }}</span>
								</div>
							</div>
						@empty
						@endforelse
					</div>
				</div>
				<!-- <div class="block">
					<div class="tr">
						<div class="title">
							<span>Настройки выхода</span>
							<span class="edit">Изменить</span>
						</div>
					</div>
					<div class="info-block">
						<div class="line">
							<div class="text">Если Вы беспокоитесь о сохранности счета, Вы можете включить функцию автоматического выхода, которая будет срабатывать, в случае если Вы не будете использовать счет на протяжении определенного промежутка времени.</div>
						</div>
						<div class="line">
							<div class="text">
								<b>Статус: </b>Выкл
							</div>
						</div>
					</div>
				</div> -->
			</div>
		</div>
@endsection