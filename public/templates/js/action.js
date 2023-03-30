/* notify - уведомления над и под меню ставок */
const notify_top = document.getElementById("notify_top")
const notify_bottom = document.getElementById("notify_bottom")
const obez = document.getElementById("obez")
const red = document.getElementById("red")
const blue = document.getElementById("blue")
const redBody = red.querySelector("tbody")
const blueBody = blue.querySelector("tbody")
/* Кнопки управления ставками */
const closeAllBtn = document.getElementById("closeAll")
const makeBetBtn = document.getElementById("makeBet")
const updateBetsBtn = document.getElementById("updateBets")
/* Элементы с открытыми ставками */
const open_bets_pair = document.getElementById("open_bets_pair")
const open_bets_nopair = document.getElementById("open_bets_nopair")
/* Управление меню ставок */
const new_bets_btn = document.getElementById("new_bets_btn")
const open_bets_btn = document.getElementById("open_bets_btn")
const open_tabs = document.getElementsByClassName("open-tab")
/* sum_field - в паре */
const sum_field = document.getElementById("sum_field")
/* profit_exists - bool, было ли значение в поле profit в момент добавления первой ставки */
let profit_exists = false
/* clet - клетки со ставками */
let clet = []
/* allowedBets - массив всех возможных ставок, например (1, 1) или (2, 2), т.е. массив объектов типа bet */
let allowedBets = []
/* bets - массив выбранных пользователем исходов, т.е. состоящий из элементов allowedBets */
let bets = []
/* Заполнение allowedBets */
for (let i = 0; i < 3; i++) {
	allowedBets.push([])
	for (let j = 0; j < 3; j++)
		allowedBets[i].push(new bet(i+1, j+1))
}
/* Проверка profit_exists */
for (let i = 1; i <= EXODUS; i++) {
	if (document.getElementById(`team_${i}_field`).textContent != "") profit_exists = true
}
/* Заполнение clet */
for (let i = 1; i <= 6 * EXODUS; i++) {
	clet.push(document.getElementById(`clet_${i}`))
}

closeAllBtn.addEventListener('click', closeAll)
makeBetBtn.addEventListener('click', makeBet)

function bet(move, team) {
	this.move = move
	this.team = team
}

function refresh() {
	let btn = document.getElementById("refresh")
	if (btn.style.cursor == "not-allowed") return
	let url = `${INFO.domain}/ajax/actioninfo/${ACTION_ID}`
	let req = new Request(url, {
		method: 'POST',
		mode: 'cors',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': `${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`
		}
	})
	fetch(req)
		.then(response => {
			if (response.ok) {
				return response.json()
			} else {
				switch(INFO.lang) {
					case "ru":
						showNotify("red-notify", "Не удалось обновить информацию об игре")
						break
					case "en":
						showNotify("red-notify", "Failed to update game info")
						break
				}
			}
		})
		.then(data => {
			if (data.success) {
				let json = JSON.parse(data.data)
				sum_field.textContent = json.action.sum
				for (let i = 1; i <= 6 * EXODUS; i++) {
					let place = Math.ceil(i / 6)
					let foo = i % 6
					if (foo == 0) foo = 6

					if (foo > 3) {
						place += "1"
						foo = foo - 4
					} else {
						place += "2"
						foo = 3 - foo
					}

					let leftover
					if (json.bets[place][foo] != undefined) {
						leftover = json.bets[place][foo].leftover
						if (leftover > 999999) leftover = `${Math.round(leftover / 1000)}k`
					}

					clet[i-1].querySelector("b").textContent = (json.bets[place][foo] != undefined) ? json.bets[place][foo].coef : ""
					clet[i-1].querySelector("span").textContent = (leftover != undefined) ? leftover : ""
				}
				btn.style.opacity = "0.5"
				btn.style.cursor = "not-allowed"
				btn.style.animationName = "rotation"
				setTimeout(() => { btn.style.opacity = "1"; btn.style.cursor = "pointer"; btn.style.animationName = "" }, 3000);
			}
		})
		.catch(err => {
			console.log(err.message)
		})
}

