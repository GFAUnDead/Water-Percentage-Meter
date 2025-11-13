<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Water Meter</title>
	<style>
		:root {
			--bg: #071022;
			--panel: #071420;
			--accent: #18b4ff;
			--accent-strong: #00a3e6;
			--muted: #93a4b1;
			--glass: rgba(255, 255, 255, 0.03);
			--transition-smooth: cubic-bezier(0.2, 0.9, 0.3, 1);
			--transition-bounce: cubic-bezier(0.2, 0.85, 0.3, 1);
		}
		html, body {
			height: 100%;
			box-sizing: border-box;
		}
		body {
			margin: 0;
			font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
			background: #00ff00;
			color: #d9eef8;
			padding: 0;
			min-height: 100vh;
			min-width: 360px;
			position: relative;
		}
		.panel {
			width: 360px;
			padding: 22px;
			border-radius: 16px;
			background: transparent;
			box-shadow: none;
			backdrop-filter: none;
			border: none;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
		.meter-wrap {
			display: flex;
			gap: 20px;
			align-items: center;
			margin-top: 18px;
		}
		.meter {
			position: relative;
			width: 72px;
			height: 280px;
			border-radius: 42px;
			padding: 12px;
			display: flex;
			align-items: flex-end;
			justify-content: center;
			overflow: visible;
			background: linear-gradient(180deg, rgba(255, 255, 255, 0.01), rgba(255, 255, 255, 0.008));
			box-shadow: none;
		}
		.meter-inner {
			position: relative;
			width: 44px;
			height: 100%;
			border-radius: 34px;
			overflow: hidden;
			background: linear-gradient(180deg, #0b2a33, #042022);
			border: 1px solid rgba(255, 255, 255, 0.03);
			box-shadow: none;
			display: flex;
			align-items: flex-end;
			justify-content: center;
		}
		.meter-percentage {
			position: absolute;
			bottom: 10px;
			left: 50%;
			transform: translateX(-50%);
			color: rgba(255, 255, 255, 0.8);
			font-size: 12px;
			font-weight: bold;
			text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
			z-index: 2;
			pointer-events: none;
		}
		.meter-inner::before {
			content: '';
			position: absolute;
			inset: -8px;
			border-radius: 40px;
			background: transparent;
			pointer-events: none;
			box-shadow: none;
			opacity: 0;
		}
		.meter-inner.full::before {
			box-shadow: 0 0 40px rgba(24, 180, 255, 0.22), 0 0 80px rgba(24, 180, 255, 0.08);
		}
		.meter-inner::after {
			content: '';
			position: absolute;
			left: -6px;
			right: -6px;
			bottom: 0;
			height: var(--fill, 0%);
			border-radius: 28px;
			pointer-events: none;
			background: linear-gradient(180deg, rgba(24, 180, 255, 0.14), rgba(24, 180, 255, 0.06));
			box-shadow: 0 0 28px rgba(24, 180, 255, 0.18);
			transition: height 850ms var(--transition-smooth), box-shadow 220ms ease, opacity 220ms ease;
			opacity: 0.9;
		}
		.meter-inner.hasFill::after {
			opacity: 0.95;
		}
		.meter-inner.full::after {
			box-shadow: 0 0 40px rgba(24, 180, 255, 0.28), 0 0 100px rgba(24, 180, 255, 0.08);
		}
		.meter-fill {
			position: absolute;
			left: 0;
			right: 0;
			bottom: 0;
			height: 0%;
			background: linear-gradient(180deg, rgba(24, 180, 255, 0.18) 0%, rgba(3, 86, 110, 0.12) 50%, rgba(2, 40, 52, 0.06) 100%);
			box-shadow: none;
			transition: height 850ms var(--transition-smooth);
		}
		.meter-fill.full {
			box-shadow: none;
		}
		.meter-fill::after {
			content: '';
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			height: 8%;
			background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), transparent);
		}
		.drop {
			position: absolute;
			width: 48px;
			height: 48px;
			left: 50%;
			transform: translateX(-50%);
			bottom: calc(var(--drop-bottom, 0%));
			pointer-events: none;
			transition: bottom 850ms var(--transition-smooth), transform 380ms var(--transition-bounce);
			filter: none;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.drop svg {
			width: 100%;
			height: 100%;
			display: block;
		}
		.drop .fill {
			fill: transparent;
			transition: fill 320ms ease, transform 320ms ease;
		}
		.drop .outline {
			fill: none;
			stroke: rgba(255, 255, 255, 0.85);
			stroke-width: 1.6;
			transition: stroke 300ms ease;
		}
		.drop.blue .fill {
			fill: var(--accent);
		}
		.drop.blue .outline {
			stroke: rgba(0, 110, 180, 0.95);
		}
		.drop.over .fill {
			fill: #ffd700;
		}
		.drop.over .outline {
			stroke: #b8860b;
		}
		.drop.small {
			transform: translateX(-50%) scale(0.92);
		}
		.drop.full {
			transform: translateX(-50%) translateY(-2px) scale(1.04);
		}
		.controls {
			flex: 1;
		}
		.controls .percent {
			font-size: 34px;
			font-weight: 800;
			color: var(--accent);
			text-align: right;
		}
		.percent.full {
			color: var(--accent-strong);
			text-shadow: 0 6px 24px rgba(0, 140, 200, 0.12);
		}
		.slider {
			width: 100%;
			margin-top: 10px;
		}
		input[type=range] {
			width: 100%;
			-webkit-appearance: none;
			background: transparent;
		}
		input[type=range]::-webkit-slider-runnable-track {
			height: 8px;
			background: linear-gradient(90deg, #07384a, rgba(24, 180, 255, 0.18));
			border-radius: 999px;
		}
		input[type=range]::-webkit-slider-thumb {
			-webkit-appearance: none;
			margin-top: -6px;
			width: 16px;
			height: 16px;
			border-radius: 50%;
			background: var(--accent);
			box-shadow: 0 4px 14px rgba(24, 180, 255, 0.26);
		}
		input[type=range]:focus {
			outline: none;
		}
		.buttons {
			display: flex;
			gap: 8px;
			margin-top: 12px;
		}
		button {
			background: transparent;
			border: 1px solid rgba(255, 255, 255, 0.04);
			color: var(--muted);
			padding: 8px 10px;
			border-radius: 10px;
			cursor: pointer;
		}
		button.primary {
			background: linear-gradient(90deg, var(--accent), #6dd5fa);
			color: #022;
			font-weight: 700;
			box-shadow: none;
		}
		.inc-btn[disabled],
		.dec-btn[disabled] {
			opacity: 0.36;
			pointer-events: none;
			transform: none;
			box-shadow: none;
		}
		@keyframes pop {
			0% {
				transform: translateX(-50%) scale(1);
			}
			50% {
				transform: translateX(-50%) scale(1.08);
			}
			100% {
				transform: translateX(-50%) scale(1);
			}
		}
		.drop.pop {
			animation: pop 360ms var(--transition-smooth);
		}
		.meta {
			margin-top: 12px;
			font-size: 12px;
			color: var(--muted);
		}
		.sr-only {
			position: absolute !important;
			width: 1px;
			height: 1px;
			padding: 0;
			margin: -1px;
			overflow: hidden;
			clip: rect(0 0 0 0);
			white-space: nowrap;
			border: 0;
		}
		/* Modal styles */
		.modal {
			display: none;
			position: absolute;
			z-index: 10;
			top: 0;
			left: 114px;
			width: calc(100% - 114px);
			height: 100%;
			background-color: transparent;
		}
		.modal-content {
			background-color: var(--panel);
			margin: 10% auto;
			padding: 20px;
			border: 1px solid #888;
			width: 240px;
			border-radius: 16px;
			position: relative;
		}
		.close {
			color: var(--muted);
			float: right;
			font-size: 28px;
			font-weight: bold;
			cursor: pointer;
		}
		.close:hover {
			color: var(--accent);
		}
		#pastTable {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
		}
		#pastTable th,
		#pastTable td {
			border: 1px solid rgba(255, 255, 255, 0.1);
			padding: 8px;
			text-align: left;
		}
		#pastTable th {
			background-color: var(--bg);
			color: var(--accent);
		}
	</style>
