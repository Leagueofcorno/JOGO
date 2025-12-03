const player = document.getElementById("teemo");
const enemies = [
  document.getElementById("singed"),
  document.getElementById("malphite"),
  document.getElementById("skarner"),
  document.getElementById("viego") 
];
const scoreElement = document.getElementById("time");
const timerElement = document.getElementById("timer");
const gameOverHud = document.getElementById("gameOverHud");
const recordsDiv = document.getElementById("records");

const initialX = player ? parseInt(window.getComputedStyle(player).left) : 0;
const initialY = player ? parseInt(window.getComputedStyle(player).top) : 0;

let playerX = initialX;
let playerY = initialY;
let playerSpeed = 20;
let score = 0;
let seconds = 0;
let gameInterval;
let timerInterval;

let recordSent = false;

function updatePlayerPosition() {
  if (!player) return;
  player.style.left = playerX + "px";
  player.style.top = playerY + "px";
}

function movePlayer(event) {
  if (!player) return;
  switch (event.key) {
    case "ArrowLeft": if (playerX > 40) playerX -= playerSpeed; break;
    case "ArrowRight": if (playerX < 410) playerX += playerSpeed; break;
    case "ArrowUp": if (playerY > 10) playerY -= playerSpeed; break;
    case "ArrowDown": if (playerY < 560) playerY += playerSpeed; break;
  }
  updatePlayerPosition();
}

function updateEnemies() {
  for (const enemy of enemies) {
    if (!enemy) continue;
    const enemyTop = parseInt(window.getComputedStyle(enemy).top);
    enemy.style.top = enemyTop + 5 + "px";

    if (enemyTop > 600) {
      enemy.style.top = "0px";
      score += 150;
      if (scoreElement) scoreElement.textContent = "Score: " + score;
    }

    if (!player) continue;
    const playerRect = player.getBoundingClientRect();
    const enemyRect = enemy.getBoundingClientRect();

    if (
      playerRect.left < enemyRect.right &&
      playerRect.right > enemyRect.left &&
      playerRect.top < enemyRect.bottom &&
      playerRect.bottom > enemyRect.top
    ) {
      gameOver();
    }
  }
}

function startTimer() {
  seconds = 0;
  clearInterval(timerInterval);
  timerInterval = setInterval(() => {
    seconds++;
    if (timerElement) timerElement.textContent = "Tempo: " + seconds + "s";
  }, 1000);
}

function gameOver() {
  clearInterval(gameInterval);
  clearInterval(timerInterval);

  document.removeEventListener("keydown", movePlayer);

  enemies.forEach(enemy => {
    if (enemy) enemy.style.animationPlayState = "paused";
  });

  if (gameOverHud) gameOverHud.classList.remove("hidden");

  showRecords();

  if (!recordSent) {
    recordSent = true;
    saveRecordToServer(score, seconds);
  } else {
    console.log('Recorde já enviado anteriormente (recordSent=true).');
  }
}

function showRecords() {
  if (!recordsDiv) return;
  recordsDiv.innerHTML = `<p>Score final: ${score}</p><p>Tempo sobrevivido: ${seconds}s</p>`;
}

function saveRecordToServer(pontos, tempo) {
  if (!recordsDiv) {
    console.warn('recordsDiv não encontrado; não será exibido feedback.');
  }

  const payload = {
    pontos: Number(pontos) || 0,
    tempo: Number(tempo) || 0
  };

  console.log('Enviando payload para salvar.php:', payload);

  const statusEl = document.createElement('p');
  statusEl.textContent = 'Salvando recorde...';
  if (recordsDiv) recordsDiv.appendChild(statusEl);

  fetch('salvar.php', { 
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(async response => {
    console.log('HTTP status:', response.status, response.statusText);
    const text = await response.text();
    console.log('Resposta bruta do servidor:', text);

    try {
      return JSON.parse(text);
    } catch (err) {
      return { success: false, message: 'Resposta inválida do servidor.', raw: text };
    }
  })
  .then(data => {
    if (statusEl && statusEl.parentNode) statusEl.parentNode.removeChild(statusEl);

    if (data && data.success) {
      const ok = document.createElement('p');
      ok.textContent = 'Recorde salvo com sucesso!';
      if (recordsDiv) recordsDiv.appendChild(ok);

      if (data.assigned_elo) {
        const elo = data.assigned_elo;
        const eloEl = document.createElement('p');
        eloEl.innerHTML = `Elo atribuído: <strong>${elo.nome_elos}</strong> (meta ${elo.meta}, vagas ${elo.vagas}, ocupação agora ${elo.ocupacao})`;
        if (recordsDiv) recordsDiv.appendChild(eloEl);
      } else if (data.note) {
        const noteEl = document.createElement('p');
        noteEl.textContent = data.note;
        if (recordsDiv) recordsDiv.appendChild(noteEl);
      }
    } else {
      const err = document.createElement('p');
      err.textContent = 'Não foi possível salvar o recorde: ' + (data.message || 'erro desconhecido');
      if (recordsDiv) recordsDiv.appendChild(err);
      console.error('saveRecord error data:', data);
    }
  })
  .catch(err => {
    if (statusEl && statusEl.parentNode) statusEl.parentNode.removeChild(statusEl);
    const e = document.createElement('p');
    e.textContent = 'Erro ao conectar com o servidor.';
    if (recordsDiv) recordsDiv.appendChild(e);
    console.error('saveRecord fetch error:', err);
  });
}

document.addEventListener("keydown", movePlayer);

gameInterval = setInterval(updateEnemies, 50);
startTimer();