function refresh_cent(data) {
	if (data.data.original.success) {
		let json = JSON.parse(data.data.original.data)
		sum_field.textContent = json.action.sum
		for (let i = 1; i <= 6 * EXODUS; i++) {
			let place = Math.ceil(i / 6)
			let foo = i % 6
			if (foo == 0) foo = 6

			if (foo > 3) {
				place += "1"
				foo = foo - 4
			} else {
				place += "2"
				foo = 3 - foo
			}

			let leftover
			if (json.bets[place][foo] != undefined) {
				leftover = json.bets[place][foo].leftover
				if (leftover > 999999) leftover = `${Math.round(leftover / 1000)}k`
			}

			clet[i-1].querySelector("b").textContent = (json.bets[place][foo] != undefined) ? json.bets[place][foo].coef : ""
			clet[i-1].querySelector("span").textContent = (leftover != undefined) ? leftover : ""
		}
	} else {
		switch(INFO.lang) {
			case "ru":
				showNotify("red-notify", "Не удалось обновить информацию об игре")
				break
			case "en":
				showNotify("red-notify", "Failed to update game info")
				break
		}
	}
}

function getOpenBets() {
	if (!AUTH) return
	let url = `${INFO.domain}/ajax/betsinfo`
	let data = {
		id: ACTION_ID
	}
	let req = new Request(url, {
		method: 'POST',
		mode: 'cors',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': `${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`
		},
		body: JSON.stringify(data)
	})
	fetch(req)
		.then(response => {
			if (response.ok) {
				return response.json()
			} else {
				switch(INFO.lang) {
					case "ru":
						showNotify("red-notify", "Не удалось получить открытые ставки")
						break
					case "en":
						showNotify("red-notify", "Failed to get open bets")
						break
				}
			}
		})
		.then(data => {
			if (data.success) {
				let content = JSON.parse(data.data)
				open_bets_pair.innerHTML = ""
				open_bets_nopair.innerHTML = ""
				for (let bets in content['pair']) {
					for (let bet in content['pair'][bets]) {
						let element = document.createElement("div")
						let langFirst, langSecond, langThird, type
						let profitOrObez = (content['pair'][bets][bet].coef * content['pair'][bets][bet].sum - content['pair'][bets][bet].sum).toFixed(2)
						let betClass = (content['pair'][bets][bet].move == 1) ? "blue" : "red"
						switch(INFO.lang) {
							case "ru":
								langFirst = (content['pair'][bets][bet].move == 1) ? "За" : "Против"
								langSecond = "Коэффициент"
								langThird = "Ставка"
								type = (content['pair'][bets][bet].move == 1) ? "Прибыль" : "Обязательства"
								break
							case "en":
								langFirst = (content['pair'][bets][bet].move == 1) ? "Back" : "Lay"
								langSecond = "Coefficient"
								langThird = "Bet"
								type = (content['pair'][bets][bet].move == 1) ? "Profit" : "Obligations"
								break
						}
						let html = `
						<div class="bet-id">
							<table class="${betClass}">
								<thead>
									<tr>
										<td>${langFirst}</td>
										<td>${langSecond}</td>
										<td>${langThird}</td>
										<td>${type}</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>${content['pair'][bets][bet].team}</td>
										<td>${content['pair'][bets][bet].coef}</td>
										<td>${content['pair'][bets][bet].sum}</td>
										<td>${profitOrObez}</td>
									</tr>
								</tbody>
							</table>
						</div>`
						element.innerHTML = html
						open_bets_pair.appendChild(element)
					}
				}
				for (let bets in content['nopair']) {
					for (let bet in content['nopair'][bets]) {
						let element = document.createElement("div")
						let langFirst, langSecond, langThird, type
						let profitOrObez = (content['nopair'][bets][bet].coef * content['nopair'][bets][bet].sum - content['nopair'][bets][bet].sum).toFixed(2)
						let betClass = (content['nopair'][bets][bet].move == 1) ? "blue" : "red"
						switch(INFO.lang) {
							case "ru":
								langFirst = (content['nopair'][bets][bet].move == 1) ? "За" : "Против"
								langSecond = "Коэффициент"
								langThird = "Ставка"
								type = (content['nopair'][bets][bet].move == 1) ? "Прибыль" : "Обязательства"
								break
							case "en":
								langFirst = (content['nopair'][bets][bet].move == 1) ? "Back" : "Lay"
								langSecond = "Coefficient"
								langThird = "Bet"
								type = (content['nopair'][bets][bet].move == 1) ? "Profit" : "Obligations"
								break
						}
						let html = `
						<div class="bet-id">
							<table class="${betClass}">
								<thead>
									<tr>
										<td>${langFirst}</td>
										<td>${langSecond}</td>
										<td>${langThird}</td>
										<td>${type}</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>${content['nopair'][bets][bet].team}</td>
										<td><input type="text" step="0.01" value="${content['nopair'][bets][bet].coef}"></td>
										<td><input type="text" step="1.0" value="${content['nopair'][bets][bet].sum}"></td>
										<td>${profitOrObez}</td>
									</tr>
								</tbody>
							</table>
						</div>`
						element.innerHTML = html
						open_bets_nopair.appendChild(element)
					}
				}
			}
		})
		.catch(err => {
			console.log(err.message)
		})
}
getOpenBets()

