<?php

namespace MediaWiki\Extension\DynamicJsonLD;

use Scribunto_LuaLibraryBase;

class SchemaOrgLuaLibrary extends Scribunto_LuaLibraryBase {

    public function register(): array {
        $lib = [
            'setMainEntity' => [ $this, 'setMainEntity' ],
            'addData'       => [ $this, 'addData' ],
        ];

        return $this->getEngine()->registerInterface(
            __DIR__ . '/../resources/mw.ext.schemaOrg.lua',
            $lib,
            []
        );
    }

    public function setMainEntity( array $data ): array {
        Hooks::$mainEntity = $this->cleanLuaTable( $data );
        return [];
    }

    public function addData( array $data ): array {
        Hooks::$extraData[] = $this->cleanLuaTable( $data );
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