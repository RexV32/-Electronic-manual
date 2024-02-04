const allDropmenuButton = document.querySelectorAll(".dropmenu__button");
const dropmenu = document.querySelector(".dropmenu__menu");

function OnClickDropMenu(evt) {
  const buttonTarget = evt.target;
  const parentNode = evt.target.parentNode;
  const dropmenu = parentNode.querySelector(".dropmenu__menu");
  buttonTarget.classList.toggle("dropmenu__button--open");
  dropmenu.classList.toggle("dropmenu__menu--open");
}


allDropmenuButton.forEach((value) => {
  value.addEventListener("click", OnClickDropMenu);
});
