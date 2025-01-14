<?php

namespace Sthom\Kernel\Database;

use ReflectionClass;
use ReflectionProperty;
use DateTimeImmutable;

class Hydrator
{
    /**
     * Types de base supportés pour la conversion automatique
     */
    private const TYPES = [
        'string' => 'string',
        'int' => 'integer',
        'float' => 'double',
        'bool' => 'boolean',
        'array' => 'array'
    ];

    /**
     * Hydrate une entité avec les données fournies
     */
    public static function hydrate(array $data, string $entityClass): object
    {
        // Création de l'entité
        $entity = new $entityClass();
        $reflection = new ReflectionClass($entityClass);

        foreach ($data as $property => $value) {
            try {
                self::setPropertyValue($entity, $reflection, $property, $value);
            } catch (\Exception $e) {
                // Log l'erreur mais continue l'hydratation
                error_log("Erreur d'hydratation pour la propriété '$property': " . $e->getMessage());
            }
        }

        return $entity;
    }

    /**
     * Définit la valeur d'une propriété en utilisant son setter ou directement
     */
    private static function setPropertyValue(object $entity, ReflectionClass $reflection, string $property, mixed $value): void
    {
        // Vérifier si la propriété existe
        if (!$reflection->hasProperty($property)) {
            return; // Ignore les propriétés qui n'existent pas dans l'entité
        }

        $reflectionProperty = $reflection->getProperty($property);
        $setter = 'set' . ucfirst($property);

        // Convertir la valeur au bon type
        $value = self::convertValue($value, $reflectionProperty);

        // Utiliser le setter s'il existe
        if (method_exists($entity, $setter)) {
            $entity->$setter($value);
            return;
        }

        // Sinon, définir la valeur directement sur la propriété
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($entity, $value);
    }

    /**
     * Convertit une valeur dans le type approprié
     */
    private static function convertValue(mixed $value, ReflectionProperty $property): mixed
    {
        if ($value === null) {
            return null;
        }

        $type = $property->getType()?->getName();

        if (!$type) {
            return $value; // Si pas de type défini, retourner la valeur telle quelle
        }

        return match ($type) {
            // Types de base
            'string' => (string)$value,
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => (bool)$value,
            'array' => is_string($value) ? json_decode($value, true) : (array)$value,

            // Types spéciaux
            DateTimeImmutable::class => self::createDateTime($value),

            // Type par défaut
            default => $value
        };
    }

    /**
     * Crée un objet DateTime à partir d'une valeur
     */
    private static function createDateTime(mixed $value): DateTimeImmutable
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        if (is_string($value)) {
            return new DateTimeImmutable($value);
        }

        if (is_int($value)) {
            return (new DateTimeImmutable())->setTimestamp($value);
        }

        throw new \InvalidArgumentException('Impossible de convertir la valeur en DateTime');
    }

    public static function extract(object $entity)
    {
        $reflection = new ReflectionClass($entity);
        $data = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            if ($property->isInitialized($entity)) {
                $value = $property->getValue($entity);
                $data[$property->getName()] = $value;

            }
        }

        return $data;
    }
}