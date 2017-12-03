## dto alternative as declared structure

example:

return [
    Structure->string("name")->optional()->description("Example"),
    Structure->list("name", [
        Structure->integer("age"),
        Structure->float("weight"),
        Structure->boolean("active"),
        Structure->string("name"),
        Structure->array("list")
    ])
];

alternative:

return [
    "name" => String()->optional()->description(),
    "list" => Array([
        "age" => Integer(),
        "weight" => Float(),
        "active" => Boolean(),
        "name" => String(),
        "list" => Array()
    ])->optional()->validator(function($data){
        //if $data['age'] > 99 
        return "Error message"
        //if all good then return true
        //When not throwing exceptions, we can list all of the errors at once
    })
];

//how to build object from structure?
//how to populate object
//how to fetch a single value
//how to fetch a nested value, lets say you got array of "list" and

//fetching methods, (array, string, integer, float)

$dataObject->getString("name") //ensures return type
$dataObject->getInteger("age")
$dataObject->getFloat("weight")
$dataObject->getArray("list")
$dataObject->getList("name")
$dataObject->get(key) //returns value as is
$dataObject->listProperties() <- return first level

$dataObject->push("list", [data...])
$dataObject->remove("list", index)

$dataObject->set(key, value) //if types are incompatible then throw exception
