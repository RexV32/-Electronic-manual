const submit = document.querySelector(".main__form-button");

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
        submit.disabled = false;
        modal = document.querySelector(".modal");
        modal.remove();
    };

    buttons.forEach((button) => {
        button.addEventListener("click", closeModal);
    });
}

submit.addEventListener("click", (evt) => {
    evt.preventDefault();
    submit.disabled = true;
    const name = document.querySelector(".main__input").value.trim();
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const refer = urlParams.get('section');
    const data = new FormData();
    data.append("id", id);
    data.append("name", name);
    data.append("refer", refer);
    fetch("../server/rename.php", {
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
                const refer = data.url;
                let url = "";
                switch (refer) {
                    case "discipline":
                        url = "disciplines.php";
                        break;
                    case "section":
                        url = "sections.php";
                        break;
                    case "test":
                        url = "test-list.php";
                        break;
                    default:
                        modal("Не удалось перенаправить на другую страницу", "Ошибка");
                        return;
                }
                window.location.href = url;
            }
            else {
                modal(data.message, "Ошибка");
            }
        })
        .catch(error => {
            modal(error, "Ошибка");
        });
});