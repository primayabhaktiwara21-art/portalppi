<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// === UPLOAD FILE ===
if (isset($_POST['upload'])) {
    $file = $_FILES['file'];
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : NULL;

    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $safeName = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $file['name']);
    $filePath = $uploadDir . $safeName;
    $url = "https://portalppi.my.id/drive/uploads/" . $safeName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $id = uniqid();
        $type = $file['type'];
        $size = $file['size'];

        $stmt = $conn->prepare("INSERT INTO drive_files (id, name, type, size, url, parent_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $id, $safeName, $type, $size, $url, $parent_id);
        $stmt->execute();

        if ($parent_id) {
            echo "<script>alert('✅ File berhasil diunggah!');window.location='drive.php?parent={$parent_id}';</script>";
        } else {
            echo "<script>alert('✅ File berhasil diunggah!');window.location='drive.php';</script>";
        }
        exit;
    } else {
        echo "<script>alert('⚠️ Gagal mengunggah file. Pastikan folder uploads bisa ditulis (0777).');window.history.back();</script>";
        exit;
    }
}



// === BUAT FOLDER BARU ===
if (isset($_POST['buat_folder'])) {
    $nama = $_POST['nama_folder'];
    $parent_id = $_POST['parent_id'] ?? NULL;
    $id = uniqid();
    $conn->query("INSERT INTO drive_files (id, name, type, size, parent_id) VALUES ('$id', '$nama', 'folder', 0, " . ($parent_id ? "'$parent_id'" : "NULL") . ")");
    echo "<script>alert('📁 Folder berhasil dibuat!');window.location='drive.php';</script>";
    exit;
}

// === HAPUS FILE/FOLDER ===
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = $conn->query("SELECT * FROM drive_files WHERE id='$id'");
    if ($r = $q->fetch_assoc()) {
        if ($r['type'] !== 'folder' && !empty($r['url'])) {
            $path = __DIR__ . '/uploads/' . basename($r['url']);


            if (file_exists($path)) unlink($path);
        }
        $conn->query("DELETE FROM drive_files WHERE id='$id'");
    }
    echo "<script>alert('🗑️ Berhasil dihapus.');window.location='drive.php';</script>";
    exit;
}

// === PINDAHKAN FILE / FOLDER (drag & drop) ===
if (isset($_POST['move_file']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $target = $_POST['target_folder'] ?: NULL;

    $stmt = $conn->prepare("UPDATE drive_files SET parent_id = ? WHERE id = ?");
    $stmt->bind_param("ss", $target, $id);
    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error";
    }
    exit; // hentikan agar tidak lanjut render HTML
}


// === TAMPILAN FILE ===
$currentParent = $_GET['parent'] ?? NULL;

if ($currentParent) {
    $stmt = $conn->prepare("SELECT * FROM drive_files WHERE parent_id = ? ORDER BY updated_at DESC");
    $stmt->bind_param("s", $currentParent);
    $stmt->execute();
    $data = $stmt->get_result();
} else {
    $data = $conn->query("SELECT * FROM drive_files WHERE parent_id IS NULL ORDER BY updated_at DESC");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Aplikasi Penyimpanan - Drive-like UI</title>
  <style>
    :root {
      --bg:#e5ebf5; /* sedikit lebih gelap dari #f3f6fb */
      --card:#ffffff;
      --accent:#2563eb;
      --muted:#4b5563; /* teks abu sedikit lebih gelap */
      --radius:12px;
      --shadow:0 6px 18px rgba(0,0,0,0.08);
      --gap:16px;
      --max-width:1100px;
    }

    
    
    * { box-sizing:border-box }
    
    
    body {
      margin:0;
      font-family:Inter,system-ui,Segoe UI,Roboto,"Helvetica Neue",Arial;
      background:linear-gradient(180deg,#eef2ff 0%,var(--bg) 100%);
      color:#0f172a;
      padding:32px;
      display:flex;
      justify-content:center;
    }
    
    
    .app {
      width:100%;
      max-width:var(--max-width);
      background:transparent;
      border-radius:16px;
    }
    header {
      display:flex;
      align-items:center;
      gap:16px;
      margin-bottom:20px;
      flex-wrap:wrap;
    }
    .brand {
      display:flex;
      align-items:center;
      gap:12px;
    }
    .logo {
      width:48px; height:48px;
      border-radius:10px;
      background:linear-gradient(135deg,var(--accent),#7c3aed);
      display:flex; align-items:center; justify-content:center;
      color:white; font-weight:700; font-size:18px;
      box-shadow:var(--shadow);
    }
    h1 { margin:0; font-size:18px }
    p.lead { margin:0; color:var(--muted); font-size:13px }

    .controls {
      margin-left:auto;
      display:flex; gap:10px; align-items:center; flex-wrap:wrap;
    }
    .btn {
      background:var(--card);
      border-radius:10px;
      padding:8px 12px;
      box-shadow:var(--shadow);
      border:1px solid rgba(15,23,42,0.04);
      cursor:pointer;
      display:inline-flex;
      gap:8px;
      align-items:center;
      font-size:14px;
      text-decoration:none;
      color:inherit;
    }
    .btn.primary { background:var(--accent); color:white; border:none }
    .btn.icon { padding:8px }

    .toolbar {
      display:flex;
      gap:12px;
      align-items:center;
      margin-bottom:18px;
      flex-wrap:wrap;
    }
    .search {
      flex:1;
      display:flex;
      align-items:center;
      gap:8px;
      background:var(--card);
      padding:8px 12px;
      border-radius:12px;
      box-shadow:var(--shadow);
    }
    .search input {
      border:none; outline:none;
      font-size:14px;
      background:transparent;
      width:100%;
    }
    .meta { display:flex; gap:8px; align-items:center }
    .toggle {
      display:flex; gap:6px;
      border-radius:10px;
      overflow:hidden;
      background:var(--card);
      box-shadow:var(--shadow);
    }
    .toggle button {
      border:none;
      padding:8px 10px;
      cursor:pointer;
      background:transparent;
    }
    .toggle button.active {
      background:linear-gradient(90deg,var(--accent),#7c3aed);
      color:white;
    }

    .panel { background:transparent }
    .drive-container {
      display:grid;
      grid-template-columns:repeat(auto-fill,minmax(160px,1fr));
      gap:var(--gap);
    }
    .file-card {
      background:var(--card);
      border-radius:12px;
      padding:14px;
      box-shadow:var(--shadow);
      display:flex;
      flex-direction:column;
      gap:10px;
      align-items:center;
      text-align:center;
      min-height:120px;
      cursor:pointer;
      transition:transform .15s ease;
    }
    .file-card:hover { transform:translateY(-6px); }
    
    
    .file-icon {
      width:90px;  /* dari 56px */
      height:90px; /* dari 56px */
      display:flex;
      align-items:center;
      justify-content:center;
      border-radius:12px;
      font-size:40px; /* menambah ukuran emoji 📁 */
      font-weight:600;
    }

.file-icon img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}

    
    
    
    .file-name { font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; width:100% }
    .file-sub { font-size:12px; color:var(--muted) }
    
    
    

/* === Tampilan List View yang rapi seperti Google Drive === */
.list-view .drive-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.list-view .file-card {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  text-align: left;
  min-height: auto;
  width: 100%;
  gap: 16px;
}

.list-view .file-icon {
  width: 48px;
  height: 48px;
  font-size: 28px;
  border-radius: 8px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.list-view .file-card img {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: 8px;
}

.list-view .file-name {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 14px;
}

.list-view .file-sub {
  color: var(--muted);
  font-size: 13px;
  margin-left: 10px;
  min-width: 90px;
  text-align: right;
}

.list-view .file-card .btn {
  flex-shrink: 0;
  background: var(--accent);
  color: #fff;
  border: none;
  font-size: 13px;
}

    
    
    .list-row .file-icon { width:38px; height:38px; border-radius:8px }
    .list-row .row-main { flex:1 }

    .empty { text-align:center; color:var(--muted); padding:40px }

    .modal {
      position:fixed; inset:0;
      display:none;
      align-items:center; justify-content:center;
      background:rgba(2,6,23,0.5);
      z-index:50;
    }
    .modal.show { display:flex }
    .modal-inner {
      width:90%; max-width:900px;
      background:var(--card);
      border-radius:12px;
      padding:18px;
    }
    .modal .top { display:flex; justify-content:space-between; align-items:center; gap:12px }
    .modal .preview-area { margin-top:12px; max-height:70vh; overflow:auto }

    @media(max-width:600px){
      .brand h1{font-size:16px}
      .drive-container{grid-template-columns:repeat(auto-fill,minmax(120px,1fr))}
    }
    /* Tombol biru solid seperti contoh (Bersihkan) */
.btn.btn-blue-solid {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #2563eb; /* biru modern */
  color: #ffffff;       /* teks putih */
  font-weight: 600;
  font-size: 0.95rem;
  text-decoration: none;
  padding: 10px 20px;
  border: none;
  border-radius: 9999px; /* full rounded */
  transition: all 0.2s ease-in-out;
  box-shadow: 0 4px 10px rgba(37,99,235,0.2);
}

.btn.btn-blue-solid:hover {
  background: #1e40af; /* biru sedikit lebih gelap */
  transform: translateY(-1px);
  box-shadow: 0 6px 15px rgba(37,99,235,0.3);
  text-decoration: none;
}
.panel.dragover {
  outline: 2px dashed var(--accent);
  outline-offset: 10px;
  background: rgba(37,99,235,0.05);
  transition: all 0.2s ease-in-out;
}

.panel {
  background: transparent;
  position: relative;
  pointer-events: all; /* Penting agar bisa menerima drop */
}

    
  </style>
</head>
<body>
  <div class="app">
    <header>
      <div class="brand">
        <div class="logo">DR</div>
        <div>
          <h1>Drive - Penyimpanan Saya</h1>
          <p class="lead">Unggah, lihat, dan kelola file.</p>
        </div>
      </div>


        <div class="controls">
          <!-- tombol upload langsung kirim ke PHP -->
          <label for="fileHidden" class="btn">📤 Upload</label>
          <div class="btn" id="newFolderBtn">📁 Folder Baru</div>
          <div><a href="https://portalppi.my.id/dashboard.php" class="btn btn-blue-solid">⬅️ Kembali ke Dashboard</a></div>
        </div>
        
<!-- form upload tersembunyi -->
<form method="POST" enctype="multipart/form-data" id="uploadForm" style="display:none;">
  <input type="file" name="file" id="fileHidden" onchange="document.getElementById('uploadForm').submit()">
  <input type="hidden" name="upload" value="1">
  <input type="hidden" name="parent_id" value="<?= htmlspecialchars($currentParent ?? '') ?>">
</form>


      
      
    </header>

    <div class="toolbar">
      <div class="search">🔎 <input id="searchInput" placeholder="Cari file berdasarkan nama..."></div>
      <div class="meta">
        <div class="toggle">
          <button id="gridBtn" class="active">🔳</button>
          <button id="listBtn">📋</button>
        </div>
        <div class="btn" id="sortBtn">Urutkan ▾</div>
      </div>
    </div>
<?php if (!empty($currentParent)): ?>
  <div style="margin-bottom:16px;">
    <a href="drive.php" class="btn btn-blue-solid">⬅️ Kembali</a>
  </div>
<?php endif; ?>


<main class="panel" id="panel">
  <div id="drive" class="drive-container">
    <?php if ($data->num_rows > 0): ?>
      <?php while ($row = $data->fetch_assoc()): ?>
     
     
      
      
<div class="file-card"
     style="cursor:pointer;"
     data-id="<?= $row['id'] ?>"
     data-type="<?= $row['type'] ?>"
     draggable="true"
     onclick="<?php if ($row['type'] === 'folder'): ?>
       window.location='drive.php?parent=<?= $row['id'] ?>'
     <?php else: ?>
       openPreview('<?= htmlspecialchars($row['url']) ?>', '<?= htmlspecialchars($row['name']) ?>', '<?= htmlspecialchars($row['type']) ?>')
     <?php endif; ?>">

     
  <div class="file-icon">
    <?php if ($row['type'] === 'folder'): ?>
      📁
    <?php elseif (strpos($row['type'], 'image') !== false): ?>
      <img src="<?= htmlspecialchars($row['url']) ?>" 
           alt="<?= htmlspecialchars($row['name']) ?>" 
           style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
    <?php else: ?>
      📄
    <?php endif; ?>
  </div>

  <div class="file-name"><?= htmlspecialchars($row['name']) ?></div>
  <div class="file-sub">
    <?= $row['type'] === 'folder' ? 'Folder' : round($row['size'] / 1024, 1) . ' KB' ?>
  </div>

  <?php if ($row['type'] === 'folder'): ?>
    <a href="?hapus=<?= $row['id'] ?>" class="btn" onclick="event.stopPropagation();return confirm('Yakin ingin hapus folder ini?')">🗑️</a>
  <?php else: ?>
    <a href="<?= htmlspecialchars($row['url']) ?>" class="btn" target="_blank" onclick="event.stopPropagation();">⬇️ Download</a>
    <a href="?hapus=<?= $row['id'] ?>" class="btn" onclick="event.stopPropagation();return confirm('Yakin ingin hapus file ini?')">🗑️</a>
  <?php endif; ?>
</div>

      <?php endwhile; ?>
    <?php else: ?>
      <div class="empty">Belum ada file. Silakan upload atau buat folder baru.</div>
    <?php endif; ?>
  </div>
</main>

  </div>

  <!-- Modal preview -->
  <div class="modal" id="modal">
    <div class="modal-inner">
      <div class="top">
        <div id="modalTitle">Preview</div>
        <div style="display:flex; gap:8px;">
          <a id="downloadLink" class="btn" href="#" download>⬇️ Download</a>
          <button id="closeModal" class="btn">✖</button>
        </div>
      </div>
      <div class="preview-area" id="previewArea"></div>
    </div>
  </div>

<script>
  
  const drive = document.getElementById('drive');
  const empty = document.getElementById('empty');
  const gridBtn = document.getElementById('gridBtn');
  const listBtn = document.getElementById('listBtn');
  const panel = document.getElementById('panel');
  const searchInput = document.getElementById('searchInput');
  const sortBtn = document.getElementById('sortBtn');
  const modal = document.getElementById('modal');
  const previewArea = document.getElementById('previewArea');
  const modalTitle = document.getElementById('modalTitle');
  const downloadLink = document.getElementById('downloadLink');
  const closeModal = document.getElementById('closeModal');
  const clearAllBtn = document.getElementById('clearAllBtn');
  const newFolderBtn = document.getElementById('newFolderBtn');

// Ambil semua file/folder dari database
let files = <?php
  $all = $conn->query("SELECT * FROM drive_files");
  $arr = [];
  while ($r = $all->fetch_assoc()) {
    $arr[] = [
      'id' => $r['id'],
      'name' => $r['name'],
      'type' => $r['type'],
      'size' => (int)$r['size'],
      'url' => $r['url'],
      'parentId' => $r['parent_id']
    ];
  }
  echo json_encode($arr);
?>;

let viewMode = 'grid';
let sortMode = 'newest';
let currentFolderId = <?= $currentParent ? json_encode($currentParent) : 'null' ?>;



  

  function saveFiles() {
    localStorage.setItem('drive_files', JSON.stringify(files));
  }

  function humanSize(bytes){
    if(bytes===0)return '0 B';
    const u=['B','KB','MB','GB'];
    let i=0,n=bytes;
    while(n>=1024&&i<u.length-1){n/=1024;i++}
    return (Math.round(n*10)/10)+' '+u[i];
  }

  // === Fungsi hapus ===
  function deleteItem(id) {
    const target = files.find(f => f.id === id);
    if (!target) return;

    if (!confirm(`Hapus ${target.type === 'folder' ? 'folder dan isinya' : 'file'} "${target.name}"?`))
      return;



    // Jika folder, hapus semua isinya (rekursif)
    if (target.type === 'folder') {
      deleteFolderRecursive(id);
    } else {
      files = files.filter(f => f.id !== id);
    }
    saveFiles();
    render();
  }

  function deleteFolderRecursive(folderId) {
    const children = files.filter(f => f.parentId === folderId);
    for (const c of children) {
      if (c.type === 'folder') deleteFolderRecursive(c.id);
      else files = files.filter(f => f.id !== c.id);
    }
    files = files.filter(f => f.id !== folderId);
  }

  // === Render isi folder ===
  function render() {
    drive.innerHTML='';
    const q=searchInput.value.trim().toLowerCase();
    let list=files.filter(f => f.parentId === currentFolderId && f.name.toLowerCase().includes(q));

    if(sortMode==='newest')list.sort((a,b)=>b.time-a.time);
    if(sortMode==='oldest')list.sort((a,b)=>a.time-b.time);
    if(sortMode==='name')list.sort((a,b)=>a.name.localeCompare(b.name));

    if(list.length===0){empty.style.display='block'}else{empty.style.display='none'}
    
    

    if(viewMode==='grid'){
      panel.classList.remove('list-view');
      for(const f of list){
        const card=document.createElement('div');
        card.className='file-card';
        card.style.position='relative';

        // tombol hapus
        const del=document.createElement('button');
        del.textContent='🗑️';
        del.title='Hapus';
        del.style.position='absolute';
        del.style.top='8px';
        del.style.right='10px';
        del.style.background='transparent';
        del.style.border='none';
        del.style.cursor='pointer';
        del.style.fontSize='16px';
        del.addEventListener('click', e => { e.stopPropagation(); deleteItem(f.id); });

        // ikon atau thumbnail
        const icon=document.createElement('div');
        icon.className='file-icon';
        icon.style.overflow='hidden';
        icon.style.display='flex';
        icon.style.alignItems='center';
        icon.style.justifyContent='center';
        icon.style.borderRadius='8px';
        icon.style.width='80px';
        icon.style.height='80px';
        icon.style.background='#f3f4f6';

        if (f.type.startsWith('image/')) {
          const img=document.createElement('img');
          img.src=f.url;
          img.alt=f.name;
          img.style.width='100%';
          img.style.height='100%';
          img.style.objectFit='cover';
          icon.innerHTML='';
          icon.appendChild(img);
        } else {
          icon.textContent=f.type==='folder'?'📁':'📦';
        }

        const name=document.createElement('div');
        name.className='file-name';
        name.textContent=f.name;
        const sub=document.createElement('div');
        sub.className='file-sub';
        sub.textContent=f.type==='folder'?'Folder':humanSize(f.size||0);

        card.append(del,icon,name,sub);
        card.addEventListener('click',()=>openItem(f.id));
        drive.appendChild(card);
      }
    } else {
      panel.classList.add('list-view');
      for(const f of list){
        const row=document.createElement('div');
        row.className='list-row';
        const icon=document.createElement('div');
        icon.className='file-icon';
        if (f.type.startsWith('image/')) {
          const img=document.createElement('img');
          img.src=f.url;
          img.style.width='38px';
          img.style.height='38px';
          img.style.objectFit='cover';
          img.style.borderRadius='8px';
          icon.appendChild(img);
        } else {
          icon.textContent=f.type==='folder'?'📁':'📦';
        }
        const main=document.createElement('div');
        main.className='row-main';
        const name=document.createElement('div');
        name.className='file-name';
        name.textContent=f.name;
        const sub=document.createElement('div');
        sub.className='file-sub';
        sub.textContent=f.type==='folder'?'Folder':humanSize(f.size||0);
        main.append(name,sub);

        const del=document.createElement('button');
        del.textContent='🗑️';
        del.title='Hapus';
        del.style.background='transparent';
        del.style.border='none';
        del.style.cursor='pointer';
        del.addEventListener('click', e => { e.stopPropagation(); deleteItem(f.id); });

        row.append(icon,main,del);
        row.addEventListener('click',()=>openItem(f.id));
        drive.appendChild(row);
      }
    }

    // tombol kembali (versi tombol biru solid)
    const backBtn = document.getElementById('backBtn');
    if (currentFolderId) {
      if (!backBtn) {
        const back = document.createElement('a');
        back.id = 'backBtn';
        back.href = '#';
        back.className = 'btn btn-blue-solid';
        back.textContent = '⬅️ Kembali';
        back.style.display = 'inline-flex';
        back.style.alignItems = 'center';
        back.style.gap = '6px';
        back.style.marginBottom = '16px';
        back.style.textDecoration = 'none';
        back.addEventListener('click', (e) => {
          e.preventDefault();
          goBack();
        });
        panel.prepend(back);
      }
    } else {
      if (backBtn) backBtn.remove();
    }

      
      
render();
enableDragAndDrop();
enableRootDrop();


    }
    

  function openItem(id){
    const f = files.find(x=>x.id===id);
    if(!f)return;
    if(f.type==='folder'){
      currentFolderId = f.id;
      render();
    } else {
      openPreview(f.id);
    }
  }

  function goBack(){
    const parentFolder = files.find(f => f.id === currentFolderId);
    currentFolderId = parentFolder ? parentFolder.parentId || null : null;
    render();
  }

  function openPreview(id){
    const f=files.find(x=>x.id===id);
    if(!f)return alert('Tidak ditemukan.');
    modalTitle.textContent=f.name;
    previewArea.innerHTML='';
    downloadLink.href=f.url||'#';
    downloadLink.style.display=f.type==='folder'?'none':'inline-flex';
    if(f.type.startsWith('image/')){
      const img=document.createElement('img');
      img.src=f.url; img.style.maxWidth='100%';
      previewArea.appendChild(img);
    } else if(f.type==='application/pdf'){
      const iframe=document.createElement('iframe');
      iframe.src=f.url; iframe.style.width='100%'; iframe.style.height='70vh';
      previewArea.appendChild(iframe);
    } else {
      const info=document.createElement('div');
      info.textContent='Tidak bisa menampilkan preview file ini.';
      previewArea.appendChild(info);
    }
    modal.classList.add('show');
  }

  function closePreview(){ modal.classList.remove('show'); }




newFolderBtn.addEventListener('click', ()=>{
  const name = prompt('Nama folder baru:');
  if(!name)return;

  // buat form kirim otomatis ke PHP
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = 'drive.php';

  const input1 = document.createElement('input');
  input1.type = 'hidden';
  input1.name = 'buat_folder';
  input1.value = '1';

  const input2 = document.createElement('input');
  input2.type = 'hidden';
  input2.name = 'nama_folder';
  input2.value = name;

  // ini penting biar folder disimpan di folder yang sedang dibuka
  const input3 = document.createElement('input');
  input3.type = 'hidden';
  input3.name = 'parent_id';
  input3.value = "<?= htmlspecialchars($_GET['parent'] ?? '') ?>";

  form.appendChild(input1);
  form.appendChild(input2);
  form.appendChild(input3);
  document.body.appendChild(form);
  form.submit();
});



  
gridBtn.addEventListener('click',()=>{
  viewMode='grid';
  gridBtn.classList.add('active');
  listBtn.classList.remove('active');
  document.querySelector('.panel').classList.remove('list-view');
});

listBtn.addEventListener('click',()=>{
  viewMode='list';
  listBtn.classList.add('active');
  gridBtn.classList.remove('active');
  document.querySelector('.panel').classList.add('list-view');
});

  
  searchInput.addEventListener('input',()=>render());
  sortBtn.addEventListener('click',()=>{
    const next={newest:'oldest',oldest:'name',name:'newest'}[sortMode];
    sortMode=next;
    sortBtn.textContent='Urutkan ▾ ('+({newest:'Terbaru',oldest:'Terlama',name:'Nama'}[sortMode])+')';
    render();
  });
  closeModal.addEventListener('click',closePreview);
  modal.addEventListener('click',e=>{if(e.target===modal)closePreview()});
  clearAllBtn.addEventListener('click',()=>{if(confirm('Hapus semua file & folder?')){files=[];saveFiles();render();}});

// === Drag & Drop Upload (Final, tested & working) ===
// === Drag & Drop Upload (CARA ALTERNATIF, STABIL DI SEMUA BROWSER) ===

// Buat area drop zone besar di atas panel
const dropZone = document.createElement('div');
dropZone.style.position = 'fixed';
dropZone.style.inset = '0';
dropZone.style.background = 'rgba(37,99,235,0.08)';
dropZone.style.border = '3px dashed var(--accent)';
dropZone.style.display = 'none';
dropZone.style.alignItems = 'center';
dropZone.style.justifyContent = 'center';
dropZone.style.zIndex = '1000';
dropZone.style.fontSize = '18px';
dropZone.style.fontWeight = '600';
dropZone.textContent = 'Lepaskan file di sini untuk upload';
document.body.appendChild(dropZone);

window.addEventListener('dragenter', (e) => {
  e.preventDefault();
  dropZone.style.display = 'flex';
});

window.addEventListener('dragover', (e) => {
  e.preventDefault();
});

window.addEventListener('dragleave', (e) => {
  e.preventDefault();
  // Sembunyikan jika benar-benar keluar dari jendela
  if (e.clientX <= 0 || e.clientY <= 0 || e.clientX >= window.innerWidth || e.clientY >= window.innerHeight) {
    dropZone.style.display = 'none';
  }
});

window.addEventListener('drop', (e) => {
  e.preventDefault();
  dropZone.style.display = 'none';

  // Kirim file lewat form upload tersembunyi (seperti tombol Upload)
  if (e.dataTransfer.files.length > 0) {
    const uploadInput = document.getElementById('fileHidden');
    uploadInput.files = e.dataTransfer.files;
    document.getElementById('uploadForm').submit();
  }
});



render();

  
  // === Aktifkan drag & drop antar folder ===
function enableDragAndDrop() {
  const cards = document.querySelectorAll('.file-card');

  cards.forEach(card => {
    const itemId = files.find(f => f.name === card.querySelector('.file-name').textContent && f.parentId === currentFolderId)?.id;
    if (!itemId) return;

    // Jika item ini file (bukan folder), izinkan drag
    card.setAttribute('draggable', true);
    card.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/plain', itemId);
      e.target.style.opacity = '0.4';
    });

    card.addEventListener('dragend', e => {
      e.target.style.opacity = '1';
    });

    // Jika item ini folder, izinkan drop ke dalamnya
    const f = files.find(x => x.id === itemId);
    if (f && f.type === 'folder') {
      card.addEventListener('dragover', e => {
        e.preventDefault();
        card.style.outline = '2px dashed var(--accent)';
        card.style.background = 'rgba(37,99,235,0.05)';
      });

      card.addEventListener('dragleave', () => {
        card.style.outline = '';
        card.style.background = '';
      });

      card.addEventListener('drop', e => {
        e.preventDefault();
        card.style.outline = '';
        card.style.background = '';
        const draggedId = e.dataTransfer.getData('text/plain');
        const draggedItem = files.find(x => x.id === draggedId);

        if (!draggedItem || draggedItem.id === f.id) return;
        if (draggedItem.type === 'folder' && isInsideFolder(f.id, draggedItem.id)) {
          alert('Tidak bisa memindahkan folder ke dalam dirinya sendiri.');
          return;
        }

        // Pindahkan file ke folder tujuan (langsung ke database)
        fetch('drive.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'move_file=1&id=' + encodeURIComponent(draggedId) + '&target_folder=' + encodeURIComponent(f.id)
        })
        .then(r => r.text())
        .then(res => {
          if (res.trim() === 'ok') {
            location.reload();
          } else {
            alert('⚠️ Gagal memindahkan file.');
          }
        });

      });
    }
  });
}

// Cegah folder masuk ke dirinya sendiri (rekursif)
function isInsideFolder(targetFolderId, movingFolderId) {
  if (targetFolderId === movingFolderId) return true;
  const target = files.find(f => f.id === targetFolderId);
  if (!target || !target.parentId) return false;
  return isInsideFolder(target.parentId, movingFolderId);
}

// === Drop ke area kosong untuk memindahkan file keluar folder ===
function enableRootDrop() {
  // pastikan area panel bisa menerima drop
  panel.addEventListener('dragover', e => {
    e.preventDefault();

    // hanya aktif kalau ada folder aktif (kita di dalam folder)
    if (currentFolderId) {
      panel.classList.add('dragover');
    }
  });

  panel.addEventListener('dragleave', e => {
    e.preventDefault();
    panel.classList.remove('dragover');
  });

  panel.addEventListener('drop', e => {
    e.preventDefault();
    panel.classList.remove('dragover');

    const draggedId = e.dataTransfer.getData('text/plain');
    const draggedItem = files.find(x => x.id === draggedId);
    if (!draggedItem) return;

    // jika user di dalam folder, pindahkan ke root
    if (currentFolderId) {
      const currentFolder = files.find(f => f.id === currentFolderId);
      
    // Pindahkan file keluar folder ke root
    fetch('drive.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'move_file=1&id=' + encodeURIComponent(draggedId) + '&target_folder='
    })
    .then(r => r.text())
    .then(res => {
      if (res.trim() === 'ok') {
        location.reload();
      } else {
        alert('⚠️ Gagal memindahkan file ke root.');
      }
    });

    }
  });
}

