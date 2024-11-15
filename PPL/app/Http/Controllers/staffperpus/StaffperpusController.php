<?php

namespace App\Http\Controllers\staffperpus;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\kategori_buku;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\buku;


class StaffperpusController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $transaksi_peminjaman = DB::table('transaksi_peminjaman')
            ->join('buku', 'transaksi_peminjaman.id_buku', '=', 'buku.id_buku')
            ->join('kategori_buku', 'buku.id_kategori_buku', '=', 'kategori_buku.id_kategori_buku')
            ->join('jenis_buku', 'buku.id_jenis_buku', '=', 'jenis_buku.id_jenis_buku')
            ->limit(7)
            ->get();

        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString(); // Get the date 7 days ago

        $transactionsevendays = DB::table('transaksi_peminjaman')
            ->join('buku', 'transaksi_peminjaman.id_buku', '=', 'buku.id_buku')
            ->where('transaksi_peminjaman.tgl_awal_Peminjaman', '>', $sevenDaysAgo)
            ->orderBy('transaksi_peminjaman.tgl_awal_Peminjaman', 'asc')
            ->get();

        $all = DB::table('transaksi_peminjaman')
            ->join('buku', 'transaksi_peminjaman.id_buku', '=', 'buku.id_buku')
            ->get();
        $book = DB::table('buku')
            ->get();
        $book10 = DB::table('buku')
            ->orderBy('tgl_ditambahkan', 'desc')
            ->limit(7)
            ->get();
        $cat10 = DB::table('kategori_buku')
            ->limit(7)
            ->get();
        $totalCategory = DB::table('kategori_buku')
            ->count();
        return view('staff_perpus.dashboard', ['transaksi' => $transaksi_peminjaman, 'transactionsevendays' => $transactionsevendays, 'alltrans' => $all, 'buku' => $book, 'buku10' => $book10, 'cat10' => $cat10, 'totalCategory' => $totalCategory]);
    }

    public function daftarbuku(Request $request)
    {

        // Ambil data search dan kategori dari query string
        $search = $request->input('search');
        $kategori_buku = $request->input('kategori_buku');

        $query = buku::query();

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('judul_buku', 'like', "%{$search}%")
                ->orWhere('author_buku', 'like', "%{$search}%")
                ->orWhereHas('kategoriBuku', function ($q) use ($search) {
                    $q->where('nama_kategori', 'like', "%{$search}%");
                })
                ->orWhereHas('jenisBuku', function ($q) use ($search) {
                    $q->where('nama_jenis_buku', 'like', "%{$search}%");
                });
        }

        // Filter berdasarkan kategori jika dipilih
        if ($request->filled('kategori')) {
            $query->where('id_kategori_buku', $request->kategori);
        }

        // Mengambil buku yang difilter dan diurutkan berdasarkan `tgl_ditambahkan`
        $buku = $query->orderBy('tgl_ditambahkan', 'desc')->paginate(10);

        // Mengambil daftar kategori untuk dropdown
        $kategoriBuku = DB::table('kategori_buku')->get();

        return view('staff_perpus.buku.daftarbuku', compact('buku', 'kategoriBuku'));
    }

    // Menampilkan form tambah buku
    public function createbuku()
    {
        $kategoriBuku = DB::table('kategori_buku')->get();
        $jenisBuku = DB::table('jenis_buku')->get(); // Jika juga perlu jenis buku

        return view('staff_perpus.buku.create', compact('kategoriBuku', 'jenisBuku'));
    }

    // Menyimpan buku baru
    public function storebuku(Request $request)
    {
        $request->validate([
            'foto_buku' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'judul_buku' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if (buku::where('judul_buku', $value)->where('author_buku', $request->author_buku)->exists()) {
                        $fail('Buku dengan judul dan author yang sama sudah ada.');
                    }
                },
            ],
            'author_buku' => 'required|string|max:255',
            'rak_buku' => 'required|integer|min:0',
            'id_kategori_buku' => 'required|exists:kategori_buku,id_kategori_buku',
            'id_jenis_buku' => 'required|exists:jenis_buku,id_jenis_buku',
            'stok_buku' => 'required|integer|min:0',
            'tahun_terbit' => 'required|string|max:4',
            'bahasa_buku' => 'required|string|max:255',
            'publisher_buku' => 'required|string|max:255',
        ], [
            // Pesan error custom untuk setiap field yang required
            'foto_buku.required' => 'Foto buku harus diunggah.',
            'judul_buku.required' => 'Judul buku harus diisi.',
            'author_buku.required' => 'Author buku harus diisi.',
            'rak_buku.required' => 'Rak buku harus diisi.',
            'id_kategori_buku.required' => 'Kategori buku harus dipilih.',
            'id_jenis_buku.required' => 'Jenis buku harus dipilih.',
            'stok_buku.required' => 'Stok buku harus diisi.',
            'tahun_terbit.required' => 'Tahun terbit harus diisi.',
            'bahasa_buku.required' => 'Bahasa buku harus diisi.',
            'publisher_buku.required' => 'Publisher buku harus diisi.',

            // Pesan error untuk validasi custom
            'foto_buku.image' => 'Foto buku harus berupa file gambar.',
            'judul_buku.max' => 'Judul buku tidak boleh lebih dari 255 karakter.',
            'author_buku.max' => 'Author buku tidak boleh lebih dari 255 karakter.',
            'rak_buku.integer' => 'Rak buku harus berupa angka.',
            'stok_buku.integer' => 'Stok buku harus berupa angka.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh lebih dari 4 karakter.',
        ]);

        $fotoBuku = null;
        if ($request->hasFile('foto_buku')) {
            $fotoBuku = $request->file('foto_buku')->store('public/buku');
        }

        buku::create([
            'foto_buku' => $fotoBuku,
            'judul_buku' => $request->judul_buku,
            'author_buku' => $request->author_buku,
            'rak_buku' => $request->rak_buku,
            'id_kategori_buku' => $request->id_kategori_buku,
            'id_jenis_buku' => $request->id_jenis_buku,
            'stok_buku' => $request->stok_buku,
            'tahun_terbit' => $request->tahun_terbit,
            'bahasa_buku' => $request->bahasa_buku,
            'publisher_buku' => $request->publisher_buku,
            'tgl_ditambahkan' => now(),
        ]);

        return redirect()->route('staff_perpus.buku.daftarbuku')->with('success', 'Buku berhasil ditambahkan!');
    }


    public function editbuku($id)
    {
        $buku = buku::findOrFail($id);
        $kategoriBuku = DB::table('kategori_buku')->get();
        $jenisBuku = DB::table('jenis_buku')->get();

        return view('staff_perpus.buku.edit', compact('buku', 'kategoriBuku', 'jenisBuku'));
    }

    public function updatebuku(Request $request, $id)
    {
        $request->validate([
            'foto_buku' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'judul_buku' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $id) {
                    if (buku::where('judul_buku', $value)
                        ->where('author_buku', $request->author_buku)
                        ->where('id_buku', '!=', $id) // Mengecualikan buku yang sedang diedit
                        ->exists()
                    ) {
                        $fail('Buku dengan judul dan author yang sama sudah ada.');
                    }
                },
            ],
            'author_buku' => 'required|string|max:255',
            'rak_buku' => 'required|integer|min:0',
            'id_kategori_buku' => 'required|exists:kategori_buku,id_kategori_buku',
            'id_jenis_buku' => 'required|exists:jenis_buku,id_jenis_buku',
            'stok_buku' => 'required|integer|min:0',
            'tahun_terbit' => 'required|string|max:4',
            'bahasa_buku' => 'required|string|max:255',
            'publisher_buku' => 'required|string|max:255',
        ], [
            // Pesan error custom untuk setiap field yang required
            'foto_buku.required' => 'Foto buku harus diunggah.',
            'judul_buku.required' => 'Judul buku harus diisi.',
            'author_buku.required' => 'Author buku harus diisi.',
            'rak_buku.required' => 'Rak buku harus diisi.',
            'id_kategori_buku.required' => 'Kategori buku harus dipilih.',
            'id_jenis_buku.required' => 'Jenis buku harus dipilih.',
            'stok_buku.required' => 'Stok buku harus diisi.',
            'tahun_terbit.required' => 'Tahun terbit harus diisi.',
            'bahasa_buku.required' => 'Bahasa buku harus diisi.',
            'publisher_buku.required' => 'Publisher buku harus diisi.',

            // Pesan error untuk validasi custom
            'foto_buku.image' => 'Foto buku harus berupa file gambar.',
            'judul_buku.max' => 'Judul buku tidak boleh lebih dari 255 karakter.',
            'author_buku.max' => 'Author buku tidak boleh lebih dari 255 karakter.',
            'rak_buku.integer' => 'Rak buku harus berupa angka.',
            'stok_buku.integer' => 'Stok buku harus berupa angka.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh lebih dari 4 karakter.',
        ]);

        $buku = buku::findOrFail($id);
        if ($request->hasFile('foto_buku')) {
            if ($buku->foto_buku) {
                Storage::delete($buku->foto_buku);
            }
            $buku->foto_buku = $request->file('foto_buku')->store('public/buku');
        }

        $buku->update([
            'judul_buku' => $request->judul_buku,
            'author_buku' => $request->author_buku,
            'rak_buku' => $request->rak_buku,
            'id_kategori_buku' => $request->id_kategori_buku,
            'id_jenis_buku' => $request->id_jenis_buku,
            'stok_buku' => $request->stok_buku,
            'tahun_terbit' => $request->tahun_terbit,
            'bahasa_buku' => $request->bahasa_buku,
            'publisher_buku' => $request->publisher_buku,
        ]);

        return redirect()->route('staff_perpus.buku.daftarbuku')->with('success', 'Buku berhasil diperbarui!');
    }


    public function destroybuku($id)
    {
        $buku = buku::findOrFail($id);
        if ($buku->foto_buku) {
            Storage::delete($buku->foto_buku);
        }
        $buku->delete();

        return redirect()->route('staff_perpus.buku.daftarbuku')->with('success', 'Buku berhasil dihapus!');
    }
}