function betsToggle(id) {
	switch(id) {
		case "new":
			/* Меняем цвет кнопок */
			new_bets_btn.style.background = "rgb(30, 136, 229)"
			open_bets_btn.style.background = "rgb(33, 150, 243)"
			if (AUTH) {
				/* Скрываем открытые ставки и кнопку обновления */
				open_bets_pair.style.display = "none"
				open_bets_nopair.style.display = "none"
				updateBetsBtn.style.display = "none"
				/* Скрываем заголовки открытых ставок */
				open_tabs[0].style.display = "none"
				open_tabs[1].style.display = "none"
				/* Если выбрана хоть одна новая ставка, выводим обязательства и кнопки */
				if (redBody.children.length > 0 || blueBody.children.length > 0) {
					obez.style.display = ""
					closeAllBtn.style.display = ""
					makeBetBtn.style.display = ""
				}
				/* Выводим новые ставки */
				if (redBody.children.length > 0) {
					red.style.display = ""
				}
				if (blueBody.children.length > 0) {
					blue.style.display = ""
				}
			}
			break
		case "open":
			/* Меняем цвет кнопок */
			new_bets_btn.style.background = "rgb(33, 150, 243)"
			open_bets_btn.style.background = "rgb(30, 136, 229)"
			if (AUTH) {
				/* Выводим открытые ставки и кнопку обновления */
				open_bets_pair.style.display = ""
				open_bets_nopair.style.display = ""
				if (open_bets_nopair.children.length > 0) updateBetsBtn.style.display = ""
				/* Выводим заголовки открытых ставок */
				open_tabs[0].style.display = ""
				open_tabs[1].style.display = ""
				/* Если выбрана хоть одна новая ставка, скрываем обязательства и кнопки */
				if (redBody.children.length > 0 || blueBody.children.length > 0) {
					obez.style.display = "none"
					closeAllBtn.style.display = "none"
					makeBetBtn.style.display = "none"
				}
				/* Скрываем новые ставки */
				if (redBody.children.length > 0) {
					red.style.display = "none"
				}
				if (blueBody.children.length > 0) {
					blue.style.display = "none"
				}
			}
			break
	}
}

