const menu = document.getElementById("right_menu")
const myBalanceModal = document.getElementById("myBalanceModal")
const balanceModal = document.getElementById("balanceModal")

function getBalance() {
	if (!AUTH) return
	let buttons = document.getElementsByClassName("balanceUpdateBtn")
	for (button of buttons) {
		if (button.style.cursor == "not-allowed") return -1
	}
	let blocks = document.getElementsByClassName("balanceUpdate")
	let url = `${INFO.domain}/ajax/balance`
	let checkBalanceReq = new Request(url, {
		method: 'POST',
		mode: 'cors',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': `${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`
		}
	})
	fetch(checkBalanceReq)
		.then(response => {
			if (response.ok) {
				return response.json()
			}
		})
		.then(data => {
			if (data.success) {
				for (block of blocks) block.textContent = data.data
				for (button of buttons) {
					button.style.opacity = "0.5"
					button.style.cursor = "not-allowed"
					button.style.animationName = "rotation"
					Timeout(button)
				}
				return Number(data.data)
			}

			return -1
		})
		.catch(err => {
			console.log(err.message)
		})
}

function Timeout(element) {
	setTimeout(() => { element.style.opacity = "1"; element.style.cursor = "pointer"; element.style.animationName = "" }, 3000)
}

function myBalanceToggle() {
	let display = myBalanceModal.style.display
	switch(display) {
		case "none":
			myBalanceModal.style.display = "block"
			balanceModal.style.display = "none"
			break
		case "block":
			myBalanceModal.style.display = "none"
			break
	}
}

function balanceToggle() {
	let display = balanceModal.style.display
	switch(display) {
		case "none":
			balanceModal.style.display = "block"
			myBalanceModal.style.display = "none"
			break
		case "block":
			balanceModal.style.display = "none"
			break
	}
}

function actionLink(id) {
	document.location.href = `${INFO.domain}/action/${id}`
}