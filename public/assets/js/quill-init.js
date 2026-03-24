let quill = new Quill('#quill-container', {
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