<?php

namespace SSF\ORM\Object\Metadata;

enum DataType: string
{
    case BigInt = 'BIGINT';
    case Bit = 'BIT';
    case Decimal = 'DECIMAL';
    case Double = 'DOUBLE';
    case Float = 'FLOAT';
    case Int = 'INT';
    case MediumInt = 'MEDIUMINT';
    case Numeric = 'NUMERIC';
    case SmallInt = 'SMALLINT';
    case TinyInt = 'TINYINT';

    case Date = 'DATE';
    case Datetime = 'DATETIME';
    case Time = 'TIME';
    case Year = 'YEAR';

    case Binary = 'BINARY';
    case Blob = 'BLOB';
    case Char = 'CHAR';
    case Enum = 'ENUM';
    case Json = 'JSON';
    case LongBlob = 'LONGBLOB';
    case LongText = 'LONGTEXT';
    case MediumBlob = 'MEDIUMBLOB';
    case MediumText = 'MEDIUMTEXT';
    case Set = 'SET';
    case Text = 'TEXT';
    case TinyBlob = 'TINYBLOB';
    case TinyText = 'TINYTEXT';
    case Varchar = 'VARCHAR';
    case Varbinary = 'VARBINARY';

    case Geometry = 'GEOMETRY';
    case GeometryCollection = 'GEOMETRYCOLLECTION';
    case Linestring = 'LINESTRING';
    case MultiLinestring = 'MULTILINESTRING';
    case MultiPoint = 'MULTIPOINT';
    case MultiPolygon = 'MULTIPOLYGON';
    case Point = 'POINT';
    case Polygon = 'POLYGON';

    public function isBinary(): bool
    {
        return match($this) {
            DataType::Binary, DataType::Varbinary => true,
            default => false
        };
    }

    public function isBlob(): bool
    {
        return match($this) {
            DataType::Blob, DataType::LongBlob, DataType::MediumBlob, DataType::TinyBlob => true,
            default => false
        };
    }

    public function isBool(): bool
    {
        return match($this) {
            DataType::TinyInt => true,
            default => false
        };
    }

    public function isInt(): bool
    {
        return match($this) {
            DataType::Int, DataType::SmallInt, DataType::MediumInt, DataType::BigInt => true,
            default => false
        };
    }

    public function isNumeric(): bool
    {
        return match($this) {
            DataType::BigInt, DataType::Bit, DataType::Double, DataType::Float, DataType::Int, DataType::MediumInt,
            DataType::Numeric, DataType::SmallInt, DataType::TinyInt => true,
            default => false
        };
    }

    public function isSpacial(): bool
    {
        return match($this) {
            DataType::Geometry, DataType::GeometryCollection, DataType::Linestring, DataType::MultiLinestring,
            DataType::MultiPoint, DataType::MultiPolygon, DataType::Point, DataType::Polygon => true,
            default => false
        };
    }

    public function isString(): bool
    {
        return match($this) {
            DataType::Blob, DataType::TinyBlob, DataType::MediumBlob, DataType::LongBlob,
            DataType::Date, DataType::Datetime, DataType::Time, DataType::Year,
            DataType::Text, DataType::TinyText, DataType::MediumText, DataType::LongText,
            DataType::Set, DataType::Enum,
            DataType::Varchar, DataType::Varbinary,
            DataType::Json,
            DataType::Geometry, DataType::GeometryCollection, DataType::Linestring, DataType::MultiLinestring,
            DataType::MultiPoint, DataType::MultiPolygon, DataType::Point, DataType::Polygon => true,
            default => false,
        };
    }

    public function isText(): bool
    {
        return match($this) {
            DataType::LongText, DataType::MediumText, DataType::Text, DataType::TinyText => true,
            default => false
        };
    }
}
