const dismissErrorMessages = document.querySelectorAll('.error-message')
const dismissErrorMessageContainers = document.querySelectorAll(
  '.error-message-container'
)
const inputTelefone = document.getElementById('telefone')

dismissErrorMessages?.forEach((button, index) =>
  button.addEventListener('click', () => {
    dismissErrorMessageContainers[index].classList.add('hidden')
  })
)

inputTelefone.addEventListener('input', (event) => {
  let numeroLimpo = event.target.value.replace(/\D/g, '')
  numeroLimpo = numeroLimpo.substring(0, 11)
  let numeroMascarado = ''

  if (numeroLimpo.length > 0) {
    numeroMascarado += '(' + numeroLimpo.substring(0, 2)
  }
  if (numeroLimpo.length > 2) {
    numeroMascarado += ') ' + numeroLimpo.substring(2, 7)
  }
  if (numeroLimpo.length > 7) {
    numeroMascarado += '-' + numeroLimpo.substring(7, 11)
  }

  event.target.value = numeroMascarado
})
