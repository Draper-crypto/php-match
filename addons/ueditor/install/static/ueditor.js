window.UEDITOR_HOME_URL = '/static/addons/ueditor/ue/';

require.config({
    paths: {
        'ueditor-config':'addons/ueditor/ue/ueditor.config',
        'ueditor':'addons/ueditor/ue/ueditor.all.min',
        'ueditor-lang':'addons/ueditor/ue/lang/zh-cn/'+Config.admin_lang,
    },
    shim: {
        'ueditor': ['ueditor-config'],
        'ueditor-lang': ['ueditor']
    }
})
require(['jquery'], function ($) {
    if ($('.editor', document).length>0) {

        require(['ueditor', 'ueditor-lang'], function () {

            var arr = [];
            $('.editor', document).each(function (id, vo) {
                $(this).attr('class', '.editor');
                $(this).css('height', '320px');

                arr[$(this).attr('id')] = UE.getEditor($(this).attr('id'), {
                    autoHeight: true,
                    serverUrl: '/'+Config.root_file+'/ueditor/index',
                    maximumWords:99999,
                    initialFrameWidth: "100%",
                    autoFloatEnabled:false,
                    toolbars: [
                        [
                            "fullscreen",
                            "source",
                            "|",
                            "undo",
                            "redo",
                            "|",
                            "bold",
                            "italic",
                            "underline",
                            "fontborder",
                            "strikethrough",
                            "superscript",
                            "subscript",
                            "removeformat",
                            "formatmatch",
                            "autotypeset",
                            "blockquote",
                            "pasteplain",
                            "|",
                            "forecolor",
                            "backcolor",
                            "insertorderedlist",
                            "insertunorderedlist",
                            "|",
                            "rowspacingtop",
                            "rowspacingbottom",
                            "lineheight",
                            "|",
                            "customstyle",
                            "paragraph",
                            "fontfamily",
                            "fontsize",
                            "|",
                            "directionalityltr",
                            "directionalityrtl",
                            "indent",
                            "|",
                            "justifyleft",
                            "justifycenter",
                            "justifyright",
                            "justifyjustify",
                            "|",
                            "touppercase",
                            "tolowercase",
                            "|",
                            "link",
                            "unlink",
                            "|",
                            "imagenone",
                            "imageleft",
                            "imageright",
                            "imagecenter",
                            "|",
                            "simpleupload",
                            "insertimage",
                            "emotion",
                            "insertvideo",
                            "attachment",
                            "map",
                            "insertframe",
                            "insertcode",
                            "|",
                            "horizontal",
                            "spechars",
                            "|",
                            "inserttable",
                            "deletetable",
                            "insertparagraphbeforetable",
                            "insertrow",
                            "deleterow",
                            "insertcol",
                            "deletecol",
                            "mergecells",
                            "mergeright",
                            "mergedown",
                            "splittocells",
                            "splittorows",
                            "splittocols",
                            "charts",
                            "|",
                            "preview",
                            "searchreplace",
                            "drafts",
                        ]
                    ]
                });
            });
        })
    }
})