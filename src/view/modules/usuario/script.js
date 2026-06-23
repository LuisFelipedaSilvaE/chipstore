const dismissMessages = document.querySelectorAll('.message')
const dismissMessageContainers = document.querySelectorAll('.message-container')
const dialogMask = document.getElementById('delete-confirmation-dialog-mask')
const dialog = document.getElementById('delete-confirmation-dialog')
const dialogDismissButtons = document.querySelectorAll('.dialog-dismiss-button')
const dialogConfirmButton = document.getElementById('dialog-confirm-button')
const dialogUserNameContainer = document.getElementById('user-to-delete-name')
const tableBody = document.querySelector('tbody')

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

dismissMessages?.forEach((button, index) => {
  button.addEventListener('click', () => dismissMessageContainers[index].classList.add('hidden'))
})

dialogMask.addEventListener('click', toggleDialog)
dialogDismissButtons.forEach(element => element.addEventListener('click', toggleDialog))

tableBody?.addEventListener('click', event => {
  const button = event.target.closest('.delete-button')
  if (!button) return

  const row = button.closest('tr')
  const userName = row.querySelector('.user-name')

  dialogUserNameContainer.innerText = `\'${userName.innerText}\'`
  dialogConfirmButton.setAttribute(
    'href',
    `../../actions/usuario/remover-usuario-action.php?id=${button.dataset.usuarioId}`,
  )
  toggleDialog()
})

document.addEventListener('DOMContentLoaded', () => {
  dismissMessageContainers?.forEach(element => {
    setTimeout(() => {
      element.classList.add('opacity-0')
      element.classList.add('transition-all')
      element.classList.add('duration-2000')
    }, 1000)
    setTimeout(() => element.classList.add('hidden'), 2000)
  })
})

const filterInputs = document.querySelectorAll('#filtros [data-filter]')

function applyFilters() {
  const values = {}
  filterInputs.forEach(f => (values[f.dataset.filter] = f.value.toLowerCase().trim()))

  let linhasVisiveis = 0

  document.querySelectorAll('tbody tr').forEach(row => {
    if (row.querySelector('td[colspan]')) return

    const matchNome = !values.nome || row.querySelector('.user-name').textContent.toLowerCase().includes(values.nome)
    const matchEmail = !values.email || row.children[2].textContent.toLowerCase().includes(values.email)
    const visivel = matchNome && matchEmail
    row.style.display = visivel ? 'table-row' : 'none'
    if (visivel) linhasVisiveis++
  })

  document.getElementById('filtro-vazio').style.display = linhasVisiveis === 0 ? 'table-row' : 'none'
}

filterInputs.forEach(f => f.addEventListener('input', applyFilters))

document.getElementById('limpar-filtros')?.addEventListener('click', () => {
  filterInputs.forEach(f => (f.value = ''))
  applyFilters()
})
