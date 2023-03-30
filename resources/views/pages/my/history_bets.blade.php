@extends('layouts.my')
@section('title', 'История ставок')
@section('content')
<div class="content-content">
	<div class="history-bets full-bl">
		<div class="block">
			<div class="tr">
				<div class="title">
					<span>Ваши ставки за период <b>21.09.2019 - 13.12.2019</b></span>
					<span class="edit">Изменить <img src="/templates/img/icon/calendar.svg" alt=""></span>
				</div>
			</div>
			<div class="info-block">
				<div class="table">
					<table>
						<thead>
							<tr>
								<th>Размещено</th>
								<th>Описание</th>
								<th>Коэффициент</th>
								<th>Размер ставки</th>
								<th>Потенциальный Возврат</th>
								<th>Статус</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div class="no-info">В этом временном периоде у Вас нет ставок.</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection