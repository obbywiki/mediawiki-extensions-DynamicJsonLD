local schemaOrg = {}
local php

function schemaOrg.setMainEntity( args )
    return php.setMainEntity( args )
end

function schemaOrg.addData( args )
    return php.addData( args )
end


function schemaOrg.setupInterface()
    schemaOrg.setupInterface = nil
    php = mw_interface
    mw_interface = nil

    mw = mw or {}
    mw.ext = mw.ext or {}
    mw.ext.schemaOrg = schemaOrg

    package.loaded['mw.ext.schemaOrg'] = schemaOrg
end

return schemaOrg