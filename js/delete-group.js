const buttons = document.querySelectorAll(".main__panel-button");
const pageBody = document.querySelector(".page__body");
const templateModal = `<div class="modal-accept">
<div class="modal-accept__wrapper">
  <p class="modal-accept__text">Вы точно хотите удалить?</p>
  <button class="modal-accept__button" type="button">Да</button>
  <button class="modal-accept__button modal-accept__button--cansel" type="button">Нет</button>
</div>
</div>`;
let acceptButton, canselButton, id;

function modal(error, title, isSuccess = false) {
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
        if (isSuccess) {
            window.location.href = `group.php`;
        }
    };
    
    buttons.forEach((button) => {
        button.addEventListener("click", closeModal);
    });
}

function cansel() {
    const modal = document.querySelector(".modal-accept");
    acceptButton.removeEventListener("click", accept);
    canselButton.removeEventListener("click", cansel);
    modal.remove();
}

function accept() {
    document.querySelector(".modal-accept").remove();
    const data = new FormData();
    data.append("id", id);
    fetch("../server/delete-group.php", {
        method: "POST",
        body: data
    })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            else {
                throw new Error("Не удалось выполнить запрос");
            }
        })
        .then(data => {
            if (data.success) {
                modal(data.message, "Успешно", true);
            }
            else {
                modal(data.message, "Ошибка");
            }
        })
        .catch(error => {
            modal(error, "Ошибка");
        });
}

buttons.forEach((button) => {
    button.addEventListener("click", () => {
        id = button.dataset.id;
        pageBody.insertAdjacentHTML("beforeend", templateModal);
        acceptButton = document.querySelector(".modal-accept__button");
        canselButton = document.querySelector(".modal-accept__button--cansel");
        acceptButton.addEventListener("click", accept);
        canselButton.addEventListener("click", cansel);
    });
});