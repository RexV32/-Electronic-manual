const customSelects = document.querySelectorAll(".select");

customSelects.forEach((customSelect) => {
  const selectButton = customSelect.querySelector(".select__button");
  const optionsList = customSelect.querySelectorAll(".select__dropdown li");
  const selectedValue = customSelect.querySelector(".select__selected-value");

  selectButton.addEventListener("click", (evt) => {
    evt.preventDefault();
    customSelect.classList.toggle("active");
    customSelect.classList.toggle("unactive");
    selectButton.setAttribute("aria-expanded", selectButton.getAttribute("aria-expanded") === "true" ? "false" : "true");
  });

  optionsList.forEach((option) => {
    option.addEventListener("click", () => {
      selectedValue.textContent = option.textContent;
      customSelect.classList.remove("active");
      customSelect.classList.add("unactive");
      selectButton.setAttribute("aria-expanded", "false");
    });
  });
});


const radios = document.querySelectorAll("input[name=disciplines]");
const sectionList = document.querySelector(".select__dropdown--section");

function updateSectionList(id) {
  const data = new FormData();
  data.append("id", id);

  fetch("server/get-section.php", {
    method: "POST",
    body: data
  })
  .then(response => response.json())
  .then(data => {
    const selectValue = document.querySelector(".select__selected-value--section");
    selectValue.textContent = data[0].Name;
    sectionList.innerHTML = "";
    let isFirst = true;
    data.forEach(item => {
      let checked = isFirst ? "checked" : "";
      sectionList.innerHTML += `<li role="select__option">
        <input type="radio" id="${item.Id}" name="sections" value="${item.Id}" ${checked}/>
        <label for="${item.Id}">${item.Name}</label>
      </li>`;
      isFirst = false;
    });

    const radioInputs = document.querySelectorAll("input[name=sections]");
    radioInputs.forEach(radioInput => {
      radioInput.addEventListener("click", () => {
        const select = radioInput.closest(".select");
        const button = select.querySelector(".select__button");
        const label = document.querySelector(`label[for="${radioInput.value}"]`);
        const currentValue = select.querySelector(".select__selected-value");
        currentValue.textContent = label.textContent;
        select.classList.remove("active");
        select.classList.add("unactive");
        button.setAttribute("aria-expanded", "false");
      });
    });
  })
  .catch(error => console.error("Ошибка:", error));
}

radios.forEach(radio => {
  radio.addEventListener("change", (evt) => {
    const id = evt.target.value;
    updateSectionList(id);
  });
});
