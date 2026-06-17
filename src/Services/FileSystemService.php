<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Services;

use AndyDefer\PhpServices\Contracts\FileSystemInterface;
use AndyDefer\PhpServices\Enums\PermissionMode;

/**
 * Native PHP implementation of the file system interface.
 *
 * This implementation uses only PHP built-in functions and has no external
 * dependencies, making it suitable for any PHP project regardless of the
 * framework being used.
 *
 * @author Andy Defer
 */
class FileSystemService implements FileSystemInterface
{
    /**
     * {@inheritDoc}
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $path): string
    {
        if (! $this->exists($path)) {
            throw new \RuntimeException(sprintf('File does not exist at path: %s', $path));
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException(sprintf('Cannot read file at path: %s', $path));
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function put(string $path, string $content): int|false
    {
        $this->ensureDirectoryExists(dirname($path));

        // ✅ Vérifier si le répertoire est accessible en écriture
        $directory = dirname($path);
        if (! $this->isWritable($directory)) {
            throw new \RuntimeException(sprintf('Cannot write file: %s - directory is not writable', $path));
        }

        return file_put_contents($path, $content);
    }

    /**
     * {@inheritDoc}
     */
    public function append(string $path, string $content): int|false
    {
        $this->ensureDirectoryExists(dirname($path));

        // ✅ Vérifier si le répertoire est accessible en écriture
        $directory = dirname($path);
        if (! $this->isWritable($directory)) {
            throw new \RuntimeException(sprintf('Cannot append to file: %s - directory is not writable', $path));
        }

        return file_put_contents($path, $content, FILE_APPEND | LOCK_EX);
    }

    /**
     * {@inheritDoc}
     */
    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * {@inheritDoc}
     */
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * {@inheritDoc}
     */
    public function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    /**
     * {@inheritDoc}
     */
    public function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    /**
     * {@inheritDoc}
     */
    public function makeDirectory(string $path, PermissionMode $mode = PermissionMode::DIRECTORY, bool $recursive = true): bool
    {
        if ($this->isDirectory($path)) {
            return true;
        }

        // ✅ Vérifier si le parent est accessible en écriture
        if (! $recursive) {
            $parent = dirname($path);
            if (! $this->isDirectory($parent) || ! $this->isWritable($parent)) {
                return false;
            }
        }

        return mkdir($path, $mode->value(), $recursive);
    }

    /**
     * {@inheritDoc}
     */
    public function ensureDirectoryExists(string $path): void
    {
        if ($this->isDirectory($path)) {
            return;
        }

        // ✅ Vérifier si le parent existe et est accessible en écriture
        $parent = dirname($path);

        // Si le parent n'existe pas, on vérifie récursivement
        if (! $this->isDirectory($parent)) {
            // Vérifier si on peut créer le parent
            $grandParent = dirname($parent);
            if (! $this->isDirectory($grandParent) || ! $this->isWritable($grandParent)) {
                throw new \RuntimeException(sprintf(
                    'Cannot create directory: %s - parent directory does not exist or is not writable',
                    $path
                ));
            }
        }

        // Vérifier si le parent est accessible en écriture
        if (! $this->isWritable($parent)) {
            throw new \RuntimeException(sprintf(
                'Cannot create directory: %s - parent directory is not writable',
                $path
            ));
        }

        if (! $this->makeDirectory($path, PermissionMode::DIRECTORY, true)) {
            throw new \RuntimeException(sprintf('Cannot create directory: %s', $path));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function copy(string $source, string $destination): bool
    {
        $this->ensureDirectoryExists(dirname($destination));

        // ✅ Vérifier si le répertoire de destination est accessible en écriture
        $directory = dirname($destination);
        if (! $this->isWritable($directory)) {
            throw new \RuntimeException(sprintf('Cannot copy file: %s - destination directory is not writable', $destination));
        }

        return copy($source, $destination);
    }

    /**
     * {@inheritDoc}
     */
    public function move(string $source, string $destination): bool
    {
        $this->ensureDirectoryExists(dirname($destination));

        // ✅ Vérifier si le répertoire de destination est accessible en écriture
        $directory = dirname($destination);
        if (! $this->isWritable($directory)) {
            throw new \RuntimeException(sprintf('Cannot move file: %s - destination directory is not writable', $destination));
        }

        return rename($source, $destination);
    }

    /**
     * {@inheritDoc}
     */
    public function glob(string $pattern, int $flags = 0): array
    {
        $result = glob($pattern, $flags);

        return $result === false ? [] : $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $path): bool
    {
        if (! $this->exists($path)) {
            return true;
        }

        if ($this->isDirectory($path)) {
            return $this->deleteDirectory($path);
        }

        return unlink($path);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDirectory(string $directory): bool
    {
        if (! $this->isDirectory($directory)) {
            return false;
        }

        $files = $this->glob($directory.'/*');

        foreach ($files as $file) {
            if ($this->isDirectory($file)) {
                $this->deleteDirectory($file);
            } else {
                unlink($file);
            }
        }

        return rmdir($directory);
    }

    /**
     * {@inheritDoc}
     */
    public function size(string $path): int
    {
        if (! $this->exists($path)) {
            throw new \RuntimeException(sprintf('File does not exist: %s', $path));
        }

        $size = filesize($path);

        if ($size === false) {
            throw new \RuntimeException(sprintf('Cannot get file size: %s', $path));
        }

        return $size;
    }

    /**
     * {@inheritDoc}
     */
    public function lastModified(string $path): int
    {
        if (! $this->exists($path)) {
            throw new \RuntimeException(sprintf('File does not exist: %s', $path));
        }

        $mtime = filemtime($path);

        if ($mtime === false) {
            throw new \RuntimeException(sprintf('Cannot get last modified time: %s', $path));
        }

        return $mtime;
    }

    /**
     * {@inheritDoc}
     */
    public function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * {@inheritDoc}
     */
    public function basename(string $path): string
    {
        return basename($path);
    }

    /**
     * {@inheritDoc}
     */
    public function dirname(string $path): string
    {
        return dirname($path);
    }
}