function add(event, btnId, team) {
	if (!AUTH) return
	/* data - вложенный HTML добавляемой ставки */
	let data = "<td>"
	/* element - HTML элемент добавляемой ставки */
	let element = document.createElement("tr")
	/* btn - кнопка, которая была нажата */
	let btn = document.getElementById(btnId)
	/* children - является массивом потомков, среди которых ищется elementId */
	let children
	/* elementId - служит для проверки существования выбранной ставки (если элемент с выбранным исходом уже существует, вместо создания нового требуется либо удалить элемент, либо поменять у него коэф) */
	let elementId
	/* obj - сюда попадает нужный исход из allowedBets (потом он либо удаляется из bets, либо добавляется) */
	let obj
	/* num - минимальный коэф (INFO.minCoef) */
	let num = Number(btn.querySelector("b").textContent)
	if (num < INFO.minCoef)
		num = INFO.minCoef
	/* Выводим обязательства и кнопки */
	obez.style.display = ""
	closeAllBtn.style.display = ""
	makeBetBtn.style.display = ""
	/* Выводим новые ставки */
	if (redBody.children.length > 0) {
		red.style.display = ""
	}
	if (blueBody.children.length > 0) {
		blue.style.display = ""
	}
	/* Скрываем открытые ставки */
	if (open_bets_btn.style.background == "rgb(30, 136, 229)") {
		/* Меняем цвет кнопок */
		new_bets_btn.style.background = "rgb(30, 136, 229)"
		open_bets_btn.style.background = "rgb(33, 150, 243)"
		/* Скрываем открытые ставки и кнопку обновления */
		open_bets_pair.style.display = "none"
		open_bets_nopair.style.display = "none"
		updateBetsBtn.style.display = "none"
		/* Скрываем заголовки открытых ставок */
		open_tabs[0].style.display = "none"
		open_tabs[1].style.display = "none"
	}
	switch(event) {
		/* lay - против */
		case "lay":
			red.style.display = "table"
			children = redBody.children
			obj = allowedBets[1][team-1]
			clet[6 * team - 3].className = "click-right"
			/* Заполняем elementId, если выбранный исход был добавлен ранее */
			for (let i = 0, child; child = children[i]; i++) {
				if (child.getAttribute('id') == `lay_team_${team}`) {
					elementId = `lay_team_${team}`
					break
				}
			}
			/* Если выбранного исхода не было, т.е. требуется добавить ставку в bets */
			if (elementId == undefined) {
				let content = document.getElementById(`team_${team}`).textContent
				bets.push(obj)
				data += `${content}</td>`
				element.setAttribute("id", `lay_team_${team}`)
			}
			/* Если исход уже был выбран, следует либо удалить элемент, либо заменить коэф */
			if (elementId != undefined) {
				element = document.getElementById(elementId)
				let inputField = element.querySelector("input")
				let coefPrev = Number(inputField.value)
				let coefNew = num
				/* Если коэф равны, удаляем элемент и удаляем исход из bets */
				if (coefPrev == coefNew) {
					let index = bets.indexOf(obj)
					bets.splice(index, 1)
					redBody.removeChild(element)
					if (children.length == 0) {
						red.style.display = "none"
						children = blueBody.children
						if (children.length == 0) {
							obez.style.display = "none"
							closeAllBtn.style.display = "none"
							makeBetBtn.style.display = "none"
						}
					}
					clet[6 * team - 3].className = "clet"
					calcObez()
				} else {
					/* Иначе меняем коэф у ставки на коэф выбранного элемента */
					let spl = inputField.id.split("_")
					inputField.value = coefNew
					calcProfitOrObez(`${spl[0]}_${spl[1]}`)
				}
			} else {
				data += `<td><input id="${`lay_${children.length}_coef`}" class="input-validate-coef" type="text" step="0.01" value="${num}" oninput="calcProfitOrObez('${`lay_${children.length}`}')"></td>`
				data += `<td><input id="${`lay_${children.length}_bet`}" class="input-validate-bet" type="text" step="1.0" value="${INFO.minBet}" oninput="calcProfitOrObez('${`lay_${children.length}`}')"></td>`
				data += `<td><text id="${`lay_${children.length}`}" class="obezField_2">0.00</text><img src="/templates/img/icon/coins.svg" alt=""></td>`
				/* Добавляем элемент в tbody */
				element.innerHTML = data
				redBody.appendChild(element)
				/* Считаем выручку сразу */
				calcProfitOrObez(`lay_${children.length - 1}`)
			}
			break
		/* back - за */
		case "back":
			blue.style.display = "table"
			children = blueBody.children
			obj = allowedBets[0][team-1]
			clet[6 * team - 4].className = "click-right"
			/* Заполняем elementId, если выбранный исход был добавлен ранее */
			for (let i = 0, child; child = children[i]; i++) {
				if (child.getAttribute('id') == `back_team_${team}`) {
					elementId = `back_team_${team}`
					break
				}
			}
			/* Если выбранного исхода не было, т.е. требуется добавить ставку в bets */
			if (elementId == undefined) {
				let content = document.getElementById(`team_${team}`).textContent
				bets.push(obj)
				data += `${content}</td>`
				element.setAttribute("id", `back_team_${team}`)
			}
			/* Если исход уже был выбран, следует либо удалить элемент, либо заменить коэф */
			if (elementId != undefined) {
				element = document.getElementById(elementId)
				let inputField = element.querySelector("input")
				let coefPrev = Number(inputField.value)
				let coefNew = num
				/* Если коэф равны, удаляем элемент и удаляем исход из bets */
				if (coefPrev == coefNew) {
					let index = bets.indexOf(obj)
					bets.splice(index, 1)
					blueBody.removeChild(element)
					if (children.length == 0) {
						blue.style.display = "none"
						children = redBody.children
						if (children.length == 0) {
							obez.style.display = "none"
							closeAllBtn.style.display = "none"
							makeBetBtn.style.display = "none"
						}
					}
					clet[6 * team - 4].className = "clet"
					calcObez()
				} else {
					/* Иначе меняем коэф у ставки на коэф выбранного элемента */
					let spl = inputField.id.split("_")
					inputField.value = coefNew
					calcProfitOrObez(`${spl[0]}_${spl[1]}`)
				}
			} else {
				data += `<td><input id="${`back_${children.length}_coef`}" class="input-validate-coef" type="text" step="0.01" value="${num}" oninput="calcProfitOrObez('${`back_${children.length}`}')"></td>`
				data += `<td><input id="${`back_${children.length}_bet`}" class="obezField_1 input-validate-bet" type="text" step="1.0" value="${INFO.minBet}" oninput="calcProfitOrObez('${`back_${children.length}`}')"></td>`
				data += `<td><text id="${`back_${children.length}`}">0.00</text><img src="/templates/img/icon/coins.svg" alt=""></td>`
				/* Добавляем элемент в tbody */
				element.innerHTML = data
				blueBody.appendChild(element)
				/* Считаем выручку сразу */
				calcProfitOrObez(`back_${children.length - 1}`)
			}
			break
	}
}

