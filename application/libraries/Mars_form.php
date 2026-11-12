<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mars_form
{
    public function validate($postedAnswers, array $questions)
    {
        if (!is_array($postedAnswers)) {
            $postedAnswers = array();
        }

        $answers = array();
        foreach ($questions as $question) {
            $questionId = (int) $question['id'];
            $answer = isset($postedAnswers[$questionId]) && is_scalar($postedAnswers[$questionId])
                ? trim((string) $postedAnswers[$questionId])
                : '';

            if ($answer === '') {
                return array('success' => FALSE, 'message' => 'Semua pernyataan MARS-5 wajib dijawab.');
            }

            $valid = FALSE;
            foreach ($question['opsi'] as $option) {
                if ((string) $option['nilai'] === $answer) {
                    $valid = TRUE;
                    break;
                }
            }
            if (!$valid) {
                return array('success' => FALSE, 'message' => 'Terdapat jawaban MARS-5 yang tidak valid.');
            }

            $answers[(string) $question['kode']] = $answer;
        }

        return array('success' => TRUE, 'answers' => $answers);
    }
}
