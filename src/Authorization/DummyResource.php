<?php

namespace LaminasApiToolsAddon\Authorization;

use CirclicalUser\Provider\ResourceInterface;

class DummyResource implements ResourceInterface {

    public function __construct(private string $class, private string $id) {}

    public function getClass(): string {
        return $this->class;
    }

    public function getId(): string {
        return $this->id;
    }
}
