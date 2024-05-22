const fileField = document.querySelector("input[type='file']");
const image = document.querySelector(".form-question__image");
const buttonDeleteFile = document.querySelector(".form-question__button");
const addQuestionButton = document.querySelector("#add-question");
const submitCreate = document.querySelector(".form-question__submit--create");
const submitEdit = document.querySelector(".form-question__submit--edit");
const optionAnswer = document.querySelectorAll(".form-question__radio[name='option-answer']");

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
const FILE_TYPES = ['webp', 'jpg', 'jpeg', 'png', 'gif'];
let file = "";
let count = 0;
let isDeletePhoto = 0;
let oldPhoto = "";

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
    submitEdit.disabled = false;
    modal = document.querySelector(".modal");
    modal.remove();
    if (isSuccess) {
      window.location.href = `question-list.php`;
    }
  };

  buttons.forEach((button) => {
    button.addEventListener("click", closeModal);
  });
}

function isValidUrl(string) {
  try {
    const url = new URL(string);
    return url.protocol === 'blob:';
  } catch (err) {
    return false;
  }
}


function buttonRemove() {
  const buttons = document.querySelectorAll(".form-question__button-delete");
  buttons.forEach((button) => {
    button.addEventListener("click", (evt) => {
      const button = evt.target;
      const parentNode = button.parentNode;
      const input = parentNode.querySelector(".form-question__answer-input");
      const id = input.dataset.id;

      if (!input.classList.contains("form-question__answer-input--delete")) {
        if (id !== undefined) {
          input.classList.add("form-question__answer-input--delete");
        } else {
          parentNode.remove();
        }
      } else {
        input.classList.remove("form-question__answer-input--delete");
      }
    })
  });
}

function changeOnRadio(answer) {
  const input = answer.querySelector(".form-question__checkbox");
  const control = answer.querySelector(".form-question__control-checkbox");
  input.type = "radio";
  input.classList.add("form-question__radio");
  input.classList.remove("form-question__checkbox");
  control.classList.add("form-question__control");
  control.classList.remove("form-question__control-checkbox");
}

function changeOnCheckbox(answer) {
  const input = answer.querySelector(".form-question__radio");
  const control = answer.querySelector(".form-question__control");
  input.type = "checkbox";
  input.classList.remove("form-question__radio");
  input.classList.add("form-question__checkbox");
  control.classList.remove("form-question__control");
  control.classList.add("form-question__control-checkbox");
}

function onPageLoad() {
  if (image.src) {
    fileField.disabled = true;
    const label = document.querySelector(".form-question__label-button");
    label.classList.add("form-question__label-button--disabled");
  }
  buttonRemove();
}

onPageLoad();

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
  buttonRemove();
});

optionAnswer.forEach((answer) => {
  answer.addEventListener("change", (evt) => {
    const value = evt.target.id;
    const answers = document.querySelectorAll(".form-question__group-answer");
    answers.forEach((answer) => {
      if (value === "checkbox") {
        changeOnCheckbox(answer);
      } else {
        changeOnRadio(answer);
      }
    });
  });
});

fileField.addEventListener("change", () => {
  file = fileField.files[0];
  const fileName = file.name.toLowerCase();
  const isFileTypeValid = FILE_TYPES.some((type) => fileName.endsWith(type));

  if (isFileTypeValid) {
    image.classList.remove("form-question__image--none");
    buttonDeleteFile.classList.remove("form-question__button--none");
    fileField.disabled = true;
    const label = document.querySelector(".form-question__label-button");
    label.classList.add("form-question__label-button--disabled");
    image.src = URL.createObjectURL(file);
  }
  else {
    modal("Неверное разрешение фотографии", "Ошибка");
  }

  fileField.value = "";
});

buttonDeleteFile.addEventListener("click", () => {
  if (!isDeletePhoto) {
    oldPhoto = image.src;
    isDeletePhoto = 1;
  }
  fileField.disabled = false;
  const label = document.querySelector(".form-question__label-button");
  label.classList.remove("form-question__label-button--disabled");
  image.src = "";
  file = "";
  image.classList.add("form-question__image--none");
  buttonDeleteFile.classList.add("form-question__button--none");
});

submitEdit.addEventListener("click", (evt) => {
  evt.preventDefault();
  submitEdit.disabled = true;
  const questionData = {
    answers: []
  };
  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get('id');
  const questionText = document.querySelector(".form-question__question-input").value;
  const options = document.querySelectorAll(".form-question__radio[name='option-answer']");
  const answers = document.querySelectorAll(".form-question__group-answer");
  let multipleOption = false;

  options.forEach((option) => {
    if (option.checked) {
      multipleOption = option.id === "checkbox";
    }
  });
  if (questionText.length === 0) {
    modal("Введите текст вопроса", "Ошибка");
    return;
  }

  for (let i = 0; i < answers.length; i++) {
    const answer = answers[i];
    const input = multipleOption ? answer.querySelector(".form-question__checkbox") : answer.querySelector(".form-question__radio");
    const isCorrectAnswer = input.checked ? true : false;
    if (isCorrectAnswer) {
      count++;
    }
    const answerInput = answer.querySelector(".form-question__answer-input");
    const idAnswer = answerInput.dataset.id;
    const answerText = answerInput.value;
    if (answerText.length === 0) {
      modal("Введите текст ответа", "Ошибка");
      return;
    }
    const isDeleteAnswer = answerInput.classList.contains("form-question__answer-input--delete") ? true : false;
    const question = { answer: answerText, correct: isCorrectAnswer, id: idAnswer, isDelete: isDeleteAnswer };
    questionData.answers.push(question);
  };

  if (questionData.answers.length === 0) {
    modal("Добавьте ответы к вопросу", "Ошибка");
    return;
  }

  if (count <= 0) {
    modal("Отметье правильный ответ", "Ошибка");
    return;
  }

  if (isValidUrl(oldPhoto)) {
    oldPhoto = "";
    isDeletePhoto = 0;
  }
  questionData.id = id;
  questionData.text = questionText;
  questionData.multiple = multipleOption;
  const data = new FormData();
  const jsonData = JSON.stringify(questionData, null, 2);
  data.append("data", jsonData);
  data.append("file", file);
  data.append("currentPhoto", oldPhoto);
  data.append("isDeletePhoto", isDeletePhoto);
  fetch("../server/edit-questions.php", {
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
        modal("Вопрос успешно изменен", "Успешно", true);
      }
      else {
        modal(data.message, "Ошибка");
      }
    })
    .catch(error => {
      modal(error, "Ошибка");
    });
});