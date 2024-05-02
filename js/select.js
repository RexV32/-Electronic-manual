const select = document.querySelector(".select--discipline");

function modal(error, title) {
  const pageBody = document.body;
  const templateModal = `<div class="modal">
      <div class="modal__wrapper">
          <p class="modal__title">${title}</p>
          <p class="modal__text-error">${error}</p>
          <button class="modal__button" type="button">Ок</button>
      </div>
  </div>`;
  
  pageBody.insertAdjacentHTML("beforeend", templateModal);
  
  let modal = document.querySelector(".modal");
  const buttons = document.querySelectorAll(".modal__button");

  const closeModal = () => {
      modal = document.querySelector(".modal");
      modal.remove();
  };
  
  buttons.forEach((button) => {
      button.addEventListener("click", closeModal);
  });
}

select.addEventListener("change", (evt) => {
  const id = evt.target.value;
  const data = new FormData();
  data.append("id", id);
  fetch("../server/get-section.php", {
    method: "POST",
    body: data
  })
    .then(response => response.json())
    .then(data => {
      const menuSection = document.querySelector(".select--sections");
      let html = `<div class="dropdown bootstrap-select select select--padding select--sections" style="width: 300px;"><select class="selectpicker select select--padding select--sections" data-width="300px" data-live-search="true" data-size="5" title="Разделы" name="section" tabindex="-98">`;
      let isFirst = true;
      data.forEach(value => {
        const selected = isFirst? "selected=''":"";
        html += `<option value="${value.Id}" ${selected}>${value.Name}</option>`;
        isFirst = false;
      });
      html += `</select>
      <button type="button" class="btn dropdown-toggle btn-light" data-toggle="dropdown" role="combobox" aria-owns="bs-select-2" aria-haspopup="listbox" aria-expanded="false" title="Теория">
        <div class="filter-option">
          <div class="filter-option-inner">
              <div class="filter-option-inner-inner">dadada</div>
          </div>
        </div>
      </button>
      <div class="dropdown-menu ">
      <div class="bs-searchbox">
        <input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-2" aria-autocomplete="list">
      </div>
      <div class="inner show" role="listbox" id="bs-select-2" tabindex="-1">
         <ul class="dropdown-menu inner show" role="presentation"></ul>
      </div>
      </div>
      </div>`;
      const menuDescripline = document.querySelector(".select--discipline");
      menuDescripline.insertAdjacentHTML("afterend", html);
      menuSection.remove();
      let removeNode = document.querySelector(".select--sections");
      const moveNode = removeNode.querySelector(".select--sections");
      menuDescripline.insertAdjacentElement("afterend", moveNode);
      removeNode.remove();
      $('.select--sections').selectpicker('refresh');
    })
    .catch(error => modal(error, "Ошибка"));
});