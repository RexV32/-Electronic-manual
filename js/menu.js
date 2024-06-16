const openbutton = document.querySelector(".header__toggle-button");
const menu = document.querySelector(".menu__wrapper");
const closeButton = document.querySelector(".menu__button");
const html = document.querySelector("html");
const body = document.body;

openbutton.addEventListener("click", () => {
  menu.classList.add("menu__wrapper--open");
  let div = document.createElement("div");
  div.classList.add("menu-background");
  body.append(div);
  html.classList.add("page-block");
  body.classList.add("body-block");
})

closeButton.addEventListener("click",() => {
  menu.classList.remove("menu__wrapper--open");
  const background = document.querySelector(".menu-background");
  body.removeChild(background);
  html.classList.remove("page-block");
  body.classList.remove("body-block");
})