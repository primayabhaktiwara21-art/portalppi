<?php
include '../koneksi.php';
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Artikel | Smart_tekno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f9f9f9;
      font-family: "Inter", "Helvetica Neue", Arial, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    .btn {
      transition: all 0.2s ease-in-out;
    }
    .btn-primary:hover {
      background-color: #154b92;
      transform: translateY(-1px);
    }
    .btn-secondary:hover {
      background-color: #6c757d;
      transform: translateY(-1px);
    }
    h3 {
      font-weight: 700;
      color: #1e63c3;
      letter-spacing: -0.3px;
    }

pre code {
  display: block;
  background: #f4f6ff;
  color: #1a1a1a;
  padding: 14px 18px;
  border-radius: 10px;
  font-family: 'Courier New', monospace;
  border: 1px solid #e1e4f8;
  overflow-x: auto;
  font-size: 0.95rem;
}



    .container { max-width: 800px; margin-top: 40px; }
    .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
  </style>
</head>
<body>
<div class="container">
  <div class="card p-4">
    <h3 class="mb-4 fw-bold text-primary">Tambah Artikel</h3>
    <form method="POST" action="simpan.php" enctype="multipart/form-data">
      <input type="text" name="judul" class="form-control mb-3" placeholder="Judul" required>
      <select name="kategori" class="form-select mb-3" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="Teknologi">Teknologi</option>
          <option value="AI">AI</option>
          <option value="Gadget">Gadget</option>
          <option value="Coding">Coding</option>
          <option value="Internet">Internet</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      <input type="file" name="gambar" class="form-control mb-3">
      <textarea name="isi" id="editor" class="form-control" rows="10" placeholder="Isi artikel..."></textarea>
      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/code-block/code-block.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/source-editing/source-editing.js"></script>

<script>
ClassicEditor
  .create(document.querySelector('#editor'), {
    toolbar: [
      'heading', '|',
      'bold', 'italic', 'link', 'bulletedList', 'numberedList',
      'blockQuote', 'insertTable', '|',
      'codeBlock', 'sourceEditing', '|',
      'undo', 'redo'
    ],
    heading: {
      options: [
        { model: 'paragraph', title: 'Paragraf', class: 'ck-heading_paragraph' },
        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
      ]
    },
    codeBlock: {
      languages: [
        { language: 'plaintext', label: 'Teks Biasa' },
        { language: 'html', label: 'HTML' },
        { language: 'css', label: 'CSS' },
        { language: 'javascript', label: 'JavaScript' },
        { language: 'php', label: 'PHP' },
        { language: 'sql', label: 'SQL' }
      ]
    }
  })
  .catch(error => console.error(error));
</script>
</body>
</html>
