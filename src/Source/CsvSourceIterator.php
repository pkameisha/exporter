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

namespace Sonata\Exporter\Source;

/**
 * Read data from a csv file.
 *
 * @author Vincent Touzet <vincent.touzet@gmail.com>
 */
class CsvSourceIterator implements SourceIteratorInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var resource|null
     */
    protected $file;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $enclosure;

    /**
     * @var string
     */
    protected $escape;

    /**
     * @var bool
     */
    protected $hasHeaders;

    /**
     * @var array
     */
    protected $lines = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var array
     */
    protected $currentLine = [];

    public function __construct(
        string $filename,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\',
        bool $hasHeaders = true
    ) {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->hasHeaders = $hasHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->currentLine;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        $line = fgetcsv($this->file, 0, $this->delimiter, $this->enclosure, $this->escape);
        $this->currentLine = $line;
        ++$this->position;
        if ($this->hasHeaders && \is_array($line)) {
            $data = [];
            foreach ($line as $key => $value) {
                $data[$this->columns[$key]] = $value;
            }
            $this->currentLine = $data;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->file = fopen($this->filename, 'rb');
        $this->position = 0;
        $line = fgetcsv($this->file, 0, $this->delimiter, $this->enclosure, $this->escape);
        if ($this->hasHeaders) {
            $this->columns = $line;
            $line = fgetcsv($this->file, 0, $this->delimiter, $this->enclosure, $this->escape);
        }
        $this->currentLine = $line;
        if ($this->hasHeaders && \is_array($line)) {
            $data = [];
            foreach ($line as $key => $value) {
                $data[$this->columns[$key]] = $value;
            }
            $this->currentLine = $data;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        if (!\is_array($this->currentLine)) {
            if (\is_resource($this->file)) {
                fclose($this->file);
            }

            return false;
        }

        return true;
    }
}
