<?php
namespace App\CoreDB;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileDBEnginee{

    private  $dbType;
	
	private $dbName;
	
	private $connectionStatus;

    private $currentTable;

    private $conditionalStatament=array();


    public function __construct($that)
    {
       $className =  explode("\\",$that::class);

       $this->currentTable=end($className);

        $this->dbType = getenv('FILE_DB_CONNECTION');

        $this->dbName = getenv('FILE_DB_DATABASE');

        
        $this->connectionStatus=true;

     

        File::ensureDirectoryExists($this->dbName);
        
            try{
        
                if(!File::exists($this->dbName.'/'.$this->currentTable.'.ini')){

            
                    $this->connectionStatus =  file_put_contents($this->dbName.'/'.$this->currentTable.'.ini','');

        
                }
            }catch(Throwable $t){
        
                $this->connectionStatus=false;
        }

        return $this->connectionStatus;
       
    }

    private function loadData(){

        return $this->parse_ini($this->dbName.'/'.$this->currentTable.'.ini');

    }

    private function getAnId(){
        if(count($this->loadData($this->currentTable))){
            return (int)(max(array_keys($this->loadData($this->currentTable))))+1;
        }else{
            return 1;
        }
    }


    public function add($values){
        
        $data = $this->loadData($this->currentTable);

        $string = "[".$this->getAnId()."]\n";

            foreach($values as $key => $value){
            
                  $string.= $key."=".$value."\n";  // yeni veriler eklendi

            }

        file_put_contents($this->dbName.'/'.$this->currentTable.'.ini', $string, FILE_APPEND | LOCK_EX);
    }



    public function getAll(){
        return $this->loadData($this->currentTable);
    }


    private   function parse_ini () {
        $filepath = $this->dbName.'/'.$this->currentTable.'.ini';
        $ini = file( $filepath );
        if ( count( $ini ) == 0 ) { return array(); }
        $sections = array();
        $values = array();
        $globals = array();
        $i = 0;
        foreach( $ini as $line ){
            $line = trim( $line );
            // Comments
            if ( $line == '' || $line[0] == ';' ) { continue; }
            // Sections
            if ( $line[0] == '[' ) {
                $sections[] = substr( $line, 1, -1 );
                $i++;
                continue;
            }
            // Key-value pair
            list( $key, $value ) = explode( '=', $line, 2 );
            $key = trim( $key );
            $value = trim( $value );
            if ( $i == 0 ) {
                // Array values
                if ( substr( $line, -1, 2 ) == '[]' ) {
                    $globals[ $key ][] = $value;
                } else {
                    $globals[ $key ] = $value;
                }
            } else {
                // Array values
                if ( substr( $line, -1, 2 ) == '[]' ) {
                    $values[ $i - 1 ][ $key ][] = $value;
                } else {
                    $values[ $i - 1 ][ $key ] = $value;
                }
            }
        }
        for( $j=0; $j<$i; $j++ ) {
            $result[ $sections[ $j ] ] = $values[ $j ];
        }
        return $result + $globals;
    }


    private function update_ini_file($data) { 
		$content = ""; 
        $filepath = $this->dbName.'/'.$this->currentTable.'.ini';

		
		foreach($data as $section=>$values){
			//append the section 
			$content .= "[".$section."]\n"; 
			//append the values
			foreach($values as $key=>$value){
				$content .= $key."=".$value."\n"; 
			}
		}
		
		//write it into file
		if (!$handle = fopen( $filepath, 'w')) { 
			return false; 
		}
 
		$success = fwrite($handle, $content);
		fclose($handle); 
 
		return $success; 
	}

    public function getById($id){

            if(isset($this->loadData($this->currentTable)[$id]))
            return array($id=>$this->loadData($this->currentTable)[$id]); 
          
    }

    public function updateById($id,$newArray=array()){
        $newdata = $this->loadData($this->currentTable);
       
        foreach($newArray as $key=>$value){

            $newdata[$id][$key]=$value;
        }

        return $this->update_ini_file($newdata);
    }

    public function deleteById($id){

        $newdata = $this->loadData($this->currentTable);

        unset($newdata[$id]);

        return $this->update_ini_file($newdata);
    }

    


    public function runQuery($str){

         $this->conditionalStatament = $str;
    
    
         $data =  $this->loadData($this->currentTable);

         $filteredArray = array_filter($data, function($element){

        $ser ="";

        foreach($this->conditionalStatament as $key => $value)
                {
                 
                    if(($value[0])=="="){
                        $ser.= ($element[$key] == $value[1]);
                    }

                    if(($value[0])=="!="){
                        $ser.= ($element[$key] != $value[1]);
                    }

                    if(($value[0])==">"){
                        $ser.= ($element[$key] > $value[1]);
                    }


                    if(($value[0])==">="){
                        $ser.= ($element[$key] >= $value[1]);
                    } 

                    if(($value[0])=="<"){
                        $ser.= ($element[$key] < $value[1]);
                    }                        
                
                    if(($value[0])=="<="){
                        $ser.= ($element[$key] <= $value[1]);
                    }   
        
                }
           
          return $ser;
       });
        
      return $filteredArray;
    }

}



?>