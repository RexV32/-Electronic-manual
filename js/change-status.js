const buttonsChange = document.querySelectorAll(".main__panel-button--change");

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

buttonsChange.forEach((button) => {
    button.addEventListener("click", () => {
        const dataSection = button.dataset;
        const jsonData = JSON.stringify(dataSection, null, 2);
        const data = new FormData();
        data.append("data", jsonData);
        fetch("../server/change-status.php", {
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
                const url = window.location.pathname.split("/").slice(-1)[0];
                window.location.href = url;
            }
            else {
                modal(data.message, data.title); 
            }
        })
        .catch(error => {
            modal(error.message, "Ошибка");
        });
    });
});