<?php

declare(strict_types=1);

namespace App\Services;

use App\Database\Database;
use App\Database\Transaction;
use PDOException;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\Header;
use PhpOffice\PhpWord\Element\Row;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ProcessDocsService
{
    private PhpWord $phpWord;

    public function __construct(private string $documentPath)
    {
        $this->phpWord = IOFactory::load($documentPath);
    }

    public function process(): int
    {
        $alternatives = [];
        $checklistTitle = '';
        $text = '';
        foreach ($this->phpWord->getSections() as $section) {
            foreach ($section->getHeaders() as $header) {
                if ($header->countElements() > 1) {
                    $checklistTitle = $this->getChecklistTitle($header); // save `checklists`
                }
            }

            foreach ($section->getElements() as $element) {
                if ($element instanceof Table) {
                    $rows = $element->getRows();
                    if ($element->countColumns() >= 4) {
                        $alternatives = $this->getAlternatives(count($rows[0]->getCells()) >= 4 ? $rows[0] : $rows[1]);
                    }
                }
                $text .= $this->getDocText($element);
            }
        }

        $questions = [];
        foreach (explode("\n", $this->removeEmptyLines($text)) as $line) {
            $question = preg_replace('/\s+/', ' ', $line);
            if (
                !str_starts_with($question, ':') &&
                (str_contains($question, '?') ||
                    str_contains($question, ':_') ||
                    str_contains($question, ': _') ||
                    str_contains($question, ': ('))
            ) {
                $type = 0; //text
                $title = trim(str_replace(['_', '/'], '', $question));

                if (!str_ends_with($title, ':') && !empty($alternatives)) {
                    $type = 1; //multiple_choice
                }

                $questionData = [
                    'title' => $title,
                    'type' => $type,
                    'alternatives' => $type === 1 ? $alternatives : null,
                ];
                if ($title === 'DEVOLUÇÃO:') {
                    $questionData['type'] = 1; //multiple_choice
                    $questionData['alternatives'] = ['Sim', 'Não'];
                }
                $questions[] = $questionData;
            }
        }

        try {
            Transaction::open();
            $connection = Transaction::getConnection();

            $dbChecklist = new Database($connection, 'checklists');
            $checklistId = $dbChecklist->insert(['title' => $checklistTitle]);

            $dbQuestion = new Database($connection, 'questions');
            $countQuestions = 0;
            foreach ($questions as $question) {
                $questionId = $dbQuestion->insert([
                    'title' => $question['title'],
                    'type' => $question['type'],
                    'id_checklist' => $checklistId,
                ]);

                $dbAlternative = new Database($connection, 'alternatives');
                foreach ($question['alternatives'] ?? [] as $alternative) {
                    $dbAlternative->insert([
                        'title' => $alternative,
                        'id_question' => $questionId,
                    ]);
                }

                $countQuestions++;
            }
            Transaction::close();

            return $countQuestions;
        } catch (PDOException $e) {
            Transaction::rollback();
            throw $e;
        }
    }

    private function removeEmptyLines($string): string
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $string));
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, fn($value) => $value !== '');

        return implode("\n", $lines);
    }

    private function getDocText(AbstractElement|AbstractContainer|Text|Table $element): string
    {
        $result = '';
        if ($element instanceof AbstractContainer) {
            foreach ($element->getElements() as $absElement) {
                $result .= $this->getDocText($absElement);
            }
        } elseif ($element instanceof Text) {
            $text = $element->getText();
            if (trim($text) === 'FORNECEDOR') {
                $result .= "\nFORNECEDOR: EM:_";
            } elseif (trim($text) === 'RESPONSÁVEL') {
                $result .= "\nRESPONSÁVEL:_\nDATA:_";
            } elseif (str_starts_with($text, 'DATA:')) {
                $result .= "\nDATA:_\nHORÁRIO:_";
            } elseif (str_starts_with($text, ':  EM')) {
                $result .= "\nDEVOLUÇÃO:_";
            } elseif (is_numeric($text[0]) || str_contains($text, ':')) {
                $result .= "\n".$text;
            } elseif (str_ends_with($text, '?') || str_ends_with($text, '_')) {
                $result .= $text."\n";
            } else {
                $result .= $text;
            }
        } elseif ($element instanceof Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellElement) {
                        $result .= $this->getDocText($cellElement);
                    }
                }
            }
        }

        return $result;
    }

    private function getAlternatives(Row $row): array
    {
        $alternatives = [];
        $cells = $row->getCells();
        unset($cells[0]);
        foreach ($cells as $cell) {
            foreach ($cell->getElements() as $cellElement) {
                if (($text = $this->getDocText($cellElement)) !== '') {
                    $alternatives[] = $text;
                }
            }
        }

        return $alternatives;
    }

    private function getChecklistTitle(Header $header): string
    {
        $title = '';
        foreach ($header->getElements() as $element) {
            if ($element instanceof Table) {
                foreach ($element->getRows() as $row) {
                    foreach ($row->getCells() as $cell) {
                        foreach ($cell->getElements() as $cellElement) {
                            $title .= $this->getDocText($cellElement);
                            if ($title !== '') {
                                return $title;
                            }
                        }
                    }
                }
            }
        }

        return $title;
    }
}
