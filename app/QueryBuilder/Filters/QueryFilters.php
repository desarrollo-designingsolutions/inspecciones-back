<?php

namespace App\QueryBuilder\Filters;

use Carbon\Carbon;

class QueryFilters
{
    /**
     * Filtra un campo de texto buscando coincidencias parciales en un array de sufijos.
     */
    public static function filterByText($query, $value, $field, array $endsWith)
    {
        $valueLower = strtolower($value);

        foreach ($endsWith as $suffix => $fieldValue) {
            $suffixLower = strtolower($suffix);
            if (str_contains($suffixLower, $valueLower) !== false) {
                $query->orWhere($field, $fieldValue);

                return;
            }
        }
    }

    /**
     * Filtra un campo de fecha manejando fragmentos parciales en formato d-m-Y.
     *
     * @param  mixed  $query  El objeto de consulta (Builder)
     * @param  mixed  $value  El valor ingresado por el usuario (e.g., "01-01-2025", "01-01", "01-01-2")
     * @param  string  $field  El nombre del campo en la base de datos (e.g., "start_date")
     * @return void
     */
    public static function filterByDMYtoYMD($query, $value, $field)
    {
        // Limpiar el valor eliminando guiones al inicio o final
        $cleanValue = trim($value, '-');
        // Separar el valor en partes usando el guión
        $parts = explode('-', $cleanValue);

        if (count($parts) === 3) {
            // Caso 1: Fecha completa o con año parcial (ej: "01-01-2025" o "01-01-2")
            $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT); // Día: "01"
            $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT); // Mes: "01"
            $yearPart = $parts[2]; // Parte del año: "2025", "2", "20", "202"

            if (strlen($yearPart) === 4 && !empty($yearPart)) {
                // Subcaso 1.1: Año completo (d-m-Y)
                try {
                    $date = Carbon::createFromFormat('d-m-Y', $cleanValue);
                    $formattedDate = $date->format('Y-m-d'); // "2025-01-01"
                    $query->orWhere($field, 'like', "%$formattedDate%");

                    return;
                } catch (\Exception $e) {
                    // Si falla, seguir con la lógica de año parcial
                }
            }

            // Subcaso 1.2: Año parcial (ej: "01-01-2", "01-01-20", "01-01-202")
            if (!empty($yearPart)) {
                $formattedPartial = "$yearPart%-$month-$day"; // Ej: "2%-01-01", "20%-01-01", "202%-01-01"
                $query->orWhere($field, 'like', "%$formattedPartial%");

                return;
            }
        }

        if (count($parts) >= 2 && !empty($parts[0])) {
            // Caso 2: Fecha parcial (d-m completo o incompleto, ej: "01-01" o "01-0")
            $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT); // Día: "01"
            $monthPart = !empty($parts[1]) ? $parts[1] : ''; // Parte del mes: "01" o "0"

            // Construir el patrón ajustado al formato Y-m-d
            if ($monthPart) {
                $formattedPartial = "%-$monthPart%-$day"; // Ej: "%-01%-01" o "%-0%-01"
            } else {
                $formattedPartial = "%-$day"; // Ej: "%-01"
            }
            $query->orWhere($field, 'like', $formattedPartial);

            return;
        }

        // Caso 3: Si no encaja en los formatos anteriores, buscar como texto
        $query->orWhere($field, 'like', "%$cleanValue%");
    }

    /**
     * Filtra un campo de estado comparando un valor de entrada con un arreglo de estados personalizados.
     *
     * Este método compara el valor ingresado con un conjunto de estados definidos en un arreglo,
     * permitiendo diferentes tipos de comparación (estricta, igualdad, desigualdad o búsqueda parcial).
     * Si se encuentra una coincidencia, aplica un filtro al campo especificado en la consulta.
     * Si no hay coincidencia, realiza una búsqueda directa con LIKE en el campo.
     *
     * @param  mixed  $query         El objeto de consulta (Builder) sobre el que se aplicará el filtro.
     * @param  mixed  $value         El valor ingresado por el usuario para buscar (e.g., "Asignado", "comp").
     * @param  string $field         El nombre del campo en la base de datos donde se aplicará el filtro (e.g., "status").
     * @param  array $customTypes Arreglo de estados con estructura [['value' => ..., 'title' => ...], ...].
     *                               Si es null, no se validan estados predefinidos (requiere arreglo externo).
     * @param  string $compareByKey  La clave del arreglo de estados con la que se compara $value (default: "title").
     * @param  string $returnByKey   La clave del arreglo de estados cuyo valor se usará en el filtro (default: "value").
     * @param  string $typeSearch    Tipo de comparación: '===', '==', '!=', 'LIKE' (default: "LIKE").
     * @return void
     */
    public static function filterByStatusOld($query, $value, $field, $customTypes = null, $compareByKey = 'title', $returnByKey = 'value', $typeSearch = 'LIKE')
    {
        if ($value !== null) {
            // Convertir el valor a minúsculas para comparación insensible
            $inputValue = strtolower($value);

            foreach ($customTypes as $state) {
                // Convertir el valor de comparación a minúsculas
                $stateValue = strtolower($state[$compareByKey]);

                // Comparar según el tipo de búsqueda
                switch ($typeSearch) {
                    case '===': // Comparación estricta
                        if ($stateValue === $inputValue) {
                            $query->orWhere($field, $state[$returnByKey]);
                            return;
                        }
                        break;

                    case '==': // Comparación de igualdad
                        if ($stateValue == $inputValue) {
                            $query->orWhere($field, $state[$returnByKey]);
                            return;
                        }
                        break;

                    case '!=': // Comparación de desigualdad
                        if ($stateValue != $inputValue) {
                            $query->orWhere($field, $state[$returnByKey]);
                            return;
                        }
                        break;

                    case 'LIKE': // Búsqueda parcial
                        if (strpos($stateValue, $inputValue) !== false) {
                            $query->orWhere($field, $state[$returnByKey]);
                            return;
                        }
                        break;

                    default: // Por defecto, comparación estricta
                        if ($stateValue === $inputValue) {
                            $query->orWhere($field, $state[$returnByKey]);
                            return;
                        }
                        break;
                }
            }

            // Si no hay coincidencia, buscar directamente en el campo
            $query->orWhere($field, 'like', "%$inputValue%");
        }
    }
}
