/**
* @version:	2.0.0.b10-99 - 2012 March 09 16:03:58 +0300
* @package:	jbetolo
* @subpackage:	jbetolo
* @copyright:	Copyright (C) 2010 - 2011 jproven.com. All rights reserved. 
* @license:	GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
*/

var jbetoloclearcache = new Class({
        initialize: function(options) {
                this.setOptions(options);

                window.addEvent('domready', function(){
                        this.assignActions();
                }.bind(this));
        },

        assignActions: function() {
                $('clearSiteCacheBtn').addEvent('click', function() { this.clearCache('site'); }.bind(this));
                $('clearAdministratorCacheBtn').addEvent('click', function() { this.clearCache('administrator'); }.bind(this));
        },

        /** field/param element actions **/

        clearCache: function(app){
                var request = this.options.base+'index.php?option=com_jbetolo&task=clearcache&app='+app;

                var opts = {
                        onSuccess: function() {
                                alert(this.options.PLG_SYSTEM_JBETOLO_CACHE_CLEARED.replace('%s', app));
                        }.bind(this)
                };

                if (MooTools.version.substr(2, 1) != 1) {
                        opts['url'] = request;
                        new Request(opts).send();
                } else {
                        new Ajax(request, opts).request();
                }
                
                return false;
        }
});

jbetoloclearcache.implement(new Options);