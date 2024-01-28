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
  readOnly: true,

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
      shortcut: 'CTRL+M',
      tunes: ['footnotes'],
    },
    inlineCode: {
      class: inlineCode,
      //shortcut: 'CTRL+Z',
      tunes: ['footnotes'],
    },
    image: {
      class: ImageTool,
      config: {
        endpoints: {
          byFile: 'php/upload.php',
        },
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
      class:Embed,
      inlineToolBar:true,
      config: {
          services: {
              youtube:true,
              coub:true
          }
      },
    },
    attaches: {
      class: AttachesTool,
      config: {
        //endpoint: 'upload.php',
        buttonText: "Выберите файл для загрузки",

        uploader: {
          async uploadByFile(file) {
              const formData = new FormData();
              formData.append("file", file);

              const response = await fetch("upload.php", {
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
                            name: file.size,
                            extension: file.name.split('.').pop()
                        }
                    };
                }
          },
        },
      },
    },
  },

  data: {
    "time": 1703243644210,
    "blocks": [
      {
        "id": "jsBH51x1Nk",
        "type": "Header",
        "data": {
          "text": "Заголовок 1",
          "level": 1
        },
        "tunes": {
          "footnotes": []
        }
      },
      {
        "id": "9Kf_cBjgAN",
        "type": "paragraph",
        "data": {
          "text": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget semper nulla. Integer posuere tempor nibh, eget aliquet eros laoreet vel. Cras nec mi ante. Integer in semper sem, sed dapibus tortor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nullam vel lorem eu mauris scelerisque lacinia vitae quis libero. Cras auctor, sem ultrices vehicula suscipit, arcu elit laoreet nibh, quis dapibus orci urna eget nunc. Quisque ac orci sit amet magna rhoncus mollis. Integer orci mi, pretium nec dapibus non, efficitur id magna. Suspendisse ultricies lectus id mauris molestie, non fringilla tortor semper. In id porttitor risus. Curabitur semper augue eget velit ornare hendrerit. Nullam pharetra massa et velit laoreet vulputate. Donec tincidunt libero vel dui varius eleifend. Quisque eget odio convallis, sollicitudin sapien sit amet, mollis tellus. Sed auctor laoreet erat, vulputate tempus ligula volutpat in. Praesent condimentum maximus ullamcorper. Quisque sit amet nulla dolor. Suspendisse faucibus orci ipsum. Quisque viverra lectus at fermentum commodo. Vivamus porta ipsum vitae risus porttitor pulvinar at et ex. Duis dignissim pellentesque nunc. Cras vestibulum, lorem vitae blandit placerat, justo tortor efficitur nisl, ac cursus massa nulla eu lectus. Sed bibendum nisi at purus ultrices laoreet. Nunc at sagittis ex. Aenean mollis commodo leo, egestas pulvinar eros dictum ut. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam finibus sodales volutpat. Integer cursus rhoncus pharetra. Ut non sem placerat, semper urna eget, auctor leo. Donec pellentesque consectetur lorem, quis euismod nibh consectetur sed. Proin fermentum nec libero auctor rutrum."
        },
        "tunes": {
          "footnotes": []
        }
      },
      {
        "id": "VrXPeEWenA",
        "type": "table",
        "data": {
          "withHeadings": false,
          "content": [
            [
              "<b>Заголовок</b>",
              "<b>Заголовок</b>",
              "<b>Заголовок</b>"
            ],
            [
              "Текст",
              "Текст",
              "Текст"
            ]
          ]
        },
        "tunes": {
          "footnotes": []
        }
      }
    ],
    "version": "2.28.2"
  },
})

/* const saveButton = document.querySelector(".button");

saveButton.addEventListener("click",() => {
  editor.save().then((outputData) => {
      console.log("Article data: ", outputData);
      const jsonData = JSON.stringify(outputData, null, 2);

      // Создание объекта Blob для записи данных
      const blob = new Blob([jsonData], { type: "application/json" });

      // Создание ссылки для скачивания файла
      const a = document.createElement("a");
      a.href = URL.createObjectURL(blob);
      a.download = "article.json";

      // Добавление ссылки на страницу и эмуляция клика для скачивания файла
      document.body.appendChild(a);
      a.click();

      // Удаление ссылки после завершения скачивания
      document.body.removeChild(a);
  }).catch((error) => {
      console.log("Saving failed: ", error);
  })
}) */
