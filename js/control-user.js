const buttonsChange = document.querySelectorAll(".user-list__button--change");
const buttonsDelete = document.querySelectorAll(".user-list__button--delete");
const pageBody = document.querySelector(".page__body");
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

function closeModalError() {
  const buttonError = document.querySelector(".modal-error__button");
  const modalError = document.querySelector(".modal-error");
  buttonError.removeEventListener("click", closeModalError);
  modalError.remove();  
}

function modalError(error) {
  const modal = document.querySelector(".modal");
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
      const data = new FormData();
      data.append("id", id);
      data.append("password", password);
      data.append("passwordConfirm", passwordConfirm);
      fetch("server/change-password.php", {
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
            window.location.href = "users.php";
          }
          else {
            modalError(data.message);
          }
        })
        .catch(error => {
          modalError(error);
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
    const data = new FormData();
    data.append("id", id);
    fetch("server/delete-user.php", {
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
          window.location.href = `users.php`;
        }
        else {
          modalError(data.message);
        }
      })
      .catch(error => {
        modalError(error);
      });
  });
}

buttonsChange.forEach((button) => {
  button.addEventListener("click", () => handleChangeButtonClick(button));
});

buttonsDelete.forEach((button) => {
  button.addEventListener("click", () => handleDeleteButtonClick(button));
});