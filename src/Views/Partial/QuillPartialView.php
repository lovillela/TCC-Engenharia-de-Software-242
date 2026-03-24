<?php ?>

<link rel="stylesheet" href="/assets/css/quill.snow.css">

<div id="quill-container" style="height: 300px; background: #fff; border-radius: 4px;">
  <?php echo isset($blogPost) ? $blogPost : ''; ?>
</div>

<input type="hidden" name="blogPost" id="quill-input">

<script src="/assets/js/quill.min.js"> </script>
<script src="/assets/js/quill-init.js"> </script>
