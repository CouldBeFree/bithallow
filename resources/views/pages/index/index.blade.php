@extends('layouts.index')
@section('title', 'Главная страница')
@section('content')
@include('includes.navleft')
		<div class="content-content">
			@forelse($data as $category)
			<div class="bets">
				<div class="title">
					<table>
						<tr>
							<td>{{ $category['name_category'] ?? '' }}</td>
							<td></td>
							<td>В паре</td>
							<td>1</td>
							<td>X</td>
							<td>2</td>
						</tr>
					</table>
				</div>
				<div class="data">
					<table>
						@forelse($category['info']['actions'] as $key => $item)
						<tr style="cursor: pointer;" onclick="actionLink('{{ $item->id }}')">
							<td>{{ $item->date }}<br>{{ $item->time }}</td>
							<td>{{ $item->name }}</td>
							<td>{{ $item->sum }} <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;"></td>
							<td>
								<div class="left">
									<b>{{ $bets[$key]['12'][0]['coef'] ?? '' }}</b><br>
									<span>
									{{
										isset($bets[$key]['12'][0]['leftover']) ? (
											($bets[$key]['12'][0]['leftover'] > 999999) ? round($bets[$key]['12'][0]['leftover'] / 1000)."k" : $bets[$key]['12'][0]['leftover']
										) : ''
									}}
									</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
								</div>
								<div class="right">
									<b>{{ $bets[$key]['11'][0]['coef'] ?? '' }}</b><br>
									<span>
									{{
										isset($bets[$key]['11'][0]['leftover']) ? (
											($bets[$key]['11'][0]['leftover'] > 999999) ? round($bets[$key]['11'][0]['leftover'] / 1000)."k" : $bets[$key]['11'][0]['leftover']
										) : ''
									}}
									</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
								</div>
							</td>
							@if($item->exodus == 2)
							<td style="opacity: 0.3">
								<div class="left">
									<b></b><br>
									<span></span>
								</div>
								<div class="right">
									<b></b><br>
									<span></span>
								</div>
							</td>
							@else
							<td>
								<div class="left">
									<b>{{ $bets[$key]['32'][0]['coef'] ?? '' }}</b><br>
									<span>
									{{
										isset($bets[$key]['32'][0]['leftover']) ? (
											($bets[$key]['32'][0]['leftover'] > 999999) ? round($bets[$key]['32'][0]['leftover'] / 1000)."k" : $bets[$key]['32'][0]['leftover']
										) : ''
									}}
									</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
								</div>
								<div class="right">
									<b>{{ $bets[$key]['31'][0]['coef'] ?? '' }}</b><br>
									<span>
									{{
										isset($bets[$key]['31'][0]['leftover']) ? (
											($bets[$key]['31'][0]['leftover'] > 999999) ? round($bets[$key]['31'][0]['leftover'] / 1000)."k" : $bets[$key]['31'][0]['leftover']
										) : ''
									}}
									</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
								</div>
							</td>
							@endif
							<td>
								<div class="left">
									<b>{{ $bets[$key]['22'][0]['coef'] ?? '' }}</b><br>
									<span>
									{{
										isset($bets[$key]['22'][0]['leftover']) ? (
											($bets[$key]['22'][0]['leftover'] > 999999) ? round($bets[$key]['22'][0]['leftover'] / 1000)."k" : $bets[$key]['22'][0]['leftover']
										) : ''
									}}
									</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
								</div>
								<div class="right">
									<b>{{ $bets[$key]['21'][0]['coef'] ?? '' }}</b><br>
									<span>
									{{
										isset($bets[$key]['21'][0]['leftover']) ? (
											($bets[$key]['21'][0]['leftover'] > 999999) ? round($bets[$key]['21'][0]['leftover'] / 1000)."k" : $bets[$key]['21'][0]['leftover']
										) : ''
									}}
									</span> <img src="/templates/img/icon/coins.svg" alt="" style="width: 10px;">
								</div>
							</td>
						</tr>
						@empty
						@endforelse
					</table>
				</div>		
			</div>
			@empty
			@endforelse
			@if(session('message'))
			<div class="text-info success">{{ session('message') }}</div>
			@elseif($errors->any())
				@foreach ($errors->all() as $error)
	                <div class="text-info error">{{ $error }}</div>
	            @endforeach
			@endif
			@include('includes.notify')
		</div>
@endsection