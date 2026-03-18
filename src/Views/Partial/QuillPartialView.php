<?php ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">

<div id="quill-container">

</div>

<input type="hidden" name="content" id="quill-input">

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
  var quill = new Quill('#quill-container', {
    theme: 'snow',
    placeholder: 'Escreva algo incrível...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'clean']
        ]
    }
  });

  let quillForm = document.getElementById('quill-container').closest('form');

  if (quillForm) {
    form.addEventListener('submit', function(){
      let quillInput = document.getElementById('quill-input');
      quillInput.value = quill.getSemanticHTML();
    });
  }
</script>
