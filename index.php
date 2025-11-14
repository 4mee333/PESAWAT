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
    .modal-backdrop{background:rgba(0,0,0,0.45);backdrop-filter:blur(6px);}
    .modal-box{background:#fff;border-radius:16px;padding:18px;max-width:600px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
  </style>

<header class="p-4 bg-indigo-600 text-white text-center font-bold text-xl">Pesawat Insight — Smart Lookup</header>

<main class="max-w-4xl mx-auto p-4">
  <div class="search flex gap-2 mb-4">
    <input id="q" placeholder="Ketik gejala, masalah, atau topik pesawat..." />
    <button id="searchBtn" class="px-4 py-2 rounded-md bg-indigo-600 text-white">Cari</button>
  </div>
  <section id="resultsArea" class="grid gap-4 grid-cols-1 md:grid-cols-2"></section>
</main>

<!-- MODAL -->
<div id="modalRoot" class="fixed inset-0 hidden items-center justify-center p-6 modal-backdrop">
  <div class="modal-box fade-up">
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
      {title:"Compressor stall akibat es", desc:"Icing di inlet menyebabkan stall compressor; anti-ice wajib diaktifkan.", category:"Mesin", tags:["stall","ice","compressor"]},
      {title:"Kipas pendingin avionik gagal", desc:"Overheating pada sistem avionik; ganti fan dan cek power supply.", category:"Avionik", tags:["avionik","cooling","fan"]},
      {title:"Ban pecah saat takeoff", desc:"Ban pecah menimbulkan getaran dan kerusakan wheel bay.", category:"Landing Gear", tags:["tire","burst","takeoff"]},
      {title:"Kontaminasi bahan bakar", desc:"Air dalam bahan bakar menyebabkan engine roughness; drain dan ganti filter.", category:"Sistem", tags:["fuel","contamination","engine"]},
      {title:"Gear roboh saat touchdown", desc:"Landing gear collapse akibat overload atau actuator failure.", category:"Landing Gear", tags:["landing gear","collapse","actuator"]},
      {title:"Autopilot erratic", desc:"Autopilot disconnects karena sensor atau flight computer error.", category:"Avionik", tags:["autopilot","flight control","sensor"]},
      // Tambahkan 90 data lainnya (misal variasi kerusakan, masalah, safety, sistem, avionik)
      {title:"Getaran di sayap saat cruise", desc:"Vibrasi pada sayap karena flutter aerodinamis; cek balance aileron.", category:"Aerodinamika", tags:["wing","vibration","cruise"]},
      {title:"Radar gagal mendeteksi cuaca", desc:"Radar cuaca tidak akurat, periksa sensor dan software.", category:"Avionik", tags:["radar","weather","sensor"]},
      {title:"Overheat pada rem", desc:"Rem wheel bay panas berlebih saat landing; cek brake assembly.", category:"Landing Gear", tags:["brake","overheat","landing"]},
      {title:"Kebocoran oli mesin", desc:"Kebocoran oli di engine dapat mengurangi performa; lakukan inspeksi rutin.", category:"Mesin", tags:["oil","leak","engine"]},
      {title:"Sensor tekanan kabin error", desc:"Sensor tekanan kabin memberi nilai salah; perlu kalibrasi.", category:"Sistem", tags:["cabin","sensor","pressure"]},
      {title:"Kesalahan software avionik", desc:"Software glitch menyebabkan indikator salah; update firmware.", category:"Avionik", tags:["software","avionik","update"]},
      {title:"Ketidakstabilan rudder", desc:"Rudder tidak responsif akibat actuator problem.", category:"Kontrol Penerbangan", tags:["rudder","actuator","control"]},
      {title:"Fuel pump gagal", desc:"Fuel pump gagal pasokan bahan bakar; ganti unit dan cek line.", category:"Sistem", tags:["fuel","pump","engine"]},
      {title:"AC kabin tidak dingin", desc:"Kabin panas karena AC failure; cek compressor dan duct.", category:"Sistem", tags:["AC","cabin","comfort"]},
      {title:"Kerusakan flap akibat metal fatigue", desc:"Flap mengalami retak logam; lakukan penggantian.", category:"Aerodinamika", tags:["flap","fatigue","wing"]},
      {title:"Kontrol Yoke keras", desc:"Yoke berat dan sulit digerakkan; cek hydraulic actuator.", category:"Kontrol Penerbangan", tags:["yoke","hydraulic","control"]},
      {title:"Landing gear retract lambat", desc:"Gear retract tidak normal; hydraulic system perlu dicek.", category:"Landing Gear", tags:["landing gear","hydraulic","retract"]},
      {title:"Bocornya jendela kokpit", desc:"Window seal rusak; menyebabkan kebocoran udara.", category:"Struktur", tags:["cockpit","window","leak"]},
      {title:"Vibrasi engine idle", desc:"Vibrasi saat idle; periksa fan dan bearing.", category:"Mesin", tags:["engine","vibration","idle"]},
      {title:"Kesalahan altimeter", desc:"Altimeter memberikan reading salah; kalibrasi wajib.", category:"Avionik", tags:["altimeter","sensor","flight"]},
      {title:"Ice detector error", desc:"Sensor anti-icing tidak mendeteksi ice formation.", category:"Mesin", tags:["ice","sensor","engine"]},
      {title:"Overpressure di fuel tank", desc:"Tekanan tangki tinggi; cek valve dan sensor.", category:"Sistem", tags:["fuel","pressure","tank"]},
      {title:"De-icing system gagal", desc:"Sistem de-icing tidak aktif; periksa panel kontrol.", category:"Mesin", tags:["ice","deicing","engine"]},
      {title:"Overload pada struktur fuselage", desc:"Penerbangan overload; cek stress pada fuselage.", category:"Struktur", tags:["fuselage","overload","stress"]},
      {title:"Kebocoran hydraulic line flap", desc:"Flap tidak bekerja karena hydraulic leak.", category:"Aerodinamika", tags:["flap","hydraulic","leak"]},
      {title:"Turbulence sensor error", desc:"Sensor turbulensi tidak akurat; periksa accelerometer.", category:"Avionik", tags:["turbulence","sensor","avionik"]},
      {title:"Fuel gauge stuck", desc:"Indikator bahan bakar tidak berubah; ganti sensor.", category:"Sistem", tags:["fuel","sensor","gauge"]},
      {title:"Kebocoran udara kabin", desc:"Cabin pressurization leak; periksa seal dan panel.", category:"Sistem", tags:["cabin","pressure","leak"]},
      {title:"Overheat engine oil", desc:"Temperature oli tinggi; lakukan pendinginan dan check line.", category:"Mesin", tags:["engine","oil","overheat"]},
      {title:"Kontrol elevator keras", desc:"Elevator tidak responsif; cek hydraulic actuator.", category:"Kontrol Penerbangan", tags:["elevator","hydraulic","control"]},
      {title:"Radar altimeter false reading", desc:"Kesalahan altimeter radar; periksa wiring dan sensor.", category:"Avionik", tags:["radar","altimeter","sensor"]},
      {title:"Flap asymmetry", desc:"Flap kiri-kanan tidak sama; lakukan pengukuran dan perbaikan.", category:"Aerodinamika", tags:["flap","wing","asymmetry"]},
      {title:"Spoiler tidak deploy", desc:"Spoiler gagal deploy; periksa hydraulic system.", category:"Aerodinamika", tags:["spoiler","hydraulic","wing"]},
      {title:"Landing light mati", desc:"Lampu landing tidak menyala; ganti bulb atau wiring.", category:"Sistem", tags:["light","landing","electrical"]},
      {title:"Battery pack overheat", desc:"Baterai pesawat panas; cek charging system.", category:"Sistem", tags:["battery","overheat","electrical"]},
      {title:"Smoke di kabin", desc:"Terjadi asap; periksa avionik dan kabin HVAC.", category:"Sistem", tags:["smoke","cabin","safety"]},
      {title:"Kegagalan flap actuator", desc:"Actuator flap gagal; ganti unit dan test.", category:"Aerodinamika", tags:["flap","actuator","wing"]},
      {title:"Sensor angle of attack error", desc:"AOA sensor tidak akurat; kalibrasi wajib.", category:"Avionik", tags:["AOA","sensor","flight"]},
      {title:"Landing gear warning false", desc:"Warning muncul palsu; cek switch dan indicator.", category:"Landing Gear", tags:["landing gear","warning","sensor"]},
      {title:"Overload pada rudder", desc:"Rudder terkena stress berlebih; periksa struktur.", category:"Kontrol Penerbangan", tags:["rudder","stress","control"]},
      {title:"Fuel line clogged", desc:"Fuel flow terhambat; lakukan cleaning line.", category:"Sistem", tags:["fuel","line","clog"]},
      {title:"Hydraulic pump noise", desc:"Pump berisik; periksa bearing dan pressure valve.", category:"Sistem", tags:["hydraulic","pump","noise"]},
      {title:"De-icing fluid low", desc:"Cairan de-icing kurang; refill dan cek pump.", category:"Mesin", tags:["deicing","fluid","engine"]},
      {title:"Engine surge saat climb", desc:"Engine mengalami surge; cek inlet dan anti-ice.", category:"Mesin", tags:["engine","surge","climb"]},
      {title:"Winglet damage", desc:"Kerusakan pada winglet; periksa structural dan skin.", category:"Aerodinamika", tags:["winglet","damage","wing"]},
      {title:"Smoke detector kabin error", desc:"Sensor asap tidak aktif; ganti unit.", category:"Sistem", tags:["smoke","sensor","cabin"]},
      {title:"Engine flameout", desc:"Engine mati mendadak; cek fuel dan ignition system.", category:"Mesin", tags:["engine","flameout","safety"]},
      {title:"Landing gear vibration", desc:"Getaran landing gear saat landing; cek tire dan struts.", category:"Landing Gear", tags:["landing gear","vibration","tire"]},
      {title:"Rudder trim failure", desc:"Rudder trim tidak bekerja; cek actuator dan control link.", category:"Kontrol Penerbangan", tags:["rudder","trim","control"]},
      {title:"Fuel imbalance warning", desc:"Peringatan bahan bakar tidak seimbang; cek tank level.", category:"Sistem", tags:["fuel","imbalance","warning"]},
      {title:"Cabin pressurization slow", desc:"Pressurization naik lambat; periksa outflow valve.", category:"Sistem", tags:["cabin","pressure","valve"]},
      {title:"Engine oil pressure low", desc:"Tekanan oli rendah; cek pump dan line.", category:"Mesin", tags:["engine","oil","pressure"]},
      {title:"Flap lever stuck", desc:"Tuas flap macet; cek linkage dan hydraulic.", category:"Aerodinamika", tags:["flap","lever","stuck"]},
      {title:"Avionics reboot failure", desc:"Avionik tidak restart; periksa power supply.", category:"Avionik", tags:["avionics","reboot","error"]},
      {title:"Spoiler asymmetric deployment", desc:"Spoiler deploy tidak simetris; periksa actuator.", category:"Aerodinamika", tags:["spoiler","asymmetry","wing"]},
      {title:"Wheel brake fade", desc:"Rem kehilangan performa saat landing; cek temperature.", category:"Landing Gear", tags:["brake","fade","landing"]},
      {title:"Pitot tube blockage", desc:"Pitot tube tersumbat; menyebabkan speed indicator error.", category:"Avionik", tags:["pitot","blockage","sensor"]},
      {title:"Landing gear lock warning", desc:"Warning gear tidak terkunci; cek mechanical lock.", category:"Landing Gear", tags:["landing gear","warning","lock"]},
      {title:"Overheat avionics bay", desc:"Ruang avionik panas; periksa cooling fan.", category:"Avionik", tags:["avionics","overheat","cooling"]},
      {title:"De-icing boot failure", desc:"De-icing boot tidak mengembang; cek pneumatic system.", category:"Mesin", tags:["deicing","boot","engine"]},
      {title:"Cabin smoke warning", desc:"Asap terdeteksi di kabin; periksa HVAC dan avionik.", category:"Sistem", tags:["cabin","smoke","warning"]},
      {title:"Engine vibration high", desc:"Vibrasi engine tinggi; periksa fan dan shaft.", category:"Mesin", tags:["engine","vibration","fan"]},
      {title:"Flight control hydraulic leak", desc:"Kebocoran hydraulic; kontrol penerbangan terganggu.", category:"Kontrol Penerbangan", tags:["hydraulic","leak","flight control"]},
      {title:"Windshield crack", desc:"Retak pada windshield; segera ganti.", category:"Struktur", tags:["windshield","crack","cockpit"]},
      {title:"Yaw damper failure", desc:"Yaw damper tidak bekerja; cek actuator.", category:"Kontrol Penerbangan", tags:["yaw","damper","actuator"]},
      {title:"Fuel filter clog", desc:"Filter bahan bakar tersumbat; ganti filter.", category:"Sistem", tags:["fuel","filter","clog"]},
      {title:"Engine bleed air failure", desc:"Bleed air tidak berfungsi; cek duct dan valves.", category:"Mesin", tags:["bleed","air","engine"]},
      {title:"Stall warning false", desc:"Peringatan stall muncul palsu; kalibrasi sensor AOA.", category:"Avionik", tags:["stall","warning","sensor"]},
      {title:"Cabin lighting failure", desc:"Lampu kabin mati; cek power dan wiring.", category:"Sistem", tags:["cabin","light","electrical"]},
      {title:"Autopilot disconnect", desc:"Autopilot disconnects; periksa flight control system.", category:"Avionik", tags:["autopilot","disconnect","flight control"]},
      {title:"Engine surge high altitude", desc:"Engine surge saat di ketinggian; periksa anti-ice.", category:"Mesin", tags:["engine","surge","altitude"]},
      {title:"Landing gear strut leak", desc:"Strut bocor; ganti seal.", category:"Landing Gear", tags:["landing gear","strut","leak"]},
      {title:"Elevator trim runaway", desc:"Elevator trim bergerak sendiri; cek control system.", category:"Kontrol Penerbangan", tags:["elevator","trim","control"]
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
  const filtered = DATASET.filter(d=>d.title.toLowerCase().includes(qL)||d.desc.toLowerCase().includes(qL)||d.tags.some(t=>t.includes(qL)));
  renderResults(filtered);
}

function openModal(article){
  modalTitle.textContent=article.title;
  modalBody.textContent=article.desc;
  modalCategory.textContent=article.category;
  modalTags.textContent=article.tags.join(', ');
  modalRoot.classList.remove('hidden');
  document.body.style.overflow='hidden';
}

function closeModal(){
  modalRoot.classList.add('hidden');
  document.body.style.overflow='';
}

btnClose.addEventListener('click',closeModal);
modalRoot.addEventListener('click',e=>{if(e.target===modalRoot) closeModal();});
document.addEventListener('keydown',e=>{if(e.key==='Escape') closeModal();});
searchBtn.addEventListener('click',()=>search(qInput.value));
qInput.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();search(qInput.value);}});

renderResults(DATASET);
</script>

</body>
</html>

