const pageTitle = document.querySelector("title");
const sidebarItems = document.querySelectorAll("#desktop-sidebar ul li");

document.querySelectorAll("#desktop-sidebar ul li a").forEach((link, index) => {
  if (link.innerText === pageTitle.innerText) {
    sidebarItems[index].classList.add("bg-[var(--sidebar-item-focus)]");
  } else if (
    sidebarItems[index].classList.contains("bg-[var(--sidebar-item-focus)]")
  ) {
    sidebarItems[index].classList.remove("bg-[var(--sidebar-item-focus)]");
  }
});
