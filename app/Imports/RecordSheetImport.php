<?php

namespace App\Imports;

use App\Models\ClassType;
use App\Models\ClassTypeRecord;
use App\Models\Disciplines;
use App\Models\Group;

use App\Models\Record;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class RecordSheetImport implements ToCollection
{
    private $currentPage = 1;
    private $pageToRead;
    public function __construct($page)
    {
        $this->pageToRead = (int) $page;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection): void
    {
        info('      START IMPORT LOG      ');

        $classType = [];

        if (empty($this->pageToRead) || $this->currentPage == $this->pageToRead) {
            foreach($collection[3] as $key => $word) {
                info('Key: ' . $key . '. Word: ' . $word);

                switch ($key) {
                    case 8:
                    case 9:
                        break;
                    default:
                        if (is_null($word)) break;
                        $value = ClassType::firstOrCreate(['class_type_name' => $word]);

                        $classType[] = $value->id;
                }
            }

            foreach($collection[4] as $key => $word) {
                if (is_null($word) || ($key != 8 && $key != 9)) continue;
                $value = ClassType::firstOrCreate(['class_type_name' => $word]);

                $classType[] = $value->id;
            }

            foreach($collection as $keyLine=> $line) {
                if (!is_numeric($line[0]) || !is_numeric($line[1])) continue;

                $classTypeRecords = [];

                $data = [
                    'date' => gmdate("Y-m-d H:i:s"),
                    'course' => null,
                    'group_id' => null,
                    'discipline_id' => null,
                ];

                foreach ($line as $key=>$word) {
                    if ($word === null || $word === '') continue;

                    if ($key == 0) $data['date'] = Date::excelToDateTimeObject($word);
                    else if ($key == 1) $data['course'] = (int) $word;
                    else if ($key == 2) $data['group_id'] = Group::firstOrCreate(['group_name' => $word])->id;
                    else if ($key == 3) $data['discipline_id'] = Disciplines::firstOrCreate(['discipline_name' => $word])->id;
                    else if ($key > 3 && $key < 22) $classTypeRecords[] = [ 'class_type_id' =>  $classType[$key - 4], 'hours_spend' => (float) $word ];
                }

                $record = Record::firstOrCreate($data);

                foreach ($classTypeRecords as $item) {
                    $data = [
                        'record_id' => $record->id,
                        ...$item,
                    ];

                    ClassTypeRecord::create($data);
                }
            }
        }

        $this->currentPage++;
        info('      END IMPORT LOG      ');
    }
}
