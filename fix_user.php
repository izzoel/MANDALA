<?php
$content = file_get_contents('app/Models/User.php');
// Remove all existing pegawaiNonAsn and peserta methods and commented ones
$content = preg_replace('/(\/\/ )?public function (pegawaiNonAsn|peserta)\(\): HasOne\s*\{.*?\}/s', '', $content);

$methods = "
    public function peserta(): HasOne
    {
        return \$this->hasOne(Peserta::class, 'user_id');
    }

    public function pegawaiNonAsn(): HasOne
    {
        return \$this->hasOne(Peserta::class, 'user_id');
    }
";
$content = str_replace("public function mahasiswa(): HasOne", ltrim($methods) . "\n    public function mahasiswa(): HasOne", $content);
file_put_contents('app/Models/User.php', $content);
