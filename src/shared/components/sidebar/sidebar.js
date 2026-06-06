const pageTitle = document.querySelector('title')
const sidebar = document.getElementById('sidebar')
const sidebarLogo = document.getElementById('sidebar-logo')
const sidebarItems = document.querySelectorAll('#sidebar ul li')
const sidebarItemsLinks = document.querySelectorAll('#sidebar ul li a')
const toggleSidebar = document.querySelectorAll('.toggle-mobile-sidebar')
const sidebarText = document.querySelectorAll('.collapsable-text')
const mobileSidebarMask = document.querySelector('.sidebar-mobile-mask')
const collapseSidebar = document.getElementById('collapse-side-bar')
const uncollapseSidebar = document.getElementById('uncollapse-side-bar')
const logoutButton = document.getElementById('logout-button')
const toggleMobileSidebarOpen = document.getElementById('toggle-mobile-sidebar-open')

const toggleMaskStyles = () => {
  if (mobileSidebarMask.classList.contains('hidden')) {
    mobileSidebarMask.classList.toggle('hidden')
    setTimeout(() => {
      mobileSidebarMask.classList.toggle('opacity-0')
    }, 0)
  } else {
    setTimeout(() => {
      mobileSidebarMask.classList.toggle('hidden')
    }, 150)
    mobileSidebarMask.classList.toggle('opacity-0')
  }
}

const collapseSidebarFunction = (instant = false) => {
  sidebarText.forEach(element => {
    element.classList.toggle('lg:hidden')
  })

  collapseSidebar.classList.toggle('hidden')

  if (!uncollapseSidebar.classList.contains('hidden')) {
    uncollapseSidebar.classList.toggle('hidden')
  }

  sidebar.classList.toggle('lg:w-100')
  sidebar.classList.toggle('lg:w-20')

  const isCollapsed = sidebar.classList.contains('lg:w-20')
  localStorage.setItem('sidebar-collapsed', isCollapsed)

  const applyLinkChanges = () => {
    sidebarItemsLinks.forEach(element => {
      element.classList.toggle('lg:justify-center')
    })
    logoutButton.classList.toggle('lg:justify-center')
  }

  if (isCollapsed) {
    document.getElementById('sidebar-header').classList.remove('justify-end')
    if (instant) {
      sidebar.classList.remove('transition-all', 'duration-300', 'ease-in-out')
      applyLinkChanges()
      sidebar.offsetHeight
      sidebar.classList.add('transition-all', 'duration-300', 'ease-in-out')
    } else {
      setTimeout(applyLinkChanges, 300)
    }
  } else {
    document.getElementById('sidebar-header').classList.add('justify-end')
    applyLinkChanges()
  }
}

uncollapseSidebar.addEventListener('click', () => collapseSidebarFunction())

collapseSidebar.addEventListener('click', () => collapseSidebarFunction())

// sidebarLogo.addEventListener('mouseover', () => {
//   sidebarLogo.classList.toggle('hidden')
//   if (sidebar.classList.contains('w-fit-important')) {
//     uncollapseSidebar.classList.toggle('hidden')
//   } else {
//     collapseSidebar.classList.toggle('hidden')
//   }
// })

// sidebarLogo.addEventListener('mouseout', () => {
//   sidebarLogo.classList.toggle('hidden')
// })

mobileSidebarMask.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('translate-x-full')

  toggleMaskStyles()
})

toggleSidebar.forEach(element => {
  element.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('translate-x-full')

    toggleMaskStyles()

    console.log(toggleMobileSidebarOpen)

    toggleMobileSidebarOpen.classList.toggle('hidden')
    toggleMobileSidebarOpen.classList.toggle('inline-flex')
  })
})

sidebarItemsLinks.forEach((link, index) => {
  if (link.innerText === pageTitle.innerText) {
    sidebarItems[index].classList.add('bg-[var(--sidebar-item-focus)]')
  } else if (sidebarItems[index].classList.contains('bg-[var(--sidebar-item-focus)]')) {
    sidebarItems[index].classList.remove('bg-[var(--sidebar-item-focus)]')
  }
})

if (localStorage.getItem('sidebar-collapsed') === 'true') {
  collapseSidebarFunction(true)
}