function validate() {
	let result = []
	let back = []
	let lay = []
	let field_new
	let arrows = document.getElementsByClassName("arrows")
	let betBtn = document.getElementById("makeBet")
	let oldNotify = notify_top.querySelector("div")

	notify_top.style.margin = "0"
	if (oldNotify != null) {
		notify_top.removeChild(oldNotify)
	}

	if (bets.length == 0) {
		for (let i = 1; i <= EXODUS; i++) {
			if (profit_exists) {
				document.getElementById(`team_${i}_field_new`).textContent = ""
				arrows[i-1].style.display = "none"
			} else {
				document.getElementById(`team_${i}_field`).textContent = ""
			}
		}
	} else {
		for (let i = 1; i <= EXODUS; i++) {
			if (profit_exists) {
				result.push(Number(document.getElementById(`team_${i}_field`).textContent))
				arrows[i-1].style.display = ""
				field_new = `_new`
			} else {
				result.push(0)
				arrows[i-1].style.display = "none"
				field_new = ``
			}
			back.push(document.getElementById(`back_team_${i}`))
			lay.push(document.getElementById(`lay_team_${i}`))
		}

		for (let index in back) {
			if (back[index] == null) continue
			let inputs_back = back[index].getElementsByTagName("input")

			if (lay[index] != null) {
				let inputs_lay = lay[index].getElementsByTagName("input")
				if (Number(inputs_back[0].value) <= Number(inputs_lay[0].value)) {
					let msg
					betBtn.style.opacity = 0.5
					betBtn.style.cursor = "not-allowed"
					notify_top.style.margin = "10px 0"
					switch(INFO.lang) {
						case "ru":
							msg = "Коэффициент ставки За не может быть меньше или равным коэффициенту Против"
							break
						case "en":
							msg = "Back odds cannot be less or the same as Lay odds"
							break
					}
					for (let i = 1; i <= EXODUS; i++) {
						if (profit_exists) {
							document.getElementById(`team_${i}_field_new`).textContent = ""
							arrows[i-1].style.display = "none"
						} else {
							document.getElementById(`team_${i}_field`).textContent = ""
						}
					}
					return {success: false, message: msg}
				}
			}

			for (let i in result) {
				if (index == i) {
					result[i] += Number(back[index].querySelector("text").textContent)
				} else {
					result[i] -= Number(inputs_back[1].value)
				}
			}
		}

		for (let index in lay) {
			if (lay[index] == null) continue
			let inputs = lay[index].getElementsByTagName("input")

			for (let i in result) {
				if (index == i) {
					result[i] -= Number(lay[index].querySelector("text").textContent)
				} else {
					result[i] += Number(inputs[1].value)
				}
			}
		}

		if (calcCommission == true) {
			result.forEach(res => {
				res = (res > 0) ? (res * ((100 - INFO.commission) / 100)) : res
			})
		}

		for (let i = 1; i <= EXODUS; i++) {
			let el = document.getElementById(`team_${i}_field${field_new}`)
			el.style.color = (result[i-1] < 0) ? "#ff0028" : "#60cc00"
			el.textContent = (result[i-1] < 0) ? result[i-1].toFixed(2) : `+${result[i-1].toFixed(2)}`
		}
	}

	betBtn.style.opacity = 1
	betBtn.style.cursor = "pointer"

	return {success: true, message: ""}
}

