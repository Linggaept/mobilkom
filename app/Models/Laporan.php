<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Laporan extends Model
{
    use SoftDeletes;

    protected $table = 'laporan';

    protected $fillable = [
        'nomor_laporan',
        'user_id',
        'nama_pelapor',
        'jabatan_pelapor',
        'tanggal_laporan',
        'site_id',
        'tipe_radio_id',
        'jenis_kerusakan_id',
        'deskripsi_kerusakan',
        'foto',
        'status',
        'teknisi_id',
        'admin_id',
        'catatan_admin',
        'catatan_teknisi',
        'tanggal_verifikasi',
        'tanggal_selesai',
        'ttd_pelapor',
        'ttd_teknisi',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function tipeRadio()
    {
        return $this->belongsTo(TipeRadio::class);
    }

    public function jenisKerusakan()
    {
        return $this->belongsTo(JenisKerusakan::class);
    }

    public static function generateNomor()
    {
        $prefix = 'INC';

        return DB::transaction(function () use ($prefix) {

            $last = self::where('nomor_laporan', 'like', $prefix . '%')
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->orderBy('nomor_laporan', 'desc')
                ->first();

            if ($last) {
                $lastNumber = (int) substr($last->nomor_laporan, 3);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            return $prefix . str_pad($nextNumber, 12, '0', STR_PAD_LEFT);
        });
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diverifikasi'        => 'Diverifikasi',
            'sedang_proses'       => 'Sedang Proses',
            'selesai'             => 'Selesai',
            'ditolak'             => 'Ditolak',
            default               => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'warning',
            'diverifikasi'        => 'info',
            'sedang_proses'       => 'primary',
            'selesai'             => 'success',
            'ditolak'             => 'danger',
            default               => 'secondary',
        };
    }

    // =============================================
    // DEADLINE & DURASI
    // =============================================

    const DEADLINE_VERIFIKASI_JAM = 24; // 1 hari
    const DEADLINE_PROSES_JAM     = 48; // 2 hari

    /** Tenggat verifikasi: created_at + 24 jam */
    public function getDeadlineVerifikasiAttribute(): Carbon
    {
        return $this->created_at->copy()->addHours(self::DEADLINE_VERIFIKASI_JAM);
    }

    /** Tenggat selesai proses: tanggal_verifikasi + 48 jam */
    public function getDeadlineProsesAttribute(): ?Carbon
    {
        return $this->tanggal_verifikasi
            ? $this->tanggal_verifikasi->copy()->addHours(self::DEADLINE_PROSES_JAM)
            : null;
    }

    /** Estimasi selesai (alias deadline_proses, ditampilkan ke pelapor) */
    public function getEstimasiSelesaiAttribute(): ?Carbon
    {
        return $this->deadline_proses;
    }

    /** Apakah melewati tenggat verifikasi */
    public function getIsOverdueVerifikasiAttribute(): bool
    {
        return $this->status === 'menunggu_verifikasi'
            && now()->gt($this->deadline_verifikasi);
    }

    /** Apakah melewati tenggat proses */
    public function getIsOverdueProsesAttribute(): bool
    {
        return in_array($this->status, ['diverifikasi', 'sedang_proses'])
            && $this->deadline_proses
            && now()->gt($this->deadline_proses);
    }

    /** Status penyelesaian: on_time, terlambat, null jika belum selesai */
    public function getStatusPenyelesaianAttribute(): ?string
    {
        if ($this->status !== 'selesai' || !$this->tanggal_selesai || !$this->deadline_proses) {
            return null;
        }
        return $this->tanggal_selesai->lte($this->deadline_proses) ? 'on_time' : 'terlambat';
    }

    /** Durasi dari dikirim sampai diverifikasi */
    public function getDurasiVerifikasiAttribute(): ?string
    {
        if (!$this->tanggal_verifikasi) return null;
        return $this->_formatDiff($this->created_at, $this->tanggal_verifikasi);
    }

    /** Durasi dari diverifikasi sampai selesai */
    public function getDurasiProsesAttribute(): ?string
    {
        if (!$this->tanggal_verifikasi || !$this->tanggal_selesai) return null;
        return $this->_formatDiff($this->tanggal_verifikasi, $this->tanggal_selesai);
    }

    /** Total durasi dari dikirim sampai selesai */
    public function getDurasiTotalAttribute(): ?string
    {
        if (!$this->tanggal_selesai) return null;
        return $this->_formatDiff($this->created_at, $this->tanggal_selesai);
    }

    /** Sisa waktu atau keterlambatan verifikasi */
    public function getSisaWaktuVerifikasiAttribute(): string
    {
        if ($this->status !== 'menunggu_verifikasi') return '-';
        return $this->_sisaWaktu($this->deadline_verifikasi);
    }

    /** Sisa waktu atau keterlambatan proses */
    public function getSisaWaktuProsesAttribute(): string
    {
        if (!in_array($this->status, ['diverifikasi', 'sedang_proses'])) return '-';
        if (!$this->deadline_proses) return '-';
        return $this->_sisaWaktu($this->deadline_proses);
    }

    private function _formatDiff(Carbon $from, Carbon $to): string
    {
        $diff = $from->diff($to);
        $totalJam = ($diff->days * 24) + $diff->h;
        if ($totalJam > 0) return $totalJam . ' jam ' . $diff->i . ' menit';
        return $diff->i . ' menit';
    }

    private function _sisaWaktu(Carbon $deadline): string
    {
        if (now()->gt($deadline)) {
            $diff = $deadline->diff(now());
            $jam  = ($diff->days * 24) + $diff->h;
            return 'Terlambat ' . ($jam > 0 ? $jam . ' jam ' : '') . $diff->i . ' menit';
        }
        $diff = now()->diff($deadline);
        $jam  = ($diff->days * 24) + $diff->h;
        return 'Sisa ' . ($jam > 0 ? $jam . ' jam ' : '') . $diff->i . ' menit';
    }

    // =============================================
    // Scopes for filtering
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['tanggal_mulai'] ?? null, fn($q, $v) => $q->whereDate('tanggal_laporan', '>=', $v));
        $query->when($filters['tanggal_akhir'] ?? null, fn($q, $v) => $q->whereDate('tanggal_laporan', '<=', $v));
        $query->when($filters['site_id'] ?? null, fn($q, $v) => $q->where('site_id', $v));
        $query->when($filters['tipe_radio_id'] ?? null, fn($q, $v) => $q->where('tipe_radio_id', $v));
        $query->when($filters['jenis_kerusakan_id'] ?? null, fn($q, $v) => $q->where('jenis_kerusakan_id', $v));
        $query->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v));
        $query->when(
            $filters['search'] ?? null,
            fn($q, $v) =>
            $q->where(
                fn($q2) => $q2->where('nomor_laporan', 'like', "%$v%")
                    ->orWhere('nama_pelapor', 'like', "%$v%")
                    ->orWhere('deskripsi_kerusakan', 'like', "%$v%")
            )
        );
        return $query;
    }
}
