<?php

namespace Build\Migration;

/**
 * Klasifikace driftu žurnál ↔ soubory ve větvi.
 */
enum MigrationDriftStatus: string
{
    case Ok = 'ok';
    case Behind = 'behind';
    case Ahead = 'ahead';
    /** Žurnál má verze mimo soubory a zároveň zbývají pending. */
    case Diverged = 'diverged';
}