function inputValidate() {
	let inputsBet = document.getElementsByClassName("input-validate-bet")
	let inputsCoef = document.getElementsByClassName("input-validate-coef")
	let result
	/* Убираем знак минуса */
	for (obj of inputsBet) {
		obj.value = obj.value.replace(/\,/g, '.').replace(/[^.\d]/g, '')
		let [str_1, str_2] = obj.value.split('.', 2)
		if (str_2 != undefined) {
			str_2 = str_2.replace(/[.,]/g, '').slice(0, 2)
			result = `${str_1}.${str_2}`
		} else {
			result = str_1
		}
		if (!Number(result)) result = ""
		obj.value = result
	}
	for (obj of inputsCoef) {
		obj.value = obj.value.replace(/\,/g, '.').replace(/[^.\d]/g, '')
		let [str_1, str_2] = obj.value.split('.', 2)
		if (str_2 != undefined) {
			if (EXODUS == 2) {
				str_2 = (Number(str_1) == 1) ? str_2.replace(/[.,]/g, '').slice(0, 2) : str_2.replace(/[.,]/g, '').slice(0, 4)
			} else {
				str_2 = str_2.replace(/[.,]/g, '').slice(0, 2)
			}
			result = `${str_1}.${str_2}`
		} else {
			result = str_1
		}
		if (!Number(result)) result = ""
		obj.value = result

		if (Number(obj.value) > INFO.maxCoef) obj.value = INFO.maxCoef
	}
}

function calcProfitOrObez(id) {
	inputValidate()
	let thirdField = document.getElementById(id)
	let coefField = document.getElementById(`${id}_coef`)
	let betField = document.getElementById(`${id}_bet`)
	let number = Number((Number(coefField.value) * Number(betField.value) - Number(betField.value)).toFixed(2))
	if (number < 0) number = 0
	thirdField.textContent = number
	calcObez()
}

function calcObez() {
	let obezFields_1 = document.getElementsByClassName("obezField_1")
	let obezFields_2 = document.getElementsByClassName("obezField_2")
	let obezFieldPlain = document.getElementById("obez_plain")
	let value = 0
	for (let i = 0, obez; obez = obezFields_1[i]; i++) {
		value += Number(obez.value)
	}
	for (let i = 0, obez; obez = obezFields_2[i]; i++) {
		value += Number(obez.textContent)
	}
	value = value.toFixed(2)
	obezFieldPlain.textContent = value
	let result = validate()
	if (!result.success) {
		let newNotify = document.createElement("div")
		newNotify.className = "red-notify"
		newNotify.textContent = result.message
		notify_top.appendChild(newNotify)
	}
	return value
}

function updateProfit() {
	let url = `${INFO.domain}/ajax/profitinfo`
	let data = {
		id: ACTION_ID
	}
	let req = new Request(url, {
		method: 'POST',
		mode: 'cors',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': `${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`
		},
		body: JSON.stringify(data)
	})
	fetch(req)
		.then(response => {
			if (response.ok) {
				return response.json()
			} else {
				switch(INFO.lang) {
					case "ru":
						showNotify("red-notify", "Не удалось обновить информацию на странице")
						break
					case "en":
						showNotify("red-notify", "Failed to update page information")
						break
				}
			}
		})
		.then(data => {
			if (data.success) {
				let profit = JSON.parse(data.data)
				let arrows = document.getElementsByClassName("arrows")
				for (let i = 1; i <= EXODUS; i++) {
					let el = document.getElementById(`team_${i}_field`)
					document.getElementById(`team_${i}_field_new`).textContent = ""
					arrows[i-1].style.display = "none"
					if (profit[i] == 0) {
						if (profit["exists"]) {
							el.style.color = "#60cc00"
							el.textContent = "+0.00"
						} else {
							el.style.color = ""
						}
						continue
					}
					el.style.color = (profit[i] < 0) ? "#ff0028" : "#60cc00"
					el.textContent = (profit[i] < 0) ? profit[i].toFixed(2) : `+${profit[i].toFixed(2)}`
					/* Проверка profit_exists */
					if (document.getElementById(`team_${i}_field`).textContent != "") profit_exists = true
				}
			}
		})
		.catch(err => {
			console.log(err.message)
		})
}

function closeAll() {
	let obezFieldPlain = document.getElementById("obez_plain")
	let oldNotify = notify_top.querySelector("div")
	clearBets()
	validate()
	notify_top.style.margin = "0"
	if (oldNotify != null) {
		notify_top.removeChild(oldNotify)
	}
	for (let i = 1; i <= EXODUS; i++) {
		clet[6 * i - 3].className = "clet"
		clet[6 * i - 4].className = "clet"
	}
	obezFieldPlain.textContent = 0
	redBody.innerHTML = ""
	blueBody.innerHTML = ""
	obez.style.display = "none"
	red.style.display = "none"
	blue.style.display = "none"
	closeAllBtn.style.display = "none"
	makeBetBtn.style.display = "none"
}

