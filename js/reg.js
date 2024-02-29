const submit = document.querySelector(".reg__submit");
const inputsPassword = document.querySelectorAll("input[type='password']");
const pageBody = document.body;

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
            window.location.href = `auth.php`;
        }
    };
    
    buttons.forEach((button) => {
        button.addEventListener("click", closeModal);
    });
}

function displayErrors(data) {
    const spansError = document.querySelectorAll(".reg__input-error");
    const inputs = document.querySelectorAll(".reg__input");
    const select = document.querySelector(".select-reg");
    if(select.classList.contains("reg__input--error")) {
        select.classList.remove("reg__input--error");
    }
    inputs.forEach(input => {
        if (input.classList.contains("reg__input--error")) {
            input.classList.remove("reg__input--error");
        }
    });
    spansError.forEach((span) => {
        span.innerHTML = "";
    });
    console.log(data.errors);
    const fields = ["confirm","group","login","password","patronymic","surname", "name"];
    fields.forEach((value) => {
        let input = document.querySelector(`.reg__input--${value}`);
        if(value === "group") {
            input = document.querySelector(`.select-reg`);
        }
        console.log(data.errors.value);
        if(data.errors[value]) {
            const message = data.errors[value];
            input.classList.add("reg__input--error");
            input.nextElementSibling.innerHTML = message;
        }
    });
}

function showError(input, message) {
    input.classList.add("reg__input--error");
    input.nextElementSibling.innerHTML = message;
}

function clearError(input) {
    input.classList.remove("reg__input--error");
    input.nextElementSibling.innerHTML = "";
}

inputsPassword.forEach((input) => {
    input.addEventListener("input", () => {
        const value = input.value.length;
        if (value >= 10) {
            clearError(input);
        } else {
            showError(input, "Длина пароля минимум 10 символов");
        }
    });
});

submit.addEventListener("click", (evt) => {
    evt.preventDefault();
    const dataUser = {
        name: document.querySelector(".reg__input--name").value.trim(),
        surname: document.querySelector(".reg__input--surname").value.trim(),
        patronymic: document.querySelector(".reg__input--patronymic").value.trim(),
        login: document.querySelector(".reg__input--login").value.trim(),
        group: document.querySelector(".selectpicker").value.trim(),
        password: document.querySelector(".reg__input--password").value.trim(),
        confirm: document.querySelector(".reg__input--confirm").value.trim()
    };
    const data = new FormData();
    data.append("data", JSON.stringify(dataUser));
    fetch("./server/add-user.php", {
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
                console.log(data);
                modal(data.message, data.title, true);
            }
            else {
                modal(data.message, data.title);
                displayErrors(data);
            }
        })
        .catch(error => {
            modal(error, "Произошла ошибка");
        });
});