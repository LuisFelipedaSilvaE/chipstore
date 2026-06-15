const form = document.getElementById('order-form')
const itemsContainer = document.getElementById('items-container')
const itemTemplate = document.getElementById('order-item-template')
const addItemButton = document.getElementById('add-item-button')
const orderTotal = document.getElementById('order-total')
const itemsError = document.getElementById('items-error')
const currency = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
})

const getRows = () => [...itemsContainer.querySelectorAll('.order-item')]

const updateProductAvailability = () => {
  const selects = getRows().map(row => row.querySelector('.product-select'))
  const selectedIds = selects.map(select => select.value).filter(Boolean)

  selects.forEach(select => {
    ;[...select.options].forEach(option => {
      option.disabled = Boolean(
        option.value
        && option.value !== select.value
        && selectedIds.includes(option.value),
      )
    })
  })
}

const updateTotals = () => {
  let total = 0

  getRows().forEach(row => {
    const select = row.querySelector('.product-select')
    const quantityInput = row.querySelector('.quantity-input')
    const selectedOption = select.selectedOptions[0]
    const price = Number(selectedOption?.dataset.price || 0)
    const stock = Number(selectedOption?.dataset.stock || 0)
    const quantity = Math.max(1, Number(quantityInput.value) || 1)

    quantityInput.max = stock || ''
    const subtotal = price * quantity
    row.querySelector('.item-subtotal').textContent = currency.format(subtotal)
    total += subtotal
  })

  orderTotal.textContent = currency.format(total)
  updateProductAvailability()
}

const bindRow = row => {
  row.querySelector('.product-select').addEventListener('change', updateTotals)
  row.querySelector('.quantity-input').addEventListener('input', updateTotals)
  row.querySelector('.remove-item-button').addEventListener('click', () => {
    if (getRows().length === 1) {
      itemsError.textContent = 'O pedido deve possuir pelo menos um produto.'
      itemsError.classList.remove('hidden')
      return
    }

    row.remove()
    itemsError.classList.add('hidden')
    updateTotals()
  })
}

getRows().forEach(bindRow)

addItemButton.addEventListener('click', () => {
  const fragment = itemTemplate.content.cloneNode(true)
  const row = fragment.querySelector('.order-item')
  itemsContainer.appendChild(fragment)
  bindRow(row)
  updateTotals()
})

form.addEventListener('submit', event => {
  const rows = getRows()
  const productIds = rows
    .map(row => row.querySelector('.product-select').value)
    .filter(Boolean)
  const hasDuplicates = new Set(productIds).size !== productIds.length
  const hasInvalidStock = rows.some(row => {
    const select = row.querySelector('.product-select')
    const quantity = Number(row.querySelector('.quantity-input').value)
    const stock = Number(select.selectedOptions[0]?.dataset.stock || 0)
    return select.value && (quantity < 1 || quantity > stock)
  })

  if (productIds.length === 0 || hasDuplicates || hasInvalidStock) {
    event.preventDefault()
    itemsError.textContent = hasDuplicates
      ? 'Cada produto pode ser adicionado apenas uma vez.'
      : hasInvalidStock
        ? 'Uma das quantidades é maior que o estoque disponível.'
        : 'Adicione pelo menos um produto ao pedido.'
    itemsError.classList.remove('hidden')
  }
})

updateTotals()
