<?php
include '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$q = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($q) == 0) {
  echo "Data tidak ditemukan";
  exit;
}
$row = mysqli_fetch_assoc($q);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Artikel | Smart_Tech</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f9f9f9;
      font-family: "Inter", "Helvetica Neue", Arial, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    .container { max-width: 800px; margin-top: 50px; }
    .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    h3 {
      font-weight: 700;
      color: #1e63c3;
      letter-spacing: -0.3px;
    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #dcdcdc;
    }
    .form-control:focus {
      border-color: #1e63c3;
      box-shadow: 0 0 5px rgba(30,99,195,0.2);
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

    .breadcrumb a {
      text-decoration: none;
      color: #1e63c3;
    }
    .breadcrumb a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="card p-4">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Artikel</li>
      </ol>
    </nav>

    <h3 class="mb-4">Edit Artikel</h3>
    <form method="POST" action="update.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
      <input type="text" name="judul" value="<?php echo htmlspecialchars($row['judul']); ?>" class="form-control mb-3" required>
      
      <select name="kategori" class="form-select mb-3" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="Teknologi" <?php if($row['kategori']=='Teknologi') echo 'selected'; ?>>Teknologi</option>
          <option value="AI" <?php if($row['kategori']=='AI') echo 'selected'; ?>>AI</option>
          <option value="Gadget" <?php if($row['kategori']=='Gadget') echo 'selected'; ?>>Gadget</option>
          <option value="Coding" <?php if($row['kategori']=='Coding') echo 'selected'; ?>>Coding</option>
          <option value="Internet" <?php if($row['kategori']=='Internet') echo 'selected'; ?>>Internet</option>
          <option value="Lainnya" <?php if($row['kategori']=='Lainnya') echo 'selected'; ?>>Lainnya</option>
        </select>


      <?php if(!empty($row['gambar'])): ?>
        <img src="../uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="" class="img-fluid mb-3" style="max-height:150px; border-radius:6px;">
      <?php endif; ?>

      <input type="file" name="gambar" class="form-control mb-3">
      <textarea name="isi" id="editor" class="form-control" rows="10">
          <?php echo htmlspecialchars($row['konten']); ?>
        </textarea>


      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>

      </div>
    </form>
  </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
  .create(document.querySelector('#editor'), {
    toolbar: [
      'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
      'blockQuote', 'insertTable', 'undo', 'redo'
    ],
    heading: {
      options: [
        { model: 'paragraph', title: 'Paragraf', class: 'ck-heading_paragraph' },
        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
      ]
    }
  })
  .catch(error => console.error(error));
</script>
</body>
</html>
