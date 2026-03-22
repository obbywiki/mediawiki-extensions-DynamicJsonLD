<?php

namespace MediaWiki\Extension\DynamicJsonLD;

use OutputPage;
use Skin;

class Hooks {
    public static ?array $mainEntity = null;
    public static array $extraData = [];
    public static function onScribunto_LuaEngine_Setup( $engine ): void {
        self::$mainEntity = null;
        self::$extraData = [];
        $engine->getInterpreter()->executeString( 'require( "mw.ext.schemaOrg" )', 'DynamicJsonLD-autoload' );
    }

    public static function onScribuntoExternalLibraries( $engine, array &$extraLibraries ): bool {
        if ( $engine == 'lua' ) {
            $extraLibraries['mw.ext.schemaOrg'] = SchemaOrgLuaLibrary::class;
        }
        
        return true;
    }

    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ): void {
        $title = $out->getTitle();
        $fullUrl = $title->getFullURL();

        $data = [
            '@context' => 'https://schema.org',
            '@type'    => 'Article',
            'headline' => $title->getText(),
            'url'      => $fullUrl,
        ];

        if ( self::$mainEntity !== null ) {
            $data['mainEntity'] = array_merge(
                [ '@type' => 'Thing' ],
                self::$mainEntity
            );
        }

        foreach ( self::$extraData as $extra ) {
            $data = array_merge( $data, $extra );
        }
        $data = array_filter( $data, fn( $v ) => $v !== null );

        $json = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

        $out->addHeadItem(
            'schemaOrg',
            '<script type="application/ld+json">' . $json . '</script>'
        );
    }
}
