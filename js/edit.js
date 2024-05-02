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

const submit = document.querySelector(".main__form-button");

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

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const id = urlParams.get('id');
const data = new FormData();
data.append("id", id);

fetch("../server/get-data.php", {
    method: "POST",
    body: data
})
    .then(response => response.json())
    .then(data => {
        const content = JSON.parse(data.Content);
        const editor = new EditorJS({
            holder: "editor",
            data: content,
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

        submit.addEventListener("click", (evt) => {
            evt.preventDefault();
            editor.save().then((outputData) => {
                const input = document.querySelector(".main__input");
                const name = input.value.trim();
                if (name === "") {
                    modalError("Заполните поле с наименованием");
                    return;
                }
                const jsonData = JSON.stringify(outputData, null, 2);
                const data = new FormData();
                data.append("id", id);
                data.append("content", jsonData);
                data.append("name", name);
                fetch("../server/update-subSection.php", {
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
                            modal("Данные успешно обновлены", "Успешно", true);
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
    })
    .catch(error => modal(`Не удалось получить содержимое подраздела ${error}`, "Ошибка"));