<?php

namespace App\Console\Commands;

use App\Models\ClassType;
use App\Models\ClassTypeRecord;
use App\Models\Disciplines;
use App\Models\Group;
use App\Models\Record;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FillTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:fill {count?} {table?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill the database with test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->argument('count') ?? 10;
        $table = $this->argument('table') ?? null;

        $faker = Faker::create();

        if ($count <= 0) {
            $this->info('Invalid count of items');
            return;
        }

        if ($table) {
            switch ($table) {
                case 'users':
                    // Fill users table
                    try {
                        $this->fillUsersTable($count, $faker);
                        $this->info('The table "Users" has been successfully filled');
                    } catch (\Exception $ex) {
                        $this->info('An error occurred while filling the "Users" table. The details of the error are shown below');
                        $this->info($ex);
                    }
                    break;
                case 'records':
                    // Fill records table
                    try {
                        $this->fillRecordsTable($count, $faker);
                        $this->info('The table "Records" has been successfully filled');
                    } catch (\Exception $ex) {
                        $this->info('An error occurred while filling the "Records" table. The details of the error are shown below');
                        $this->info($ex);
                    }
                    break;
                default:
                    $this->info('Invalid table name');
                    break;
            }
        } else {
            // Fill users and records tables
            try {
                $this->fillUsersTable($count, $faker);
                $this->fillRecordsTable($count, $faker);
                $this->info('The tables "Users" and "Records" has been successfully filled');
            } catch (\Exception $ex) {
                $this->info('An error occurred while filling the "Users" and "Records" tables. The details of the error are shown below');
                $this->info($ex);
            }
        }
    }

    private function fillUsersTable($count, $faker): void
    {
        for ($i = 0; $i < $count; $i++) {
            $user = new User();
            $user->name = $faker->name;
            $user->email = $faker->email;
            $user->password = Hash::make($faker->password);
            $user->save();
        }
    }

    private function fillRecordsTable($count, $faker): void
    {
        $groups = [
            'ИВТ',
            'ИВТ,зао',
            'ИВТ,зао,уск',
            'ИВТ-1,2',
            'ИВТ-3,4',
            'ИВТ-5,6',
        ];
        $disciplines = [
            'Операц. системы',
            'Архитектур. ЭВМ',
            'Компьют. графика',
            'Программир. НУ',
            'Курсовая работа',
            'ЭВМ и ПУ',
            'Руководство ВКР',
        ];
        $classTypes = [
            'Лекции',
            'Практические (сем.) занятия',
            'Лабораторные занятия',
            'Модульный контроль',
            'Зачеты',
            'Экзамены',
            'Курсовые работы',
            'ВКР бакалавов',
            'ВКР специалистов',
            'ВКР магистров',
            'Руководство практикой',
            'Госэкзамены',
            'Рецензирование ВКР',
            'Защита ВКР',
            'Руководство аспирантами',
            'Другие виды учеб. нагрузки',
            'Семестр',
            'Экзамен',
        ];

        for ($i = 0; $i < $count; $i++) {
            $recordToDb = [
                'date' => $faker->date('Y-m-d', 'now'),
                'course' => $faker->numberBetween(1, 6),
            ];

            $group = Group::firstOrCreate([
                'group_name' => $faker->randomElement($groups),
            ]);
            $recordToDb['group_id'] = $group->id;

            $discipline = Disciplines::firstOrCreate([
                'discipline_name' => $faker->randomElement($disciplines),
            ]);
            $recordToDb['discipline_id'] = $discipline->id;

            $record = Record::firstOrCreate($recordToDb);

            $classType = ClassType::firstOrCreate([
                'class_type_name' => $faker->randomElement($classTypes),
            ]);

            ClassTypeRecord::firstOrCreate([
                'class_type_id' => $classType->id,
                'record_id' => $record->id,
                'hours_spend' => $faker->numberBetween(1, 250),
            ]);
        }
    }
}
