		<div class="nav-left">
			<div class="block">
				<div class="title">
					<div class="text">Спорт</div>
					<div class="icon">
						<img src="/templates/img/icon/list-open.svg" alt="">
					</div>
				</div>
				<ul>
					@forelse($categories_left as $item)
						<li>
							<a href="{{ route('category', $item->id ?? '') }}">{{ $item->name ?? ''}}</a>
						</li>
					@empty
					@endforelse
				</ul>
			</div>
			<div class="block">
				<a href="{{ route('historyBets') }}">
					<div class="title">
						<div class="text">Мои рынки</div>
					</div>
				</a>
			</div>
		</div>