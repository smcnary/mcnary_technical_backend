<?php

namespace Symfony\Config\ApiPlatform;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Graphql'.\DIRECTORY_SEPARATOR.'GraphiqlConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Graphql'.\DIRECTORY_SEPARATOR.'IntrospectionConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Graphql'.\DIRECTORY_SEPARATOR.'GraphqlPlaygroundConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Graphql'.\DIRECTORY_SEPARATOR.'CollectionConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GraphqlConfig 
{
    private $enabled;
    private $defaultIde;
    private $graphiql;
    private $introspection;
    private $maxQueryDepth;
    private $graphqlPlayground;
    private $maxQueryComplexity;
    private $nestingSeparator;
    private $collection;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * @default 'graphiql'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultIde($value): static
    {
        $this->_usedProperties['defaultIde'] = true;
        $this->defaultIde = $value;

        return $this;
    }

    /**
     * @default {"enabled":false}
    */
    public function graphiql(array $value = []): \Symfony\Config\ApiPlatform\Graphql\GraphiqlConfig
    {
        if (null === $this->graphiql) {
            $this->_usedProperties['graphiql'] = true;
            $this->graphiql = new \Symfony\Config\ApiPlatform\Graphql\GraphiqlConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "graphiql()" has already been initialized. You cannot pass values the second time you call graphiql().');
        }

        return $this->graphiql;
    }

    /**
     * @default {"enabled":true}
    */
    public function introspection(array $value = []): \Symfony\Config\ApiPlatform\Graphql\IntrospectionConfig
    {
        if (null === $this->introspection) {
            $this->_usedProperties['introspection'] = true;
            $this->introspection = new \Symfony\Config\ApiPlatform\Graphql\IntrospectionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "introspection()" has already been initialized. You cannot pass values the second time you call introspection().');
        }

        return $this->introspection;
    }

    /**
     * @default 20
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function maxQueryDepth($value): static
    {
        $this->_usedProperties['maxQueryDepth'] = true;
        $this->maxQueryDepth = $value;

        return $this;
    }

    /**
     * @deprecated The child node "graphql_playground" at path "graphql" is deprecated.
    */
    public function graphqlPlayground(array $value = []): \Symfony\Config\ApiPlatform\Graphql\GraphqlPlaygroundConfig
    {
        if (null === $this->graphqlPlayground) {
            $this->_usedProperties['graphqlPlayground'] = true;
            $this->graphqlPlayground = new \Symfony\Config\ApiPlatform\Graphql\GraphqlPlaygroundConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "graphqlPlayground()" has already been initialized. You cannot pass values the second time you call graphqlPlayground().');
        }

        return $this->graphqlPlayground;
    }

    /**
     * @default 500
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function maxQueryComplexity($value): static
    {
        $this->_usedProperties['maxQueryComplexity'] = true;
        $this->maxQueryComplexity = $value;

        return $this;
    }

    /**
     * The separator to use to filter nested fields.
     * @default '_'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function nestingSeparator($value): static
    {
        $this->_usedProperties['nestingSeparator'] = true;
        $this->nestingSeparator = $value;

        return $this;
    }

    /**
     * @default {"pagination":{"enabled":true}}
    */
    public function collection(array $value = []): \Symfony\Config\ApiPlatform\Graphql\CollectionConfig
    {
        if (null === $this->collection) {
            $this->_usedProperties['collection'] = true;
            $this->collection = new \Symfony\Config\ApiPlatform\Graphql\CollectionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "collection()" has already been initialized. You cannot pass values the second time you call collection().');
        }

        return $this->collection;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('default_ide', $value)) {
            $this->_usedProperties['defaultIde'] = true;
            $this->defaultIde = $value['default_ide'];
            unset($value['default_ide']);
        }

        if (array_key_exists('graphiql', $value)) {
            $this->_usedProperties['graphiql'] = true;
            $this->graphiql = \is_array($value['graphiql']) ? new \Symfony\Config\ApiPlatform\Graphql\GraphiqlConfig($value['graphiql']) : $value['graphiql'];
            unset($value['graphiql']);
        }

        if (array_key_exists('introspection', $value)) {
            $this->_usedProperties['introspection'] = true;
            $this->introspection = new \Symfony\Config\ApiPlatform\Graphql\IntrospectionConfig($value['introspection']);
            unset($value['introspection']);
        }

        if (array_key_exists('max_query_depth', $value)) {
            $this->_usedProperties['maxQueryDepth'] = true;
            $this->maxQueryDepth = $value['max_query_depth'];
            unset($value['max_query_depth']);
        }

        if (array_key_exists('graphql_playground', $value)) {
            $this->_usedProperties['graphqlPlayground'] = true;
            $this->graphqlPlayground = new \Symfony\Config\ApiPlatform\Graphql\GraphqlPlaygroundConfig($value['graphql_playground']);
            unset($value['graphql_playground']);
        }

        if (array_key_exists('max_query_complexity', $value)) {
            $this->_usedProperties['maxQueryComplexity'] = true;
            $this->maxQueryComplexity = $value['max_query_complexity'];
            unset($value['max_query_complexity']);
        }

        if (array_key_exists('nesting_separator', $value)) {
            $this->_usedProperties['nestingSeparator'] = true;
            $this->nestingSeparator = $value['nesting_separator'];
            unset($value['nesting_separator']);
        }

        if (array_key_exists('collection', $value)) {
            $this->_usedProperties['collection'] = true;
            $this->collection = new \Symfony\Config\ApiPlatform\Graphql\CollectionConfig($value['collection']);
            unset($value['collection']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['defaultIde'])) {
            $output['default_ide'] = $this->defaultIde;
        }
        if (isset($this->_usedProperties['graphiql'])) {
            $output['graphiql'] = $this->graphiql instanceof \Symfony\Config\ApiPlatform\Graphql\GraphiqlConfig ? $this->graphiql->toArray() : $this->graphiql;
        }
        if (isset($this->_usedProperties['introspection'])) {
            $output['introspection'] = $this->introspection->toArray();
        }
        if (isset($this->_usedProperties['maxQueryDepth'])) {
            $output['max_query_depth'] = $this->maxQueryDepth;
        }
        if (isset($this->_usedProperties['graphqlPlayground'])) {
            $output['graphql_playground'] = $this->graphqlPlayground->toArray();
        }
        if (isset($this->_usedProperties['maxQueryComplexity'])) {
            $output['max_query_complexity'] = $this->maxQueryComplexity;
        }
        if (isset($this->_usedProperties['nestingSeparator'])) {
            $output['nesting_separator'] = $this->nestingSeparator;
        }
        if (isset($this->_usedProperties['collection'])) {
            $output['collection'] = $this->collection->toArray();
        }

        return $output;
    }

}
