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

function closeModalError() {
    const buttonError = document.querySelector(".modal-error__button");
    const modalError = document.querySelector(".modal-error");
    buttonError.removeEventListener("click", closeModalError);
    modalError.remove();  
}

function modalError(error) {
    const modal = document.querySelector(".modal-accept");
    modal.remove();
    const templateModalError = `<div class="modal-error">
<div class="modal-error__wrapper">
  <p class="modal-error__title">Произошла ошибка</p>
  <p class="modal-error__text-error">${error}</p>
  <button class="modal-error__button" type="button">Ок</button>
</div>
    </div>`;
    pageBody.insertAdjacentHTML("beforeend", templateModalError);
    const buttonError = document.querySelector(".modal-error__button");
    buttonError.addEventListener("click", closeModalError);
}

function cansel() {
    const modal = document.querySelector(".modal-accept");
    acceptButton.removeEventListener("click", accept);
    canselButton.removeEventListener("click", cansel);
    modal.remove();
}

function accept() {
    const data = new FormData();
    data.append("id", id);
    fetch("../server/delete-group.php", {
        method:"POST",
        body: data
    })
    .then(response => {
        if(response.ok) {
            return response.json();
        }
        else {
            throw new Error("Не удалось выполнить запрос");
        }
    })
    .then(data => {
        if(data.success) {
            window.location.href = "group.php";
        }
        else {
            modalError(data.message); 
        }
    })
    .catch(error => {
        modalError(error);
    });
}

function findClosestButton(element) {
    let currentElement = element;
    while (currentElement) {
        if (currentElement.tagName === 'BUTTON') {
            return currentElement;
        }
        currentElement = currentElement.parentElement;
    }

    return null;
}

function modalOpen(evt) {
    const closestButton = findClosestButton(evt.target);
    if (closestButton) {
        pageBody.insertAdjacentHTML("beforeend", templateModal);
        acceptButton = document.querySelector(".modal-accept__button");
        canselButton = document.querySelector(".modal-accept__button--cansel");
        acceptButton.addEventListener("click", accept);
        canselButton.addEventListener("click", cansel);
        id = closestButton.dataset.id;
    }
}

buttons.forEach((button) => {
    button.addEventListener("click", modalOpen);
});