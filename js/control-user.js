const buttonsChange = document.querySelectorAll(".user-list__button--change");
const buttonsDelete = document.querySelectorAll(".user-list__button--delete");
const pageBody = document.querySelector(".page__body");
const resetButton = document.querySelector(".user-list__submit--reset");
const searchButton = document.querySelector(".user-list__submit--search");
const templateChangeModal = `
  <div class="modal-change modal">
    <div class="modal-change__wrapper">
      <p class="modal-change__text">Изменить пароль пользователя</p>
      <label class="modal-change__label">
        Новый пароль
        <input class="modal-change__input" type="password">
        <span class="modal-change__message"></span>
      </label>
      <label class="modal-change__label">
        Повторите пароль
        <input class="modal-change__input modal-change__input--confirm" type="password">
        <span class="modal-change__message"></span>
      </label>
      <div class="modal-change__wrapper-button">
        <button class="modal-change__button modal-change__button--accept" type="button">Изменить</button>
        <button class="modal-change__button modal-change__button--cancel" type="button">Отмена</button>
      </div>
    </div>
  </div>
`;
const templateDeleteModal = `
<div class="modal-accept modal">
<div class="modal-accept__wrapper">
  <p class="modal-accept__text">Вы точно хотите удалить?</p>
  <button class="modal-accept__button" type="button">Да</button>
  <button class="modal-accept__button modal-accept__button--cancel" type="button">Нет</button>
</div>
</div>
`;

function showError(input, message) {
  input.classList.add("modal-change__input--error");
  input.nextElementSibling.innerHTML = message;
}

function clearError(input) {
  input.classList.remove("modal-change__input--error");
  input.nextElementSibling.innerHTML = "";
}

function removeModal() {
  const modal = document.querySelector(".modal");
  modal.remove();
}

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
      window.location.href = `users.php`;
    }
  };

  buttons.forEach((button) => {
    button.addEventListener("click", closeModal);
  });
}

function handleChangeButtonClick(button) {
  const id = button.dataset.id;
  pageBody.insertAdjacentHTML("beforeend", templateChangeModal);

  const modalChange = document.querySelector(".modal-change");
  const inputs = modalChange.querySelectorAll(".modal-change__input");
  const cancelButton = document.querySelector(".modal-change__button--cancel");
  const acceptButton = document.querySelector(".modal-change__button--accept");

  inputs.forEach((input) => {
    input.addEventListener("input", () => {
      const value = input.value.length;
      if (value >= 10) {
        clearError(input);
      } else {
        showError(input, "Длина пароля минимум 10 символов");
      }
    });
  });

  cancelButton.addEventListener("click", removeModal);

  acceptButton.addEventListener("click", () => {
    const password = inputs[0].value;
    const passwordConfirm = inputs[1].value;

    if (password === passwordConfirm) {
      removeModal();
      const data = new FormData();
      data.append("id", id);
      data.append("password", password);
      data.append("passwordConfirm", passwordConfirm);
      fetch("../server/change-password.php", {
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
            modal(data.message, data.title, true);
          }
          else {
            modal(data.message, data.title);
          }
        })
        .catch(error => {
          modal(error, "Ошибка");
        });
    } else {
      showError(inputs[0], "");
      showError(inputs[1], "Пароли не совпадают");
    }
  });
}

function handleDeleteButtonClick(button) {
  const id = button.dataset.id;
  pageBody.insertAdjacentHTML("beforeend", templateDeleteModal);

  const cancelButton = document.querySelector(".modal-accept__button--cancel");
  const acceptButton = document.querySelector(".modal-accept__button");

  cancelButton.addEventListener("click", removeModal);
  acceptButton.addEventListener("click", () => {
    removeModal();
    const data = new FormData();
    data.append("id", id);
    fetch("../server/delete-user.php", {
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
          modal(data.message, data.title, true);
        }
        else {
          modal(data.message, data.title);
        }
      })
      .catch(error => {
        modal(error, "Ошибка");
      });
  });
}

buttonsChange.forEach((button) => {
  button.addEventListener("click", () => handleChangeButtonClick(button));
});

buttonsDelete.forEach((button) => {
  button.addEventListener("click", () => handleDeleteButtonClick(button));
});

resetButton.addEventListener("click", () => {
  window.location.href = "users.php";
});

searchButton.addEventListener("click", (evt) => {
  evt.preventDefault();
  const dataForm = {
    name: document.querySelector("input[name='name']").value.trim(),
    surname: document.querySelector("input[name='surname']").value.trim(),
    patronymic: document.querySelector("input[name='patronymic']").value.trim(),
    login: document.querySelector("input[name='login']").value.trim(),
    group: document.querySelector(".selectpicker").value.trim()
  };
  const data = new FormData();
  data.append("data", JSON.stringify(dataForm, null, 2));
  fetch("../server/search-users.php", {
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
        const users = data.users;
        if (users.length > 0) {
          users.forEach((user) => {
            content += `<tr class="user-list__tr">
          <td class="user-list__td" data-label="Группа">${user.NameGroup}</td>
          <td class="user-list__td" data-label="ФИО">
            ${user.Surname}
            ${user.Name}
            ${user.Patronymic}
          </td>
          <td class="user-list__td" data-label="Логин">${user.Login}</td>
          <td class="user-list__td" data-label="Управление">
            <ul class="user-list__control-list">
              <li class="user-list__control-item" title="Изменить пароль">
                <button class="user-list__button user-list__button--change" type="button" data-id="${user.Id}">
                  <svg class="user-list__icon" width="29" height="29" role="img" aria-label="edit">
                    <use xlink:href="../image/icons/sprite.svg#editIcon"></use>
                  </svg>
                  <span class="visually-hidden">Изменить пароль</span>
                </button>
              </li>
              <li class="user-list__control-item" title="Удалить">
                <button class="user-list__button user-list__button--delete" type="button" data-id="${user.Id}">
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
          const template = `<p class="main__stub-text">Пользователи не найдены</p>`;
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