Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead('e:\projek pkl\backup\laravel_pkl\bot-antrian (2)\bot-antrian\bot-antrian (3)\bot-antrian (30)\bot-antrian\public\cek_skripsi\SKRIPSI - cek.docx')
$entry = $zip.GetEntry('word/document.xml')
$stream = $entry.Open()
$reader = New-Object System.IO.StreamReader($stream)
$xml = $reader.ReadToEnd()
$reader.Close()
$stream.Close()
$zip.Dispose()
$xml = $xml -replace '<[^>]+>', ' ' -replace '\s+', ' '
Out-File -FilePath 'e:\projek pkl\backup\laravel_pkl\bot-antrian (2)\bot-antrian\bot-antrian (3)\bot-antrian (30)\bot-antrian\public\cek_skripsi\extracted.txt' -InputObject $xml -Encoding utf8
