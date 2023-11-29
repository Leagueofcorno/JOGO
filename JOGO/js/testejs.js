var interval
var sec=0
var min=0
var hr=0
var points=0

function twoDigits(digit){ //fins esteticos coloca ozero na frente
  if(digit<10){
      return('0'+digit)
  }else
  {
      return(digit)
  }
}

function start(){
  Cronometro()
  interval= setInterval(Cronometro,10)
  var inimigosElements = document.getElementsByClassName("inimigos");
  for (var i = 0; i < inimigosElements.length; i++) {
    var inimigoElement = inimigosElements[i];
    inimigoElement.style.animationPlayState = 'running';
  }
}


function Cronometro(){
  sec++
  if(sec==60){
      min++
      sec=0
      points=points+5;
      if(min==60){
          min=0
          hr++
      }
  }
  document.getElementById('Cronometro').innerText=twoDigits(hr)+':'+twoDigits(min)
  document.getElementById('Pontos').innerText=+twoDigits(points)

}


////movimentar o player 
var x = 1860
var y=1900
document.addEventListener("keydown",function(event)
{
    var tecla = event.key
    switch(tecla)
    {
    case "d":
       if(x<3610)
       {
        x = x + 350
        document.getElementById("rocket").style.left=x+"px"
       }
      break;

    case"a":
    if(x>110)
       {
       
        x = x - 350
        document.getElementById("rocket").style.left=x+"px"
       }
      
      break;

       case"w":
       if(y>150)
       {
       y = y - 350
       document.getElementById("rocket").style.top=y+"px"

       }
      break;

       case"s":
       if(y<1900)
       {
       y = y + 350
       document.getElementById("rocket").style.top=y+"px"
       console.log(y);

       }
       break;
       
    }

})



// Função para verificar colisão atualziado

const player = document.getElementById("rocket");
const inimigo1 = document.getElementById("Skarner");
const inimigo2 = document.getElementById("Singed");
const inimigo3 = document.getElementById("Malphite");

function verificarColisao() {
  const playerRect = player.getBoundingClientRect();
  const enemies = [inimigo1, inimigo2, inimigo3];

  for (const inimigos of enemies) {
    const enemyRect = inimigos.getBoundingClientRect();

    if (
      playerRect.left < enemyRect.right &&
      playerRect.right > enemyRect.left &&
      playerRect.top < enemyRect.bottom &&
      playerRect.bottom > enemyRect.top
    ) {

      console.log("Meu ferrão trará uma morte hedionda");

    }
  }
}

setInterval(verificarColisao, 50);
