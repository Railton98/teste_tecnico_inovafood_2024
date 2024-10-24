<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Library\Http\JsonResponse;
use App\Library\Http\Request;
use App\Services\ProcessDocsService;
use Exception;
use InvalidArgumentException;

class ProcessDocsController
{
    public function process(): JsonResponse
    {
        try {
            $document = Request::file('document');
            $this->validateFile($document);

            $insertedQuestions = (new ProcessDocsService($document['tmp_name']))
                ->process();

            return new JsonResponse([
                'success' => true,
                'message' => "$insertedQuestions questions inserted",
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function validateFile($document): void
    {
        if (!$document || !$document['tmp_name']) {
            throw new InvalidArgumentException('Document is required. Please upload a `.docx` file.');
        }
        if (mime_content_type($document['tmp_name']) !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            throw new InvalidArgumentException('Invalid file type. Please upload a `.docx` file.');
        }
    }
}
