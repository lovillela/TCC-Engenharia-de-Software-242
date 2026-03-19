<?php ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">

<div id="quill-container" style="height: 300px; background: #fff; border-radius: 4px;">
  <?php echo isset($blogPost) ? $blogPost : ''; ?>
</div>

<input type="hidden" name="blogPost" id="quill-input">

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
  let quill = new Quill('quill-container', {
    theme: 'snow',
    placeholder: 'Seu texto aqui',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'clean', 'code-block']
        ]
    }
  });

  let quillForm = document.getElementById('quill-container').closest('form');

  if (quillForm) {
    quillForm.addEventListener('submit', function(){
      let quillInput = document.getElementById('quill-input');
      quillInput.value = quill.getSemanticHTML();
    });
  }
</script>
