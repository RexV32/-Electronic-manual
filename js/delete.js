const buttons = document.querySelectorAll(".main__panel-button--delete");
const pageBody = document.querySelector(".page__body");
const templateModal = `<div class="modal-accept">
<div class="modal-accept__wrapper">
  <p class="modal-accept__text">Вы точно хотите удалить?</p>
  <button class="modal-accept__button" type="button">Да</button>
  <button class="modal-accept__button modal-accept__button--cansel" type="button">Нет</button>
</div>
</div>`;
let acceptButton, canselButton, id, section;

function modal(error, title, section, isSuccess = false) {
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
            switch (section) {
                case "discipline":
                    window.location.href = "disciplines.php";
                    break;
                case "sections":
                    window.location.href = "sections.php";
                    break;
                case "subSection":
                    window.location.href = "sub-sections.php";
                    break;
                case "tests":
                    window.location.href = "test-list.php";
                    break;
                case "question":
                    window.location.href = "question-list.php";
                    break;
                default:
                    window.location.href = "admin.php";
                    break;
            }
        }
    };

    buttons.forEach((button) => {
        button.addEventListener("click", closeModal);
    });
}

function accept() {
    document.querySelector(".modal-accept").remove();
    const data = new FormData();
    data.append("id", id);
    data.append("section", section);
    console.log(id);
    fetch("../server/delete-section.php", {
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
                modal("Данные успешно удалены", "Успех", section, true);
            }
            else {
                modalError(data.message, "Ошибка");
            }
        })
        .catch(error => {
            modal(error, "Ошибка");
        });
}

function cansel() {
    const modal = document.querySelector(".modal-accept");
    acceptButton.removeEventListener("click", accept);
    canselButton.removeEventListener("click", cansel);
    modal.remove();
}

buttons.forEach((button) => {
    button.addEventListener("click", () => {
        id = button.dataset.id;
        section = button.dataset.section;
        pageBody.insertAdjacentHTML("beforeend", templateModal);
        acceptButton = document.querySelector(".modal-accept__button");
        canselButton = document.querySelector(".modal-accept__button--cansel");
        acceptButton.addEventListener("click", accept);
        canselButton.addEventListener("click", cansel);
    });
});