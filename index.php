<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Pesawat Insight — Smart Lookup</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    body{font-family:'Poppins',sans-serif;margin:0;padding:0;background:#f0f4f8;color:#111827;}
    .search input{border:0;outline:0;padding:10px;border-radius:10px;width:100%;}
    .result-card{background:#fff;padding:12px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.05);cursor:pointer;}
    .result-card:hover{transform:translateY(-3px);transition:0.2s;}
    .chip{background:rgba(15,23,42,0.06);padding:4px 8px;border-radius:999px;font-size:12px;color:#6b7280;}
    .muted{color:#6b7280;}
    .fade-up{animation:fadeUp .36s ease both;}
    @keyframes fadeUp{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}
    .modal-backdrop{background:rgba(0,0,0,0.45);backdrop-filter:blur(6px); display:none; align-items:center; justify-content:center;}
    .modal-box{background:#fff;border-radius:16px;padding:18px;max-width:600px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
  </style>
</head>
<body>

  <header class="p-4 bg-indigo-600 text-white text-center font-bold text-xl">Pesawat Insight — Smart Lookup</header>

  <main class="max-w-4xl mx-auto p-4">
    <div class="search flex gap-2 mb-4">
      <input id="q" placeholder="Ketik gejala, masalah, atau topik pesawat..." />
      <button id="searchBtn" class="px-4 py-2 rounded-md bg-indigo-600 text-white">Cari</button>
    </div>
    <section id="resultsArea" class="grid gap-4 grid-cols-1 md:grid-cols-2"></section>
  </main>

  <!-- MODAL -->
  <div id="modalRoot" class="modal-backdrop fixed inset-0 z-50 p-6">
    <div class="modal-box fade-up relative">
      <div class="flex justify-between items-start">
        <h3 id="modalTitle" class="text-xl font-bold"></h3>
        <button id="btnClose" class="px-3 py-1 bg-gray-800 text-white rounded">Tutup</button>
      </div>
      <div id="modalBody" class="mt-4 text-gray-700 text-sm"></div>
      <div class="mt-4 flex gap-2">
        <div class="chip">Kategori</div>
        <div id="modalCategory" class="muted"></div>
      </div>
      <div class="mt-2 flex gap-2">
        <div class="chip">Tag</div>
        <div id="modalTags" class="muted"></div>
      </div>
    </div>
  </div>

  <script>
    const DATASET = [
      {title:"Fuselage retak akibat fatigue", desc:"Retak kecil di fuselage akibat siklus penerbangan; perlu inspeksi visual rutin.", category:"Struktur", tags:["fuselage","fatigue","retak"]},
      {title:"Korosi sayap akibat kelembaban", desc:"Korosi pada bagian bawah sayap karena kelembaban tinggi; periksa rivet dan skin.", category:"Struktur", tags:["sayap","korosi","inspection"]},
      {title:"Serangan burung ke mesin", desc:"Bird strike menyebabkan kerusakan fan blade, perlu pengecekan engine.", category:"Mesin", tags:["engine","bird strike","vibration"]},
      {title:"Kebocoran hidrolik", desc:"Kebocoran pada line tekanan hidrolik dapat mengganggu landing gear.", category:"Sistem", tags:["hydraulic","leak","landing gear"]},
      {title:"Compressor stall akibat es", desc:"Icing di inlet menyebabkan stall compressor; anti-ice wajib diaktifkan.", category:"Mesin", tags:["stall","ice","compressor"]}
      // Bisa tambahkan data lebih banyak
    ];

    const resultsArea = document.getElementById('resultsArea');
    const qInput = document.getElementById('q');
    const searchBtn = document.getElementById('searchBtn');

    const modalRoot = document.getElementById('modalRoot');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalCategory = document.getElementById('modalCategory');
    const modalTags = document.getElementById('modalTags');
    const btnClose = document.getElementById('btnClose');

    function renderResults(list){
      resultsArea.innerHTML='';
      if(list.length===0){
        resultsArea.innerHTML='<div class="col-span-full result-card text-center muted">Tidak ditemukan hasil.</div>';
        return;
      }
      list.forEach(article=>{
        const el = document.createElement('div');
        el.className='result-card fade-up';
        el.innerHTML=`<h4 class="font-semibold">${article.title}</h4><p class="muted text-sm">${article.desc.substring(0,80)}...</p>`;
        el.addEventListener('click',()=>openModal(article));
        resultsArea.appendChild(el);
      });
    }

    function search(q){
      if(!q||!q.trim()) return renderResults([]);
      const qL = q.toLowerCase();
      const filtered = DATASET.filter(d=>d.title.toLowerCase().includes(qL) || d.desc.toLowerCase().includes(qL) || d.tags.some(t=>t.includes(qL)));
      renderResults(filtered);
    }

    function openModal(article){
      modalTitle.textContent=article.title;
      modalBody.textContent=article.desc;
      modalCategory.textContent=article.category;
      modalTags.textContent=article.tags.join(', ');
      modalRoot.style.display='flex';
      document.body.style.overflow='hidden';
    }

    function closeModal(){
      modalRoot.style.display='none';
      document.body.style.overflow='';
    }

    btnClose.addEventListener('click',closeModal);
    modalRoot.addEventListener('click',e=>{if(e.target===modalRoot) closeModal();});
    document.addEventListener('keydown',e=>{if(e.key==='Escape') closeModal();});
    searchBtn.addEventListener('click',()=>search(qInput.value));
    qInput.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();search(qInput.value);}});

    renderResults(DATASET); // tampil semua awal
  </script>
</body>
</html>
