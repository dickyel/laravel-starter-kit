import os
from pathlib import Path

try:
    from gtts import gTTS
    print("OK - gTTS sudah terinstall")
except ImportError:
    print("ERROR - gTTS belum terinstall")
    print("\nInstal dulu dengan perintah:")
    print("pip install gTTS")
    print("\nSetelah install, jalankan script ini lagi.")
    exit(1)

# Tentukan folder output
output_dir = Path(__file__).parent
print(f"\nFolder output: {output_dir}")

# Daftar audio yang akan dibuat
audio_texts = {
    "absen-berhasil.mp3": "Terima kasih, Anda berhasil absen.",
    "sudah-absen.mp3": "Anda sudah melakukan absen hari ini.",
    "wajah-tidak-dikenali.mp3": "Maaf, wajah tidak dikenali. Silakan coba lagi."
}

print("\nMembuat file audio...\n")

for filename, text in audio_texts.items():
    try:
        # Gunakan gTTS dengan bahasa Indonesia
        tts = gTTS(text=text, lang='id', slow=False)
        
        # Simpan file
        output_path = output_dir / filename
        tts.save(str(output_path))
        
        print(f"OK - Berhasil: {filename}")
        print(f"  Teks: \"{text}\"")
        
    except Exception as e:
        print(f"ERROR - Gagal: {filename}")
        print(f"  Error: {e}")

print("\nSELESAI! File audio sudah dibuat.")
print(f"Lokasi: {output_dir}")
print("\nRefresh browser Anda untuk mendengar suara baru!")
