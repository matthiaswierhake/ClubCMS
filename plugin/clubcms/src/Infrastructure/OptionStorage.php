<?php

declare(strict_types=1);

namespace ClubCMS\Infrastructure;

final class OptionStorage
{
    public function __construct(
        private readonly string $prefix = 'clubcms_',
    ) {
    }

    public function get(string $name, mixed $default = []): mixed
    {
        $value = get_option($this->prefix . $name, $default);

        return $value === false ? $default : $value;
    }

    public function update(string $name, mixed $value): bool
    {
        return update_option($this->prefix . $name, $value, false);
    }

    public function delete(string $name): bool
    {
        return delete_option($this->prefix . $name);
    }
}