function closeOne(move, team) {
	move = (move == 1) ? "back" : "lay"
	let bet = document.getElementById(`${move}_team_${team}`)
	switch(move) {
		case "lay":
			bets.splice(bets.indexOf(allowedBets[2][team]), 1)
			redBody.removeChild(bet)
			if (redBody.querySelector("tr") == null) {
				red.style.display = "none"
			}
			clet[6 * team - 3].className = "clet"
			break
		case "back":
			bets.splice(bets.indexOf(allowedBets[1][team]), 1)
			blueBody.removeChild(bet)
			if (blueBody.querySelector("tr") == null) {
				blue.style.display = "none"
			}
			clet[6 * team - 4].className = "clet"
			break
	}
	if (redBody.children.length == 0 && blueBody.children.length == 0) {
		document.getElementById("obez_plain").textContent = 0
		obez.style.display = "none"
		closeAllBtn.style.display = "none"
		makeBetBtn.style.display = "none"
		validate()
	} else {
		calcObez()
	}
}

function clearBets() {
	bets.forEach(bet => delete bet)
	bets.length = 0
}

function closeNotify() {
	notify_bottom.removeChild(notify_bottom.querySelector("div"))
}

function showNotify(type, message, data = undefined) {
	let newNotify = document.createElement("div")
	newNotify.className = type
	newNotify.textContent = (data == undefined) ? message : `${message}${data}`
	if (data == undefined) {
		newNotify.textContent = message
	} else {
		let json = JSON.parse(data)
		let inputsCoef = document.getElementsByClassName("input-validate-coef")
		message = message.replace("{ph1}", json.new)
		for (obj of inputsCoef) {
			if (obj.value == json.original) {
				obj.value = json.new
				calcProfitOrObez(obj.id.replace("_coef", ""))
			}
		}
		newNotify.textContent = message
	}
	notify_bottom.appendChild(newNotify)
	setTimeout(closeNotify, 4000)
}

