<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\District;
use App\Models\EmploymentType;
use App\Models\Industry;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Accounting / Finance', 'Administration', 'Human Resources', 'Marketing',
            'Sales', 'IT / Software', 'Engineering', 'Customer Service',
            'Supply Chain', 'Research and Development', 'Design and Creative',
            'Legal', 'Healthcare', 'Education',
        ] as $name) {
            JobCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        foreach ([
            'Banking and Finance', 'NGO and Development', 'IT and Telecom',
            'Garments and Textile', 'Pharmaceuticals', 'Education', 'Healthcare',
            'Manufacturing', 'Real Estate', 'Retail and Wholesale', 'FMCG',
            'Hospitality and Tourism', 'Agriculture', 'Logistics', 'Media and Advertising',
        ] as $name) {
            Industry::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        foreach ([
            'Software Engineer', 'Web Developer', 'Accountant', 'Marketing Executive',
            'HR Executive', 'Sales Executive', 'Graphic Designer', 'Civil Engineer',
            'Teacher', 'Customer Service Executive',
        ] as $name) {
            JobPosition::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        foreach (['Entry level', 'Mid level', 'Senior level', 'Executive'] as $name) {
            JobLevel::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        foreach (['Full time', 'Part time', 'Contract', 'Internship', 'Freelance'] as $name) {
            EmploymentType::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        foreach ([
            'PHP', 'Laravel', 'JavaScript', 'Python', 'Accounting', 'Communication',
            'Project Management', 'Microsoft Excel', 'Graphic Design',
        ] as $name) {
            Skill::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        $locations = [
            'Dhaka' => ['Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Kishoreganj', 'Madaripur', 'Manikganj', 'Munshiganj', 'Narayanganj', 'Narsingdi', 'Rajbari', 'Shariatpur', 'Tangail'],
            'Chattogram' => ['Bandarban', 'Brahmanbaria', 'Chandpur', 'Chattogram', 'Cumilla', "Cox's Bazar", 'Feni', 'Khagrachhari', 'Lakshmipur', 'Noakhali', 'Rangamati'],
            'Rajshahi' => ['Bogura', 'Joypurhat', 'Naogaon', 'Natore', 'Chapai Nawabganj', 'Pabna', 'Rajshahi', 'Sirajganj'],
            'Khulna' => ['Bagerhat', 'Chuadanga', 'Jashore', 'Jhenaidah', 'Khulna', 'Kushtia', 'Magura', 'Meherpur', 'Narail', 'Satkhira'],
            'Barishal' => ['Barguna', 'Barishal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur'],
            'Sylhet' => ['Habiganj', 'Moulvibazar', 'Sunamganj', 'Sylhet'],
            'Rangpur' => ['Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh', 'Rangpur', 'Thakurgaon'],
            'Mymensingh' => ['Jamalpur', 'Mymensingh', 'Netrokona', 'Sherpur'],
        ];

        foreach ($locations as $divisionName => $districtNames) {
            $division = Division::firstOrCreate(['name' => $divisionName]);
            foreach ($districtNames as $districtName) {
                District::firstOrCreate([
                    'division_id' => $division->id,
                    'name' => $districtName,
                ]);
            }
        }

        $upazilas = [
            'Dhaka' => ['Dhamrai', 'Dohar', 'Keraniganj', 'Nawabganj', 'Savar'],
            'Gazipur' => ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur'],
            'Chattogram' => ['Anwara', 'Banshkhali', 'Boalkhali', 'Chandanaish', 'Fatikchhari', 'Hathazari', 'Lohagara', 'Mirsharai', 'Patiya', 'Rangunia', 'Raozan', 'Sandwip', 'Satkania', 'Sitakunda'],
            'Cumilla' => ['Barura', 'Brahmanpara', 'Burichang', 'Chandina', 'Chauddagram', 'Daudkandi', 'Debidwar', 'Homna', 'Laksam', 'Monohorgonj', 'Muradnagar', 'Nangalkot', 'Titas', 'Meghna'],
            'Rajshahi' => ['Bagha', 'Bagmara', 'Charghat', 'Durgapur', 'Godagari', 'Mohanpur', 'Paba', 'Puthia', 'Tanore'],
            'Sylhet' => ['Balaganj', 'Beanibazar', 'Bishwanath', 'Companiganj', 'Fenchuganj', 'Golapganj', 'Gowainghat', 'Jaintiapur', 'Kanaighat', 'Sylhet Sadar', 'Zakiganj', 'South Surma'],
        ];

        foreach ($upazilas as $districtName => $names) {
            $district = District::where('name', $districtName)->first();
            if (! $district) {
                continue;
            }

            foreach ($names as $name) {
                $district->upazilas()->firstOrCreate(['name' => $name]);
            }
        }

        if (! app()->environment('local')) {
            return;
        }

        $accounts = [
            ['name' => 'Local Administrator', 'email' => 'admin@example.test', 'role' => 'admin', 'password' => config('demo.passwords.admin')],
            ['name' => 'Demo Employer', 'email' => 'employer@example.test', 'role' => 'employer', 'password' => config('demo.passwords.employer')],
            ['name' => 'Demo Job Seeker', 'email' => 'employee@example.test', 'role' => 'employee', 'password' => config('demo.passwords.employee')],
        ];

        foreach ($accounts as $account) {
            if (! is_string($account['password']) || $account['password'] === '') {
                throw new \RuntimeException('Set the local demo account passwords in .env before seeding.');
            }
        }

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => Hash::make($account['password']),
                    'is_active' => true,
                ]
            );

            if ($account['role'] === 'employer') {
                $user->company()->firstOrCreate([], [
                    'name' => 'Demo Employer Company',
                    'slug' => 'demo-employer-company',
                ]);
            } elseif ($account['role'] === 'employee') {
                $user->candidateProfile()->firstOrCreate([]);
            }
        }

        $demoEmployer = User::where('email', 'employer@example.test')->firstOrFail();
        $company = $demoEmployer->company()->firstOrCreate([], [
            'name' => 'Demo Employer Company',
            'slug' => 'demo-employer-company',
        ]);
        $company->update([
            'industry_id' => Industry::where('slug', 'it-and-telecom')->value('id'),
            'is_verified' => true,
        ]);

        $dhaka = Division::where('name', 'Dhaka')->firstOrFail();
        $dhakaDistrict = $dhaka->districts()->where('name', 'Dhaka')->firstOrFail();
        $examples = [
            [
                'slug' => 'demo-laravel-software-engineer',
                'title' => 'Laravel Software Engineer',
                'category' => 'IT / Software',
                'industry' => 'IT and Telecom',
                'position' => 'Software Engineer',
                'level' => 'Mid level',
                'workplace' => 'hybrid',
                'min_salary' => 65000,
                'max_salary' => 95000,
                'summary' => 'Help build reliable digital products for teams and customers across Bangladesh.',
                'responsibilities' => "Develop and maintain Laravel applications.\nCollaborate with product and design colleagues.\nWrite tested, maintainable code.",
                'requirements' => "Experience building web applications with PHP.\nWorking knowledge of Laravel and relational databases.\nClear communication and problem-solving skills.",
                'skills' => ['PHP', 'Laravel', 'JavaScript'],
            ],
            [
                'slug' => 'demo-marketing-executive',
                'title' => 'Marketing Executive',
                'category' => 'Marketing',
                'industry' => 'FMCG',
                'position' => 'Marketing Executive',
                'level' => 'Entry level',
                'workplace' => 'office',
                'min_salary' => 35000,
                'max_salary' => 50000,
                'summary' => 'Support thoughtful campaigns and help grow a locally focused consumer brand.',
                'responsibilities' => "Coordinate campaign activities.\nPrepare clear performance updates.\nWork with sales and creative teams.",
                'requirements' => "Strong written and verbal communication.\nComfort with spreadsheets and presentations.\nInterest in customer-focused marketing.",
                'skills' => ['Communication', 'Microsoft Excel'],
            ],
            [
                'slug' => 'demo-staff-accountant',
                'title' => 'Staff Accountant',
                'category' => 'Accounting / Finance',
                'industry' => 'Banking and Finance',
                'position' => 'Accountant',
                'level' => 'Mid level',
                'workplace' => 'office',
                'min_salary' => 45000,
                'max_salary' => 65000,
                'summary' => 'Support accurate financial reporting and day-to-day accounting operations.',
                'responsibilities' => "Maintain financial records and reconciliations.\nAssist with monthly reporting.\nCoordinate supporting documentation.",
                'requirements' => "Accounting or finance education.\nStrong attention to detail.\nWorking knowledge of Microsoft Excel.",
                'skills' => ['Accounting', 'Microsoft Excel'],
            ],
        ];

        foreach ($examples as $example) {
            $job = Job::firstOrCreate(
                ['slug' => $example['slug']],
                [
                    'company_id' => $company->id,
                    'position_id' => JobPosition::where('name', $example['position'])->value('id'),
                    'category_id' => JobCategory::where('name', $example['category'])->value('id'),
                    'industry_id' => Industry::where('name', $example['industry'])->value('id'),
                    'job_level_id' => JobLevel::where('name', $example['level'])->value('id'),
                    'employment_type_id' => EmploymentType::where('name', 'Full time')->value('id'),
                    'division_id' => $dhaka->id,
                    'district_id' => $dhakaDistrict->id,
                    'title' => $example['title'],
                    'workplace_type' => $example['workplace'],
                    'vacancies' => 1,
                    'min_experience' => 1,
                    'max_experience' => 5,
                    'min_salary' => $example['min_salary'],
                    'max_salary' => $example['max_salary'],
                    'salary_period' => 'monthly',
                    'summary' => $example['summary'],
                    'responsibilities' => $example['responsibilities'],
                    'requirements' => $example['requirements'],
                    'application_deadline' => now()->addDays(30)->toDateString(),
                    'status' => 'active',
                    'published_at' => now(),
                ]
            );
            $job->skills()->sync(Skill::whereIn('name', $example['skills'])->pluck('id'));
        }
    }
}
