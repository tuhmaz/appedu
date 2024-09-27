import 'summernote/dist/summernote-lite.min.css';
import 'summernote/dist/summernote-lite.min.js';
import 'summernote/dist/lang/summernote-ar-AR.js';
import axios from 'axios';

$(document).ready(function() {
  $('#summernote').summernote({
    height: 300,
    lang: 'ar-AR', // تعيين اللغة إلى العربية
    popover: {
      image: [
        ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
        ['float', ['floatLeft', 'floatRight', 'floatNone']],
        ['remove', ['removeMedia']]
      ],
      link: [
        ['link', ['linkDialogShow', 'unlink']]
      ],
      table: [
        ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
        ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
      ],
      air: [
        ['color', ['color']],
        ['font', ['bold', 'underline', 'clear']],
        ['para', ['ul', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture']]
      ]
    },
    buttons: {
      // زر مخصص لإدراج نص
      myButton: function(context) {
        var ui = $.summernote.ui;
        var button = ui.button({
          contents: '<i class="fa fa-child"/> My Button',
          tooltip: 'My Custom Button',
          click: function() {
            context.invoke('editor.insertText', 'Hello World!');
          }
        });
        return button.render();
      }
    },
    // رفع الصور
    callbacks: {
      onImageUpload: function(files) {
        if (files.length > 0) {
          uploadImage(files[0]);
        }
      }
    }
  });
});

// وظيفة رفع الصورة
function uploadImage(file) {
  let data = new FormData();
  data.append("file", file);

  axios.post('/upload-image', data, {
    headers: {
      'Content-Type': 'multipart/form-data' 
    },
    withCredentials: true
  })
  .then(response => {
    if (response.status === 200 && response.data.url) {
      let fileName = file.name;
      // إدراج الصورة في المحرر
      $('#summernote').summernote('insertImage', response.data.url, function($image) {
        $image.attr('alt', fileName);
      });
    } else {
      alert('Failed to upload the image. Please try again.');
    }
  })
  .catch(error => {
    console.error('Image upload failed:', error);
    alert('An error occurred while uploading the image. Please check your connection or try again later.');
  });
}
