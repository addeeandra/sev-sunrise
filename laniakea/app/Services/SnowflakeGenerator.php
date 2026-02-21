<?php

namespace App\Services;

class SnowflakeGenerator
{
    // Custom epoch: 2020-01-01 00:00:00 UTC in milliseconds
    private const EPOCH = 1577836800000;
    private const NODE_BITS = 10;
    private const SEQUENCE_BITS = 12;
    private const MAX_SEQUENCE = (1 << 12) - 1; // 4095
    private const MAX_NODE_ID = (1 << 10) - 1;  // 1023

    private int $nodeId;
    private int $sequence = 0;
    private int $lastTimestamp = -1;

    public function __construct(int $nodeId = 1)
    {
        if ($nodeId < 0 || $nodeId > self::MAX_NODE_ID) {
            throw new \InvalidArgumentException('Node ID must be between 0 and '.self::MAX_NODE_ID);
        }
        $this->nodeId = $nodeId;
    }

    /**
     * Generate a new Snowflake ID.
     *
     * 64-bit layout:
     *   [0 (sign)] [41-bit timestamp ms since epoch] [10-bit node_id] [12-bit sequence]
     *
     * Returns as string to safely represent 64-bit integers across all environments.
     */
    public function generate(): string
    {
        $timestamp = $this->currentTimestamp();

        if ($timestamp < $this->lastTimestamp) {
            throw new \RuntimeException('Clock moved backwards. Refusing to generate ID.');
        }

        if ($timestamp === $this->lastTimestamp) {
            $this->sequence = ($this->sequence + 1) & self::MAX_SEQUENCE;

            if ($this->sequence === 0) {
                // Sequence overflowed (>4095 in 1ms): spin-wait for next millisecond
                while ($timestamp <= $this->lastTimestamp) {
                    $timestamp = $this->currentTimestamp();
                }
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        $id = (($timestamp - self::EPOCH) << (self::NODE_BITS + self::SEQUENCE_BITS))
            | ($this->nodeId << self::SEQUENCE_BITS)
            | $this->sequence;

        return (string) $id;
    }

    /**
     * Decode a Snowflake ID back to its components.
     *
     * @return array{timestamp_ms: int, datetime: string, node_id: int, sequence: int}
     */
    public function parse(string $id): array
    {
        $numeric = (int) $id;

        $sequence  = $numeric & self::MAX_SEQUENCE;
        $nodeId    = ($numeric >> self::SEQUENCE_BITS) & self::MAX_NODE_ID;
        $timestamp = ($numeric >> (self::NODE_BITS + self::SEQUENCE_BITS)) + self::EPOCH;

        $seconds      = intdiv($timestamp, 1000);
        $milliseconds = $timestamp % 1000;
        $datetime     = \DateTime::createFromFormat('U', (string) $seconds, new \DateTimeZone('UTC'));

        return [
            'timestamp_ms' => $timestamp,
            'datetime'     => $datetime->format('Y-m-d H:i:s').'.'.str_pad((string) $milliseconds, 3, '0', STR_PAD_LEFT).' UTC',
            'node_id'      => $nodeId,
            'sequence'     => $sequence,
        ];
    }

    private function currentTimestamp(): int
    {
        return intdiv(hrtime(true), 1_000_000);
    }
}
