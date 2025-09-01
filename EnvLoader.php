<?php

class EnvLoader {
    private $path;
    private $env = [];

    public function __construct($path = ".env") {
        $this->path = $path;
        $this->setEnv();
    }

    private function setEnv() {
        if (!file_exists($this->path)) {
            throw new Exception("Env файл не найден по следующему пути: $this->path");
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line)  {
            if (strpos($line, "=") !== false) {
                list($name, $value) = explode("=", $line, 2);
                $name = trim($name);
                $value = trim($value);

            }

            $this->env[$name] = $value;
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }


    }
    public function getEnv($name, $default = null) {
        return $this->env[$name] ?: $default;
    }
}