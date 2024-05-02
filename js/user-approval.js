const acceptButtons = document.querySelectorAll(".main__button-control--accept");
const cancelButtons = document.querySelectorAll(".main__button-control--cancel");
const acceptAllButton = document.querySelector(".main__accept-button");
const pageBody = document.querySelector(".page__body");

function handleButtonClick(evt, actionType) {
    const id = evt.target.dataset.id;
    userApproval(id, actionType);
}

acceptButtons.forEach((button) => {
    button.addEventListener("click", (evt) => handleButtonClick(evt, "accept"));
});

cancelButtons.forEach((button) => {
    button.addEventListener("click", (evt) => handleButtonClick(evt, "cancel"));
});

acceptAllButton.addEventListener("click", () => {
    userApproval(null, "allAccept");
});

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

function userApproval(id, action) {
    const data = new FormData();
    data.append("id", id);
    data.append("action", action);

    fetch("../server/approval.php", {
        method: "POST",
        body: data
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error("Не удалось выполнить запрос");
        }
    })
    .then(data => {
        if (data.success) {
            window.location.href = "user-approval.php";
        }
        else {
            modal(data.message, "Ошибка");
        }
    })
    .catch(error => {
        modal(error, "Ошибка");
    });
}