$RESULT_FILE = "check_code.result.cache"
Remove-Item -Path $RESULT_FILE -ErrorAction SilentlyContinue
New-Item -Path $RESULT_FILE -ItemType File | Out-Null

Write-Output "Installing dependencies..."
try {
    composer install --no-interaction
    composer require --dev friendsofphp/php-cs-fixer --no-interaction
    composer require --dev squizlabs/php_codesniffer --no-interaction
    composer require --dev escapestudios/symfony2-coding-standard --no-interaction

    $symfony2StandardPath = (Get-Item -Path "vendor/escapestudios/symfony2-coding-standard").FullName
    ./vendor/bin/phpcs --config-set installed_paths $symfony2StandardPath
    ./vendor/bin/phpcs --config-set default_standard Symfony
}
catch {
    Write-Warning "Failed to install dependencies."
    exit 1
}



Write-Output "Running php-cs-fixer..."
./vendor/bin/php-cs-fixer fix src/ --dry-run -vvv --rules=@Symfony,@PSR1,@PSR2,@PSR12 | Out-File -FilePath $RESULT_FILE -Append
php ./vendor/bin/php-cs-fixer fix src/ --dry-run -vvv --rules "@Symfony","@PSR1","@PSR2","@PSR12" | Out-File -FilePath $RESULT_FILE -Append

Remove-Item -Path ".php-cs-fixer.dist.php" -ErrorAction SilentlyContinue
Remove-Item -Path ".php-cs-fixer.cache" -ErrorAction SilentlyContinue

Write-Output "Running phpcs..."
./vendor/bin/phpcs --standard=Symfony src/ --ignore=Kernel.php | Out-File -FilePath $RESULT_FILE -Append

Write-Output "Running debug:translation..."
try {
    ./bin/console debug:translation en --only-missing
    ./bin/console debug:translation pl --only-missing | Out-File -FilePath $RESULT_FILE -Append
}
catch {
    Write-Warning "Failed to run debug:translation."
}

Write-Output "Running DB schema and data fixtures..."
try {
    ./bin/console doctrine:schema:drop --no-interaction --full-database --force
    ./bin/console doctrine:migrations:migrate --no-interaction
    ./bin/console doctrine:fixtures:load --no-interaction | Out-File -FilePath $RESULT_FILE -Append
}
catch {
    Write-Warning "Failed to run DB schema and data fixtures."
}

Write-Output "Tear down..."
try {
    ./bin/console doctrine:schema:drop --no-interaction --full-database --force
    Remove-Item -Path "var" -Recurse -Force
    Remove-Item -Path "vendor" -Recurse -Force | Out-Null
}
catch {
    Write-Warning "Failed to tear down."
}
