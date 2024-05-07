<?php

namespace App\Http\Controllers;

use App\Imports\RecordSheetImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExelController extends Controller
{
    public function loadFile(Request $request): JsonResponse
    {
        info('Request:');
        info($request);

        info('File:');
        info($request->file('excel_file'));

        info('Page:');
        info($request->input('pageValue'));

        $page = $request->input('pageValue') ?? 0;

        if ($request->hasFile('excel_file')) {
            info('===== START LOG =====');

            $uploadedFile = $request->file('excel_file');
            $pathName = $uploadedFile->getPathname(); // Получаем путь к временному файлу
            $fileName = $uploadedFile->getClientOriginalName(); // Получаем оригинальное название файла

            info('PathName: ' . $pathName);
            info('FileName: ' . $fileName);

            ini_set('max_execution_time', 180);
            ini_set('memory_limit', '-1');

            Excel::import(new RecordSheetImport($page), $pathName);

            info('===== END LOG =====');
        }

        $response = [
            'success' => 'Ok',
            'message' => 'I\'m working!',
        ];

        return response()->json($response);
    }
}
