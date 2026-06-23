const messageContainers = document.querySelectorAll('.message-container')
const dialogMask = document.getElementById('delete-confirmation-dialog-mask')
const dialog = document.getElementById('delete-confirmation-dialog')
const dialogConfirmButton = document.getElementById('dialog-confirm-button')
const orderNameContainer = document.getElementById('order-to-delete-name')

const toggleDialog = () => {
  const opening = dialog.classList.contains('hidden')

  if (opening) {
    dialog.classList.remove('hidden')
    dialogMask.classList.remove('hidden')
    setTimeout(() => {
      dialog.classList.remove('opacity-0', 'scale-0')
      dialogMask.classList.remove('opacity-0')
    }, 0)
    return
  }

  dialog.classList.add('opacity-0', 'scale-0')
  dialogMask.classList.add('opacity-0')
  setTimeout(() => {
    dialog.classList.add('hidden')
    dialogMask.classList.add('hidden')
  }, 150)
}

document.querySelectorAll('.message').forEach((button, index) => {
  button.addEventListener('click', () => messageContainers[index].classList.add('hidden'))
})

dialogMask.addEventListener('click', toggleDialog)
document.querySelectorAll('.dialog-dismiss-button').forEach(button => {
  button.addEventListener('click', toggleDialog)
})

document.querySelectorAll('.delete-button').forEach(button => {
  button.addEventListener('click', () => {
    orderNameContainer.innerText = `#${button.dataset.pedidoId}`
    dialogConfirmButton.href = `../../actions/pedido/remover-pedido-action.php?id=${button.dataset.pedidoId}`
    toggleDialog()
  })
})

setTimeout(() => {
  messageContainers.forEach(container => container.classList.add('hidden'))
}, 3000)

const filterInputs = document.querySelectorAll('#filtros [data-filter]')

function applyFilters() {
  const values = {}
  filterInputs.forEach(f => (values[f.dataset.filter] = f.value.trim()))

  let linhasVisiveis = 0

  document.querySelectorAll('tbody tr').forEach(row => {
    if (row.querySelector('td[colspan]')) return

    const matchId = !values.id || row.children[0].textContent.replace('#', '').trim() === values.id
    const matchCliente =
      !values.cliente || row.children[1].textContent.toLowerCase().includes(values.cliente.toLowerCase())
    const matchStatus = !values.status || row.children[3].textContent.trim() === values.status
    const visivel = matchId && matchCliente && matchStatus
    row.style.display = visivel ? 'table-row' : 'none'
    if (visivel) linhasVisiveis++
  })

  document.getElementById('filtro-vazio').style.display = linhasVisiveis === 0 ? 'table-row' : 'none'
}

filterInputs.forEach(f => f.addEventListener('input', applyFilters))
filterInputs.forEach(f => f.addEventListener('change', applyFilters))

document.getElementById('limpar-filtros')?.addEventListener('click', () => {
  filterInputs.forEach(f => (f.value = ''))
  applyFilters()
})
