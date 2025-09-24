require.config({})
require(['jquery'], function ($) {
    if ($('.J-urlmodel1').length==2) {
        $('#custom-tabs-three-tab').append('<li class="nav-item">\n' +
            '                    <a class="nav-link"  data-page="group" href="/'+Config.root_file+'/appcenter/setConfig.html?name=sitemaps&type=addon" role="tab" aria-selected="false">Sitemap</a>\n' +
            '                </li>');
    }
})