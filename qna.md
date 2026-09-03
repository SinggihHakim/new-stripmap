# QnA — Implementasi KML/KMZ & Polyline Visual di Peta (Leaflet.js)

> Pertanyaan & jawaban seputar bagaimana sistem **Web BMBK - Stripmap** menggunakan
> file KML/KMZ untuk menampilkan rute jalan secara visual di peta, beserta logika
> pewarnaan segmen dan batasan interval meter.

---

## Q1: Apa itu file KML/KMZ dan kenapa digunakan di sistem ini?

**A:**
- **KML (Keyhole Markup Language)** adalah format berbasis XML yang menyimpan data
  geografis (titik, garis, poligon). Dibuat oleh Google, digunakan di Google Earth/Maps.
- **KMZ** adalah versi terkompresi dari KML (zip berisi file `.kml` di dalamnya).
- Di sistem ini, petugas survei lapangan mengekspor rute jalan dari GPS atau
  Google Earth dalam format KML/KMZ, kemudian mengimpornya ke sistem agar rute
  jalan muncul di peta interaktif secara akurat mengikuti jalur asli jalan,
  bukan sekadar garis lurus dari titik A ke titik B.

---

## Q2: Bagaimana proses impor KML/KMZ berjalan? (Alur teknis)

**A:** Proses impor sepenuhnya dilakukan di sisi **frontend (browser)** menggunakan
JavaScript — tidak ada upload ke server untuk parsing KML.

### Alur step-by-step:

```
User pilih file .kml / .kmz
        |
        v
[JS: importKml()] — form.php baris 558
        |
        +-- Jika .kmz --> JSZip.loadAsync() --> ekstrak file .kml dari dalam arsip
        |
        +-- Jika .kml --> file.text() langsung baca teks
        |
        v
[JS: parseKmlText()] — form.php baris 606
        |
        v
DOMParser().parseFromString(kmlText, 'application/xml')
        |
        +-- 1. Cari tag <LineString><coordinates>  <- prioritas utama
        +-- 2. Jika kosong --> cari tag <gx:Track><gx:coord>
        +-- 3. Fallback --> semua tag <coordinates> yang punya >= 2 titik
        |
        v
Pilih LineString dengan titik TERBANYAK (rute utama jalan)
        |
        v
[JS: parseCoordString()] — baris 649
Tiap tuple "lng,lat,alt" dipecah --> simpan sebagai [lat, lng]
        |
        v
Simpan ke: koordinatJson = JSON.stringify(coords.map(p => [lng, lat]))
(format simpan: [[lng,lat], [lng,lat], ...])
        |
        v
<input type="hidden" name="koordinat_json"> dikirim saat form submit
        |
        v
PHP (RuasService.php) simpan ke kolom `koordinat_json` di tabel ruas_jalan
(tipe LONGTEXT di database)
```

---

## Q3: Bagaimana file KML/KMZ ditampilkan sebagai polyline di peta?

**A:** Polyline ditampilkan menggunakan **Leaflet.js** dengan data koordinat yang
sudah tersimpan di database.

### Alur rendering peta (stripmap/index.php):

```javascript
// 1. PHP inject data koordinat ke JavaScript
const rawRoute = <?= $safeRouteJson ?>; // [[lng,lat], [lng,lat], ...]

// 2. Convert format [lng,lat] --> [lat,lng] (format Leaflet)
const route = rawRoute.map(p => [p[1], p[0]]);

// 3. Hitung jarak kumulatif tiap titik (haversine)
const cum = [0];
for (let i = 1; i < route.length; i++)
    cum.push(cum[i-1] + haversine(route[i-1], route[i]));

// 4. Fungsi sliceByDist: potong segmen polyline berdasarkan jarak
mapSliceByDist = (d0, d1) => { ... } // interpolasi titik-titik di antara d0-d1

// 5. Tiap segmen STA dipetakan ke potongan polyline
segments.forEach(seg => {
    const segD0 = (seg.sta_awal  / mapStaSpan) * mapTotal;
    const segD1 = (seg.sta_akhir / mapStaSpan) * mapTotal;
    condPieces(seg).forEach(piece => {
        const slice = mapSliceByDist(dd0, dd1);
        L.polyline(slice, { color: piece.color, weight: 7 }).addTo(map);
    });
});
```

Hasilnya: setiap segmen STA (misalnya STA 0+000 s/d 0+100) digambar sebagai
**potongan polyline berwarna** di atas jalur jalan yang sesungguhnya di peta.

---

## Q4: Kenapa penggaris STA di peta hanya bisa per 100 meter? Apakah bisa diubah intervalnya?

**A:** Yang **per 100 meter** bukan di peta, melainkan di **input data stripmap** —
data kondisi jalan diinput dalam unit meter dengan granularitas bebas (bisa 50m,
100m, 200m, dll) tergantung data survei.

Untuk **marker STA di peta**, sistem menggunakan logika **auto-scaling**:

```javascript
// stripmap/index.php baris 643-659
function addStaMarkers(map, posAtFrac, totalSta) {
    let step = 1000;
    const steps = [1000, 2000, 5000, 10000, 20000, 50000];
    for (const s of steps) {
        step = s;
        if (totalSta / s <= 15) break; // maksimal 15 marker di peta
    }
    for (let m = 0; m <= totalSta + 1; m += step) {
        // render marker STA di posisi yang sesuai di polyline
    }
}
```

