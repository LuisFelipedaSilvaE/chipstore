const dismissErrorMessages = document.querySelectorAll('.error-message')
const dismissErrorMessageContainers = document.querySelectorAll(
  '.error-message-container'
)

dismissErrorMessages?.forEach((button, index) =>
  button.addEventListener('click', () => {
    dismissErrorMessageContainers[index].classList.add('hidden')
  })
)
