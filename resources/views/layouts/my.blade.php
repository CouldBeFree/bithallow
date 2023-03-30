<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>@yield('title') - {{ env('APP_NAME') }}</title>
	<!-- Less -->
	<link rel="stylesheet/less" type="text/css" href="/templates/less/styles.less">
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap&subset=cyrillic" rel="stylesheet">
	<!-- Less -->
	<link rel="stylesheet/less" type="text/css" href="/templates/less/myaccount.less">
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
	<div class="top">
		<div class="left">
			<div class="logo">
				<a href="/"><img src="/templates/img/logo.png" alt="BITHALLOW"></a>
			</div>
			<div class="search">
				<input type="text" placeholder="Команды, соревнования и тд...">
				<button>
					<img src="/templates/img/icon/search.png" alt="Search">
				</button>
			</div>
		</div>
		@if(Auth::guest())
		<div class="right">
			<form action="{{ route('signin') }}" class="auth-form" method="POST">
				<div class="left-auth">
					<input type="email" placeholder="Электронная почта" style="width: 200px" name="email" required>
					<input type="password" placeholder="Пароль" style="width: 120px;" name="password" required>
					{{ csrf_field() }}
					<div class="button">
						<button>Войти</button>
					</div>
				</div>
				@if($errors->any())
					<div class="right-auth">
						<div class="message">Проверьте правиильность введенных данных!</div>
					</div>
				@elseif(session('message_auth'))
					<div class="right-auth">
						<div class="message">{{ session('message_auth') }}</div>
					</div>
				@endif
			</form>
			<a href="/register" class="button-a">Регистрация</a>
			<div class="lang">
				<div class="icon-lang">
					<img src="/templates/img/icon/lang/usa.svg" alt="">
				</div>
				<div class="icon">
					<img src="/templates/img/icon/list-open.svg" alt="">
				</div>
			</div>
		</div>
		@else
		<div class="right" id="right_menu">
			<div class="list" onclick="myBalanceToggle()">
				<div class="text">Мой счет</div>
				<div class="icon">
					<img src="/templates/img/icon/list-open.svg" alt="">
				</div>
				<div class="modal mybalance" id="myBalanceModal" style="display: none;">
					<ul>
						<li><a href="#"><b>0.3 BTC</b> Денежные балансы</a></li>
						<li><a href="#"><b>0.001123 BTC</b> Баланс сайта</a></li>
						<li><a href="#"><b>0.2 BTC</b> Баланс биржи</a></li>
						<li><a href="#"><b>0.33 BIP</b> Баланс биржи</a></li>
					</ul>
				</div>
			</div>
			<div class="list" onclick="balanceToggle()">
				<div class="text balanceUpdate">{{ Auth::user()->balance }}</div>
				<div class="icon">
					<img src="/templates/img/icon/coins.svg" alt="">
				</div>
				<div class="icon">
					<img src="/templates/img/icon/list-open.svg" alt="">
				</div>
				<div class="modal balance balance_hhh" id="balanceModal" style="display: none;">
					<ul>
						<li>{{ Auth::user()->email }}</li>
						<li><a href="{{ route('balance') }}"><b>Мой счет</b></a></li>
						<li><a href="{{ route('data') }}"><b>Личные данные</b></a></li>
						<li><a href="{{ route('historyBets') }}"><b>История ставок</b></a></li>
						<li><a href="{{ route('security') }}"><b>Безопасность</b></a></li>
						<li><a href="{{ route('logout') }}"><b>Выход</b></a></li>
					</ul>
				</div>
			</div>
			<div class="icon" style="margin-right: 10px; cursor: pointer;" onclick="getBalance()">
				<img src="/templates/img/icon/refresh.svg" alt="" class="balanceUpdateBtn" style="animation-duration: 3s; animation-iteration-count: infinite; animation-timing-function: linear;">
			</div>
			<div class="lang">
				<div class="icon-lang">
					<img src="/templates/img/icon/lang/usa.svg" alt="">
				</div>
				<div class="icon">
					<img src="/templates/img/icon/list-open.svg" alt="">
				</div>
			</div>
		</div>
		@endif
	</div>
	<div class="nav">
		<div class="left">
			<ul>
				<li>
					<a href="{{ route('balance') }}">Мой счет</a>
				</li>
				<li>
					<a href="{{ route('data') }}">Личные данные</a>
				</li>
				<li>
					<a href="{{ route('historyBets') }}">История ставок</a>
				</li>
				<li>
					<a href="{{ route('security') }}">Безопасность</a>
				</li>
			</ul>
		</div>
		<div class="right">
			<ul>
				<li>
					<a href="#">
						<span>Обмен btc/bip</span>
						<span class="icon" style="height: 23px;">
							<img src="/templates/img/icon/ripple.svg" alt="">
						</span>
					</a>
				</li>
				<li>
					<a href="#">
						<span>Помощь</span>
						<span class="icon" style="height: 29px;">
							<img src="/templates/img/icon/refresh.svg" alt="">
						</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="content">
		@yield('content')
	</div>
	<!-- JavaScrip -->
    <script src="/templates/js/less.js"></script>
    <script src="/templates/js/app.js"></script>
    <script type="text/javascript">
    	const INFO = {
			domain: "",
			minCoef: 1.01,
			maxCoef: 101,
			minBet: 1,
			commission: 5,
			lang: "ru"
		}
    	@if(Auth::guest())
		const AUTH = false
		let calcCommission = false
		@else
		const AUTH = true
		let calcCommission = false
		document.addEventListener('click', e => {
			let target = e.target
			let click_menu = false
			if (menu.contains(target) || myBalanceModal.contains(target) || balanceModal.contains(target)) {
				click_menu = true
			}

			if (!click_menu) {
				myBalanceModal.style.display = "none"
				balanceModal.style.display = "none"
			}
		})
		@endif
    </script>
    @yield('jspage')
</body>
</html>
<!-- browser-sync start --server --files "less/*.less" -->
