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

function userApproval(id, action) {
    const data = new FormData();
    data.append("id", id);
    data.append("action", action);

    fetch("server/approval.php", {
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
            modalError(data.message);
        }
    })
    .catch(error => {
        modalError(error);
    });
}

function closeModalError() {
    const buttonError = document.querySelector(".modal-error__button");
    const modalError = document.querySelector(".modal-error");
    buttonError.removeEventListener("click", closeModalError);
    modalError.remove();  
}

function modalError(error) {
    const templateModalError = `
        <div class="modal-error">
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
