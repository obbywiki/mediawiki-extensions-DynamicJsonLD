local JsonLD = {}
local php

function JsonLD.setMainEntity( args )
    return php.setMainEntity( args )
end

function JsonLD.addData( args )
    return php.addData( args )
end


function JsonLD.setupInterface()
    JsonLD.setupInterface = nil
    php = mw_interface
    mw_interface = nil

    mw = mw or {}
    mw.ext = mw.ext or {}
    mw.ext.jsonld = JsonLD

    package.loaded['mw.ext.jsonld'] = JsonLD
end

return JsonLD