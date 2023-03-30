@extends('layouts.my')
@section('title', 'Мой счет')
@section('content')
<div class="content-content">
			<div class="left">
				<div class="block">
					<div class="tr">
						<div class="title">Здравствуйте! Здесь Вы можете узнать, какую ставку Вы можете разместить:</div>
					</div>
					<div class="info-block">
						<div class="balance">
							<span class="sum">0,00 BTC</span>
							<span class="icon">
								<img src="/templates/img/icon/refresh-dark.svg" alt="">
							</span>
						</div>
						<div class="url">
							<a href="#">Пополнение счета</a> \ <a href="#">Снятие средств</a>
						</div>
					</div>
				</div>
			</div>
			<div class="right">
				<div class="block">
					<div class="tr">
						<div class="title">Денежные балансы</div>
					</div>
					<div class="info-block">
						<div class="title-block">Баланс сайта</div>
						<div class="balance">
							<span class="sum"><text class="balanceUpdate">{{ Auth::user()->balance }}</text><img style="width: 25px; margin-left: 10px;" src="/templates/img/icon/coins.svg" alt=""></span>
							<span class="icon" onclick="getBalance()">
								<img src="/templates/img/icon/refresh-dark.svg" alt="" class="balanceUpdateBtn" style="animation-duration: 3s; animation-iteration-count: infinite; animation-timing-function: linear;">
							</span>
							<div class="curs">1 BTC = 100 000 <img src="/templates/img/icon/coins.svg" alt=""></div>
						</div>
						<div class="url">
							<a href="#">Пополнение счета</a> \ <a href="#">Снятие средств</a>
						</div>
					</div>
					<div class="info-block two-info">
						<div class="left">
							<div class="title-block">Баланс сайта</div>
							<div class="balance">
								<span class="sum">0 (0,0 BTC)</span>
								<span class="icon">
									<img src="/templates/img/icon/refresh-dark.svg" alt="">
								</span>
							</div>
							<div class="url">
								<a href="#">Пополнение счета</a> \ <a href="#">Снятие средств</a>
							</div>
						</div>
						<div class="right">
							<div class="title-block">Баланс сайта</div>
							<div class="balance">
								<span class="sum">0 (0,0 BTC)</span>
								<span class="icon">
									<img src="/templates/img/icon/refresh-dark.svg" alt="">
								</span>
							</div>
							<div class="url">
								<a href="#">Пополнение счета</a> \ <a href="#">Снятие средств</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection
