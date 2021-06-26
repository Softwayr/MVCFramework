<?php

namespace Softwayr\MVCFramework;

class Router
{
    private $routes = array();
    
    public function add()
    {
        $args = func_get_args();
        
        if( count( $args ) <= 0 || ! is_array( $args ) )
            die( "Router->add() Error: First Arg must exist and be an array!" );
        
        $args = $args[0];
        
        if( ! array_key_exists( "method", $args ) || ( array_key_exists( "method", $args ) && $args['method'] == "" ) )
            $args['method'] = "GET";
        if( array_key_exists( "method", $args ) && strtoupper( $args['method'] ) != "GET" && strtoupper( $args['method'] ) != "POST" )
            $args['method'] = "GET";
        
        if( array_key_exists( "scheme", $args ) || $args['scheme'] != "http" || $args['scheme'] != "https" )
            $args['scheme'] = "http";
        
        if( ! array_key_exists( "host", $args ) && ! array_key_exists( "hosts", $args ) )
            die( "Router->add() Error: Route must contain one \"host\" or multiple \"hosts\"!" );
        if( array_key_exists( "host", $args ) && ! is_string( $args['host'] ) )
            die( "Router->add() Error: Route \"host\" must be a string!" );
        if( array_key_exists( "hosts", $args ) && ! is_array( $args['hosts'] ) )
            die( "Router->add() Error: Route \"hosts\" must be an array!" );
        if( array_key_exists( "hosts", $args ) && count( $args['hosts'] ) < 1 )
            die( "Router->add() Error: Route \"hosts\" array cannot be empty!" );
        foreach( $args['hosts'] as $host )
            if( ! is_string( $host ) )
                die( "Router->add() Error: Route \"hosts\" must be strings!" );
        
        if( ! array_key_exists( "port", $args ) )
            $args['port'] = $scheme == "http" ? 80 : 443;
        if( array_key_exists( "port", $args ) && ! is_int( $args['port'] ) && ! intval( $args['port'] ) )
            die( "Router->add() Error: Port must be an integer or a number as a string!" );
        if( array_key_exists( "port", $args ) && is_string( $args['port'] ) && ! intval( $args['port'] ) )
            die( "Router->add() Error: Port is not a number!" );
        if( array_key_exists( "port", $args ) && intval( $args['port'] ) )
            $args['port'] = intval( $args['port'] );
        
        if( ! array_key_exists( "path", $args ) )
            die( "Router->add() Error: Route must have a path!" );
        if( ! is_string( $args['path'] ) )
            die( "Router->add() Error: Route path must be a string!" );
        $args['path'] = $this->remove_extra_slashes_and_lead_trail_slashes( $args['path'] );
        
        if( ! array_key_exists( "callback", $args ) && is_array( $args['callback'] ) && ! empty( $args['callback'] ) )
            die( "Router->add() Error: Callback must exist and be an array with elements!" );
        if( count( $args['callback'] ) < 2 || ! $args['callback'][0] instanceof Controller || ! method_exists( $args['callback'][0], $args['callback'][1] ) )
            die( "Router->add() Error: Callback must be an array of at least two elements, first being a Controller instance, and second an existing method within that Controller!" );
        if( ! is_callable( $args['callback'] ) )
            die( "Router->add() Error: Callback is not callable!" );
        
        $this->routes[] = $args;
    }
    
    public function routes()
    {
        return $this->routes;
    }
    
    public function route()
    {
        $args = func_get_args();
        
        if( count( $args ) <= 0 || ! is_array( $args[0] ) || empty( $args[0] ) )
            die( "Router->route() Error: First arg must exist and be an array with elements!" );
        
        $args = $args[0];
        
        $routes = $this->routes();
        
        $matched_routes = array();
        
        foreach( $routes as $route )
        {
            $matches = 0;
            foreach( $args as $arg_key => $arg_value )
            {
                if( array_key_exists( $arg_key, $route ) && $arg_key == "path" )
                {
                    $arg_value = $this->remove_extra_slashes_and_lead_trail_slashes( $arg_value );
                }
                
                if( array_key_exists( $arg_key, $route ) && $route[ $arg_key ] == $arg_value )
                {
                    $matches++;
                    continue;
                }
                
                if( $arg_key == "host" && array_key_exists( "hosts", $route ) )
                {
                    foreach( $route['hosts'] as $host )
                    {
                        if( $arg_value == $host )
                        {
                            $matches++;
                            continue;
                        }
                    }
                }
            }
            
            if( count( $args ) == $matches )
            {
                $matched_routes[] = $route;
            }
        }
        
        return $matched_routes;
    }
    
    public function execute()
    {
        $args = func_get_args();
        
        if( count( $args ) < 1 )
            die( "Router->execute() Error: At least one arg is required!" );
        if( ! is_array( $args[0] ) || empty( $args[0] ) )
            die( "Router->execute() Error: Arg must be an array with elements!" );
        
        $args = $args[0];
        
        $route = $this->route( $args );
        
        if( count( $route ) != 1 )
        {
            die( "Router->execute() Error: No route found!" );
        }
        
        $route = $route[0];
        
        //die( "<pre>" . print_r( $route, true ) . "</pre>" );
        
        if( ! array_key_exists( "callback", $route ) || ! is_callable( $route['callback'] ) )
            die( "Router->execute() Error: A callable callback is required to execute this route!" );
        
        call_user_func( $route['callback'], $route );
    }
    
    public function remove_extra_slashes_and_lead_trail_slashes()
    {
        $args = func_get_args();
        
        if( count( $args ) > 0 )
        {
            $path = $args[0];
            
            if( is_string( $path ) )
            {
                while( substr( $path, 0, 1 ) == "/" )
                    $path = substr( $path, 1 );
                while( substr( $path, -1 ) == "/" )
                    $path = substr( $path, 0, -1 );
                while( strpos( $path, "//" ) )
                    $path = str_replace( "//", "/", $path );
                return $path;
            }
        }
    }
}