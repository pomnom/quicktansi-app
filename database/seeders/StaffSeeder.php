<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Staff::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $instansi = 'Badan Pengelolaan Keuangan dan Aset Daerah';

        $data = [
            ['199906282025061003', 'M. YUSRIL RAIHAN S.Kom',              'Fungsional Umum',                                    'III/a'],
            ['200105112025062005', 'FIRLANA FAJRI S.Ak',                  'Fungsional Umum',                                    'III/a'],
            ['200107062025061002', 'MOHAMMAD ATSYAFE\'I PUTRA S.Kom',     'Fungsional Umum',                                    'III/a'],
            ['200008142025061002', 'MUH ROIS ANSARI S.Ak',                'Fungsional Umum',                                    'III/a'],
            ['199404112025062001', 'EKA SULFIAH S.Ak',                    'Fungsional Umum',                                    'III/a'],
            ['199310052025062002', 'RIZKIANA ISKANDAR S.E',               'Fungsional Umum',                                    'III/a'],
            ['199605222022032020', 'EKAWATI SURYA PERDANA, A.MD.',        'Fungsional Umum',                                    'II/c'],
            ['199908302022032009', 'SRIAIDA SANGGAR WATI, A.MD.AK',      'Fungsional Umum',                                    'II/c'],
            ['199805202022032026', 'KRISTAL ROSALINA, A.MD.AK.',         'Fungsional Umum',                                    'II/c'],
            ['199606272022031009', 'INDRA ALAM MUZZAKIR, S.H',           'Fungsional Umum',                                    'III/a'],
            ['199005142022032006', 'I GUSTI AYU PUTU METRIANI, S.E.',    'Fungsional Umum',                                    'III/a'],
            ['199903312020081001', 'AMAR MUHAIMIN, S.IP',                 'Fungsional Umum',                                    'III/b'],
            ['199708022019082003', 'ANNISA FADILLAH,S.STP',              'Fungsional Umum',                                    'III/b'],
            ['198908132015032005', 'FAHITATUL QAROMA,A.MD',              'Fungsional Umum',                                    'III/a'],
            ['198712312019032010', 'SUCIATI, SE',                         'Fungsional Umum',                                    'III/b'],
            ['199509262019031005', 'M ABDUL SALAM, SM',                   'Fungsional Umum',                                    'III/b'],
            ['199202092019031007', 'LUKMAN BIMA SAPUTRA,S.AKUN',         'Fungsional Umum',                                    'III/b'],
            ['197808082009011006', 'AGUS MULYADIN',                       'Fungsional Umum',                                    'II/d'],
            ['197210082008011018', 'SYAFRUDDIN',                          'Fungsional Umum',                                    'II/d'],
            ['198106272012122002', 'JUNI KURNIAWATI',                     'Fungsional Umum',                                    'III/a'],
            ['199509292017082001', 'ADE LYTA RIZKY AMELYA, S.IP',        'Fungsional Umum',                                    'III/c'],
            ['197309122012121003', 'SUDARMAWAN SEPTOHADI',                'Fungsional Umum',                                    'II/d'],
            ['198806302015032008', 'HIKMAH LAELI,S.SI',                  'Fungsional Umum',                                    'III/c'],
            ['198208302009012006', 'ATIAH',                               'Fungsional Umum',                                    'III/a'],
            ['197011122010011003', 'RAMADHAN, SE',                        'Fungsional Umum',                                    'III/a'],
            ['197408262007012011', 'RAHMANIAH',                           'Fungsional Umum',                                    'III/a'],
            ['197305122005011010', 'ABDULLAH,SE',                         'Fungsional Umum',                                    'III/b'],
            ['196904282009011007', 'MUHAMMAD SALAHUDDIN',                 'Fungsional Umum',                                    'III/a'],
            ['199105062012062001', 'FITRIYAH, S.STP',                    'ESELON 4A',                                          'III/d'],
            ['198307302008012009', 'FERAWATI',                            'Fungsional Umum',                                    'III/a'],
            ['197911222012122003', 'DIAH NOVIYANTI,AMD',                  'Fungsional Umum',                                    'III/b'],
            ['198201072012122003', 'ERA LEADY DIANA, SE',                 'Fungsional Umum',                                    'III/b'],
            ['198203012014082004', 'ST.SA\'ADAH,S.P',                    'Fungsional Umum',                                    'III/c'],
            ['197912042010011013', 'L.MOH HASIBUAN, S.KOM',              'ESELON 3B',                                          'III/d'],
            ['198308052011011003', 'R. DEDI DARMA PRAMANA, SE, M.AK',    'ESELON 4A',                                          'III/d'],
            ['197906122011012007', 'ARIAWANTI, SE',                       'Fungsional Umum',                                    'III/d'],
            ['197605192011012006', 'SRI MURNIATI, SE',                    'Fungsional Umum',                                    'III/d'],
            ['198510122005011002', 'AJHAR, SE. M.AK',                    'ESELON 4A',                                          'III/d'],
            ['198104222011012004', 'ROSMIATI, SE',                        'Fungsional Umum',                                    'III/d'],
            ['198005192003122013', 'SRI WAHYUNI',                         'Fungsional Umum',                                    'III/d'],
            ['197408152009031004', 'GUNAWAN, SE',                         'ESELON 4A',                                          'III/d'],
            ['197812072009031005', 'MUH.IRFAN, M.AK',                    'ESELON 4A',                                          'IV/a'],
            ['197103262006041014', 'HARTO UTOMO',                         'Fungsional Umum',                                    'III/d'],
            ['198002232002122003', 'HENNY RAHMAWATI, A.MD',              'Fungsional Umum',                                    'III/c'],
            ['197812012012121005', 'IMAM MUSLIM,S.SOS',                   'ESELON 4A',                                          'III/d'],
            ['197702112010011003', 'FERI IRVAN SE',                       'ESELON 4A',                                          'III/d'],
            ['197907172010012010', 'JUHAERATUL JUNARIAH, SE',             'Fungsional Umum',                                    'III/d'],
            ['197002242002121003', 'AKHMAD',                              'ESELON 4A',                                          'III/d'],
            ['197711232000032004', 'ERNA PUJI ASTUTI',                    'ESELON 3B',                                          'IV/a'],
            ['197911132010012018', 'WITA HARDININGSIH, SE',               'ESELON 4A',                                          'III/d'],
            ['197101011998021009', 'ABDUL KARIM, S.SOS.',                 'ESELON 3B',                                          'IV/a'],
            ['197010181992031003', 'DRS.ABDI MUSLIMIN',                   'ESELON 3B',                                          'IV/a'],
            ['197610232007011010', 'RASULUDDIN ,SE',                      'ESELON 3A',                                          'IV/b'],
            ['197007221993032009', 'NURHAYATI, S.SOS',                    'ANALIS KEUANGAN PUSAT DAN DAERAH AHLI MUDA',         'III/d'],
            ['197310071998031006', 'MUHAMMAD SYAHRONI, SP, MM',           'ESELON 2B',                                          'IV/c'],
            ['198404162009012006', 'FINA NOFIANTI',                       'Fungsional Umum',                                    'III/a'],
            ['197511212024211001', 'Ardyansyah, SE',                      'Fungsional Umum',                                    'III/a'],
            ['198202222024212008', 'Lu\'luwal Marjan, SE',                'Fungsional Umum',                                    'III/a'],
            ['197703102025211017', 'Eka Mulyadi, SE',                     'Fungsional Umum',                                    'III/a'],
            ['198101122025211025', 'Muslim Ya\'luddin, SE',               'Fungsional Umum',                                    'III/a'],
            ['198306112025212018', 'Ida Fitriani, SE',                    'Fungsional Umum',                                    'III/a'],
            ['198312312025212047', 'Anggun Khusnul Khatimah, SE',         'Fungsional Umum',                                    'III/a'],
            ['198403152025212026', 'Sri Ramadani, S.Si',                  'Fungsional Umum',                                    'III/a'],
            ['198404102025212045', 'Multiningsih, SE',                    'Fungsional Umum',                                    'III/a'],
            ['198711062025212032', 'Kristiamayangsari, SE',               'Fungsional Umum',                                    'III/a'],
            ['198903152025211037', 'Khaerul Akbar, S.Pt',                 'Fungsional Umum',                                    'III/a'],
            ['199307092025212026', 'Rosdiana, SE',                        'Fungsional Umum',                                    'III/a'],
            ['197604072025212011', 'Suhada, A.Md',                        'Fungsional Umum',                                    'II/c'],
            ['198106102025212027', 'Siti Fatimah Tussyauri, A.Md',       'Fungsional Umum',                                    'II/c'],
            ['198503032025212040', 'Endah, A.Md',                         'Fungsional Umum',                                    'II/c'],
            ['198609032025211033', 'Fitra Haerul Jamal, A.Md',           'Fungsional Umum',                                    'II/c'],
            ['197712312025211063', 'Iswadi',                              'Fungsional Umum',                                    'II/a'],
            ['197803082025212016', 'Sri Wahyuningsih',                    'Fungsional Umum',                                    'II/a'],
            ['198009292025212019', 'Asfurah',                             'Fungsional Umum',                                    'II/a'],
            ['198306272025211034', 'Muhammad Najib',                      'Fungsional Umum',                                    'II/a'],
            ['198604062025212026', 'Lia Andri Astuti',                    'Fungsional Umum',                                    'II/a'],
            ['199007152025212042', 'Rostiati',                            'Fungsional Umum',                                    'II/a'],
        ];

        foreach ($data as [$nip, $nama, $jabatan, $golongan]) {
            Staff::create([
                'instansi' => $instansi,
                'nip'      => $nip,
                'nama'     => $nama,
                'jabatan'  => $jabatan,
                'golongan' => $golongan,
                'status'   => null,
            ]);
        }

        $this->command->info('Staff selesai: ' . count($data) . ' pegawai.');
    }
}
