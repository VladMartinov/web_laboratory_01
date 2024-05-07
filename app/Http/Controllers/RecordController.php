<?php

namespace App\Http\Controllers;

use App\Models\ClassType;
use App\Models\ClassTypeRecord;
use App\Models\Disciplines;
use App\Models\Group;
use App\Models\Record;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecordController extends Controller
{
    /*
     * Create new record
     * */
    public function createRecord(Request $request): JsonResponse
    {
        $group = null;
        $discipline = null;

        $record = null;
        $classTypeRecords = [];

        try {
            $recordToDb = $request->recordData;

            if ($request->groupData) {
                $group = Group::firstOrCreate($request->groupData);
                $recordToDb['group_id'] = $group->id;
            }
            if ($request->disciplineData) {
                $discipline = Disciplines::firstOrCreate($request->disciplineData);
                $recordToDb['discipline_id'] = $discipline->id;
            }

            $record = Record::firstOrCreate($recordToDb);

            $classTypeRecordToDb = [
                "record_id" => $record->id,
                "data" => $request->classTypeRecordData,
            ];

            foreach ($classTypeRecordToDb['data'] as $item) {
                $data = [
                    'class_type_id' => $item['class_type_id'],
                    'record_id' => $classTypeRecordToDb['record_id'],
                    'hours_spend' => $item['hours_spend'],
                ];

                $classTypeRecords[] = ClassTypeRecord::firstOrCreate($data);
            }

            $success = true;
            $message = 'Record successfully created';
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        // response
        $response = [
            'group' => $group,
            'discipline' => $discipline,
            'record' => $record,
            'classTypeRecords' => $classTypeRecords,

            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response);
    }

    /*
     * Return all records
     * */
    public function getRecords(): array
    {
        $classTypeRecords = ClassTypeRecord::get();
        $records = Record::get();
        $classTypes = ClassType::get();
        $groups = Group::get();
        $disciplines = Disciplines::get();

        return [
            'classTypeRecords' => $classTypeRecords,
            'classTypes' => $classTypes,
            'records' => $records,
            'disciplines' => $disciplines,
            'groups' => $groups,
        ];
    }

    /*
     * @return Record
     * */
    public function updateRecord(Request $request): JsonResponse
    {
        $group = null;
        $discipline = null;

        $record = null;
        $classTypeRecords = null;

        try {
            $recordToDb = $request->recordData;

            if ($request->groupData) {
                $group = Group::firstOrCreate($request->groupData);
                $recordToDb['group_id'] = $group->id;
            }
            if ($request->disciplineData) {
                $discipline = Disciplines::firstOrCreate($request->disciplineData);
                $recordToDb['discipline_id'] = $discipline->id;
            }

            $record = Record::find($recordToDb['id']);
            unset($recordToDb['id']);

            $record->update($recordToDb);

            $classTypeRecordData = $request->classTypeRecordData;
            $classTypeRecords = [];
            foreach ($classTypeRecordData as $item) {
                $data = [
                    'class_type_id' => $item['class_type_id'],
                    'record_id' => $record->id,
                    'hours_spend' => $item['hours_spend'],
                ];
                $classTypeRecord = ClassTypeRecord::updateOrCreate([
                    'class_type_id' => $data['class_type_id'],
                    'record_id' => $data['record_id'],
                ], $data);
                $classTypeRecords[] = $classTypeRecord;
            }

            $success = true;
            $message = 'Record successfully updated';
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        // response
        $response = [
            'group' => $group,
            'discipline' => $discipline,
            'record' => $record,
            'classTypeRecords' => $classTypeRecords,

            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response);
    }

    public function deleteRecord($recordId): JsonResponse
    {
        try {
            $record = Record::find($recordId);
            info("Record object:");
            info(print_r($record, true));

            $classTypeRecords = ClassTypeRecord::where('record_id', $record->id)->get();
            info("ClassTypeRecord object:");
            info(print_r($classTypeRecords, true));

            foreach ($classTypeRecords as $classTypeRecord) {
                $classTypeRecord->delete();
            }
            $record->delete();

            $success = true;
            $message = 'Record successfully deleted';
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        // response
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        return response()->json($response);
    }
}
