const submit = document.querySelector(".auth__submit");

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
        submit.disabled = false;
    };
    
    buttons.forEach((button) => {
        button.addEventListener("click", closeModal);
    });
}

submit.addEventListener("click", (evt) => {
    evt.preventDefault();
    evt.target.disabled = true;
    const dataUser = {
        login: document.querySelector(".auth__input[type='text']").value.trim(),
        password: document.querySelector(".auth__input[type='password']").value.trim(),
    };
    const data = new FormData();
    data.append("data", JSON.stringify(dataUser));
    fetch("./server/authentication.php", {
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
                window.location.href = "index.php";
            }
            else {
                modal(data.message, data.title);
            }
        })
        .catch(error => {
            modal(error, "Произошла ошибка");
        });
});