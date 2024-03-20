const submit = document.querySelector(".test__submit");
let test = [];
let unansweredQuestionsCounter = 0;

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
            window.location.href = `index.php`;
        }
    };
    
    buttons.forEach((button) => {
        button.addEventListener("click", closeModal);
    });
}

submit.addEventListener("click", (evt) => {
    evt.preventDefault();
    let array = [];
    const questionWrapper = document.querySelectorAll(".test__wrapper-question");
    questionWrapper.forEach((question) => {
        const TextQuestion = question.querySelector(".test__question").textContent;
        const idQuestion = question.querySelector(".test__question").dataset.id;
        const answers = question.querySelectorAll(".test__group-answer");
        answers.forEach((answer) => {
            const checked = answer.querySelector("input").checked;
            if (checked) {
                const idAnswer = answer.querySelector("input").dataset.id;
                const textAnswer = answer.querySelector(".test__answer").textContent;
                array.push({
                    id: idAnswer,
                    answer: textAnswer
                });
            }
        });
        if (array.length <= 0) {
            unansweredQuestionsCounter++;
            question.querySelector(".test__question").style.color = "red";
        }
        else {
            const questions = question.querySelectorAll(".test__question");
            questions.forEach((question) => {
                question.style.color = "";
            });
        }
        test.push({
            question: TextQuestion,
            questionId: idQuestion,
            answers: array
        });
        array = [];
    });
    if (unansweredQuestionsCounter > 0) {
        modal("Вы не ответили на все вопросы", "Ошибка");
        unansweredQuestionsCounter = 0;
        test = [];
        return;
    }
    const jsonData = JSON.stringify(test, null, 2);
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const data = new FormData();
    data.append("data", jsonData);
    data.append("id", id);
    console.log(jsonData);
    fetch("./server/check-test.php", {
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
                modal(data.result, data.title, true);
            }
            else {
                modal(data.message, data.title);
            }
        })
        .catch(error => {
            modal(error, "Произошла ошибка");
        });
});