function makeBet() {
	if (!AUTH) return
	if (EXODUS != 2) {
		let msg
		switch(INFO.lang) {
			case "ru":
				msg = "Один шаг прироста для коэффициентов от {ph1} до {ph2} должен равняться {ph3}. Ваши коэффициенты были обновлены."
				break
			case "en":
				msg = "One increment step for coefficients from {ph1} to {ph2} should equal {ph3}. Your coefficients have been updated."
				break
		}
		let inputsCoef = document.getElementsByClassName("input-validate-coef")
		let check = true
		for (obj of inputsCoef) {
			if (obj.value <= 2) continue
			if (Number(obj.value) < 3 && (Number(obj.value) % 0.02) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 2)
				local_msg = local_msg.replace("{ph2}", 3)
				local_msg = local_msg.replace("{ph3}", 0.02)
				obj.value = (Number(obj.value) + 0.01).toFixed(2)
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) < 4 && (Number(obj.value) % 0.05) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 3)
				local_msg = local_msg.replace("{ph2}", 4)
				local_msg = local_msg.replace("{ph3}", 0.05)
				let last_num = (`${obj.value}`).split(".")[1].slice(1, 2)
				obj.value = (Number(obj.value) + ((5 - last_num) / 100)).toFixed(2)
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) < 6 && (Number(obj.value) % 0.1) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 4)
				local_msg = local_msg.replace("{ph2}", 6)
				local_msg = local_msg.replace("{ph3}", 0.1)
				let last_num = (`${obj.value}`).split(".")[1].slice(1, 2)
				obj.value = (Number(obj.value) + ((10 - last_num) / 100)).toFixed(2)
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) < 10 && (Number(obj.value) % 0.2) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 6)
				local_msg = local_msg.replace("{ph2}", 10)
				local_msg = local_msg.replace("{ph3}", 0.2)
				let first_num = (`${obj.value}`).split(".")[1].slice(0, 1)
				let last_num = (`${obj.value}`).split(".")[1].slice(1, 2)
				let value
				if (last_num == "") {
					value = (Number(obj.value) + 0.1).toFixed(2)
				} else {
					value = (first_num % 2 == 0) ? (Number(obj.value) + 0.1).toFixed(2) : obj.value
					value = (Number(value) + ((10 - last_num) / 100)).toFixed(2)
				}
				obj.value = value
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) < 20 && (Number(obj.value) % 0.5) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 10)
				local_msg = local_msg.replace("{ph2}", 20)
				local_msg = local_msg.replace("{ph3}", 0.5)
				let first_num = (`${obj.value}`).split(".")[1].slice(0, 1)
				let last_num = (`${obj.value}`).split(".")[1].slice(1, 2)
				let num = (last_num == "") ? Number(`${first_num}0`) : Number(`${first_num}${last_num}`)
				obj.value = (Number(obj.value) + ((50 - num) / 100)).toFixed(2)
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) < 30 && (Number(obj.value) % 1) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 20)
				local_msg = local_msg.replace("{ph2}", 30)
				local_msg = local_msg.replace("{ph3}", 1)
				obj.value = Math.ceil(Number(obj.value))
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) < 50 && (Number(obj.value) % 2) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 30)
				local_msg = local_msg.replace("{ph2}", 50)
				local_msg = local_msg.replace("{ph3}", 2)
				let value = Math.ceil(Number(obj.value))
				if (Number(value) % 2 != 0) value = Number(value) + 1
				obj.value = value
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) < 100 && (Number(obj.value) % 10) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 50)
				local_msg = local_msg.replace("{ph2}", 100)
				local_msg = local_msg.replace("{ph3}", 10)
				let value = Math.ceil(Number(obj.value))
				while (value % 10 != 0) value += 1
				obj.value = value
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
			if (Number(obj.value) > 100 && (Number(obj.value) % 101) != 0) {
				let local_msg = msg
				local_msg = local_msg.replace("{ph1}", 100)
				local_msg = local_msg.replace("{ph2}", 101)
				local_msg = local_msg.replace("{ph3}", 101)
				obj.value = 101
				calcProfitOrObez(obj.id.replace("_coef", ""))
				showNotify("red-notify", local_msg)
				check = false
				continue
			}
		}
		if (!check) return
	}
	let result = validate()
	if (result.success) {
		for (let i = 0; i < bets.length; i++) {
			let obj = bets[i]
			let currentId
			switch(obj.move) {
				case 1:
					currentId = `back_team_${obj.team}`
					break
				case 2:
					currentId = `lay_team_${obj.team}`
					break
			}
			let inputs = document.getElementById(currentId).getElementsByTagName("input")
			if (Number(inputs[0].value) < INFO.minCoef) {
				switch(INFO.lang) {
					case "ru":
						showNotify("red-notify", `Минимальный коэффициент: ${INFO.minCoef}`)
						break
					case "en":
						showNotify("red-notify", `Minimum coefficient: ${INFO.minCoef}`)
						break
				}
				return
			} else if (Number(inputs[0].value) > INFO.maxCoef) {
				switch(INFO.lang) {
					case "ru":
						showNotify("red-notify", `Максимальный коэффициент: ${INFO.maxCoef}`)
						break
					case "en":
						showNotify("red-notify", `Maximum coefficient: ${INFO.maxCoef}`)
						break
				}
				return
			}
			let url = `${INFO.domain}/ajax/addbet`
			let data = {
				id: ACTION_ID,
				coef: Number(inputs[0].value),
				move: obj.move,
				team: obj.team,
				sum: Number(inputs[1].value)
			}
			let req = new Request(url, {
				method: 'POST',
				mode: 'cors',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': `${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`
				},
				body: JSON.stringify(data)
			})
			fetch(req)
				.then(response => {
					if (response.ok) {
						return response.json()
					} else {
						switch(INFO.lang) {
							case "ru":
								showNotify("red-notify", "Повторите попытку позже")
								break
							case "en":
								showNotify("red-notify", "Please try again later")
								break
						}
					}
				})
				.then(data => {
					if (data.success) {
						showNotify("green-notify", data.text)
					} else {
						if (data.text != undefined) {
							if (data.data != undefined) {
								showNotify("red-notify", data.text, data.data)
							} else {
								showNotify("red-notify", data.text)
							}
						} else {
							switch(INFO.lang) {
								case "ru":
									showNotify("red-notify", "Неизвестная ошибка")
									break
								case "en":
									showNotify("red-notify", "Unknown error")
									break
							}
						}
					}
				})
				.catch(err => {
					showNotify("red-notify", err.message)
					closeAll()
				})
		}
	} else {
		let newNotify = document.createElement("div")
		newNotify.className = "red-notify"
		newNotify.textContent = result.message
		notify_top.appendChild(newNotify)
	}
}

function betHandler(data) {
	if (!AUTH) return
	if (data.data.original.success) {
		let json = JSON.parse(data.data.original.data)
		let blocks = document.getElementsByClassName("balanceUpdate")
		for (let i = 0, block; block = blocks[i]; i++) {
			block.textContent = json.balance
		}
		showNotify("green-notify", data.data.original.text)
		closeOne(json.move, json.team)
		updateProfit()
		getOpenBets()
	} else {
		switch(INFO.lang) {
			case "ru":
				showNotify("red-notify", "Неизвестная ошибка")
				break
			case "en":
				showNotify("red-notify", "Unknown error")
				break
		}
	}
}