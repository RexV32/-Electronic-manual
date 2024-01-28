const customSelect = document.querySelector(".select-reg");
const selectBtn = document.querySelector(".select-reg__button");
const selectedValue = document.querySelector(".select-reg__selected-value");
const optionsList = document.querySelectorAll(".select-reg__dropdown li");

if(selectedValue.innerHTML === "Номер группы") {
  selectedValue.style.color = "#000000";
  selectedValue.style.opacity = "0.5";
}

selectBtn.addEventListener("click", (evt) => {
  evt.preventDefault();
  customSelect.classList.toggle("active");
  customSelect.classList.toggle("unactive");
  selectBtn.setAttribute("aria-expanded",selectBtn.getAttribute("aria-expanded") === "true" ? "false" : "true");
});

optionsList.forEach((option) => {
  function handler(e) {
    if (e.type === "click" && e.clientX !== 0 && e.clientY !== 0) {
      selectedValue.textContent = this.children[1].textContent;
      customSelect.classList.remove("active");
      customSelect.classList.add("unactive");
      selectedValue.style.opacity = "1";
    }
    if (e.key === "Enter") {
      selectedValue.textContent = this.textContent;
      customSelect.classList.remove("active");
      customSelect.classList.add("unactive");
    }
  }
  option.addEventListener("keyup", handler);
  option.addEventListener("click", handler);
});
