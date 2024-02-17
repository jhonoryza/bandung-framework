<?php

namespace Fajar\Bandung\Command;

use Fajar\Bandung\Attribute\Command;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

/**
 * this class is used for internal purpose
 * to serve webserver
 * and install bandung framework
 */
class InternalCommand
{
    /**
     * php bandung serve
     * you can customize host and port from .env APP_URL
     */
    #[Command('serve')]
    public function serve(): void
    {
        $host = getenv('APP_URL') ?? '127.0.0.1:8000';
        $host = str_replace(['http://', 'https://'], '', $host);
        passthru("php -S {$host} -t public/");
    }

    /**
     * php bandung install
     * this function will copy all required files
     * to run bandung framework
     */
    #[Command('install')]
    public function install(): void
    {
        $confirmed = confirm(
            label: 'are you sure want to install bandung frameworks ?',
        );
        if ($confirmed) {
            info('starting install ...');

            $cwd = getcwd();

            $this->createAppDir($cwd);

            $this->copyEnvExampleFile($cwd);

            $this->copyPublicIndexFile($cwd);

            $this->copyBandungFile($cwd);

            $this->initializeGit($cwd);

            info('install success');
        }
    }

    private function initializeGit(string $cwd): void
    {
        if (confirm(
            label: sprintf("Do you want to initialize git repo? %s", $cwd),
            default: true,
        )) {
            if (!is_dir($cwd . '/.git')) {
                passthru('git init');
                passthru('cp vendor/jhonoryza/bandung-framework/.gitignore .');
                passthru('git add .');
                passthru('git commit -m "initial commit"');

                info('git repo initialized');
            }
        }
    }

    private function createAppDir(string $cwd): void
    {
        if (!is_dir($cwd . '/app')) {
            mkdir($cwd . '/app');
            info('app dir created');
        }
        $file = $cwd . '/composer.json';
        $data = json_decode(file_get_contents($file), true);
        $data["autoload"]["psr-4"] = array_merge(['App\\' => 'app/'], $data["autoload"]["psr-4"]);
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        passthru('composer dump-autoload');
        info('updated composer.json');
    }

    private function copyEnvExampleFile(string $cwd): void
    {
        $path = $cwd . '/.env';

        if (file_exists($path)) {

            warning("{$path} already exists");

            if (confirm(
                label: sprintf("Do you want to override %s ?", $path),
                default: false,
            )) {
                copy(__DIR__ . '/../../.env.example', $path);

                info("{$path} overrides");
                return;
            }
            info("{$path} skipped");
            return;
        }


        copy(__DIR__ . '/../../.env.example', $path);

        info("{$path} created");
    }

    private function copyBandungFile(string $cwd): void
    {
        $path = $cwd . '/bandung';

        if (file_exists($path)) {
            warning("{$path} already exists.");
            if (confirm(
                label: sprintf("Do you want to override %s ?", $path),
                default: false,
            )) {

                copy(__DIR__ . '/../../bandung', $path);
                exec("chmod +x {$path}");
                info("{$path} overrides");
                return;
            }
            info("{$path} skipped");
            return;
        }

        copy(__DIR__ . '/../../bandung', $path);
        exec("chmod +x {$path}");

        info("{$path} created");
    }

    private function copyPublicIndexFile(string $cwd): void
    {
        $path = $cwd . '/public/index.php';

        if (file_exists($path)) {
            warning("{$path} already exists.");
            if (confirm(
                label: sprintf("Do you want to override %s ?", $path),
                default: false,
            )) {

                if (!is_dir(dirname($path))) {
                    mkdir(dirname($path), recursive: true);
                }

                copy(__DIR__ . '/../../public/index.php', $path);

                info("{$path} overrides");
                return;
            }
            info("{$path} skipped");
            return;
        }

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), recursive: true);
        }

        copy(__DIR__ . '/../../public/index.php', $path);

        info("{$path} created");
    }
}