<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetService
{
    protected $service;
    protected $spreadsheetId = "1MCDA9I-jeJtzITvvNcbE4Aj_BK8wAs9HCFr5s5s_Z_U"; 

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(Sheets::SPREADSHEETS);

        $this->service = new Sheets($client);
    }

    public function addRow($folderName, $userId)
    {
        $range = 'Sheet1!A:C';
        $values = [
            [$folderName, $userId, now()->toDateTimeString()],
        ];

        $body = new Sheets\ValueRange(['values' => $values]);

        return $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            ['valueInputOption' => 'RAW']
        );
    }
}
