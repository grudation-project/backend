<?php

namespace Modules\Map\Traits;

use Elattar\LaravelMysqlSpatial\Eloquent\BaseBuilder;
use Elattar\LaravelMysqlSpatial\Eloquent\Builder;
use Elattar\LaravelMysqlSpatial\Eloquent\SpatialExpression;
use Elattar\LaravelMysqlSpatial\Exceptions\SpatialFieldsNotDefinedException;
use Elattar\LaravelMysqlSpatial\Exceptions\UnknownSpatialFunctionException;
use Elattar\LaravelMysqlSpatial\Exceptions\UnknownSpatialRelationFunction;
use Elattar\LaravelMysqlSpatial\Types\Geometry;
use Elattar\LaravelMysqlSpatial\Types\GeometryInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait MapTrait
{
    public $geometries = [];

    protected $stRelations = [
        'within',
        'crosses',
        'contains',
        'disjoint',
        'equals',
        'intersects',
        'overlaps',
        'touches',
    ];

    protected $stOrderFunctions = [
        'distance',
        'distance_sphere',
    ];

    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new BaseBuilder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    protected function performInsert(EloquentBuilder $query, array $options = [])
    {
        foreach ($this->attributes as $key => $value) {
            if ($value instanceof GeometryInterface) {
                $this->geometries[$key] = $value; //Preserve the geometry objects prior to the insert
                $this->attributes[$key] = new SpatialExpression($value);
            }
        }

        $insert = parent::performInsert($query, $options);

        foreach ($this->geometries as $key => $value) {
            $this->attributes[$key] = $value; //Retrieve the geometry objects so they can be used in the model
        }

        return $insert; //Return the result of the parent insert
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $spatial_fields = $this->getSpatialFields();

        foreach ($attributes as $attribute => &$value) {
            if (in_array($attribute, $spatial_fields) && is_string($value) && strlen($value) >= 13) {
                $value = Geometry::fromWKB($value);
            }
        }

        return parent::setRawAttributes($attributes, $sync);
    }

    public function getSpatialFields()
    {
        if (property_exists($this, 'spatialFields')) {
            return $this->spatialFields;
        } else {
            throw new SpatialFieldsNotDefinedException(__CLASS__.' has to define $spatialFields');
        }
    }

    public function isColumnAllowed($geometryColumn)
    {
        if (! in_array($geometryColumn, $this->getSpatialFields())) {
            throw new SpatialFieldsNotDefinedException;
        }

        return true;
    }

    public function scopeDistance($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query->whereRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?, ?)) <= ?", [
            $geometry->toWkt(),
            $geometry->getSrid(),
            $distance,
        ]);

        return $query;
    }

    public function scopeDistanceExcludingSelf($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query = $this->scopeDistance($query, $geometryColumn, $geometry, $distance);

        $query->whereRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?, ?)) != 0", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $query;
    }

    public function scopeDistanceValue($query, $geometryColumn, $geometry)
    {
        $this->isColumnAllowed($geometryColumn);

        $columns = $query->getQuery()->columns;

        if (! $columns) {
            $query->select('*');
        }

        $query->selectRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?, ?)) as distance", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);
    }

    public function scopeDistanceSphere($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query->whereRaw("st_distance_sphere(`$geometryColumn`, ST_GeomFromText(?, ?)) <= ?", [
            $geometry->toWkt(),
            $geometry->getSrid(),
            $distance,
        ]);

        return $query;
    }

    public function scopeDistanceSphereExcludingSelf($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query = $this->scopeDistanceSphere($query, $geometryColumn, $geometry, $distance);

        $query->whereRaw("st_distance_sphere($geometryColumn, ST_GeomFromText(?, ?)) != 0", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $query;
    }

    public function scopeDistanceSphereValue($query, $geometryColumn, $geometry)
    {
        $this->isColumnAllowed($geometryColumn);

        $columns = $query->getQuery()->columns;

        if (! $columns) {
            $query->select('*');
        }
        $query->selectRaw("st_distance_sphere(`$geometryColumn`, ST_GeomFromText(?, ?)) as distance", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);
    }

    public function scopeComparison($query, $geometryColumn, $geometry, $relationship)
    {
        $this->isColumnAllowed($geometryColumn);

        if (! in_array($relationship, $this->stRelations)) {
            throw new UnknownSpatialRelationFunction($relationship);
        }

        $query->whereRaw("st_{$relationship}(`$geometryColumn`, ST_GeomFromText(?, ?))", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $query;
    }

    public function scopeWithin($query, $geometryColumn, $polygon)
    {
        return $this->scopeComparison($query, $geometryColumn, $polygon, 'within');
    }

    public function scopeCrosses($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'crosses');
    }

    public function scopeContains($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'contains');
    }

    public function scopeDisjoint($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'disjoint');
    }

    public function scopeEquals($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'equals');
    }

    public function scopeIntersects($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'intersects');
    }

    public function scopeOverlaps($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'overlaps');
    }

    public function scopeDoesTouch($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'touches');
    }

    public function scopeOrderBySpatial($query, $geometryColumn, $geometry, $orderFunction, $direction = 'asc')
    {
        $this->isColumnAllowed($geometryColumn);

        if (! in_array($orderFunction, $this->stOrderFunctions)) {
            throw new UnknownSpatialFunctionException($orderFunction);
        }

        $query->orderByRaw("st_{$orderFunction}(`$geometryColumn`, ST_GeomFromText(?, ?)) {$direction}", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $query;
    }

    public function scopeOrderByDistance($query, $geometryColumn, $geometry, $direction = 'asc')
    {
        return $this->scopeOrderBySpatial($query, $geometryColumn, $geometry, 'distance', $direction);
    }

    public function scopeOrderByDistanceSphere($query, $geometryColumn, $geometry, $direction = 'asc')
    {
        return $this->scopeOrderBySpatial($query, $geometryColumn, $geometry, 'distance_sphere', $direction);
    }
}
