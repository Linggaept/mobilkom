<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $laporan->nomor_laporan }}</title>
    <style>
        * { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; box-sizing: border-box; }
        body { padding: 20px 30px; color: #1a1a1a; }
        .header { display: table; width: 100%; border-bottom: 3px double #0d3b8e; padding-bottom: 12px; margin-bottom: 16px; }
        .header-logos { display: table-cell; width: 80px; vertical-align: middle; }
        .header-logos img { width: 70px; height: 60px; object-fit: contain; }
        .header-title { display: table-cell; text-align: center; vertical-align: middle; }
        .header-title h2 { font-size: 15px; font-weight: bold; color: #0d3b8e; margin-bottom: 2px; }
        .header-title h3 { font-size: 12px; font-weight: normal; margin-bottom: 1px; }
        .header-title p  { font-size: 10px; color: #555; }
        .header-logo-right { display: table-cell; width: 80px; vertical-align: middle; text-align: right; }
        .header-logo-right img { width: 70px; height: 60px; object-fit: contain; }

        .nomor-laporan { background: #0d3b8e; color: #fff; padding: 8px 16px; border-radius: 4px; margin-bottom: 16px; }
        .nomor-laporan h4 { font-size: 13px; margin: 0; }
        .nomor-laporan span { font-size: 10px; opacity: 0.85; }

        table.info { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.info td { padding: 6px 10px; vertical-align: top; border: 1px solid #ddd; }
        table.info td.label { background: #f0f4ff; font-weight: bold; width: 35%; color: #0d3b8e; }

        .deskripsi-box { border: 1px solid #ddd; padding: 10px; background: #fafafa; border-radius: 4px; margin-bottom: 14px; }
        .deskripsi-box .label { font-weight: bold; color: #0d3b8e; margin-bottom: 6px; }

        .foto-box { margin-bottom: 14px; }
        .foto-box img { max-width: 250px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px; }

        .status-box { padding: 8px 12px; border-radius: 4px; margin-bottom: 16px; display: inline-block; font-weight: bold; }
        .status-menunggu_verifikasi { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-diverifikasi        { background: #cff4fc; color: #055160; border: 1px solid #0dcaf0; }
        .status-sedang_proses       { background: #cfe2ff; color: #084298; border: 1px solid #0d6efd; }
        .status-selesai             { background: #d1e7dd; color: #0f5132; border: 1px solid #198754; }
        .status-ditolak             { background: #f8d7da; color: #842029; border: 1px solid #dc3545; }

        .ttd-section { margin-top: 20px; }
        .ttd-row { display: table; width: 100%; }
        .ttd-cell { display: table-cell; width: 50%; text-align: center; padding: 0 10px; vertical-align: top; }
        .ttd-cell .ttd-name { font-weight: bold; margin-top: 4px; border-top: 1px solid #333; padding-top: 4px; font-size: 11px; }
        .ttd-cell .ttd-label { font-size: 10px; color: #555; margin-top: 2px; }
        .ttd-cell img { max-height: 80px; max-width: 160px; }
        .ttd-box { min-height: 80px; border: 1px dashed #aaa; border-radius: 4px; background: #fafafa; display: flex; align-items: center; justify-content: center; }

        .section-title { background: #0d3b8e; color: #fff; padding: 5px 10px; font-weight: bold; margin-bottom: 8px; border-radius: 3px; font-size: 11px; }
        .footer { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; text-align: center; font-size: 9px; color: #888; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>
    @include('pdf.laporan_single_partial', ['laporan' => $laporan])
</body>
</html>