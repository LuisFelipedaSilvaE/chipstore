const togglePassword = document.getElementById('togglePassword')
const password = document.getElementById('password')
const toggleIcon = document.getElementById('toggleIcon')

togglePassword.addEventListener('click', () => {
  const type = password.getAttribute('type') === 'password' ? 'text' : 'password'
  password.setAttribute('type', type)

  if (type === 'password') {
    toggleIcon.classList.remove('fa-eye-slash')
    toggleIcon.classList.add('fa-eye')
  } else {
    toggleIcon.classList.remove('fa-eye')
    toggleIcon.classList.add('fa-eye-slash')
  }
})