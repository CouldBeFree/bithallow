@extends('layouts.my')
@section('title', 'Личные данные')
@section('content')
		<div class="content-content">
			<div class="personal-date full-bl">
				<div class="block">
					<div class="tr">
						<div class="title">
							<span>Личные данные</span>
							<span class="edit" id="editBtn">Изменить</span>
						</div>
					</div>
					<div class="info-block info-noinput-block" id="keep">
						<div class="line">
							<div class="text">
								<b>Имя: </b>{{ Auth::user()->name }}
							</div>
						</div>
						<div class="line">
							<div class="text">
								<b>Логин: </b>{{ Auth::user()->login }}
							</div>
						</div>
						<div class="line">
							<div class="text">
								<b>Электронная почта: </b>{{ Auth::user()->email }}
							</div>
						</div>
						<div class="line">
							<div class="text">
								<b>Номер телефона: </b>+{{ Auth::user()->number }}
							</div>
						</div> 
					</div>
					<div class="info-block input-block" id="edit" style="display: none;">
						<form action="{{ route('editdate') }}" method="POST">
							{{ csrf_field() }}
							<div class="line">
								<div class="text">
									<b>Электронная почта: </b><input name="email" type="email" value="{{ Auth::user()->email }}" required>
								</div>
							</div>
							<div class="line">
								<div class="text">
									<b>Номер телефона: </b>+<input name="number" type="number" value="{{ Auth::user()->number }}" required>
								</div>
							</div>
							<button class="save-btn">Сохранить</button>					
						</form>
					</div>
				</div>
				<!-- <div class="block">
					<div class="tr">
						<div class="title">
							<span>Часовой пояс</span>
							<span class="edit">Изменить</span>
						</div>
					</div>
					<div class="info-block">
						<div class="line">
							<div class="text">
								<b>Предпочтительный часовой пояс: </b>EET
							</div>
						</div>
					</div>
				</div> -->
				@if(session('message'))
				<div class="text-info success">{{ session('message') }}</div>
				@elseif($errors->any())
					@foreach ($errors->all() as $error)
		                <div class="text-info error">{{ $error }}</div>
		            @endforeach
				@endif
				</div>
		</div>
@endsection
@section('jspage')
<script>
	const editBtn = document.getElementById("editBtn")
	const edit = document.getElementById("edit")
	const keep = document.getElementById("keep")
	function editInfo() {
		switch(edit.style.display) {
			case "none":
				editBtn.textContent = "Отмена"
				edit.style.display = "block"
				keep.style.display = "none"
				break
		case "block":
			editBtn.textContent = "Изменить"
			edit.style.display = "none"
			keep.style.display = "block"
			break
		}
	}
	editBtn.addEventListener('click', editInfo)
</script>
@endsection