| Panjang Ruas   | Step Marker yang Muncul  |
|----------------|--------------------------|
| < 15 km        | Setiap 1.000 m (1 km)    |
| 15 - 30 km     | Setiap 2.000 m (2 km)    |
| 30 - 75 km     | Setiap 5.000 m (5 km)    |
| 75 - 150 km    | Setiap 10.000 m (10 km)  |
| dst.           | dst.                     |

### Jika ingin marker per 100 meter:
Ini **bisa diubah** di kode, tapi **tidak disarankan** karena:
1. Ruas jalan bisa panjangnya 5-50 km, akan muncul 50-500 marker sehingga peta penuh.
2. Performa browser akan turun drastis karena ratusan DOM element di-render sekaligus.

Solusi yang tepat jika ingin granularitas lebih halus: gunakan **popup interaktif**
saat klik polyline — yang sudah ada dan menampilkan info segmen per klik.

---

## Q5: Bagaimana polyline di peta bisa diwarnai sesuai kondisi jalan (baik/rusak)?

**A:** Sistem menggunakan teknik **"slice polyline berdasarkan proporsi STA"**:

1. **Total jarak nyata** rute dihitung dengan rumus Haversine (jarak bola bumi).
2. Setiap segmen STA (misalnya `sta_awal=0`, `sta_akhir=500`) dikonversi ke proporsi:
   ```
   d0 = (sta_awal  / panjang_ruas) x total_jarak_nyata
   d1 = (sta_akhir / panjang_ruas) x total_jarak_nyata
   ```
3. Fungsi `mapSliceByDist(d0, d1)` memotong polyline original antara jarak d0-d1
   dengan interpolasi linier antar titik koordinat.
4. Potongan polyline tersebut digambar dengan **warna sesuai kondisi**:
   - Hijau  `#10b981` = Baik
   - Kuning `#facc15` = Sedang
   - Oranye `#f97316` = Rusak Ringan
   - Merah  `#ef4444` = Rusak Berat

---

## Q6: Bagaimana jika ruas jalan tidak punya file KML (tidak ada koordinat rute)?

**A:** Sistem memiliki fallback berlapis:

```
Ada koordinat_json (KML)? --> Gunakan polyline asli dari KML
        | Tidak
        v
Ada lat/lng awal & akhir? --> Gambar GARIS LURUS dari titik awal ke titik akhir
        | Tidak
        v
Tampilkan pesan: "Koordinat Peta Belum Tersedia"
dengan instruksi untuk mengedit ruas dan menambahkan koordinat
```

Saat mode garis lurus, segmen STA tetap dipetakan secara proporsional di
sepanjang garis tersebut (fungsi `setupStraightGeometry()`).

---

## Q7: Di mana data koordinat KML disimpan di database?

**A:** Di kolom `koordinat_json` pada tabel `ruas_jalan`:

```sql
-- schema.sql
`koordinat_json` LONGTEXT NULL
COMMENT 'Polyline rute jalan (array [lng,lat]) hasil impor KML/KMZ, format JSON'
```

Format data: array JSON `[[lng, lat], [lng, lat], ...]`

Contoh isi: `[[ 105.2631, -5.4500 ], [ 105.2640, -5.4510 ], ...]`

Tipe `LONGTEXT` dipilih karena rute jalan yang panjang bisa memiliki ribuan
titik koordinat sehingga membutuhkan kapasitas penyimpanan yang besar.

---

## Q8: Kenapa format penyimpanan koordinat memakai [lng, lat] bukan [lat, lng]?

**A:** Ini mengikuti **standar KML/GeoJSON** yang menggunakan urutan `longitude, latitude`
(X, Y), sedangkan Leaflet.js menggunakan urutan `latitude, longitude`.

Oleh karena itu di kode ada konversi dua arah:

```javascript
// Saat SIMPAN dari KML (form.php baris 588):
// KML format: [lng, lat] --> simpan sebagai [lng, lat]
this.koordinatJson = JSON.stringify(coords.map(p => [p[1], p[0]]));

// Saat BACA untuk Leaflet (stripmap/index.php baris 719):
// Balik lagi: [lng, lat] --> [lat, lng] untuk Leaflet
const route = rawRoute.map(p => [p[1], p[0]]);
```

---

## Ringkasan Alur Lengkap

```
FILE KML/KMZ
    |
    | (1) User upload di halaman Edit Ruas
    v
PARSING (Browser JS)
    +-- .kmz: ekstrak via JSZip --> ambil file .kml
    +-- .kml: baca langsung
    |
    | (2) Ekstrak koordinat LineString
    v
ARRAY KOORDINAT [[lat,lng], ...]
    |
    | (3) Simpan sebagai [[lng,lat], ...] di hidden input
    v
SUBMIT FORM --> PHP RuasService --> MySQL (kolom koordinat_json LONGTEXT)
    |
    | (4) Saat halaman detail ruas dibuka
    v
PHP inject ke JS: const rawRoute = [[lng,lat], ...]
    |
    | (5) Leaflet.js render
    v
PETA INTERAKTIF
    +-- Polyline rute asli (abu-abu/ungu)
    +-- Potongan polyline per segmen STA (warna kondisi)
    +-- Marker titik awal & akhir
    +-- Label STA otomatis (interval adaptif)
```
