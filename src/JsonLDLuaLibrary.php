<?php

namespace MediaWiki\Extension\DynamicJsonLD;

use Scribunto_LuaLibraryBase;

class JsonLDLuaLibrary extends Scribunto_LuaLibraryBase {

    public function register(): array {
        $lib = [
            'setMainEntity' => [ $this, 'setMainEntity' ],
            'addData'       => [ $this, 'addData' ],
        ];

        return $this->getEngine()->registerInterface(
            __DIR__ . '/../resources/mw.ext.JsonLD.lua',
            $lib,
            []
        );
    }

    public function setMainEntity( array $data ): array {
        $cleanData = $this->cleanLuaTable( $data );
        Hooks::$mainEntity = $cleanData;
        
        $parser = $this->getParser();
        if ( $parser ) {
            $parser->getOutput()->setExtensionData( 'DynamicJsonLD:mainEntity', $cleanData );
        }
        
        return [];
    }

    public function addData( array $data ): array {
        $cleanData = $this->cleanLuaTable( $data );
        Hooks::$extraData[] = $cleanData;
        
        $parser = $this->getParser();
        if ( $parser ) {
            $existing = $parser->getOutput()->getExtensionData( 'DynamicJsonLD:extraData' ) ?: [];
            $existing[] = $cleanData;
            $parser->getOutput()->setExtensionData( 'DynamicJsonLD:extraData', $existing );
        }
        
        return [];
    }

    private function cleanLuaTable( array $table ): array {
        $clean = [];
        foreach ( $table as $key => $value ) {
            $clean[$key] = is_array( $value )
                ? $this->cleanLuaTable( $value )
                : $value;
        }
        return $clean;
    }
}