</head>
<body>
	<div class="panel">
		<div class="meter-wrap">
			<div class="meter" aria-hidden="true">
				<div class="meter-inner" id="meterInner">
					<div class="meter-percentage" id="meterPercentage">0%</div>
					<div class="meter-fill" id="meterFill" style="height: 0%"></div>
				</div>
				<div class="drop" id="drop" style="--drop-bottom: 0%">
					<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path class="fill" d="M32 6c0 0-14 14-14 24a14 14 0 0028 0C46 20 32 6 32 6z" />
						<path class="outline" d="M32 6c0 0-14 14-14 24a14 14 0 0028 0C46 20 32 6 32 6z" />
					</svg>
				</div>
			</div>
			<div class="controls">
				<div class="sr-only">
					<div>Level</div>
					<div id="srPercent">Current level</div>
				</div>
				<div style="display: flex; align-items: center; justify-content: flex-end;">
					<div class="percent" id="percentText">0%</div>
				</div>
				<div class="buttons">
					<button id="inc1" class="primary inc-btn">+1</button>
					<button id="inc5" class="primary inc-btn">+5</button>
					<button id="inc10" class="primary inc-btn">+10</button>
					<button id="dec1" class="primary dec-btn">-1</button>
					<button id="dec5" class="primary dec-btn">-5</button>
					<button id="dec10" class="primary dec-btn">-10</button>
					<button id="viewPast" class="primary">View Past</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal for past records -->
	<div id="pastModal" class="modal">
		<div class="modal-content">
			<span class="close">&times;</span>
			<h3>Past Records</h3>
			<table id="pastTable">
				<thead>
					<tr>
						<th>Date</th>
						<th>Percentage</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	<script>
		const WaterMeter = (() => {
			// Configuration constants
			const CONFIG = {
				SAVE_DELAY: 350,
				DROP_OFFSET_PCT: 6,
				DROP_BOTTOM_FULL: 86,
				TRANSITION_DURATION: 850,
				SCALE_ANIMATION_DURATION: 320,
				MAX_PERCENT: 1000,
				CHECK_INTERVAL: 60000,
				PERCENT_THRESHOLD_FULL: 100,
				PERCENT_THRESHOLD_LOW: 20,
				PERCENT_THRESHOLD_VERY_LOW: 0,
			};
			// DOM elements cache
			const DOM = {
				meterFill: document.getElementById('meterFill'),
				meterInner: document.getElementById('meterInner'),
				drop: document.getElementById('drop'),
				percentText: document.getElementById('percentText'),
				meterPercentage: document.getElementById('meterPercentage'),
				incButtons: Array.from(document.querySelectorAll('.inc-btn')),
				decButtons: Array.from(document.querySelectorAll('.dec-btn')),
				modal: document.getElementById('pastModal'),
				closeButton: document.querySelector('.close'),
				pastTable: document.querySelector('#pastTable tbody'),
				viewPastButton: document.getElementById('viewPast'),
			};
			// State management
			let state = {
				saveTimeout: null,
				dropAnimationTimeout: null,
			};
			const clamp = (value) => {
				return Math.max(0, Math.min(Math.round(value), CONFIG.MAX_PERCENT));
			};
			const savePercentDebounced = (percent) => {
				clearTimeout(state.saveTimeout);
				state.saveTimeout = setTimeout(() => {
					fetch('water_save.php', {
						method: 'POST',
						headers: { 'Content-Type': 'application/json' },
						body: JSON.stringify({ percent }),
					}).catch(() => {
						// Fail silently - server may be temporarily unavailable
					});
				}, CONFIG.SAVE_DELAY);
			};
			const updatePercentagePosition = (percent) => {
				if (percent === CONFIG.PERCENT_THRESHOLD_VERY_LOW) {
					DOM.meterPercentage.style.bottom = '50%';
					DOM.meterPercentage.style.transform = 'translateX(-50%) translateY(50%)';
				} else if (percent < CONFIG.PERCENT_THRESHOLD_LOW) {
					const bottom = Math.max(percent + 5, 15);
					DOM.meterPercentage.style.bottom = `${bottom}px`;
					DOM.meterPercentage.style.transform = 'translateX(-50%)';
				} else {
					DOM.meterPercentage.style.bottom = `${percent - 8}px`;
					DOM.meterPercentage.style.transform = 'translateX(-50%)';
				}
			};
			const updateDropState = (percent) => {
				if (percent > CONFIG.PERCENT_THRESHOLD_FULL) {
					// Over 100%
					DOM.drop.style.setProperty('--drop-bottom', `${CONFIG.DROP_BOTTOM_FULL}%`);
					DOM.drop.classList.add('over', 'full');
					DOM.drop.classList.remove('blue');
				} else if (percent >= CONFIG.PERCENT_THRESHOLD_FULL) {
					// Exactly 100%
					DOM.drop.style.setProperty('--drop-bottom', `${CONFIG.DROP_BOTTOM_FULL}%`);
					DOM.drop.classList.add('blue', 'full');
					DOM.drop.classList.remove('over');
				} else {
					// Below 100%
					const dropBottom = Math.max(0, percent - CONFIG.DROP_OFFSET_PCT);
					DOM.drop.style.setProperty('--drop-bottom', `${dropBottom}%`);
					DOM.drop.classList.remove('blue', 'over', 'full');
				}
			};
			const updateMeterClasses = (percent) => {
				const isFull = percent >= CONFIG.PERCENT_THRESHOLD_FULL;
				const hasFill = percent > CONFIG.PERCENT_THRESHOLD_VERY_LOW;
				DOM.meterInner.classList.toggle('full', isFull);
				DOM.meterFill.classList.toggle('full', isFull);
				DOM.percentText.classList.toggle('full', isFull);
				DOM.meterInner.classList.toggle('hasFill', hasFill);
			};
			const updateButtonStates = (percent) => {
				DOM.incButtons.forEach((btn) => {
					btn.removeAttribute('disabled');
				});
				DOM.decButtons.forEach((btn) => {
					const delta = parseInt(btn.textContent.replace('-', ''), 10);
					if (percent - delta < 0) {
						btn.setAttribute('disabled', '');
					} else {
						btn.removeAttribute('disabled');
					}
				});
			};
			const playPopAnimation = () => {
				DOM.drop.classList.add('small');
				clearTimeout(state.dropAnimationTimeout);
				state.dropAnimationTimeout = setTimeout(
					() => DOM.drop.classList.remove('small'),
					CONFIG.SCALE_ANIMATION_DURATION
				);
			};
			const setLevel = (targetPercent) => {
				const percent = clamp(targetPercent);
				// Update display
				DOM.percentText.textContent = `${percent}%`;
				DOM.meterPercentage.textContent = `${percent}%`;
				// Update positions and heights
				updatePercentagePosition(percent);
				DOM.meterFill.style.height = `${Math.min(percent, 100)}%`;
				DOM.meterInner.style.setProperty('--fill', `${Math.min(percent, 100)}%`);
				// Update visual states
				updateDropState(percent);
				updateMeterClasses(percent);
				updateButtonStates(percent);
				// Persist the change
				savePercentDebounced(percent);
			};
			const handleIncrement = (delta) => {
				const currentPercent = Number(DOM.percentText.textContent.replace('%', '')) || 0;
				setLevel(currentPercent + delta);
				playPopAnimation();
				DOM.drop.classList.add('pop');
			};
			const handleDecrement = (delta) => {
				const currentPercent = Number(DOM.percentText.textContent.replace('%', '')) || 0;
				setLevel(currentPercent - delta);
				playPopAnimation();
				DOM.drop.classList.add('pop');
			};
			const loadPastRecords = () => {
				fetch('water_save.php?all=true')
					.then((response) => response.json())
					.then((data) => {
						if (data && data.records) {
							DOM.pastTable.innerHTML = '';
							const sortedDates = Object.keys(data.records).sort(
								(a, b) => new Date(b) - new Date(a)
							);
							sortedDates.forEach((date) => {
								const percent = data.records[date];
								const row = DOM.pastTable.insertRow();
								row.insertCell(0).textContent = date;
								row.insertCell(1).textContent = `${percent}%`;
							});
							DOM.modal.style.display = 'block';
						}
					})
					.catch(() => {
						// Handle error silently
					});
			};
			const closeModal = () => {
				DOM.modal.style.display = 'none';
			};
			const checkDateAndSync = () => {
				fetch('water_save.php')
					.then((response) => response.json())
					.then((data) => {
						if (data) {
							const currentLevel = Number(DOM.percentText.textContent.replace('%', '')) || 0;
							if (data.date !== data.current_date) {
								// New day, reset to 0
								setLevel(0);
							} else if (typeof data.percent === 'number' && currentLevel !== data.percent) {
								// Sync with saved level
								setLevel(data.percent);
							}
						}
					})
					.catch(() => {
						// Handle error silently
					});
			};
			const initializeLevel = () => {
				fetch('water_save.php')
					.then((response) => response.json())
					.then((data) => {
						if (data && data.date === data.current_date && typeof data.percent === 'number') {
							setLevel(data.percent);
						} else {
							setLevel(0);
						}
					})
					.catch(() => {
						setLevel(0);
					});
			};
			const bindEvents = () => {
				document.getElementById('inc1').addEventListener('click', () => handleIncrement(1));
				document.getElementById('inc5').addEventListener('click', () => handleIncrement(5));
				document.getElementById('inc10').addEventListener('click', () => handleIncrement(10));
				document.getElementById('dec1').addEventListener('click', () => handleDecrement(1));
				document.getElementById('dec5').addEventListener('click', () => handleDecrement(5));
				document.getElementById('dec10').addEventListener('click', () => handleDecrement(10));
				DOM.viewPastButton.addEventListener('click', loadPastRecords);
				DOM.closeButton.addEventListener('click', closeModal);
				DOM.modal.addEventListener('click', (event) => {
					if (event.target === DOM.modal) {
						closeModal();
					}
				});
				// Check date and sync every minute
				setInterval(checkDateAndSync, CONFIG.CHECK_INTERVAL);
			};
			const init = () => {
				bindEvents();
				initializeLevel();
			};
			// Public API
			return {
				init,
				setLevel,
			};
		})();
		// Start the application when DOM is ready
		document.addEventListener('DOMContentLoaded', WaterMeter.init);
		// Also try immediate init in case DOM is already loaded
		WaterMeter.init();
	</script>
</body>
</html>
