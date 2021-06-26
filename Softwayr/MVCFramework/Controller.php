<?php

namespace Softwayr\MVCFramework;

class Controller
{
    private $viewsDir = "";
    private $modelsDir = "";
    
    public function __construct()
    {
        $args = func_get_args();
        
        if( count( $args ) > 0 && is_array( $args[0] ) && !empty( $args[0] ) )
        {
            $args = $args[0];
            
            if( array_key_exists( "viewsDir", $args ) )
            {
                if( ! is_string( $args['viewsDir'] ) )
                    die( get_class() . " Constructor Error: Views Dir must be a String!" );
                if( $args['viewsDir'] == "" )
                    die( get_class() . " Constructor Error: Views Dir must not be an Empty String!" );
                if( ! is_dir( $args['viewsDir'] ) )
                    die( get_class() . " Constructor Error: Views Dir does not exist!" );
                
                $this->viewsDir = $args['viewsDir'];
            }
            
            if( array_key_exists( "modelsDir", $args ) )
            {
                if( ! is_string( $args['modelsDir'] ) )
                    die( get_class() . " Constructor Error: Models Dir must be a String!" );
                if( $args['modelsDir'] == "" )
                    die( get_class() . " Constructor Error: Models Dir must not be an Empty String!" );
                if( ! is_dir( $args['modelsDir'] ) )
                    die( get_class() . " Constructor Error: Models Dir does not exist!" );
                
                $this->modelsDir = $args['modelsDir'];
            }
        }
    }
    
    public function view()
    {
        $args = func_get_args();
        
        if( count( $args ) < 1 )
            die( get_class() . "->view() Error: At least one arg is required!" );
        
        $view = $args[0];
        
        if( $this->viewsDir == "" )
            die( get_class() . "->view() Error: Views Dir Not Set!" );
        if( ! is_string( $view ) )
            die( get_class() . "->view() Error: Arg must be a String!" );
        if( ! file_exists( $this->viewsDir . "/" . $view . ".view.php" ) )
            die( get_class() . "->view() Error: View does not exist!" );
        
        $data = array();
        
        if( count( $args ) >= 2 && is_array( $args[1] ) )
            $data = $args[1];
        
        require_once $this->viewsDir . "/" . $view . ".view.php";
    }
    
    public function model()
    {
        $args = func_get_args();
        
        if( count( $args ) < 1 )
            die( get_class() . "->model() Error: At least one arg is required!" );
        
        $model = $args[0];
        
        if( $this->modelDir == "" )
            die( get_class() . "->model() Error: Models Dir Not Set!" );
        if( ! is_string( $model ) )
            die( get_class() . "->model() Error: Arg must be a String!" );
        if( ! file_exists( $this->modelsDir . "/" . $model . ".model.php" ) )
            die( get_class() . "->model() Error: Model does not exist!" );
        
        require_once $this->modelsDir . "/" . $model . ".model.php";
    }
    
    public function viewsDir()
    {
        return $this->viewsDir;
    }
    
    public function modelsDir()
    {
        return $this->modelsDir;
    }
}