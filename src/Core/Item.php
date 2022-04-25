<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\Core;

/**
 * Item
 * 
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class Item
{
    /**
     * @var mixed|null Variable to be dumped
     */
    private $var;

    /**
     * @var int Caller type
     */
    private $caller;

    /**
     * @var string|null Label for element
     */
    private $label;

    /**
     * @var string|null Filename given from backtrace
     */
    private $file;

    /**
     * @var int|null Line given from backtrace
     */
    private $line;

    /**
     * @var string|null Class name given from backtrace
     */
    private $class;

    /**
     * @var array|null Backtrace
     */
    private $trace;

    /**
     * @var string|null Function name given from backtrace
     */
    private $function;

    /**
     * @return mixed
     */
    public function getVar()
    {
        return $this->var;
    }

    /**
     * @param mixed $var
     *
     * @return self
     */
    public function setVar($var): self
    {
        $this->var = $var;

        return $this;
    }

    /**
     * @return int
     */
    public function getCaller(): ?int
    {
        return $this->caller;
    }

    /**
     * @param int $caller
     *
     * @return self
     */
    public function setCaller(int $caller): self
    {
        $this->caller = $caller;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     *
     * @return self
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string|null $file
     *
     * @return self
     */
    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLine(): ?int
    {
        return $this->line;
    }

    /**
     * @param int|null $line
     *
     * @return self
     */
    public function setLine(?int $line): self
    {
        $this->line = $line;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param string|null $class
     *
     * @return self
     */
    public function setClass(?string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTrace(): ?array
    {
        return $this->trace;
    }

    /**
     * @param array|null $trace
     *
     * @return self
     */
    public function setTrace(?array $trace): self
    {
        $this->trace = $trace;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFunction(): ?string
    {
        return $this->function;
    }

    /**
     * @param string|null $function
     *
     * @return self
     */
    public function setFunction(?string $function): self
    {
        $this->function = $function;

        return $this;
    }
}