<?php

namespace Flaravel\Generator;


class SyntaxBuilder
{

    /**
     * A template to be inserted.
     *
     * @var string
     */
    private $template;


    public function create($schema, $meta, $type = "migration")
    {
        if ($type == "migration") {

            $up = $this->createSchemaForUpMethod($schema, $meta);
            $down = $this->createSchemaForDownMethod($schema, $meta);

            return compact('up', 'down');

        } else {
            throw new \Exception("Type not found in syntaxBuilder");
        }
    }

     /**
     * 创建迁移 up
     *
     * @param  string $schema
     * @param  array $meta
     * @return string
     * @throws GeneratorException
     */
    private function createSchemaForUpMethod($schema, $meta)
    {
        $fields = $this->constructSchema($schema);

        if ($meta['action'] == 'create') {
            return $this->insert($fields)->into($this->getCreateSchemaWrapper());
        }

        throw new GeneratorException;
    }

    /**
     * 创建迁移 down
     *
     * @param  array $schema
     * @param  array $meta
     * @return string
     * @throws GeneratorException
     */
    private function createSchemaForDownMethod($schema, $meta)
    {
        if ($meta['action'] == 'create') {
            return sprintf("Schema::drop('%s');", $meta['table']);
        }

        throw new GeneratorException;
    }

    /**
     * Store the given template, to be inserted somewhere.
     *
     * @param  string $template
     * @return $this
     */
    private function insert($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the stored template, and insert into the given wrapper.
     *
     * @param  string $wrapper
     * @param  string $placeholder
     * @return mixed
     */
    private function into($wrapper, $placeholder = 'schema_up')
    {
        return str_replace('{{' . $placeholder . '}}', $this->template, $wrapper);
    }

    /**
     * Get the wrapper template for a "create" action.
     *
     * @return string
     */
    private function getCreateSchemaWrapper()
    {
        return file_get_contents(__DIR__ . '/Stubs/migration-create.stub');
    }

    /**
     * Construct the schema fields.
     *
     * @param  array $schema
     * @param  string $direction
     * @return array
     */
    private function constructSchema($schema, $direction = 'Add')
    {
        if (!$schema) return '';

        $fields = array_map(function ($field) use ($direction) {
            $method = "{$direction}Column";
            return $this->$method($field);
        }, $schema);


        return implode("\n" . str_repeat(' ', 12), $fields);
    }

     /**
     * Construct the syntax to add a column.
     *
     * @param  string $field
     * @param string $type
     * @param $meta
     * @return string
     */
    private function addColumn($field, $type = "migration", $meta = "")
    {


        if ($type == 'migration') {

            $syntax = sprintf("\$table->%s('%s')", $field['type'], $field['name']);

            // If there are arguments for the schema type, like decimal('amount', 5, 2)
            // then we have to remember to work those in.
            if ($field['arguments']) {
                $syntax = substr($syntax, 0, -1) . ', ';

                $syntax .= implode(', ', $field['arguments']) . ')';
            }

            foreach ($field['options'] as $method => $value) {
                $syntax .= sprintf("->%s(%s)", $method, $value === true ? '' : $value);
            }

            $syntax .= ';';

        }

        return $syntax;
    }
}
