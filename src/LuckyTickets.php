<?php

declare(strict_types=1);

namespace Ticket;

use InvalidArgumentException;

class LuckyTickets
{
    const MAX_ROOT = 9;
    private int $size;
    private int $upperLimit;

    /**
     * @param int $size Count of digits
     * @throws InvalidArgumentException If size is odd, non positive or greater than 8
    */
    public function __construct(int $size)
    {
        if ($size % 2 == 1 || $size < 1 || $size > 8) {
            throw new InvalidArgumentException("The value of the 'size' variable should be even,
                                          positive and less than 9");
        }

        $this->size = $size;
        $this->upperLimit = 10 ** ($this->size / 2) - 1;
    }

    /**
     * Returns generator representing lucky numbers in range
     *
     * @param int $min
     * @param int $max
     * @throws InvalidArgumentException If min (or max) is negative or has wrong size or if max < min
    */
    public function range(int $min, int $max)
    {
        $this->bordersVerification($min, $max);

        $max = $this->getNearestBottom($max);

        $current = $min;
        do {
            $current = $this->getNearestAbove($current);

            yield $this->format($current);

            $current++;
        } while ($current < $max);
    }

    private function bordersVerification(int $min, int $max): void
    {
        $this->argumentVerification($min, 'Min');
        $this->argumentVerification($max, 'Max');

        if ($min > $max) {
            throw new InvalidArgumentException("Max value must be greater than Min value");
        }
    }

    private function argumentVerification(int $argument, string $name): void
    {
        if ($argument < 0) {
            throw new InvalidArgumentException("$name must be non negative");
        }

        if ($this->digitsCount($argument) > $this->size) {
            throw new InvalidArgumentException("$name value cannot be longer than $this->size digits");
        }
    }

    /**
     * Calculates number of digits
     */
    private function digitsCount(int $number): int
    {
        return (int)(log10(abs($number)) + 1);
    }

    private function getNearestBottom(int $number): int
    {
        list($left, $right) = $this->split($number);

        $leftRoot = $this->digitalRoot($left);
        $rightRoot = $this->digitalRoot($right);

        if ($leftRoot == $rightRoot) {
            return $number;
        }

        //When number is like 000 XYX
        if ($number <= $this->upperLimit + 1) {
            return 0;
        }

        return $number +
            $leftRoot - $rightRoot +
            ($leftRoot > $rightRoot ? -static::MAX_ROOT - ($right < static::MAX_ROOT ? 2 : 0) : 0);
    }

    /**
     * Calculates digital root of number
     */
    private function digitalRoot(int $number): int
    {
        return 1 + ($number - 1) % static::MAX_ROOT;
    }

    private function split(int $number): array
    {
        $divider = $this->upperLimit + 1;
        $part2 = $number % $divider;
        $part1 = ($number - $part2) / $divider;
        return [$part1, $part2];
    }

    private function getNearestAbove(int $number): int
    {
        list($left, $right) = $this->split($number);

        $leftRoot = $this->digitalRoot($left);
        $rightRoot = $this->digitalRoot($right);

        if ($leftRoot == $rightRoot) {
            return $number;
        }
        
        //When number is 000 XYZ
        if ($leftRoot === 0) {
            return $number + ($this->upperLimit + 1) * $rightRoot;
        }

        return $number +
            $leftRoot - $rightRoot +
            ($leftRoot < $rightRoot ? static::MAX_ROOT + ($this->upperLimit - $right < static::MAX_ROOT ? 2 : 0) : 0);
    }

    private function format(int $number): string
    {
        return str_pad((string)$number, $this->size, "0", STR_PAD_LEFT);
    }
  
    /**
     * Returns the nearest lucky number from above (if argument is not lucky number)
     * @param int $number
     * @throws InvalidArgumentException If number is negative or has wrong size
     *
    */
    public function getTopNumber(int $number): string
    {
        $this->argumentVerification($number, 'Number');

        $result = $this->getNearestAbove($number);

        return $this->format($result);
    }

    /**
     * Returns the nearest lucky number from below (if argument is not lucky number)
     * @param int $number
     * @throws InvalidArgumentException If number is negative or has wrong size
     *
    */
    public function getBottomNumber(int $number): string
    {
        $this->argumentVerification($number, 'Number');

        $result = $this->getNearestBottom($number);

        return $this->format($result);
    }

    /**
     * Returns count of lucky numbers in range
     *
     * @param int $min
     * @param int $max
     * @throws InvalidArgumentException If min (or max) is negative or has wrong size or if max < min
     *
    */
    public function count(int $min, int $max): int
    {
        $this->bordersVerification($min, $max);

        $isZeroBased = 0;

        if ($min == 0) {
            $isZeroBased = 1;
            $lower = $this->getNearestAbove(1);
        } else {
            $lower = $this->getNearestAbove($min);
        }

        $upper = $this->getNearestBottom($max);

        if ($lower > $upper) {
            return 0;
        }
        
        list($leftLower, $rightLower) = $this->split($lower);
        list($leftUpper, $rightUpper) = $this->split($upper);
       
        $count = ($leftUpper - $leftLower - 1) *  $this->upperLimit / static::MAX_ROOT;
        $count += ($this->upperLimit - $rightLower + $this->digitalRoot($rightLower)) / static::MAX_ROOT;
        $count += ($rightUpper - $this->digitalRoot($rightUpper)) / static::MAX_ROOT + 1;

        return (int)$count + $isZeroBased;
    }
}
