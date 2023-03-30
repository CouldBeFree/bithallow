@extends('layouts.index')
@section('title', 'Регистрация')
@section('content')
<div class="content-content register">
	<div class="block">
		<div class="tr">
			<div class="title">Регистрация</div>
		</div>
		<div class="info-block">
			<form action="{{ route('signup') }}" method="POST">
				<div class="input">
					<label class="labelText">Email адрес</label>
					<input class="userInput" id="email" type="email" name="email" required>
				</div>
				<div class="input">
					<label class="labelText">Пароль</label>
					<input class="userInput" id="password" type="password" name="password" required>
					<div class="text">Надежный пароль должен иметь не менее 8 символов,<br>строчные и заглавные буквы, цифры.</div>
				</div>
				<div class="input">
					<label class="labelText">Имя</label>
					<input class="userInput" id="name" type="text" name="name" required>
				</div>
				<div class="input">
					<label class="labelText">Логин</label>
					<input class="userInput" id="login" type="text" name="login" required>
				</div>
				<div class="input">
					<label class="labelText">Номер телефона в международном формате</label>
					<input class="userInput" id="number" type="number" name="number" required>
				</div>
				<div class="input">
					<label class="labelText">ID реферала</label>
					<input class="userInput" id="id_ref" type="text" name="id_ref">
				</div>
				<div class="input checkbox">
					<input type="checkbox" id="checkbox" required>
					<div class="text">Я подтверждаю, что мне есть 18 лет и я согласен<br>с <a href="#">Политикой конфеденциальности</a>.</div>
				</div>
				{{ csrf_field() }}
				<div class="input button">
					<button>Регистрация</button>
				</div>
			</form>
			<div class="text-info">Есть аккаунт? <label for="auth_email">Войдите в него!</label></div>
			@if(session('message'))
			<div class="text-info success">{{ session('message') }}</div>
			@elseif($errors->any())
				@foreach ($errors->all() as $error)
	                <div class="text-info error">{{ $error }}</div>
	            @endforeach
			@endif
		</div>
	</div>
</div>
@endsection
@section('jspage')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
	$('.userInput').focus(function () {
		$(this).parent().addClass('focus');
	}).blur(function () {
		if($(this).val()===''){
			$(this).parent().removeClass('focus');
		}
	})
</script>
@endsection
@section('lesspage')
<link rel="stylesheet/less" type="text/css" href="/templates/less/myaccount.less">
<link rel="stylesheet/less" type="text/css" href="/templates/less/register.less">
@endsection