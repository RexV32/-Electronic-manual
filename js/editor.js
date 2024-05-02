import EditorJS from "./editorJs/editorjs/dist/editorjs.mjs";
import Embed from "./editorJs/embed/dist/embed.mjs";
import Header from "./editorJs/header/dist/header.mjs";
import ImageTool from "./editorJs/image/dist/image.mjs";
import inlineCode from "./editorJs/inline-code/dist/inline-code.mjs";
import Marker from "./editorJs/marker/dist/marker.mjs";
import nestedList from "./editorJs/nested-list/dist/nested-list.mjs";
import Quote from "./editorJs/quote/dist/quote.mjs";
import Table from "./editorJs/table/dist/table.mjs";
import Paragraph from "./editorJs/paragraph/dist/paragraph.mjs";

const editor = new EditorJS({
  holder: "editor",
  tools: {
    underline: Underline,
    footnotes: {
      class: FootnotesTune,
    },
    table: {
      class: Table,
      inlineToolbar: true,
      withHeadings: true,
      config: {
        rows: 2,
        cols: 3,
      },
      tunes: ['footnotes'],
    },
    quote: {
      class: Quote,
      inlineToolbar: true,
      config: {
        quotePlaceholder: 'Текст цитаты',
        captionPlaceholder: 'Автор цитаты',
      },
      tunes: ['footnotes'],
    },
    list: {
      class: nestedList,
      inlineToolbar: true,
      config: {
        defaultStyle: 'ordered'
      },
      tunes: ['footnotes'],
    },
    Marker: {
      class: Marker,
      shortcut: 'CTRL+2',
      tunes: ['footnotes'],
    },
    inlineCode: {
      class: inlineCode,
      shortcut: 'CTRL+1',
      tunes: ['footnotes'],
    },
    image: {
      class: ImageTool,
      config: {
        endpoints: {
          byFile: '../server/upload-image.php',
        }
      },
    },
    Header: {
      class: Header,
      inlineToolBar: true,
      config: {
        placeholder: 'Введите заголовок',
        levels: [1, 2, 3, 4, 5, 6],
        defaultLevel: 1
      },
      tunes: ['footnotes'],
    },
    paragraph: {
      class: Paragraph,
      inlineToolbar: true,
      tunes: ['footnotes'],
    },
    embed: {
      class: Embed,
      inlineToolBar: true,
      config: {
        services: {
          youtube: true,
          coub: true
        }
      },
    },
    attaches: {
      class: AttachesTool,
      config: {
        buttonText: "Выберите файл для загрузки",

        uploader: {
          async uploadByFile(file) {
            const formData = new FormData();
            formData.append("file", file);

            const response = await fetch("../server/upload-file.php", {
              method: "POST",
              body: formData,
            });
            const data = await response.json();
            if (data.success) {
              return {
                success: 1,
                file: {
                  url: data.file.url,
                  size: file.size,
                  name: file.name,
                  title: file.name,
                  extension: file.name.split('.').pop()
                }
              };
            }
          },
        },
      },
    },
  }
});

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
      window.location.href = `sub-sections.php`;
    }
  };

  buttons.forEach((button) => {
    button.addEventListener("click", closeModal);
  });
}

const saveButton = document.querySelector(".main__form-button");
const input = document.querySelector(".main__input");

saveButton.addEventListener("click", (evt) => {
  evt.preventDefault();
  editor.save().then((outputData) => {
    const jsonData = JSON.stringify(outputData, null, 2);
    const radioButtonDiscipline = document.querySelector("select[name='discipline']");
    const radioButtonSection = document.querySelector("select[name='section']");
    const idDiscipline = radioButtonDiscipline.value ? radioButtonDiscipline.value : null;
    const idSection = radioButtonSection.value ? radioButtonSection.value : null;
    const name = input.value.trim();
    if (idDiscipline === null || idSection === null) {
      modal("Дисциплина или раздел не выбран", "Ошибка");
      return;
    }
    if (name === "") {
      modal("Заполните поле с наименованием", "Ошибка");
      return;
    }
    if (outputData.blocks.length === 0) {
      modal("Поле с основным содержимым подраздела не заполнено", "Ошибка");
      return;
    }
    const data = new FormData();
    data.append("idDiscipline", idDiscipline);
    data.append("idSection", idSection);
    data.append("name", name);
    data.append("content", jsonData);
    fetch("../server/send-data.php", {
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
          modal("Подраздел успешно создан", "Успешно", true);
        }
        else {
          modal(data.message, "Ошибка");
        }
      })
      .catch(error => {
        modal(error, "Ошибка");
      });
  });
});