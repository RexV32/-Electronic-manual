const fileField = document.querySelector("input[type='file']");
const image = document.querySelector(".form-question__image");
const buttonDeleteFile = document.querySelector(".form-question__button");
const addQuestionButton = document.querySelector("#add-question");
const submit = document.querySelector(".form-question__submit");
const templateRadioButton = `
<p class="form-question__group-answer">
<label class="form-question__label-answer">
  <input class="form-question__radio" type="radio" name="answer-control">
  <span class="form-question__control">
    <input class="form-question__answer-input" type="text" placeholder="Ответ" name="answer">
  </span>
</label>
<button class="form-question__button-delete" type="button"></button>
</p>
`;
const templateCheckboxButton = `
<p class="form-question__group-answer">
<label class="form-question__label-answer">
  <input class="form-question__checkbox" type="checkbox" name="answer-control">
  <span class="form-question__control-checkbox">
    <input class="form-question__answer-input" type="text" placeholder="Ответ" name="answer-text">
  </span>
</label>
<button class="form-question__button-delete" type="button"></button>
</p>
`;
const optionAnswer = document.querySelectorAll(".form-question__radio[name='option-answer']");
const FILE_TYPES = ['webp', 'jpg', 'jpeg', 'png', 'gif'];
let file;
let count = 0;

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

function handleButtonRemove() {
  const buttons = document.querySelectorAll(".form-question__button-delete");
  buttons.forEach((button) => {
    button.addEventListener("click", (evt) => {
      const parentNode = evt.target.parentNode;
      parentNode.remove();
    });
  });
}

handleButtonRemove()

fileField.addEventListener("change", () => {
  file = fileField.files[0];
  const fileName = file.name.toLowerCase();
  const isFileTypeValid = FILE_TYPES.some((type) => fileName.endsWith(type));

  if (isFileTypeValid) {
    image.classList.remove("form-question__image--none");
    buttonDeleteFile.classList.remove("form-question__button--none");
    image.src = URL.createObjectURL(file);
  }
});

buttonDeleteFile.addEventListener("click", () => {
  image.src = "";
  file = "";
  image.classList.add("form-question__image--none");
  buttonDeleteFile.classList.add("form-question__button--none");
});

addQuestionButton.addEventListener("click", () => {
  const answers = document.querySelectorAll(".form-question__group-answer");
  let lastAnswer;
  if (answers.length === 0) {
    lastAnswer = document.querySelector(".form-question__question-input");
  } else {
    lastAnswer = answers[answers.length - 1];
  }
  const inputOptionRadio = document.querySelectorAll(".form-question__radio[name='option-answer']");
  let currentValue;
  inputOptionRadio.forEach((input) => {
    if (input.checked) {
      currentValue = input.id;
    }
  });
  if (currentValue === "radio") {
    lastAnswer.insertAdjacentHTML("afterend", templateRadioButton);
  } else {
    lastAnswer.insertAdjacentHTML("afterend", templateCheckboxButton);
  }
  handleButtonRemove()
});

optionAnswer.forEach((answer) => {
  answer.addEventListener("change", (evt) => {
    const value = evt.target.id;
    const answers = document.querySelectorAll(".form-question__group-answer");
    answers.forEach((answer) => {
      if (value === "checkbox") {
        const input = answer.querySelector(".form-question__radio");
        const control = answer.querySelector(".form-question__control");
        input.type = "checkbox";
        input.classList.remove("form-question__radio");
        input.classList.add("form-question__checkbox");
        control.classList.remove("form-question__control");
        control.classList.add("form-question__control-checkbox");
      } else {
        const input = answer.querySelector(".form-question__checkbox");
        const control = answer.querySelector(".form-question__control-checkbox");
        input.type = "radio";
        input.classList.add("form-question__radio");
        input.classList.remove("form-question__checkbox");
        control.classList.add("form-question__control");
        control.classList.remove("form-question__control-checkbox");
      }
    });
  });
});

submit.addEventListener("click", (evt) => {
  evt.preventDefault();

  const questionData = {
    questions: []
  };

  const idTest = document.querySelector(".selectpicker").value;
  if(idTest === '') {
    modal("Тест не выбран", "Ошибка");
    return;
  }

  const options = document.querySelectorAll(".form-question__radio[name='option-answer']");
  let multipleOption = false;
  options.forEach((option) => {
    if (option.checked) {
      multipleOption = option.id === "checkbox";
    }
  });

  const questionText = document.querySelector(".form-question__question-input").value;
  if(questionText.length === 0) {
    modal("Вы не ввели текст вопроса", "Ошибка");
    return;
  }
  const answers = document.querySelectorAll(".form-question__group-answer");

  answers.forEach((answer) => {
    const input = multipleOption ? answer.querySelector(".form-question__checkbox") : answer.querySelector(".form-question__radio");
    const isCorrectAnswer = input.checked ? true : false;
    if(isCorrectAnswer) {
      count++;
    }
    const answerText = answer.querySelector(".form-question__answer-input").value;
    if(answerText.length === 0) {
      modal("Вы не ввели текст ответа", "Ошибка");
      return;
    }
    const question = { answer: answerText, correct: isCorrectAnswer };
    questionData.questions.push(question);
  });
  questionData.id = idTest;
  questionData.text = questionText;
  questionData.multiple = multipleOption;
  if(count === 0) {
    modal("Вы не отметили правильный ответ", "Ошибка");
  }
  const data = new FormData();
  const jsonData = JSON.stringify(questionData, null, 2);
  data.append("data", jsonData);
  data.append("file", file);
  fetch("../server/add-questions.php", {
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
          window.location.href = "question-list.php";
      }
      else {
          modal(data.message, "Ошибка");
      }
  })
  .catch(error => {
      modal(error, "Ошибка");
  });
});
