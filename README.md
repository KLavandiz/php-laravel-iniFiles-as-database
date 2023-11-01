<h1>PHP use .ini files as database for basic projects</h1>

<h3>Installation</h3>

import FileDBEngine to your Model class

use App\CoreDB\FileDBEnginee;

add a function to your model class as

    public function FileDB(){
    
        return new FileDBEnginee($this);
        
    }

<h3>Usage</h3>

///// Add

$user = new User();

$user ->FileDB()->add([

    'columnName' => 'Value',
    
    'columnName' => 'Value',
    
    'columnName' => 'Value',
    
    'columnName' => 'Value'
    
]);


//////// Delete

$user = new User();

$user->FileDB()-deleteById(12) // delete the record has id number of 12


///// Update

$user = new User();

$user ->FileDB()->updateById(12,[

    'columnName' => 'Value',
    
    'columnName' => 'Value',
    
    'columnName' => 'Value',
    
    'columnName' => 'Value'
    
]);

////// Query

$user = new User();

$user ->FileDB()->runQuery([

'columnName' => ['='=>'value']

])
