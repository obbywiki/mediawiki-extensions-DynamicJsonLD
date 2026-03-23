<?php

namespace MediaWiki\Extension\DynamicJsonLD;

use OutputPage;
use Skin;
use ParserOutput;

class Hooks {
    public static ?array $mainEntity = null;
    public static array $extraData = [];
    public static function onScribunto_LuaEngine_Setup( $engine ): void {
        $engine->getInterpreter()->executeString( 'require( "mw.ext.JsonLD" )', 'DynamicJsonLD-autoload' );
    }

    public static function onOutputPageParserOutput( OutputPage $out, ParserOutput $parserOutput ): void {
        $mainEntity = $parserOutput->getExtensionData( 'DynamicJsonLD:mainEntity' );
        if ( $mainEntity ) {
            self::$mainEntity = $mainEntity;
        }

        $extraData = $parserOutput->getExtensionData( 'DynamicJsonLD:extraData' );
        if ( $extraData ) {
            self::$extraData = array_merge( self::$extraData, $extraData );
        }
    }

    public static function onScribuntoExternalLibraries( $engine, array &$extraLibraries ): bool {
        if ( $engine == 'lua' ) {
            $extraLibraries['mw.ext.JsonLD'] = JsonLDLuaLibrary::class;
        }
        
        return true;
    }

    public static function onBeforePageDisplay( OutputPage $out, Skin $skin ): void {
        $title = $out->getTitle();
        $fullURL = $title->getFullURL();

        if ( $skin->getRequest()->getVal( 'action', 'view' ) !== 'view' ) {
			return;
		}

        if ( !$title->exists() ) {
            return;
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type'    => 'Article',
            'headline' => $title->getText(),
            'name' => $title->getText(),
            'url'      => $fullURL,
            'publisher'=> [
                '@type' => 'Organization',
                'name' => $out->getConfig()->get( 'Sitename' ),
                'url' => $out->getConfig()->get( 'Server' ),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $out->getConfig()->get( 'Logos' ) && $out->getConfig()->get( 'Logos' )['1x'] ? $out->getConfig()->get( 'Logos' )['1x'] : '',
                ]
            ]
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
            'JsonLD',
            '<script type="application/ld+json">' . $json . '</script>'
        );
    }
}
