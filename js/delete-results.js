const buttons = document.querySelectorAll(".user-list__link");
const pageBody = document.body;
const searchButton = document.querySelector(".user-list__submit--search");
const templateModal = `<div class="modal-accept modal">
<div class="modal-accept__wrapper">
  <p class="modal-accept__text">Вы точно хотите удалить?</p>
  <button class="modal-accept__button" type="button">Да</button>
  <button class="modal-accept__button modal-accept__button--cansel" type="button">Нет</button>
</div>
</div>`;
const url = window.location.search.replace('?', '');
const resetButton = document.querySelector(".user-list__submit--reset");
let acceptButton, canselButton, id;

function cansel() {
    acceptButton.removeEventListener("click", accept);
    canselButton.removeEventListener("click", cansel);
    document.querySelector(".modal").remove();
}

function accept() {
    document.querySelector(".modal").remove();
    const data = new FormData();
    data.append("id", id);
    fetch("../server/delete-results.php", {
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
                modal("Результат пользователя успешно удален", "Успешно", true)
            }
            else {
                modal(data.message, "Ошибка");
            }
        })
        .catch(error => {
            modal(data.message, "Ошибка");
        });
}

function modal(error, title, isSuccess = false) {
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
            window.location.href = `results.php?${url}`;
        }
    };

    buttons.forEach((button) => {
        button.addEventListener("click", closeModal);
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

resetButton.addEventListener("click", () => {
    window.location.href = "results.php";
});

searchButton.addEventListener("click", (evt) => {
    evt.preventDefault();
    let params = new URLSearchParams(window.location.search);
    let id = params.get('id');
    const dataForm = {
        name: document.querySelector("input[name='name']").value.trim(),
        surname: document.querySelector("input[name='surname']").value.trim(),
        patronymic: document.querySelector("input[name='patronymic']").value.trim(),
        login: document.querySelector("input[name='login']").value.trim(),
        group: document.querySelector(".selectpicker").value.trim(),
        idTest: id
    };
    const data = new FormData();
    data.append("data", JSON.stringify(dataForm, null, 2));
    fetch("../server/search-results.php", {
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
                const tbody = document.querySelector("tbody");
                const table =  document.querySelector(".user-list__table");
                const stubText = document.querySelector(".main__stub-text");
                if(stubText) {
                  stubText.remove();
                }
                table.classList.remove("user-list__table--none");
                tbody.innerHTML = "";
                let content = "";
                const results = data.results;
                if (results.length > 0) {
                    results.forEach((result) => {
                        content += `
                    <tr class="user-list__tr">
                        <td class="user-list__td" data-label="Группа">${result.GroupName}</td>
                        <td class="user-list__td" data-label="Логин">${result.Login}</td>
                        <td class="user-list__td" data-label="ФИО">
                            ${result.Surname}
                            ${result.Name}
                            ${result.Patronymic}
                        </td>
                        <td class="user-list__td" data-label="Score">${result.Score}</td>
                        <td class="user-list__td" data-label="Управление">
                            <ul class="user-list__control-list">
                                <li class="user-list__control-item" title="Удалить">
                                    <button class="user-list__link" data-id="${result.Id}">
                                        <svg class="user-list__icon" width="28" height="28" role="img" aria-label="delete">
                                            <use xlink:href="../image/icons/sprite.svg#deleteIcon"></use>
                                        </svg>
                                        <span class="visually-hidden">Удалить</span>
                                    </button>
                                </li>
                            </ul>
                        </td>
                    </tr>`;
                    });
                    tbody.innerHTML = content;
                }
                else {
                    const table =  document.querySelector(".user-list__table");
                    table.classList.add("user-list__table--none");
                    const form = document.querySelector(".user-list__form");
                    const template = `<p class="main__stub-text">Результаты не найденый</p>`;
                    form.insertAdjacentHTML("afterend", template);
                }
            }
            else {
                modal(data.message, data.title);
            }
        })
        .catch(error =>
            modal(error, "Ошибка")
        );
});