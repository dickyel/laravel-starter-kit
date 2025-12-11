# CARA INSTALL & GENERATE AUDIO - GRATIS & MUDAH!

## Metode 1: Gunakan Python Script (RECOMMENDED - Paling Mudah)

### Langkah 1: Install gTTS
Buka Command Prompt / Terminal di folder ini, lalu jalankan:
```
pip install gTTS
```

### Langkah 2: Generate Audio
Jalankan script:
```
python generate_audio.py
```

✅ Selesai! 3 file MP3 akan otomatis dibuat dengan suara Google Indonesia (suara wanita natural)

---

## Metode 2: Download Manual dari Website (Tanpa Install Apapun)

### Website Gratis Tanpa Daftar:

1. **TTSMaker** (RECOMMENDED - Paling Natural):
   - Website: https://ttsmaker.com/
   - Pilih bahasa: **Indonesian**
   - Pilih voice: **Female** (biasanya ada Sinta atau Gadis)
   - Input teks, klik "Convert to Speech", lalu download MP3

2. **Narakeet**:
   - Website: https://www.narakeet.com/languages/indonesian-text-to-speech/
   - Pilih voice Indonesia (biasanya Sinta)
   - Langsung download MP3

3. **VoiceMaker**:
   - Website: https://voicemaker.in/
   - Pilih: Indonesian > Female voice
   - Download sebagai MP3

### Teks yang Perlu Di-convert:

**File 1: absen-berhasil.mp3**
```
Terima kasih, Anda berhasil absen.
```

**File 2: sudah-absen.mp3**
```
Anda sudah melakukan absen hari ini.
```

**File 3: wajah-tidak-dikenali.mp3** (opsional)
```
Maaf, wajah tidak dikenali. Silakan coba lagi.
```

---

## Metode 3: Download Langsung (Sudah Saya Sediakan Link)

Saya akan generate dan berikan link download langsung...

---

## Setelah Punya File MP3:

1. Letakkan 3 file MP3 di folder: `public/assets/audio/`
2. Refresh browser kiosk
3. ✅ Suara robot hilang, diganti suara wanita natural!

---

## Troubleshooting:

**Q: Masih terdengar robot?**
A: Berarti file MP3 belum ada. Cek di `public/assets/audio/` apakah sudah ada file:
   - absen-berhasil.mp3
   - sudah-absen.mp3

**Q: File sudah ada tapi masih robot?**
A: Buka Console browser (F12), cek apakah ada error "Audio file tidak tersedia"

**Q: Ingin ganti suara?**
A: Tinggal replace file MP3 dengan yang baru, refresh browser.
