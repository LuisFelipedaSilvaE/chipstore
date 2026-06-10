const dismissSuccessMessage = document.getElementById('dismissSuccessMessage')
const dialogMask = document.getElementById('delete-confirmation-dialog-mask')
const dialog = document.getElementById('delete-confirmation-dialog')
const dialogDismissButtons = document.querySelectorAll('.dialog-dismiss-button')
const dialogConfirmButton = document.getElementById('dialog-confirm-button')
const dialogClientNameContainer = document.getElementById(
  'client-to-delete-name'
)
const clientNames = document.querySelectorAll('.client-name')
const deleteButtons = document.querySelectorAll('.delete-button')
const toggleDialog = () => {
  if (dialog.classList.contains('hidden')) {
    dialog.classList.toggle('hidden')
    dialogMask.classList.toggle('hidden')
    setTimeout(() => {
      dialog.classList.toggle('opacity-0')
      dialog.classList.toggle('scale-0')
      dialogMask.classList.toggle('opacity-0')
    }, 0)
  } else {
    dialog.classList.toggle('opacity-0')
    dialog.classList.toggle('scale-0')
    dialogMask.classList.toggle('opacity-0')
    setTimeout(() => {
      dialog.classList.toggle('hidden')
      dialogMask.classList.toggle('hidden')
    }, 150)
  }
}

dismissSuccessMessage?.addEventListener('click', () => {
  document.getElementById('successMessage').classList.add('hidden')
})

dialogMask.addEventListener('click', toggleDialog)
dialogDismissButtons.forEach((element) =>
  element.addEventListener('click', toggleDialog)
)

deleteButtons.forEach((element, index) => {
  element.addEventListener('click', () => {
    dialogClientNameContainer.innerText = `\'${clientNames[index].innerText}\'`
    dialogConfirmButton.setAttribute(
      'href',
      `../../actions/cliente/remover-cliente-action.php?id=${element.dataset.clienteId}`
    )
    toggleDialog()
  })
})
