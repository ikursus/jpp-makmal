<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Mohd Faiz bin Razak',
            'email' => 'faiz@jpp.gov.my',
            'password' => Hash::make('password'),
            'phone' => '088-123400',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        // Admin (HQ)
        $admin = User::create([
            'name' => 'Siti binti Abdullah',
            'email' => 'siti@jpp.gov.my',
            'password' => Hash::make('password'),
            'phone' => '088-123401',
            'district_id' => District::where('code', 'KK')->first()->id,
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Users (Pegawai Daerah) - 500 sample users
        $districtIds = District::pluck('id')->toArray();

        // Nama-nama Melayu
        $melayuMale = [
            'Ahmad', 'Mohd', 'Muhammad', 'Abdul', 'Ismail', 'Hassan', 'Roslan', 'Kamal', 'Zainal', 'Azman',
            'Rahim', 'Shamsul', 'Fauzi', 'Jamil', 'Rashid', 'Yusof', 'Bakar', 'Daud', 'Harun', 'Ibrahim',
            'Khalid', 'Latif', 'Mahmud', 'Nasir', 'Omar', 'Razak', 'Saad', 'Tajuddin', 'Wahab', 'Zakaria',
            'Amran', 'Baharuddin', 'Che', 'Dollah', 'Ghani', 'Hamid', 'Jusoh', 'Kasim', 'Leman', 'Mamat',
            'Nordin', 'Osman', 'Puteh', 'Ramli', 'Salleh', 'Talib', 'Umar', 'Yahya', 'Zainuddin', 'Aziz',
        ];
        $melayuFemale = [
            'Siti', 'Fatimah', 'Aminah', 'Nor', 'Zainab', 'Halimah', 'Rohani', 'Khadijah', 'Mariam', 'Salbiah',
            'Azizah', 'Bibi', 'Che', 'Dewi', 'Faridah', 'Habibah', 'Indah', 'Jamilah', 'Kartini', 'Latifah',
            'Mahani', 'Nazirah', 'Puspa', 'Rahmah', 'Saodah', 'Tini', 'Umi', 'Wan', 'Yusnita', 'Zaiton',
            'Ain', 'Balkis', 'Cik', 'Diana', 'Ema', 'Fauziah', 'Gayatri', 'Hasnah', 'Intan', 'Junaidah',
            'Kamariah', 'Laila', 'Masitah', 'Nadia', 'Puteri', 'Rafidah', 'Suriani', 'Tengku', 'Wardah', 'Zarina',
        ];

        // Nama-nama Cina
        $cinaMale = [
            'Ah Chong', 'Ah Meng', 'Ah Hock', 'Ah Kow', 'Ah Soon', 'Beng Hock', 'Chee Keong', 'Chin Fatt', 'Chong Wei', 'Eng Hock',
            'Fook Yew', 'Guan Huat', 'Heng Lee', 'Hock Seng', 'Hong Chuan', 'Joo Ming', 'Kah Wai', 'Kean Yew', 'Kok Leong', 'Kuan Yew',
            'Lai Fatt', 'Lee Meng', 'Lian Huat', 'Meng Wai', 'Mun Keat', 'Peng Soon', 'Poh Chuan', 'Seng Huat', 'Soon Lee', 'Swee Keong',
            'Tat Fatt', 'Teck Meng', 'Tian Huat', 'Wai Leong', 'Weng Yew', 'Wing Keong', 'Yew Ming', 'Yong Chuan', 'Yuen Fatt', 'Ah Beng',
            'Ah Lian', 'Boon Kiat', 'Chai Soon', 'Chuan Huat', 'Eng Keat', 'Fong Yew', 'Hin Leong', 'Jin Huat', 'Kee Meng', 'Liang Huat',
        ];
        $cinaFemale = [
            'Ah Moi', 'Ah Yoke', 'Bee Hong', 'Chai Ling', 'Chin Chin', 'Chooi Yee', 'Eng Eng', 'Fong Fong', 'Geok Lan', 'Hooi Ling',
            'Jia En', 'Kai Ling', 'Kheng Yee', 'Kok Yee', 'Lai Kuan', 'Lay Hong', 'Lee Lee', 'Lian Lian', 'Mei Ling', 'Mei Yee',
            'Moi Moi', 'Oi Ling', 'Pik Wah', 'Poh Yee', 'Siew Hong', 'Siew Ling', 'Siew Yee', 'Soo Ling', 'Sook Yee', 'Swee Lan',
            'Wai Yee', 'Wan Ling', 'Yee Ling', 'Yen Yen', 'Yoke Lan', 'Yoke Yee', 'Yoon Ling', 'Yuen Yee', 'Ah Ying', 'Bee Bee',
            'Chai Yee', 'Chooi Ling', 'Eng Yee', 'Fong Yee', 'Geok Yee', 'Hooi Yee', 'Kai Yee', 'Kheng Ling', 'Lai Yee', 'Lay Yee',
        ];

        // Nama-nama Kadazan/Dusun
        $kadazanMale = [
            'Johnny', 'Peter', 'Joseph', 'Michael', 'Francis', 'Albert', 'Patrick', 'Richard', 'Robert', 'James',
            'Gilbert', 'Edward', 'Alfred', 'Bernard', 'Charles', 'Daniel', 'Edwin', 'Felix', 'George', 'Henry',
            'Ignatius', 'Jeffrey', 'Kenneth', 'Lawrence', 'Martin', 'Nicholas', 'Oliver', 'Paul', 'Raymond', 'Stephen',
            'Thomas', 'Victor', 'William', 'Alexander', 'Benedict', 'Christopher', 'Dominic', 'Eugene', 'Frederick', 'Gregory',
            'Herman', 'Julius', 'Kelvin', 'Leonard', 'Maximilian', 'Norbert', 'Oswald', 'Philip', 'Rudolph', 'Sebastian',
        ];
        $kadazanFemale = [
            'Mary', 'Margaret', 'Catherine', 'Elizabeth', 'Rose', 'Anna', 'Teresa', 'Helen', 'Jane', 'Lucy',
            'Agnes', 'Beatrice', 'Clara', 'Diana', 'Elena', 'Florence', 'Grace', 'Hilda', 'Irene', 'Joan',
            'Kathleen', 'Lilian', 'Martha', 'Nancy', 'Olivia', 'Patricia', 'Queenie', 'Rita', 'Stella', 'Ursula',
            'Veronica', 'Wendy', 'Yvonne', 'Zita', 'Adeline', 'Bernadette', 'Caroline', 'Dorothy', 'Edith', 'Frances',
            'Gertrude', 'Harriet', 'Isabel', 'Jacqueline', 'Katherine', 'Louise', 'Matilda', 'Natalie', 'Pauline', 'Rosemary',
        ];

        // Nama-nama Bidayuh
        $bidayuhMale = [
            'Dominic', 'Simon', 'Andrew', 'Anthony', 'Benedict', 'Clement', 'David', 'Edmund', 'Fabian', 'Gabriel',
            'Hilary', 'Isaac', 'Jeremiah', 'Kieran', 'Leo', 'Mathew', 'Noah', 'Owen', 'Pius', 'Quintin',
            'Raphael', 'Samuel', 'Timothy', 'Urban', 'Valentine', 'Wilfred', 'Xavier', 'Zachary', 'Adrian', 'Basil',
            'Cornelius', 'Desmond', 'Elias', 'Ferdinand', 'Gideon', 'Hugh', 'Ivan', 'Jerome', 'Kevin', 'Lucius',
            'Moses', 'Nathan', 'Octavius', 'Percival', 'Reginald', 'Silas', 'Titus', 'Vincent', 'Winston', 'Yusup',
        ];
        $bidayuhFemale = [
            'Anita', 'Brenda', 'Cecilia', 'Deborah', 'Esther', 'Felicia', 'Gloria', 'Hannah', 'Imelda', 'Julia',
            'Karen', 'Lydia', 'Monica', 'Nora', 'Ophelia', 'Priscilla', 'Rachel', 'Sarah', 'Tabitha', 'Una',
            'Valerie', 'Winifred', 'Yolanda', 'Zoe', 'Amelia', 'Bianca', 'Celestine', 'Daphne', 'Eunice', 'Faith',
            'Georgina', 'Helena', 'Ivy', 'Judith', 'Laura', 'Melissa', 'Nadia', 'Pearl', 'Rebecca', 'Susanna',
            'Theresa', 'Vivian', 'Winnie', 'Xena', 'Yvette', 'Abigail', 'Beatrix', 'Camilla', 'Daisy', 'Eva',
        ];

        // Surnames
        $melayuSurname = ['bin Ismail', 'bin Abdullah', 'bin Hassan', 'bin Ahmad', 'bin Mohd Ali', 'bin Razak', 'bin Karim', 'bin Osman', 'bin Yusof', 'bin Hashim',
                          'binti Ismail', 'binti Abdullah', 'binti Hassan', 'binti Ahmad', 'binti Mohd Ali', 'binti Razak', 'binti Karim', 'binti Osman', 'binti Yusof', 'binti Hashim'];
        $cinaSurname = ['Tan', 'Lim', 'Wong', 'Chong', 'Lee', 'Ong', 'Koh', 'Ng', 'Chin', 'Teo',
                        'Lau', 'Chua', 'Yap', 'Goh', 'Loh', 'Ting', 'Sim', 'Khoo', 'Liew', 'Foo'];
        $kadazanSurname = ['@', ' anak', ' ak', ' bin', ' binti', ' a/l', ' a/p', ' @', ' ak', ' anak'];
        $bidayuhSurname = [' ak', ' anak', ' a/l', ' a/p', ' @', ' bin', ' binti', ' ak', ' anak', ' @'];

        $phonePrefixes = ['088', '089', '087', '086', '085', '084', '083', '082'];
        $emails = []; // track used emails

        $generatePhone = function () use ($phonePrefixes) {
            $prefix = $phonePrefixes[array_rand($phonePrefixes)];
            $number = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            return $prefix . '-' . $number;
        };

        $generateEmail = function ($name) use (&$emails) {
            $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
            $email = $base . '@jpp.gov.my';
            $counter = 1;
            while (in_array($email, $emails)) {
                $email = $base . $counter . '@jpp.gov.my';
                $counter++;
            }
            $emails[] = $email;
            return $email;
        };

        $ethnicGroups = [
            'melayu' => ['male' => $melayuMale, 'female' => $melayuFemale, 'surname' => $melayuSurname],
            'cina' => ['male' => $cinaMale, 'female' => $cinaFemale, 'surname' => $cinaSurname],
            'kadazan' => ['male' => $kadazanMale, 'female' => $kadazanFemale, 'surname' => $kadazanSurname],
            'bidayuh' => ['male' => $bidayuhMale, 'female' => $bidayuhFemale, 'surname' => $bidayuhSurname],
        ];

        // 125 users per ethnic group = 500 total
        $usersPerGroup = 125;

        foreach ($ethnicGroups as $group => $data) {
            $maleNames = $data['male'];
            $femaleNames = $data['female'];
            $surnames = $data['surname'];

            for ($i = 0; $i < $usersPerGroup; $i++) {
                $isMale = ($i % 2 === 0);
                $firstName = $isMale ? $maleNames[array_rand($maleNames)] : $femaleNames[array_rand($femaleNames)];
                $surname = $surnames[array_rand($surnames)];

                if ($group === 'cina') {
                    $fullName = $firstName . ' ' . $surname;
                } elseif ($group === 'kadazan' || $group === 'bidayuh') {
                    $fullName = $firstName . $surname;
                } else {
                    // Melayu
                    $fullName = $firstName . ' ' . $surname;
                }

                $email = $generateEmail($fullName);
                $phone = $generatePhone();
                $districtId = $districtIds[array_rand($districtIds)];
                $isActive = mt_rand(0, 10) > 1; // ~90% active

                $user = User::create([
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'phone' => $phone,
                    'district_id' => $districtId,
                    'is_active' => $isActive,
                ]);
                $user->assignRole('user');
            }
        }

        $this->command->info('500 sample users created successfully!');
    }
}
