tinymce.init({
  selector: '#tinymce-editor',
  plugins: 'image',
  toolbar1: 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | ' +
            'styleselect formatselect fontselect fontsizeselect',
  toolbar2: 'cut copy paste | bullist numlist outdent indent | link image media | forecolor backcolor emoticons | ' +
            'print preview code fullscreen | insertdatetime table hr pagebreak | charmap anchor toc codesample',
  toolbar3: 'searchreplace visualblocks visualchars nonbreaking template | ltr rtl | spellchecker',

  relative_urls: false,
  remove_script_host: false,
  document_base_url: '{{ url('/') }}',
  images_upload_url: '{{ route("upload.image") }}',
  automatic_uploads: true,
  file_picker_types: 'image',
  file_picker_callback: function (cb, value, meta) {
      var input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');

      input.onchange = function () {
          var file = this.files[0];
          var reader = new FileReader();
          reader.onload = function () {
              var id = 'blobid' + (new Date()).getTime();
              var blobCache = tinymce.activeEditor.editorUpload.blobCache;
              var base64 = reader.result.split(',')[1];
              var blobInfo = blobCache.create(id, file, base64);
              blobCache.add(blobInfo);

              cb(blobInfo.blobUri(), { title: file.name });
          };
          reader.readAsDataURL(file);
      };

      input.click();
  },
  images_upload_handler: function (blobInfo, success, failure) {
      var xhr, formData;
      xhr = new XMLHttpRequest();
      xhr.withCredentials = false;
      xhr.open('POST', '{{ route("upload.image") }}');
      xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
      xhr.onload = function () {
          var json;
          if (xhr.status != 200) {
              failure('HTTP Error: ' + xhr.status);
              return;
          }
          json = JSON.parse(xhr.responseText);
          if (!json || typeof json.location != 'string') {
              failure('Invalid JSON: ' + xhr.responseText);
              return;
          }
          success(json.location);
      };
      formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());
      xhr.send(formData);
  }
});
