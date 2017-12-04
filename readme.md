# Structure library

## Author
*Lauri Orgla <theorx@hotmail.com>*

## Description
*Structure library meant for describing complex data structures for transferring data*

## Features

* Structures with scalar types (array, boolean, string, float, integer)
* Nested structure type (list)
* Structure validation
* Validator function declaration for each property, must pass callable `S::Type->validator({callable})`
* Validate whole object and get an array of errors if __values__ in `DataObject` are not valid
* Add custom descriptors for each property `S::Type->add(key, value)` - Can be used for automating
 documentation generation for data structures
* Populating DataObject by passing an array, nested structures are automatically populated
* Getter methods for each type (`getString`, `getFloat`, `getBoolean`, `getInteger`, `getArray`, `getList`)
* 

## Usage



## Examples

