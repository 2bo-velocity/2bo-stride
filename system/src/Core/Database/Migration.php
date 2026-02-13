<?php

namespace Stride\Core\Database;

abstract class Migration
{
    abstract public function up(): void;
    abstract public function down(): void;
}
