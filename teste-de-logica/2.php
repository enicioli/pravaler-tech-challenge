<?php

const mass_loss_rate_per_second = 0.25 / 30;
const minimum_mass = 0.1;

main();

/**
 * @return void
 */
function main(): void
{
    echo '########## PERDA DE MASSA ##########' . PHP_EOL;

    $mass = (float) readline('Qual a massa do material (em gramas)? ');

    $t = 0;
    while ($mass > minimum_mass) {
        $mass = massLoss($mass);
        $t++;
    }

    echo "Tempo necessário para atingir 0.1g: {$t} segundos" . PHP_EOL;
}

/**
 * @param float $mass
 * @return float|int
 */
function massLoss(float $mass): float
{
    return $mass - ($mass * mass_loss_rate_per_second);
}
