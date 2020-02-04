<?php

namespace LibBundle\Excel;

use LibBundle\Util\Xml;

class PhpExcelObjectParser
{
    private $objectClass = null;
    private $columnMappings = array();
    private $formatters = array();
    private $startRow = 0;
    private $endRow = 0;
    private $index = 0;

    public function addFormatter($name, $func)
    {
        $this->formatters[$name] = $func;
    }

    public function removeFormatter($name)
    {
        unset($this->formatters[$name]);
    }

    public function clearFormatter()
    {
        $this->formatters = array();
    }

    public function reset()
    {
        $this->objectClass = null;
        $this->columnMappings = array();
        $this->formatters = array();
        $this->startRow = 0;
        $this->endRow = 0;
        $this->index = 0;
    }

    public function setObjectMappingsFromXml($xml)
    {
        $this->reset();
        $this->processObjectMappings(Xml::parse($xml));
    }

    public function load($pathname, $worksheetIndex = 0)
    {
        if (empty($this->objectClass) || empty($this->columnMappings)) {
            return null;
        }

        $objects = array();
        $references = array();
        $phpExcel = \PHPExcel_IOFactory::load($pathname);
        $worksheet = $phpExcel->getSheet($worksheetIndex);
        foreach ($worksheet->getRowIterator() as $row) {
            $currentRow = $row->getRowIndex() - 1;
            $inRange = $currentRow >= $this->startRow;
            if ($this->endRow > -1) {
                $inRange = $inRange && $currentRow <= $this->endRow;
            }
            if (!$inRange) {
                continue;
            }
            $class = $this->objectClass;
            $object = new $class();
            foreach ($row->getCellIterator() as $cell) {
                $this->processCell($cell, $object, $references);
            }
            $objects[$this->index++] = $object;
        }

        return array(
            'objects' => $objects,
            'references' => $references,
        );
    }

    private function processCell($cell, $object, &$references)
    {
        $column = $cell->getColumn();
        if (!isset($this->columnMappings[$column])) {
            return;
        }

        $mappingItems = $this->columnMappings[$column];
        $value = $cell->getValue();
        if (isset($this->formatters[$mappingItems['formatter']])) {
            $formatter = $this->formatters[$mappingItems['formatter']];
            $value = $formatter($value);
        }
        $caster = empty($mappingItems['type']) ? null : $mappingItems['type'] . 'val';
        if ($caster !== null) {
            $value = $caster($value);
        }
        if (!empty($mappingItems['field'])) {
            $field = $mappingItems['field'];
            if (!$mappingItems['unmapped']) {
                if ($mappingItems['public']) {
                    $object->$field = $value;
                } else {
                    $setter = 'set' . ucfirst($field);
                    $object->$setter($value);
                }
            }
            if ($mappingItems['referenced']) {
                $references[$field][$value][] = $this->index;
            }
        }
    }

    private function processObjectMappings($current)
    {
        $attributes = $current->attributes;
        switch ($current->name) {
            case 'x:Sheet':
                $this->objectClass = $attributes['x:ObjectClass'];
                break;
            case 'x:Columns':
                $this->startRow = isset($attributes['x:StartRow']) ? $attributes['x:StartRow'] - 1 : 0;
                $this->endRow = isset($attributes['x:EndRow']) ? $attributes['x:EndRow'] - 1 : -1;
                break;
            case 'x:Column':
                $items = array(
                    'field' => $attributes['x:Field'],
                    'type' => null,
                    'unmapped' => false,
                    'referenced' => false,
                    'public' => false,
                    'formatter' => null,
                );
                if (isset($attributes['x:DataType']) && in_array($attributes['x:DataType'], array('Boolean', 'DoublePrecision', 'Integer', 'SinglePrecision', 'String'))) {
                    $dataType = $attributes['x:DataType'];
                    $casts = array(
                        'Boolean' => 'bool',
                        'DoublePrecision' => 'double',
                        'Integer' => 'int',
                        'SinglePrecision' => 'float',
                        'String' => 'str',
                    );
                    $type = isset($casts[$dataType]) ? $casts[$dataType] : null;
                    $items['type'] = $type;
                }
                if (isset($attributes['x:Unmapped']) && in_array($attributes['x:Unmapped'], array('0', '1'))) {
                    $items['unmapped'] = boolval($attributes['x:Unmapped']);
                }
                if (isset($attributes['x:Referenced']) && in_array($attributes['x:Referenced'], array('0', '1'))) {
                    $items['referenced'] = boolval($attributes['x:Referenced']);
                }
                if (isset($attributes['x:Public']) && in_array($attributes['x:Public'], array('0', '1'))) {
                    $items['public'] = boolval($attributes['x:Public']);
                }
                if (isset($attributes['x:FormatterID'])) {
                    $items['formatter'] = $attributes['x:FormatterID'];
                }
                $this->columnMappings[$attributes['x:Name']] = $items;
                break;
        }
        if (!empty($current->children)) {
            foreach ($current->children as $childNode) {
                $this->processObjectMappings($childNode);
            }
        }
    }
}
