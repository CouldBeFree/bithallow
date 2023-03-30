@extends('layouts.index')
@section('title', 'Событие')
@section('content')
@include('includes.navleft')
<div class="content-content">
			<div class="bets">
				<div class="block">
					<div class="tr">
						<div class="title full-bl">
							<span>{{ $data['info']->name }}</span>
							<span>{{ $data['info']->time }} {{ $data['info']->date }}</span>
						</div>
					</div>
				</div>
				<div class="block full-bl info-bl">
					<div class="left">
						<a href="#"><img src="/templates/img/icon/info-dark.svg" alt="">Правила</a>
					</div>
					<div class="right">
						<span>В паре: <b id="sum_field"> {{ $data['info']->sum }}</b></span>
						<div class="icon">
							<img src="/templates/img/icon/coins.svg" alt="" style="margin-left: 10px;">
						</div>
						<div class="icon" onclick="refresh()">
							<img src="/templates/img/icon/refresh-dark.svg" alt="" id="refresh" style="width: 13px; animation-duration: 3s; animation-iteration-count: infinite; animation-timing-function: linear;">
						</div>
					</div>
				</div>
				<div class="block" style="display: flex;">
					<div class="left data-inf" style="width: 50%">
						<table>
							<thead>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td>
										<div class="left center-center">
											<b>“За”</b>
										</div>
									</td>
									<td>
										<div class="right center-center">
											<b>“Против”</b>
										</div>
									</td>
									<td></td>
									<td></td>
								</tr>
							</thead>
							<tbody>
								@foreach(unserialize($data['info']->teams) as $key => $item)
									<tr>
									<td>
										<span id="team_{{ $key + 1 }}">{{ $item }}</span>
										@if(Auth::guest())
										<div>
											<span id="team_{{ $key + 1 }}_field"></span>
											<span class="arrows" style="display: none;"> » </span>
											<span id="team_{{ $key + 1 }}_field_new"></span>
										</div>
										@elseif ($data['profit']['exists'] == false)
										<div>
											<span id="team_{{ $key + 1 }}_field"></span>
											<span class="arrows" style="display: none;"> » </span>
											<span id="team_{{ $key + 1 }}_field_new"></span>
										</div>
										@else
										<div>
											<span id="team_{{ $key + 1 }}_field" style="@if($data['profit'][$key + 1] >= 0) color: #60cc00; @else color: #ff0028; @endif">@if($data['profit'][$key + 1] >= 0)+@endif{{ number_format($data['profit'][$key + 1], 2, '.', '')}}</span>
											<span class="arrows" style="display: none;"> » </span>
											<span id="team_{{ $key + 1 }}_field_new"></span>
										</div>
										@endif
									</td>
									<td>
										<div id="clet_{{ ($key + 1) * 6 - 5 }}" class="clet" onclick="add('back', 'clet_{{ ($key + 1) * 6 - 5 }}', Number('{{ $key + 1 }}'))">
											<b>{{ $data['bets'][($key + 1).'2'][2]['coef'] ?? '' }}</b><br>
											<span>
											{{
												isset($data['bets'][($key + 1).'2'][2]['leftover']) ? (
													($data['bets'][($key + 1).'2'][2]['leftover'] > 999999) ? round($data['bets'][($key + 1).'2'][2]['leftover'] / 1000)."k" : $data['bets'][($key + 1).'2'][2]['leftover']
												) : ''
											}}
											</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
										</div>
									</td>
									<td>
										<div id="clet_{{ ($key + 1) * 6 - 4 }}" class="clet" onclick="add('back', 'clet_{{ ($key + 1) * 6 - 4 }}', Number('{{ $key + 1 }}'))">
											<b>{{ $data['bets'][($key + 1).'2'][1]['coef'] ?? '' }}</b><br>
											<span>
											{{
												isset($data['bets'][($key + 1).'2'][1]['leftover']) ? (
													($data['bets'][($key + 1).'2'][1]['leftover'] > 999999) ? round($data['bets'][($key + 1).'2'][1]['leftover'] / 1000)."k" : $data['bets'][($key + 1).'2'][1]['leftover']
												) : ''
											}}
											</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
										</div>
									</td>
									<td class="top_clet">
										<div id="clet_{{ ($key + 1) * 6 - 3 }}" class="clet" onclick="add('back', 'clet_{{ ($key + 1) * 6 - 3 }}', Number('{{ $key + 1 }}'))">
											<b>{{ $data['bets'][($key + 1).'2'][0]['coef'] ?? '' }}</b><br>
											<span>
											{{
												isset($data['bets'][($key + 1).'2'][0]['leftover']) ? (
													($data['bets'][($key + 1).'2'][0]['leftover'] > 999999) ? round($data['bets'][($key + 1).'2'][0]['leftover'] / 1000)."k" : $data['bets'][($key + 1).'2'][0]['leftover']
												) : ''
											}}
											</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
										</div>
									</td>
									<td class="ttop_clet">
										<div id="clet_{{ ($key + 1) * 6 - 2 }}" class="clet" onclick="add('lay', 'clet_{{ ($key + 1) * 6 - 2 }}', Number('{{ $key + 1 }}'))">
											<b>{{ $data['bets'][($key + 1).'1'][0]['coef'] ?? '' }}</b><br>
											<span>
											{{
												isset($data['bets'][($key + 1).'1'][0]['leftover']) ? (
													($data['bets'][($key + 1).'1'][0]['leftover'] > 999999) ? round($data['bets'][($key + 1).'1'][0]['leftover'] / 1000)."k" : $data['bets'][($key + 1).'1'][0]['leftover']
												) : ''
											}}
											</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
										</div>
									</td>
									<td>
										<div id="clet_{{ ($key + 1) * 6 - 1 }}" class="clet" onclick="add('lay', 'clet_{{ ($key + 1) * 6 - 1 }}', Number('{{ $key + 1 }}'))">
											<b>{{ $data['bets'][($key + 1).'1'][1]['coef'] ?? '' }}</b><br>
											<span>
											{{
												isset($data['bets'][($key + 1).'1'][1]['leftover']) ? (
													($data['bets'][($key + 1).'1'][1]['leftover'] > 999999) ? round($data['bets'][($key + 1).'1'][1]['leftover'] / 1000)."k" : $data['bets'][($key + 1).'1'][1]['leftover']
												) : ''
											}}
											</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
										</div>
									</td>
									<td>
										<div id="clet_{{ ($key + 1) * 6 }}" class="clet" onclick="add('lay', 'clet_{{ ($key + 1) * 6 }}', Number('{{ $key + 1 }}'))">
											<b>{{ $data['bets'][($key + 1).'1'][2]['coef'] ?? '' }}</b><br>
											<span>
											{{
												isset($data['bets'][($key + 1).'1'][2]['leftover']) ? (
													($data['bets'][($key + 1).'1'][2]['leftover'] > 999999) ? round($data['bets'][($key + 1).'1'][2]['leftover'] / 1000)."k" : $data['bets'][($key + 1).'1'][2]['leftover']
												) : ''
											}}
											</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="right" style="width: 45%">	
						<div class="block">	
							<div class="tr">
								<div class="title">Ставки по текущим коэффициентам</div>
							</div>
							<div class="notify" id="notify_top" style="margin: 0px;"></div>
							<div class="open-bets">
								<div class="bl-bl">
									<div>
										<table class="open-tb">
											<thead>
												<tr>
													<td id="new_bets_btn" style="background: rgb(30, 136, 229); cursor: pointer;" onclick="betsToggle('new')">Новые ставки</td>
													<td id="open_bets_btn" style="background: rgb(33, 150, 243); cursor: pointer;" onclick="betsToggle('open')">Открытые ставки</td>
												</tr>
											</thead>
										</table>
										<table class="open-tb open-tab" style="display: none;">
											<thead>
												<tr>
													<td style="background: rgb(30, 136, 229);">Парные ставки</td>
												</tr>
											</thead>
										</table>
										<div class="act-bt" id="open_bets_pair" style="display: none;"></div>
										<table class="open-tb open-tab" style="display: none;">
											<thead>
												<tr>
													<td style="background: rgb(30, 136, 229);">Непарные ставки</td>
												</tr>
											</thead>
										</table>
										<div class="act-bt" id="open_bets_nopair" style="display: none;"></div>
									</div>
								</div>
							</div>
							<div class="bets-inf">
								<div class="bl-bl">
									<table class="blue" id="blue" style="display: none;">
										<thead>
											<tr>
												<td>За</td>
												<td>Коэффициент</td>
												<td>Ставка</td>
												<td>Прибыль</td>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
									<table class="red" id="red" style="display: none;">
										<thead>
											<tr>
												<td>Против</td>
												<td>Коэффициент</td>
												<td>Ставка</td>
												<td>Обязательства</td>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
									<div class="obez" id="obez" style="display: none;">
										Обязательства: <b><span id="obez_plain">0.00</span> (0 BTC)</b>
										<img src="/templates/img/icon/coins.svg" alt="" style="margin-left: 6px;">
									</div>
									<div class="notify" id="notify_bottom"></div>
								</div>
							</div>
							<div class="buttons" id="buttons">
								<div class="btn button" id="closeAll" style="display: none;">Отменить все</div>
								<div class="btn button-a" id="makeBet" style="display: none;">Сделать ставку</div>
								<div class="btn button-a" id="updateBets" style="display: none;">Обновить</div>
							</div>
						</div>
					</div>
				</div>
				@include('includes.notify')
			</div>
		</div>
@endsection
@section('lesspage')
<link rel="stylesheet/less" type="text/css" href="/templates/less/action.less">
@endsection
@section('jspage')
<script src="//cdn.rawgit.com/centrifugal/centrifuge-js/2.4.0/dist/centrifuge.min.js"></script>
<script>
	const centrifuge = new Centrifuge('wss://bithallow.com/centrifugo/connection/websocket')
	const ACTION_ID = '{{ $data['info']->id }}'
	const EXODUS = Number('{{ $data['info']->exodus }}')

	centrifuge.setToken('{{ $cent_token }}')
	centrifuge.subscribe(`${ACTION_ID}_action`, data => {
		refresh_cent(data)
	})
	@if(!Auth::guest())
	centrifuge.subscribe(`${ACTION_ID}_action_${'{{Auth::id()}}'}_user`, data => {
		betHandler(data)
	})
	@endif

	centrifuge.connect()
</script>
<script src="/templates/js/action.js"></script>
@endsection