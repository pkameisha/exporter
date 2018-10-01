<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Exporter\Writer;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class JsonWriter implements TypedWriterInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var resource
     */
    protected $file;

    /**
     * @var int
     */
    protected $position = 0;

    public function __construct(string $filename)
    {
        $this->filename = $filename;

        if (is_file($filename)) {
            throw new \RuntimeException(sprintf('The file %s already exist', $filename));
        }
    }

    final public function getDefaultMimeType(): string
    {
        return 'application/json';
    }

    final public function getFormat(): string
    {
        return 'json';
    }

    public function open(): void
    {
        $this->file = fopen($this->filename, 'wb', false);

        fwrite($this->file, '[');
    }

    public function close(): void
    {
        fwrite($this->file, ']');

        fclose($this->file);
    }

    public function write(array $data): void
    {
        fwrite($this->file, ($this->position > 0 ? ',' : '').json_encode($data));

        ++$this->position;
    }
}
