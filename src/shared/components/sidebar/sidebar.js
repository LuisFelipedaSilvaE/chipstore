const pageTitle = document.querySelector('title')
const sidebar = document.getElementById('sidebar')
const sidebarLogo = document.getElementById('sidebar-logo')
const sidebarItems = document.querySelectorAll('.sidebar-item')
const sidebarItemsLinks = document.querySelectorAll('.sidebar-item a')
const sidebarItemsIcons = document.querySelectorAll('.sidebar-icon')
const toggleSidebar = document.querySelectorAll('.toggle-mobile-sidebar')
const sidebarText = document.querySelectorAll('.collapsable-text')
const mobileSidebarMask = document.querySelector('.sidebar-mobile-mask')
const collapseSidebar = document.getElementById('collapse-side-bar')
const uncollapseSidebar = document.getElementById('uncollapse-side-bar')
const logoutButton = document.getElementById('logout-button')

const toggleMaskStyles = () => {
  sidebar.classList.toggle('translate-x-full')

  toggleSidebar.item(0).classList.toggle('hidden')
  toggleSidebar.item(0).classList.toggle('inline-flex')

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
  sidebar.classList.toggle('lg:w-100')
  sidebar.classList.toggle('lg:w-20')

  const isCollapsed = sidebar.classList.contains('lg:w-20')
  localStorage.setItem('sidebar-collapsed', isCollapsed)

  sidebarText.forEach((element) => {
    element.classList.toggle('lg:hidden')
  })

  collapseSidebar.classList.toggle('hidden')

  if (!uncollapseSidebar.classList.contains('hidden')) {
    uncollapseSidebar.classList.toggle('hidden')
  }

  sidebarItemsIcons.forEach((element) => {
    element.classList.toggle('lg:text-lg')
  })

  sidebarItemsLinks.forEach((element) => {
    element.classList.toggle('lg:justify-center')
    element.classList.toggle('lg:h-[55px]')
  })

  logoutButton.classList.toggle('lg:h-[55px]')
  logoutButton.classList.toggle('lg:w-[55px]')

  logoutButton.classList.toggle('lg:justify-center')

  const applyLinkChanges = () => {
    sidebarItemsLinks.forEach((element) => {
      element.classList.toggle('lg:w-[55px]')
    })

    logoutButton.classList.toggle('lg:w-[55px]')
  }

  if (isCollapsed) {
    if (instant) {
      sidebar.classList.remove('transition-all', 'duration-300', 'ease-in-out')
      applyLinkChanges()
      sidebar.offsetHeight
      sidebar.classList.add('transition-all', 'duration-300', 'ease-in-out')
    } else {
      applyLinkChanges()
    }
  } else {
    applyLinkChanges()
  }
}

uncollapseSidebar.addEventListener('click', () => collapseSidebarFunction())

collapseSidebar.addEventListener('click', () => collapseSidebarFunction())

mobileSidebarMask.addEventListener('click', () => toggleMaskStyles())

toggleSidebar.forEach((element) => {
  element.addEventListener('click', () => toggleMaskStyles())
})

sidebarItemsLinks.forEach((link, index) => {
  if (link.innerText === pageTitle.innerText) {
    sidebarItems[index].classList.add('bg-[var(--sidebar-item-focus)]')
  } else if (
    sidebarItems[index].classList.contains('bg-[var(--sidebar-item-focus)]')
  ) {
    sidebarItems[index].classList.remove('bg-[var(--sidebar-item-focus)]')
  }
})

if (localStorage.getItem('sidebar-collapsed') === 'true') {
  collapseSidebarFunction(true)
}
