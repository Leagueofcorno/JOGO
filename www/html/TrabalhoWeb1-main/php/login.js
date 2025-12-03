const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

if (registerBtn) {
  registerBtn.addEventListener('click', () => {
    if (container) container.classList.add("active");
  });
}
if (loginBtn) {
  loginBtn.addEventListener('click', () => {
    if (container) container.classList.remove("active");
  });
}

$(function(){
  $("#form-test").on("submit", function(e) {
    let valid = true;

    let nome_input = $("input[name='nome']");
    if (!nome_input.val() || !nome_input.val().trim())
    {
      $("#erro-nome").html("O nome é obrigatório");
      valid = false;
    } else {
      $("#erro-nome").html("");
    }

    let mail_input = $("input[name='email']");
    if (!mail_input.val() || !mail_input.val().trim()) 
    {
      $("#erro-mail").html("O e-mail é obrigatório");
      valid = false;
    } else {
      $("#erro-mail").html("");
    }

    let senha_input = $("input[name='senha']");
    if (!senha_input.val() || !senha_input.val().trim()) {
      $("#erro-senha").html("A senha é obrigatória");
      valid = false;
    } else {
      $("#erro-senha").html("");
    }

    if (!valid) {
      e.preventDefault();
      return false;
    }

    const submitBtn = $(this).find("button[type='submit']");
    if (submitBtn.length) {
      submitBtn.prop("disabled", true).attr("data-waiting", "true");
      submitBtn.data('orig-text', submitBtn.text()).text('Enviando...');
    }
    return true;
  });

  $("#form-test input").on("input", function() {
    const submitBtn = $("#form-test").find("button[type='submit']");
    if (submitBtn.length && submitBtn.attr("data-waiting") === "true") {
      submitBtn.prop("disabled", false).removeAttr("data-waiting").text(submitBtn.data('orig-text') || 'Cadastre-se');
    }
  });
});
