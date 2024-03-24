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

const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get('id');
const data = new FormData();
data.append("id", id);
fetch("./server/get-data.php", {
  method: "POST",
  body: data
})
  .then(response => response.json())
  .then(data => {
    document.querySelector(".main__section-title").textContent = data.Name;
    const content = JSON.parse(data.Content);
    content.blocks.forEach((value) => {
      if (value.type === "image" || value.type === "attaches") {
        const url = value.data.file.url;
        value.data.file.url = url.substring(1);
      }
    })
    loadEditor(content);
  })
  .catch(error => console.error("Ошибка:", error));

function loadEditor(content) {
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
        class: AttachesTool
      },
    },

    data: content,
  })
}