const allDropmenuButton = document.querySelectorAll(".dropmenu__button");
const menu = document.querySelector(".dropmenu__menu");

function OnClickDropMenu(evt) {
  const buttonTarget = evt.target;
  const parentNode = evt.target.parentNode;
  const menu = parentNode.querySelector(".dropmenu__menu");
  buttonTarget.classList.toggle("dropmenu__button--open");
  menu.classList.toggle("dropmenu__menu--open");
}


allDropmenuButton.forEach((value) => {
  value.addEventListener("click", OnClickDropMenu);
});