function openPreview(url, name, type) {
  const modal = document.getElementById('modal');
  const modalTitle = document.getElementById('modalTitle');
  const previewArea = document.getElementById('previewArea');
  const downloadLink = document.getElementById('downloadLink');

  modalTitle.textContent = name;
  downloadLink.href = url;
  previewArea.innerHTML = '';

  if (type.startsWith('image/')) {
    const img = document.createElement('img');
    img.src = url;
    img.style.maxWidth = '100%';
    img.style.borderRadius = '12px';
    previewArea.appendChild(img);
  } else if (type === 'application/pdf') {
    const iframe = document.createElement('iframe');
    iframe.src = url;
    iframe.style.width = '100%';
    iframe.style.height = '70vh';
    iframe.style.border = 'none';
    previewArea.appendChild(iframe);
  } else {
    const info = document.createElement('p');
    info.textContent = 'Tidak bisa menampilkan preview file ini.';
    previewArea.appendChild(info);
  }

  modal.classList.add('show');
}

// === Tambahan perbaikan drag & drop ===

// 1️⃣ Cegah drop folder ke dirinya sendiri dan aktifkan efek visual
    document.querySelectorAll('.file-card').forEach(card => {
  card.addEventListener('dragstart', e => {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('id', card.dataset.id);
    e.dataTransfer.setData('type', card.dataset.type);
    card.style.opacity = '0.5';
  });
  card.addEventListener('dragend', () => card.style.opacity = '1');

  if (card.dataset.type === 'folder') {
    card.addEventListener('dragover', e => { e.preventDefault(); card.style.background = 'rgba(37,99,235,0.1)'; });
    card.addEventListener('dragleave', () => { card.style.background = ''; });
    card.addEventListener('drop', e => {
      e.preventDefault(); card.style.background = '';
      const draggedId = e.dataTransfer.getData('id');
      if (!draggedId || draggedId === card.dataset.id) return; // cegah drop diri sendiri

      fetch('drive.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `move_file=1&id=${encodeURIComponent(draggedId)}&target_folder=${encodeURIComponent(card.dataset.id)}`
      })
      .then(r => r.text())
      .then(res => {
        if (res.trim() === 'ok') location.reload();
        else alert('⚠️ Gagal memindahkan file.');
      });
    });
  }
});

// 2️⃣ Aktifkan drop ke area kosong (pindah ke root)
const driveContainer = document.getElementById('drive');
driveContainer.addEventListener('dragover', e => { e.preventDefault(); panel.classList.add('dragover'); });
driveContainer.addEventListener('dragleave', e => { e.preventDefault(); panel.classList.remove('dragover'); });
driveContainer.addEventListener('drop', e => {
  e.preventDefault();
  panel.classList.remove('dragover');
  const draggedId = e.dataTransfer.getData('id');
  if (!draggedId) return;
  fetch('drive.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `move_file=1&id=${encodeURIComponent(draggedId)}&target_folder=`
  })
  .then(r => r.text())
  .then(res => {
    if (res.trim() === 'ok') location.reload();
    else alert('⚠️ Gagal memindahkan file ke root.');
  });
});


</script>



</body>
</html>
