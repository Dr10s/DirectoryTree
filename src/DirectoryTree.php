<?php

namespace Dr10s\DirectoryTree;

class DirectoryTree
{
    private string $mainDirectory;
    private int $filesPerDirectory;
    private int $step;

    public function __construct(string $mainDirectory, int $filesPerDirectory, int $step)
    {
        $this->mainDirectory = $mainDirectory;
        $this->filesPerDirectory = $filesPerDirectory;
        $this->step = $step;
    }

    public function createPathForFile(string $fileNameAndExtension): string
    {
        [$filename, $extension] = \explode('.', $fileNameAndExtension);
        $separatedFilename = \str_split($filename, $this->step);
        $dirPath = $this->mainDirectory;

        foreach ($separatedFilename as $fragment) {
            $dirPath = \sprintf('%s/%s', $dirPath, $fragment);

            $this->createDirIfNotExist($dirPath);

            if (\count(\scandir($dirPath)) < $this->filesPerDirectory) {
                return \sprintf('%s/%s', $dirPath, $fileNameAndExtension);
            }
        }

        throw new \RuntimeException(\sprintf('Not valid filename: "%s"', $fileNameAndExtension));
    }

    private function createDirIfNotExist(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }
}
