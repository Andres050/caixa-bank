<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtractTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extract:translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $translationKeys = [];

        $files = File::allFiles(resource_path('views'));

        foreach ($files as $file) {
            $content = $file->getContents();

            preg_match_all("/__\(\s*['\"](.*?)['\"]\s*\)/", $content, $matches);

            foreach ($matches[1] as $translation) {
                $translationKeys[] = $translation;
            }
        }

        $jsonPath = resource_path('lang/en.json');
        $existingTranslations = File::exists($jsonPath)
            ? json_decode(File::get($jsonPath), true)
            : [];

        foreach ($translationKeys as $key) {
            if (!array_key_exists($key, $existingTranslations)) {
                $existingTranslations[$key] = ''; // o $key como valor predeterminado
            }
        }

        ksort($existingTranslations);

        File::put($jsonPath, json_encode($existingTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Archivo en.json actualizado con " . count($translationKeys) . " claves.");
    }
}
