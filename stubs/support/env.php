<?php

if (! function_exists('update_env')) {
    /**
     * Update environment variable in .env file.
     */
    function update_env(string $key, mixed $value): bool
    {
        $envFile = base_path('.env');

        if (! file_exists($envFile)) {
            return false;
        }

        $envContent = file_get_contents($envFile);

        // Format the value properly
        $formattedValue = is_bool($value)
            ? ($value ? 'true' : 'false')
            : (is_string($value) && str_contains($value, ' ')
                ? '"'.$value.'"'
                : $value);

        // Check if key exists
        $pattern = "/^{$key}=.*/m";

        if (preg_match($pattern, $envContent)) {
            // Update existing key
            $envContent = preg_replace($pattern, "{$key}={$formattedValue}", $envContent);
        } else {
            // Add new key at the end
            $envContent .= "\n{$key}={$formattedValue}";
        }

        file_put_contents($envFile, $envContent);

        return true;
    }
}

if (! function_exists('update_env_multiple')) {
    /**
     * Update multiple environment variables in .env file.
     */
    function update_env_multiple(array $data): bool
    {
        foreach ($data as $key => $value) {
            update_env($key, $value);
        }

        return true;
    }
}
