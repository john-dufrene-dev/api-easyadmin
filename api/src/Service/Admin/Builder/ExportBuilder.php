<?php

namespace App\Service\Admin\Builder;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ExportBuilder
{
    // @Todo : add json and xml export format
    public const CSV_FORMAT = 'csv';

    /**
     * format
     *
     * @param  mixed $format
     * @return string
     */
    public function format($format): string
    {
        return $format;
    }

    /**
     * exportCsv
     *
     * @param  mixed $data
     * @param  mixed $filename
     * @return void
     */
    public function exportCsv($data, $filename)
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $response = new Response($serializer->encode($data, CsvEncoder::FORMAT));
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
        return $response;
    }

    /**
     * importCsv
     *
     * @param  mixed $filename
     * @param  mixed $options
     * @return void
     */
    public function importCsv($filename, $options = [])
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        return $serializer->decode(file_get_contents($filename), CsvEncoder::FORMAT, $options);
    }
}
